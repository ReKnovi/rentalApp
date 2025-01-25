<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('contracts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('room_id')->constrained('rooms')->onDelete('cascade'); // Room ID
            $table->foreignId('landlord_id')->constrained('users')->onDelete('cascade'); // Landlord
            $table->foreignId('tenant_id')->constrained('users')->onDelete('cascade'); // Tenant
            $table->text('contract_content'); // Agreement text
            $table->string('landlord_signature')->nullable(); // Path to landlord's signature
            $table->string('tenant_signature')->nullable(); // Path to tenant's signature
            $table->enum('status', ['pending', 'signed', 'completed'])->default('pending'); // Contract status
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('contracts');
    }
};
