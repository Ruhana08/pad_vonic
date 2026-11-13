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

    public function updateProfile(Request $request)
    {
        $request->validate([
            'nama'  => 'required|string|max:150',
            'email' => 'required|email|max:150',
            'bio'   => 'nullable|string|max:300',
        ], [
            'nama.required'  => 'Nama admin wajib diisi.',
            'email.required' => 'Email admin wajib diisi.',
            'email.email'    => 'Format email tidak valid.',
        ]);

        return redirect()->route('admin.profil')
            ->with('success', 'Informasi profil admin berhasil diperbarui.');
    }

    public function storeAdmin(Request $request)
    {
        $request->validate([
            'nama'                  => 'required|string|max:150',
            'email'                 => 'required|email|ends_with:@ugm.ac.id,@mail.ugm.ac.id|max:150',
            'role'                  => 'required|string',
            'password'              => 'required|string|min:8|same:password_confirmation',
            'password_confirmation' => 'required|string',
        ], [
            'nama.required'     => 'Nama lengkap calon admin wajib diisi.',
            'email.required'    => 'Email institusi UGM wajib diisi.',
            'email.ends_with'   => 'Email harus menggunakan domain resmi @ugm.ac.id atau @mail.ugm.ac.id.',
            'password.required' => 'Kata sandi akun baru wajib diisi.',
            'password.min'      => 'Kata sandi minimal 8 karakter.',
            'password.same'     => 'Konfirmasi kata sandi tidak cocok.',
        ]);

        return redirect()->route('admin.profil')
            ->with('success', 'Akun admin baru berhasil didaftarkan ke sistem.');
    }
}