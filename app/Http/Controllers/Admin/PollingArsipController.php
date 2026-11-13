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

        // Sort status priority: Aktif (1) -> Draft (2) -> Selesai (3)
        $statusOrder = ['Aktif' => 1, 'Draft' => 2, 'Selesai' => 3];
        $polls = $polls->sortBy(fn($p) => $statusOrder[$p['status']] ?? 99)->values();

        $history = collect([
            [
                'id'           => 1,
                'title'        => 'Piknik DTEDI Periode 2026',
                'description'  => 'Pemilihan paket wisata dan desain kaos tahunan civitas DTEDI.',
                'start'        => '01/09/26',
                'end'          => '12/09/26',
                'full_duration'=> '1 September 2026 - 12 September 2026',
                'participants' => 120,
                'peserta_type' => 'Dosen dan Tenaga Kependidikan',
                'public'       => true,
                'kaos'         => [
                    'title'    => 'Kaos A',
                    'image'    => 'https://images.unsplash.com/photo-1507525428034-b723cf961d3e?auto=format&fit=crop&w=600&q=80',
                    'gallery'  => [
                        'https://images.unsplash.com/photo-1519046904884-53103b34b206?auto=format&fit=crop&w=300&q=80',
                        'https://images.unsplash.com/photo-1473186578172-c141e6798cf4?auto=format&fit=crop&w=300&q=80',
                    ],
                ],
                'wisata'       => [
                    'title'       => 'Pantai Kartini',
                    'rating'      => '4,8',
                    'image'       => 'https://images.unsplash.com/photo-1507525428034-b723cf961d3e?auto=format&fit=crop&w=800&q=80',
                    'gallery'     => [
                        'https://images.unsplash.com/photo-1519046904884-53103b34b206?auto=format&fit=crop&w=400&q=80',
                        'https://images.unsplash.com/photo-1473186578172-c141e6798cf4?auto=format&fit=crop&w=400&q=80',
                    ],
                    'lokasi'      => 'Pantai Kartini, Jepara, Jawa Tengah.',
                    'jarak'       => '120 Km',
                    'durasi'      => '2 Hari',
                    'dikunjungi'  => 2,
                    'benefits'    => ['Bis', 'Makan', 'Minum', 'Kamar 2 hari'],
                    'biaya'       => 'Rp 500.000',
                ],
            ],
            [
                'id'           => 2,
                'title'        => 'Gathering Civitas DTEDI 2024',
                'description'  => 'Eksplorasi Karimunjawa 3H2M bersama keluarga besar DTEDI.',
                'start'        => '15/11/24',
                'end'          => '18/11/24',
                'full_duration'=> '15 November 2024 - 18 November 2024',
                'participants' => 285,
                'peserta_type' => 'Dosen dan Tenaga Kependidikan',
                'public'       => true,
                'kaos'         => [
                    'title'    => 'Desain Spirit White',
                    'image'    => 'https://images.unsplash.com/photo-1521572267360-ee0c2909d518?auto=format&fit=crop&w=600&q=80',
                    'gallery'  => [
                        'https://images.unsplash.com/photo-1503342217505-b0a15ec3261c?auto=format&fit=crop&w=300&q=80',
                    ],
                ],
                'wisata'       => [
                    'title'       => 'Karimunjawa Island',
                    'rating'      => '4,9',
                    'image'       => 'https://images.unsplash.com/photo-1544551763-46a013bb70d5?auto=format&fit=crop&w=800&q=80',
                    'gallery'     => [
                        'https://images.unsplash.com/photo-1506929562872-bb421503ef21?auto=format&fit=crop&w=400&q=80',
                    ],
                    'lokasi'      => 'Karimunjawa, Jepara, Jawa Tengah.',
                    'jarak'       => '180 Km',
                    'durasi'      => '3 Hari',
                    'dikunjungi'  => 1,
                    'benefits'    => ['Kapal Cepat', 'Resort', 'Snorkeling', 'Makan 3x'],
                    'biaya'       => 'Rp 850.000',
                ],
            ],
        ])->values();

        return view('admin.polling.arsip', [
            'polls'     => $this->paginate($polls, $request, 5, 'polling_page'),
            'history'   => $history,
            'publicIds' => $history->where('public', true)->pluck('id')->values(),
        ]);
    }
}
