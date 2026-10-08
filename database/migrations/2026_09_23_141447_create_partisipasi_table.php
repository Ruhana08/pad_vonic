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
        Schema::create('partisipasi', function (Blueprint $table) {
            $table->id('id_vote');

            $table->foreignId('id_user')
                ->constrained('users', 'id_user');

            $table->foreignId('id_periode')
                ->constrained('periode_polling', 'id_periode');

            $table->foreignId('id_paket')
                ->constrained('paket_wisata', 'id_paket');

            $table->foreignId('id_kaos')
                ->constrained('desain_kaos', 'id_kaos');

            $table->foreignId('id_ukuran')
                ->constrained('ukuran_kaos', 'id_ukuran');

            $table->timestamp('waktu_submit')->nullable();

            $table->unique(['id_user', 'id_periode']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('partisipasi');
    }
};
