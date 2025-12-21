<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Referral;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ReferralController extends Controller
{
    public function useCode(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'code' => 'required|string',
        ]);

        $user = auth()->user();
        $code = strtoupper(trim($validated['code']));

        if ($user->referrer_id) {
            return response()->json(['message' => 'Referral already set'], 409);
        }

        $referrer = User::where('referral_code', $code)->first();
        if (!$referrer) {
            return response()->json(['message' => 'Invalid referral code'], 404);
        }

        if ($referrer->id === $user->id) {
            return response()->json(['message' => 'You cannot refer yourself'], 400);
        }

        $user->referrer_id = $referrer->id;
        $user->save();

        $referral = Referral::firstOrCreate([
            'referrer_id' => $referrer->id,
            'referred_id' => $user->id,
        ], [
            'status' => 'active',
        ]);

        return response()->json([
            'message' => 'Referral applied',
            'referrer_id' => $referrer->id,
            'referred_id' => $user->id,
            'status' => $referral->status,
        ], 201);
    }

    public function myStats(): JsonResponse
    {
        $user = auth()->user();

        return response()->json([
            'user_id' => $user->id,
            'referral_code' => $user->referral_code,
            'total_referred' => $user->referrals()->count(),
            'active_referred' => $user->referrals()->where('status', 'active')->count(),
        ]);
    }
}
