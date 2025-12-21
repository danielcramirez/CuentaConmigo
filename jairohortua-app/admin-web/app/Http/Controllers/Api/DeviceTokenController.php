<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\DeviceToken;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DeviceTokenController extends Controller
{
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'token' => 'required|string',
            'platform' => 'required|in:android,ios,web',
        ]);

        $deviceToken = DeviceToken::updateOrCreate(
            [
                'user_id' => auth()->id(),
                'token' => $validated['token'],
            ],
            [
                'platform' => $validated['platform'],
                'last_seen_at' => now(),
            ]
        );

        return response()->json([
            'id' => $deviceToken->id,
            'token' => $deviceToken->token,
            'platform' => $deviceToken->platform,
            'last_seen_at' => $deviceToken->last_seen_at,
        ], 201);
    }
}
