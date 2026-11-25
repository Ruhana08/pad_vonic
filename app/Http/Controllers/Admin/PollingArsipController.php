<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PeriodePolling;
use App\Models\Partisipasi;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

class PollingArsipController extends Controller
{
    private function paginate($items, Request $request, int $perPage, string $pageName): LengthAwarePaginator
    {
        $page = max(1, (int) $request->query($pageName, 1));

        return new LengthAwarePaginator(
            $items->forPage($page, $perPage)->values(),
            $items->count(),
            $perPage,
            $page,
            ['path' => $request->url(), 'query' => $request->query(), 'pageName' => $pageName]
        );
    }

    public function index(Request $request)
    {
        $isEmpty     = $request->boolean('empty');
        $noActive    = $request->query('active') === '0';
        $stateFilter = $request->query('state');
        $perPage     = (int) $request->query('per_page', 10);
        if ($perPage <= 0) $perPage = 10;

        if ($isEmpty) {
            $polls   = collect();
            $history = collect();
        } else {
            // Ambil data langsung dari basis data vonicpad
            $query = PeriodePolling::with(['paketWisata.galeri', 'desainKaos.galeri', 'partisipasi']);

            if ($noActive) {
                $query->where('status', '!=', 'aktif');
            }

            if ($stateFilter) {
                $query->where('status', strtolower($stateFilter));
            }

            $dbPolls = $query->orderBy('tanggal_mulai', 'desc')->get();

            $polls = $dbPolls->map(function ($p) {
                $year = $p->tanggal_mulai ? date('Y', strtotime($p->tanggal_mulai)) : date('Y');
                $startFormatted = $p->tanggal_mulai ? date('d/m/y', strtotime($p->tanggal_mulai)) : '—';
                $endFormatted = $p->tanggal_selesai ? date('d/m/y', strtotime($p->tanggal_selesai)) : '—';

                $statusLabel = match (strtolower($p->status ?? 'draft')) {
                    'aktif'   => 'Aktif',
                    'selesai' => 'Selesai',
                    default   => 'Draft',
                };

                return [
                    'id'          => 'POLL-' . str_pad($p->id_periode, 2, '0', STR_PAD_LEFT),
                    'id_periode'  => $p->id_periode,
                    'name'        => $p->nama_periode,
                    'year'        => $year,
                    'start'       => $startFormatted,
                    'end'         => $endFormatted,
                    'status'      => $statusLabel,
                    'model'       => $p,
                ];
            });

            // Urutkan prioritas status: Aktif -> Draft -> Selesai
            $statusOrder = ['Aktif' => 1, 'Draft' => 2, 'Selesai' => 3];
            $polls = $polls->sortBy(fn($p) => $statusOrder[$p['status']] ?? 99)->values();

            // Riwayat arsip diambil dari polling yang berstatus 'selesai'
            $completedPolls = $dbPolls->where('status', 'selesai')->values();

            $history = $completedPolls->map(function ($p) {
                // Cari paket wisata pemenang berdasarkan vote terbanyak atau opsi pertama
                $paketCounts = $p->partisipasi->groupBy('id_paket')->map->count();
                $topPaketId  = $paketCounts->sortDesc()->keys()->first();
                $wisata      = $p->paketWisata->firstWhere('id_paket', $topPaketId) ?? $p->paketWisata->first();

                // Cari kaos pemenang berdasarkan vote terbanyak atau opsi pertama
                $kaosCounts  = $p->partisipasi->groupBy('id_kaos')->map->count();
                $topKaosId   = $kaosCounts->sortDesc()->keys()->first();
                $kaos        = $p->desainKaos->firstWhere('id_kaos', $topKaosId) ?? $p->desainKaos->first();

                $start = $p->tanggal_mulai ? date('d/m/y', strtotime($p->tanggal_mulai)) : '—';
                $end   = $p->tanggal_selesai ? date('d/m/y', strtotime($p->tanggal_selesai)) : '—';

                $pesertaCounts = $p->partisipasi->groupBy('pilihan_kelompok')->map->count();
                $topPesertaKey = $pesertaCounts->sortDesc()->keys()->first();
                $pesertaKategori = match ($topPesertaKey) {
                    'dipisah'  => 'Dosen dan tendik dipisah',
                    'digabung' => 'Dosen dan tendik digabung',
                    default    => 'Belum ada suara',
                };

                $fotoWisata = $wisata && $wisata->foto_url ? $wisata->foto_url : 'https://images.unsplash.com/photo-1507525428034-b723cf961d3e?auto=format&fit=crop&w=800&q=80';
                $fotoKaos   = $kaos && $kaos->foto_url ? $kaos->foto_url : 'https://images.unsplash.com/photo-1521572267360-ee0c2909d518?auto=format&fit=crop&w=600&q=80';

                $kaosGaleri = $kaos && $kaos->galeri
                    ? $kaos->galeri->pluck('foto_url')->filter()->values()->all()
                    : [];
                $wisataGaleri = $wisata && $wisata->galeri
                    ? $wisata->galeri->pluck('foto_url')->filter()->values()->all()
                    : [];

                $durasiStr = $wisata && $wisata->durasi_perjalanan
                    ? (str_contains(strtolower((string) $wisata->durasi_perjalanan), 'jam') ? $wisata->durasi_perjalanan : $wisata->durasi_perjalanan . ' Jam')
                    : '—';

                $jarakStr = $wisata && $wisata->jarak
                    ? (str_contains(strtolower((string) $wisata->jarak), 'km') ? $wisata->jarak : $wisata->jarak . ' Km')
                    : '—';

                return [
                    'id'           => $p->id_periode,
                    'title'        => $p->nama_periode,
                    'description'  => 'Pemilihan paket wisata dan desain kaos tahunan civitas DTEDI periode ' . date('Y', strtotime($p->tanggal_mulai ?? now())) . '.',
                    'start'        => $start,
                    'end'          => $end,
                    'full_duration'=> $start . ' - ' . $end,
                    'participants' => $p->partisipasi->count(),
                    'peserta_type' => $pesertaKategori,
                    'public'       => (bool) ($p->is_public ?? true),
                    'kaos'         => [
                        'title'    => $kaos->nama_desain ?? 'Kaos Resmi DTEDI',
                        'image'    => $fotoKaos,
                        'gallery'  => $kaosGaleri,
                    ],
                    'wisata'       => [
                        'title'       => $wisata->nama_destinasi ?? 'Destinasi Wisata Pilihan',
                        'kategori'    => $wisata->kategori ?? 'Wisata',
                        'provinsi'    => $wisata->provinsi ?? null,
                        'rating'      => $wisata->rating ?? '4.8',
                        'image'       => $fotoWisata,
                        'gallery'     => $wisataGaleri,
                        'lokasi'      => $wisata->lokasi ?? ($wisata->provinsi ?? '—'),
                        'jarak'       => $jarakStr,
                        'durasi'      => $durasiStr,
                        'dikunjungi'  => $wisata->total_kunjungan ?? null,
                        'benefits'    => $wisata && $wisata->benefit ? (is_array($wisata->benefit) ? $wisata->benefit : array_values(array_filter(array_map('trim', explode(',', $wisata->benefit))))) : [],
                        'biaya'       => ($wisata && $wisata->harga) ? ('Rp ' . number_format($wisata->harga, 0, ',', '.')) : '—',
                    ],
                ];
            });
        }

        // Paginasi tabel status polling
        $paginatedPolls = $this->paginate($polls, $request, $perPage, 'page_polls');

        // Kumpulkan ID riwayat yang memiliki akses publik
        $publicIds = $history->where('public', true)->pluck('id')->values()->all();

        return view('admin.polling.arsip', [
            'polls'     => $paginatedPolls,
            'history'   => $history->values()->all(),
            'publicIds' => $publicIds,
        ]);
    }

