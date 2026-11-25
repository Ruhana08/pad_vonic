<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class AkunController extends Controller
{
    public function index(Request $request)
    {
        $tab = $request->query('tab', 'dosen') === 'tendik' ? 'tendik' : 'dosen';

        $perPage = (int) $request->query('per_page', 10);
        $perPage = in_array($perPage, [10, 25, 50]) ? $perPage : 10;

        // Daftar unit kerja resmi DTEDI SV UGM
        $units = [
            'Teknologi Rekayasa Perangkat Lunak',
            'Teknologi Rekayasa Internet',
            'Teknologi Rekayasa Instrumentasi dan Kontrol',
            'Teknologi Rekayasa Elektro',
            'Akademik',
            'Kemahasiswaan',
            'Sarana dan Prasarana',
        ];

        // Query dari tabel users
        $query = User::query();

        // Filter tab kategori (dosen / tendik)
        $query->where('kategori', $tab);

        // Filter pencarian nama
        if ($request->filled('nama')) {
            $keyword = '%' . strtolower(trim($request->nama)) . '%';
            $query->whereRaw('LOWER(nama) LIKE ?', [$keyword]);
        }

        // Filter unit kerja
        if ($request->filled('unit')) {
            $query->where('unit_kerja', $request->unit);
        }

        // State empty testing support: ?empty=1
        if ($request->boolean('empty')) {
            $query->whereRaw('1 = 0');
        }

        $users = $query->orderBy('id_user', 'asc')
            ->paginate($perPage)
            ->withQueryString();

        // Transform ke format yang diharapkan view
        $accounts = $users->through(function ($u) {
            return [
                'id'     => $u->nip ?? ('USER-' . $u->id_user),
                'id_user'=> $u->id_user,
                'name'   => $u->nama,
                'email'  => $u->email,
                'unit'   => $u->unit_kerja ?? '—',
                'phone'  => $u->no_telp ?? '—',
                'status' => 'Aktif',
            ];
        });

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
            'name'   => 'required|string|max:150',
            'email'  => 'required|email|max:150|unique:users,email',
            'unit'   => 'required|string|max:100',
            'phone'  => 'nullable|string|max:30',
        ], [
            'name.required'  => 'Nama lengkap sivitas wajib diisi.',
            'email.required' => 'Email resmi wajib diisi.',
            'email.email'    => 'Format email tidak valid.',
            'email.unique'   => 'Email ini sudah terdaftar di sistem.',
            'unit.required'  => 'Unit kerja wajib dipilih.',
        ]);

        User::create([
            'nip'        => $request->filled('id') ? $request->id : null,
            'nama'       => $request->name,
            'email'      => strtolower(trim($request->email)),
            'unit_kerja' => $request->unit,
            'no_telp'    => $request->phone,
            'kategori'   => $request->role,
            'role'       => 'user',
        ]);

        return redirect()->route('admin.akun.index', ['tab' => $request->role])
            ->with('success', 'Akun ' . ($request->role === 'dosen' ? 'Dosen' : 'Tendik') . ' berhasil ditambahkan ke database.');
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

        $file = $request->file('csv_file');
        $handle = fopen($file->getRealPath(), 'r');
        $header = fgetcsv($handle, 1000, ',');

        $count = 0;
        if ($header) {
            while (($row = fgetcsv($handle, 1000, ',')) !== false) {
                if (count($row) >= 3) {
                    $nip      = trim($row[0] ?? '');
                    $nama     = trim($row[1] ?? '');
                    $email    = strtolower(trim($row[2] ?? ''));
                    $unit     = trim($row[3] ?? 'Teknologi Rekayasa Perangkat Lunak');
                    $noTelp   = trim($row[4] ?? '');
                    $kategori = strtolower(trim($row[5] ?? 'dosen'));
                    if (!in_array($kategori, ['dosen', 'tendik'])) $kategori = 'dosen';

                    if ($nip && $nama && $email) {
                        User::updateOrCreate(
                            ['nip' => $nip],
                            [
                                'nama'       => $nama,
                                'email'      => $email,
                                'unit_kerja' => $unit,
                                'no_telp'    => $noTelp ?: '-',
                                'kategori'   => $kategori,
                                'role'       => 'user',
                            ]
                        );
                        $count++;
                    }
                }
            }
        }
        fclose($handle);

        return redirect()->route('admin.akun.index')
            ->with('success', 'Berhasil mengimpor ' . $count . ' data akun sivitas dari file CSV ke database.');
    }

    public function destroy($id)
    {
        $numericId = str_starts_with((string) $id, 'USER-') ? substr((string) $id, 5) : $id;
        $user = User::where('nip', $id)
            ->orWhere('id_user', $numericId)
            ->first();

        if ($user) {
            \App\Models\Partisipasi::where('id_user', $user->id_user)->delete();
            $user->delete();
        }

        return redirect()->route('admin.akun.index')
            ->with('success', 'Akun sivitas dan data partisipasinya berhasil dihapus dari sistem.');
    }
}
