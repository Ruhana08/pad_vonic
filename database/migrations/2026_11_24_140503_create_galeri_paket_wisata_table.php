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
        Schema::create('galeri_paket_wisata', function (Blueprint $table) {
            $table->id('id_galeri');

            $table->unsignedBigInteger('id_paket');
            $table->string('foto', 255);

            $table->timestamps();

            $table->foreign('id_paket')
                ->references('id_paket')
                ->on('paket_wisata')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('galeri_paket_wisata');
    }
};
