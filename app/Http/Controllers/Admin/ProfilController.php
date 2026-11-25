<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Partisipasi;
use App\Models\PeriodePolling;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class ProfilController extends Controller
{
    public function index(Request $request)
    {
        // Ambil data admin yang sedang login, atau fallback ke admin pertama di database
        $adminUser = Auth::user() ?? User::where('role', 'admin')->first() ?? User::first();

        $profile = [
            'id'         => $adminUser?->id_user ?? 1,
            'name'       => $adminUser?->nama ?? 'Administrator DTEDI',
            'email'      => $adminUser?->email ?? 'admin.dtedi@ugm.ac.id',
            'nip'        => $adminUser?->nip ?? '-',
            'unit_kerja' => $adminUser?->unit_kerja ?? 'Departemen TEDIV',
            'pekerjaan'  => $adminUser?->pekerjaan ?? 'Tenaga Kependidikan',
            'photo'      => $adminUser?->foto ?? null,
        ];

        $perPage = 10;
        $page = max(1, (int) $request->query('page', 1));

        // Ambil log aktivitas nyata dari database (hasil partisipasi e-voting & aktivitas polling)
        $realVotes = Partisipasi::with(['user', 'periodePolling'])
            ->orderBy('waktu_submit', 'desc')
            ->get();

        if ($realVotes->count() > 0) {
            $all = $realVotes->map(function ($p) {
                return [
                    'id'     => 'LOG-' . str_pad($p->id_partisipasi, 4, '0', STR_PAD_LEFT),
                    'desc'   => 'Partisipasi e-voting ' . ($p->periodePolling?->nama_periode ?? 'Piknik DTEDI'),
                    'admin'  => $p->user?->email ?? ($p->user?->nama ?? 'Civitas DTEDI'),
                    'time'   => $p->waktu_submit ? date('H:i d/m/Y', strtotime($p->waktu_submit)) : date('H:i d/m/Y'),
                    'status' => 'Tersimpan',
                ];
            });
        } else {
            // Jika belum ada voting submit, ambil daftar polling sebagai log pembuatan/pengelolaan
            $periodes = PeriodePolling::orderBy('id_periode', 'desc')->get();
            $all = $periodes->map(function ($pol) use ($profile) {
                return [
                    'id'     => 'ACT-' . str_pad($pol->id_periode, 4, '0', STR_PAD_LEFT),
                    'desc'   => 'Pengelolaan polling: ' . $pol->nama_periode . ' (' . ucfirst($pol->status) . ')',
                    'admin'  => $profile['email'],
                    'time'   => date('H:i d/m/Y', strtotime($pol->tanggal_mulai ?? now())),
                    'status' => 'Tersimpan',
                ];
            });
        }

        $logs = new LengthAwarePaginator(
            $all->forPage($page, $perPage)->values(),
            $all->count(),
            $perPage,
            $page,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        return view('admin.profil', compact('profile', 'logs'));
    }

    public function updateProfile(Request $request)
    {
        $request->validate([
            'nama'  => 'required|string|max:150',
            'email' => 'required|email|max:150',
        ], [
            'nama.required'  => 'Nama admin wajib diisi.',
            'email.required' => 'Email admin wajib diisi.',
            'email.email'    => 'Format email tidak valid.',
        ]);

        $adminUser = Auth::user() ?? User::where('role', 'admin')->first() ?? User::first();
        if ($adminUser) {
            $adminUser->nama = $request->nama;
            $adminUser->email = $request->email;
            if ($request->filled('password')) {
                $request->validate([
                    'password' => ['min:8', 'regex:/[A-Z]/', 'regex:/[a-z]/', 'regex:/[0-9]/', 'regex:/[^A-Za-z0-9]/', 'confirmed'],
                ], [
                    'password.min'       => 'Password minimal 8 karakter.',
                    'password.regex'     => 'Password harus mengandung huruf besar, huruf kecil, angka, dan karakter spesial.',
                    'password.confirmed' => 'Konfirmasi password tidak cocok.',
                ]);
                $adminUser->password = Hash::make($request->password);
            }
            $adminUser->save();
        }

        return redirect()->route('admin.profil')
            ->with('success', 'Informasi profil admin berhasil diperbarui.');
    }

    public function storeAdmin(Request $request)
    {
        $request->validate([
            'nama'                  => 'required|string|max:150',
            'email'                 => 'required|email|ends_with:@ugm.ac.id,@mail.ugm.ac.id|unique:users,email|max:150',
            'unit_kerja'            => 'nullable|string|max:100',
            'no_telp'               => 'nullable|string|max:30',
            'password'              => ['required', 'string', 'min:8', 'regex:/[A-Z]/', 'regex:/[a-z]/', 'regex:/[0-9]/', 'regex:/[^A-Za-z0-9]/', 'confirmed'],
        ], [
            'nama.required'      => 'Nama lengkap admin baru wajib diisi.',
            'email.required'     => 'Email institusi UGM wajib diisi.',
            'email.ends_with'    => 'Email harus menggunakan domain resmi @ugm.ac.id atau @mail.ugm.ac.id.',
            'email.unique'       => 'Email tersebut sudah terdaftar di sistem Vonic.',
            'password.required'  => 'Password akun baru wajib diisi.',
            'password.min'       => 'Password minimal 8 karakter.',
            'password.regex'     => 'Password harus mengandung huruf besar, huruf kecil, angka, dan karakter spesial.',
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
        ]);

        User::create([
            'nama'       => $request->nama,
            'email'      => strtolower(trim($request->email)),
            'unit_kerja' => $request->unit_kerja ?? 'Departemen TEDIV',
            'no_telp'    => $request->no_telp ?? '-',
            'role'       => 'admin',
            'kategori'   => 'Tenaga Kependidikan',
            'pekerjaan'  => 'Administrator Sistem Vonic',
            'password'   => Hash::make($request->password),
        ]);

        return redirect()->route('admin.profil')
            ->with('success', 'Akun admin baru berhasil didaftarkan ke sistem Vonic.');
    }
}