<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Banner extends Model
{
    use HasFactory;

    protected $fillable = [
        'image_url',
        'target_url',
        'order',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'order' => 'integer',
        'updated_at' => 'datetime',
    ];

    // Scope para obtener banner activo
    public static function active()
    {
        return self::where('is_active', true)
            ->orderBy('order', 'asc')
            ->first();
    }
}
