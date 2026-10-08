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
            'id_ukuran' => 'required|exists:ukuran_kaos,id_ukuran',
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
            'id_ukuran' => $request->id_ukuran,
            'waktu_submit' => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Voting berhasil disimpan.',
            'data' => $partisipasi
        ], 201);
    }

    public function riwayat(Request $request)
    {
        $riwayat = \App\Models\Partisipasi::with([
            'periodePolling',
            'paketWisata',
            'desainKaos',
            'ukuranKaos'
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
}