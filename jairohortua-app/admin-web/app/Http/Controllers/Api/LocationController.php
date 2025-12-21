<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\UserLocation;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LocationController extends Controller
{
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'accuracy' => 'nullable|numeric',
            'timestamp' => 'nullable|string',
        ]);

        $createdAt = null;
        if (!empty($validated['timestamp'])) {
            try {
                $createdAt = Carbon::parse($validated['timestamp'])->toDateTimeString();
            } catch (\Exception $e) {
                return response()->json([
                    'message' => 'Invalid timestamp',
                    'errors' => ['timestamp' => ['Invalid ISO8601 timestamp']],
                ], 422);
            }
        }

        $location = UserLocation::create([
            'user_id' => auth()->id(),
            'latitude' => $validated['latitude'],
            'longitude' => $validated['longitude'],
            'accuracy' => $validated['accuracy'] ?? null,
            'created_at' => $createdAt ?? now(),
            'updated_at' => now(),
        ]);

        try {
            DB::statement(
                "UPDATE user_locations SET location = ST_PointFromText(CONCAT('POINT(', longitude, ' ', latitude, ')'), 4326) WHERE id = ?",
                [$location->id]
            );
        } catch (\Exception $e) {
        }

        return response()->json([
            'id' => $location->id,
            'user_id' => $location->user_id,
            'latitude' => $location->latitude,
            'longitude' => $location->longitude,
            'accuracy' => $location->accuracy,
            'created_at' => $location->created_at,
        ], 201);
    }
}
