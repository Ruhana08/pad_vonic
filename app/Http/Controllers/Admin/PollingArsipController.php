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
        // Data dummy: nanti ganti dengan query database
        $statuses = ['Draft', 'Aktif', 'Selesai'];

        $polls = collect(range(1, 30))->map(fn($i) => [
            'id'     => '12Q67Hab' . $i,
            'name'   => 'Piknik 2025',
            'year'   => '2025',
            'start'  => '12/10/25',
            'end'    => '30/10/25',
            'status' => $statuses[$i % 3],
        ]);

        $history = collect(range(1, 36))->map(fn($i) => [
            'id'           => $i,
            'title'        => 'Piknik DTEDI Periode 2025',
            'description'  => 'Riwayat apadah ini terserah deskripsinya gimana.',
            'period'       => '8 - 12 Oktober 2025',
            'participants' => 200,
            'public'       => $i <= 12, // dummy: nanti dari kolom database
        ])->values();

        return view('admin.polling.arsip', [
            'polls'     => $this->paginate($polls, $request, 5, 'polling_page'),
            'history'   => $history,
            'publicIds' => $history->where('public', true)->pluck('id')->values(),
        ]);
    }
}
