<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PeriodeKaos extends Model
{
    protected $table = 'periode_kaos';

    public $timestamps = false;

    protected $fillable = [
        'id_periode',
        'id_kaos',
    ];
}