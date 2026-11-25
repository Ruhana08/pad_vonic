@extends('layouts.user-voting')

@section('title', 'Ikuti Voting')
@section('page_title', 'Ikuti Voting')

@section('content')
<div x-data="votingApp()"
     x-init="init()"
     class="relative min-h-[calc(100vh-72px)] w-full overflow-hidden bg-gray-50">

    <!-- ========================================== -->
    <!-- SIDEBAR KIRI (FLOATING / STICKY DI PINGGIR)-->
    <!-- ========================================== -->
    <div class="fixed left-8 top-[96px] z-20 w-[240px] space-y-4">
        
        <!-- Tombol Batalkan -->
        <a href="{{ route('home') }}"
           class="flex h-11 items-center gap-2 rounded-xl border border-grey-1 bg-white px-4 text-xs font-semibold text-grey-deep shadow-xs transition-colors hover:border-danger hover:text-danger">
            <x-solar-close-circle-linear class="h-5 w-5" />
            <span>Batalkan</span>
        </a>

        <!-- Container Stepper Biru Solid -->
        <div class="space-y-2.5 rounded-2xl bg-primary-1 p-3.5 shadow-sm text-white">
            
            <!-- Step 1: Peserta -->
            <button type="button"
                    @click="if(step > 1) step = 1"
                    :class="step === 1 ? 'bg-secondary-1 text-white font-bold' : (step > 1 ? 'bg-primary-1/40 hover:bg-white/10 text-white font-medium' : 'bg-white text-black font-semibold')"
                    class="flex h-12 w-full items-center justify-between rounded-xl px-4 text-xs transition-all cursor-pointer">
                <span class="flex items-center gap-2">
                    <template x-if="step > 1">
                        <x-solar-check-circle-bold class="h-4 w-4 text-white" />
                    </template>
                    <span>Peserta</span>
                </span>
                <span class="text-[11px] opacity-75">1/4</span>
            </button>

            <!-- Step 2: Paket Wisata -->
            <button type="button"
                    @click="if(step > 2) step = 2"
                    :class="step === 2 ? 'bg-secondary-1 text-white font-bold' : (step > 2 ? 'bg-primary-1/40 hover:bg-white/10 text-white font-medium' : 'bg-white text-black font-semibold')"
                    class="flex h-12 w-full items-center justify-between rounded-xl px-4 text-xs transition-all cursor-pointer">
                <span class="flex items-center gap-2">
                    <template x-if="step > 2">
                        <x-solar-check-circle-bold class="h-4 w-4 text-white" />
                    </template>
                    <span>Paket Wisata</span>
                </span>
                <span class="text-[11px] opacity-75">2/4</span>
            </button>

            <!-- Step 3: Kaos -->
            <button type="button"
                    @click="if(step > 3) step = 3"
                    :class="step === 3 ? 'bg-secondary-1 text-white font-bold' : (step > 3 ? 'bg-primary-1/40 hover:bg-white/10 text-white font-medium' : 'bg-white text-black font-semibold')"
                    class="flex h-12 w-full items-center justify-between rounded-xl px-4 text-xs transition-all cursor-pointer">
                <span class="flex items-center gap-2">
                    <template x-if="step > 3">
                        <x-solar-check-circle-bold class="h-4 w-4 text-white" />
                    </template>
                    <span>Kaos</span>
                </span>
                <span class="text-[11px] opacity-75">3/4</span>
            </button>

            <!-- Step 4: Hasil Akhir -->
            <button type="button"
                    :class="step === 4 ? 'bg-secondary-1 text-white font-bold' : 'bg-white text-black font-semibold'"
                    class="flex h-12 w-full items-center justify-between rounded-xl px-4 text-xs transition-all">
                <span>Hasil akhir</span>
                <span class="text-[11px] opacity-75">4/4</span>
            </button>

        </div>

        <!-- Info Card: Kerahasiaan Suara -->
        <div class="rounded-xl border border-primary-1/15 bg-primary-3/30 p-3.5 text-[11px] leading-relaxed text-grey-deep">
            <div class="flex items-start gap-2">
                <x-solar-info-circle-linear class="mt-0.5 h-4 w-4 shrink-0 text-primary-1" />
                <span>Suara Anda bersifat rahasia dan hanya dapat diberikan satu kali.</span>
            </div>
        </div>

    </div>

    <!-- ========================================== -->
    <!-- PAPER CANVAS (CENTERED, NEMBUS KE BAWAH)   -->
    <!-- ========================================== -->
    <div class="mx-auto flex min-h-[calc(100vh-72px)] w-full max-w-[860px] justify-center px-4 pt-6">
        
        <!-- Paper Container: Rounded hanya di atas, bawah nembus tak terbatas -->
        <div class="min-h-full w-full rounded-t-[28px] rounded-b-none border-x border-t border-gray-200/80 bg-white p-6 sm:p-10 pb-36 shadow-type-2">

            <!-- Loading State Saat Fetch API -->
            <div x-show="loading" class="flex flex-col items-center justify-center py-24 text-center">
                <div class="h-10 w-10 animate-spin rounded-full border-4 border-primary-1 border-t-transparent"></div>
                <p class="mt-4 text-sm font-medium text-grey-dark">Memuat data pilihan voting...</p>
            </div>

            <!-- Error State: Polling Tidak Ditemukan / Sudah Selesai / Masalah Jaringan -->
            <div x-show="!loading && loadError" x-cloak class="space-y-6 py-16 text-center">
                <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-full bg-red-50 text-danger">
                    <x-solar-danger-triangle-bold class="h-10 w-10" />
                </div>
                <div class="space-y-2">
                    <h2 class="text-xl font-bold tracking-tight text-grey-dark" x-text="loadError"></h2>
                    <p class="text-xs text-grey-deep">Silakan kembali ke beranda untuk melihat jadwal dan status voting terbaru.</p>
                </div>
                <div>
                    <a href="{{ route('home') }}"
                       class="inline-flex items-center gap-2 rounded-xl bg-primary-1 px-6 py-3 text-xs font-semibold text-white shadow-sm transition-all hover:bg-primary-4">
                        <x-solar-arrow-left-linear class="h-4 w-4" />
                        <span>Kembali ke Beranda</span>
                    </a>
                </div>
            </div>

            <!-- State: Pengguna Sudah Memberikan Suara -->
            <div x-show="!loading && !loadError && sudahVote" x-cloak class="space-y-6 py-16 text-center">
                <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-full bg-green-50 text-success">
                    <x-solar-check-circle-bold class="h-10 w-10" />
                </div>
                <div class="space-y-2">
                    <h2 class="text-xl font-bold tracking-tight text-primary-1">Anda Sudah Memilih</h2>
                    <p class="mx-auto max-w-md text-xs text-grey-deep leading-relaxed">
                        Anda telah tercatat memberikan suara pada periode polling ini. Setiap pemilih hanya dapat berpartisipasi 1 kali.
                    </p>
                </div>
                <div class="flex justify-center gap-3 pt-2">
                    <a href="{{ route('home') }}"
                       class="inline-flex items-center gap-2 rounded-xl border border-gray-200 bg-white px-5 py-2.5 text-xs font-semibold text-grey-dark transition-all hover:border-primary-1 hover:text-primary-1">
                        <span>Ke Beranda</span>
                    </a>
                    <a href="{{ route('riwayat') }}"
                       class="inline-flex items-center gap-2 rounded-xl bg-primary-1 px-5 py-2.5 text-xs font-semibold text-white shadow-sm transition-all hover:bg-primary-4">
                        <x-solar-folder-with-files-linear class="h-4 w-4" />
                        <span>Lihat Riwayat Saya</span>
                    </a>
                </div>
            </div>

            <div x-show="!loading && !loadError && !sudahVote">

            <!-- ========================================== -->
            <!-- TAHAP 1: PESERTA                           -->
            <!-- ========================================== -->
            <div x-show="step === 1" x-cloak class="space-y-8">
                <!-- Header Tahap dengan Navigasi Icon di Kanan (Sesuai Figma) -->
                <div class="flex items-center justify-between border-b border-gray-100 pb-4">
                    <div class="flex items-center gap-3">
                        <span class="rounded-lg bg-primary-3 px-3 py-1 text-xs font-bold text-primary-1">1/4</span>
                        <h2 class="text-xl font-bold tracking-tight text-primary-1">Peserta</h2>
                    </div>

                    <!-- Tombol Navigasi Ikon (Atas) -->
                    <div class="flex items-center gap-2">
                        <button type="button"
                                disabled
                                aria-label="Sebelumnya"
                                class="flex h-10 w-10 items-center justify-center rounded-xl border border-gray-200 bg-white text-grey-2 shadow-xs opacity-40 cursor-not-allowed">
                            <x-solar-arrow-left-linear class="h-4 w-4" />
                        </button>
                        <button type="button"
                                @click="if(pilihanPeserta) step = 2"
                                :disabled="!pilihanPeserta"
                                aria-label="Selanjutnya"
                                class="flex h-10 w-10 items-center justify-center rounded-xl border border-grey-1 bg-white text-primary-1 shadow-xs transition-all hover:border-primary-1 hover:bg-primary-3/30 disabled:opacity-30 disabled:cursor-not-allowed cursor-pointer">
                            <x-solar-arrow-right-linear class="h-4 w-4" />
                        </button>
                    </div>
                </div>

                <!-- Deskripsi Pertanyaan -->
                <div class="rounded-xl border border-gray-100 bg-gray-50/70 p-6">
                    <h3 class="text-base sm:text-lg font-bold text-black tracking-tight leading-snug">Bagaimana rencana pembagian peserta piknik yang Anda harapkan?</h3>
                    <p class="mt-2 text-xs sm:text-sm text-grey-deep leading-relaxed">
                        Pilihan ini menentukan alokasi armada bus, pengaturan kamar hotel/villa, serta pembagian sesi santai antar civitas akademika DTEDI Sekolah Vokasi UGM.
                    </p>
                </div>

                <!-- Opsi Pilihan (Radio Button Cards) -->
                <div class="space-y-3.5">
                    <!-- Opsi Dipisah -->
                    <label :class="pilihanPeserta === 'dipisah' ? 'is-active' : ''"
                           class="card-choice flex items-center gap-3.5">
                        <input type="radio" name="pilihan_peserta" value="dipisah" x-model="pilihanPeserta" class="h-4 w-4 text-primary-1 focus:ring-primary-1">
                        <span class="text-sm font-medium text-black">
                            Kelompok Dosen dan Tenaga Kependidikan (Tendik) <strong class="font-bold">dipisah</strong>
                        </span>
                    </label>

                    <!-- Opsi Digabung -->
                    <label :class="pilihanPeserta === 'digabung' ? 'is-active' : ''"
                           class="card-choice flex items-center gap-3.5">
                        <input type="radio" name="pilihan_peserta" value="digabung" x-model="pilihanPeserta" class="h-4 w-4 text-primary-1 focus:ring-primary-1">
                        <span class="text-sm font-medium text-black">
                            Kelompok Dosen dan Tenaga Kependidikan (Tendik) <strong class="font-bold">digabung</strong>
                        </span>
                    </label>
                </div>
            </div>

            <!-- ========================================== -->
            <!-- TAHAP 2: PAKET WISATA                      -->
            <!-- ========================================== -->
            <div x-show="step === 2" x-cloak class="space-y-6">
                <!-- Header Tahap dengan Navigasi Icon di Kanan (Sesuai Figma) -->
                <div class="flex items-center justify-between border-b border-gray-100 pb-4">
                    <div class="flex items-center gap-3">
                        <span class="rounded-lg bg-primary-3 px-3 py-1 text-xs font-bold text-primary-1">2/4</span>
                        <h2 class="text-xl font-bold tracking-tight text-primary-1">Paket Wisata</h2>
                    </div>

                    <!-- Tombol Navigasi Ikon (Atas) -->
                    <div class="flex items-center gap-2">
                        <button type="button"
                                @click="step = 1"
                                aria-label="Sebelumnya"
                                class="flex h-10 w-10 items-center justify-center rounded-xl border border-grey-1 bg-white text-grey-dark shadow-xs transition-all hover:border-primary-1 hover:bg-primary-3/30 hover:text-primary-1 cursor-pointer">
                            <x-solar-arrow-left-linear class="h-4 w-4" />
                        </button>
                        <button type="button"
                                @click="if(pilihanWisata) step = 3"
                                :disabled="!pilihanWisata"
                                aria-label="Selanjutnya"
                                class="flex h-10 w-10 items-center justify-center rounded-xl border border-grey-1 bg-white text-primary-1 shadow-xs transition-all hover:border-primary-1 hover:bg-primary-3/30 disabled:opacity-30 disabled:cursor-not-allowed cursor-pointer">
                            <x-solar-arrow-right-linear class="h-4 w-4" />
                        </button>
                    </div>
                </div>

                <!-- Subtitle & Bar Filter Interaktif -->
                <div class="space-y-2">
                    <p class="text-xs text-grey-deep">Silakan memilih paket wisata yang diinginkan.</p>

                    <!-- Filter Pill Bar: Tidak terpotong overflow & dropdown z-index 50 -->
                    <div class="flex flex-wrap items-center gap-1.5 sm:gap-2 relative z-20">
                        
                        <!-- Filter Jarak (Toggle: Terdekat / Terjauh) -->
                        <button type="button"
                                @click="toggleFilterJarak()"
                                :class="filterJarak !== 'none' ? 'border-primary-1 bg-primary-3 text-primary-1 font-bold' : 'border-grey-1 bg-white text-grey-deep'"
                                class="inline-flex shrink-0 items-center gap-1.5 rounded-lg border px-2.5 sm:px-3 py-1.5 text-xs transition-colors hover:border-primary-1 cursor-pointer">
                            <span>Jarak</span>
                            <span class="flex flex-col leading-none text-[8px]">
                                <span :class="filterJarak === 'jauh' ? 'font-black text-primary-1 text-[10px]' : 'opacity-40'">▲</span>
                                <span :class="filterJarak === 'dekat' ? 'font-black text-primary-1 text-[10px]' : 'opacity-40'">▼</span>
                            </span>
                        </button>

                        <!-- Filter Lama Perjalanan (Toggle: Singkat / Lama) -->
                        <button type="button"
                                @click="toggleFilterDurasi()"
                                :class="filterDurasi !== 'none' ? 'border-primary-1 bg-primary-3 text-primary-1 font-bold' : 'border-grey-1 bg-white text-grey-deep'"
                                class="inline-flex shrink-0 items-center gap-1.5 rounded-lg border px-2.5 sm:px-3 py-1.5 text-xs transition-colors hover:border-primary-1 cursor-pointer">
                            <span>Lama Perjalanan</span>
                            <span class="flex flex-col leading-none text-[8px]">
                                <span :class="filterDurasi === 'lama' ? 'font-black text-primary-1 text-[10px]' : 'opacity-40'">▲</span>
                                <span :class="filterDurasi === 'singkat' ? 'font-black text-primary-1 text-[10px]' : 'opacity-40'">▼</span>
                            </span>
                        </button>

                        <!-- Dropdown Filter Daerah (Provinsi) -->
                        <div class="relative shrink-0" @click.outside="openDaerahDropdown = false">
                            <button type="button"
                                    @click="openDaerahDropdown = !openDaerahDropdown; openKategoriDropdown = false"
                                    :class="filterDaerah !== 'semua' && filterDaerah !== 'Semua Daerah' ? 'border-primary-1 bg-primary-3 text-primary-1 font-bold' : 'border-grey-1 bg-white text-grey-deep'"
                                    class="inline-flex items-center gap-1.5 rounded-lg border px-2.5 sm:px-3 py-1.5 text-xs transition-colors hover:border-primary-1 cursor-pointer">
                                <span x-text="filterDaerah === 'semua' ? 'Daerah' : filterDaerah"></span>
                                <x-solar-alt-arrow-down-linear class="h-3.5 w-3.5" />
                            </button>
                            <div x-show="openDaerahDropdown" x-cloak
                                 class="absolute left-0 top-full z-50 mt-1.5 max-h-56 w-48 overflow-y-auto rounded-lg border border-gray-200 bg-white p-1 shadow-xl">
                                <template x-for="p in daftarProvinsi" :key="p">
                                    <button type="button"
                                            @click="filterDaerah = (p === 'Semua Daerah' ? 'semua' : p); openDaerahDropdown = false"
                                            :class="(filterDaerah === p || (p === 'Semua Daerah' && filterDaerah === 'semua')) ? 'bg-primary-3 text-primary-1 font-bold' : 'text-black hover:bg-gray-50'"
                                            class="w-full rounded-md px-3 py-1.5 text-left text-xs cursor-pointer">
                                        <span x-text="p"></span>
                                    </button>
                                </template>
                            </div>
                        </div>

                        <!-- Dropdown Filter Kategori -->
                        <div class="relative shrink-0" @click.outside="openKategoriDropdown = false">
                            <button type="button"
                                    @click="openKategoriDropdown = !openKategoriDropdown; openDaerahDropdown = false"
                                    :class="filterKategori !== 'semua' && filterKategori !== 'Semua Kategori' ? 'border-primary-1 bg-primary-3 text-primary-1 font-bold' : 'border-grey-1 bg-white text-grey-deep'"
                                    class="inline-flex items-center gap-1.5 rounded-lg border px-2.5 sm:px-3 py-1.5 text-xs transition-colors hover:border-primary-1 cursor-pointer">
                                <span x-text="filterKategori === 'semua' ? 'Kategori' : filterKategori" class="max-w-28 truncate"></span>
                                <x-solar-alt-arrow-down-linear class="h-3.5 w-3.5" />
                            </button>
                            <div x-show="openKategoriDropdown" x-cloak
                                 class="absolute left-0 top-full z-50 mt-1.5 max-h-56 w-52 overflow-y-auto rounded-lg border border-gray-200 bg-white p-1 shadow-xl">
                                <template x-for="k in daftarKategori" :key="k">
                                    <button type="button"
                                            @click="filterKategori = (k === 'Semua Kategori' ? 'semua' : k); openKategoriDropdown = false"
                                            :class="(filterKategori === k || (k === 'Semua Kategori' && filterKategori === 'semua')) ? 'bg-primary-3 text-primary-1 font-bold' : 'text-black hover:bg-gray-50'"
                                            class="w-full rounded-md px-3 py-1.5 text-left text-xs truncate cursor-pointer">
                                        <span x-text="k"></span>
                                    </button>
                                </template>
                            </div>
                        </div>

                        <!-- Tombol Silang (X) Reset Filter Sejajar Bersama Tombol Filter -->
                        <template x-if="filterJarak !== 'none' || filterDurasi !== 'none' || filterDaerah !== 'semua' || filterKategori !== 'semua'">
                            <button type="button"
                                    @click="resetFilter()"
                                    title="Reset Semua Filter"
                                    class="inline-flex h-8 w-8 shrink-0 items-center justify-center rounded-lg border border-danger/30 bg-rose-50 text-danger transition-colors hover:bg-danger hover:text-white cursor-pointer ml-0.5">
                                <x-solar-close-circle-linear class="h-4 w-4" />
                            </button>
                        </template>

                    </div>
                </div>

                <!-- Grid Opsi Wisata (2 Kolom) -->
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                    <template x-for="item in filteredWisataList" :key="item.id">
                        <div @click="pilihanWisata = item.id"
                             :class="pilihanWisata === item.id ? 'is-active' : ''"
                             class="card-choice flex flex-col justify-between">
                            
                            <div>
                                <!-- Header Card: Radio + Nama + Badge Kategori & Daerah -->
                                <div>
                                    <div class="flex items-start justify-between gap-2">
                                        <div class="flex items-center gap-2.5">
                                            <input type="radio" :value="item.id" x-model="pilihanWisata" class="h-4 w-4 text-primary-1 focus:ring-primary-1">
                                            <h4 class="text-sm font-bold text-black" x-text="item.nama"></h4>
                                        </div>
                                        <span class="rounded-md bg-primary-3/50 px-2 py-0.5 text-[10px] font-bold text-primary-1 shrink-0" x-text="item.kategori"></span>
                                    </div>
                                    <div class="mt-1 flex items-center gap-2 text-[11px] text-grey-deep pl-6">
                                        <span class="flex items-center gap-1 font-semibold text-amber-500">
                                            <x-solar-star-bold class="h-3.5 w-3.5" />
                                            <span x-text="item.rating"></span>
                                        </span>
                                        <span>•</span>
                                        <span x-text="item.provinsi || item.lokasi"></span>
                                    </div>
                                </div>

                                <!-- Galeri Foto Miniatur: Rounded minimal (hampir kotak), klik untuk buka slider foto interaktif -->
                                <div class="mt-3 grid grid-cols-3 gap-1.5 overflow-hidden rounded-xs bg-gray-100 p-1">
                                    <div class="col-span-2 relative h-36 overflow-hidden rounded-xs cursor-pointer group"
                                         @click.stop="openModalGallery(item.photos?.length ? item.photos : [item.image], 0, item.nama)">
                                        <img :src="item.photos?.[0] || item.image" class="h-full w-full object-cover transition-transform duration-300 group-hover:scale-105">
                                        <div class="absolute inset-0 bg-black/0 transition-colors group-hover:bg-black/20 flex items-center justify-center">
                                            <span class="opacity-0 group-hover:opacity-100 bg-black/70 text-white rounded-xs px-2 py-1 text-[10px] font-medium backdrop-blur-xs flex items-center gap-1">
                                                <x-solar-magnifer-linear class="h-3 w-3" /> Lihat Galeri
                                            </span>
                                        </div>
                                    </div>
                                    <div class="flex flex-col gap-1.5">
                                        <div class="relative h-[68px] overflow-hidden rounded-xs cursor-pointer group"
                                             @click.stop="openModalGallery(item.photos?.length ? item.photos : [item.image], 1, item.nama)">
                                            <img :src="item.photos?.[1] || item.image" class="h-full w-full object-cover transition-transform duration-300 group-hover:scale-105">
                                            <div class="absolute inset-0 bg-black/0 transition-colors group-hover:bg-black/20"></div>
                                        </div>
                                        <div class="relative h-[68px] overflow-hidden rounded-xs cursor-pointer group"
                                             @click.stop="openModalGallery(item.photos?.length ? item.photos : [item.image], 2, item.nama)">
                                            <img :src="item.photos?.[2] || item.image" class="h-full w-full object-cover transition-transform duration-300 group-hover:scale-105">
                                            <template x-if="(item.photos?.length || 0) > 2">
                                                <div class="absolute inset-0 flex items-center justify-center bg-black/55 text-[11px] font-bold text-white backdrop-blur-[1px]">
                                                    <span x-text="item.photos.length + ' Foto'"></span>
                                                </div>
                                            </template>
                                        </div>
                                    </div>
                                </div>

                                <!-- Model Khusus Info Metrik: Jarak, Durasi & Cashback (Border & Divider Halus Soft Gray-100) -->
                                <div class="mt-3.5 grid grid-cols-3 divide-x divide-gray-100 rounded-xs border border-gray-200 bg-white text-center shadow-xs">
                                    <div class="py-2.5 px-1.5">
                                        <span class="block text-[10px] font-medium uppercase tracking-wider text-grey-2">Jarak</span>
                                        <strong class="mt-0.5 block text-xs font-bold text-black" x-text="item.jarak"></strong>
                                    </div>
                                    <div class="py-2.5 px-1.5">
                                        <span class="block text-[10px] font-medium uppercase tracking-wider text-grey-2">Durasi</span>
                                        <strong class="mt-0.5 block text-xs font-bold text-black" x-text="item.durasi"></strong>
                                    </div>
                                    <div class="py-2.5 px-1.5">
                                        <span class="block text-[10px] font-semibold uppercase tracking-wider text-primary-1">Cashback</span>
                                        <strong class="mt-0.5 block text-xs font-bold text-primary-1" x-text="item.harga"></strong>
                                    </div>
                                </div>

                                <!-- Fasilitas & Benefit (Border Halus Soft Senada) -->
                                <div class="mt-3 text-[11px]">
                                    <span class="font-bold text-grey-deep block mb-1">Fasilitas Termasuk:</span>
                                    <div class="rounded-xs border border-gray-200 bg-white p-2.5 text-black text-[11px] leading-relaxed">
                                        <template x-if="item.benefit && item.benefit.length > 0">
                                            <div class="flex flex-wrap gap-1.5">
                                                <template x-for="b in item.benefit">
                                                    <span class="inline-flex items-center rounded-xs bg-gray-100 px-2 py-0.5 text-[10px] font-medium text-grey-dark" x-text="b"></span>
                                                </template>
                                            </div>
                                        </template>
                                        <template x-if="!item.benefit || item.benefit.length === 0">
                                            <span class="text-grey-2 italic">Tidak ada catatan fasilitas khusus</span>
                                        </template>
                                    </div>
                                </div>
                                </div>
                            </div>

                            <!-- Footer Card: Estimasi Cashback -->
                            <div class="mt-4 border-t border-gray-100 pt-3 text-center">
                                <span class="text-xs font-bold text-primary-1" x-text="item.cashback"></span>
                            </div>

                        </div>
                    </template>
                </div>

                <!-- Empty State jika filter tidak menemukan hasil -->
                <div x-show="filteredWisataList.length === 0" class="py-12 text-center text-xs text-grey-deep">
                    <p>Tidak ada paket wisata yang cocok dengan filter yang dipilih.</p>
                    <button type="button" @click="resetFilter()" class="mt-2 text-xs font-bold text-primary-1 hover:underline">
                        Reset Filter
                    </button>
                </div>
            </div>

            <!-- ========================================== -->
            <!-- TAHAP 3: KAOS (MULTI-UKURAN UNTUK KELUARGA)-->
            <!-- ========================================== -->
            <div x-show="step === 3" x-cloak class="space-y-6">
                <!-- Header Tahap dengan Navigasi Icon di Kanan (Sesuai Figma) -->
                <div class="flex items-center justify-between border-b border-gray-100 pb-4">
                    <div class="flex items-center gap-3">
                        <span class="rounded-lg bg-primary-3 px-3 py-1 text-xs font-bold text-primary-1">3/4</span>
                        <h2 class="text-xl font-bold tracking-tight text-primary-1">Kaos</h2>
                    </div>

                    <!-- Tombol Navigasi Ikon (Atas) -->
                    <div class="flex items-center gap-2">
                        <button type="button"
                                @click="step = 2"
                                aria-label="Sebelumnya"
                                class="flex h-10 w-10 items-center justify-center rounded-xl border border-grey-1 bg-white text-grey-dark shadow-xs transition-all hover:border-primary-1 hover:bg-primary-3/30 hover:text-primary-1 cursor-pointer">
                            <x-solar-arrow-left-linear class="h-4 w-4" />
                        </button>
                        <button type="button"
                                @click="if(pilihanKaos && totalKaos() > 0) step = 4"
                                :disabled="!pilihanKaos || totalKaos() === 0"
                                aria-label="Selanjutnya"
                                class="flex h-10 w-10 items-center justify-center rounded-xl border border-grey-1 bg-white text-primary-1 shadow-xs transition-all hover:border-primary-1 hover:bg-primary-3/30 disabled:opacity-40 disabled:cursor-not-allowed cursor-pointer">
                            <x-solar-arrow-right-linear class="h-4 w-4" />
                        </button>
                    </div>
                </div>

                <p class="text-xs text-grey-deep">Silakan memilih Desain Kaos & Ukuran yang diinginkan (bisa memilih lebih dari 1 ukuran untuk anggota keluarga).</p>

                <!-- Section Size Chart Banner -->
                <div>
                    <h3 class="mb-3 text-sm font-bold text-primary-1">Size Chart</h3>
                    <div class="overflow-hidden rounded-xl border border-gray-100 bg-gray-50/50 p-4 text-center">
                        <img src="https://images.unsplash.com/photo-1523381294911-8d3cead13475?w=800&auto=format&fit=crop&q=80"
                             alt="Size Chart Kaos" class="mx-auto max-h-56 rounded-lg object-contain">
                    </div>
                </div>

                <!-- Section Desain Kaos -->
                <div>
                    <h3 class="mb-3 text-sm font-bold text-primary-1">Desain Kaos</h3>
                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
                        <template x-for="kaos in kaosList" :key="kaos.id">
                            <div @click="pilihanKaos = kaos.id"
                                 :class="pilihanKaos === kaos.id ? 'is-active' : ''"
                                 class="card-choice flex flex-col justify-between">
                                
                                <div class="flex items-center gap-2">
                                    <input type="radio" :value="kaos.id" x-model="pilihanKaos" class="h-4 w-4 text-primary-1 focus:ring-primary-1">
                                    <span class="text-xs font-bold text-black" x-text="kaos.label"></span>
                                </div>

                                <div class="mt-3 overflow-hidden rounded-lg cursor-pointer group relative"
                                     @click.stop="openModalGallery(kaos.photos?.length ? kaos.photos : [kaos.image], 0, kaos.label)">
                                    <img :src="kaos.image" class="h-48 w-full object-cover transition-transform duration-300 group-hover:scale-105">
                                    <div class="absolute inset-0 bg-black/0 transition-colors group-hover:bg-black/20 flex items-center justify-center opacity-0 group-hover:opacity-100">
                                        <span class="rounded-full bg-white/90 p-2 text-black shadow-md">
                                            <x-solar-magnifer-zoom-in-linear class="h-4 w-4" />
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>

                <!-- Section Multi-Ukuran Kaos (Counter Tiap Ukuran) -->
                <div>
                    <div class="mb-3 flex items-center justify-between">
                        <h3 class="text-sm font-bold text-primary-1">Pilih Ukuran & Jumlah Kaos (Termasuk Keluarga)</h3>
                        <span class="rounded-full bg-primary-3 px-3 py-1 text-xs font-bold text-primary-1"
                              x-text="'Total: ' + totalKaos() + ' Kaos'"></span>
                    </div>

                    <div class="grid grid-cols-2 gap-3 sm:grid-cols-4">
                        <template x-for="uk in ukuranTersedia" :key="uk.id">
                            <div :class="hitungJumlah(uk.id) > 0 ? 'is-active' : ''"
                                 class="card-stepper flex items-center justify-between p-3">
                                <div>
                                    <span class="block text-sm font-bold text-black" x-text="uk.kode"></span>
                                    <span class="text-[10px] text-grey-deep" x-text="hitungJumlah(uk.id) > 0 ? hitungJumlah(uk.id) + ' pcs' : '0 pcs'"></span>
                                </div>

                                <div class="flex items-center gap-1.5">
                                    <button type="button"
                                            @click="kurangUkuran(uk.id)"
                                            :disabled="hitungJumlah(uk.id) === 0"
                                            class="flex h-7 w-7 items-center justify-center rounded-lg border border-grey-1 bg-white text-xs font-bold text-black hover:bg-gray-100 disabled:opacity-30 disabled:pointer-events-none cursor-pointer">
                                        -
                                    </button>
                                    <span class="w-5 text-center text-xs font-bold text-primary-1" x-text="hitungJumlah(uk.id)"></span>
                                    <button type="button"
                                            @click="tambahUkuran(uk.id, uk.kode)"
                                            class="flex h-7 w-7 items-center justify-center rounded-lg bg-primary-1 text-xs font-bold text-white shadow-xs hover:bg-primary-4 cursor-pointer">
                                        +
                                    </button>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>
            </div>

            <!-- ========================================== -->
            <!-- TAHAP 4: HASIL AKHIR (Ringkasan & Submit)  -->
            <!-- ========================================== -->
            <div x-show="step === 4" x-cloak class="space-y-6">
                <!-- Header Tahap dengan Navigasi Icon Prev & Tombol Kirim di Atas Kanan -->
                <div class="flex items-center justify-between border-b border-gray-100 pb-4">
                    <div class="flex items-center gap-3">
                        <span class="rounded-lg bg-primary-3 px-3 py-1 text-xs font-bold text-primary-1">4/4</span>
                        <h2 class="text-xl font-bold tracking-tight text-primary-1">Hasil Akhir</h2>
                    </div>

                    <!-- Tombol Navigasi: Back + Kirim Suara Saya di Atas -->
                    <div class="flex items-center gap-2.5">
                        <button type="button"
                                @click="step = 3"
                                aria-label="Sebelumnya"
                                class="flex h-10 w-10 items-center justify-center rounded-xl border border-grey-1 bg-white text-grey-dark shadow-xs transition-all hover:border-primary-1 hover:bg-primary-3/30 hover:text-primary-1 cursor-pointer">
                            <x-solar-arrow-left-linear class="h-4 w-4" />
                        </button>
                        <button type="button"
                                @click="submitVote()"
                                :disabled="submitting"
                                class="inline-flex items-center gap-2 rounded-xl bg-gradient-to-r from-secondary-1 to-primary-1 px-5 sm:px-6 py-2.5 text-xs font-semibold text-white shadow-primary-glow transition-all hover:opacity-95 disabled:opacity-60 cursor-pointer">
                            <x-solar-check-circle-bold class="h-4 w-4" />
                            <span x-text="submitting ? 'Menyimpan...' : 'Kirim Suara Saya'"></span>
                        </button>
                    </div>
                </div>

                <!-- Box Dotted Review -->
                <div class="rounded-2xl border-2 border-dashed border-gray-200 p-6 space-y-6">
                    
                    <!-- Row 1: Peserta -->
                    <div class="flex items-start gap-6">
                        <div class="w-36 shrink-0">
                            <span class="inline-block rounded-lg bg-primary-1 px-4 py-2 text-xs font-bold text-white shadow-xs">
                                Peserta
                            </span>
                        </div>
                        <div class="rounded-xl border border-gray-100 bg-gray-50/70 p-3.5 text-xs text-black">
                            Dosen dan Tenaga Didik DTEDI <strong class="capitalize" x-text="pilihanPeserta"></strong>.
                        </div>
                    </div>

                    <!-- Row 2: Paket Wisata -->
                    <div class="flex items-start gap-6">
                        <div class="w-36 shrink-0">
                            <span class="inline-block rounded-lg bg-primary-1 px-4 py-2 text-xs font-bold text-white shadow-xs">
                                Paket Wisata
                            </span>
                        </div>
                        <!-- Card Mini Tempat Wisata Terpilih -->
                        <div class="flex-1 max-w-sm rounded-xl border border-grey-1 bg-white p-4 shadow-xs">
                            <h4 class="text-sm font-bold text-black" x-text="selectedWisataObj.nama ?? '—'"></h4>
                            <img :src="selectedWisataObj.image ?? ''" class="mt-2 h-28 w-full rounded-lg object-cover">
                            <div class="mt-3 space-y-1 text-[11px]">
                                <p class="text-grey-deep" x-text="selectedWisataObj.lokasi ?? '—'"></p>
                                <p class="font-semibold text-primary-1" x-text="selectedWisataObj.harga ? 'Harga: ' + selectedWisataObj.harga : ''"></p>
                            </div>
                        </div>
                    </div>

                    <!-- Row 3: Kaos & Daftar Ukuran Keluarga -->
                    <div class="flex items-start gap-6">
                        <div class="w-36 shrink-0">
                            <span class="inline-block rounded-lg bg-primary-1 px-4 py-2 text-xs font-bold text-white shadow-xs">
                                Kaos
                            </span>
                        </div>
                        <div class="flex flex-wrap items-center gap-4">
                            <!-- Card Kaos -->
                            <div class="rounded-xl border border-grey-1 bg-white p-3 text-center shadow-xs">
                                <span class="text-xs font-bold text-black" x-text="selectedKaosObj.label ?? '—'"></span>
                                <div class="mt-2 h-24 w-24 overflow-hidden rounded-md">
                                    <img :src="selectedKaosObj.image ?? ''" class="h-full w-full object-cover">
                                </div>
                            </div>

                            <!-- List Ukuran Terpilih -->
                            <div class="space-y-2">
                                <span class="text-[11px] font-bold text-grey-deep">Daftar Ukuran Dipesan:</span>
                                <div class="flex flex-wrap gap-2">
                                    <template x-for="item in keranjangKaos" :key="item.id_ukuran">
                                        <div class="flex items-center gap-1.5 rounded-xl border-2 border-primary-1 bg-primary-3/30 px-3 py-1.5 text-xs font-bold text-primary-1">
                                            <span x-text="item.kode_ukuran"></span>
                                            <span class="rounded bg-primary-1 px-1.5 py-0.5 text-[10px] text-white" x-text="item.jumlah + 'x'"></span>
                                        </div>
                                    </template>
                                    <template x-if="keranjangKaos.length === 0">
                                        <span class="text-xs text-grey-2 italic">Belum ada ukuran dipilih</span>
                                    </template>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>

                <!-- Error Submit (Bila terjadi kesalahan validasi/koneksi) -->
                <div x-show="submitError" x-cloak class="mt-4 rounded-xl border border-danger/20 bg-red-50 px-4 py-3 text-xs font-medium text-danger">
                    <div class="flex items-center gap-2">
                        <x-solar-danger-triangle-bold class="h-4 w-4 shrink-0" />
                        <span x-text="submitError"></span>
                    </div>
                </div>
            </div>

            <!-- ========================================== -->
            <!-- TAHAP 5: SUKSES PENDAFTARAN                -->
            <!-- ========================================== -->
            <div x-show="step === 5" x-cloak class="space-y-6 py-12 text-center">
                <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-full bg-success-soft">
                    <x-solar-check-circle-bold class="h-10 w-10 text-success" />
                </div>

                <h2 class="text-2xl font-bold tracking-tight text-primary-1">Pendaftaran Selesai!</h2>

                <p class="mx-auto max-w-md text-xs text-grey-deep leading-relaxed">
                    Pilihan voting Anda dan ukuran kaos keluarga telah berhasil tercatat di sistem Vonic DTEDI SV UGM. Terima kasih atas partisipasi Anda!
                </p>

                <div class="pt-4">
                    <a href="{{ route('home') }}"
                       class="inline-flex items-center gap-2 rounded-xl border border-primary-1 bg-white px-6 py-2.5 text-xs font-semibold text-primary-1 transition-all hover:bg-primary-3">
                        <x-solar-arrow-right-linear class="h-4 w-4" />
                        <span>Kembali ke Beranda</span>
                    </a>
                </div>
            </div>

            </div> <!-- Penutup div x-show="!loading && !loadError && !sudahVote" -->

        </div>

    </div> <!-- Penutup Paper Canvas -->

    <!-- ========================================== -->
    <!-- MODAL LIGHTBOX SIMPLE: < > dan X           -->
    <!-- ========================================== -->
    <div x-show="modalGalleryOpen" x-cloak
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/85 backdrop-blur-xs transition-opacity"
         @keydown.escape.window="closeModalGallery()"
         @keydown.arrow-left.window="prevFoto()"
         @keydown.arrow-right.window="nextFoto()">
        
        <!-- Backdrop Click -->
        <div class="fixed inset-0" @click="closeModalGallery()"></div>

        <div class="relative z-10 max-w-4xl w-full flex items-center justify-center select-none" @click.stop>
            <!-- Tombol Tutup (X) -->
            <button type="button"
                    @click="closeModalGallery()"
                    aria-label="Tutup"
                    class="absolute -top-12 right-0 flex h-9 w-9 items-center justify-center rounded-full bg-white/15 text-white hover:bg-white/30 transition-colors cursor-pointer">
                <x-solar-close-circle-linear class="h-6 w-6" />
            </button>

            <!-- Frame Gambar -->
            <div class="relative w-full h-[75vh] max-h-[560px] flex items-center justify-center overflow-hidden rounded-xl bg-black">
                <img :src="modalGalleryList[modalGalleryIndex]"
                     alt="Preview Foto"
                     class="max-h-full max-w-full object-contain">

                <!-- Tombol Navigasi Kiri -->
                <template x-if="modalGalleryList.length > 1">
                    <button type="button"
                            @click.stop="prevFoto()"
                            aria-label="Sebelumnya"
                            class="absolute left-3 top-1/2 -translate-y-1/2 flex h-10 w-10 items-center justify-center rounded-full bg-black/60 text-white hover:bg-primary-1 transition-all shadow-md cursor-pointer">
                        <x-solar-alt-arrow-left-linear class="h-5 w-5" />
                    </button>
                </template>

                <!-- Tombol Navigasi Kanan -->
                <template x-if="modalGalleryList.length > 1">
                    <button type="button"
                            @click.stop="nextFoto()"
                            aria-label="Selanjutnya"
                            class="absolute right-3 top-1/2 -translate-y-1/2 flex h-10 w-10 items-center justify-center rounded-full bg-black/60 text-white hover:bg-primary-1 transition-all shadow-md cursor-pointer">
                        <x-solar-alt-arrow-right-linear class="h-5 w-5" />
                    </button>
                </template>
            </div>
        </div>
    </div>

