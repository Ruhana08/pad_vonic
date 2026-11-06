<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

class ProfilController extends Controller
{
    public function index(Request $request)
    {
        // Data dummy: nanti ganti dengan auth()->user() dan query log
        $profile = [
            'name'  => 'Maria Danuar, S.Kom',
            'email' => 'mariadanuar@mail.ugm.co.id',
            'photo' => null, // URL foto; kalau null tampil inisial
        ];

        $perPage = 10;
        $page = max(1, (int) $request->query('page', 1));

        $all = collect(range(1, 60))->map(fn () => [
            'id'     => '12Q67Hab4',
            'desc'   => 'Lorem Ipsum',
            'admin'  => 'melisadanuar@mail.ugm.ac.id',
            'time'   => '09.90 12/34/89',
            'status' => 'Tersimpan',
        ]);

        $logs = new LengthAwarePaginator(
            $all->forPage($page, $perPage)->values(),
            $all->count(), $perPage, $page,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        return view('admin.profil', compact('profile', 'logs'));
    }
}