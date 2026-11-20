<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PeriodePolling;
use Illuminate\Http\Request;


class PollingController extends Controller
{
    public function index()
    {
        $periode = PeriodePolling::with(['paketWisata', 'desainKaos'])
            ->orderBy('tanggal_mulai', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'message' => 'Data periode polling berhasil diambil.',
            'data' => $periode
        ]);
    }

    public function show($id)
    {
        $periode = PeriodePolling::with([
            'paketWisata',
            'desainKaos'
        ])->find($id);

        if (!$periode) {
            return response()->json([
                'success' => false,
                'message' => 'Periode polling tidak ditemukan.',
                'data' => null
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Detail periode polling berhasil diambil.',
            'data' => $periode
        ]);
    }
    public function pilihan($id)
    {
        $periode = PeriodePolling::with([
            'paketWisata',
            'desainKaos'
        ])->find($id);

        if (!$periode) {
            return response()->json([
                'success' => false,
                'message' => 'Periode polling tidak ditemukan.',
                'data' => null
            ], 404);
        }

        $ukuranKaos = \App\Models\UkuranKaos::orderBy('id_ukuran')->get();

        return response()->json([
            'success' => true,
            'message' => 'Pilihan polling berhasil diambil.',
            'data' => [
                'periode' => $periode,
                'ukuran_kaos' => $ukuranKaos
            ]
        ]);
    }

    public function vote(Request $request, $id)
    {
        $request->validate([
        'id_paket' => 'required|exists:paket_wisata,id_paket',
        'id_kaos' => 'required|exists:desain_kaos,id_kaos',
        'ukuran' => 'required|array|min:1',
        'ukuran.*.id_ukuran' => 'required|exists:ukuran_kaos,id_ukuran',
        'ukuran.*.jumlah' => 'required|integer|min:1',
    ]);

        $periode = PeriodePolling::find($id);

        if (!$periode) {
            return response()->json([
                'success' => false,
                'message' => 'Periode polling tidak ditemukan.',
                'data' => null
            ], 404);
        }

        if ($periode->status !== 'aktif') {
            return response()->json([
                'success' => false,
                'message' => 'Polling sedang tidak aktif.',
                'data' => null
            ], 422);
        }

        $hariIni = now()->toDateString();
        if ($hariIni < $periode->tanggal_mulai) {
            return response()->json([
                'success' => false,
                'message' => 'Polling belum dimulai.',
                'data' => null
            ], 422);
        }

        if ($hariIni > $periode->tanggal_selesai) {
            return response()->json([
                'success' => false,
                'message' => 'Polling sudah berakhir.',
                'data' => null
            ], 422);
        }

        $sudahVote = \App\Models\Partisipasi::where('id_user', $request->user()->id_user)
            ->where('id_periode', $id)
            ->exists();

        if ($sudahVote) {
            return response()->json([
                'success' => false,
                'message' => 'Anda sudah melakukan voting pada periode ini.',
                'data' => null
            ], 409);
        }

        $partisipasi = \App\Models\Partisipasi::create([
            'id_user' => $request->user()->id_user,
            'id_periode' => $id,
            'id_paket' => $request->id_paket,
            'id_kaos' => $request->id_kaos,
            'waktu_submit' => now(),
        ]);

        foreach ($request->ukuran as $item) {
            $partisipasi->detailUkuran()->create([
                'id_ukuran' => $item['id_ukuran'],
                'jumlah' => $item['jumlah'],
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Voting berhasil disimpan.',
            'data' => $partisipasi->load('detailUkuran.ukuranKaos')
        ], 201);
    }

    public function riwayat(Request $request)
    {
        $riwayat = \App\Models\Partisipasi::with([
        'periodePolling',
        'paketWisata',
        'desainKaos',
        'detailUkuran.ukuranKaos'
        ])
        ->where('id_user', $request->user()->id_user)
        ->orderBy('waktu_submit', 'desc')
        ->get();

        return response()->json([
            'success' => true,
            'message' => 'Riwayat voting berhasil diambil.',
            'data' => $riwayat
        ]);
    }

    public function hasil($id)
    {
        $periode = PeriodePolling::find($id);

        if (!$periode) {
            return response()->json([
                'success' => false,
                'message' => 'Periode polling tidak ditemukan.',
                'data' => null
            ], 404);
        }

        // Fungsi untuk mengambil hasil berdasarkan kategori peserta
        $ambilHasil = function ($kategori = null) use ($id) {

            // Hasil paket wisata
            $hasilPaket = \App\Models\Partisipasi::with('paketWisata')
                ->where('id_periode', $id)
                ->when($kategori, function ($query) use ($kategori) {
                    $query->whereHas('user', function ($userQuery) use ($kategori) {
                        $userQuery->where('kategori', $kategori);
                    });
                })
                ->selectRaw('id_paket, COUNT(*) as jumlah_suara')
                ->groupBy('id_paket')
                ->get();

            // Hasil desain kaos
            $hasilKaos = \App\Models\Partisipasi::with('desainKaos')
                ->where('id_periode', $id)
                ->when($kategori, function ($query) use ($kategori) {
                    $query->whereHas('user', function ($userQuery) use ($kategori) {
                        $userQuery->where('kategori', $kategori);
                    });
                })
                ->selectRaw('id_kaos, COUNT(*) as jumlah_suara')
                ->groupBy('id_kaos')
                ->get();

            // Hasil jumlah kaos per ukuran
            $hasilUkuran = \App\Models\DetailUkuranVote::with('ukuranKaos')
                ->whereHas('partisipasi', function ($query) use ($id, $kategori) {
                    $query->where('id_periode', $id)
                        ->when($kategori, function ($subQuery) use ($kategori) {
                            $subQuery->whereHas('user', function ($userQuery) use ($kategori) {
                                $userQuery->where('kategori', $kategori);
                            });
                        });
                })
                ->selectRaw('id_ukuran, SUM(jumlah) as total_kaos')
                ->groupBy('id_ukuran')
                ->get();

            return [
                'hasil_paket_wisata' => $hasilPaket,
                'hasil_desain_kaos' => $hasilKaos,
                'hasil_ukuran_kaos' => $hasilUkuran,
            ];
        };

        // Jika mode kelompok digabung
        if ($periode->mode_kelompok === 'digabung') {
            $hasil = $ambilHasil();

            return response()->json([
                'success' => true,
                'message' => 'Hasil voting berhasil diambil.',
                'data' => [
                    'periode' => $periode,
                    ...$hasil
                ]
            ]);
        }

        // Jika mode kelompok dipisah
        $hasilDosen = $ambilHasil('dosen');
        $hasilTendik = $ambilHasil('tendik');

        return response()->json([
            'success' => true,
            'message' => 'Hasil voting berhasil diambil.',
            'data' => [
                'periode' => $periode,
                'hasil_per_kategori' => [
                    'dosen' => $hasilDosen,
                    'tendik' => $hasilTendik,
                ]
            ]
        ]);
    }
}