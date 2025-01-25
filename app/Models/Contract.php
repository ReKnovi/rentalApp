<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Contract extends Model
{
    use HasFactory;

    protected $fillable = [
        'room_id',
        'landlord_id',
        'tenant_id',
        'contract_content',
        'landlord_signature',
        'tenant_signature',
        'status',
    ];

    public function room()
    {
        return $this->belongsTo(Room::class);
    }

    public function landlord()
    {
        return $this->belongsTo(User::class, 'landlord_id');
    }

    public function tenant()
    {
        return $this->belongsTo(User::class, 'tenant_id');
    }
}
