<?php

namespace App\Jobs;

use App\Models\Event;
use App\Models\EventNotificationBatch;
use App\Models\Notification;
use App\Models\Setting;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;

class SendEventNotificationsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private Event $event;

    public function __construct(Event $event)
    {
        $this->event = $event;
    }

    public function handle(): void
    {
        $radiusKm = $this->event->radius_km ?? Setting::get('notification_radius_km', 20);
        $daysWindow = $this->event->days_window ?? Setting::get('notification_days_window', 15);

        $latest = DB::table('user_locations')
            ->select('user_id', DB::raw('MAX(created_at) as max_created_at'))
            ->groupBy('user_id');

        $baseQuery = DB::table('user_locations as ul')
            ->joinSub($latest, 'latest', function ($join) {
                $join->on('ul.user_id', '=', 'latest.user_id')
                    ->on('ul.created_at', '=', 'latest.max_created_at');
            })
            ->where('ul.created_at', '>=', now()->subDays($daysWindow))
            ->where('ul.user_id', '!=', $this->event->created_by);

        $userIds = [];
        try {
            $point = "POINT({$this->event->longitude} {$this->event->latitude})";
            $userIds = $baseQuery
                ->whereNotNull('ul.location')
                ->whereRaw(
                    'ST_Distance_Sphere(ul.location, ST_GeomFromText(?, 4326)) <= ?',
                    [$point, $radiusKm * 1000]
                )
                ->pluck('ul.user_id')
                ->all();
        } catch (\Exception $e) {
            $userIds = $baseQuery
                ->where('ul.latitude', '>=', $this->event->latitude - ($radiusKm / 111))
                ->where('ul.latitude', '<=', $this->event->latitude + ($radiusKm / 111))
                ->where('ul.longitude', '>=', $this->event->longitude - ($radiusKm / (111 * cos(deg2rad($this->event->latitude)))))
                ->where('ul.longitude', '<=', $this->event->longitude + ($radiusKm / (111 * cos(deg2rad($this->event->latitude)))))
                ->pluck('ul.user_id')
                ->all();
        }

        $usersSent = 0;

        foreach ($userIds as $userId) {
            try {
                Notification::create([
                    'user_id' => $userId,
                    'title' => "Nuevo evento: {$this->event->title}",
                    'message' => "Hay un evento a tu alrededor: {$this->event->description}",
                    'type' => 'event_proximity',
                ]);

                $usersSent++;
            } catch (\Exception $e) {
                \Log::error("Error sending notification to user {$userId}", ['error' => $e->getMessage()]);
            }
        }

        EventNotificationBatch::create([
            'event_id' => $this->event->id,
            'users_targeted' => count($userIds),
            'users_sent' => $usersSent,
            'radius_km' => $radiusKm,
            'days_window' => $daysWindow,
        ]);
    }
}
