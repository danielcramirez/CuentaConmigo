<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'image_url',
        'latitude',
        'longitude',
        'starts_at',
        'radius_km',
        'days_window',
        'created_by',
    ];

    protected $casts = [
        'starts_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'latitude' => 'float',
        'longitude' => 'float',
        'radius_km' => 'float',
        'days_window' => 'integer',
    ];

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function notificationBatches()
    {
        return $this->hasMany(EventNotificationBatch::class);
    }

    public function attendances()
    {
        return $this->hasMany(EventAttendance::class);
    }
}
