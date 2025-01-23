<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Room extends Model
{
    // Fillable fields to prevent mass-assignment vulnerability
    protected $fillable = [
        'user_id', 'title', 'description', 'location', 'rent_type', 'price',
        'capacity', 'bedrooms', 'bathrooms', 'shared_space', 'shared_space_details',
        'is_sharable', 'max_occupancy', 'security_deposit', 'size', 'amenities',
        'tenant_preferences', 'utilities_included', 'furnished', 'status',
        'verification_status', 'instant_booking', 'floor', 'parking_available',
        'pets_allowed', 'accessibility_features', 'nearby_facilities', 'lease_terms',
        'condition', 'available_from', 'available_until'
    ];

    // Type Casting
    protected $casts = [
        'amenities' => 'array', // Casting JSON columns
        'tenant_preferences' => 'array',
        'accessibility_features' => 'array',
        'nearby_facilities' => 'array',
        'lease_terms' => 'array',
        'available_from' => 'date',
        'available_until' => 'date',
        'shared_space' => 'boolean',
        'is_sharable' => 'boolean',
        'utilities_included' => 'boolean',
        'furnished' => 'boolean',
        'instant_booking' => 'boolean',
        'parking_available' => 'boolean',
        'pets_allowed' => 'boolean',
    ];

    public function landlord()
    {
        return $this->belongsTo(User::class, 'user_id')->where('role', 'landlord');
    }
}
