<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('user_locations', function (Blueprint $table) {
            if (!Schema::hasColumn('user_locations', 'accuracy')) {
                $table->float('accuracy')->nullable()->after('longitude');
            }
        });
    }

    public function down(): void
    {
        Schema::table('user_locations', function (Blueprint $table) {
            if (Schema::hasColumn('user_locations', 'accuracy')) {
                $table->dropColumn('accuracy');
            }
        });
    }
};
