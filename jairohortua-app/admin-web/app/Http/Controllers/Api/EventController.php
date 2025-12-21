<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\EventAttendance;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class EventController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Event::query();

        if ($request->has('latitude') && $request->has('longitude')) {
            $latitude = (float) $request->input('latitude');
            $longitude = (float) $request->input('longitude');
            $radius = (float) $request->input('radius_km', 50);

            $haversine = '(6371 * acos(cos(radians(?)) * cos(radians(latitude)) * cos(radians(longitude) - radians(?)) + sin(radians(?)) * sin(radians(latitude))))';
            $query->select('*')
                ->selectRaw("{$haversine} as distance", [$latitude, $longitude, $latitude])
                ->having('distance', '<=', $radius)
                ->orderBy('distance', 'asc');
        } else {
            $query->orderBy('starts_at', 'desc');
        }

        $limit = (int) $request->input('limit', 50);
        $offset = (int) $request->input('offset', 0);

        $events = $query->limit($limit)
            ->offset($offset)
            ->get();

        return response()->json([
            'data' => $events,
            'pagination' => [
                'total' => Event::count(),
                'limit' => $limit,
                'offset' => $offset,
            ],
        ]);
    }

    public function show(Event $event): JsonResponse
    {
        return response()->json([
            'id' => $event->id,
            'title' => $event->title,
            'description' => $event->description,
            'image_url' => $event->image_url,
            'latitude' => $event->latitude,
            'longitude' => $event->longitude,
            'starts_at' => $event->starts_at,
            'created_by' => [
                'id' => $event->creator->id,
                'username' => $event->creator->username,
            ],
            'created_at' => $event->created_at,
            'updated_at' => $event->updated_at,
        ]);
    }

    public function attend(Event $event): JsonResponse
    {
        $attendance = EventAttendance::firstOrCreate([
            'event_id' => $event->id,
            'user_id' => auth()->id(),
        ]);

        return response()->json([
            'event_id' => $attendance->event_id,
            'user_id' => $attendance->user_id,
            'created_at' => $attendance->created_at,
        ], 201);
    }
}
