<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'username' => $this->username,
            'email' => $this->email,
            'referral_code' => $this->referral_code,
            'roles' => $this->roles->pluck('name'),
            'modules' => $this->modules()->pluck('modules.key'),
            'profile' => $this->profile ? [
                'id' => $this->profile->id,
                'user_id' => $this->profile->user_id,
                'avatar_url' => $this->profile->avatar_url,
                'bio' => $this->profile->bio,
                'phone' => $this->profile->phone,
            ] : null,
            'updated_at' => $this->updated_at,
        ];
    }
}
