<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaketWisata extends Model
{
    protected $table = 'paket_wisata';

    protected $primaryKey = 'id_paket';

    public $timestamps = false;

    protected $fillable = [
        'nama_destinasi',
        'lokasi',
        'jarak',
        'durasi_perjalanan',
        'benefit',
        'harga',
        'foto',
    ];
    
    public function periodePolling()
    {
        return $this->belongsToMany(
            PeriodePolling::class,
            'periode_paket_wisata',
            'id_paket',
            'id_periode'
        );
    }
}