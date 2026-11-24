<?php

namespace App\Imports;

use App\Models\User;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class PesertaImport implements ToCollection, WithHeadingRow
{
    public int $berhasil = 0;
    public int $dilewati = 0;

    public function collection(Collection $rows)
    {
        foreach ($rows as $row) {
            $nama = trim((string) ($row['nama'] ?? ''));
            $nip = trim((string) ($row['nip'] ?? ''));
            $email = trim((string) ($row['email'] ?? ''));

            // Lewati jika data wajib kosong
            if ($nama === '' || $nip === '' || $email === '') {
                $this->dilewati++;
                continue;
            }

            // Lewati jika email atau NIP sudah terdaftar
            if (
                User::where('email', $email)->exists() ||
                User::where('nip', $nip)->exists()
            ) {
                $this->dilewati++;
                continue;
            }

            // Tambahkan peserta baru
            User::create([
                'nama' => $nama,
                'nip' => $nip,
                'email' => $email,
                'pekerjaan' => $row['pekerjaan'] ?? null,
                'unit_kerja' => $row['unit_kerja'] ?? null,
                'no_telp' => $row['no_telp'] ?? null,
                'kategori' => strtolower(trim((string) ($row['kategori'] ?? ''))),
                'role' => 'user',
            ]);

            $this->berhasil++;
        }
    }
}
