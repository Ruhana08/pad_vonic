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
        return $this->foto ? url('storage/' . $this->foto) : null;
    }

    public function desainKaos()
    {
        return $this->belongsTo(DesainKaos::class, 'id_kaos', 'id_kaos');
    }
}
