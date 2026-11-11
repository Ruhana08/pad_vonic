<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\PaketWisata;

class AdminPaketWisataController extends Controller
{
    // Memastikan hanya admin yang bisa mengakses
    private function cekAdmin(Request $request)
    {
        if ($request->user()?->role !== 'admin') {
            abort(403, 'Akses hanya untuk admin.');
        }
    }

    // Menambahkan paket wisata
    public function store(Request $request)
    {
        $this->cekAdmin($request);

        $validated = $request->validate([
            'nama_destinasi' => 'required|string|max:255',
            'lokasi' => 'nullable|string|max:255',
            'jarak' => 'nullable|string|max:255',
            'durasi_perjalanan' => 'nullable|string|max:255',
            'benefit' => 'nullable|string',
            'harga' => 'nullable|integer|min:0',
            'foto' => 'nullable|string|max:255',
        ]);

        $paket = PaketWisata::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Paket wisata berhasil ditambahkan.',
            'data' => $paket
        ], 201);
    }

    
    // Mengubah data paket wisata
    public function update(Request $request, $id)
    {
        $this->cekAdmin($request);

        $paket = PaketWisata::findOrFail($id);

        $validated = $request->validate([
            'nama_destinasi' => 'sometimes|required|string|max:255',
            'lokasi' => 'sometimes|nullable|string|max:255',
            'jarak' => 'sometimes|nullable|string|max:255',
            'durasi_perjalanan' => 'sometimes|nullable|string|max:255',
            'benefit' => 'sometimes|nullable|string',
            'harga' => 'sometimes|nullable|integer|min:0',
            'foto' => 'sometimes|nullable|string|max:255',
        ]);

        $paket->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Paket wisata berhasil diperbarui.',
            'data' => $paket
        ], 200);
    }

    // Menghapus paket wisata
    public function destroy(Request $request, $id)
    {
        $this->cekAdmin($request);

        $paket = PaketWisata::findOrFail($id);

        $paket->delete();

        return response()->json([
            'success' => true,
            'message' => 'Paket wisata berhasil dihapus.'
        ], 200);
    }

}
