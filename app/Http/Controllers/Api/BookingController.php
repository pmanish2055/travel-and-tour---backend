<?php
/**
 * File: app/Http/Controllers/Api/BookingController.php
 * Purpose: Handle booking creation from frontend. Generates booking_code.
 *          Routes: POST /api/v1/bookings
 */

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreBookingRequest;
use App\Models\Booking;
use App\Models\BookingTraveler;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class BookingController extends Controller
{
    /**
     * Store a new booking with travelers.
     * Uses DB transaction to ensure booking + travelers + payment consistency.
     */
    public function store(StoreBookingRequest $request): JsonResponse
    {
        $validated = $request->validated();

        // Validate departure belongs to package and is open (if provided)
        $package = \App\Models\Package::findOrFail($validated["package_id"]);
        if (!empty($validated["departure_id"])) {
            $depCheck = \App\Models\PackageDeparture::where('id', $validated["departure_id"])->first();
            if (!$depCheck || (int)$depCheck->package_id !== (int)$validated["package_id"]) {
                return response()->json(['success'=>false,'message'=>'Selected departure does not belong to this package'], 422);
            }
            if ($depCheck->status !== 'open') {
                return response()->json(['success'=>false,'message'=>'Selected departure is not open for booking (status: '.$depCheck->status.')'], 422);
            }
            if ($depCheck->departure_date < now()->toDateString()) {
                return response()->json(['success'=>false,'message'=>'Selected departure date has passed'], 422);
            }
        }

        $totalPax = $validated["pax_adult"] + ($validated["pax_child"] ?? 0);
        // Use departure price override if set, otherwise package final price
        $unitPrice = $package->finalPrice();
        if (!empty($validated["departure_id"])) {
            $depPrice = \App\Models\PackageDeparture::where('id', $validated["departure_id"])->value('price');
            if ($depPrice) $unitPrice = (float)$depPrice;
        }
        $totalAmount = $unitPrice * $totalPax;

        // DB transaction with row lock to prevent overbooking
        try {
            $booking = DB::transaction(function() use ($validated, $totalAmount, $totalPax) {
                // Lock departure row if booking has departure
                if (!empty($validated["departure_id"])) {
                    $dep = \App\Models\PackageDeparture::where("id", $validated["departure_id"])->lockForUpdate()->first();
                    if (!$dep) throw new \RuntimeException('Departure not found');
                    $remaining = $dep->seats_total - $dep->seats_booked;
                    if ($remaining < $totalPax) {
                        throw new \RuntimeException('Not enough seats available. Remaining: '.$remaining.', requested: '.$totalPax);
                    }
                    // Atomic increment only if still within capacity (extra safety)
                    $affected = \App\Models\PackageDeparture::where("id", $dep->id)
                        ->whereRaw('seats_booked + ? <= seats_total', [$totalPax])
                        ->increment("seats_booked", $totalPax);
                    if ($affected === 0) {
                        throw new \RuntimeException('Seats just sold out, please try another departure');
                    }
                }

                $booking = Booking::create([
                    "package_id" => $validated["package_id"],
                    "departure_id" => $validated["departure_id"] ?? null,
                    "travel_date" => $validated["travel_date"],
                    "pax_adult" => $validated["pax_adult"],
                    "pax_child" => $validated["pax_child"] ?? 0,
                    "total_amount" => $totalAmount,
                    "advance_amount" => 0,
                    "payment_status" => "unpaid",
                    "booking_status" => "pending",
                    "customer_name" => $validated["customer_name"],
                    "customer_email" => $validated["customer_email"],
                    "customer_phone" => $validated["customer_phone"],
                    "customer_country" => $validated["customer_country"] ?? null,
                    "special_request" => $validated["special_request"] ?? null,
                ]);
                if (!empty($validated["travelers"])) {
                    foreach ($validated["travelers"] as $idx => $t) {
                        BookingTraveler::create([
                            "booking_id" => $booking->id,
                            "full_name" => $t["full_name"],
                            "passport_no" => $t["passport_no"] ?? null,
                            "nationality" => $t["nationality"],
                            "dob" => $t["dob"] ?? null,
                            "gender" => $t["gender"] ?? null,
                            "is_lead" => $idx === 0
                        ]);
                    }
                }
                return $booking;
            });
        } catch (\RuntimeException $e) {
            return response()->json(['success'=>false,'message'=>$e->getMessage()], 422);
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error('Booking create failed', ['error'=>$e->getMessage()]);
            return response()->json(['success'=>false,'message'=>'Booking failed, please try again'], 500);
        }

        return response()->json([
            "success" => true,
            "message" => "Booking created successfully. Booking code: " . $booking->booking_code,
            "data" => $booking->load("package", "travelers")
        ], 201);
    }

    /**
     * Show booking by code. Requires ?email= to verify ownership (prevents IDOR/PII leak).
     * In production, email is MANDATORY; in local/testing it remains optional for dev convenience.
     */
    public function show(string $code, \Illuminate\Http\Request $request): JsonResponse
    {
        $booking = Booking::with(["package","departure","travelers","payments"])->where("booking_code", $code)->first();
        if (!$booking) return response()->json(["success"=>false,"message"=>"Booking not found"],404);

        // Production: require email ownership check to prevent IDOR
        if (app()->isProduction() && !$request->filled('email')) {
            return response()->json(['success'=>false,'message'=>'Email is required to view booking (booking_code + email verification)'], 422);
        }
        if ($request->filled('email') && strtolower(trim($request->input('email'))) !== strtolower(trim($booking->customer_email))) {
            return response()->json(['success'=>false,'message'=>'Email does not match booking'], 403);
        }
        // If email not provided in non-prod, mask sensitive traveler fields (passport)
        if (!$request->filled('email') && $booking->travelers) {
            $booking->travelers->each(function ($t) {
                if ($t->passport_no) $t->passport_no = substr($t->passport_no, 0, 2) . str_repeat('*', max(0, strlen($t->passport_no)-4)) . substr($t->passport_no, -2);
            });
        }
        return response()->json(["success"=>true, "data"=>$booking]);
    }
}
