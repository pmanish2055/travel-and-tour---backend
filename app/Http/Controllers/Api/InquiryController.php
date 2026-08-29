<?php
/**
 * File: app/Http/Controllers/Api/InquiryController.php
 * Purpose: Handle inquiry form submissions from frontend.
 *          Routes: POST /api/v1/inquiries, POST /api/v1/custom-trips
 */

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreInquiryRequest;
use App\Models\Inquiry;
use App\Models\CustomTrip;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class InquiryController extends Controller
{
    /**
     * Store a package inquiry.
     * Validates and creates inquiry record. Sends email notification (queue).
     */
    public function store(StoreInquiryRequest $request): JsonResponse
    {
        $validated = $request->validated();
        $inquiry = Inquiry::create($validated);
        // TODO: Dispatch mail notification: Mail::to(Setting::get("company.email"))->send(new InquiryNotification($inquiry));

        return response()->json([
            "success" => true,
            "message" => "Inquiry sent successfully. We will contact you soon!",
            "data" => $inquiry
        ], 201);
    }

    /**
     * Store custom trip request.
     */
    public function customTrip(Request $request): JsonResponse
    {
        $validated = $request->validate([
            "name" => "required|string",
            "email" => "required|email",
            "phone" => "required|string",
            "country" => "nullable|string",
            "destination_interest" => "nullable|string",
            "duration_days" => "nullable|integer",
            "budget" => "nullable|numeric",
            "travel_date" => "nullable|date",
            "pax" => "nullable|integer",
            "interests" => "nullable|string",
            "message" => "nullable|string",
        ]);
        $trip = CustomTrip::create($validated);
        return response()->json(["success"=>true, "message"=>"Custom trip request sent!", "data"=>$trip], 201);
    }
}
