<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('rooms', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade'); // Landlord ID
            $table->string('title');
            $table->text('description');
            $table->string('location'); // e.g., City, Country
            $table->enum('rent_type', ['short_term', 'long_term']); // Rent type
            $table->decimal('price', 10, 2); // Rent price
            $table->integer('capacity'); // Number of occupants allowed
            $table->integer('bedrooms')->nullable(); // Number of bedrooms
            $table->integer('bathrooms')->nullable(); // Number of bathrooms
            $table->boolean('shared_space')->default(false); // If there are shared spaces
            $table->text('shared_space_details')->nullable(); // Details of shared spaces
            $table->boolean('is_sharable')->default(false); // If the room can be shared
            $table->integer('max_occupancy')->nullable(); // Maximum number of occupants
            $table->float('security_deposit')->nullable(); // Security deposit
            $table->integer('size')->nullable(); // Room size
            $table->json('amenities')->nullable(); // Amenities (e.g., WiFi, AC)
            $table->json('tenant_preferences')->nullable(); // Tenant restrictions (e.g., "No pets")
            $table->boolean('utilities_included')->default(false); // Utilities included
            $table->boolean('furnished')->default(false); // Is room furnished
            $table->enum('status', ['available', 'unavailable', 'reserved'])->default('available'); // Status
            $table->enum('verification_status', ['unverified', 'verified', 'pending'])->default('unverified'); // Verification levels
            $table->boolean('instant_booking')->default(false); // Can be booked instantly
            $table->integer('floor')->nullable(); // Floor or level of the room
            $table->boolean('parking_available')->default(false); // Is parking available
            $table->boolean('pets_allowed')->default(false); // Are pets allowed
            $table->json('accessibility_features')->nullable(); // Accessibility features (e.g., elevator, wheelchair access)
            $table->json('nearby_facilities')->nullable(); // Nearby facilities (e.g., schools, markets, transport)
            $table->json('lease_terms')->nullable(); // Lease terms (e.g., minimum duration)
            $table->string('condition')->nullable(); // Room condition (e.g., "newly renovated")
            $table->date('available_from')->nullable(); // When the room becomes available
            $table->date('available_until')->nullable(); // Until when the room is available
            $table->json('rental_rules')->nullable();
            $table->timestamp('highlighted_until')->nullable();
            $table->timestamps(); // Created and updated timestamps
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rooms');
    }
};
