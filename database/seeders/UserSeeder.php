<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            'nama' => 'Admin Vonic',
            'nip' => 'ADMIN001',
            'email' => 'admin@vonic.test',
            'pekerjaan' => 'Administrator',
            'unit_kerja' => 'DTEDI SV UGM',
            'no_telp' => '081234567890',
            'foto' => null,
            'kategori' => null,
            'role' => 'admin',
        ]);

        User::create([
            'nama' => 'Dosen Dummy 1',
            'nip' => 'DOSEN001',
            'email' => 'dosen1@vonic.test',
            'pekerjaan' => 'Dosen',
            'unit_kerja' => 'DTEDI SV UGM',
            'no_telp' => '081234567891',
            'foto' => null,
            'kategori' => 'dosen',
            'role' => 'user',
        ]);

        User::create([
            'nama' => 'Dosen Dummy 2',
            'nip' => 'DOSEN002',
            'email' => 'dosen2@vonic.test',
            'pekerjaan' => 'Dosen',
            'unit_kerja' => 'DTEDI SV UGM',
            'no_telp' => '081234567892',
            'foto' => null,
            'kategori' => 'dosen',
            'role' => 'user',
        ]);

        User::create([
            'nama' => 'Tendik Dummy 1',
            'nip' => 'TENDIK001',
            'email' => 'tendik1@vonic.test',
            'pekerjaan' => 'Tendik',
            'unit_kerja' => 'DTEDI SV UGM',
            'no_telp' => '081234567893',
            'foto' => null,
            'kategori' => 'tendik',
            'role' => 'user',
        ]);
    }
}