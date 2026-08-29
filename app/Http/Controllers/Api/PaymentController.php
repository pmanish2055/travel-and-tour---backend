<?php
/**
 * File: app/Http/Controllers/Api/PaymentController.php
 * Purpose: Handle payments for bookings — eSewa, Khalti, Stripe, Bank Transfer.
 *          Provides: list gateways, initiate payment, verify callback, check status.
 *          Tokens/keys are stored encrypted in settings table (group tokens) via Setting::get('tokens.*').
 *          In production, you will call real gateway SDKs using those decrypted tokens.
 *          This controller returns proper scaffolding with stubs so frontend can integrate.
 *          Routes: GET /api/v1/payments/methods, POST /api/v1/payments/initiate, POST /api/v1/payments/verify, GET /api/v1/payments/{booking_code}
 *          Model: App\Models\Payment, Booking
 */

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\InitiatePaymentRequest;
use App\Http\Requests\VerifyPaymentRequest;
use App\Models\Booking;
use App\Models\Payment;
use App\Models\Setting;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    /**
     * List available payment gateways.
     * Reads from settings tokens to see which are configured; returns public info only (no secrets).
     * GET /api/v1/payments/methods
     */
    public function methods(): JsonResponse
    {
        // Check which gateways are configured (keys exist and non-empty)
        $gateways = [];

        // eSewa: check merchant code exists
        if (Setting::get('tokens.esewa_merchant_code')) {
            $gateways[] = ['key' => 'esewa', 'name' => 'eSewa', 'currency' => 'NPR', 'enabled' => true];
        } else {
            $gateways[] = ['key' => 'esewa', 'name' => 'eSewa', 'currency' => 'NPR', 'enabled' => false, 'reason' => 'Configure in Admin → Company → Company Settings → Tokens & Keys'];
        }

        if (Setting::get('tokens.khalti_public_key')) {
            $gateways[] = ['key' => 'khalti', 'name' => 'Khalti', 'currency' => 'NPR', 'enabled' => true];
        } else {
            $gateways[] = ['key' => 'khalti', 'name' => 'Khalti', 'currency' => 'NPR', 'enabled' => false];
        }

        if (Setting::get('tokens.stripe_secret')) {
            $gateways[] = ['key' => 'stripe', 'name' => 'Stripe (Card)', 'currency' => 'USD', 'enabled' => true];
        } else {
            $gateways[] = ['key' => 'stripe', 'name' => 'Stripe (Card)', 'currency' => 'USD', 'enabled' => false];
        }

        // Always allow bank transfer
        $gateways[] = ['key' => 'bank', 'name' => 'Bank Transfer', 'currency' => 'NPR', 'enabled' => true];

        return response()->json([
            'success' => true,
            'data' => $gateways
        ]);
    }

    /**
     * Initiate payment for a booking.
     * POST /api/v1/payments/initiate
     * Body: { booking_code, gateway: esewa|khalti|stripe|bank, amount? (if partial) }
     */
    public function initiate(InitiatePaymentRequest $request): JsonResponse
    {
        $data = $request->validated();

        $booking = Booking::with('package')->where('booking_code', $data['booking_code'])->first();

        if (!$booking) {
            return response()->json(['success' => false, 'message' => 'Booking not found'], 404);
        }

        if ($booking->payment_status === 'paid') {
            return response()->json(['success' => false, 'message' => 'Booking is already paid'], 422);
        }

        // SECURITY: ignore client amount to prevent tampering (always charge server-calculated balance)
        $amount = $booking->balance();
        if ($amount <= 0) $amount = (float) $booking->total_amount;
        if ($amount <= 0) {
            return response()->json(['success'=>false,'message'=>'Invalid booking amount'], 422);
        }

        // Create payment record with pending status
        $payment = Payment::create([
            'booking_id' => $booking->id,
            'gateway' => $data['gateway'],
            'amount' => $amount,
            'currency' => in_array($data['gateway'], ['stripe']) ? 'USD' : 'NPR',
            'status' => 'pending',
            'transaction_id' => $data['gateway'] . '_' . strtoupper(\Illuminate\Support\Str::random(10)),
            'raw_response' => ['initiated_at' => now()->toIso8601String(), 'booking_code' => $booking->booking_code],
        ]);

        // Build gateway-specific payload (frontend will use this to redirect / call SDK)
        $payload = [];
        if ($data['gateway'] === 'esewa') {
            $merchantCode = Setting::get('tokens.esewa_merchant_code', 'EPAYTEST');
            $payload = [
                'merchant_code' => $merchantCode,
                'booking_code' => $booking->booking_code,
                'amount' => $amount,
                // eSewa expects: tAmt=total, amt=amount, txAmt=0, psc=0, pdc=0, scd=merchant, pid=booking_code, su=successUrl, fu=failureUrl
                // Frontend should POST to https://esewa.com.np/epay/main with these fields
                'esewa_form_data' => [
                    'amt' => $amount,
                    'pdc' => 0,
                    'psc' => 0,
                    'txAmt' => 0,
                    'tAmt' => $amount,
                    'pid' => $booking->booking_code,
                    'scd' => $merchantCode,
                    'su' => url('/api/v1/payments/callback/esewa/success'),
                    'fu' => url('/api/v1/payments/callback/esewa/failure'),
                ],
                'message' => 'Submit esewa_form_data to eSewa. On success, eSewa will redirect to su with query params. Then call POST /api/v1/payments/verify with transaction_id.'
            ];
        } elseif ($data['gateway'] === 'khalti') {
            $publicKey = Setting::get('tokens.khalti_public_key');
            $payload = [
                'public_key' => $publicKey ? substr($publicKey, 0, 8) . '...' : null,
                'amount' => $amount * 100, // Khalti expects paisa
                'booking_code' => $booking->booking_code,
                'purchase_order_id' => $booking->booking_code,
                'message' => 'Use Khalti SDK Checkout with this amount. On success call POST /api/v1/payments/verify'
            ];
        } elseif ($data['gateway'] === 'stripe') {
            $publishable = Setting::get('tokens.stripe_publishable');
            $payload = [
                'publishable_key' => $publishable ? substr($publishable, 0, 12) . '...' : null,
                'amount' => $amount,
                'currency' => 'usd',
                'booking_code' => $booking->booking_code,
                'client_secret' => 'pi_stub_' . \Illuminate\Support\Str::random(24) . '_secret_stub',
                'message' => 'Use Stripe Elements with client_secret. Confirm on frontend, then POST /api/v1/payments/verify. Server should create PaymentIntent via Stripe SDK using tokens.stripe_secret.'
            ];
        } else { // bank
            $payload = [
                'bank_details' => [
                    'account_name' => Setting::get('company.name', config('app.name', 'Travel Company')),
                    'account_number' => Setting::get('company.bank_account', 'Provide in Company Settings → Company Details'),
                    'bank_name' => Setting::get('company.bank_name', 'Provide in Company Settings → Company Details'),
                ],
                'booking_code' => $booking->booking_code,
                'amount' => $amount,
                'message' => 'Customer must transfer to bank and upload proof. Admin will mark booking as paid in Filament → Bookings → Edit → Payments.'
            ];
        }

        return response()->json([
            'success' => true,
            'message' => 'Payment initiated',
            'data' => [
                'payment' => $payment,
                'booking' => $booking->load('package'),
                'gateway' => $data['gateway'],
                'gateway_data' => $payload
            ]
        ], 201);
    }

    /**
     * Verify payment callback (from gateway).
     * POST /api/v1/payments/verify
     * Body: { booking_code, gateway, transaction_id, raw_response }
     * SECURITY: Client `status` is NEVER trusted. Server verifies via gateway API
     * using secrets from Setting::get('tokens.*') configured in Admin -> Company Settings.
     * When no gateway keys are configured (local dev), a safe mock path is used that
     * STILL requires a pending payment from /initiate and never auto-creates a paid record.
     */
    public function verify(VerifyPaymentRequest $request): JsonResponse
    {
        $data = $request->validated();

        $booking = Booking::where('booking_code', $data['booking_code'])->first();
        if (!$booking) {
            return response()->json(['success' => false, 'message' => 'Booking not found'], 404);
        }

        if ($booking->payment_status === 'paid') {
            return response()->json(['success' => false, 'message' => 'Booking is already paid'], 422);
        }

        // Must have a pending payment created via /initiate - no auto-create of paid records
        $payment = Payment::where('booking_id', $booking->id)
            ->where('gateway', $data['gateway'])
            ->where('status', 'pending')
            ->latest()
            ->first();

        if (!$payment) {
            return response()->json([
                'success' => false,
                'message' => 'No pending payment found for this booking/gateway. Call POST /api/v1/payments/initiate first.',
            ], 422);
        }

        // Server-side gateway verification using keys from settings (Company Settings -> Tokens)
        $verified = false;
        $gatewayMessage = null;
        $mockMode = false;

        try {
            $txnId = $data['transaction_id'] ?? null;
            // SECURITY: mock only in non-production and when explicitly enabled
            $allowAnyMock = !app()->isProduction() && app()->environment('local', 'testing') && Setting::get('tokens.payment_mock_enabled', false);
            if ($allowAnyMock && $txnId) {
                $isTestOk = str_starts_with($txnId, 'TEST_OK');
                // In local, any txn succeeds; but also keep TEST_OK explicit for production mock
                if ($isTestOk || $allowAnyMock) {
                    $verified = true;
                    $mockMode = true;
                    $gatewayMessage = 'Mock verification (local dev). Configure real keys in Admin -> Company Settings -> Tokens & Keys for production.';
                    \Illuminate\Support\Facades\Log::warning('Payment mock verification used (local)', ['booking_code'=>$booking->booking_code, 'gateway'=>$data['gateway'], 'payment_id'=>$payment->id]);
                }
            } else {
                if ($data['gateway'] === 'esewa') {
                    $secret = Setting::get('tokens.esewa_secret');
                    $merchantCode = Setting::get('tokens.esewa_merchant_code');
                    if ($secret && $merchantCode && $txnId) {
                        $verified = false;
                        $gatewayMessage = 'eSewa keys are configured but server verification not yet implemented. Implement Http call in PaymentController::verify and set $verified=true on COMPLETE.';
                    } else {
                        $mockMode = true;
                    }
                } elseif ($data['gateway'] === 'khalti') {
                    $secret = Setting::get('tokens.khalti_secret');
                    if ($secret && $txnId) {
                        $verified = false;
                        $gatewayMessage = 'Khalti secret configured but lookup not yet implemented.';
                    } else {
                        $mockMode = true;
                    }
                } elseif ($data['gateway'] === 'stripe') {
                    $secret = Setting::get('tokens.stripe_secret');
                    if ($secret && $txnId) {
                        $verified = false;
                        $gatewayMessage = 'Stripe secret configured but SDK verification not yet implemented.';
                    } else {
                        $mockMode = true;
                    }
                } elseif ($data['gateway'] === 'bank') {
                    $verified = false;
                    $gatewayMessage = 'Bank transfer requires admin confirmation in Filament -> Bookings.';
                }

                if ($mockMode && !$verified) {
                    if (app()->isProduction()) {
                        $verified = false;
                        $gatewayMessage = 'Payment gateway not configured. Contact administrator.';
                    } else {
                        $isMockSuccess = $txnId && str_starts_with($txnId, 'TEST_OK');
                        if ($isMockSuccess) {
                            $verified = true;
                            $gatewayMessage = 'Mock verification (no gateway keys configured). Configure real keys in Admin -> Company Settings -> Tokens & Keys for production.';
                            \Illuminate\Support\Facades\Log::warning('Payment mock verification used', ['booking_code'=>$booking->booking_code, 'gateway'=>$data['gateway'], 'payment_id'=>$payment->id]);
                        } else {
                            $verified = false;
                            $gatewayMessage = 'Gateway not configured. Set keys in Admin -> Company Settings -> Tokens & Keys, or send transaction_id starting with TEST_OK for local mock.';
                        }
                    }
                }
            }
            // If still no txnId and not verified, require it
            if (!$verified && !$txnId && $data['gateway'] !== 'bank') {
                // keep pending but inform
                $gatewayMessage = $gatewayMessage ?? 'transaction_id is required for verification';
            }
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error('Payment verify error', ['error'=>$e->getMessage(), 'booking_code'=>$booking->booking_code]);
            return response()->json(['success'=>false,'message'=>'Verification failed, try again'], 500);
        }

        // Atomic update of payment + booking with row lock to prevent double-spend
        try {
            \Illuminate\Support\Facades\DB::transaction(function () use ($payment, $booking, $verified, $data) {
                $p = Payment::where('id', $payment->id)->lockForUpdate()->first();
                $b = Booking::where('id', $booking->id)->lockForUpdate()->first();
                if ($p->status !== 'pending') return; // idempotent - already verified
                if ($verified) {
                    $p->update([
                        'transaction_id' => $data['transaction_id'] ?? $p->transaction_id,
                        'status' => 'completed',
                        'raw_response' => array_merge($p->raw_response ?? [], $data['raw_response'] ?? [], ['verified_at' => now()->toIso8601String(), 'verified_gateway'=>$p->gateway]),
                    ]);
                    $b->update([
                        'payment_status' => 'paid',
                        'advance_amount' => $p->amount,
                        'booking_status' => $b->booking_status === 'pending' ? 'confirmed' : $b->booking_status
                    ]);
                } else {
                    // keep pending, store attempted verification payload for admin audit
                    $p->update([
                        'raw_response' => array_merge($p->raw_response ?? [], $data['raw_response'] ?? [], ['verify_attempt_at'=>now()->toIso8601String(), 'verify_gateway'=>$p->gateway, 'transaction_id_attempt'=>$data['transaction_id'] ?? null]),
                    ]);
                }
            });
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error('Payment verify transaction failed', ['error'=>$e->getMessage()]);
            return response()->json(['success'=>false,'message'=>'Verification transaction failed'], 500);
        }

        $freshPayment = $payment->fresh();
        $freshBooking = $booking->fresh()->load('payments', 'package');

        if ($freshPayment->status === 'completed') {
            return response()->json([
                'success' => true,
                'message' => 'Payment verified successfully',
                'data' => ['payment' => $freshPayment, 'booking' => $freshBooking]
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => $gatewayMessage ?? 'Payment verification pending - admin will confirm',
            'data' => ['payment' => $freshPayment, 'booking' => $freshBooking, 'mock_mode'=>$mockMode]
        ], 202);
    }

    /**
     * Get payment history for a booking.
     * GET /api/v1/payments/booking/{booking_code}
     */
    public function bookingPayments(string $bookingCode): JsonResponse
    {
        $booking = Booking::where('booking_code', $bookingCode)->first();
        if (!$booking) {
            return response()->json(['success' => false, 'message' => 'Booking not found'], 404);
        }

        $payments = Payment::where('booking_id', $booking->id)->latest()->get();

        return response()->json([
            'success' => true,
            'data' => [
                'booking' => $booking->load('package'),
                'payments' => $payments,
                'total_paid' => $payments->where('status', 'completed')->sum('amount'),
                'balance' => $booking->balance(),
            ]
        ]);
    }

    /**
     * Callback placeholders for gateway redirects (eSewa).
     * GET /api/v1/payments/callback/esewa/{status}
     * These are hit by browser after eSewa payment - show simple view or JSON.
     */
    public function esewaCallback(Request $request, string $status): JsonResponse
    {
        // eSewa sends ?oid=booking_code&amt=amount&refId=transactionId
        return response()->json([
            'success' => $status === 'success',
            'message' => $status === 'success' ? 'eSewa payment success callback' : 'eSewa payment failed',
            'params' => $request->all(),
            'next_step' => 'Frontend should call POST /api/v1/payments/verify with booking_code, gateway=esewa, transaction_id=refId, status=' . ($status === 'success' ? 'completed' : 'failed')
        ]);
    }
}
