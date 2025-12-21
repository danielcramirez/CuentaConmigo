<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Referral;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;

class ReferralController extends Controller
{
    public function index(): View
    {
        return view('admin.referrals.index');
    }

    public function graphData(): JsonResponse
    {
        $users = User::select('id', 'username')->get();
        $referrals = Referral::select('referrer_id', 'referred_id')->get();

        $nodes = $users->map(function ($user) {
            return [
                'id' => $user->id,
                'label' => $user->username,
            ];
        });

        $edges = $referrals->map(function ($ref) {
            return [
                'from' => $ref->referrer_id,
                'to' => $ref->referred_id,
            ];
        });

        return response()->json([
            'nodes' => $nodes,
            'edges' => $edges,
        ]);
    }
}
