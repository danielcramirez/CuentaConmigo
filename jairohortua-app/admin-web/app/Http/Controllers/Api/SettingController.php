<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\JsonResponse;

class SettingController extends Controller
{
    public function app(): JsonResponse
    {
        return response()->json([
            'social_facebook_url' => Setting::get('social_facebook_url'),
            'social_instagram_url' => Setting::get('social_instagram_url'),
            'notification_radius_km' => (int) Setting::get('notification_radius_km', 20),
            'notification_days_window' => (int) Setting::get('notification_days_window', 15),
            'app_name' => Setting::get('app_name', config('app.name')),
            'app_version' => Setting::get('app_version', '1.0.0'),
        ]);
    }
}
