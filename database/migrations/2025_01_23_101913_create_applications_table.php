<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('applications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('users')->onDelete('cascade'); // Tenant ID
            $table->foreignId('room_id')->constrained('rooms')->onDelete('cascade');  // Room ID
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending'); // Application status
            $table->boolean('is_approved')->default(false);
            $table->text('message')->nullable(); // Tenant's message to the landlord
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('applications');
    }
};
