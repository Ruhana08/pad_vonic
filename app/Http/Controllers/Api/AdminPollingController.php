<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PeriodePolling;
use App\Models\Partisipasi;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class AdminPollingController extends Controller
{
    private function cekAdmin(Request $request)
    {
            if (!$request->user() || $request->user()->role !== 'admin') {
            abort(403, 'Akses hanya untuk admin.');
        }
    }

    // 1. Menampilkan semua periode polling
    public function index(Request $request)
    {
        $this->cekAdmin($request);

        $periode = PeriodePolling::with(['paketWisata', 'desainKaos'])
            ->orderBy('tanggal_mulai', 'desc')
            ->paginate(10);

        return response()->json([
            'success' => true,
            'message' => 'Data periode polling berhasil diambil.',
            'data' => $periode
        ]);
    }

    // 2. Membuat periode polling
    public function store(Request $request)
    {
        $this->cekAdmin($request);

        $validated = $request->validate([
            'nama_periode' => 'required|string|max:255',
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'required|date|after_or_equal:tanggal_mulai',
            'status' => 'required|in:draft,aktif,selesai',
            'mode_kelompok' => 'required|in:dipisah,digabung',
            'paket_wisata' => 'required|array|min:1',
            'paket_wisata.*' => 'required|exists:paket_wisata,id_paket',
            'desain_kaos' => 'required|array|min:1',
            'desain_kaos.*' => 'required|exists:desain_kaos,id_kaos',
        ]);

        $periode = PeriodePolling::create([
            'nama_periode' => $validated['nama_periode'],
            'tanggal_mulai' => $validated['tanggal_mulai'],
            'tanggal_selesai' => $validated['tanggal_selesai'],
            'status' => $validated['status'],
            'mode_kelompok' => $validated['mode_kelompok'],
            'created_by' => $request->user()->id_user,
        ]);

        $periode->paketWisata()->sync($validated['paket_wisata']);
        $periode->desainKaos()->sync($validated['desain_kaos']);

        return response()->json([
            'success' => true,
            'message' => 'Periode polling berhasil dibuat.',
            'data' => $periode->load(['paketWisata', 'desainKaos'])
        ], 201);
    }

    // 3. Melihat detail periode polling
    public function show(Request $request, $id)
    {
        $this->cekAdmin($request);

        $periode = PeriodePolling::with(['paketWisata', 'desainKaos'])
            ->findOrFail($id);

        return response()->json([
            'success' => true,
            'message' => 'Detail periode polling berhasil diambil.',
            'data' => $periode
        ]);
    }

    // 4. Mengubah periode polling
    public function update(Request $request, $id)
    {
        $this->cekAdmin($request);

        $periode = PeriodePolling::findOrFail($id);

        if (Partisipasi::where('id_periode', $id)->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'Periode sudah memiliki voting. Data periode tidak dapat diubah.'
            ], 409);
        }

        $validated = $request->validate([
            'nama_periode' => 'sometimes|required|string|max:255',
            'tanggal_mulai' => 'sometimes|required|date',
            'tanggal_selesai' => 'sometimes|required|date',
            'status' => 'sometimes|required|in:draft,aktif,selesai',
            'mode_kelompok' => 'sometimes|required|in:dipisah,digabung',
            'paket_wisata' => 'sometimes|required|array|min:1',
            'paket_wisata.*' => 'required|exists:paket_wisata,id_paket',
            'desain_kaos' => 'sometimes|required|array|min:1',
            'desain_kaos.*' => 'required|exists:desain_kaos,id_kaos',
        ]);

        $tanggalMulai = $validated['tanggal_mulai'] ?? $periode->tanggal_mulai;
        $tanggalSelesai = $validated['tanggal_selesai'] ?? $periode->tanggal_selesai;

        if ($tanggalSelesai < $tanggalMulai) {
            return response()->json([
                'success' => false,
                'message' => 'Tanggal selesai tidak boleh sebelum tanggal mulai.'
            ], 422);
        }

        $periode->update(collect($validated)->except([
            'paket_wisata',
            'desain_kaos'
        ])->toArray());

        if (isset($validated['paket_wisata'])) {
            $periode->paketWisata()->sync($validated['paket_wisata']);
        }

        if (isset($validated['desain_kaos'])) {
            $periode->desainKaos()->sync($validated['desain_kaos']);
        }

        return response()->json([
            'success' => true,
            'message' => 'Periode polling berhasil diperbarui.',
            'data' => $periode->load(['paketWisata', 'desainKaos'])
        ]);
    }

    // 5. Menghapus periode polling
    public function destroy(Request $request, $id)
    {
        $this->cekAdmin($request);

        $periode = PeriodePolling::findOrFail($id);

        if (Partisipasi::where('id_periode', $id)->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'Periode sudah memiliki voting sehingga tidak dapat dihapus.'
            ], 409);
        }

        $periode->paketWisata()->detach();
        $periode->desainKaos()->detach();
        $periode->delete();

        return response()->json([
            'success' => true,
            'message' => 'Periode polling berhasil dihapus.'
        ]);
    }

    // 6. Menampilkan hasil voting khusus admin
    public function hasil(Request $request, $id)
    {
        $this->cekAdmin($request);

        $periode = PeriodePolling::find($id);

        if (!$periode) {
            return response()->json([
                'success' => false,
                'message' => 'Periode polling tidak ditemukan.',
                'data' => null
            ], 404);
        }

        $hasilPaket = Partisipasi::with('paketWisata')
            ->where('id_periode', $id)
            ->selectRaw('id_paket, COUNT(*) as jumlah_suara')
            ->groupBy('id_paket')
            ->get();

        $hasilKaos = Partisipasi::with('desainKaos')
            ->where('id_periode', $id)
            ->selectRaw('id_kaos, COUNT(*) as jumlah_suara')
            ->groupBy('id_kaos')
            ->get();

        return response()->json([
            'success' => true,
            'message' => 'Hasil voting berhasil diambil.',
            'data' => [
                'periode' => $periode,
                'hasil_paket_wisata' => $hasilPaket,
                'hasil_desain_kaos' => $hasilKaos
            ]
        ]);
    }
}