<?php
/**
 * File: app/Http/Controllers/Api/CouponController.php
 * Purpose: Coupon validation and application for booking checkout.
 *          Frontend calls this before creating booking to display discount.
 *          Routes: POST /api/v1/coupons/validate, GET /api/v1/coupons/{code}
 *          Model: App\Models\Coupon
 */

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Coupon;
use App\Models\Package;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CouponController extends Controller
{
    /**
     * Validate a coupon code for a given package and amount.
     * POST /api/v1/coupons/validate
     * Body: { code, package_id (optional), amount (optional) }
     */
    public function validate(Request $request): JsonResponse
    {
        $data = $request->validate([
            'code' => 'required|string|max:50',
            'package_id' => 'nullable|exists:packages,id',
            'amount' => 'nullable|numeric|min:0',
        ]);

        $coupon = Coupon::where('code', $data['code'])->first();

        if (!$coupon) {
            return response()->json(['success' => false, 'message' => 'Coupon code not found'], 404);
        }

        // Check active + date range + usage limit
        if (!$coupon->isValid()) {
            $reason = !$coupon->is_active ? 'Coupon is inactive' :
                      ($coupon->valid_to && now()->gt($coupon->valid_to) ? 'Coupon has expired' :
                      ($coupon->valid_from && now()->lt($coupon->valid_from) ? 'Coupon is not yet valid' : 'Coupon usage limit reached'));
            return response()->json(['success' => false, 'message' => $reason, 'data' => $coupon], 422);
        }

        // If package_id provided and coupon is restricted to specific packages, check
        if ($data['package_id'] ?? null) {
            // If pivot has entries, coupon is restricted. If empty, applies to all.
            $restrictedCount = $coupon->packages()->count();
            if ($restrictedCount > 0) {
                $applies = $coupon->packages()->where('packages.id', $data['package_id'])->exists();
                if (!$applies) {
                    return response()->json(['success' => false, 'message' => 'Coupon not applicable to this package'], 422);
                }
            }
        }

        // Calculate discount if amount provided
        $discount = null;
        $finalAmount = null;
        if (isset($data['amount'])) {
            $amount = (float) $data['amount'];
            if ($coupon->discount_type === 'percent') {
                $discount = round($amount * ($coupon->value / 100), 2);
            } else { // fixed
                $discount = (float) $coupon->value;
            }
            // Don't exceed amount
            $discount = min($discount, $amount);
            $finalAmount = $amount - $discount;
        }

        return response()->json([
            'success' => true,
            'message' => 'Coupon is valid',
            'data' => [
                'coupon' => $coupon,
                'discount_type' => $coupon->discount_type,
                'value' => $coupon->value,
                'discount' => $discount,
                'final_amount' => $finalAmount,
            ]
        ]);
    }

    /**
     * Get coupon details by code (public limited info).
     * GET /api/v1/coupons/{code}
     */
    public function show(string $code): JsonResponse
    {
        $coupon = Coupon::where('code', $code)->first();
        if (!$coupon) {
            return response()->json(['success' => false, 'message' => 'Coupon not found'], 404);
        }

        // Hide sensitive fields, only show public discount info if valid
        return response()->json([
            'success' => true,
            'data' => [
                'code' => $coupon->code,
                'discount_type' => $coupon->discount_type,
                'value' => $coupon->value,
                'is_valid' => $coupon->isValid(),
                'valid_from' => $coupon->valid_from,
                'valid_to' => $coupon->valid_to,
            ]
        ]);
    }
}
