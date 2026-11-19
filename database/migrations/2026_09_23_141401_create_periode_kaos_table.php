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
        Schema::create('periode_kaos', function (Blueprint $table) {
            $table->foreignId('id_periode')
                ->constrained('periode_polling', 'id_periode');

            $table->foreignId('id_kaos')
                ->constrained('desain_kaos', 'id_kaos');

            $table->primary(['id_periode', 'id_kaos']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('periode_kaos');
    }
};
