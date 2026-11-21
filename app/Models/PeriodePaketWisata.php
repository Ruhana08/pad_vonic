<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PeriodePaketWisata extends Model
{
    protected $table = 'periode_paket_wisata';

    public $timestamps = false;

    protected $fillable = [
        'id_periode',
        'id_paket',
    ];
}