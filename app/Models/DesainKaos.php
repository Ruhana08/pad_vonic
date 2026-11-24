<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DesainKaos extends Model
{
    protected $table = 'desain_kaos';

    protected $primaryKey = 'id_kaos';

    public $timestamps = false;

    protected $fillable = [
        'nama_desain',
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
            'periode_kaos',
            'id_kaos',
            'id_periode'
        );
    }

    public function galeri()
    {
        return $this->hasMany(
            GaleriDesainKaos::class,
            'id_kaos',
            'id_kaos'
        );
    }
}