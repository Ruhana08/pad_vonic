<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\PeriodePolling;
use App\Models\PaketWisata;
use App\Models\DesainKaos;
use App\Models\Partisipasi;


class AdminDashboardController extends Controller
{
    public function index(Request $request)
    {
        // Memastikan hanya admin yang bisa mengakses
        if ($request->user()?->role !== 'admin') {
            abort(403, 'Akses hanya untuk admin.');
        }

        return response()->json([
            'success' => true,
            'message' => 'Dashboard admin berhasil ditampilkan.',
            'data' => [
                'total_peserta' => User::where('role', 'user')->count(),
                'total_periode_polling' => PeriodePolling::count(),
                'total_paket_wisata' => PaketWisata::count(),
                'total_desain_kaos' => DesainKaos::count(),
                'total_voting' => Partisipasi::count(),
            ]
        ], 200);
    }
}
