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

        $existingEmails = User::pluck('email')
            ->map(fn ($e) => strtolower(trim((string) $e)))
            ->filter()
            ->values();

        return view('admin.akun.index', [
            'tab'            => $tab,
            'units'          => $units,
            'accounts'       => $accounts,
            'existingEmails' => $existingEmails,
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'role'   => 'required|in:dosen,tendik',
            'name'   => 'required|string|max:150',
            'email'  => 'required|email|max:150|unique:users,email',
            'unit'   => 'required|string|max:100',
            'phone'  => ['nullable', 'string', 'max:20', 'regex:/^[0-9+][0-9\s\-]{6,19}$/'],
        ], [
            'role.required'  => 'Pekerjaan (Dosen / Tendik) wajib dipilih.',
            'name.required'  => 'Nama lengkap sivitas wajib diisi.',
            'email.required' => 'Email resmi wajib diisi.',
            'email.email'    => 'Format email tidak valid.',
            'email.unique'   => 'Email ini sudah terdaftar di sistem.',
            'unit.required'  => 'Unit kerja wajib dipilih.',
            'phone.regex'    => 'Nomor telepon hanya boleh berisi angka (contoh: 081234567890 atau +6281234567890).',
        ]);

        User::create([
            'nip'        => null,
            'nama'       => trim($request->name),
            'email'      => strtolower(trim($request->email)),
            'pekerjaan'  => $request->role === 'dosen' ? 'Dosen' : 'Tenaga Kependidikan',
            'unit_kerja' => $request->unit,
            'no_telp'    => $request->phone ? trim($request->phone) : null,
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

        $path = $request->file('csv_file')->getRealPath();
        $rawContent = file_get_contents($path);
        // Hapus UTF-8 BOM jika ada
        $rawContent = preg_replace('/^\xEF\xBB\xBF/', '', $rawContent);
        $lines = preg_split('/\r\n|\r|\n/', $rawContent);

        // Deteksi delimiter otomatis (, atau ; atau tab)
        $sample = implode("\n", array_slice(array_filter($lines, fn ($l) => trim($l) !== ''), 0, 5));
        $delimiter = ',';
        if (substr_count($sample, ';') > substr_count($sample, ',')) {
            $delimiter = ';';
        } elseif (substr_count($sample, "\t") > substr_count($sample, ',')) {
            $delimiter = "\t";
        }

        $existingEmails = User::pluck('email')
            ->map(fn ($e) => strtolower(trim((string) $e)))
            ->filter()
            ->flip()
            ->all();

        $imported = 0;
        $skippedDuplicate = 0;
        $skippedInvalid = 0;
        $colMap = null;

        foreach ($lines as $line) {
            if (trim($line) === '') continue;
            $row = array_map(fn ($c) => trim((string) $c), str_getcsv($line, $delimiter));

            // Deteksi baris header (judul kolom) secara otomatis
            $joinedLower = strtolower(implode(' ', $row));
            if ($colMap === null && (str_contains($joinedLower, 'email') && (str_contains($joinedLower, 'nama') || str_contains($joinedLower, 'name')))) {
                $colMap = [];
                foreach ($row as $idx => $h) {
                    $hl = strtolower(trim($h));
                    if (str_contains($hl, 'nip') || $hl === 'id') $colMap['nip'] = $idx;
                    elseif (str_contains($hl, 'nama') || $hl === 'name') $colMap['nama'] = $idx;
                    elseif (str_contains($hl, 'email') || str_contains($hl, 'surel')) $colMap['email'] = $idx;
                    elseif (str_contains($hl, 'unit') || str_contains($hl, 'prodi') || str_contains($hl, 'departemen')) $colMap['unit'] = $idx;
                    elseif (str_contains($hl, 'telp') || str_contains($hl, 'hp') || str_contains($hl, 'phone') || str_contains($hl, 'wa')) $colMap['phone'] = $idx;
                    elseif (str_contains($hl, 'kategori') || str_contains($hl, 'pekerjaan') || str_contains($hl, 'role') || str_contains($hl, 'jabatan')) $colMap['kategori'] = $idx;
                }
                continue;
            }

            // Skip baris judul dokumen / baris yang tidak memiliki email valid
            $emailIdx = $colMap['email'] ?? null;
            if ($emailIdx === null) {
                // Cari kolom yang berisi '@'
                foreach ($row as $idx => $cell) {
                    if (filter_var($cell, FILTER_VALIDATE_EMAIL)) {
                        $emailIdx = $idx;
                        break;
                    }
                }
            }

            $email = $emailIdx !== null ? strtolower(trim($row[$emailIdx] ?? '')) : '';
            if (! $email || ! filter_var($email, FILTER_VALIDATE_EMAIL)) {
                // Jika baris tidak punya email valid (misal baris judul laporan di atas header), lewati
                if (count(array_filter($row)) <= 2 || str_contains($joinedLower, 'daftar') || str_contains($joinedLower, 'data ')) {
                    continue;
                }
                $skippedInvalid++;
                continue;
            }

            // Ambil kolom lain berdasarkan mapping header atau posisi default
            if ($colMap !== null) {
                $nip      = isset($colMap['nip']) ? trim($row[$colMap['nip']] ?? '') : '';
                $nama     = isset($colMap['nama']) ? trim($row[$colMap['nama']] ?? '') : '';
                $unit     = isset($colMap['unit']) ? trim($row[$colMap['unit']] ?? '') : 'Teknologi Rekayasa Perangkat Lunak';
                $noTelp   = isset($colMap['phone']) ? trim($row[$colMap['phone']] ?? '') : '';
                $katRaw   = isset($colMap['kategori']) ? strtolower(trim($row[$colMap['kategori']] ?? 'dosen')) : 'dosen';
            } else {
                // Tanpa header: dukung [NIP, Nama, Email, ...] atau [Nama, Email, ...]
                if ($emailIdx === 2) {
                    $nip    = trim($row[0] ?? '');
                    $nama   = trim($row[1] ?? '');
                    $unit   = trim($row[3] ?? 'Teknologi Rekayasa Perangkat Lunak');
                    $noTelp = trim($row[4] ?? '');
                    $katRaw = strtolower(trim($row[5] ?? 'dosen'));
                } else {
                    $nip    = '';
                    $nama   = trim($row[0] ?? '');
                    $unit   = trim($row[2] ?? 'Teknologi Rekayasa Perangkat Lunak');
                    $noTelp = trim($row[3] ?? '');
                    $katRaw = strtolower(trim($row[4] ?? 'dosen'));
                }
            }

            if (! $nama) {
                $skippedInvalid++;
                continue;
            }

            if (isset($existingEmails[$email])) {
                $skippedDuplicate++;
                continue;
            }

            // Bersihkan no telp agar hanya angka / + / - / spasi
            $noTelpClean = preg_replace('/[^0-9+\-\s]/', '', $noTelp);
            $kategori = (str_contains($katRaw, 'tendik') || str_contains($katRaw, 'kependidikan')) ? 'tendik' : 'dosen';

            User::create([
                'nip'        => $nip !== '' ? $nip : null,
                'nama'       => $nama,
                'email'      => $email,
                'pekerjaan'  => $kategori === 'dosen' ? 'Dosen' : 'Tenaga Kependidikan',
                'unit_kerja' => $unit ?: 'Teknologi Rekayasa Perangkat Lunak',
                'no_telp'    => $noTelpClean !== '' ? $noTelpClean : null,
                'kategori'   => $kategori,
                'role'       => 'user',
            ]);

            $existingEmails[$email] = true;
            $imported++;
        }

        $msg = "Berhasil mengimpor {$imported} akun sivitas baru dari CSV.";
        if ($skippedDuplicate > 0) {
            $msg .= " ({$skippedDuplicate} baris duplikat dilewati)";
        }
        if ($skippedInvalid > 0) {
            $msg .= " ({$skippedInvalid} baris tidak valid dilewati)";
        }

        return redirect()->route('admin.akun.index')
            ->with($imported > 0 ? 'success' : 'info', $msg);
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
