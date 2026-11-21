@extends('layouts.user')

@section('title', 'Beranda')

@section('content')
<script>
function berandaApp(initialPollingAktif, initialRiwayatList) {
    return {
        loading: false,
        loadingRiwayat: false,
        pollingAktif: initialPollingAktif,
        sudahVote: false,
        countdown: '--:--:--',
        riwayatList: initialRiwayatList || [],
        countdownInterval: null,
        activeHistoryItem: null,
        showResultModal: false,
        previewImage: null,

        lihatHasil(poll) {
            // Transform data polling selesai ke format Quick-View Pemenang (identik admin arsip)
            const id = poll.id_periode || 1;
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
        },

        async init() {
            // Langsung jalankan countdown secara instan dari SSR data jika ada (tanpa menunggu axios)
            if (this.pollingAktif && this.pollingAktif.tanggal_selesai) {
                this.startCountdown(this.pollingAktif.tanggal_selesai);
            }

            const token = localStorage.getItem('vonic_token');
            const headers = token ? { Authorization: 'Bearer ' + token } : {};

            // Fetch polling aktif untuk background sync / refresh
            try {
                const res = await axios.get('/api/polling');
                const semua = res.data.data ?? [];
                const latestAktif = semua.find(p => p.status === 'aktif') ?? null;
                if (latestAktif) {
                    this.pollingAktif = latestAktif;
                    if (!this.countdownInterval) {
                        this.startCountdown(this.pollingAktif.tanggal_selesai);
                    }
                }
                const latestRiwayat = semua.filter(p => p.status === 'selesai').slice(0, 3);
                if (latestRiwayat.length > 0) {
                    this.riwayatList = latestRiwayat;
                }

                // Cek apakah sudah vote (hanya kalau ada token)
                if (token && this.pollingAktif) {
                    try {
                        const riwRes = await axios.get('/api/riwayat-voting', { headers });
                        const riwayat = riwRes.data.data ?? [];
                        this.sudahVote = riwayat.some(r => r.id_periode === this.pollingAktif.id_periode);
                    } catch(e) {}
                }
            } catch(e) {
                console.error('Gagal sinkronisasi polling background:', e.message);
            } finally {
                this.loading = false;
                this.loadingRiwayat = false;
            }
        },

        startCountdown(tanggalSelesai) {
            const update = () => {
                const diff = new Date(tanggalSelesai + 'T23:59:59') - new Date();
                if (diff <= 0) {
                    this.countdown = 'Selesai';
                    clearInterval(this.countdownInterval);
                    return;
                }
                const h = Math.floor(diff / 3600000);
                const m = Math.floor((diff % 3600000) / 60000);
                const s = Math.floor((diff % 60000) / 1000);
                this.countdown = String(h).padStart(2,'0') + ':' + String(m).padStart(2,'0') + ':' + String(s).padStart(2,'0');
            };
            update();
            this.countdownInterval = setInterval(update, 1000);
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
        }
    };
}
</script>

