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

    public function periodePolling()
    {
        return $this->belongsToMany(
            PeriodePolling::class,
            'periode_kaos',
            'id_kaos',
            'id_periode'
        );
    }
}