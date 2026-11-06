<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

class PollingAktifController extends Controller
{
    public function index(Request $request)
    {
        // Data dummy: nanti ganti dengan query database
        $categories = [
            'peserta' => ['label' => 'Peserta', 'options' => [
                ['name' => 'Dosen dan tendik dipisah',   'dosen' => 154, 'tendik' => 66],
                ['name' => 'Dosen dan tendik digabung',  'dosen' => 90,  'tendik' => 40],
            ]],
            'wisata' => ['label' => 'Tempat Wisata', 'options' => [
                ['name' => 'Pantai Soleram',  'dosen' => 120, 'tendik' => 40],
                ['name' => 'Pantai Parangtritis', 'dosen' => 80, 'tendik' => 30],
                ['name' => 'Candi Prambanan', 'dosen' => 60, 'tendik' => 20],
                ['name' => 'Kebun Teh',       'dosen' => 40, 'tendik' => 10],
                ['name' => 'Gunung Api Purba', 'dosen' => 20, 'tendik' => 10],
            ]],
            'kaos' => ['label' => 'Desain Kaos', 'options' => [
                ['name' => 'Desain A', 'dosen' => 110, 'tendik' => 50],
                ['name' => 'Desain B', 'dosen' => 90,  'tendik' => 30],
                ['name' => 'Desain C', 'dosen' => 70,  'tendik' => 20],
                ['name' => 'Desain D', 'dosen' => 40,  'tendik' => 10],
                ['name' => 'Desain E', 'dosen' => 20,  'tendik' => 10],
            ]],
        ];

        // Hitung total, persentase dosen/tendik, dan proporsi per opsi
        foreach ($categories as $key => &$cat) {
            $catTotal = collect($cat['options'])->sum(fn ($o) => $o['dosen'] + $o['tendik']);
            $cat['options'] = collect($cat['options'])->map(function ($o) use ($catTotal) {
                $total = $o['dosen'] + $o['tendik'];
                return $o + [
                    'total'      => $total,
                    'dosen_pct'  => $total ? round($o['dosen'] / $total * 100) : 0,
                    'tendik_pct' => $total ? round($o['tendik'] / $total * 100) : 0,
                    'proporsi'   => $catTotal ? round($total / $catTotal * 100) : 0,
                ];
            })->sortByDesc('total')->values();
        }
        unset($cat);

        $kategori = $request->query('kategori', 'semua');
        $kategori = array_key_exists($kategori, $categories) ? $kategori : 'semua';

        // Aktivitas terbaru (paginasi)
        $perPage = (int) $request->query('per_page', 10);
        $perPage = in_array($perPage, [10, 25, 50]) ? $perPage : 10;
        $page = max(1, (int) $request->query('page', 1));

        $all = collect(range(1, 60))->map(fn () => [
            'name'   => 'Melisa Danuar, S.T',
            'email'  => 'melisadanuar@mail.ugm.ac.id',
            'peserta' => 'Opsi 1',
            'wisata' => 'Opsi 1',
            'kaos'   => 'Opsi 1',
            'status' => 'Tersimpan',
        ]);

        $activities = new LengthAwarePaginator(
            $all->forPage($page, $perPage)->values(),
            $all->count(), $perPage, $page,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        return view('admin.polling.aktif', [
            'title'      => 'Piknik DTEDI Periode 2026',
            'subtitle'   => 'Pemungutan suara untuk menentukan lokasi dan tanggal pelaksanaan piknik tahunan civitas DTEDI.',
            'countdown'  => ['days' => 3, 'hours' => 8, 'minutes' => 45],
            'deadline'   => ['Jumat', '16 Okt 2026, 18.00 WIB'],
            'totalVote'  => ['percent' => 82, 'received' => 279, 'pending' => 61],
            'categories' => $categories,
            'kategori'   => $kategori,
            'activities' => $activities,
        ]);
    }
}