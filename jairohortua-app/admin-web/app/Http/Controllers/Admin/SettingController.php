<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SettingController extends Controller
{
    public function index(): View
    {
        $keys = [
            'notification_radius_km',
            'notification_days_window',
            'social_facebook_url',
            'social_instagram_url',
        ];

        $settings = Setting::whereIn('key', $keys)->get()->keyBy('key');

        return view('admin.settings.index', [
            'settings' => $settings,
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'notification_radius_km' => 'required|integer|min:1',
            'notification_days_window' => 'required|integer|min:1',
            'social_facebook_url' => 'nullable|string',
            'social_instagram_url' => 'nullable|string',
        ]);

        foreach ($validated as $key => $value) {
            Setting::set($key, $value);
        }

        return redirect()->route('admin.settings.index')->with('success', 'Settings updated');
    }
}
