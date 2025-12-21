<?php

namespace Database\Seeders;

use App\Models\Banner;
use Illuminate\Database\Seeder;

class BannerSeeder extends Seeder
{
    public function run(): void
    {
        Banner::create([
            'image_url' => 'https://via.placeholder.com/1200x400?text=Banner+1',
            'target_url' => 'https://jairohortua.com',
            'order' => 1,
            'is_active' => true,
        ]);

        Banner::create([
            'image_url' => 'https://via.placeholder.com/1200x400?text=Banner+2',
            'target_url' => 'https://jairohortua.com/promo',
            'order' => 2,
            'is_active' => false,
        ]);
    }
}
