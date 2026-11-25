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
        if (!$this->foto) {
            return null;
        }
        if (str_starts_with($this->foto, 'http://') || str_starts_with($this->foto, 'https://') || str_starts_with($this->foto, '/')) {
            return $this->foto;
        }
        return url('storage/' . $this->foto);
    }

    public function paketWisata()
    {
        return $this->belongsTo(PaketWisata::class, 'id_paket', 'id_paket');
    }
}
