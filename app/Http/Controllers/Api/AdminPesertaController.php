<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Notifikasi;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Imports\PesertaImport;
use Maatwebsite\Excel\Facades\Excel;

class AdminPesertaController extends Controller
{
    // Memastikan hanya admin yang bisa mengakses
    private function cekAdmin(Request $request)
    {
        if (!$request->user()?->isAdmin()) {
            abort(403, 'Akses hanya untuk admin.');
        }
    }

    // Menampilkan semua peserta
    public function index(Request $request)
    {
        $this->cekAdmin($request);

        $peserta = User::where('role', 'user')
            ->orderBy('nama')
            ->paginate(10);

        return response()->json([
            'success' => true,
            'message' => 'Data peserta berhasil diambil.',
            'data' => $peserta
        ]);
    }

    // Menambahkan peserta
    public function store(Request $request)
    {
        $this->cekAdmin($request);

        $validated = $request->validate([
            'nama'       => 'required|string|max:255',
            'nip'        => 'nullable|string|max:255|unique:users,nip',
            'email'      => 'required|email|max:255|unique:users,email',
            'pekerjaan'  => 'nullable|string|max:255',
            'unit_kerja' => 'nullable|string|max:255',
            'no_telp'    => 'nullable|string|max:255',
            'foto'       => 'nullable|string|max:255',
            'kategori'   => 'required|in:dosen,tendik',
        ]);

        $validated['role'] = 'user';

        $peserta = User::create($validated);

        // Notif ke admin: peserta baru berhasil ditambahkan
        Notifikasi::kirim(
            $request->user()->id_user,
            'Peserta Baru Ditambahkan',
            'Akun sivitas atas nama "' . $peserta->nama . '" berhasil dibuat.',
            '/admin/akun'
        );

        return response()->json([
            'success' => true,
            'message' => 'Peserta berhasil ditambahkan.',
            'data' => $peserta
        ], 201);
    }

    // Melihat detail peserta
    public function show(Request $request, $id)
    {
        $this->cekAdmin($request);

        $peserta = User::where('role', 'user')
            ->findOrFail($id);

        return response()->json([
            'success' => true,
            'message' => 'Detail peserta berhasil diambil.',
            'data' => $peserta
        ]);
    }

    // Mengubah data peserta
    public function update(Request $request, $id)
    {
        $this->cekAdmin($request);

        $peserta = User::where('role', 'user')
            ->findOrFail($id);

        $validated = $request->validate([
            'nama' => 'sometimes|required|string|max:255',
            'nip' => [
                'nullable',
                'sometimes',
                'string',
                'max:255',
                Rule::unique('users', 'nip')->ignore($id, 'id_user'),
            ],
            'email' => [
                'sometimes',
                'required',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($id, 'id_user'),
            ],
            'pekerjaan' => 'nullable|string|max:255',
            'unit_kerja' => 'nullable|string|max:255',
            'no_telp' => 'nullable|string|max:255',
            'foto' => 'nullable|string|max:255',
            'kategori' => 'sometimes|required|in:dosen,tendik',
        ]);

        $peserta->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Data peserta berhasil diperbarui.',
            'data' => $peserta
        ]);
    }

    // Menghapus peserta
    public function destroy(Request $request, $id)
    {
        $this->cekAdmin($request);

        $peserta = User::where('role', 'user')
            ->findOrFail($id);

        // Cascade delete: hapus detail_ukuran_vote → partisipasi → user
        // agar statistik Dashboard & Polling Aktif tetap sinkron
        $partisipasiIds = \App\Models\Partisipasi::where('id_user', $id)
            ->pluck('id_vote');

        if ($partisipasiIds->isNotEmpty()) {
            \App\Models\DetailUkuranVote::whereIn('id_vote', $partisipasiIds)->delete();
            \App\Models\Partisipasi::whereIn('id_vote', $partisipasiIds)->delete();
        }

        $peserta->delete();

        return response()->json([
            'success' => true,
            'message' => 'Peserta berhasil dihapus.'
        ]);
    }

    // Import peserta dari Excel
    public function import(Request $request)
    {
        $this->cekAdmin($request);

        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls,csv|max:5120',
        ]);

        $import = new PesertaImport();

        Excel::import($import, $request->file('file'));

        // Notif ke admin yang melakukan import: hasil import selesai
        Notifikasi::kirim(
            $request->user()->id_user,
            'Import Akun Sivitas Selesai',
            'Import data peserta selesai. Berhasil: ' . $import->berhasil . ', Dilewati: ' . $import->dilewati . '.',
            '/admin/akun'
        );

        return response()->json([
            'success' => true,
            'message' => 'Import peserta selesai.',
            'data' => [
                'berhasil' => $import->berhasil,
                'dilewati' => $import->dilewati,
            ]
        ]);
    }
}