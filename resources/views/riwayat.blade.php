@extends('layouts.user')

@section('title', 'Riwayat Pemilihan')

@section('content')
<div class="space-y-6"
     x-data="riwayatPageApp()"
     x-init="init()">

    <!-- Header Section -->
    <div class="flex items-center justify-between">
        <h2 class="text-xl font-bold tracking-tight text-primary-1">
            Riwayat Pemilihan Destinasi Wisata Piknik DTEDI
        </h2>
    </div>

    <!-- Loading State -->
    <div x-show="loading" class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
        <template x-for="i in 3" :key="i">
            <div class="animate-pulse rounded-2xl border border-grey-1 bg-white p-6 shadow-xs">
                <div class="h-5 w-40 rounded bg-gray-200"></div>
                <div class="mt-2 h-3 w-full rounded bg-gray-200"></div>
                <div class="mt-4 space-y-2">
                    <div class="h-10 rounded-lg bg-gray-200"></div>
                    <div class="h-10 rounded-lg bg-gray-200"></div>
                </div>
                <div class="mt-6 h-10 rounded-xl bg-gray-200"></div>
            </div>
        </template>
    </div>

    <!-- 3-Column Card Grid -->
    <div x-show="!loading" class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
        <template x-for="poll in riwayatList" :key="poll.id_periode">
            <x-card variant="showcase" class="flex flex-col justify-between">
                <div>
                    <h3 class="text-base font-bold text-primary-1" x-text="poll.nama_periode"></h3>
                    <p class="mt-1 text-xs text-grey-deep">
                        Pemilihan destinasi wisata sivitas akademika DTEDI SV UGM.
                    </p>

                    <!-- Metadata Box -->
                    <div class="mt-4 space-y-2">
                        <!-- Periode -->
                        <x-card variant="info-box" class="flex items-center gap-3 px-3.5 py-2.5">
                            <x-solar-calendar-linear class="h-4 w-4 shrink-0 text-primary-1" />
                            <div>
                                <span class="block text-[10px] text-grey-2">Periode</span>
                                <strong class="text-xs font-semibold text-black"
                                        x-text="formatDateRange(poll.tanggal_mulai, poll.tanggal_selesai)"></strong>
                            </div>
                        </x-card>

                        <!-- Status / Partisipasi -->
                        <x-card variant="info-box" class="flex items-center gap-3 px-3.5 py-2.5">
                            <x-solar-check-circle-linear class="h-4 w-4 shrink-0 text-primary-1" />
                            <div>
                                <span class="block text-[10px] text-grey-2">Status Periode</span>
                                <strong class="text-xs font-semibold text-black capitalize" x-text="poll.status"></strong>
                            </div>
                        </x-card>
                    </div>
                </div>

                <!-- Tombol Lihat Hasil -->
                <div class="mt-6">
                    <button type="button"
                            @click="lihatHasil(poll)"
                            class="inline-flex w-full items-center justify-center rounded-xl bg-primary-1 py-2.5 text-xs font-semibold text-white shadow-sm transition-all hover:bg-primary-4 active:bg-primary-4 cursor-pointer">
                        Lihat Hasil
                    </button>
                </div>
            </x-card>
        </template>

        <!-- Empty state -->
        <template x-if="!loading && riwayatList.length === 0">
            <x-card variant="empty" class="col-span-3 p-12">
                <x-solar-folder-with-files-linear class="mx-auto mb-3 h-10 w-10 text-grey-2" />
                <p class="text-sm text-grey-dark">Belum ada riwayat periode pemilihan</p>
            </x-card>
        </template>
    </div>

    {{-- Modal Quick-View Hasil Voting Pemenang (Identik Admin Arsip) --}}
    <x-modal-hasil-voting show="showResultModal" close="showResultModal = false" item="activeHistoryItem" />

    {{-- Lightbox Fullscreen Preview Foto saat diklik --}}
    <x-modal show="Boolean(previewImage)" close="previewImage = null" maxWidth="lg">
        <div class="relative p-2 bg-black/90 rounded-2xl flex flex-col items-center">
            <button type="button"
                    @click="previewImage = null"
                    class="absolute top-4 right-4 z-10 flex h-8 w-8 items-center justify-center rounded-full bg-white/20 text-white hover:bg-white/40 transition-colors cursor-pointer">
                <x-solar-close-circle-linear class="h-5 w-5" />
            </button>
            <div class="w-full max-h-[80vh] flex items-center justify-center overflow-hidden rounded-xl p-2">
                <img :src="previewImage" alt="Preview Foto Fullscreen" class="max-h-[75vh] w-auto max-w-full object-contain rounded-lg shadow-2xl">
            </div>
        </div>
    </x-modal>

</div>

