<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BookingResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'booking_code' => $this->booking_code,
            'travel_date' => $this->travel_date,
            'pax_adult' => $this->pax_adult,
            'pax_child' => $this->pax_child,
            'pax_total' => $this->totalPax(),
            'total_amount' => $this->total_amount,
            'advance_amount' => $this->advance_amount,
            'balance' => $this->balance(),
            'payment_status' => $this->payment_status,
            'booking_status' => $this->booking_status,
            'customer_name' => $this->customer_name,
            'customer_email' => $this->customer_email,
            'customer_phone' => $this->customer_phone,
            'customer_country' => $this->customer_country,
            'special_request' => $this->when($request->user()?->id === $this->user_id || $request->is('api/*'), $this->special_request),
            'package' => $this->whenLoaded('package'),
            'departure' => $this->whenLoaded('departure'),
            'travelers' => $this->whenLoaded('travelers'),
            'payments' => $this->whenLoaded('payments'),
            'created_at' => $this->created_at,
        ];
    }
}
