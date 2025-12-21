<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DashboardResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'modules' => $this['modules']->map(function ($module) {
                return [
                    'id' => $module->id,
                    'key' => $module->key,
                    'name' => $module->name,
                    'description' => $module->description,
                    'icon' => $module->icon,
                ];
            }),
            'roles' => $this['roles'],
            'navigation' => $this->buildNavigation($this['modules']),
            'stats' => [
                'events_count' => $this['user']->createdEvents()->count(),
                'notifications_count' => $this['user']->notifications()->whereNull('read_at')->count(),
            ],
        ];
    }

    private function buildNavigation($modules)
    {
        $nav = [];

        if ($modules->contains('key', 'events')) {
            $nav[] = ['label' => 'Mis Eventos', 'route' => '/events', 'icon' => 'calendar'];
        }

        if ($modules->contains('key', 'referrals')) {
            $nav[] = ['label' => 'Invitar', 'route' => '/referrals', 'icon' => 'share'];
        }

        if ($modules->contains('key', 'notifications')) {
            $nav[] = ['label' => 'Notificaciones', 'route' => '/notifications', 'icon' => 'bell'];
        }

        return $nav;
    }
}
