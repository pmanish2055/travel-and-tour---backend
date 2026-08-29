<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreBookingRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            "package_id" => "required|exists:packages,id",
            "departure_id" => "nullable|exists:package_departures,id",
            "travel_date" => "required|date|after:today",
            "pax_adult" => "required|integer|min:1|max:50",
            "pax_child" => "nullable|integer|min:0|max:20",
            "customer_name" => "required|string|max:255",
            "customer_email" => "required|email|max:255",
            "customer_phone" => "required|string|max:50",
            "customer_country" => "nullable|string|max:100",
            "special_request" => "nullable|string|max:2000",
            "travelers" => "nullable|array|max:20",
            "travelers.*.full_name" => "required_with:travelers|string|max:255",
            "travelers.*.passport_no" => "nullable|string|max:100",
            "travelers.*.nationality" => "required_with:travelers|string|max:100",
            "travelers.*.dob" => "nullable|date|before:today",
            "travelers.*.gender" => "nullable|in:male,female,other",
        ];
    }

    public function messages(): array
    {
        return [
            'departure_id.exists' => 'Selected departure is invalid.',
            'travel_date.after' => 'Travel date must be in the future.',
        ];
    }
}
