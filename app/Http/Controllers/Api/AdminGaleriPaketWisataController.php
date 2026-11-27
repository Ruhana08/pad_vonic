<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\GaleriPaketWisata;

class AdminGaleriPaketWisataController extends Controller
{
    // Memastikan hanya admin yang bisa mengakses
    private function cekAdmin(Request $request)
    {
        if (!$request->user()?->isAdmin()) {
            abort(403, 'Akses hanya untuk admin.');
        }
    }

    // Menghapus foto galeri paket wisata
    public function destroy(Request $request, $id)
    {
        $this->cekAdmin($request);

        $galeri = GaleriPaketWisata::findOrFail($id);

        if ($galeri->foto) {
            Storage::disk('public')->delete($galeri->foto);
        }

        $galeri->delete();

        return response()->json([
            'success' => true,
            'message' => 'Foto galeri paket wisata berhasil dihapus.'
        ], 200);
    }
}