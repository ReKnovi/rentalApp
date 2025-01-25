<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Application extends Model
{
    use HasFactory;

    protected $fillable = [
        'room_id',
        'tenant_id',
        'status',
        'is_approved',
        'message',
    ];

    // Relationship to Room
    public function room()
    {
        return $this->belongsTo(Room::class);
    }

    // Relationship to User (Tenant)
    public function tenant()
    {
        return $this->belongsTo(User::class, 'tenant_id');
    }
}
