<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('partisipasi', function (Blueprint $table) {
            $table->enum('pilihan_kelompok', ['dipisah', 'digabung'])->nullable()->after('id_ukuran');
        });
    }

    public function down(): void
    {
        Schema::table('partisipasi', function (Blueprint $table) {
            $table->dropColumn('pilihan_kelompok');
        });
    }
};
