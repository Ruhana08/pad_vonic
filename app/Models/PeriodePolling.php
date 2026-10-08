<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PeriodePolling extends Model
{
    protected $table = 'periode_polling';

    protected $primaryKey = 'id_periode';

    public $timestamps = false;

    protected $fillable = [
        'nama_periode',
        'tanggal_mulai',
        'tanggal_selesai',
        'status',
        'mode_kelompok',
        'created_by',
    ];

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by', 'id_user');
    }

    public function paketWisata()
    {
        return $this->belongsToMany(
            PaketWisata::class,
            'periode_paket_wisata',
            'id_periode',
            'id_paket'
        );
    }

    public function desainKaos()
    {
        return $this->belongsToMany(
            DesainKaos::class,
            'periode_kaos',
            'id_periode',
            'id_kaos'
        );
    }
}