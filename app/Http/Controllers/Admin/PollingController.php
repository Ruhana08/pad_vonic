<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DesainKaos;
use App\Models\GaleriDesainKaos;
use App\Models\GaleriPaketWisata;
use App\Models\PaketWisata;
use App\Models\Partisipasi;
use App\Models\PeriodePolling;
use App\Models\UkuranKaos;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class PollingController extends Controller
{
    public function create(Request $request)
    {
        $wisataLibrary = PaketWisata::with('galeri')
            ->where(function ($q) {
                $q->where('hidden_from_library', false)->orWhereNull('hidden_from_library');
            })
            ->orderBy('id_paket', 'desc')
            ->get()
            ->map(function ($p) {
                $benefits = [];
                if (!empty($p->benefit)) {
                    $benefits = array_values(array_filter(array_map('trim', explode(',', $p->benefit))));
                }
                return [
                    'id'          => $p->id_paket,
                    'name'        => $p->nama_destinasi,
                    'label'       => $p->kategori ?? 'Wisata Lainnya',
                    'lokasi'      => $p->lokasi ?? '',
                    'provinsi'    => $p->provinsi ?? '',
                    'jarak'       => $p->jarak ?? 0,
                    'lama'        => $p->durasi_perjalanan ?? '',
                    'biaya'       => $p->harga ?? 0,
                    'rating'      => $p->rating ?? 4.8,
                    'dikunjungi'  => $p->total_kunjungan ?? 0,
                    'benefits'    => $benefits,
                    'description' => $p->lokasi ?? '',
                    'tags'        => [],
                ];
            })
            ->values();

        $draftId = null;
        $draftData = null;

        if ($request->filled('id')) {
            $draft = PeriodePolling::with(['paketWisata.galeri', 'desainKaos.galeri'])
                ->where('id_periode', $request->query('id'))
                ->first();

            if ($draft && strtolower($draft->status ?? '') === 'draft') {
                $draftId = $draft->id_periode;
                $cfg = is_array($draft->config) ? $draft->config : [];
                $uidCounter = 10;

                $wisataOptions = $draft->paketWisata->map(function ($w) use (&$uidCounter) {
                    $benefits = !empty($w->benefit) && $w->benefit !== '-'
                        ? array_values(array_filter(array_map('trim', explode(',', $w->benefit))))
                        : [];
                    $galeriPaths = $w->galeri->pluck('foto')->filter()->values()->all();
                    $galeriUrls  = $w->galeri->pluck('foto_url')->filter()->values()->all();
                    if (empty($galeriPaths) && $w->foto) {
                        $galeriPaths = [$w->foto];
                        $galeriUrls  = [$w->foto_url];
                    }
                    $photos = [
                        ['preview' => $galeriUrls[0] ?? ''],
                        ['preview' => $galeriUrls[1] ?? ''],
                        ['preview' => $galeriUrls[2] ?? ''],
                    ];
                    return [
                        'uid'             => ++$uidCounter,
                        'existing_id'     => $w->id_paket,
                        'existing_foto'   => $w->foto,
                        'existing_photos' => $galeriPaths,
                        'memoryId'        => null,
                        'name'            => $w->nama_destinasi ?? '',
                        'label'           => $w->kategori ?? '',
                        'isCustom'        => false,
                        'openDropdown'    => false,
                        'lokasi'          => $w->lokasi ?? '',
                        'provinsi'        => $w->provinsi ?? '',
                        'jarak'           => $w->jarak ?? '',
                        'lama'            => $w->durasi_perjalanan ?? '',
                        'biaya'           => $w->harga ?? '',
                        'rating'          => $w->rating ?? '',
                        'dikunjungi'      => $w->total_kunjungan ?? 0,
                        'benefits'        => $benefits,
                        'benefitInput'    => '',
                        'description'     => $w->lokasi ?? '',
                        'tags'            => [],
                        'tagInput'        => '',
                        'photos'          => $photos,
                    ];
                })->values()->all();

                if (empty($wisataOptions)) {
                    $wisataOptions = [[
                        'uid' => ++$uidCounter, 'memoryId' => null, 'name' => '', 'label' => '',
                        'isCustom' => false, 'openDropdown' => false, 'lokasi' => '', 'provinsi' => '',
                        'jarak' => '', 'lama' => '', 'biaya' => '', 'rating' => '', 'dikunjungi' => 0,
                        'benefits' => [], 'benefitInput' => '', 'description' => '', 'tags' => [],
                        'tagInput' => '', 'photos' => [['preview' => ''], ['preview' => ''], ['preview' => '']],
                    ]];
                }

                $kaosOptions = $draft->desainKaos->map(function ($k) use (&$uidCounter) {
                    $galeriPaths = $k->galeri->pluck('foto')->filter()->values()->all();
                    $galeriUrls  = $k->galeri->pluck('foto_url')->filter()->values()->all();
                    if (empty($galeriPaths) && $k->foto) {
                        $galeriPaths = [$k->foto];
                        $galeriUrls  = [$k->foto_url];
                    }
                    return [
                        'uid'             => ++$uidCounter,
                        'existing_id'     => $k->id_kaos,
                        'existing_foto'   => $k->foto,
                        'existing_photos' => $galeriPaths,
                        'name'            => $k->nama_desain ?? '',
                        'photos'          => [
                            ['preview' => $galeriUrls[0] ?? ''],
                            ['preview' => $galeriUrls[1] ?? ''],
                            ['preview' => $galeriUrls[2] ?? ''],
                        ],
                    ];
                })->values()->all();

                if (empty($kaosOptions)) {
                    $kaosOptions = [[
                        'uid' => ++$uidCounter, 'name' => '',
                        'photos' => [['preview' => ''], ['preview' => ''], ['preview' => '']],
                    ]];
                }

                $defaultChart = [
                    'XS' => ['chest' => '', 'length' => ''], 'S' => ['chest' => '', 'length' => ''],
                    'M' => ['chest' => '', 'length' => ''], 'L' => ['chest' => '', 'length' => ''],
                    'XL' => ['chest' => '', 'length' => ''], 'XXL' => ['chest' => '', 'length' => ''],
                    '3XL' => ['chest' => '', 'length' => ''],
                ];

                $chartImgPath = $cfg['kaos']['chart_image'] ?? '';
                $chartImgPreview = $chartImgPath
                    ? (str_starts_with($chartImgPath, 'http') || str_starts_with($chartImgPath, '/') ? $chartImgPath : url('storage/' . $chartImgPath))
                    : '';

                $draftData = [
                    'identity' => [
                        'title'       => $draft->nama_periode ?? '',
                        'description' => $cfg['description'] ?? '',
                        'start'       => [
                            'time' => $cfg['start_time'] ?? '08:00',
                            'date' => $draft->tanggal_mulai ? date('Y-m-d', strtotime($draft->tanggal_mulai)) : '',
                        ],
                        'end'         => [
                            'time' => $cfg['end_time'] ?? '18:00',
                            'date' => $draft->tanggal_selesai ? date('Y-m-d', strtotime($draft->tanggal_selesai)) : '',
                        ],
                    ],
                    'peserta' => [
                        'question'    => $cfg['peserta']['question'] ?? '',
                        'description' => $cfg['peserta']['description'] ?? '',
                        'options'     => !empty($cfg['peserta']['options']) ? $cfg['peserta']['options'] : [
                            ['uid' => 1, 'label' => ''],
                        ],
                    ],
                    'wisata' => [
                        'question'    => $cfg['wisata']['question'] ?? '',
                        'description' => $cfg['wisata']['description'] ?? '',
                        'options'     => $wisataOptions,
                    ],
                    'kaos' => [
                        'question'               => $cfg['kaos']['question'] ?? '',
                        'description'            => $cfg['kaos']['description'] ?? '',
                        'chart_mode'             => $cfg['kaos']['chart_mode'] ?? 'manual',
                        'chart_image'            => $chartImgPath,
                        'chart_image_preview'    => $chartImgPreview,
                        'allow_custom_user_size' => (bool) ($cfg['kaos']['allow_custom_user_size'] ?? false),
                        'sizes'                  => $cfg['kaos']['sizes'] ?? [],
                        'chart'                  => array_merge($defaultChart, $cfg['kaos']['chart'] ?? []),
                        'options'                => $kaosOptions,
                    ],
                ];
            }
        }

        return view('admin.polling.create', compact('wisataLibrary', 'draftId', 'draftData'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'payload'  => ['required', 'json'],
            'action'   => ['required', 'in:draft,publish'],
            'draft_id' => ['nullable', 'integer'],
        ]);

        $publish = $request->action === 'publish';
        $data = json_decode($request->payload, true);

        if (!is_array($data)) {
            return back()->with('error', 'Data formulir tidak valid. Silakan coba lagi.');
        }

        // Bersihkan opsi kosong agar tidak memicu validasi palsu
        if (isset($data['peserta']['options']) && is_array($data['peserta']['options'])) {
            $data['peserta']['options'] = array_values(array_filter(
                $data['peserta']['options'],
                fn ($o) => trim((string) ($o['label'] ?? '')) !== ''
            ));
        }
        if (isset($data['wisata']['options']) && is_array($data['wisata']['options'])) {
            $data['wisata']['options'] = array_values(array_filter(
                $data['wisata']['options'],
                fn ($o) => trim((string) ($o['name'] ?? '')) !== ''
            ));
        }
        if (isset($data['kaos']['options']) && is_array($data['kaos']['options'])) {
            $data['kaos']['options'] = array_values(array_filter(
                $data['kaos']['options'],
                fn ($o) => trim((string) ($o['name'] ?? '')) !== ''
            ));
        }

        $rules = [
            'identity.title'      => 'required|string|max:200',
            'identity.start.date' => 'nullable|date',
            'identity.end.date'   => 'nullable|date|after_or_equal:identity.start.date',
        ];

        $messages = [
            'identity.title.required'         => 'Judul kegiatan voting wajib diisi.',
            'identity.end.date.after_or_equal' => 'Tanggal selesai tidak boleh lebih awal dari tanggal mulai.',
            'identity.start.date.required'    => 'Tanggal mulai voting wajib diisi.',
            'identity.end.date.required'      => 'Tanggal selesai voting wajib diisi.',
            'peserta.options.min'             => 'Minimal 1 opsi rombongan peserta wajib diisi.',
            'wisata.options.min'              => 'Minimal 1 opsi destinasi wisata wajib diisi.',
            'kaos.options.min'                => 'Minimal 1 opsi desain kaos wajib diisi.',
        ];

        if ($publish) {
            $rules = array_merge($rules, [
                'identity.start.date'      => 'required|date',
                'identity.end.date'        => 'required|date|after_or_equal:identity.start.date',
                'peserta.options'          => 'required|array|min:1',
                'wisata.options'           => 'required|array|min:1',
                'wisata.options.*.name'    => 'required|string|max:150',
                'kaos.options'             => 'required|array|min:1',
                'kaos.options.*.name'      => 'required|string|max:150',
            ]);
        }

        $validator = Validator::make($data, $rules, $messages);
        if ($validator->fails()) {
            return back()->withErrors($validator)->with('error', $validator->errors()->first());
        }

        // Validasi gabungan tanggal & jam (waktu selesai tidak boleh lebih awal/sama dengan waktu mulai)
        $startDate = $data['identity']['start']['date'] ?? null;
        $endDate   = $data['identity']['end']['date'] ?? null;
        $startTime = $data['identity']['start']['time'] ?? '00:00';
        $endTime   = $data['identity']['end']['time'] ?? '23:59';

        if ($startDate && $endDate) {
            try {
                $startDt = Carbon::parse($startDate . ' ' . $startTime);
                $endDt   = Carbon::parse($endDate . ' ' . $endTime);
                if ($endDt->lte($startDt)) {
                    $errMsg = 'Waktu selesai polling tidak boleh lebih awal atau sama dengan waktu mulai polling.';
                    return back()->withErrors(['identity.end.date' => $errMsg])->with('error', $errMsg);
                }
            } catch (\Exception $e) {
                $errMsg = 'Format tanggal atau waktu polling tidak valid.';
                return back()->withErrors(['identity.end.date' => $errMsg])->with('error', $errMsg);
            }
        }

        $status = $publish ? 'aktif' : 'draft';

        // Jika dipublish sebagai 'aktif', nonaktifkan polling aktif sebelumnya agar hanya ada 1 polling aktif
        if ($publish) {
            PeriodePolling::where('status', 'aktif')->update(['status' => 'selesai']);
        }

        DB::beginTransaction();
        try {
            $chartImagePath = $data['kaos']['chart_image'] ?? null;
            if ($request->hasFile('kaos_chart_image')) {
                $chartImagePath = $request->file('kaos_chart_image')->store('polling/kaos', 'public');
            }

            $configuredSizes = array_values(array_filter(array_map(
                fn ($s) => trim((string) $s),
                $data['kaos']['sizes'] ?? []
            )));

            foreach ($configuredSizes as $kodeUkuran) {
                UkuranKaos::firstOrCreate(['kode_ukuran' => $kodeUkuran]);
            }

            $pollingConfig = [
                'description' => $data['identity']['description'] ?? '',
                'start_time'  => $startTime,
                'end_time'    => $endTime,
                'peserta'     => [
                    'question'    => $data['peserta']['question'] ?? '',
                    'description' => $data['peserta']['description'] ?? '',
                    'options'     => array_values(array_map(fn ($o) => [
                        'uid'   => $o['uid'] ?? null,
                        'label' => trim((string) ($o['label'] ?? '')),
                    ], $data['peserta']['options'] ?? [])),
                ],
                'wisata'      => [
                    'question'    => $data['wisata']['question'] ?? '',
                    'description' => $data['wisata']['description'] ?? '',
                ],
                'kaos'        => [
                    'question'               => $data['kaos']['question'] ?? '',
                    'description'            => $data['kaos']['description'] ?? '',
                    'chart_mode'             => $data['kaos']['chart_mode'] ?? 'manual',
                    'chart_image'            => $chartImagePath,
                    'allow_custom_user_size' => (bool) ($data['kaos']['allow_custom_user_size'] ?? false),
                    'sizes'                  => $configuredSizes,
                    'chart'                  => $data['kaos']['chart'] ?? [],
                ],
            ];

            $draftId = $request->input('draft_id');
            $periode = $draftId
                ? PeriodePolling::where('id_periode', $draftId)->where('status', 'draft')->first()
                : null;

            if ($periode) {
                $periode->update([
                    'nama_periode'    => $data['identity']['title'],
                    'tanggal_mulai'   => $startDate ?: now()->toDateString(),
                    'tanggal_selesai' => $endDate ?: now()->addDays(7)->toDateString(),
                    'status'          => $status,
                    'config'          => $pollingConfig,
                ]);
                // Lepas relasi lama untuk disinkronkan ulang dengan data terbaru
                $periode->paketWisata()->detach();
                $periode->desainKaos()->detach();
            } else {
                $periode = PeriodePolling::create([
                    'nama_periode'    => $data['identity']['title'],
                    'tanggal_mulai'   => $startDate ?: now()->toDateString(),
                    'tanggal_selesai' => $endDate ?: now()->addDays(7)->toDateString(),
                    'status'          => $status,
                    'mode_kelompok'   => 'dipisah',
                    'config'          => $pollingConfig,
                    'created_by'      => Auth::id(),
                ]);
            }

            // 2. Simpan Paket Wisata + semua foto galeri (maks 3) & attach ke pivot
            foreach ($data['wisata']['options'] ?? [] as $w) {
                $existingPhotos = is_array($w['existing_photos'] ?? null) ? $w['existing_photos'] : [];
                $fotoPaths = [];
                for ($f = 0; $f < 3; $f++) {
                    $key = 'wisata_image_' . ($w['uid'] ?? '') . '_' . $f;
                    if ($request->hasFile($key)) {
                        $fotoPaths[] = $request->file($key)->store('polling/wisata', 'public');
                    } elseif (!empty($existingPhotos[$f])) {
                        $fotoPaths[] = $existingPhotos[$f];
                    }
                }
                if (empty($fotoPaths) && !empty($w['existing_foto'])) {
                    $fotoPaths[] = $w['existing_foto'];
                }
                $fotoMain = count($fotoPaths) > 0 ? $fotoPaths[0] : null;

                $paket = PaketWisata::create([
                    'nama_destinasi'    => $w['name'] ?? 'Destinasi Wisata',
                    'kategori'          => !empty($w['label']) ? trim((string) $w['label']) : null,
                    'lokasi'            => $w['lokasi'] ?? 'Indonesia',
                    'provinsi'          => !empty($w['provinsi']) ? trim((string) $w['provinsi']) : null,
                    'jarak'             => (string) ($w['jarak'] ?? '100'),
                    'durasi_perjalanan' => (string) ($w['lama'] ?? '2'),
                    'benefit'           => is_array($w['benefits'] ?? null) ? implode(', ', $w['benefits']) : ($w['benefits'] ?? '-'),
                    'harga'             => (int) ($w['biaya'] ?? 300000),
                    'foto'              => $fotoMain,
                ]);

                foreach ($fotoPaths as $path) {
                    GaleriPaketWisata::create([
                        'id_paket' => $paket->id_paket,
                        'foto'     => $path,
                    ]);
                }

                $periode->paketWisata()->attach($paket->id_paket);
            }

            // 3. Simpan Desain Kaos + semua foto galeri (maks 3) & attach ke pivot
            foreach ($data['kaos']['options'] ?? [] as $i => $k) {
                $existingPhotos = is_array($k['existing_photos'] ?? null) ? $k['existing_photos'] : [];
                $fotoPaths = [];
                for ($f = 0; $f < 3; $f++) {
                    $key = 'kaos_image_' . ($k['uid'] ?? '') . '_' . $f;
                    if ($request->hasFile($key)) {
                        $fotoPaths[] = $request->file($key)->store('polling/kaos', 'public');
                    } elseif (!empty($existingPhotos[$f])) {
                        $fotoPaths[] = $existingPhotos[$f];
                    }
                }
                if (empty($fotoPaths) && $request->hasFile('kaos_image_' . ($k['uid'] ?? ''))) {
                    $fotoPaths[] = $request->file('kaos_image_' . $k['uid'])->store('polling/kaos', 'public');
                }
                if (empty($fotoPaths) && !empty($k['existing_foto'])) {
                    $fotoPaths[] = $k['existing_foto'];
                }
                $fotoMain = count($fotoPaths) > 0 ? $fotoPaths[0] : null;

                $kaos = DesainKaos::create([
                    'nama_desain' => $k['name'] ?? 'Desain Kaos',
                    'foto'        => $fotoMain,
                ]);

                foreach ($fotoPaths as $path) {
                    GaleriDesainKaos::create([
                        'id_kaos' => $kaos->id_kaos,
                        'foto'    => $path,
                    ]);
                }

                $periode->desainKaos()->attach($kaos->id_kaos);
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal menyimpan polling: ' . $e->getMessage());
        }

        if ($publish) {
            $users = \App\Models\User::where('role', 'user')->get();
            foreach ($users as $u) {
                \App\Models\Notifikasi::kirim(
                    $u->id_user,
                    'Polling Baru Tersedia',
                    'Polling "' . $periode->nama_periode . '" telah dibuka. Segera berikan suaramu!',
                    '/voting?periode=' . $periode->id_periode
                );
            }

            return redirect()->route('admin.polling.aktif')
                ->with('success', 'Polling baru berhasil diposting dan langsung aktif untuk seluruh user!');
        }

        return redirect()->route('admin.polling.arsip')
            ->with('success', 'Draft polling berhasil disimpan!');
    }

    public function destroyPaketWisata($id)
    {
        $paket = PaketWisata::find($id);
        if (! $paket) {
            return response()->json([
                'success' => false,
                'message' => 'Paket wisata tidak ditemukan.',
            ], 404);
        }

        if (Partisipasi::where('id_paket', $id)->exists() || $paket->periodePolling()->exists()) {
            $paket->update(['hidden_from_library' => true]);

            return response()->json([
                'success' => true,
                'message' => 'Riwayat paket wisata berhasil dihapus dari daftar pilihan.',
            ]);
        }

        $paket->galeri()->delete();
        $paket->delete();

        return response()->json([
            'success' => true,
            'message' => 'Riwayat paket wisata berhasil dihapus.',
        ]);
    }
}
