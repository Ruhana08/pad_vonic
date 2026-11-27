<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class KelolaAdminController extends Controller
{
    public function index(Request $request)
    {
        $search = trim((string) $request->query('search', ''));

        $query = User::whereIn('role', ['master_admin', 'admin'])
            ->orderByRaw("FIELD(role, 'master_admin', 'admin')")
            ->orderBy('nama');

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $admins = $query->get()->map(fn ($u) => [
            'id'         => $u->id_user,
            'nama'       => $u->nama,
            'email'      => $u->email,
            'role'       => $u->role,
            'is_master'  => $u->role === 'master_admin',
            'is_self'    => $u->id_user === Auth::id(),
            'created_at' => $u->created_at ? date('d M Y', strtotime($u->created_at)) : '—',
        ]);

        return view('admin.kelola-admin.index', [
            'admins' => $admins,
            'search' => $search,
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama'     => 'required|string|max:150',
            'email'    => 'required|email|ends_with:@mail.ugm.ac.id|unique:users,email|max:150',
            'password' => 'required|string|min:8|confirmed',
        ], [
            'nama.required'      => 'Nama lengkap admin wajib diisi.',
            'email.required'     => 'Email institusi wajib diisi.',
            'email.email'        => 'Format email tidak valid.',
            'email.ends_with'    => 'Email admin wajib menggunakan domain @mail.ugm.ac.id.',
            'email.unique'       => 'Email tersebut sudah terdaftar di sistem.',
            'password.required'  => 'Kata sandi wajib diisi.',
            'password.min'       => 'Kata sandi minimal 8 karakter.',
            'password.confirmed' => 'Konfirmasi kata sandi tidak cocok.',
        ]);

        User::create([
            'nama'       => trim($request->nama),
            'email'      => strtolower(trim($request->email)),
            'password'   => Hash::make($request->password),
            'role'       => 'admin',
            'unit_kerja' => 'DTEDI SV UGM',
            'pekerjaan'  => 'Administrator',
        ]);

        return redirect()->route('admin.kelola-admin.index')
            ->with('success', 'Akun admin baru berhasil ditambahkan.');
    }

    public function update(Request $request, $id)
    {
        $admin = User::whereIn('role', ['master_admin', 'admin'])->findOrFail($id);

        $request->validate([
            'nama'     => 'required|string|max:150',
            'email'    => "required|email|ends_with:@mail.ugm.ac.id|unique:users,email,{$admin->id_user},id_user|max:150",
            'password' => 'nullable|string|min:8|confirmed',
        ], [
            'nama.required'      => 'Nama lengkap admin wajib diisi.',
            'email.required'     => 'Email institusi wajib diisi.',
            'email.email'        => 'Format email tidak valid.',
            'email.ends_with'    => 'Email admin wajib menggunakan domain @mail.ugm.ac.id.',
            'email.unique'       => 'Email tersebut sudah digunakan oleh akun lain.',
            'password.min'       => 'Kata sandi baru minimal 8 karakter.',
            'password.confirmed' => 'Konfirmasi kata sandi baru tidak cocok.',
        ]);

        $admin->nama = trim($request->nama);
        $admin->email = strtolower(trim($request->email));
        if ($request->filled('password')) {
            $admin->password = Hash::make($request->password);
        }
        $admin->save();

        return redirect()->route('admin.kelola-admin.index')
            ->with('success', 'Data akun admin berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $admin = User::whereIn('role', ['master_admin', 'admin'])->findOrFail($id);

        if ($admin->role === 'master_admin' || $admin->id_user === Auth::id()) {
            return redirect()->route('admin.kelola-admin.index')
                ->with('error', 'Akun Master Admin tidak dapat dihapus.');
        }

        $admin->delete();

        return redirect()->route('admin.kelola-admin.index')
            ->with('success', 'Akun admin berhasil dihapus.');
    }
}