    /**
     * Publikasikan Polling Draft menjadi Aktif
     */
    public function publish($id)
    {
        $hasActive = PeriodePolling::where('status', 'aktif')->where('id_periode', '!=', $id)->exists();
        if ($hasActive) {
            return back()->with('error', 'Gagal mempublikasikan: Sudah ada polling lain yang sedang berjalan aktif.');
        }

        $poll = PeriodePolling::findOrFail($id);
        $poll->status = 'aktif';
        $poll->save();

        return back()->with('success', "Polling {$poll->nama_periode} berhasil dipublikasikan dan statusnya kini Aktif!");
    }

    /**
     * Hentikan polling aktif (ubah ke selesai) atau hapus permanen jika draft
     */
    public function destroy($id)
    {
        $poll = PeriodePolling::find($id);

        if (! $poll) {
            return redirect()->route('admin.polling.arsip')
                ->with('info', 'Polling tersebut sudah tidak tersedia atau telah dihapus.');
        }

        if ($poll->status === 'aktif') {
            $poll->status = 'selesai';
            $poll->save();
            return redirect()->route('admin.polling.arsip')
                ->with('success', "Voting {$poll->nama_periode} telah dihentikan dan statusnya kini berubah menjadi Selesai.");
        }

        // Hapus relasi pivot, detail ukuran, partisipasi, lalu polling
        $partisipasiIds = $poll->partisipasi()->pluck('id_partisipasi');
        if ($partisipasiIds->isNotEmpty()) {
            \App\Models\DetailUkuranVote::whereIn('id_partisipasi', $partisipasiIds)->delete();
        }
        $poll->paketWisata()->detach();
        $poll->desainKaos()->detach();
        $poll->partisipasi()->delete();
        $poll->delete();

        return redirect()->route('admin.polling.arsip')
            ->with('success', "Polling {$poll->nama_periode} berhasil dihapus dari sistem.");
    }

    /**
     * Simpan perubahan akses publik/privat riwayat polling
     */
    public function updateAccess(Request $request)
    {
        $publicIds = $request->input('public_ids', []);
        if (!is_array($publicIds)) {
            $publicIds = [];
        }

        // Update semua periode_polling berstatus selesai
        PeriodePolling::where('status', 'selesai')->update(['is_public' => 0]);
        if (!empty($publicIds)) {
            PeriodePolling::whereIn('id_periode', $publicIds)->update(['is_public' => 1]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Hak akses publik/privat riwayat berhasil diperbarui.'
        ]);
    }
}
