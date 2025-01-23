<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreRoomRequest extends FormRequest
{
    public function authorize()
    {
        return true; // Adjust based on authorization logic if needed
    }

    public function rules()
    {
        return [
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'location' => 'required|string|max:255',
            'rent_type' => 'required|in:short_term,long_term',
            'price' => 'required|numeric|min:0',
            'capacity' => 'required|integer|min:1',
            'bedrooms' => 'nullable|integer|min:1',
            'bathrooms' => 'nullable|integer|min:1',
            'shared_space' => 'nullable|boolean',
            'shared_space_details' => 'nullable|string',
            'is_sharable' => 'nullable|boolean',
            'max_occupancy' => 'nullable|integer|min:1',
            'security_deposit' => 'nullable|numeric|min:0',
            'size' => 'nullable|integer|min:1',
            'amenities' => 'nullable|array',
            'tenant_preferences' => 'nullable|array',
            'utilities_included' => 'nullable|boolean',
            'furnished' => 'nullable|boolean',
            'status' => 'nullable|in:available,unavailable,reserved',
            'verification_status' => 'nullable|in:unverified,verified,pending',
            'instant_booking' => 'nullable|boolean',
            'floor' => 'nullable|integer',
            'parking_available' => 'nullable|boolean',
            'pets_allowed' => 'nullable|boolean',
            'accessibility_features' => 'nullable|array',
            'nearby_facilities' => 'nullable|array',
            'lease_terms' => 'nullable|array',
            'condition' => 'nullable|string',
            'available_from' => 'nullable|date',
            'available_until' => 'nullable|date',
        ];
    }
}
