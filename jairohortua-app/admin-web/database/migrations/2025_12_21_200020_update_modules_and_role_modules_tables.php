<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('modules', function (Blueprint $table) {
            if (!Schema::hasColumn('modules', 'route')) {
                $table->string('route')->nullable()->after('icon');
            }
            if (!Schema::hasColumn('modules', 'order')) {
                $table->integer('order')->default(0)->after('route');
            }
            if (!Schema::hasColumn('modules', 'is_active')) {
                $table->boolean('is_active')->default(true)->after('order');
            }
        });

        Schema::table('role_modules', function (Blueprint $table) {
            if (!Schema::hasColumn('role_modules', 'is_visible')) {
                $table->boolean('is_visible')->default(true)->after('module_id');
            }
        });
    }

    public function down(): void
    {
        Schema::table('modules', function (Blueprint $table) {
            if (Schema::hasColumn('modules', 'route')) {
                $table->dropColumn('route');
            }
            if (Schema::hasColumn('modules', 'order')) {
                $table->dropColumn('order');
            }
            if (Schema::hasColumn('modules', 'is_active')) {
                $table->dropColumn('is_active');
            }
        });

        Schema::table('role_modules', function (Blueprint $table) {
            if (Schema::hasColumn('role_modules', 'is_visible')) {
                $table->dropColumn('is_visible');
            }
        });
    }
};
