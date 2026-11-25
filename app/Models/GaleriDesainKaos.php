<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GaleriDesainKaos extends Model
{
    protected $table = 'galeri_desain_kaos';

    protected $primaryKey = 'id_galeri';

    protected $fillable = [
        'id_kaos',
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

    public function desainKaos()
    {
        return $this->belongsTo(DesainKaos::class, 'id_kaos', 'id_kaos');
    }
}
