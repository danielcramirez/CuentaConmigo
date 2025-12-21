<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\DashboardResource;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\JsonResponse;

class UserController extends Controller
{
    public function me(): UserResource
    {
        return UserResource::make(auth()->user());
    }

    public function dashboard(): DashboardResource
    {
        $user = auth()->user();

        return DashboardResource::make([
            'user' => $user,
            'modules' => $user->modules()->get(),
            'roles' => $user->roles()->pluck('name'),
        ]);
    }

    public function referrals(?int $id = null): JsonResponse
    {
        $authUser = auth()->user();
        $user = $authUser;

        if ($id !== null && $id !== $authUser->id) {
            if (!$authUser->hasRole('SuperAdmin')) {
                return response()->json(['message' => 'Forbidden'], 403);
            }

            $user = User::findOrFail($id);
        }

        $tree = $this->buildReferralTree($user, 0, 3);

        return response()->json([
            'user_id' => $user->id,
            'referral_code' => $user->referral_code,
            'tree' => $tree,
            'stats' => [
                'total_referred' => $user->referrals()->count(),
                'active_referred' => $user->referrals()->where('status', 'active')->count(),
            ],
        ]);
    }

    private function buildReferralTree($user, $depth, $maxDepth)
    {
        if ($depth >= $maxDepth) {
            return null;
        }

        $children = $user->referrals()
            ->where('status', 'active')
            ->with('referred')
            ->get()
            ->map(function ($referral) use ($depth, $maxDepth) {
                return $this->buildReferralTree($referral->referred, $depth + 1, $maxDepth);
            })
            ->filter()
            ->values();

        return [
            'id' => $user->id,
            'username' => $user->username,
            'referral_code' => $user->referral_code,
            'children' => $children->count() > 0 ? $children : [],
        ];
    }
}
