<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ContactRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool { return true; }
    public function rules(): array
    {
        $isContact = $this->route()?->getName() === 'api.contact' || $this->is('*/contact');
        if ($isContact) {
            return [
                'name' => 'required|string|max:255',
                'email' => 'required|email|max:255',
                'phone' => 'nullable|string|max:50',
                'subject' => 'required|string|max:255',
                'message' => 'required|string|max:3000',
            ];
        }
        return [
            'email' => 'required|email|max:255',
        ];
    }
}
