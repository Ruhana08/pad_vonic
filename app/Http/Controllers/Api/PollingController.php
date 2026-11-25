<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Notifikasi;
use App\Models\PeriodePolling;
use Illuminate\Http\Request;

class PollingController extends Controller
{
    public function index()
    {
        $periode = PeriodePolling::with([
            'paketWisata.galeri',
            'desainKaos.galeri',
            'partisipasi',
        ])
            ->orderBy('tanggal_mulai', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'message' => 'Data periode polling berhasil diambil.',
            'data' => $periode
        ]);
    }

    public function show(Request $request, $id)
    {
        $periode = PeriodePolling::with([
            'paketWisata.galeri',
            'desainKaos.galeri',
            'partisipasi',
        ])->find($id);

        if (!$periode) {
            return response()->json([
                'success' => false,
                'message' => 'Periode polling tidak ditemukan.',
                'data' => null
            ], 404);
        }

        // Cek status voting user jika terautentikasi
        $user = auth('sanctum')->user() ?? $request->user();
        $sudahMemilih = false;
        if ($user) {
            $sudahMemilih = \App\Models\Partisipasi::where('id_user', $user->id_user)
                ->where('id_periode', $id)
                ->exists();
        }

        return response()->json([
            'success' => true,
            'message' => 'Detail periode polling berhasil diambil.',
            'data' => [
                'periode' => $periode,
                'sudah_memilih' => $sudahMemilih,
            ]
        ]);
    }

    public function pilihan(Request $request, $id)
    {
        $periode = PeriodePolling::with([
            'paketWisata.galeri',
            'desainKaos.galeri',
            'partisipasi',
        ])->find($id);

        if (!$periode) {
            return response()->json([
                'success' => false,
                'message' => 'Periode polling tidak ditemukan.',
                'data' => null
            ], 404);
        }

        $ukuranKaos = \App\Models\UkuranKaos::orderBy('id_ukuran')->get();

        // Cek status voting user jika terautentikasi
        $user = auth('sanctum')->user() ?? $request->user();
        $sudahMemilih = false;
        $partisipasiUser = null;

        if ($user) {
            $partisipasi = \App\Models\Partisipasi::with('detailUkuran.ukuranKaos')
                ->where('id_user', $user->id_user)
                ->where('id_periode', $id)
                ->first();

            if ($partisipasi) {
                $sudahMemilih = true;
                $partisipasiUser = $partisipasi;
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Pilihan polling berhasil diambil.',
            'data' => [
                'periode' => $periode,
                'ukuran_kaos' => $ukuranKaos,
                'sudah_memilih' => $sudahMemilih,
                'partisipasi_user' => $partisipasiUser,
            ]
        ]);
    }

    public function vote(Request $request, $id)
    {
        // Mendukung payload ganda (items_kaos / ukuran / fallback id_ukuran tunggal)
        if ($request->has('items_kaos') && !$request->has('ukuran')) {
            $request->merge(['ukuran' => $request->items_kaos]);
        } elseif ($request->has('id_ukuran') && !$request->has('ukuran')) {
            $request->merge([
                'ukuran' => [
                    [
                        'id_ukuran' => $request->id_ukuran,
                        'jumlah' => (int) $request->input('jumlah', 1),
                    ]
                ]
            ]);
        }

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
            'id_user'          => $request->user()->id_user,
            'id_periode'       => $id,
            'id_paket'         => $request->id_paket,
            'id_kaos'          => $request->id_kaos,
            'id_ukuran'        => $request->ukuran[0]['id_ukuran'],
            'pilihan_kelompok' => $request->input('pilihan_kelompok', 'dipisah'),
            'waktu_submit'     => now(),
        ]);

        foreach ($request->ukuran as $item) {
            $partisipasi->detailUkuran()->create([
                'id_ukuran' => $item['id_ukuran'],
                'jumlah' => $item['jumlah'],
            ]);
        }

        // Notif ke user: voting berhasil disimpan
        Notifikasi::kirim(
            $request->user()->id_user,
            'Suara Anda Berhasil Disimpan',
            'Pilihan wisata dan kaos Anda telah tercatat. Terima kasih!',
            '/home'
        );

        // Notif ke semua admin: ada suara baru masuk
        $admins = \App\Models\User::where('role', 'admin')->get();
        foreach ($admins as $admin) {
            Notifikasi::kirim(
                $admin->id_user,
                'Suara Baru Diterima',
                $request->user()->nama . ' telah mengirimkan suara untuk periode polling ini.',
                '/admin/polling/aktif'
            );
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

        $ambilHasil = function ($kategori = null) use ($id) {

            $hasilPaket = \App\Models\Partisipasi::with('paketWisata.galeri')
                ->where('id_periode', $id)
                ->when($kategori, function ($query) use ($kategori) {
                    $query->whereHas('user', function ($userQuery) use ($kategori) {
                        $userQuery->where('kategori', $kategori);
                    });
                })
                ->selectRaw('id_paket, COUNT(*) as jumlah_suara')
                ->groupBy('id_paket')
                ->get();

            $hasilKaos = \App\Models\Partisipasi::with('desainKaos.galeri')
                ->where('id_periode', $id)
                ->when($kategori, function ($query) use ($kategori) {
                    $query->whereHas('user', function ($userQuery) use ($kategori) {
                        $userQuery->where('kategori', $kategori);
                    });
                })
                ->selectRaw('id_kaos, COUNT(*) as jumlah_suara')
                ->groupBy('id_kaos')
                ->get();

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

        $mode = strtolower(trim($periode->mode_kelompok ?? 'digabung'));
        $isDigabung = in_array($mode, ['digabung', 'gabung', 'semua sivitas bersama', 'semua sivitas', 'bersama', 'semua'])
            || str_contains($mode, 'gabung')
            || str_contains($mode, 'bersama');

        if ($isDigabung) {
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