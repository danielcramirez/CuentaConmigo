<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = auth()->user()->notifications();

        // Filtro opcional: solo no leÃ­das
        if ($request->input('read') === 'false') {
            $query->whereNull('read_at');
        }

        $limit = $request->input('limit', 50);
        $offset = $request->input('offset', 0);

        $notifications = $query->orderBy('created_at', 'desc')
            ->limit($limit)
            ->offset($offset)
            ->get();

        return response()->json([
            'data' => $notifications,
            'unread_count' => auth()->user()->notifications()->whereNull('read_at')->count(),
            'pagination' => [
                'total' => auth()->user()->notifications()->count(),
                'limit' => $limit,
                'offset' => $offset,
            ],
        ]);
    }

    public function read(Notification $notification): JsonResponse
    {
        // Verificar que pertenece al usuario actual
        if ($notification->user_id !== auth()->id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $notification->markAsRead();

        return response()->json([
            'id' => $notification->id,
            'read_at' => $notification->read_at,
        ]);
    }
}
