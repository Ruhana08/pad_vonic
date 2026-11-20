<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DetailUkuranVote extends Model
{
    protected $table = 'detail_ukuran_vote';

    protected $primaryKey = 'id_detail';

    public $timestamps = false;

    protected $fillable = [
        'id_vote',
        'id_ukuran',
        'jumlah',
    ];

    public function partisipasi()
    {
        return $this->belongsTo(Partisipasi::class, 'id_vote', 'id_vote');
    }

    public function ukuranKaos()
    {
        return $this->belongsTo(UkuranKaos::class, 'id_ukuran', 'id_ukuran');
    }
}
