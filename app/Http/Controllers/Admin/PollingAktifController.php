<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

class PollingAktifController extends Controller
{
    private function getSampleData()
    {
        return [
            [
                'name'       => 'Melisa Danuar, S.T',
                'email'      => 'melisadanuar@mail.ugm.ac.id',
                'unit_kerja' => 'Departemen Teknik Elektro dan Informatika',
                'no_telp'    => '081234567890',
                'peserta'    => 'Dosen & Tendik dipisah',
                'wisata'     => 'Pantai Kartini',
                'kaos'       => 'Navy Polo Gathering',
                'status'     => 'Tersimpan',
                'kaos_items' => [
                    ['penerima' => 'Diri Sendiri', 'size' => 'L', 'jumlah' => 1],
                    ['penerima' => 'Pasangan', 'size' => 'M', 'jumlah' => 1],
                    ['penerima' => 'Anak 1', 'size' => 'S', 'jumlah' => 1],
                ]
            ],
            [
                'name'       => 'Dr. Budi Santoso, M.Kom',
                'email'      => 'budisantoso@ugm.ac.id',
                'unit_kerja' => 'Laboratorium Rekayasa Perangkat Lunak',
                'no_telp'    => '081398765432',
                'peserta'    => 'Dosen & Tendik dipisah',
                'wisata'     => 'Karimunjawa',
                'kaos'       => 'Navy Polo Gathering',
                'status'     => 'Tersimpan',
                'kaos_items' => [
                    ['penerima' => 'Diri Sendiri', 'size' => 'XL', 'jumlah' => 1],
                    ['penerima' => 'Istri', 'size' => 'L', 'jumlah' => 1],
                    ['penerima' => 'Anak 1', 'size' => 'M', 'jumlah' => 1],
                    ['penerima' => 'Anak 2', 'size' => 'S', 'jumlah' => 1],
                ]
            ],
            [
                'name'       => 'Siti Rahmawati, S.Kom., M.Cs',
                'email'      => 'siti.rahma@ugm.ac.id',
                'unit_kerja' => 'Program Studi Sarjana Terapan TRIK',
                'no_telp'    => '082155667788',
                'peserta'    => 'Dosen & Tendik digabung',
                'wisata'     => 'Pantai Kartini',
                'kaos'       => 'White Cotton Casual',
                'status'     => 'Tersimpan',
                'kaos_items' => [
                    ['penerima' => 'Diri Sendiri', 'size' => 'M', 'jumlah' => 1],
                ]
            ],
            [
                'name'       => 'Ahmad Fauzan, S.T., M.Eng',
                'email'      => 'ahmadfauzan@ugm.ac.id',
                'unit_kerja' => 'Laboratorium Jaringan Komputer & IoT',
                'no_telp'    => '085712344321',
                'peserta'    => 'Dosen & Tendik dipisah',
                'wisata'     => 'Candi Borobudur',
                'kaos'       => 'Navy Polo Gathering',
                'status'     => 'Tersimpan',
                'kaos_items' => [
                    ['penerima' => 'Diri Sendiri', 'size' => 'XXL', 'jumlah' => 1],
                    ['penerima' => 'Istri', 'size' => 'XL', 'jumlah' => 1],
                ]
            ],
            [
                'name'       => 'Dewi Lestari, A.Md',
                'email'      => 'dewi.lestari@ugm.ac.id',
                'unit_kerja' => 'Tata Usaha DTEDI SV UGM',
                'no_telp'    => '087899887766',
                'peserta'    => 'Dosen & Tendik dipisah',
                'wisata'     => 'Pantai Kartini',
                'kaos'       => 'White Cotton Casual',
                'status'     => 'Tersimpan',
                'kaos_items' => [
                    ['penerima' => 'Diri Sendiri', 'size' => 'S', 'jumlah' => 1],
                    ['penerima' => 'Suami', 'size' => 'L', 'jumlah' => 1],
                    ['penerima' => 'Anak', 'size' => 'XS', 'jumlah' => 1],
                ]
            ],
        ];
    }

