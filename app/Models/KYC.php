<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kyc extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'document_type',
        'document_number',
        'document_file',
        'status',
    ];

    // Relationship to the User
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
