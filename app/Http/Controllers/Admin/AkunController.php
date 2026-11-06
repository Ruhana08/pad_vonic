<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

class AkunController extends Controller
{
    public function index(Request $request)
    {
        $tab = $request->query('tab', 'dosen') === 'tendik' ? 'tendik' : 'dosen';

        $perPage = (int) $request->query('per_page', 10);
        $perPage = in_array($perPage, [10, 25, 50]) ? $perPage : 10;
        $page = max(1, (int) $request->query('page', 1));

        // Data dummy: nanti ganti dengan query database
        $units = ['TRIK', 'TEKNO', 'TEDI', 'ADMIN'];
        $all = collect(range(1, 60))->map(fn($i) => [
            'id'     => '12Q67Hab' . $i,
            'name'   => $tab === 'dosen' ? 'Melisa Danuar, S.T' : 'Budi Santoso, S.Kom',
            'email'  => $tab === 'dosen' ? 'melisadanuar@mail.ugm.ac.id' : 'budisantoso@mail.ugm.ac.id',
            'unit'   => $units[$i % count($units)],
            'phone'  => '+623897702722',
            'status' => $i % 4 === 0 ? 'Nonaktif' : 'Aktif',
        ]);

        $filtered = $all
            ->when($request->filled('nama'), fn($c) => $c->filter(
                fn($a) => str_contains(strtolower($a['name']), strtolower($request->nama))
            ))
            ->when($request->filled('unit'), fn($c) => $c->where('unit', $request->unit))
            ->when($request->filled('status'), fn($c) => $c->where('status', $request->status))
            ->values();

        $accounts = new LengthAwarePaginator(
            $filtered->forPage($page, $perPage)->values(),
            $filtered->count(),
            $perPage,
            $page,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        return view('admin.akun.index', [
            'tab'      => $tab,
            'units'    => $units,
            'accounts' => $accounts,
        ]);
    }
}
