<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Partisipasi extends Model
{
    protected $table = 'partisipasi';

    protected $primaryKey = 'id_vote';

    public $timestamps = false;

    protected $fillable = [
        'id_user',
        'id_periode',
        'id_paket',
        'id_kaos',
        'id_ukuran',
        'waktu_submit',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'id_user', 'id_user');
    }

    public function periodePolling()
    {
        return $this->belongsTo(PeriodePolling::class, 'id_periode', 'id_periode');
    }

    public function paketWisata()
    {
        return $this->belongsTo(PaketWisata::class, 'id_paket', 'id_paket');
    }

    public function desainKaos()
    {
        return $this->belongsTo(DesainKaos::class, 'id_kaos', 'id_kaos');
    }

    public function ukuranKaos()
    {
        return $this->belongsTo(UkuranKaos::class, 'id_ukuran', 'id_ukuran');
    }
}