<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('partisipasi', function (Blueprint $table) {
            $table->string('pilihan_kelompok', 255)->nullable()->change();
        });

        Schema::table('paket_wisata', function (Blueprint $table) {
            if (!Schema::hasColumn('paket_wisata', 'hidden_from_library')) {
                $table->boolean('hidden_from_library')->default(false)->after('foto');
            }
        });
    }

    public function down(): void
    {
        Schema::table('paket_wisata', function (Blueprint $table) {
            if (Schema::hasColumn('paket_wisata', 'hidden_from_library')) {
                $table->dropColumn('hidden_from_library');
            }
        });
    }
};
