<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, Notifiable;

    protected $table = 'users';

    protected $primaryKey = 'id_user';

    const UPDATED_AT = null;

    protected $fillable = [
        'nama',
        'nip',
        'email',
        'pekerjaan',
        'unit_kerja',
        'no_telp',
        'foto',
        'kategori',
        'role',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];
    
    public function periodePolling()
    {
        return $this->hasMany(PeriodePolling::class, 'created_by', 'id_user');
    }
}