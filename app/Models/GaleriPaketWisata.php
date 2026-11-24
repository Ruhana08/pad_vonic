<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GaleriPaketWisata extends Model
{
    protected $table = 'galeri_paket_wisata';

    protected $primaryKey = 'id_galeri';

    protected $fillable = [
        'id_paket',
        'foto',
    ];

    protected $appends = [
        'foto_url',
    ];

    public function getFotoUrlAttribute()
    {
        return $this->foto ? url('storage/' . $this->foto) : null;
    }

    public function paketWisata()
    {
        return $this->belongsTo(PaketWisata::class, 'id_paket', 'id_paket');
    }
}
