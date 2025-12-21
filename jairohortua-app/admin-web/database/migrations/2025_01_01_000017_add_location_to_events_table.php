<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('events', 'location')) {
            try {
                DB::statement('ALTER TABLE events ADD COLUMN location POINT SRID 4326');
                DB::statement('CREATE SPATIAL INDEX idx_events_location ON events(location)');
            } catch (\Exception $e) {
                // Ignore if spatial not supported
            }
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('events', 'location')) {
            try {
                DB::statement('ALTER TABLE events DROP COLUMN location');
            } catch (\Exception $e) {
                // Ignore
            }
        }
    }
};
