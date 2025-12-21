<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Para MySQL, necesitamos crear la tabla con POINT
        Schema::create('user_locations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->double('latitude');
            $table->double('longitude');
            // Para spatial queries, creamos una columna POINT
            // Usaremos raw SQL para esto
            $table->timestamps();
        });

        // AÃ±adimos la columna POINT despuÃ©s (si MySQL soporta)
        try {
            DB::statement('ALTER TABLE user_locations ADD COLUMN location POINT SRID 4326');
            DB::statement('CREATE SPATIAL INDEX idx_user_locations_location ON user_locations(location)');
        } catch (\Exception $e) {
            // Si no soporta POINT, continuamos sin spatial index
            \Log::warning('POINT type not supported, using lat/lng fallback');
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('user_locations');
    }
};
