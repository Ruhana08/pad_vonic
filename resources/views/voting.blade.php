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
        <div class="min-h-full w-full rounded-t-[28px] rounded-b-none border-x border-t border-gray-200/80 bg-white p-10 pb-36 shadow-type-2">

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
                <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                    <p class="text-xs text-grey-deep">Silakan memilih paket wisata yang diinginkan.</p>

                    <!-- Filter Pill Bar -->
                    <div class="flex flex-wrap items-center gap-2">
                        
                        <!-- Filter Jarak (Toggle: Terdekat / Terjauh) -->
                        <button type="button"
                                @click="toggleFilterJarak()"
                                :class="filterJarak !== 'none' ? 'border-primary-1 bg-primary-3 text-primary-1 font-bold' : 'border-grey-1 bg-white text-grey-deep'"
                                class="inline-flex items-center gap-1.5 rounded-lg border px-3 py-1.5 text-xs transition-colors hover:border-primary-1 cursor-pointer">
                            <span>Jarak</span>
                            <!-- Icon visual toggle naik / turun -->
                            <span class="flex flex-col leading-none text-[9px]">
                                <span :class="filterJarak === 'jauh' ? 'font-black text-primary-1 text-[11px]' : 'opacity-40'">▲</span>
                                <span :class="filterJarak === 'dekat' ? 'font-black text-primary-1 text-[11px]' : 'opacity-40'">▼</span>
                            </span>
                        </button>

                        <!-- Filter Lama Perjalanan (Toggle: Singkat / Lama) -->
                        <button type="button"
                                @click="toggleFilterDurasi()"
                                :class="filterDurasi !== 'none' ? 'border-primary-1 bg-primary-3 text-primary-1 font-bold' : 'border-grey-1 bg-white text-grey-deep'"
                                class="inline-flex items-center gap-1.5 rounded-lg border px-3 py-1.5 text-xs transition-colors hover:border-primary-1 cursor-pointer">
                            <span>Lama Perjalanan</span>
                            <span class="flex flex-col leading-none text-[9px]">
                                <span :class="filterDurasi === 'lama' ? 'font-black text-primary-1 text-[11px]' : 'opacity-40'">▲</span>
                                <span :class="filterDurasi === 'singkat' ? 'font-black text-primary-1 text-[11px]' : 'opacity-40'">▼</span>
                            </span>
                        </button>

                        <!-- Dropdown Filter Daerah (Provinsi) -->
                        <div class="relative" @click.outside="openDaerahDropdown = false">
                            <button type="button"
                                    @click="openDaerahDropdown = !openDaerahDropdown"
                                    :class="filterDaerah !== 'semua' && filterDaerah !== 'Semua Daerah' ? 'border-primary-1 bg-primary-3 text-primary-1 font-bold' : 'border-grey-1 bg-white text-grey-deep'"
                                    class="inline-flex items-center gap-1.5 rounded-lg border px-3 py-1.5 text-xs transition-colors hover:border-primary-1 cursor-pointer">
                                <span x-text="filterDaerah === 'semua' ? 'Daerah' : filterDaerah"></span>
                                <x-solar-alt-arrow-down-linear class="h-3.5 w-3.5" />
                            </button>
                            <div x-show="openDaerahDropdown" x-cloak
                                 class="absolute right-0 top-full z-30 mt-1 max-h-48 w-44 overflow-y-auto rounded-xl border border-gray-100 bg-white p-1 shadow-lg">
                                <template x-for="p in daftarProvinsi" :key="p">
                                    <button type="button"
                                            @click="filterDaerah = (p === 'Semua Daerah' ? 'semua' : p); openDaerahDropdown = false"
                                            :class="(filterDaerah === p || (p === 'Semua Daerah' && filterDaerah === 'semua')) ? 'bg-primary-3 text-primary-1 font-bold' : 'text-black hover:bg-gray-50'"
                                            class="w-full rounded-md px-3 py-1.5 text-left text-xs">
                                        <span x-text="p"></span>
                                    </button>
                                </template>
                            </div>
                        </div>

                        <!-- Dropdown Filter Kategori -->
                        <div class="relative" @click.outside="openKategoriDropdown = false">
                            <button type="button"
                                    @click="openKategoriDropdown = !openKategoriDropdown"
                                    :class="filterKategori !== 'semua' && filterKategori !== 'Semua Kategori' ? 'border-primary-1 bg-primary-3 text-primary-1 font-bold' : 'border-grey-1 bg-white text-grey-deep'"
                                    class="inline-flex items-center gap-1.5 rounded-lg border px-3 py-1.5 text-xs transition-colors hover:border-primary-1 cursor-pointer">
                                <span x-text="filterKategori === 'semua' ? 'Kategori' : filterKategori" class="max-w-28 truncate"></span>
                                <x-solar-alt-arrow-down-linear class="h-3.5 w-3.5" />
                            </button>
                            <div x-show="openKategoriDropdown" x-cloak
                                 class="absolute right-0 top-full z-30 mt-1 max-h-52 w-60 overflow-y-auto rounded-xl border border-gray-100 bg-white p-1 shadow-lg">
                                <template x-for="k in daftarKategori" :key="k">
                                    <button type="button"
                                            @click="filterKategori = (k === 'Semua Kategori' ? 'semua' : k); openKategoriDropdown = false"
                                            :class="(filterKategori === k || (k === 'Semua Kategori' && filterKategori === 'semua')) ? 'bg-primary-3 text-primary-1 font-bold' : 'text-black hover:bg-gray-50'"
                                            class="w-full rounded-md px-3 py-1.5 text-left text-xs truncate">
                                        <span x-text="k"></span>
                                    </button>
                                </template>
                            </div>
                        </div>

                        <!-- Reset Filter Button (Silang) -->
                        <template x-if="filterJarak !== 'none' || filterDurasi !== 'none' || filterDaerah !== 'semua' || filterKategori !== 'semua'">
                            <button type="button"
                                    @click="resetFilter()"
                                    title="Reset Semua Filter"
                                    class="flex h-7 w-7 items-center justify-center rounded-lg border border-danger/30 bg-rose-50 text-danger transition-colors hover:bg-danger hover:text-white cursor-pointer">
                                <x-solar-close-circle-linear class="h-5 w-5" />
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
                                <!-- Radio + Nama Tempat -->
                                <div class="flex items-center gap-2.5">
                                    <input type="radio" :value="item.id" x-model="pilihanWisata" class="h-4 w-4 text-primary-1 focus:ring-primary-1">
                                    <h4 class="text-sm font-bold text-black" x-text="item.nama"></h4>
                                </div>

                                <!-- Galeri Foto Miniatur -->
                                <div class="mt-3 grid grid-cols-3 gap-1.5 overflow-hidden rounded-lg">
                                    <img :src="item.image" class="h-24 w-full object-cover col-span-2 rounded-l-md">
                                    <div class="flex flex-col gap-1.5">
                                        <img :src="item.image" class="h-[45px] w-full object-cover rounded-tr-md">
                                        <div class="flex h-[45px] w-full items-center justify-center rounded-br-md bg-gray-200 text-xs font-semibold text-grey-deep">
                                            +2
                                        </div>
                                    </div>
                                </div>

                                <!-- Rating & Riwayat Kunjungan -->
                                <div class="mt-3 flex items-center justify-between text-xs">
                                    <span class="flex items-center gap-1 font-semibold text-black">
                                        <x-solar-star-bold class="h-3.5 w-3.5 text-amber-400" />
                                        <span x-text="item.rating"></span>
                                    </span>
                                    <span class="text-primary-1 text-[11px] font-medium" x-text="'Dikunjungi: ' + item.dikunjungi"></span>
                                </div>

                                <!-- Metadata Lokasi & Jarak -->
                                <div class="mt-3 space-y-1 text-[11px]">
                                    <div class="flex items-start gap-2">
                                        <span class="w-16 shrink-0 rounded bg-gray-100 px-1.5 py-0.5 font-medium text-grey-deep text-center">Lokasi</span>
                                        <span class="text-black truncate" x-text="item.lokasi"></span>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <span class="w-16 shrink-0 rounded bg-gray-100 px-1.5 py-0.5 font-medium text-grey-deep text-center">Jarak</span>
                                        <span class="text-black" x-text="item.jarak"></span>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <span class="w-16 shrink-0 rounded bg-gray-100 px-1.5 py-0.5 font-medium text-grey-deep text-center">Lama Tour</span>
                                        <span class="text-black" x-text="item.durasi"></span>
                                    </div>
                                    <div class="flex items-start gap-2 pt-1">
                                        <span class="w-16 shrink-0 rounded bg-gray-100 px-1.5 py-0.5 font-medium text-grey-deep text-center">Benefit</span>
                                        <div class="text-black text-[10px]">
                                            <ul class="list-disc pl-3">
                                                <template x-for="b in item.benefit">
                                                    <li x-text="b"></li>
                                                </template>
                                            </ul>
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

                                <div class="mt-3 overflow-hidden rounded-lg">
                                    <img :src="kaos.image" class="h-48 w-full object-cover">
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
                                 class="card-stepper flex items-center justify-between">
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
                <!-- Header Tahap dengan Navigasi Icon di Kanan (Sesuai Figma) -->
                <div class="flex items-center justify-between border-b border-gray-100 pb-4">
                    <div class="flex items-center gap-3">
                        <span class="rounded-lg bg-primary-3 px-3 py-1 text-xs font-bold text-primary-1">4/4</span>
                        <h2 class="text-xl font-bold tracking-tight text-primary-1">Hasil Akhir</h2>
                    </div>

                    <!-- Tombol Navigasi Ikon (Atas) -->
                    <div class="flex items-center gap-2">
                        <button type="button"
                                @click="step = 3"
                                aria-label="Sebelumnya"
                                class="flex h-10 w-10 items-center justify-center rounded-xl border border-grey-1 bg-white text-grey-dark shadow-xs transition-all hover:border-primary-1 hover:bg-primary-3/30 hover:text-primary-1 cursor-pointer">
                            <x-solar-arrow-left-linear class="h-4 w-4" />
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

                <!-- Tombol Submit (hanya kiri prev, kanan submit) -->
                <div class="flex items-center justify-between pt-8 border-t border-gray-100">
                    <button type="button"
                            @click="step = 3"
                            class="flex h-10 w-10 items-center justify-center rounded-xl border border-grey-1 bg-white text-grey-dark shadow-xs transition-all hover:border-primary-1 hover:bg-primary-3/30 hover:text-primary-1 cursor-pointer">
                        <x-solar-arrow-left-linear class="h-4 w-4" />
                    </button>
                    <button type="button"
                            @click="submitVote()"
                            :disabled="submitting"
                            class="inline-flex items-center gap-2 rounded-xl bg-gradient-to-r from-secondary-1 to-primary-1 px-7 py-2.5 text-xs font-semibold text-white shadow-primary-glow transition-all hover:opacity-95 disabled:opacity-60 cursor-pointer">
                        <x-solar-check-circle-bold class="h-4 w-4" />
                        <span x-text="submitting ? 'Menyimpan...' : 'Kirim Suara Saya'"></span>
                    </button>
                </div>

                <!-- Error Submit -->
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
                        <span>Kembali ke Beranda</span>
                        <x-solar-arrow-right-linear class="h-4 w-4" />
                    </a>
                </div>
            </div>

            </div> <!-- Penutup div x-show="!loading && !loadError && !sudahVote" -->

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

        async init() {
            const token = localStorage.getItem('vonic_token');
            // Guard dinonaktifkan sementara untuk preview tampilan
            // if (!token) {
            //     window.location.href = '{{ route('user.login') }}';
            //     return;
            // }

            this.loading = true;
            this.loadError = '';

            // Ambil periode dari query string atau cari yang aktif
            const params = new URLSearchParams(window.location.search);
            let pid = params.get('periode');

            try {
                if (!pid) {
                    // Cari periode aktif
                    const listRes = await axios.get('/api/polling');
                    const aktif = (listRes.data.data ?? []).find(p => p.status === 'aktif');
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
                        const riwRes = await axios.get('/api/riwayat-voting');
                        const riwayat = riwRes.data.data ?? [];
                        if (riwayat.some(r => String(r.id_periode) === String(pid))) {
                            this.sudahVote = true;
                            return;
                        }
                    } catch(e) {
                        console.warn('Gagal cek riwayat vote:', e);
                    }
                }

                const res = await axios.get('/api/polling/' + pid + '/pilihan');
                const data = res.data.data;
                const periode = data?.periode;

                if (!periode) {
                    this.loadError = 'Informasi periode polling tidak ditemukan.';
                    return;
                }

                // Update page title
                document.querySelectorAll('[data-periode-title]').forEach(el => {
                    el.textContent = periode.nama_periode;
                });

                // Paket Wisata
                this.wisataList = (periode.paket_wisata ?? []).map(p => ({
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
                    benefit: p.benefit ? p.benefit.split(',').map(b => b.trim()) : [],
                    harga: p.harga ? 'Rp ' + Number(p.harga).toLocaleString('id-ID') : '—',
                    image: p.foto ?? 'https://images.unsplash.com/photo-1507525428034-b723cf961d3e?w=600&auto=format&fit=crop&q=80'
                }));

                // Desain Kaos
                this.kaosList = (periode.desain_kaos ?? []).map(k => ({
                    id: k.id_kaos,
                    label: k.nama_desain,
                    image: k.foto ?? 'https://images.unsplash.com/photo-1521572267360-ee0c2909d518?w=600&auto=format&fit=crop&q=80'
                }));

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
                if (!e.response) {
                    this.loadError = 'Koneksi gagal. Pastikan perangkat Anda terhubung ke jaringan.';
                } else if (e.response.status === 404) {
                    this.loadError = 'Periode polling tidak ditemukan atau telah dihapus.';
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

            // Ambil id_ukuran pertama dari keranjang (BE saat ini hanya support 1)
            const ukuranUtama = this.keranjangKaos.length > 0
                ? this.keranjangKaos[0].id_ukuran
                : (this.ukuranTersedia[0]?.id ?? 1);

            this.submitting = true;
            this.submitError = '';
            try {
                await axios.post('/api/polling/' + this.periodeId + '/vote', {
                    id_paket: this.pilihanWisata,
                    id_kaos: this.pilihanKaos,
                    id_ukuran: ukuranUtama,
                    pilihan_kelompok: this.pilihanPeserta,
                }, {
                    headers: { Authorization: 'Bearer ' + token }
                });
                this.step = 5;
            } catch(err) {
                if (!err.response) {
                    this.submitError = 'Koneksi terputus. Pastikan perangkat Anda terhubung ke internet lalu coba lagi.';
                } else if (err.response.status === 409) {
                    this.submitError = 'Anda sudah pernah memberikan suara pada periode polling ini.';
                } else if (err.response.status === 422) {
                    this.submitError = err.response.data?.message || 'Data pilihan voting belum lengkap. Mohon periksa kembali.';
                } else {
                    this.submitError = err.response.data?.message || 'Gagal menyimpan suara. Terjadi kesalahan pada server.';
                }
            } finally {
                this.submitting = false;
            }
        }
    };
}
</script>
@endsection
