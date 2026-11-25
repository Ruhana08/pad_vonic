<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DesainKaos;
use App\Models\PaketWisata;
use App\Models\PeriodePolling;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class PollingController extends Controller
{
    public function create()
    {
        $wisataLibrary = PaketWisata::with('galeri')
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

        return view('admin.polling.create', compact('wisataLibrary'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'payload' => ['required', 'json'],
            'action'  => ['required', 'in:draft,publish'],
        ]);

        $publish = $request->action === 'publish';
        $data = json_decode($request->payload, true);

        $rules = [
            'identity.title'      => 'required|string|max:200',
            'identity.start.date' => 'nullable|date',
            'identity.end.date'   => 'nullable|date|after_or_equal:identity.start.date',
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

        $validator = Validator::make($data ?? [], $rules);
        if ($validator->fails()) {
            return back()->withErrors($validator);
        }

        $status = $publish ? 'aktif' : 'draft';

        // Jika dipublish sebagai 'aktif', nonaktifkan polling aktif sebelumnya agar hanya ada 1 polling aktif
        if ($publish) {
            PeriodePolling::where('status', 'aktif')->update(['status' => 'selesai']);
        }

        DB::beginTransaction();
        try {
            // 1. Simpan Periode Polling
            $periode = PeriodePolling::create([
                'nama_periode'    => $data['identity']['title'],
                'tanggal_mulai'   => $data['identity']['start']['date'] ?? now()->toDateString(),
                'tanggal_selesai' => $data['identity']['end']['date'] ?? now()->addDays(7)->toDateString(),
                'status'          => $status,
                'mode_kelompok'   => 'dipisah',
                'created_by'      => 1,
            ]);

            // 2. Simpan Paket Wisata & attach ke pivot
            foreach ($data['wisata']['options'] ?? [] as $w) {
                $fotoPaths = [];
                for ($f = 0; $f < 3; $f++) {
                    $key = 'wisata_image_' . ($w['uid'] ?? '') . '_' . $f;
                    if ($request->hasFile($key)) {
                        $fotoPaths[] = $request->file($key)->store('polling/wisata', 'public');
                    }
                }
                $fotoMain = count($fotoPaths) > 0 ? $fotoPaths[0] : null;

                $paket = PaketWisata::create([
                    'nama_destinasi'    => $w['name'] ?? 'Destinasi Wisata',
                    'lokasi'            => $w['lokasi'] ?? 'Indonesia',
                    'jarak'             => (string) ($w['jarak'] ?? '100'),
                    'durasi_perjalanan' => (string) ($w['lama'] ?? '2'),
                    'benefit'           => is_array($w['benefits'] ?? null) ? implode(', ', $w['benefits']) : ($w['benefits'] ?? '-'),
                    'harga'             => (int) ($w['biaya'] ?? 300000),
                    'foto'              => $fotoMain,
                    'kategori'          => $w['kategori'] ?? 'alam',
                    'provinsi'          => $w['provinsi'] ?? 'DIY',
                    'rating'            => (float) ($w['rating'] ?? 4.5),
                    'total_kunjungan'   => (int) ($w['dikunjungi'] ?? 100),
                ]);

                $periode->paketWisata()->attach($paket->id_paket);
            }

            // 3. Simpan Desain Kaos & attach ke pivot
            foreach ($data['kaos']['options'] ?? [] as $i => $k) {
                $fotoPaths = [];
                for ($f = 0; $f < 3; $f++) {
                    $key = 'kaos_image_' . ($k['uid'] ?? '') . '_' . $f;
                    if ($request->hasFile($key)) {
                        $fotoPaths[] = $request->file($key)->store('polling/kaos', 'public');
                    }
                }
                // Fallback jika dikirim via key single lama kaos_image_{uid}
                if (empty($fotoPaths) && $request->hasFile('kaos_image_' . ($k['uid'] ?? ''))) {
                    $fotoPaths[] = $request->file('kaos_image_' . $k['uid'])->store('polling/kaos', 'public');
                }
                $fotoMain = count($fotoPaths) > 0 ? $fotoPaths[0] : null;

                $kaos = DesainKaos::create([
                    'nama_desain' => $k['name'] ?? 'Desain Kaos',
                    'foto'        => $fotoMain,
                ]);

                $periode->desainKaos()->attach($kaos->id_kaos);
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal menyimpan polling: ' . $e->getMessage());
        }

        if ($publish) {
            return redirect()->route('admin.polling.aktif')
                ->with('success', 'Polling baru berhasil diposting dan langsung aktif untuk seluruh user!');
        }

        return redirect()->route('admin.polling.arsip')
            ->with('success', 'Draft polling berhasil disimpan!');
    }
}
