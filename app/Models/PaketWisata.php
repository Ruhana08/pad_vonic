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
        'provinsi',
        'kategori',
        'jarak',
        'durasi_perjalanan',
        'benefit',
        'harga',
        'foto',
        'total_kunjungan',
        'hidden_from_library',
    ];

    protected $appends = [
        'foto_url',
    ];

    public function getFotoUrlAttribute()
    {
        if (!$this->foto) {
            return null;
        }
        if (str_starts_with($this->foto, 'http://') || str_starts_with($this->foto, 'https://') || str_starts_with($this->foto, '/')) {
            return $this->foto;
        }
        return url('storage/' . $this->foto);
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