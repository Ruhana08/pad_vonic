<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class MasterAdminSeeder extends Seeder
{
    public function run(): void
    {
        $email = strtolower(trim((string) env('MASTER_ADMIN_EMAIL', '')));
        $password = (string) env('MASTER_ADMIN_PASSWORD', '');
        $nama = trim((string) env('MASTER_ADMIN_NAME', 'Master Admin Vonic'));

        if ($email === '' || $password === '') {
            $this->command?->warn('MASTER_ADMIN_EMAIL atau MASTER_ADMIN_PASSWORD belum diisi di .env. Seeder dilewati.');
            return;
        }

        if (!str_ends_with($email, '@mail.ugm.ac.id')) {
            $this->command?->error('MASTER_ADMIN_EMAIL harus menggunakan domain @mail.ugm.ac.id.');
            return;
        }

        User::updateOrCreate(
            ['email' => $email],
            [
                'nama'       => $nama !== '' ? $nama : 'Master Admin Vonic',
                'password'   => Hash::make($password),
                'role'       => 'master_admin',
                'unit_kerja' => 'DTEDI SV UGM',
                'pekerjaan'  => 'Master Administrator',
            ]
        );

        $this->command?->info("Master Admin ({$email}) berhasil disiapkan.");
    }
}
