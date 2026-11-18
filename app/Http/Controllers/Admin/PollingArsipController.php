<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

class PollingArsipController extends Controller
{
    private function paginate($items, Request $request, int $perPage, string $pageName): LengthAwarePaginator
    {
        $page = max(1, (int) $request->query($pageName, 1));

        return new LengthAwarePaginator(
            $items->forPage($page, $perPage)->values(),
            $items->count(),
            $perPage,
            $page,
            ['path' => $request->url(), 'query' => $request->query(), 'pageName' => $pageName]
        );
    }

    public function index(Request $request)
    {
        // ===== STATE CONTROL via Query Parameters =====
        // ?empty=1       → Semua tabel kosong (test empty state)
        // ?active=0      → Tidak ada polling aktif (test publish draft)
        // ?state=draft    → Hanya tampilkan draft saja
        // ?state=selesai  → Hanya tampilkan yang selesai saja
        // Tanpa parameter → Data dummy lengkap (normal)

        $isEmpty    = $request->boolean('empty');
        $noActive   = $request->query('active') === '0';
        $stateFilter = $request->query('state');

        if ($isEmpty) {
            // === EMPTY STATE: Tidak ada data sama sekali ===
            $polls   = collect();
            $history = collect();
        } else {
            // === Data dummy lengkap ===
            $polls = collect([
                [
                    'id'     => '12Q67Hab01',
                    'name'   => 'Piknik DTEDI Periode 2026',
                    'year'   => '2026',
                    'start'  => '12/10/26',
                    'end'    => '30/10/26',
                    'status' => 'Aktif',
                ],
                [
                    'id'     => '12Q67Hab02',
                    'name'   => 'Gathering Dosen & Tendik 2025',
                    'year'   => '2025',
                    'start'  => '05/11/25',
                    'end'    => '20/11/25',
                    'status' => 'Selesai',
                ],
                [
                    'id'     => '12Q67Hab03',
                    'name'   => 'Pemilihan Kaos DTEDI 2025',
                    'year'   => '2025',
                    'start'  => '01/08/25',
                    'end'    => '15/08/25',
                    'status' => 'Selesai',
                ],
                [
                    'id'     => '12Q67Hab04',
                    'name'   => 'Family Gathering Tahunan 2024',
                    'year'   => '2024',
                    'start'  => '10/09/24',
                    'end'    => '25/09/24',
                    'status' => 'Selesai',
                ],
                [
                    'id'     => '12Q67Hab05',
                    'name'   => 'Voting Destinasi Liburan Akhir Tahun',
                    'year'   => '2027',
                    'start'  => '01/12/26',
                    'end'    => '20/12/26',
                    'status' => 'Draft',
                ],
            ]);

            // Filter: ?active=0 → hilangkan semua polling Aktif
            if ($noActive) {
                $polls = $polls->where('status', '!=', 'Aktif')->values();
            }

            // Filter: ?state=draft / selesai / aktif
            if ($stateFilter) {
                $stateMap = ['draft' => 'Draft', 'selesai' => 'Selesai', 'aktif' => 'Aktif'];
                $mapped = $stateMap[strtolower($stateFilter)] ?? null;
                if ($mapped) {
                    $polls = $polls->where('status', $mapped)->values();
                }
            }

            // Sort status priority: Aktif (1) -> Draft (2) -> Selesai (3)
            $statusOrder = ['Aktif' => 1, 'Draft' => 2, 'Selesai' => 3];
            $polls = $polls->sortBy(fn($p) => $statusOrder[$p['status']] ?? 99)->values();

            // SINKRONISASI OTOMATIS: Semua polling berstatus 'Selesai' dari tabel atas otomatis masuk ke Riwayat Arsip
            $completedPolls = $polls->where('status', 'Selesai')->values();

            $history = $completedPolls->map(function ($poll, $idx) {
                return [
                    'id'           => $idx + 1,
                    'title'        => $poll['name'],
                    'description'  => 'Pemilihan paket wisata dan desain kaos tahunan civitas DTEDI periode ' . $poll['year'] . '.',
                    'start'        => $poll['start'],
                    'end'          => $poll['end'],
                    'full_duration'=> $poll['start'] . ' - ' . $poll['end'],
                    'participants' => 120 + ($idx * 80),
                    'peserta_type' => 'Dosen dan Tenaga Kependidikan',
                    'public'       => true,
                    'kaos'         => [
                        'title'    => 'Kaos Desain ' . ($idx === 0 ? 'A' : 'Spirit White'),
                        'image'    => 'https://images.unsplash.com/photo-1507525428034-b723cf961d3e?auto=format&fit=crop&w=600&q=80',
                        'gallery'  => [
                            'https://images.unsplash.com/photo-1519046904884-53103b34b206?auto=format&fit=crop&w=300&q=80',
                            'https://images.unsplash.com/photo-1473186578172-c141e6798cf4?auto=format&fit=crop&w=300&q=80',
                        ],
                    ],
                    'wisata'       => [
                        'title'       => $idx === 0 ? 'Pantai Kartini' : 'Karimunjawa Island',
                        'rating'      => $idx === 0 ? '4,8' : '4,9',
                        'image'       => 'https://images.unsplash.com/photo-1507525428034-b723cf961d3e?auto=format&fit=crop&w=800&q=80',
                        'gallery'     => [
                            'https://images.unsplash.com/photo-1519046904884-53103b34b206?auto=format&fit=crop&w=400&q=80',
                            'https://images.unsplash.com/photo-1473186578172-c141e6798cf4?auto=format&fit=crop&w=400&q=80',
                        ],
                        'lokasi'      => 'Jepara, Jawa Tengah.',
                        'jarak'       => $idx === 0 ? '120 Km' : '180 Km',
                        'durasi'      => $idx === 0 ? '2 Hari' : '3 Hari',
                        'dikunjungi'  => $idx === 0 ? 2 : 1,
                        'benefits'    => ['Bis', 'Makan', 'Minum', 'Kamar 2 hari'],
                        'biaya'       => 'Rp 500.000',
                    ],
                ];
            })->values();
        }

        return view('admin.polling.arsip', [
            'polls'     => $this->paginate($polls, $request, 5, 'polling_page'),
            'history'   => $history,
            'publicIds' => $history->where('public', true)->pluck('id')->values(),
        ]);
    }
}
