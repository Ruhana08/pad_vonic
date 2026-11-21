<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('paket_wisata', function (Blueprint $table) {
            $table->string('kategori')->nullable()->after('foto');
            $table->string('provinsi')->nullable()->after('kategori');
            $table->decimal('rating', 3, 1)->nullable()->after('provinsi');
            $table->integer('total_kunjungan')->nullable()->after('rating');
        });
    }

    public function down(): void
    {
        Schema::table('paket_wisata', function (Blueprint $table) {
            $table->dropColumn(['kategori', 'provinsi', 'rating', 'total_kunjungan']);
        });
    }
};
