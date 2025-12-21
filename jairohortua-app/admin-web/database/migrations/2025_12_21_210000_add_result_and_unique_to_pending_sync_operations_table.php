<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pending_sync_operations', function (Blueprint $table) {
            if (!Schema::hasColumn('pending_sync_operations', 'result')) {
                $table->json('result')->nullable()->after('client_uuid');
            }

            $table->unique(['user_id', 'client_uuid'], 'pending_sync_user_uuid_unique');
        });
    }

    public function down(): void
    {
        Schema::table('pending_sync_operations', function (Blueprint $table) {
            $table->dropUnique('pending_sync_user_uuid_unique');

            if (Schema::hasColumn('pending_sync_operations', 'result')) {
                $table->dropColumn('result');
            }
        });
    }
};
