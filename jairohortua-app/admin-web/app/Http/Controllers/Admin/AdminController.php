<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Event;
use App\Models\Banner;
use App\Models\Notification;
use Illuminate\View\View;
use Illuminate\Http\JsonResponse;

class AdminController extends Controller
{
    public function dashboard(): View
    {
        return view('admin.dashboard.index', [
            'usersCount' => User::count(),
            'eventsCount' => Event::count(),
            'bannersCount' => Banner::count(),
            'notificationsCount' => Notification::count(),
        ]);
    }

    public function stats(): JsonResponse
    {
        return response()->json([
            'users' => User::count(),
            'events' => Event::count(),
            'banners' => Banner::count(),
            'notifications' => Notification::count(),
        ]);
    }
}