<div class="space-y-12"
     x-data="berandaApp({{ Js::from($pollingAktif) }}, {{ Js::from($riwayatList) }})"
     x-init="init()">

    <!-- ========================================== -->
    <!-- BAGIAN 1: POLLING AKTIF & FAQ PANDUAN      -->
    <!-- ========================================== -->
    <div>
        <div class="flex flex-col items-stretch gap-[46px] lg:flex-row lg:justify-between">

            <!-- Kolom Kiri: Polling Aktif -->
            <div class="flex w-full flex-col lg:w-3/5">
                <h2 class="mb-4 text-xl font-bold tracking-tight text-primary-1">Polling Aktif</h2>

                <!-- Loading Skeleton -->
                <div x-show="loading" class="flex h-[420px] animate-pulse flex-col justify-between rounded-2xl border-2 border-grey-1 bg-white p-6 shadow-type-1">
                    <div class="space-y-4">
                        <div class="flex items-start justify-between gap-4">
                            <div class="h-6 w-48 rounded-lg bg-gray-200"></div>
                            <div class="h-7 w-28 rounded-full bg-gray-200"></div>
                        </div>
                        <div class="h-16 rounded-xl bg-gray-200"></div>
                        <div class="flex gap-3">
                            <div class="h-[78px] flex-1 rounded-xl bg-gray-200"></div>
                            <div class="h-[78px] flex-1 rounded-xl bg-gray-200"></div>
                            <div class="h-[78px] flex-1 rounded-xl bg-gray-200"></div>
                        </div>
                    </div>
                    <div class="h-12 rounded-xl bg-gray-200"></div>
                </div>

                <!-- Card Polling Aktif (ada data) dengan inner padding lebih leluasa (p-8 sm:p-9) -->
                <x-card variant="hero-polling" x-show="!loading && pollingAktif" class="flex h-[420px] flex-col justify-between">
                        <!-- 1. Header Card: Judul Biru & Countdown Timer -->
                        <div class="flex items-center justify-between gap-4">
                            <div>
                                <h3 class="text-2xl font-bold tracking-tight text-primary-1" x-text="pollingAktif.nama_periode"></h3>
                            </div>
                            <!-- Countdown Pill -->
                            <div class="inline-flex shrink-0 items-center gap-2 rounded-full bg-danger px-4 py-2 text-sm sm:text-base font-bold text-white shadow-xs tracking-wider">
                                <x-solar-clock-circle-linear class="h-5 w-5" />
                                <span x-text="countdown"></span>
                            </div>
                        </div>

                        <!-- 2. Alert Status Merah -->
                        <div x-show="sudahVote" class="rounded-xl bg-primary-1 p-3.5 text-xs font-medium leading-relaxed text-white">✅ Anda sudah berpartisipasi dalam polling ini. Terima kasih atas suara Anda!</div>
                        <div x-show="!sudahVote" class="rounded-xl bg-danger p-3.5 text-xs font-medium leading-relaxed text-white">Anda belum mengikuti polling ini. Segera tekan tombol "Ikuti Voting" untuk memberikan suara Anda.</div>

                        <!-- 3. Tiga Metric Box (Periode, Suara Masuk, Status) menggunakan komponen info-box -->
                        <div class="flex gap-4">
                            <!-- Periode -->
                            <x-card variant="info-box" class="flex h-[102px] flex-1 flex-col justify-center gap-2 px-4 py-3">
                                <div class="flex items-center gap-1.5 text-xs font-semibold text-primary-1">
                                    <x-solar-calendar-linear class="h-4 w-4 shrink-0 text-primary-1" />
                                    <span>Periode</span>
                                </div>
                                <p class="text-xs font-bold text-black truncate"
                                   x-text="formatDateRange(pollingAktif.tanggal_mulai, pollingAktif.tanggal_selesai)"></p>
                            </x-card>

                            <!-- Partisipan -->
                            <x-card variant="info-box" class="flex h-[102px] flex-1 flex-col justify-center gap-2 px-4 py-3">
                                <div class="flex items-center gap-1.5 text-xs font-semibold text-primary-1">
                                    <x-solar-users-group-two-rounded-linear class="h-4 w-4 shrink-0 text-primary-1" />
                                    <span>Suara Masuk</span>
                                </div>
                                <p class="text-sm font-bold text-black" x-text="pollingAktif.partisipan_count ?? '—'"></p>
                            </x-card>

                            <!-- Status -->
                            <x-card variant="info-box" class="flex h-[102px] flex-1 flex-col justify-center gap-2 px-4 py-3">
                                <div class="flex items-center gap-1.5 text-xs font-semibold text-primary-1">
                                    <x-solar-clock-circle-linear class="h-4 w-4 shrink-0 text-primary-1" />
                                    <span>Status</span>
                                </div>
                                <p class="text-xs font-bold text-black truncate"
                                   x-text="sudahVote ? 'Sudah mengikuti' : 'Belum mengikuti'"></p>
                            </x-card>
                        </div>

                        <!-- 4. Tombol Ikuti Voting di Bawah: Gradient Main-Blue CTA + Glow Effect Sesuai Design System -->
                        <div>
                            <a x-show="!sudahVote"
                               :href="'{{ route('voting') }}?periode=' + (pollingAktif ? pollingAktif.id_periode : '')"
                               class="inline-flex w-full items-center justify-center gap-2.5 rounded-xl bg-gradient-to-r from-secondary-1 to-primary-1 px-8 py-3.5 text-sm font-semibold text-white shadow-primary-glow transition-all hover:opacity-95 active:opacity-90">
                                <span>Ikuti Voting</span>
                                <x-solar-arrow-right-linear class="h-4 w-4" />
                            </a>
                            <a x-show="sudahVote"
                               href="{{ route('riwayat') }}"
                               class="inline-flex w-full items-center justify-center gap-2.5 rounded-xl border-2 border-primary-1 bg-white px-8 py-3.5 text-sm font-semibold text-primary-1 transition-all hover:bg-primary-3/30">
                                <x-solar-folder-with-files-linear class="h-4 w-4" />
                                <span>Lihat Riwayat Partisipasi</span>
                            </a>
                        </div>
                </x-card>

                <!-- State: Tidak ada polling aktif -->
                <x-card variant="empty" x-show="!loading && !pollingAktif" class="flex h-[420px] flex-col items-center justify-center p-6 shadow-type-1">
                        <x-solar-calendar-linear class="mb-3 h-10 w-10 text-grey-2" />
                        <p class="text-sm font-semibold text-grey-dark">Tidak ada polling aktif saat ini</p>
                        <p class="mt-1 text-xs text-grey-2">Pantau terus halaman ini untuk info polling berikutnya</p>
                </x-card>
            </div>

            <!-- Kolom Kanan: FAQ Panduan Voting -->
            <div class="flex w-full flex-col lg:w-2/5">
                <h2 class="mb-4 text-xl font-bold tracking-tight text-primary-1">FAQ Panduan Voting</h2>

                <x-faq-card class="h-[420px] overflow-y-auto" />
            </div>

        </div>
    </div>

    <!-- ========================================== -->
    <!-- BAGIAN 2: RIWAYAT PEMILIHAN DESTINASI      -->
    <!-- ========================================== -->
    <div>
        <div class="mb-4 flex items-center justify-between">
            <h2 class="text-xl font-bold tracking-tight text-primary-1">
                Riwayat Pemilihan Destinasi Wisata Piknik DTEDI
            </h2>
            <a href="{{ route('riwayat') }}" class="text-xs font-semibold text-primary-1 hover:underline">
                Lihat Semua
            </a>
        </div>

        <!-- Loading -->
        <div x-show="loadingRiwayat" class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
            <template x-for="i in 3" :key="i">
                <div class="animate-pulse rounded-2xl border border-grey-1 bg-white p-6 shadow-xs">
                    <div class="h-5 w-32 rounded bg-gray-200"></div>
                    <div class="mt-2 h-3 w-full rounded bg-gray-200"></div>
                    <div class="mt-4 space-y-2">
                        <div class="h-10 rounded-lg bg-gray-200"></div>
                        <div class="h-10 rounded-lg bg-gray-200"></div>
                    </div>
                    <div class="mt-6 h-10 rounded-xl bg-gray-200"></div>
                </div>
            </template>
        </div>

        <!-- Cards Riwayat dari API -->
        <div x-show="!loadingRiwayat" class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
            <template x-for="poll in riwayatList" :key="poll.id_periode">
                <x-card variant="showcase" class="flex flex-col justify-between">
                    <div>
                        <h3 class="text-base font-bold text-primary-1" x-text="poll.nama_periode"></h3>
                        <p class="mt-1 text-xs text-grey-deep">
                            Periode E-Voting Piknik DTEDI Sekolah Vokasi UGM
                        </p>

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

                            <!-- Status -->
                            <x-card variant="info-box" class="flex items-center gap-3 px-3.5 py-2.5">
                                <x-solar-check-circle-linear class="h-4 w-4 shrink-0 text-primary-1" />
                                <div>
                                    <span class="block text-[10px] text-grey-2">Status</span>
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
            <template x-if="!loadingRiwayat && riwayatList.length === 0">
                <x-card variant="empty" class="col-span-3 p-12">
                    <x-solar-folder-with-files-linear class="mx-auto mb-3 h-10 w-10 text-grey-2" />
                    <p class="text-sm text-grey-dark">Belum ada riwayat polling</p>
                </x-card>
            </template>
        </div>
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


@endsection
