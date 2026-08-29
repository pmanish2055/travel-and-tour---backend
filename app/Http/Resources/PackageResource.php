<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PackageResource extends JsonResource
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
            'title' => $this->title,
            'slug' => $this->slug,
            'short_description' => $this->short_description,
            'overview' => $this->when($request->routeIs('api.packages.show'), $this->overview),
            'duration_days' => $this->duration_days,
            'duration_nights' => $this->duration_nights,
            'group_size_min' => $this->group_size_min,
            'group_size_max' => $this->group_size_max,
            'max_altitude_m' => $this->max_altitude_m,
            'difficulty' => $this->difficulty,
            'best_season' => $this->best_season,
            'accommodation' => $this->accommodation,
            'meal_plan' => $this->meal_plan,
            'price' => $this->price,
            'discount_price' => $this->discount_price,
            'final_price' => $this->when(isset($this->price), $this->finalPrice()),
            'is_on_sale' => $this->when(isset($this->price), $this->isOnSale()),
            'discount_percentage' => $this->when(isset($this->price), $this->discountPercentage()),
            'currency' => $this->currency,
            'is_price_on_request' => $this->is_price_on_request,
            'featured' => $this->featured,
            'is_trending' => $this->is_trending,
            'is_popular' => $this->is_popular,
            'status' => $this->status,
            'published_at' => $this->published_at,
            'featured_image' => $this->featured_image,
            'view_count' => $this->view_count,
            'category' => $this->whenLoaded('category'),
            'destination' => $this->whenLoaded('destination'),
            'region' => $this->whenLoaded('region'),
            'tags' => $this->whenLoaded('tags'),
            'pricings' => $this->whenLoaded('pricings'),
            'activities' => $this->whenLoaded('activities'),
            'media' => $this->whenLoaded('media'),
            'itineraries' => $this->whenLoaded('itineraries'),
            'inclusions' => $this->whenLoaded('inclusions'),
            'faqs' => $this->whenLoaded('faqs'),
            'departures' => $this->whenLoaded('departures'),
            'created_at' => $this->created_at,
        ];
    }
}
