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
        Schema::create('periode_paket_wisata', function (Blueprint $table) {
            $table->foreignId('id_periode')
              ->constrained('periode_polling', 'id_periode');

            $table->foreignId('id_paket')
                ->constrained('paket_wisata', 'id_paket');

            $table->primary(['id_periode', 'id_paket']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('periode_paket_wisata');
    }
};
