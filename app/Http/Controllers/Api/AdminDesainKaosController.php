<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\DesainKaos;

class AdminDesainKaosController extends Controller
{
    // Memastikan hanya admin yang bisa mengakses
    private function cekAdmin(Request $request)
    {
        if ($request->user()?->role !== 'admin') {
            abort(403, 'Akses hanya untuk admin.');
        }
    }

    // Menambahkan desain kaos
    public function store(Request $request)
    {
        $this->cekAdmin($request);

        $validated = $request->validate([
            'nama_desain' => 'required|string|max:255',
            'foto' => 'nullable|string|max:255',
        ]);

        $desain = DesainKaos::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Desain kaos berhasil ditambahkan.',
            'data' => $desain
        ], 201);
    }

    // Mengubah data desain kaos
    public function update(Request $request, $id)
    {
        $this->cekAdmin($request);

        $desain = DesainKaos::findOrFail($id);

        $validated = $request->validate([
            'nama_desain' => 'sometimes|required|string|max:255',
            'foto' => 'sometimes|nullable|string|max:255',
        ]);

        $desain->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Desain kaos berhasil diperbarui.',
            'data' => $desain
        ], 200);
    }

    // Menghapus desain kaos
    public function destroy(Request $request, $id)
    {
        $this->cekAdmin($request);

        $desain = DesainKaos::findOrFail($id);

        $desain->delete();

        return response()->json([
            'success' => true,
            'message' => 'Desain kaos berhasil dihapus.'
        ], 200);
    }
}

