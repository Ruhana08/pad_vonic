<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UkuranKaos extends Model
{
    protected $table = 'ukuran_kaos';

    protected $primaryKey = 'id_ukuran';

    public $timestamps = false;

    protected $fillable = [
        'kode_ukuran',
    ];
}