<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Notifikasi extends Model
{
    protected $table = 'notifikasi';

    protected $primaryKey = 'id_notifikasi';

    protected $fillable = [
        'id_user',
        'judul',
        'pesan',
        'link',
        'is_read',
    ];

    protected $casts = [
        'is_read' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Relasi ke User penerima notifikasi.
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'id_user', 'id_user');
    }

    /**
     * Helper statik untuk membuat notifikasi dengan mudah dari Controller / Observer / Event.
     *
     * Contoh penggunaan:
     *   Notifikasi::kirim($idUser, 'Judul', 'Isi pesan', '/voting');
     */
    public static function kirim(int $idUser, string $judul, string $pesan, ?string $link = null): self
    {
        return self::create([
            'id_user' => $idUser,
            'judul'   => $judul,
            'pesan'   => $pesan,
            'link'    => $link,
            'is_read' => false,
        ]);
    }
}
