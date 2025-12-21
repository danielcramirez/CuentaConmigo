<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Banner;
use Illuminate\Http\JsonResponse;

class BannerController extends Controller
{
    public function active(): JsonResponse
    {
        $banner = Banner::active();

        if (!$banner) {
            return response()->json(null, 204);
        }

        return response()->json([
            'id' => $banner->id,
            'image_url' => $banner->image_url,
            'target_url' => $banner->target_url,
            'order' => $banner->order,
            'is_active' => $banner->is_active,
            'updated_at' => $banner->updated_at,
        ]);
    }
}
