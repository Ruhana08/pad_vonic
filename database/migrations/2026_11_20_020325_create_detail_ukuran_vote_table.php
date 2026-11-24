<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('detail_ukuran_vote', function (Blueprint $table) {
            $table->id('id_detail');

            $table->foreignId('id_vote')
                ->constrained('partisipasi', 'id_vote')
                ->cascadeOnDelete();

            $table->foreignId('id_ukuran')
                ->constrained('ukuran_kaos', 'id_ukuran');

            $table->integer('jumlah')->default(1);

            $table->unique(['id_vote', 'id_ukuran']);
        });

        // Memindahkan ukuran dari vote lama ke tabel detail
        DB::table('partisipasi')->orderBy('id_vote')->get()
            ->each(function ($vote) {
                DB::table('detail_ukuran_vote')->insert([
                    'id_vote' => $vote->id_vote,
                    'id_ukuran' => $vote->id_ukuran,
                    'jumlah' => 1,
                ]);
            });

        Schema::table('partisipasi', function (Blueprint $table) {
            $table->dropForeign(['id_ukuran']);
            $table->dropColumn('id_ukuran');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('partisipasi', function (Blueprint $table) {
            $table->foreignId('id_ukuran')
                ->nullable()
                ->constrained('ukuran_kaos', 'id_ukuran');
        });

        DB::table('detail_ukuran_vote')->get()
            ->each(function ($detail) {
                DB::table('partisipasi')
                    ->where('id_vote', $detail->id_vote)
                    ->update(['id_ukuran' => $detail->id_ukuran]);
            });

        Schema::dropIfExists('detail_ukuran_vote');
    }
};