<script>
function riwayatPageApp() {
    return {
        loading: true,
        riwayatList: [],
        activeHistoryItem: null,
        showResultModal: false,
        previewImage: null,

        async init() {
            const token = localStorage.getItem('vonic_token');
            const headers = token
                ? { 'Accept': 'application/json', 'Authorization': 'Bearer ' + token }
                : { 'Accept': 'application/json' };

            try {
                const res = await fetch('/api/polling', { headers });
                if (res.ok) {
                    const json = await res.json();
                    const semua = json.data ?? [];
                    // Tampilkan semua polling selesai atau polling terdahulu
                    this.riwayatList = semua.filter(p => p.status === 'selesai');
                }
            } catch(e) {
                console.error('Gagal mengambil riwayat:', e);
            } finally {
                this.loading = false;
            }
        },

        formatDateRange(mulai, selesai) {
            const opts = { day: 'numeric', month: 'short', year: 'numeric' };
            const locale = 'id-ID';
            try {
                return new Date(mulai).toLocaleDateString(locale, { day: 'numeric', month: 'short' })
                    + ' – '
                    + new Date(selesai).toLocaleDateString(locale, opts);
            } catch(e) {
                return mulai + ' – ' + selesai;
            }
        },

        lihatHasil(poll) {
            const votes = Array.isArray(poll.partisipasi) ? poll.partisipasi : [];
            const countBy = (key) => votes.reduce((acc, v) => {
                if (v[key]) acc[v[key]] = (acc[v[key]] || 0) + 1;
                return acc;
            }, {});

            const kelompokCounts = countBy('pilihan_kelompok');
            const dipisahCount = kelompokCounts['dipisah'] || 0;
            const digabungCount = kelompokCounts['digabung'] || 0;
            let pesertaLabel = 'Belum ada suara';
            if (dipisahCount + digabungCount > 0) {
                pesertaLabel = dipisahCount >= digabungCount ? 'Dosen dan tendik dipisah' : 'Dosen dan tendik digabung';
            } else if (poll.mode_kelompok) {
                pesertaLabel = poll.mode_kelompok === 'dipisah' ? 'Dosen dan tendik dipisah' : 'Dosen dan tendik digabung';
            }

            const wisataCounts = countBy('id_paket');
            const wisataList = Array.isArray(poll.paket_wisata) ? poll.paket_wisata : [];
            const wisataUtama = wisataList.slice().sort((a, b) => (wisataCounts[b.id_paket] || 0) - (wisataCounts[a.id_paket] || 0))[0] || null;

            const kaosCounts = countBy('id_kaos');
            const kaosList = Array.isArray(poll.desain_kaos) ? poll.desain_kaos : [];
            const kaosUtama = kaosList.slice().sort((a, b) => (kaosCounts[b.id_kaos] || 0) - (kaosCounts[a.id_kaos] || 0))[0] || null;

            const kaosGaleri = kaosUtama && Array.isArray(kaosUtama.galeri) ? kaosUtama.galeri.map(g => g.foto_url || g.foto).filter(Boolean) : [];
            const wisataGaleri = wisataUtama && Array.isArray(wisataUtama.galeri) ? wisataUtama.galeri.map(g => g.foto_url || g.foto).filter(Boolean) : [];
            const benefitList = wisataUtama && wisataUtama.benefit
                ? String(wisataUtama.benefit).split(',').map(b => b.trim()).filter(Boolean)
                : [];

            const formatShortDate = (d) => {
                if (!d) return '—';
                const dt = new Date(d);
                if (isNaN(dt.getTime())) return d;
                const dd = String(dt.getDate()).padStart(2, '0');
                const mm = String(dt.getMonth() + 1).padStart(2, '0');
                const yy = String(dt.getFullYear()).slice(-2);
                return `${dd}/${mm}/${yy}`;
            };

            const rawDurasi = wisataUtama && wisataUtama.durasi_perjalanan ? String(wisataUtama.durasi_perjalanan) : '';
            const durasiFormatted = rawDurasi
                ? (rawDurasi.toLowerCase().includes('jam') ? rawDurasi : rawDurasi + ' Jam')
                : '—';

            this.activeHistoryItem = {
                title: poll.nama_periode,
                start: formatShortDate(poll.tanggal_mulai),
                end: formatShortDate(poll.tanggal_selesai),
                participants: votes.length,
                peserta_type: pesertaLabel,
                kaos: {
                    title: kaosUtama ? (kaosUtama.nama_desain || kaosUtama.nama_kaos || '—') : '—',
                    image: kaosUtama ? (kaosUtama.foto_url || kaosUtama.foto_kaos || '') : '',
                    gallery: kaosGaleri
                },
                wisata: {
                    title: wisataUtama ? wisataUtama.nama_destinasi : '—',
                    kategori: wisataUtama && wisataUtama.kategori ? wisataUtama.kategori : 'Wisata',
                    provinsi: wisataUtama && wisataUtama.provinsi ? wisataUtama.provinsi : null,
                    rating: wisataUtama && wisataUtama.rating ? wisataUtama.rating : '—',
                    image: wisataUtama ? (wisataUtama.foto_url || wisataUtama.foto_destinasi || '') : '',
                    gallery: wisataGaleri,
                    lokasi: wisataUtama ? (wisataUtama.lokasi || wisataUtama.provinsi || '—') : '—',
                    jarak: wisataUtama && wisataUtama.jarak ? (String(wisataUtama.jarak).toLowerCase().includes('km') ? wisataUtama.jarak : wisataUtama.jarak + ' Km') : '—',
                    durasi: durasiFormatted,
                    dikunjungi: wisataUtama && wisataUtama.total_kunjungan ? wisataUtama.total_kunjungan : null,
                    benefits: benefitList,
                    biaya: (wisataUtama && wisataUtama.harga) ? ('Rp ' + Number(wisataUtama.harga).toLocaleString('id-ID')) : '—'
                }
            };
            this.showResultModal = true;
        }
    };
}
</script>
@endsection
