<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\DesainKaos;

class AdminDesainKaosController extends Controller
{
    // Memastikan hanya admin yang bisa mengakses
    private function cekAdmin(Request $request)
    {
        if (!$request->user()?->isAdmin()) {
            abort(403, 'Akses hanya untuk admin.');
        }
    }

    // Menambahkan desain kaos
    public function store(Request $request)
    {
        $this->cekAdmin($request);

        $validated = $request->validate([
            'nama_desain' => 'required|string|max:255',
            'foto' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:4096',
            'galeri' => 'nullable|array|max:2',
            'galeri.*' => 'image|mimes:jpg,jpeg,png,webp|max:4096',
        ]);

        $data = [
            'nama_desain' => $validated['nama_desain'],
        ];

        if ($request->hasFile('foto')) {
            $data['foto'] = $request->file('foto')
                ->store('desain-kaos', 'public');
        }

        $desain = DesainKaos::create($data);

        if ($request->hasFile('galeri')) {
            foreach ($request->file('galeri') as $foto) {
                $desain->galeri()->create([
                    'foto' => $foto->store('desain-kaos/galeri', 'public'),
                ]);
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Desain kaos berhasil ditambahkan.',
            'data' => $desain->load('galeri'),
        ], 201);
    }

    // Mengubah data desain kaos
    public function update(Request $request, $id)
    {
        $this->cekAdmin($request);

        dd([
        'method' => $request->method(),
        'data' => $request->all(),
        'files' => $request->allFiles(),
    ]);

        $desain = DesainKaos::with('galeri')->findOrFail($id);

        $validated = $request->validate([
            'nama_desain' => 'sometimes|required|string|max:255',
            'foto' => 'sometimes|nullable|image|mimes:jpg,jpeg,png,webp|max:4096',
            'galeri' => 'nullable|array',
            'galeri.*' => 'image|mimes:jpg,jpeg,png,webp|max:4096',
        ]);

        $jumlahFotoSaatIni = ($desain->foto ? 1 : 0)
            + $desain->galeri->count();

        $jumlahFotoBaru = count($request->file('galeri', []));

        if ($jumlahFotoSaatIni + $jumlahFotoBaru > 3) {
            return response()->json([
                'success' => false,
                'message' => 'Total foto desain kaos maksimal 3 foto.',
            ], 422);
        }

        if (isset($validated['nama_desain'])) {
            $desain->nama_desain = $validated['nama_desain'];
        }

        if ($request->hasFile('foto')) {
            if ($desain->foto) {
                Storage::disk('public')->delete($desain->foto);
            }

            $desain->foto = $request->file('foto')
                ->store('desain-kaos', 'public');
        }

        $desain->save();

        if ($request->hasFile('galeri')) {
            foreach ($request->file('galeri') as $foto) {
                $desain->galeri()->create([
                    'foto' => $foto->store('desain-kaos/galeri', 'public'),
                ]);
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Desain kaos berhasil diperbarui.',
            'data' => $desain->load('galeri'),
        ], 200);
    }

    // Menghapus desain kaos
    public function destroy(Request $request, $id)
    {
        $this->cekAdmin($request);

        $desain = DesainKaos::with('galeri')->findOrFail($id);

        if ($desain->foto) {
            Storage::disk('public')->delete($desain->foto);
        }

        foreach ($desain->galeri as $foto) {
            Storage::disk('public')->delete($foto->foto);
        }

        $desain->delete();

        return response()->json([
            'success' => true,
            'message' => 'Desain kaos berhasil dihapus.',
        ], 200);
    }
}
