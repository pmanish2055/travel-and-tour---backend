<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreInquiryRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool { return true; }
    public function rules(): array
    {
        return [
            'package_id' => 'nullable|exists:packages,id',
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'nullable|string|max:50',
            'country' => 'nullable|string|max:100',
            'message' => 'required|string|max:2000',
            'travel_date' => 'nullable|date|after:today',
            'pax_adult' => 'nullable|integer|min:1|max:50',
            'pax_child' => 'nullable|integer|min:0|max:20',
        ];
    }
}
