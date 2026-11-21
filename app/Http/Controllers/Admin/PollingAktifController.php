<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Partisipasi;
use App\Models\PeriodePolling;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

class PollingAktifController extends Controller
{
    public function index(Request $request)
    {
        // Cari polling yang sedang berstatus 'aktif' dari database nyata
        $periodeAktif = PeriodePolling::with(['paketWisata', 'desainKaos', 'partisipasi.user', 'partisipasi.paketWisata', 'partisipasi.desainKaos', 'partisipasi.ukuranKaos'])
            ->where('status', 'aktif')
            ->first();

        // Jika tidak ada polling aktif di database
        if (! $periodeAktif) {
            return view('admin.polling.aktif', [
                'title'      => 'Belum Ada Polling Aktif',
                'subtitle'   => 'Saat ini belum ada sesi pemungutan suara yang sedang berlangsung.',
                'countdown'  => ['days' => 0, 'hours' => 0, 'minutes' => 0],
                'deadline'   => ['-', '-'],
                'totalVote'  => ['percent' => 0, 'received' => 0, 'pending' => 0],
                'categories' => [],
                'kategori'   => 'semua',
                'activities' => new LengthAwarePaginator(collect(), 0, 10, 1, ['path' => $request->url()]),
                'rekapKaos'  => [],
                'totalKaos'  => 0,
            ]);
        }

        // Hitung Countdown
        $endDate = \Carbon\Carbon::parse($periodeAktif->tanggal_selesai . ' 23:59:59');
        $diff = now()->diff($endDate);
        $countdown = [
            'days'    => max(0, $diff->days),
            'hours'   => max(0, $diff->h),
            'minutes' => max(0, $diff->i),
        ];

        $deadline = [
            $endDate->translatedFormat('l'),
            $endDate->translatedFormat('d M Y, H.i') . ' WIB',
        ];

        // Partisipasi
        $partisipasi = $periodeAktif->partisipasi;
        $totalReceived = $partisipasi->count();
        $totalSivitas = 340; // Total estimasi pemilih DTEDI
        $percent = $totalSivitas > 0 ? round(($totalReceived / $totalSivitas) * 100) : 0;
        $totalVote = [
            'percent'  => $percent,
            'received' => $totalReceived,
            'pending'  => max(0, $totalSivitas - $totalReceived),
        ];

        // Hitung suara per kategori
        $wisataCounts = $partisipasi->groupBy('id_paket');
        $wisataOptions = $periodeAktif->paketWisata->map(function ($p) use ($wisataCounts) {
            $votes = $wisataCounts->get($p->id_paket, collect())->count();
            return [
                'name'   => $p->nama_destinasi,
                'dosen'  => $votes,
                'tendik' => 0,
                'total'  => $votes,
            ];
        });

        $kaosCounts = $partisipasi->groupBy('id_kaos');
        $kaosOptions = $periodeAktif->desainKaos->map(function ($k) use ($kaosCounts) {
            $votes = $kaosCounts->get($k->id_kaos, collect())->count();
            return [
                'name'   => $k->nama_desain,
                'dosen'  => $votes,
                'tendik' => 0,
                'total'  => $votes,
            ];
        });

        $pesertaOptions = [
            ['name' => 'Dosen dan tendik dipisah', 'dosen' => $partisipasi->where('pilihan_kelompok', 'dipisah')->count(), 'tendik' => 0, 'total' => $partisipasi->where('pilihan_kelompok', 'dipisah')->count()],
            ['name' => 'Dosen dan tendik digabung', 'dosen' => $partisipasi->where('pilihan_kelompok', 'digabung')->count(), 'tendik' => 0, 'total' => $partisipasi->where('pilihan_kelompok', 'digabung')->count()],
        ];

        $categories = [
            'peserta' => ['label' => 'Peserta', 'options' => $pesertaOptions],
            'wisata'  => ['label' => 'Tempat Wisata', 'options' => $wisataOptions->toArray()],
            'kaos'    => ['label' => 'Desain Kaos', 'options' => $kaosOptions->toArray()],
        ];

        foreach ($categories as $key => &$cat) {
            $catTotal = collect($cat['options'])->sum('total');
            $cat['options'] = collect($cat['options'])->map(function ($o) use ($catTotal) {
                $total = $o['total'];
                return $o + [
                    'dosen_pct'  => 100,
                    'tendik_pct' => 0,
                    'proporsi'   => $catTotal > 0 ? round(($total / $catTotal) * 100) : 0,
                ];
            })->sortByDesc('total')->values();
        }
        unset($cat);

        // Filter kategori view
        $kategori = $request->query('kategori', 'semua');
        $kategori = array_key_exists($kategori, $categories) ? $kategori : 'semua';

        // Activities (Aktivitas suara masuk)
        $activitiesList = $partisipasi->map(function ($pt) {
            $u = $pt->user;
            return [
                'name'       => $u->nama ?? 'Sivitas DTEDI',
                'email'      => $u->email ?? '-',
                'unit_kerja' => 'DTEDI SV UGM',
                'no_telp'    => '-',
                'peserta'    => $pt->pilihan_kelompok === 'dipisah' ? 'Dosen & Tendik dipisah' : 'Dosen & Tendik digabung',
                'wisata'     => $pt->paketWisata->nama_destinasi ?? '-',
                'kaos'       => $pt->desainKaos->nama_desain ?? '-',
                'status'     => 'Tersimpan',
                'kaos_items' => [
                    ['penerima' => 'Diri Sendiri', 'size' => $pt->ukuranKaos->kode_ukuran ?? 'L', 'jumlah' => 1]
                ]
            ];
        });

        $perPage = (int) $request->query('per_page', 10);
        $page = max(1, (int) $request->query('page', 1));
        $activities = new LengthAwarePaginator(
            $activitiesList->forPage($page, $perPage)->values(),
            $activitiesList->count(),
            $perPage,
            $page,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        // Rekap ukuran kaos
        $rekapKaos = [
            'XS'  => 0, 'S'   => 0, 'M'   => 0, 'L'   => 0, 'XL'  => 0, 'XXL' => 0, '3XL' => 0,
        ];
        foreach ($partisipasi as $pt) {
            $kode = $pt->ukuranKaos->kode_ukuran ?? null;
            if ($kode && isset($rekapKaos[$kode])) {
                $rekapKaos[$kode]++;
            }
        }
        $totalKaos = array_sum($rekapKaos);

        return view('admin.polling.aktif', [
            'title'      => $periodeAktif->nama_periode,
            'subtitle'   => 'Pemungutan suara yang sedang berjalan untuk civitas DTEDI Sekolah Vokasi UGM.',
            'countdown'  => $countdown,
            'deadline'   => $deadline,
            'totalVote'  => $totalVote,
            'categories' => $categories,
            'kategori'   => $kategori,
            'activities' => $activities,
            'rekapKaos'  => $rekapKaos,
            'totalKaos'  => $totalKaos,
            'isStandalone' => false,
        ]);
    }

    public function detail(Request $request, $id = null)
    {
        // Cari polling spesifik berdasarkan ID jika diberikan, atau ambil polling aktif / polling pertama
        $query = PeriodePolling::with(['paketWisata', 'desainKaos', 'partisipasi.user', 'partisipasi.paketWisata', 'partisipasi.desainKaos', 'partisipasi.ukuranKaos']);
        
        if ($id) {
            $periode = $query->find($id);
        } else {
            $periode = $query->where('status', 'aktif')->first() ?? $query->first();
        }

        // Jika tidak ada data di DB, gunakan fallback tampilan dummy
        if (! $periode) {
            return view('admin.polling.detail', [
                'title'      => 'Detail Polling',
                'subtitle'   => 'Rincian dan data statistik pemungutan suara.',
                'countdown'  => ['days' => 0, 'hours' => 0, 'minutes' => 0],
                'deadline'   => ['-', '-'],
                'totalVote'  => ['percent' => 0, 'received' => 0, 'pending' => 0],
                'categories' => [],
                'kategori'   => 'semua',
                'activities' => new LengthAwarePaginator(collect(), 0, 10, 1, ['path' => $request->url()]),
                'rekapKaos'  => [],
                'totalKaos'  => 0,
                'status'     => 'selesai',
            ]);
        }

        // Hitung Countdown jika aktif
        $endDate = \Carbon\Carbon::parse($periode->tanggal_selesai . ' 23:59:59');
        $diff = now()->diff($endDate);
        $countdown = [
            'days'    => max(0, $diff->days),
            'hours'   => max(0, $diff->h),
            'minutes' => max(0, $diff->i),
        ];

        $deadline = [
            $endDate->translatedFormat('l'),
            $endDate->translatedFormat('d M Y, H.i') . ' WIB',
        ];

        // Partisipasi
        $partisipasi = $periode->partisipasi;
        $totalReceived = $partisipasi->count();
        $totalSivitas = 340;
        $percent = $totalSivitas > 0 ? round(($totalReceived / $totalSivitas) * 100) : 0;
        $totalVote = [
            'percent'  => $percent,
            'received' => $totalReceived,
            'pending'  => max(0, $totalSivitas - $totalReceived),
        ];

        // Hitung suara per kategori
        $wisataCounts = $partisipasi->groupBy('id_paket');
        $wisataOptions = $periode->paketWisata->map(function ($p) use ($wisataCounts) {
            $votes = $wisataCounts->get($p->id_paket, collect())->count();
            return [
                'name'   => $p->nama_destinasi,
                'dosen'  => $votes,
                'tendik' => 0,
                'total'  => $votes,
            ];
        });

        $kaosCounts = $partisipasi->groupBy('id_kaos');
        $kaosOptions = $periode->desainKaos->map(function ($k) use ($kaosCounts) {
            $votes = $kaosCounts->get($k->id_kaos, collect())->count();
            return [
                'name'   => $k->nama_desain,
                'dosen'  => $votes,
                'tendik' => 0,
                'total'  => $votes,
            ];
        });

        $pesertaOptions = [
            ['name' => 'Dosen dan tendik dipisah', 'dosen' => $partisipasi->where('pilihan_kelompok', 'dipisah')->count(), 'tendik' => 0, 'total' => $partisipasi->where('pilihan_kelompok', 'dipisah')->count()],
            ['name' => 'Dosen dan tendik digabung', 'dosen' => $partisipasi->where('pilihan_kelompok', 'digabung')->count(), 'tendik' => 0, 'total' => $partisipasi->where('pilihan_kelompok', 'digabung')->count()],
        ];

        $categories = [
            'peserta' => ['label' => 'Peserta', 'options' => $pesertaOptions],
            'wisata'  => ['label' => 'Tempat Wisata', 'options' => $wisataOptions->toArray()],
            'kaos'    => ['label' => 'Desain Kaos', 'options' => $kaosOptions->toArray()],
        ];

        foreach ($categories as $key => &$cat) {
            $catTotal = collect($cat['options'])->sum('total');
            $cat['options'] = collect($cat['options'])->map(function ($o) use ($catTotal) {
                $total = $o['total'];
                return $o + [
                    'dosen_pct'  => 100,
                    'tendik_pct' => 0,
                    'proporsi'   => $catTotal > 0 ? round(($total / $catTotal) * 100) : 0,
                ];
            })->sortByDesc('total')->values();
        }
        unset($cat);

        $kategori = $request->query('kategori', 'semua');
        $kategori = array_key_exists($kategori, $categories) ? $kategori : 'semua';

        // Activities
        $activitiesList = $partisipasi->map(function ($pt) {
            $u = $pt->user;
            return [
                'name'       => $u->nama ?? 'Sivitas DTEDI',
                'email'      => $u->email ?? '-',
                'unit_kerja' => 'DTEDI SV UGM',
                'no_telp'    => '-',
                'peserta'    => $pt->pilihan_kelompok === 'dipisah' ? 'Dosen & Tendik dipisah' : 'Dosen & Tendik digabung',
                'wisata'     => $pt->paketWisata->nama_destinasi ?? '-',
                'kaos'       => $pt->desainKaos->nama_desain ?? '-',
                'status'     => 'Tersimpan',
                'kaos_items' => [
                    ['penerima' => 'Diri Sendiri', 'size' => $pt->ukuranKaos->kode_ukuran ?? 'L', 'jumlah' => 1]
                ]
            ];
        });

        $perPage = (int) $request->query('per_page', 10);
        $page = max(1, (int) $request->query('page', 1));
        $activities = new LengthAwarePaginator(
            $activitiesList->forPage($page, $perPage)->values(),
            $activitiesList->count(),
            $perPage,
            $page,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        $rekapKaos = [
            'XS'  => 0, 'S'   => 0, 'M'   => 0, 'L'   => 0, 'XL'  => 0, 'XXL' => 0, '3XL' => 0,
        ];
        foreach ($partisipasi as $pt) {
            $kode = $pt->ukuranKaos->kode_ukuran ?? null;
            if ($kode && isset($rekapKaos[$kode])) {
                $rekapKaos[$kode]++;
            }
        }
        $totalKaos = array_sum($rekapKaos);

        return view('admin.polling.detail', [
            'title'      => $periode->nama_periode,
            'subtitle'   => 'Rincian dan analitik pemungutan suara sivitas DTEDI Sekolah Vokasi UGM.',
            'countdown'  => $countdown,
            'deadline'   => $deadline,
            'totalVote'  => $totalVote,
            'categories' => $categories,
            'kategori'   => $kategori,
            'activities' => $activities,
            'rekapKaos'  => $rekapKaos,
            'totalKaos'  => $totalKaos,
            'status'     => $periode->status,
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
            fputcsv($handle, ['No', 'Nama', 'Unit Kerja', 'No Telp', 'Ukuran Kaos']);

            $periodeAktif = PeriodePolling::with(['partisipasi.user', 'partisipasi.ukuranKaos'])
                ->where('status', 'aktif')
                ->first();

            $no = 1;
            if ($periodeAktif) {
                foreach ($periodeAktif->partisipasi as $pt) {
                    fputcsv($handle, [
                        $no++,
                        $pt->user->nama ?? 'Sivitas',
                        'DTEDI SV UGM',
                        '-',
                        $pt->ukuranKaos->kode_ukuran ?? 'L',
                    ]);
                }
            }

            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }
}
