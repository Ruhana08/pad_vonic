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

    protected $appends = [
        'foto_url',
    ];

    public function getFotoUrlAttribute()
    {
        return $this->foto ? url('storage/' . $this->foto) : null;
    }

    public function periodePolling()
    {
        return $this->belongsToMany(
            PeriodePolling::class,
            'periode_paket_wisata',
            'id_paket',
            'id_periode'
        );
    }

    public function galeri()
    {
        return $this->hasMany(
            GaleriPaketWisata::class,
            'id_paket',
            'id_paket'
        );
    }
}