</div>

<script>
function votingApp() {
    return {
        // Wizard state
        step: 1,
        loading: true,
        loadError: '',
        sudahVote: false,
        submitting: false,
        submitError: '',

        // Pilihan user (Default bersih/clean tanpa pra-pilih)
        pilihanPeserta: null,
        pilihanWisata: null,   // id_paket (integer dari DB)
        pilihanKaos: null,     // id_kaos (integer dari DB)
        periodeId: null,

        // Data dari API
        wisataList: [],
        kaosList: [],
        ukuranTersedia: [],
        keranjangKaos: [],

        // Filter state
        filterJarak: 'none',
        filterDurasi: 'none',
        filterDaerah: 'semua',
        filterKategori: 'semua',
        openDaerahDropdown: false,
        openKategoriDropdown: false,
        daftarProvinsi: ['Semua Daerah'],
        daftarKategori: ['Semua Kategori'],

        // Modal Slider Galeri Foto Interaktif
        modalGalleryOpen: false,
        modalGalleryList: [],
        modalGalleryIndex: 0,
        modalGalleryTitle: '',

        openModalGallery(photos, startIndex = 0, title = 'Galeri Foto') {
            this.modalGalleryList = Array.isArray(photos) && photos.length ? photos : ['https://images.unsplash.com/photo-1507525428034-b723cf961d3e?w=600&auto=format&fit=crop&q=80'];
            this.modalGalleryIndex = startIndex >= 0 && startIndex < this.modalGalleryList.length ? startIndex : 0;
            this.modalGalleryTitle = title;
            this.modalGalleryOpen = true;
        },
        closeModalGallery() {
            this.modalGalleryOpen = false;
        },
        nextFoto() {
            if (this.modalGalleryList.length > 1) {
                this.modalGalleryIndex = (this.modalGalleryIndex + 1) % this.modalGalleryList.length;
            }
        },
        prevFoto() {
            if (this.modalGalleryList.length > 1) {
                this.modalGalleryIndex = (this.modalGalleryIndex - 1 + this.modalGalleryList.length) % this.modalGalleryList.length;
            }
        },

        async init() {
            const token = localStorage.getItem('vonic_token');
            // Guard dinonaktifkan sementara untuk preview tampilan
            // if (!token) {
            //     window.location.href = '{{ route('login') }}';
            //     return;
            // }

            this.loading = true;
            this.loadError = '';

            // Ambil periode dari query string atau cari yang aktif
            const params = new URLSearchParams(window.location.search);
            let pid = params.get('periode');

            const authHeaders = token
                ? { 'Accept': 'application/json', 'Authorization': 'Bearer ' + token }
                : { 'Accept': 'application/json' };

            try {
                if (!pid) {
                    // Cari periode aktif
                    const listRes = await fetch('/api/polling', { headers: authHeaders });
                    if (!listRes.ok) throw { status: listRes.status };
                    const listJson = await listRes.json();
                    const aktif = (listJson.data ?? []).find(p => p.status === 'aktif');
                    if (!aktif) {
                        this.loadError = 'Saat ini tidak ada periode polling yang sedang aktif.';
                        return;
                    }
                    pid = aktif.id_periode;
                }
                this.periodeId = pid;

                // Cek apakah user sudah vote pada periode ini (hanya jika user memiliki token login)
                if (token) {
                    try {
                        const riwRes = await fetch('/api/riwayat-voting', { headers: authHeaders });
                        if (riwRes.ok) {
                            const riwJson = await riwRes.json();
                            const riwayat = riwJson.data ?? [];
                            if (riwayat.some(r => String(r.id_periode) === String(pid))) {
                                this.sudahVote = true;
                                return;
                            }
                        }
                    } catch(e) {
                        console.warn('Gagal cek riwayat vote:', e);
                    }
                }

                const res = await fetch('/api/polling/' + pid + '/pilihan', { headers: authHeaders });
                if (!res.ok) throw { status: res.status };
                const json = await res.json();
                const data = json.data;
                const periode = data?.periode;

                if (!periode) {
                    this.loadError = 'Informasi periode polling tidak ditemukan.';
                    return;
                }

                // Update page title
                document.querySelectorAll('[data-periode-title]').forEach(el => {
                    el.textContent = periode.nama_periode;
                });

                // Helper untuk parse foto yang bisa berupa array, json string, atau string tunggal
                const parseFoto = (fotoField, defaultUrl) => {
                    if (!fotoField) return [defaultUrl];
                    if (Array.isArray(fotoField)) return fotoField.length ? fotoField : [defaultUrl];
                    if (typeof fotoField === 'string') {
                        if (fotoField.startsWith('[') && fotoField.endsWith(']')) {
                            try {
                                const parsed = JSON.parse(fotoField);
                                if (Array.isArray(parsed) && parsed.length) return parsed;
                            } catch(e) {}
                        }
                        if (fotoField.includes(',')) {
                            return fotoField.split(',').map(s => s.trim()).filter(Boolean);
                        }
                        return [fotoField];
                    }
                    return [defaultUrl];
                };

                // Paket Wisata
                this.wisataList = (periode.paket_wisata ?? []).map(p => {
                    const defaultImg = 'https://images.unsplash.com/photo-1507525428034-b723cf961d3e?w=600&auto=format&fit=crop&q=80';
                    const mainPhotos = parseFoto(p.foto_url || p.foto, defaultImg);
                    const galeriPhotos = Array.isArray(p.galeri) ? p.galeri.map(g => g.foto_url || g.foto).filter(Boolean) : [];
                    const photos = [...new Set([...mainPhotos, ...galeriPhotos])];
                    return {
                        id: p.id_paket,
                        nama: p.nama_destinasi,
                        provinsi: p.provinsi ?? '—',
                        kategori: p.kategori ?? '—',
                        lokasi: p.lokasi ?? '—',
                        jarakKm: parseFloat(p.jarak) || 0,
                        jarak: (p.jarak ? p.jarak + ' Km' : '—'),
                        durasiHari: parseFloat(p.durasi_perjalanan) || 0,
                        durasi: (p.durasi_perjalanan ? p.durasi_perjalanan + ' Jam' : '—'),
                        rating: p.rating ?? '—',
                        dikunjungi: p.total_kunjungan ? p.total_kunjungan + ' kali' : '—',
                        benefit: p.benefit ? (Array.isArray(p.benefit) ? p.benefit : p.benefit.split(',').map(b => b.trim())) : [],
                        harga: p.harga ? 'Rp ' + Number(p.harga).toLocaleString('id-ID') : '—',
                        photos: photos,
                        image: photos[0] || defaultImg
                    };
                });

                // Desain Kaos
                this.kaosList = (periode.desain_kaos ?? []).map(k => {
                    const defaultImg = 'https://images.unsplash.com/photo-1521572267360-ee0c2909d518?w=600&auto=format&fit=crop&q=80';
                    const mainPhotos = parseFoto(k.foto_url || k.foto, defaultImg);
                    const galeriPhotos = Array.isArray(k.galeri) ? k.galeri.map(g => g.foto_url || g.foto).filter(Boolean) : [];
                    const photos = [...new Set([...mainPhotos, ...galeriPhotos])];
                    return {
                        id: k.id_kaos,
                        label: k.nama_desain,
                        photos: photos,
                        image: photos[0] || defaultImg
                    };
                });

                // Ukuran Kaos
                this.ukuranTersedia = (data.ukuran_kaos ?? []).map(u => ({
                    id: u.id_ukuran,
                    kode: u.kode_ukuran
                }));

                // Build filter options dinamis dari data
                const provinsiSet = new Set(this.wisataList.map(w => w.provinsi).filter(Boolean));
                this.daftarProvinsi = ['Semua Daerah', ...provinsiSet];
                const kategoriSet = new Set(this.wisataList.map(w => w.kategori).filter(Boolean));
                this.daftarKategori = ['Semua Kategori', ...kategoriSet];

            } catch(e) {
                if (e && e.status === 404) {
                    this.loadError = 'Periode polling tidak ditemukan atau telah dihapus.';
                } else if (e instanceof TypeError) {
                    this.loadError = 'Koneksi gagal. Pastikan perangkat Anda terhubung ke jaringan.';
                } else {
                    this.loadError = 'Gagal memuat data polling. Silakan coba beberapa saat lagi.';
                }
            } finally {
                this.loading = false;
            }
        },

        // Filter computed
        get filteredWisataList() {
            let result = [...this.wisataList];
            if (this.filterDaerah !== 'semua' && this.filterDaerah !== 'Semua Daerah') {
                result = result.filter(w => w.provinsi === this.filterDaerah);
            }
            if (this.filterKategori !== 'semua' && this.filterKategori !== 'Semua Kategori') {
                result = result.filter(w => w.kategori === this.filterKategori);
            }
            if (this.filterJarak === 'dekat') result.sort((a, b) => a.jarakKm - b.jarakKm);
            else if (this.filterJarak === 'jauh') result.sort((a, b) => b.jarakKm - a.jarakKm);
            if (this.filterDurasi === 'singkat') result.sort((a, b) => a.durasiHari - b.durasiHari);
            else if (this.filterDurasi === 'lama') result.sort((a, b) => b.durasiHari - a.durasiHari);
            return result;
        },

        get selectedWisataObj() {
            return this.wisataList.find(w => w.id === this.pilihanWisata) || (this.wisataList[0] ?? {});
        },

        get selectedKaosObj() {
            return this.kaosList.find(k => k.id === this.pilihanKaos) || (this.kaosList[0] ?? {});
        },

        // Kaos counter helpers
        tambahUkuran(ukuranId, kode) {
            let existing = this.keranjangKaos.find(item => item.id_ukuran === ukuranId);
            if (existing) { existing.jumlah++; }
            else { this.keranjangKaos.push({ id_ukuran: ukuranId, kode_ukuran: kode, jumlah: 1 }); }
        },
        kurangUkuran(ukuranId) {
            let idx = this.keranjangKaos.findIndex(item => item.id_ukuran === ukuranId);
            if (idx !== -1) {
                if (this.keranjangKaos[idx].jumlah > 1) this.keranjangKaos[idx].jumlah--;
                else this.keranjangKaos.splice(idx, 1);
            }
        },
        hitungJumlah(ukuranId) {
            let item = this.keranjangKaos.find(k => k.id_ukuran === ukuranId);
            return item ? item.jumlah : 0;
        },
        totalKaos() {
            return this.keranjangKaos.reduce((t, item) => t + item.jumlah, 0);
        },

        resetFilter() {
            this.filterJarak = 'none';
            this.filterDurasi = 'none';
            this.filterDaerah = 'semua';
            this.filterKategori = 'semua';
        },
        toggleFilterJarak() {
            if (this.filterJarak === 'none') this.filterJarak = 'dekat';
            else if (this.filterJarak === 'dekat') this.filterJarak = 'jauh';
            else this.filterJarak = 'none';
        },
        toggleFilterDurasi() {
            if (this.filterDurasi === 'none') this.filterDurasi = 'singkat';
            else if (this.filterDurasi === 'singkat') this.filterDurasi = 'lama';
            else this.filterDurasi = 'none';
        },

        async submitVote() {
            if (!this.pilihanWisata || !this.pilihanKaos) {
                this.submitError = 'Pilih paket wisata dan desain kaos terlebih dahulu.';
                return;
            }
            const token = localStorage.getItem('vonic_token');
            if (!token) {
                // Preview mode: simulasi sukses submit jika belum login
                alert('Mode Preview: Voting berhasil disimulasikan (User belum login).');
                this.step = 5;
                return;
            }

            // Siapkan format items_kaos sekaligus id_ukuran utama untuk backward & forward compatibility
            const itemsKaosPayload = this.keranjangKaos.map(item => ({
                id_ukuran: item.id_ukuran,
                jumlah: item.jumlah,
                kode_ukuran: item.kode_ukuran || ''
            }));

            // Ambil id_ukuran pertama dari keranjang (fallback untuk backend versi legacy)
            const ukuranUtama = this.keranjangKaos.length > 0
                ? this.keranjangKaos[0].id_ukuran
                : (this.ukuranTersedia[0]?.id ?? 1);

            this.submitting = true;
            this.submitError = '';
            try {
                // Dual-payload: kirim id_ukuran & items_kaos agar updateable kapan pun BE berubah
                const res = await fetch('/api/polling/' + this.periodeId + '/vote', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'Authorization': 'Bearer ' + token
                    },
                    body: JSON.stringify({
                        id_paket: this.pilihanWisata,
                        id_kaos: this.pilihanKaos,
                        id_ukuran: ukuranUtama,
                        items_kaos: itemsKaosPayload,
                        pilihan_kelompok: this.pilihanPeserta,
                    })
                });
                const resData = await res.json().catch(() => ({}));
                if (!res.ok) {
                    throw { status: res.status, data: resData };
                }
                this.step = 5;
            } catch(err) {
                if (err instanceof TypeError) {
                    this.submitError = 'Koneksi terputus. Pastikan perangkat Anda terhubung ke internet lalu coba lagi.';
                } else if (err.status === 409) {
                    this.submitError = 'Anda sudah pernah memberikan suara pada periode polling ini.';
                } else if (err.status === 422) {
                    this.submitError = err.data?.message || 'Data pilihan voting belum lengkap. Mohon periksa kembali.';
                } else {
                    this.submitError = err.data?.message || 'Gagal menyimpan suara. Terjadi kesalahan pada server.';
                }
            } finally {
                this.submitting = false;
            }
        }
    };
}
</script>
@endsection
