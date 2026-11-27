<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\PaketWisata;

class AdminPaketWisataController extends Controller
{
    private function cekAdmin(Request $request)
    {
        if (!$request->user()?->isAdmin()) {
            abort(403, 'Akses hanya untuk admin.');
        }
    }

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
            'foto' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:4096',
            'galeri' => 'nullable|array|max:2',
            'galeri.*' => 'image|mimes:jpg,jpeg,png,webp|max:4096',
        ]);

        $paket = PaketWisata::create([
            'nama_destinasi' => $validated['nama_destinasi'],
            'lokasi' => $validated['lokasi'] ?? null,
            'jarak' => $validated['jarak'] ?? null,
            'durasi_perjalanan' => $validated['durasi_perjalanan'] ?? null,
            'benefit' => $validated['benefit'] ?? null,
            'harga' => $validated['harga'] ?? null,
            'foto' => isset($validated['foto'])
                ? $validated['foto']->store('paket-wisata', 'public')
                : null,
        ]);

        if ($request->hasFile('galeri')) {
            foreach ($request->file('galeri') as $foto) {
                $paket->galeri()->create([
                    'foto' => $foto->store('paket-wisata/galeri', 'public'),
                ]);
            }
        }

        $paket->load('galeri');

        return response()->json([
            'success' => true,
            'message' => 'Paket wisata berhasil ditambahkan.',
            'data' => $paket
        ], 201);
    }

    public function update(Request $request, $id)
    {
        $this->cekAdmin($request);

        $paket = PaketWisata::with('galeri')->findOrFail($id);

        $validated = $request->validate([
            'nama_destinasi' => 'sometimes|required|string|max:255',
            'lokasi' => 'sometimes|nullable|string|max:255',
            'jarak' => 'sometimes|nullable|string|max:255',
            'durasi_perjalanan' => 'sometimes|nullable|string|max:255',
            'benefit' => 'sometimes|nullable|string',
            'harga' => 'sometimes|nullable|integer|min:0',
            'foto' => 'sometimes|nullable|image|mimes:jpg,jpeg,png,webp|max:4096',
            'galeri' => 'nullable|array',
            'galeri.*' => 'image|mimes:jpg,jpeg,png,webp|max:4096',
        ]);

        $jumlahFoto = ($paket->foto ? 1 : 0) + $paket->galeri->count();

        $fotoUtamaBaru = $request->hasFile('foto');

        $fotoTambahanBaru = count($request->file('galeri', []) ?? []);

        $jumlahFotoAkhir = $jumlahFoto + $fotoTambahanBaru;

        if ($jumlahFotoAkhir > 3) {
            return response()->json([
                'success' => false,
                'message' => 'Maksimal 3 foto untuk setiap paket wisata, termasuk foto utama.',
            ], 422);
        }

        $data = collect($validated)->except(['foto', 'galeri'])->toArray();

        if ($fotoUtamaBaru) {
            if ($paket->foto) {
                Storage::disk('public')->delete($paket->foto);
            }

            $data['foto'] = $request->file('foto')->store('paket-wisata', 'public');
        }

        $paket->update($data);

        if ($request->hasFile('galeri')) {
            foreach ($request->file('galeri') as $foto) {
                $paket->galeri()->create([
                    'foto' => $foto->store('paket-wisata/galeri', 'public'),
                ]);
            }
        }

        $paket->load('galeri');

        return response()->json([
            'success' => true,
            'message' => 'Paket wisata berhasil diperbarui.',
            'data' => $paket
        ], 200);
    }

    public function destroy(Request $request, $id)
    {
        $this->cekAdmin($request);

        $paket = PaketWisata::with('galeri')->findOrFail($id);

        if ($paket->foto) {
            Storage::disk('public')->delete($paket->foto);
        }

        foreach ($paket->galeri as $foto) {
            Storage::disk('public')->delete($foto->foto);
        }

        $paket->delete();

        return response()->json([
            'success' => true,
            'message' => 'Paket wisata berhasil dihapus.'
        ], 200);
    }
}