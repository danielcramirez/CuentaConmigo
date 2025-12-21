<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PendingSyncOperation extends Model
{
    use HasFactory;

    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    protected $fillable = [
        'id',
        'user_id',
        'operation_type',
        'payload',
        'status',
        'client_uuid',
        'result',
        'created_at',
        'processed_at',
    ];

    protected $casts = [
        'payload' => 'array',
        'result' => 'array',
        'created_at' => 'datetime',
        'processed_at' => 'datetime',
    ];
}
