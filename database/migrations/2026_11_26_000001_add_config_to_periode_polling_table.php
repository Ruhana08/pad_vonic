<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('periode_polling', function (Blueprint $table) {
            if (!Schema::hasColumn('periode_polling', 'config')) {
                $table->longText('config')->nullable()->after('mode_kelompok');
            }
        });
    }

    public function down(): void
    {
        Schema::table('periode_polling', function (Blueprint $table) {
            if (Schema::hasColumn('periode_polling', 'config')) {
                $table->dropColumn('config');
            }
        });
    }
};