    public function index(Request $request)
    {
        $categories = [
            'peserta' => ['label' => 'Peserta', 'options' => [
                ['name' => 'Dosen dan tendik dipisah',   'dosen' => 154, 'tendik' => 66],
                ['name' => 'Dosen dan tendik digabung',  'dosen' => 90,  'tendik' => 40],
            ]],
            'wisata' => ['label' => 'Tempat Wisata', 'options' => [
                ['name' => 'Pantai Kartini',  'dosen' => 120, 'tendik' => 40],
                ['name' => 'Karimunjawa',     'dosen' => 80,  'tendik' => 30],
                ['name' => 'Candi Borobudur', 'dosen' => 60,  'tendik' => 20],
                ['name' => 'Kebun Teh',       'dosen' => 40,  'tendik' => 10],
            ]],
            'kaos' => ['label' => 'Desain Kaos', 'options' => [
                ['name' => 'Navy Polo Gathering', 'dosen' => 110, 'tendik' => 50],
                ['name' => 'White Cotton Casual', 'dosen' => 90,  'tendik' => 30],
            ]],
        ];

        foreach ($categories as $key => &$cat) {
            $catTotal = collect($cat['options'])->sum(fn ($o) => $o['dosen'] + $o['tendik']);
            $cat['options'] = collect($cat['options'])->map(function ($o) use ($catTotal) {
                $total = $o['dosen'] + $o['tendik'];
                return $o + [
                    'total'      => $total,
                    'dosen_pct'  => $total ? round($o['dosen'] / $total * 100) : 0,
                    'tendik_pct' => $total ? round($o['tendik'] / $total * 100) : 0,
                    'proporsi'   => $catTotal ? round($total / $catTotal * 100) : 0,
                ];
            })->sortByDesc('total')->values();
        }
        unset($cat);

        $kategori = $request->query('kategori', 'semua');
        $kategori = array_key_exists($kategori, $categories) ? $kategori : 'semua';

        $perPage = (int) $request->query('per_page', 10);
        $perPage = in_array($perPage, [10, 25, 50]) ? $perPage : 10;
        $page = max(1, (int) $request->query('page', 1));

        $sample = $this->getSampleData();
        $all = collect(range(1, 40))->map(function ($i) use ($sample) {
            $base = $sample[($i - 1) % count($sample)];
            $base['name'] = $base['name'] . ($i > count($sample) ? ' (' . $i . ')' : '');
            return $base;
        });

        $activities = new LengthAwarePaginator(
            $all->forPage($page, $perPage)->values(),
            $all->count(), $perPage, $page,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        // Rekap total ukuran kaos untuk keperluan produksi vendor
        $rekapKaos = [
            'XS'  => 12,
            'S'   => 48,
            'M'   => 85,
            'L'   => 120,
            'XL'  => 64,
            'XXL' => 22,
            '3XL' => 8,
        ];
        $totalKaos = array_sum($rekapKaos);

        return view('admin.polling.aktif', [
            'title'      => 'Piknik DTEDI Periode 2026',
            'subtitle'   => 'Pemungutan suara untuk menentukan lokasi dan tanggal pelaksanaan piknik tahunan civitas DTEDI.',
            'countdown'  => ['days' => 3, 'hours' => 8, 'minutes' => 45],
            'deadline'   => ['Jumat', '16 Okt 2026, 18.00 WIB'],
            'totalVote'  => ['percent' => 82, 'received' => 279, 'pending' => 61],
            'categories' => $categories,
            'kategori'   => $kategori,
            'activities' => $activities,
            'rekapKaos'  => $rekapKaos,
            'totalKaos'  => $totalKaos,
        ]);
    }

    public function announce(Request $request)
    {
        return redirect()->route('admin.polling.aktif')
            ->with('success', 'Hasil voting resmi berhasil dipublikasikan kepada seluruh sivitas DTEDI!');
    }

    public function exportKaos(Request $request)
    {
        $filename = 'data-ukuran-kaos-peserta-' . date('Ymd-His') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function () {
            $handle = fopen('php://output', 'w');
            fprintf($handle, chr(0xEF).chr(0xBB).chr(0xBF)); // UTF-8 BOM

            // Header kolom CSV sesuai permintaan user: Nama, Unit Kerja, No Telp, Ukuran Kaos
            fputcsv($handle, ['No', 'Nama', 'Unit Kerja', 'No Telp', 'Ukuran Kaos']);

            $data = $this->getSampleData();
            $no = 1;
            foreach ($data as $d) {
                foreach ($d['kaos_items'] as $item) {
                    $namaDisplay = $d['name'] . ($item['penerima'] !== 'Diri Sendiri' ? ' (' . $item['penerima'] . ')' : '');
                    fputcsv($handle, [
                        $no++,
                        $namaDisplay,
                        $d['unit_kerja'] ?? '-',
                        $d['no_telp'] ?? '-',
                        $item['size'],
                    ]);
                }
            }

            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }
}
