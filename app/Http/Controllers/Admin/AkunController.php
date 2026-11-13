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

    public function store(Request $request)
    {
        $request->validate([
            'role'   => 'required|in:dosen,tendik',
            'id'     => 'required|string|max:50',
            'name'   => 'required|string|max:150',
            'email'  => 'required|email|max:150',
            'unit'   => 'required|string|max:100',
            'phone'  => 'required|string|max:30',
            'status' => 'required|in:Aktif,Nonaktif',
        ], [
            'id.required'    => 'Nomor identitas (NIP/NIU) wajib diisi.',
            'name.required'  => 'Nama lengkap sivitas wajib diisi.',
            'email.required' => 'Email resmi wajib diisi.',
            'email.email'    => 'Format email tidak valid.',
            'unit.required'  => 'Unit kerja wajib dipilih.',
            'phone.required' => 'Nomor telepon/WhatsApp wajib diisi.',
        ]);

        return redirect()->route('admin.akun.index', ['tab' => $request->role])
            ->with('success', 'Akun ' . ($request->role === 'dosen' ? 'Dosen' : 'Tendik') . ' berhasil ditambahkan.');
    }

    public function importCsv(Request $request)
    {
        $request->validate([
            'csv_file' => 'required|file|mimes:csv,txt|max:5120',
        ], [
            'csv_file.required' => 'Silakan pilih file CSV terlebih dahulu.',
            'csv_file.mimes'    => 'File harus berformat .csv atau .txt.',
            'csv_file.max'      => 'Ukuran file CSV maksimal 5MB.',
        ]);

        return redirect()->route('admin.akun.index')
            ->with('success', 'Berhasil mengimpor data akun sivitas dari file CSV.');
    }

    public function destroy($id)
    {
        return redirect()->route('admin.akun.index')
            ->with('success', 'Akun sivitas berhasil dihapus dari sistem.');
    }
}
