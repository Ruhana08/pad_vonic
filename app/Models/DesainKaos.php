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