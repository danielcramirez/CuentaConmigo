<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Jobs\SendEventNotificationsJob;
use App\Models\Event;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class EventController extends Controller
{
    public function index(): View
    {
        return view('admin.events.index', [
            'events' => Event::orderBy('starts_at', 'desc')->get(),
        ]);
    }

    public function create(): View
    {
        return view('admin.events.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'title' => 'required|string',
            'description' => 'nullable|string',
            'image_url' => 'nullable|string',
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'starts_at' => 'required|date',
            'radius_km' => 'nullable|integer|min:1',
            'days_window' => 'nullable|integer|min:1',
        ]);

        $event = Event::create([
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
            'image_url' => $validated['image_url'] ?? null,
            'latitude' => $validated['latitude'],
            'longitude' => $validated['longitude'],
            'starts_at' => $validated['starts_at'],
            'radius_km' => $validated['radius_km'] ?? null,
            'days_window' => $validated['days_window'] ?? null,
            'created_by' => auth()->id(),
        ]);

        $this->updateEventLocationPoint($event);

        SendEventNotificationsJob::dispatch($event);

        return redirect()->route('admin.events.index')->with('success', 'Event created');
    }

    public function edit(Event $event): View
    {
        return view('admin.events.edit', [
            'event' => $event,
        ]);
    }

    public function update(Request $request, Event $event): RedirectResponse
    {
        $validated = $request->validate([
            'title' => 'required|string',
            'description' => 'nullable|string',
            'image_url' => 'nullable|string',
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'starts_at' => 'required|date',
            'radius_km' => 'nullable|integer|min:1',
            'days_window' => 'nullable|integer|min:1',
        ]);

        $event->update([
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
            'image_url' => $validated['image_url'] ?? null,
            'latitude' => $validated['latitude'],
            'longitude' => $validated['longitude'],
            'starts_at' => $validated['starts_at'],
            'radius_km' => $validated['radius_km'] ?? null,
            'days_window' => $validated['days_window'] ?? null,
        ]);

        $this->updateEventLocationPoint($event);

        SendEventNotificationsJob::dispatch($event);

        return redirect()->route('admin.events.index')->with('success', 'Event updated');
    }

    public function destroy(Event $event): RedirectResponse
    {
        $event->delete();
        return redirect()->route('admin.events.index')->with('success', 'Event deleted');
    }

    private function updateEventLocationPoint(Event $event): void
    {
        try {
            DB::statement(
                "UPDATE events SET location = ST_PointFromText(CONCAT('POINT(', longitude, ' ', latitude, ')'), 4326) WHERE id = ?",
                [$event->id]
            );
        } catch (\Exception $e) {
            // Ignore if spatial is not supported
        }
    }
}
