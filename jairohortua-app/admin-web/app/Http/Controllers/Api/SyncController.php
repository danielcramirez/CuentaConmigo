<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Banner;
use App\Models\Event;
use App\Models\Notification;
use App\Models\PendingSyncOperation;
use App\Models\Referral;
use App\Models\Setting;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class SyncController extends Controller
{
    /**
     * POST /sync/push
     * Recibe operaciones pendientes del cliente y las aplica (idempotentes)
     */
    public function push(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'operations' => 'required|array',
            'operations.*.client_uuid' => 'required|string',
            'operations.*.op_type' => 'required|string',
            'operations.*.payload' => 'required|array',
        ]);

        $results = [];
        $userId = auth()->id();

        foreach ($validated['operations'] as $operation) {
            $existing = PendingSyncOperation::where('user_id', $userId)
                ->where('client_uuid', $operation['client_uuid'])
                ->first();

            if ($existing) {
                $results[] = $existing->result ?? [
                    'client_uuid' => $operation['client_uuid'],
                    'status' => 'already_processed',
                    'message' => 'Operation already processed',
                ];
                continue;
            }

            $pending = PendingSyncOperation::create([
                'id' => (string) Str::uuid(),
                'user_id' => $userId,
                'operation_type' => $operation['op_type'],
                'payload' => $operation['payload'],
                'status' => 'processing',
                'client_uuid' => $operation['client_uuid'],
                'created_at' => now(),
            ]);

            $result = $this->applyOperation($operation, $userId);
            $results[] = $result;

            $pending->status = $result['status'] === 'applied' ? 'completed' : 'failed';
            $pending->processed_at = now();
            $pending->result = $result;
            $pending->save();
        }

        return response()->json([
            'results' => $results,
            'server_time' => now()->toIso8601String(),
        ]);
    }

    /**
     * GET /sync/pull?since=timestamp
     * Retorna cambios desde un timestamp especifico
     */
    public function pull(Request $request): JsonResponse
    {
        $since = $request->input('since');
        $sinceTime = $since ? Carbon::parse($since) : now()->subDay();

        $userId = auth()->id();

        $events = Event::where('updated_at', '>=', $sinceTime)
            ->limit(50)
            ->get();

        $banners = Banner::where('updated_at', '>=', $sinceTime)
            ->get();

        $notifications = Notification::where('user_id', $userId)
            ->where('created_at', '>=', $sinceTime)
            ->get();

        $referrals = Referral::where('referrer_id', $userId)
            ->where('updated_at', '>=', $sinceTime)
            ->get();

        $modules = auth()->user()->modules()->pluck('modules.key');

        $settings = [
            'notification_radius_km' => Setting::get('notification_radius_km', 20),
            'notification_days_window' => Setting::get('notification_days_window', 15),
            'social_facebook_url' => Setting::get('social_facebook_url'),
            'social_instagram_url' => Setting::get('social_instagram_url'),
        ];

        return response()->json([
            'server_time' => now()->toIso8601String(),
            'changes' => [
                'events' => $events->map(function ($event) {
                    return [
                        'id' => $event->id,
                        'title' => $event->title,
                        'description' => $event->description,
                        'image_url' => $event->image_url,
                        'latitude' => $event->latitude,
                        'longitude' => $event->longitude,
                        'starts_at' => $event->starts_at,
                        'action' => $event->created_at->gte($event->updated_at) ? 'created' : 'updated',
                        'updated_at' => $event->updated_at,
                    ];
                }),
                'banners' => $banners->map(function ($banner) {
                    return [
                        'id' => $banner->id,
                        'image_url' => $banner->image_url,
                        'target_url' => $banner->target_url,
                        'is_active' => $banner->is_active,
                        'action' => 'updated',
                        'updated_at' => $banner->updated_at,
                    ];
                }),
                'notifications' => $notifications->map(function ($notif) {
                    return [
                        'id' => $notif->id,
                        'title' => $notif->title,
                        'message' => $notif->message,
                        'type' => $notif->type,
                        'read_at' => $notif->read_at,
                        'action' => 'created',
                        'updated_at' => $notif->created_at,
                    ];
                }),
                'referrals' => $referrals->map(function ($ref) {
                    return [
                        'id' => $ref->id,
                        'referrer_id' => $ref->referrer_id,
                        'referred_id' => $ref->referred_id,
                        'status' => $ref->status,
                        'action' => 'created',
                        'updated_at' => $ref->created_at,
                    ];
                }),
                'modules' => $modules,
                'settings' => $settings,
            ],
        ]);
    }

    private function applyOperation(array $operation, int $userId): array
    {
        $clientUuid = $operation['client_uuid'];
        $opType = $operation['op_type'];
        $payload = $operation['payload'];

        try {
            switch ($opType) {
                case 'create_event':
                    $event = Event::create([
                        'title' => $payload['title'],
                        'description' => $payload['description'] ?? null,
                        'image_url' => $payload['image_url'] ?? null,
                        'latitude' => $payload['latitude'],
                        'longitude' => $payload['longitude'],
                        'starts_at' => $payload['starts_at'] ?? now(),
                        'created_by' => $userId,
                    ]);
                    return [
                        'client_uuid' => $clientUuid,
                        'status' => 'applied',
                        'server_id' => $event->id,
                        'message' => 'Event created',
                    ];

                case 'mark_notification_read':
                    $notif = Notification::findOrFail($payload['notification_id']);
                    if ($notif->user_id !== $userId) {
                        throw new \Exception('Unauthorized');
                    }
                    $notif->markAsRead();
                    return [
                        'client_uuid' => $clientUuid,
                        'status' => 'applied',
                        'message' => 'Notification marked as read',
                    ];

                default:
                    return [
                        'client_uuid' => $clientUuid,
                        'status' => 'failed',
                        'message' => 'Unknown operation type',
                    ];
            }
        } catch (\Exception $e) {
            return [
                'client_uuid' => $clientUuid,
                'status' => 'failed',
                'message' => $e->getMessage(),
            ];
        }
    }
}
