<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EventNotificationBatch extends Model
{
    use HasFactory;

    protected $fillable = [
        'event_id',
        'users_targeted',
        'users_sent',
        'radius_km',
        'days_window',
    ];

    public function event()
    {
        return $this->belongsTo(Event::class);
    }
}
