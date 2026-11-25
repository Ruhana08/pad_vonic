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
            const votes = Array.isArray(poll.partisipasi) ? poll.partisipasi : [];
            const countBy = (key) => votes.reduce((acc, v) => {
                if (v[key]) acc[v[key]] = (acc[v[key]] || 0) + 1;
                return acc;
            }, {});

            // 1. Pemenang Kategori 1 (Peserta)
            const kelompokCounts = countBy('pilihan_kelompok');
            const dipisahCount = kelompokCounts['dipisah'] || 0;
            const digabungCount = kelompokCounts['digabung'] || 0;
            let pesertaLabel = 'Belum ada suara';
            if (dipisahCount + digabungCount > 0) {
                pesertaLabel = dipisahCount >= digabungCount ? 'Dosen dan tendik dipisah' : 'Dosen dan tendik digabung';
            } else if (poll.mode_kelompok) {
                pesertaLabel = poll.mode_kelompok === 'dipisah' ? 'Dosen dan tendik dipisah' : 'Dosen dan tendik digabung';
            }

            // 2. Pemenang Paket Wisata
            const wisataCounts = countBy('id_paket');
            const wisataList = Array.isArray(poll.paket_wisata) ? poll.paket_wisata : [];
            const wisataUtama = wisataList.slice().sort((a, b) => (wisataCounts[b.id_paket] || 0) - (wisataCounts[a.id_paket] || 0))[0] || null;

            // 3. Pemenang Desain Kaos
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
        },

        async init() {
            // Langsung jalankan countdown secara instan dari SSR data jika ada (tanpa menunggu axios)
            if (this.pollingAktif && this.pollingAktif.tanggal_selesai) {
                this.startCountdown(this.pollingAktif.tanggal_selesai);
            }

            const token = localStorage.getItem('vonic_token');
            const headers = token
                ? { 'Accept': 'application/json', 'Authorization': 'Bearer ' + token }
                : { 'Accept': 'application/json' };

            // Fetch polling aktif untuk background sync / refresh
            try {
                const res = await fetch('/api/polling', { headers: { 'Accept': 'application/json' } });
                if (res.ok) {
                    const json = await res.json();
                    const semua = json.data ?? [];
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
                }

                // Cek apakah sudah vote (hanya kalau ada token)
                if (token && this.pollingAktif) {
                    try {
                        const riwRes = await fetch('/api/riwayat-voting', { headers });
                        if (riwRes.ok) {
                            const riwJson = await riwRes.json();
                            const riwayat = riwJson.data ?? [];
                            this.sudahVote = riwayat.some(r => String(r.id_periode) === String(this.pollingAktif.id_periode));
                        }
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

                <!-- Card Polling Aktif (State 1: Belum Vote, State 2: Sudah Vote, State 4: Hasil Tersedia) -->
                <x-card variant="hero-polling" x-show="!loading && pollingAktif" class="flex min-h-[420px] lg:h-[420px] flex-col justify-between gap-4">
                        <!-- 1. Header Card: Judul Biru & Countdown / Pill Hasil Tersedia -->
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 sm:gap-4">
                            <div>
                                <h3 class="text-xl sm:text-2xl font-bold tracking-tight text-primary-1" x-text="pollingAktif.nama_periode"></h3>
                            </div>
                            <!-- Countdown Pill (Aktif) -->
                            <div x-show="countdown !== 'Selesai' && pollingAktif.status === 'aktif'"
                                 class="inline-flex w-fit shrink-0 items-center gap-2 rounded-full bg-danger px-4 py-2 text-sm sm:text-base font-bold text-white shadow-xs tracking-wider">
                                <x-solar-clock-circle-linear class="h-5 w-5" />
                                <span x-text="countdown"></span>
                            </div>
                            <!-- Pill Hasil Tersedia (Selesai) -->
                            <span x-show="countdown === 'Selesai' || pollingAktif.status !== 'aktif'" x-cloak
                                  class="inline-flex w-fit shrink-0 items-center rounded-full bg-primary-3 px-4 py-1.5 text-xs font-semibold text-primary-1">
                                Hasil tersedia
                            </span>
                        </div>

                        <!-- 2. Alert Status Kondisional -->
                        <!-- Kondisi A: Sudah Selesai / Hasil Diumumkan -->
                        <div x-show="countdown === 'Selesai' || pollingAktif.status !== 'aktif'" x-cloak
                             class="rounded-xl bg-primary-3 p-3.5 text-xs font-medium leading-relaxed text-secondary-1">
                            Hasil polling telah diumumkan. Tekan tombol "Lihat Hasil Voting" untuk melihat hasilnya.
                        </div>
                        <!-- Kondisi B: Aktif & Sudah Vote -->
                        <div x-show="countdown !== 'Selesai' && pollingAktif.status === 'aktif' && sudahVote" x-cloak
                             class="rounded-xl bg-success-soft p-3.5 text-xs font-medium leading-relaxed text-black">
                            Suara Anda telah tersimpan. Terima kasih telah berpartisipasi dalam polling ini.
                        </div>
                        <!-- Kondisi C: Aktif & Belum Vote -->
                        <div x-show="countdown !== 'Selesai' && pollingAktif.status === 'aktif' && !sudahVote"
                             class="rounded-xl bg-danger p-3.5 text-xs font-medium leading-relaxed text-white">
                            Anda belum mengikuti polling ini. Segera tekan tombol "Ikuti Voting" untuk memberikan suara Anda.
                        </div>

                        <!-- 3. Tiga Metric Box (Periode, Partisipan, Status) -->
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 sm:gap-4">
                            <!-- Periode -->
                            <x-card variant="info-box" class="flex min-h-[84px] sm:h-[102px] flex-col justify-center gap-2 px-4 py-3">
                                <div class="flex items-center gap-1.5 text-xs font-semibold text-primary-1">
                                    <x-solar-calendar-linear class="h-4 w-4 shrink-0 text-primary-1" />
                                    <span>Periode</span>
                                </div>
                                <p class="text-xs font-bold text-black truncate"
                                   x-text="formatDateRange(pollingAktif.tanggal_mulai, pollingAktif.tanggal_selesai)"></p>
                            </x-card>

                            <!-- Partisipan -->
                            <x-card variant="info-box" class="flex min-h-[84px] sm:h-[102px] flex-col justify-center gap-2 px-4 py-3">
                                <div class="flex items-center gap-1.5 text-xs font-semibold text-primary-1">
                                    <x-solar-users-group-two-rounded-linear class="h-4 w-4 shrink-0 text-primary-1" />
                                    <span>Partisipan</span>
                                </div>
                                <p class="text-sm font-bold text-black" x-text="pollingAktif.partisipan_count ?? (pollingAktif.partisipasi ? pollingAktif.partisipasi.length : '—')"></p>
                            </x-card>

                            <!-- Status -->
                            <x-card variant="info-box" class="flex min-h-[84px] sm:h-[102px] flex-col justify-center gap-2 px-4 py-3">
                                <div class="flex items-center gap-1.5 text-xs font-semibold text-primary-1">
                                    <x-solar-clock-circle-linear class="h-4 w-4 shrink-0 text-primary-1" />
                                    <span>Status</span>
                                </div>
                                <p class="text-xs font-bold text-black truncate"
                                   x-text="(countdown === 'Selesai' || pollingAktif.status !== 'aktif') ? 'Hasil diumumkan' : (sudahVote ? 'Sudah mengikuti' : 'Belum mengikuti')"></p>
                            </x-card>
                        </div>

                        <!-- 4. Area Aksi Bawah Kondisional -->
                        <div>
                            <!-- Kondisi A: Hasil Diumumkan -->
                            <button type="button"
                                    x-show="countdown === 'Selesai' || pollingAktif.status !== 'aktif'" x-cloak
                                    @click="lihatHasil(pollingAktif)"
                                    class="btn-gradient w-full px-8 py-3.5 text-sm">
                                <x-solar-arrow-right-linear class="h-4 w-4" />
                                <span>Lihat Hasil Voting</span>
                            </button>

                            <!-- Kondisi B: Aktif & Sudah Vote -->
                            <div x-show="countdown !== 'Selesai' && pollingAktif.status === 'aktif' && sudahVote" x-cloak
                                 class="flex items-center justify-center gap-2 py-2.5 text-sm font-semibold text-success">
                                <x-solar-check-circle-linear class="h-5 w-5 shrink-0" />
                                <span>Anda sudah memberikan suara</span>
                            </div>

                            <!-- Kondisi C: Aktif & Belum Vote -->
                            <a x-show="countdown !== 'Selesai' && pollingAktif.status === 'aktif' && !sudahVote"
                               :href="'{{ route('voting') }}?periode=' + (pollingAktif ? pollingAktif.id_periode : '')"
                               class="btn-gradient w-full px-8 py-3.5 text-sm">
                                <x-solar-arrow-right-linear class="h-4 w-4" />
                                <span>Ikuti Voting</span>
                            </a>
                        </div>
                </x-card>

                <!-- State 3: Tidak ada polling aktif -->
                <x-card variant="hero-polling" x-show="!loading && !pollingAktif" class="flex min-h-[420px] lg:h-[420px] flex-col justify-between gap-4">
                        <div class="flex items-start justify-between gap-4">
                            <div>
                                <h3 class="text-xl sm:text-2xl font-bold tracking-tight text-primary-1">Polling</h3>
                                <p class="mt-1 text-xs sm:text-sm text-grey-deep">Belum ada polling yang tersedia saat ini</p>
                            </div>
                            <span class="inline-flex shrink-0 items-center rounded-full bg-gray-100 px-4 py-1.5 text-xs font-semibold text-grey-dark">
                                Tidak aktif
                            </span>
                        </div>

                        <div class="flex flex-1 flex-col items-center justify-center rounded-xl bg-gray-50/80 px-6 py-8 text-center">
                            <x-solar-clock-circle-linear class="mb-3 h-8 w-8 text-secondary-1" />
                            <p class="text-base font-bold text-secondary-1">Belum ada polling aktif</p>
                            <p class="mt-1 text-xs text-grey-deep">Silakan cek kembali nanti untuk mengikuti polling berikutnya.</p>
                        </div>

                        <div class="py-2 text-center">
                            <span class="text-xs sm:text-sm font-medium text-grey-deep">Voting belum tersedia</span>
                        </div>
                </x-card>
            </div>

            <!-- Kolom Kanan: FAQ Panduan Voting -->
            <div class="flex w-full flex-col lg:w-2/5">
                <h2 class="mb-4 text-xl font-bold tracking-tight text-primary-1">FAQ Panduan Voting</h2>

                <x-faq-card class="lg:h-[420px] lg:overflow-y-auto" />
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
