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
        Schema::create('galeri_desain_kaos', function (Blueprint $table) {
            $table->id('id_galeri');

            $table->unsignedBigInteger('id_kaos');
            $table->string('foto', 255);

            $table->timestamps();

            $table->foreign('id_kaos')
                ->references('id_kaos')
                ->on('desain_kaos')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('galeri_desain_kaos');
    }
};
