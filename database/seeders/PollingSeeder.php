<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\PeriodePolling;
use App\Models\PaketWisata;
use App\Models\DesainKaos;
use App\Models\UkuranKaos;
use App\Models\PeriodePaketWisata;
use App\Models\PeriodeKaos;

class PollingSeeder extends Seeder
{
    public function run(): void
    {
        // ── 1. Users ───────────────────────────────────────────────────
        User::create([
            'nama'      => 'Admin Vonic',
            'nip'       => 'ADMIN001',
            'email'     => 'admin@vonic.test',
            'pekerjaan' => 'Administrator',
            'unit_kerja'=> 'DTEDI SV UGM',
            'no_telp'   => '081234567890',
            'foto'      => null,
            'kategori'  => null,
            'role'      => 'admin',
        ]);

        $dosen1 = User::create([
            'nama'      => 'Maria Danuar, S.Kom',
            'nip'       => 'DOSEN001',
            'email'     => 'dosen1@vonic.test',
            'pekerjaan' => 'Dosen',
            'unit_kerja'=> 'Teknologi Rekayasa Perangkat Lunak',
            'no_telp'   => '0897777000',
            'foto'      => null,
            'kategori'  => 'dosen',
            'role'      => 'user',
        ]);

        User::create([
            'nama'      => 'Budi Santoso, M.T.',
            'nip'       => 'DOSEN002',
            'email'     => 'dosen2@vonic.test',
            'pekerjaan' => 'Dosen',
            'unit_kerja'=> 'Teknologi Rekayasa Elektro',
            'no_telp'   => '081298765432',
            'foto'      => null,
            'kategori'  => 'dosen',
            'role'      => 'user',
        ]);

        User::create([
            'nama'      => 'Siti Rahayu',
            'nip'       => 'TENDIK001',
            'email'     => 'tendik1@vonic.test',
            'pekerjaan' => 'Tenaga Kependidikan',
            'unit_kerja'=> 'DTEDI SV UGM',
            'no_telp'   => '081234567893',
            'foto'      => null,
            'kategori'  => 'tendik',
            'role'      => 'user',
        ]);

        // ── 2. Paket Wisata ────────────────────────────────────────────
        $paketList = [
            [
                'nama_destinasi'    => 'Pantai Kartini',
                'lokasi'            => 'Jepara, Jawa Tengah',
                'jarak'             => '95',
                'durasi_perjalanan' => '2.5',
                'benefit'           => 'Tiket masuk, makan siang, akomodasi, guide lokal, asuransi perjalanan',
                'harga'             => 350000,
                'foto'              => null,
                'kategori'          => 'bahari',
                'provinsi'          => 'Jawa Tengah',
                'rating'            => 4.3,
                'total_kunjungan'   => 180,
            ],
            [
                'nama_destinasi'    => 'Pantai Menganti',
                'lokasi'            => 'Kebumen, Jawa Tengah',
                'jarak'             => '140',
                'durasi_perjalanan' => '3.5',
                'benefit'           => 'Tiket masuk, makan siang, snorkeling, guide lokal, asuransi perjalanan',
                'harga'             => 420000,
                'foto'              => null,
                'kategori'          => 'bahari',
                'provinsi'          => 'Jawa Tengah',
                'rating'            => 4.6,
                'total_kunjungan'   => 210,
            ],
            [
                'nama_destinasi'    => 'Dataran Tinggi Dieng',
                'lokasi'            => 'Banjarnegara, Jawa Tengah',
                'jarak'             => '120',
                'durasi_perjalanan' => '3',
                'benefit'           => 'Tiket masuk kawasan, makan siang, kunjungan candi, guide, asuransi',
                'harga'             => 390000,
                'foto'              => null,
                'kategori'          => 'alam',
                'provinsi'          => 'Jawa Tengah',
                'rating'            => 4.7,
                'total_kunjungan'   => 250,
            ],
            [
                'nama_destinasi'    => 'Pantai Indrayanti',
                'lokasi'            => 'Gunungkidul, D.I. Yogyakarta',
                'jarak'             => '65',
                'durasi_perjalanan' => '1.5',
                'benefit'           => 'Tiket masuk, makan siang, banana boat, guide lokal, asuransi',
                'harga'             => 275000,
                'foto'              => null,
                'kategori'          => 'bahari',
                'provinsi'          => 'D.I. Yogyakarta',
                'rating'            => 4.4,
                'total_kunjungan'   => 320,
            ],
            [
                'nama_destinasi'    => 'Candi Borobudur',
                'lokasi'            => 'Magelang, Jawa Tengah',
                'jarak'             => '42',
                'durasi_perjalanan' => '1',
                'benefit'           => 'Tiket masuk, pemandu wisata, makan siang, asuransi perjalanan',
                'harga'             => 310000,
                'foto'              => null,
                'kategori'          => 'budaya',
                'provinsi'          => 'Jawa Tengah',
                'rating'            => 4.8,
                'total_kunjungan'   => 410,
            ],
        ];

        $pakets = [];
        foreach ($paketList as $p) {
            $pakets[] = PaketWisata::create($p);
        }

        // ── 3. Desain Kaos ─────────────────────────────────────────────
        $kaosList = [
            ['nama_desain' => 'Kaos Biru Navy', 'foto' => null],
            ['nama_desain' => 'Kaos Putih Polos', 'foto' => null],
            ['nama_desain' => 'Kaos Hitam Elegan', 'foto' => null],
        ];
        $kaosSaved = [];
        foreach ($kaosList as $k) {
            $kaosSaved[] = DesainKaos::create($k);
        }

        // ── 4. Ukuran Kaos ─────────────────────────────────────────────
        $ukuranList = ['S', 'M', 'L', 'XL', 'XXL', '3XL', '4XL', '5XL'];
        foreach ($ukuranList as $u) {
            UkuranKaos::create(['kode_ukuran' => $u]);
        }

        // ── 5. Periode Polling Aktif ───────────────────────────────────
        $admin = User::where('role', 'admin')->first();
        $periode = PeriodePolling::create([
            'nama_periode'   => 'Piknik DTEDI Periode 2026',
            'tanggal_mulai'  => now()->toDateString(),
            'tanggal_selesai'=> now()->addDays(7)->toDateString(),
            'status'         => 'aktif',
            'mode_kelompok'  => 'dipisah',
            'created_by'     => $admin->id_user,
        ]);

        // ── 6. Periode Polling Selesai (riwayat) ──────────────────────
        $periodeSelesai1 = PeriodePolling::create([
            'nama_periode'   => 'Piknik DTEDI Periode 2025',
            'tanggal_mulai'  => '2025-10-01',
            'tanggal_selesai'=> '2025-10-10',
            'status'         => 'selesai',
            'mode_kelompok'  => 'digabung',
            'created_by'     => $admin->id_user,
        ]);

        $periodeSelesai2 = PeriodePolling::create([
            'nama_periode'   => 'Piknik DTEDI Periode 2024',
            'tanggal_mulai'  => '2024-09-15',
            'tanggal_selesai'=> '2024-09-22',
            'status'         => 'selesai',
            'mode_kelompok'  => 'digabung',
            'created_by'     => $admin->id_user,
        ]);

        // ── 7. Pivot: Periode ↔ Paket Wisata ──────────────────────────
        foreach ($pakets as $paket) {
            \DB::table('periode_paket_wisata')->insert([
                'id_periode' => $periode->id_periode,
                'id_paket'   => $paket->id_paket,
            ]);
        }
        // Paket riwayat
        \DB::table('periode_paket_wisata')->insert([
            'id_periode' => $periodeSelesai1->id_periode,
            'id_paket'   => $pakets[2]->id_paket, // Dieng
        ]);
        \DB::table('periode_paket_wisata')->insert([
            'id_periode' => $periodeSelesai2->id_periode,
            'id_paket'   => $pakets[4]->id_paket, // Borobudur
        ]);

        // ── 8. Pivot: Periode ↔ Desain Kaos ───────────────────────────
        foreach ($kaosSaved as $kaos) {
            \DB::table('periode_kaos')->insert([
                'id_periode' => $periode->id_periode,
                'id_kaos'    => $kaos->id_kaos,
            ]);
        }
        \DB::table('periode_kaos')->insert([
            'id_periode' => $periodeSelesai1->id_periode,
            'id_kaos'    => $kaosSaved[0]->id_kaos,
        ]);
        \DB::table('periode_kaos')->insert([
            'id_periode' => $periodeSelesai2->id_periode,
            'id_kaos'    => $kaosSaved[1]->id_kaos,
        ]);
    }
}
