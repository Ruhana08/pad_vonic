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
            const headers = token ? { Authorization: 'Bearer ' + token } : {};

            try {
                const res = await axios.get('/api/polling', { headers });
                const semua = res.data.data ?? [];
                // Tampilkan semua polling selesai atau polling terdahulu
                this.riwayatList = semua.filter(p => p.status === 'selesai');
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
            const wisataUtama = (poll.paket_wisata && poll.paket_wisata[0]) ? poll.paket_wisata[0] : null;
            const kaosUtama = (poll.desain_kaos && poll.desain_kaos[0]) ? poll.desain_kaos[0] : null;

            this.activeHistoryItem = {
                title: poll.nama_periode,
                start: poll.tanggal_mulai,
                end: poll.tanggal_selesai,
                participants: (poll.partisipasi ? poll.partisipasi.length : 0) || 120,
                peserta_type: 'Dosen dan Tenaga Didik',
                kaos: {
                    title: kaosUtama ? kaosUtama.nama_kaos : 'Kaos Desain Pemenang',
                    image: (kaosUtama && kaosUtama.foto_kaos) ? kaosUtama.foto_kaos : 'https://images.unsplash.com/photo-1507525428034-b723cf961d3e?auto=format&fit=crop&w=600&q=80',
                    gallery: [
                        'https://images.unsplash.com/photo-1519046904884-53103b34b206?auto=format&fit=crop&w=300&q=80',
                        'https://images.unsplash.com/photo-1473186578172-c141e6798cf4?auto=format&fit=crop&w=300&q=80'
                    ]
                },
                wisata: {
                    title: wisataUtama ? wisataUtama.nama_destinasi : 'Pantai Kartini',
                    rating: '4,8',
                    image: (wisataUtama && wisataUtama.foto_destinasi) ? wisataUtama.foto_destinasi : 'https://images.unsplash.com/photo-1507525428034-b723cf961d3e?auto=format&fit=crop&w=800&q=80',
                    gallery: [
                        'https://images.unsplash.com/photo-1519046904884-53103b34b206?auto=format&fit=crop&w=400&q=80',
                        'https://images.unsplash.com/photo-1473186578172-c141e6798cf4?auto=format&fit=crop&w=400&q=80'
                    ],
                    lokasi: (wisataUtama && wisataUtama.provinsi) ? (wisataUtama.provinsi + ', Indonesia') : 'Jepara, Jawa Tengah.',
                    jarak: '120 Km',
                    durasi: '2 Hari',
                    dikunjungi: 2,
                    benefits: ['Bis', 'Makan', 'Minum', 'Kamar 2 hari'],
                    biaya: (wisataUtama && wisataUtama.harga) ? ('Rp ' + Number(wisataUtama.harga).toLocaleString('id-ID')) : 'Rp 500.000'
                }
            };
            this.showResultModal = true;
        }
    };
}
</script>
@endsection
