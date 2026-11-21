<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('partisipasi_kaos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_vote')
                ->constrained('partisipasi', 'id_vote')
                ->cascadeOnDelete();
            $table->foreignId('id_ukuran')
                ->constrained('ukuran_kaos', 'id_ukuran');
            $table->unsignedInteger('jumlah')->default(1);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('partisipasi_kaos');
    }
};
