<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('paket_wisata', function (Blueprint $table) {
            $table->id('id_paket');
            $table->string('nama_destinasi');
            $table->string('lokasi')->nullable();
            $table->string('jarak')->nullable();
            $table->string('durasi_perjalanan')->nullable();
            $table->text('benefit')->nullable();
            $table->integer('harga')->nullable();
            $table->string('foto')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('paket_wisata');
    }
};
