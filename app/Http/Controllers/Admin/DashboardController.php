<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PeriodePolling;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $periodeAktif = PeriodePolling::with(['paketWisata', 'desainKaos', 'partisipasi.user'])
            ->where('status', 'aktif')
            ->first();

        if (! $periodeAktif) {
            return view('admin.dashboard', [
                'countdown'  => ['days' => 0, 'hours' => 0, 'minutes' => 0],
                'deadline'   => '-',
                'totalVote'  => ['percent' => 0, 'received' => 0, 'pending' => 0],
                'activities' => collect(),
                'categories' => [],
            ]);
        }

        $endDate = \Carbon\Carbon::parse($periodeAktif->tanggal_selesai . ' 23:59:59');
        $diff = now()->diff($endDate);
        $countdown = [
            'days'    => max(0, $diff->days),
            'hours'   => max(0, $diff->h),
            'minutes' => max(0, $diff->i),
        ];

        $deadline = $endDate->translatedFormat('l, d M Y, H.i') . ' WIB';

        $partisipasi = $periodeAktif->partisipasi->filter(fn($pt) => $pt->user !== null)->values();
        $totalReceived = $partisipasi->count();
        $totalSivitas = max($totalReceived, \App\Models\User::where('role', '!=', 'admin')->count());
        $percent = $totalSivitas > 0 ? round(($totalReceived / $totalSivitas) * 100) : 0;
        $totalVote = [
            'percent'  => $percent,
            'received' => $totalReceived,
            'pending'  => max(0, $totalSivitas - $totalReceived),
        ];

        $activities = $partisipasi->sortByDesc('waktu_submit')->take(5)->map(function ($pt) {
            $waktu = $pt->waktu_submit ? \Carbon\Carbon::parse($pt->waktu_submit)->diffForHumans() : 'Baru saja';
            return [
                'name' => $pt->user->nama ?? 'Sivitas DTEDI',
                'time' => $waktu,
                'text' => 'Mengirimkan suara',
            ];
        });

        // Kategori persentase
        $wisataTotal = $partisipasi->count();
        $wisataOptions = $periodeAktif->paketWisata->map(function ($p) use ($partisipasi, $wisataTotal) {
            $cnt = $partisipasi->where('id_paket', $p->id_paket)->count();
            return [
                'name'    => $p->nama_destinasi,
                'percent' => $wisataTotal > 0 ? round(($cnt / $wisataTotal) * 100) : 0,
            ];
        });

        $kaosOptions = $periodeAktif->desainKaos->map(function ($k) use ($partisipasi, $wisataTotal) {
            $cnt = $partisipasi->where('id_kaos', $k->id_kaos)->count();
            return [
                'name'    => $k->nama_desain,
                'percent' => $wisataTotal > 0 ? round(($cnt / $wisataTotal) * 100) : 0,
            ];
        });

        $pesertaDipisah = $partisipasi->where('pilihan_kelompok', 'dipisah')->count();
        $pesertaOptions = [
            [
                'name'    => 'Dosen dan Tenaga Didik dipisah',
                'percent' => $wisataTotal > 0 ? round(($pesertaDipisah / $wisataTotal) * 100) : 50,
            ],
            [
                'name'    => 'Dosen dan Tenaga Didik digabung',
                'percent' => $wisataTotal > 0 ? (100 - round(($pesertaDipisah / $wisataTotal) * 100)) : 50,
            ],
        ];

        $categories = [
            ['title' => 'Pembagian Peserta', 'options' => $pesertaOptions],
            ['title' => 'Paket Wisata', 'options' => $wisataOptions->toArray()],
            ['title' => 'Kaos', 'options' => $kaosOptions->toArray()],
        ];

        return view('admin.dashboard', [
            'countdown'  => $countdown,
            'deadline'   => $deadline,
            'totalVote'  => $totalVote,
            'activities' => $activities,
            'categories' => $categories,
        ]);
    }
}
