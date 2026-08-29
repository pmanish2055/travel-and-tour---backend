<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class VerifyPaymentRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool { return true; }
    public function rules(): array
    {
        return [
            'booking_code' => 'required|string|exists:bookings,booking_code',
            'gateway' => 'required|in:esewa,khalti,stripe,bank',
            'transaction_id' => 'nullable|string|max:100',
            'raw_response' => 'nullable|array',
        ];
    }
}
