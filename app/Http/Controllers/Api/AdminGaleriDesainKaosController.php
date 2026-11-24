<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\GaleriDesainKaos;

class AdminGaleriDesainKaosController extends Controller
{
    private function cekAdmin(Request $request)
    {
        if ($request->user()?->role !== 'admin') {
            abort(403, 'Akses hanya untuk admin.');
        }
    }

    public function destroy(Request $request, $id)
    {
        $this->cekAdmin($request);

        $galeri = GaleriDesainKaos::findOrFail($id);

        if ($galeri->foto) {
            Storage::disk('public')->delete($galeri->foto);
        }

        $galeri->delete();

        return response()->json([
            'success' => true,
            'message' => 'Foto galeri desain kaos berhasil dihapus.'
        ], 200);
    }
}