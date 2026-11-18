<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        // ===== STATE CONTROL =====
        // ?empty=1 → Dashboard kosong (belum ada polling aktif)
        $isEmpty = $request->boolean('empty');

        if ($isEmpty) {
            return view('admin.dashboard', [
                'countdown'  => ['days' => 0, 'hours' => 0, 'minutes' => 0],
                'deadline'   => '-',
                'totalVote'  => ['percent' => 0, 'received' => 0, 'pending' => 0],
                'activities' => collect(),
                'categories' => [],
            ]);
        }

        // Data dummy: nanti ganti dengan query database
        return view('admin.dashboard', [
            'countdown' => ['days' => 3, 'hours' => 8, 'minutes' => 45],
            'deadline'  => 'Jumat, 16 Okt 2026, 18.00 WIB',
            'totalVote' => ['percent' => 82, 'received' => 279, 'pending' => 61],
            'activities' => collect(range(1, 3))->map(fn () => [
                'name' => 'Melisa Danuar, S.Kom',
                'time' => '12 menit lalu',
                'text' => 'Mengirimkan suara',
            ]),
            'categories' => [
                ['title' => 'Pembagian Peserta', 'options' => [
                    ['name' => 'Dosen dan Tenaga Didik dipisah', 'percent' => 58],
                    ['name' => 'Fully In-Person Only', 'percent' => 42],
                ]],
                ['title' => 'Paket Wisata', 'options' => [
                    ['name' => 'Pantai Soleram', 'percent' => 51],
                    ['name' => 'Pantai Soleram', 'percent' => 31],
                    ['name' => 'Pantai Soleram', 'percent' => 18],
                ]],
                ['title' => 'Kaos', 'options' => [
                    ['name' => 'Premium Zip-Up Hoodie', 'percent' => 45],
                    ['name' => 'Embroidered Baseball Cap', 'percent' => 33],
                    ['name' => 'Organic Cotton Tee', 'percent' => 22],
                ]],
            ],
        ]);
    }
}