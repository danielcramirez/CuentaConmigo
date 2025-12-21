<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles;

    protected $fillable = [
        'username',
        'email',
        'password',
        'referral_code',
        'referrer_id',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    // Relaciones
    public function profile()
    {
        return $this->hasOne(Profile::class);
    }

    public function locations()
    {
        return $this->hasMany(UserLocation::class);
    }

    public function referrals()
    {
        return $this->hasMany(Referral::class, 'referrer_id');
    }

    public function referrer()
    {
        return $this->belongsTo(User::class, 'referrer_id');
    }

    public function notifications()
    {
        return $this->hasMany(Notification::class);
    }

    public function deviceTokens()
    {
        return $this->hasMany(DeviceToken::class);
    }

    public function modules()
    {
        return Module::query()
            ->select('modules.*')
            ->join('role_modules', 'role_modules.module_id', '=', 'modules.id')
            ->join('model_has_roles', 'model_has_roles.role_id', '=', 'role_modules.role_id')
            ->where('model_has_roles.model_id', $this->id)
            ->where('model_has_roles.model_type', self::class)
            ->where('role_modules.is_visible', true)
            ->where('modules.is_active', true)
            ->orderBy('modules.name');
    }

    public function createdEvents()
    {
        return $this->hasMany(Event::class, 'created_by');
    }

    // Metodos auxiliares
    public function generateReferralCode()
    {
        $this->referral_code = strtoupper(
            substr($this->username, 0, 5) .
            substr(uniqid(), -6)
        );
        $this->save();
        return $this->referral_code;
    }

    public function lastLocation()
    {
        return $this->locations()->latest()->first();
    }
}
