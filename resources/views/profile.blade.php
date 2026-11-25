@extends('layouts.focus')

@section('title', 'Profil Saya')

@section('content')
<div class="mx-auto w-full max-w-[1100px] px-6 py-10"
     x-data="profileApp()"
     x-init="init()">

    <!-- Tombol Kembali -->
    <div class="mb-6">
        <a href="{{ route('home') }}"
           class="btn-secondary h-10 px-4 text-xs font-semibold">
            <x-solar-alt-arrow-left-linear class="h-4 w-4" />
            <span>Kembali</span>
        </a>
    </div>

    <!-- Alert Sukses -->
    <div x-show="successMsg" x-cloak x-transition
         class="mb-6 flex items-center gap-3 rounded-xl border border-green-200 bg-green-50 px-5 py-3.5 text-sm font-medium text-green-700 shadow-xs">
        <x-solar-check-circle-bold class="h-5 w-5 shrink-0 text-green-500" />
        <span x-text="successMsg"></span>
    </div>

    <!-- Alert Error -->
    <div x-show="errorMsg" x-cloak x-transition
         class="mb-6 flex items-center gap-3 rounded-xl border border-danger/20 bg-red-50 px-5 py-3.5 text-sm font-medium text-danger shadow-xs">
        <x-solar-danger-triangle-bold class="h-5 w-5 shrink-0" />
        <span x-text="errorMsg"></span>
    </div>

    <!-- Loading State -->
    <div x-show="loading" class="flex items-center justify-center py-20">
        <div class="text-center">
            <div class="mx-auto h-10 w-10 animate-spin rounded-full border-4 border-primary-1 border-t-transparent"></div>
            <p class="mt-4 text-sm text-grey-2">Memuat data profil...</p>
        </div>
    </div>

    <!-- Main Content -->
    <div x-show="!loading" class="grid grid-cols-1 gap-8 lg:grid-cols-2">

        <!-- ═══════════════════════════════════ -->
        <!-- Kolom Kiri: Identitas               -->
        <!-- ═══════════════════════════════════ -->
        <div>
            <div class="mb-5 flex items-center justify-between">
                <h2 class="text-xl font-bold text-primary-1">Identitas</h2>

                <!-- Edit / Simpan Toggle -->
                <template x-if="!editMode">
                    <button type="button"
                            @click="editMode = true"
                            class="btn-primary h-10 px-5 text-xs font-semibold">
                        <x-solar-pen-new-square-linear class="h-4 w-4" />
                        <span>Edit Profil</span>
                    </button>
                </template>
                <template x-if="editMode">
                    <div class="flex gap-2.5">
                        <button type="button"
                                @click="cancelEdit()"
                                class="btn-secondary h-10 px-4 text-xs font-semibold">
                            Batal
                        </button>
                        <button type="button"
                                @click="saveProfile()"
                                :disabled="saving"
                                class="btn-primary h-10 px-5 text-xs font-semibold">
                            <span x-text="saving ? 'Menyimpan...' : 'Simpan'"></span>
                        </button>
                    </div>
                </template>
            </div>

            <!-- Card Identitas -->
            <x-card variant="panel">
                <!-- Avatar -->
                <div class="mb-6 flex flex-col items-center">
                    <div class="relative">
                        <div class="h-24 w-24 overflow-hidden rounded-full border-4 border-primary-1/20 bg-gray-100 shadow-sm">
                            <template x-if="form.fotoPreview || profile.foto">
                                <img :src="form.fotoPreview || profile.foto" alt="Foto profil" class="h-full w-full object-cover" />
                            </template>
                            <template x-if="!form.fotoPreview && !profile.foto">
                                <div class="flex h-full w-full items-center justify-center bg-primary-3">
                                    <x-solar-user-bold class="h-10 w-10 text-primary-1" />
                                </div>
                            </template>
                        </div>

                        <template x-if="editMode">
                            <label class="absolute -bottom-1 -right-1 flex h-8 w-8 cursor-pointer items-center justify-center rounded-full border-2 border-white bg-primary-1 text-white shadow-sm transition-colors hover:bg-primary-4"
                                   title="Ubah Foto Profil">
                                <x-solar-camera-linear class="h-4 w-4" />
                                <input type="file" accept="image/jpeg,image/png,image/webp" class="hidden" @change="handlePhotoChange($event)" />
                            </label>
                        </template>
                    </div>
                    <template x-if="editMode">
                        <p class="mt-2 text-[11px] text-grey-2">Format JPG/PNG, maksimal 2 MB</p>
                    </template>
                </div>

                <!-- Field-field Identitas -->
                <div class="space-y-3">

                    <!-- Nama -->
                    <div class="flex items-center gap-3 rounded-xl border border-gray-100 bg-white shadow-xs overflow-hidden">
                        <div class="flex h-full w-[110px] shrink-0 items-center gap-2 bg-primary-3/50 px-3.5 py-3">
                            <x-solar-user-linear class="h-4 w-4 text-primary-1" />
                            <span class="text-xs font-semibold text-primary-1">Nama</span>
                        </div>
                        <div class="flex-1 py-2 pr-3">
                            <template x-if="!editMode">
                                <span class="text-xs sm:text-sm font-semibold text-black" x-text="profile.nama ?? '—'"></span>
                            </template>
                            <template x-if="editMode">
                                <input type="text" x-model="form.nama" class="h-9 w-full rounded-lg border border-grey-1 px-3 text-xs sm:text-sm text-black outline-none focus:border-primary-1" />
                            </template>
                        </div>
                    </div>

                    <!-- Email (readonly) -->
                    <div class="flex items-center gap-3 rounded-xl border border-gray-100 bg-white shadow-xs overflow-hidden">
                        <div class="flex h-full w-[110px] shrink-0 items-center gap-2 bg-primary-3/50 px-3.5 py-3">
                            <x-solar-letter-linear class="h-4 w-4 text-primary-1" />
                            <span class="text-xs font-semibold text-primary-1">Email</span>
                        </div>
                        <div class="flex-1 py-2 pr-3">
                            <span class="text-xs sm:text-sm font-medium text-black" x-text="profile.email ?? '—'"></span>
                        </div>
                    </div>

                    <!-- Pekerjaan -->
                    <div class="flex items-center gap-3 rounded-xl border border-gray-100 bg-white shadow-xs overflow-hidden">
                        <div class="flex h-full w-[110px] shrink-0 items-center gap-2 bg-primary-3/50 px-3.5 py-3">
                            <x-solar-case-linear class="h-4 w-4 text-primary-1" />
                            <span class="text-xs font-semibold text-primary-1">Pekerjaan</span>
                        </div>
                        <div class="flex-1 py-2 pr-3">
                            <template x-if="!editMode">
                                <span class="text-xs sm:text-sm font-semibold text-black" x-text="profile.pekerjaan ?? '—'"></span>
                            </template>
                            <template x-if="editMode">
                                <input type="text" x-model="form.pekerjaan" class="h-9 w-full rounded-lg border border-grey-1 px-3 text-xs sm:text-sm text-black outline-none focus:border-primary-1" />
                            </template>
                        </div>
                    </div>

                    <!-- Unit Kerja -->
                    <div class="flex items-center gap-3 rounded-xl border border-gray-100 bg-white shadow-xs overflow-hidden">
                        <div class="flex h-full w-[110px] shrink-0 items-center gap-2 bg-primary-3/50 px-3.5 py-3">
                            <x-solar-buildings-linear class="h-4 w-4 text-primary-1" />
                            <span class="text-xs font-semibold text-primary-1">Unit Kerja</span>
                        </div>
                        <div class="flex-1 py-2 pr-3">
                            <template x-if="!editMode">
                                <span class="text-xs sm:text-sm font-semibold text-black" x-text="profile.unit_kerja ?? '—'"></span>
                            </template>
                            <template x-if="editMode">
                                <input type="text" x-model="form.unit_kerja" class="h-9 w-full rounded-lg border border-grey-1 px-3 text-xs sm:text-sm text-black outline-none focus:border-primary-1" />
                            </template>
                        </div>
                    </div>

                    <!-- No. Telp -->
                    <div class="flex items-center gap-3 rounded-xl border border-gray-100 bg-white shadow-xs overflow-hidden">
                        <div class="flex h-full w-[110px] shrink-0 items-center gap-2 bg-primary-3/50 px-3.5 py-3">
                            <x-solar-phone-linear class="h-4 w-4 text-primary-1" />
                            <span class="text-xs font-semibold text-primary-1">No. Telp</span>
                        </div>
                        <div class="flex-1 py-2 pr-3">
                            <template x-if="!editMode">
                                <span class="text-xs sm:text-sm font-semibold text-black" x-text="profile.no_telp ?? '—'"></span>
                            </template>
                            <template x-if="editMode">
                                <input type="tel" x-model="form.no_telp" class="h-9 w-full rounded-lg border border-grey-1 px-3 text-xs sm:text-sm text-black outline-none focus:border-primary-1" />
                            </template>
                        </div>
                    </div>

                </div>
            </x-card>
        </div>

        <!-- ═══════════════════════════════════ -->
        <!-- Kolom Kanan: Riwayat Partisipasi   -->
        <!-- ═══════════════════════════════════ -->
        <div>
            <h2 class="mb-5 text-xl font-bold text-primary-1">Riwayat Partisipasi</h2>

            <x-card variant="panel">
                <!-- Loading riwayat -->
                <div x-show="loadingRiwayat" class="space-y-4">
                    <template x-for="i in 3" :key="i">
                        <div class="flex items-center gap-4">
                            <div class="h-3 w-3 animate-pulse rounded-full bg-gray-200"></div>
                            <div class="flex-1 animate-pulse rounded-xl bg-gray-200 py-6"></div>
                        </div>
                    </template>
                </div>

                <!-- Timeline Riwayat -->
                <div x-show="!loadingRiwayat" class="relative">
                    <!-- Vertical line -->
                    <div x-show="riwayatList.length > 0"
                         class="absolute left-[5px] top-3 h-[calc(100%-24px)] w-0.5 bg-gray-200"></div>

                    <div class="space-y-4">
                        <template x-for="(item, idx) in riwayatList" :key="idx">
                            <div class="flex items-stretch gap-4">
                                <!-- Dot -->
                                <div class="relative z-10 mt-4 flex h-3 w-3 shrink-0 items-center justify-center">
                                    <div class="h-3 w-3 rounded-full bg-primary-1"></div>
                                </div>

                                <!-- Card -->
                                <x-card variant="info-box" class="flex flex-1 items-center justify-between p-3.5">
                                    <div>
                                        <p class="text-xs sm:text-sm font-bold text-black"
                                           x-text="item.periode_polling?.nama_periode ?? 'Periode Polling'"></p>
                                        <p class="mt-0.5 text-[11px] text-grey-2"
                                           x-text="item.waktu_submit ? 'Disimpan: ' + formatWaktu(item.waktu_submit) : ''"></p>
                                    </div>
                                    <button type="button"
                                            @click="bukaModalDetail(item)"
                                            class="btn-primary h-8 px-4 text-xs font-semibold ml-4 shrink-0 cursor-pointer">
                                        Lihat Riwayat
                                    </button>
                                </x-card>
                            </div>
                        </template>
                    </div>

                    <!-- Empty state -->
                    <div x-show="!loadingRiwayat && riwayatList.length === 0"
                         class="flex flex-col items-center justify-center py-12 text-center">
                        <x-solar-folder-with-files-linear class="mb-3 h-10 w-10 text-grey-2" />
                        <p class="text-sm font-semibold text-grey-dark">Belum ada riwayat partisipasi</p>
                        <p class="mt-1 text-xs text-grey-2">Ikuti polling saat aktif untuk melihat riwayat di sini</p>
                    </div>
                </div>
            </x-card>
        </div>

    </div>

    <!-- ═════════════════════════════════════════════════════════════ -->
    <!-- MODAL POPUP: DETAIL PILIHAN SUARA POLING                    -->
    <!-- ═════════════════════════════════════════════════════════════ -->
    <div x-show="modalRiwayatOpen" x-cloak
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-xs transition-opacity"
         @keydown.escape.window="tutupModalDetail()">
        
        <!-- Backdrop -->
        <div class="fixed inset-0" @click="tutupModalDetail()"></div>

        <!-- Box Modal -->
        <div class="relative z-10 w-full max-w-xl bg-white rounded-2xl overflow-hidden shadow-2xl border border-gray-100 animate-in fade-in zoom-in-95 duration-200"
             @click.stop>
            
            <!-- Header Modal -->
            <div class="flex items-center justify-between border-b border-gray-100 px-6 py-4 bg-white">
                <div>
                    <h3 class="text-base font-bold text-black" x-text="detailDipilih?.periode_polling?.nama_periode ?? 'Detail Riwayat Suara'"></h3>
                    <p class="text-xs text-grey-2 mt-0.5">
                        Waktu Submit: <span class="font-medium text-black" x-text="formatWaktu(detailDipilih?.waktu_submit)"></span>
                    </p>
                </div>
                <button type="button"
                        @click="tutupModalDetail()"
                        class="flex h-8 w-8 items-center justify-center rounded-lg bg-gray-100 text-grey-dark transition-colors hover:bg-danger hover:text-white cursor-pointer">
                    <x-solar-close-circle-linear class="h-5 w-5" />
                </button>
            </div>

            <!-- Body Modal (Isi Pilihan Tiap Kategori) -->
            <div class="p-6 space-y-4 max-h-[75vh] overflow-y-auto">
                
                <!-- 1. Kategori Peserta -->
                <div class="rounded-xl border border-gray-200 bg-gray-50/70 p-4">
                    <span class="inline-block rounded-md bg-primary-1 px-2.5 py-1 text-[11px] font-bold text-white mb-2">
                        Pilihan Peserta
                    </span>
                    <p class="text-xs sm:text-sm font-semibold text-black">
                        Kelompok Dosen dan Tendik <strong class="text-primary-1 uppercase" x-text="detailDipilih?.pilihan_kelompok ?? 'dipisah'"></strong>
                    </p>
                </div>

                <!-- 2. Kategori Paket Wisata Terpilih -->
                <div class="rounded-xl border border-gray-200 bg-white p-4 shadow-xs">
                    <span class="inline-block rounded-md bg-primary-1 px-2.5 py-1 text-[11px] font-bold text-white mb-3">
                        Pilihan Paket Wisata
                    </span>
                    <div class="flex items-start gap-4">
                        <template x-if="detailDipilih?.paket_wisata?.foto">
                            <img :src="detailDipilih.paket_wisata.foto" class="h-20 w-28 rounded-lg object-cover shrink-0 border border-gray-100">
                        </template>
                        <div class="space-y-1 min-w-0">
                            <h4 class="text-sm font-bold text-black" x-text="detailDipilih?.paket_wisata?.nama_destinasi ?? '—'"></h4>
                            <p class="text-xs text-grey-deep" x-text="detailDipilih?.paket_wisata?.provinsi || detailDipilih?.paket_wisata?.lokasi || '—'"></p>
                            <div class="flex flex-wrap items-center gap-2 pt-1 text-[11px] text-grey-2">
                                <span class="rounded bg-gray-100 px-2 py-0.5" x-text="'Jarak: ' + (detailDipilih?.paket_wisata?.jarak ? detailDipilih.paket_wisata.jarak + ' Km' : '—')"></span>
                                <span class="rounded bg-gray-100 px-2 py-0.5" x-text="'Durasi: ' + (detailDipilih?.paket_wisata?.durasi_perjalanan ? detailDipilih.paket_wisata.durasi_perjalanan + ' Jam' : '—')"></span>
                                <span class="rounded bg-primary-3 px-2 py-0.5 font-bold text-primary-1" x-text="'Harga: Rp ' + Number(detailDipilih?.paket_wisata?.harga || 0).toLocaleString('id-ID')"></span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 3. Kategori Kaos & Ukuran -->
                <div class="rounded-xl border border-gray-200 bg-white p-4 shadow-xs">
                    <span class="inline-block rounded-md bg-primary-1 px-2.5 py-1 text-[11px] font-bold text-white mb-3">
                        Pilihan Kaos & Ukuran
                    </span>
                    <div class="flex items-start gap-4">
                        <template x-if="detailDipilih?.desain_kaos?.foto">
                            <img :src="detailDipilih.desain_kaos.foto" class="h-20 w-20 rounded-lg object-cover shrink-0 border border-gray-100">
                        </template>
                        <div class="space-y-2 min-w-0 flex-1">
                            <h4 class="text-sm font-bold text-black" x-text="detailDipilih?.desain_kaos?.nama_desain ?? '—'"></h4>
                            
                            <!-- Rincian Ukuran Kaos -->
                            <div class="space-y-1">
                                <span class="block text-[11px] font-semibold text-grey-2">Daftar Ukuran:</span>
                                <div class="flex flex-wrap gap-1.5">
                                    <template x-if="detailDipilih?.detail_kaos && detailDipilih.detail_kaos.length > 0">
                                        <template x-for="dk in detailDipilih.detail_kaos" :key="dk.id_detail || dk.id_ukuran">
                                            <span class="inline-flex items-center gap-1 rounded-md border border-primary-1 bg-primary-3/30 px-2.5 py-1 text-xs font-bold text-primary-1">
                                                <span x-text="dk.ukuran_kaos?.kode_ukuran ?? 'Ukuran'"></span>
                                                <span class="rounded bg-primary-1 px-1.5 py-0.2 text-[10px] text-white" x-text="dk.jumlah + 'x'"></span>
                                            </span>
                                        </template>
                                    </template>
                                    <template x-if="!detailDipilih?.detail_kaos || detailDipilih.detail_kaos.length === 0">
                                        <span class="inline-flex items-center gap-1 rounded-md border border-primary-1 bg-primary-3/30 px-2.5 py-1 text-xs font-bold text-primary-1">
                                            <span x-text="detailDipilih?.ukuran_kaos?.kode_ukuran ?? 'L'"></span>
                                            <span class="rounded bg-primary-1 px-1.5 py-0.2 text-[10px] text-white">1x</span>
                                        </span>
                                    </template>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

            <!-- Footer Modal -->
            <div class="border-t border-gray-100 px-6 py-3.5 bg-gray-50 flex justify-end">
                <button type="button"
                        @click="tutupModalDetail()"
                        class="btn-secondary h-9 px-5 text-xs font-semibold">
                    Tutup
                </button>
            </div>

        </div>
    </div>
</div>

<script>
function profileApp() {
    return {
        loading: true,
        loadingRiwayat: true,
        editMode: false,
        saving: false,
        successMsg: '',
        errorMsg: '',
        profile: @json(Auth::user() ?? (object)[]),
        form: @json(Auth::user() ?? (object)[]),
        riwayatList: [],

        // Modal Popup Detail Pilihan Polling
        modalRiwayatOpen: false,
        detailDipilih: null,

        bukaModalDetail(item) {
            this.detailDipilih = item;
            this.modalRiwayatOpen = true;
        },
        tutupModalDetail() {
            this.modalRiwayatOpen = false;
            this.detailDipilih = null;
        },
        formatWaktu(waktuStr) {
            if (!waktuStr) return '—';
            try {
                const d = new Date(waktuStr);
                return d.toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' }) + ' ' +
                       d.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' });
            } catch(e) {
                return waktuStr;
            }
        },

        async init() {
            const token = localStorage.getItem('vonic_token');
            // Guard dinonaktifkan sementara untuk preview tampilan
            // if (!token) {
            //     window.location.href = '{{ route('login') }}';
            //     return;
            // }
            const headers = token
                ? { 'Accept': 'application/json', 'Authorization': 'Bearer ' + token }
                : { 'Accept': 'application/json' };

            // Fetch profil
            try {
                const res = await fetch('/api/profile', { headers });
                if (!res.ok) throw new Error('HTTP ' + res.status);
                const json = await res.json();
                this.profile = json.data ?? this.profile;
                this.form = { ...this.profile };
            } catch(e) {
                if (!this.profile || !this.profile.nama) {
                    this.errorMsg = token ? 'Gagal memuat data profil.' : 'Silakan login terlebih dahulu untuk melihat data profil.';
                }
            } finally {
                this.loading = false;
            }

            // Fetch riwayat
            try {
                const rr = await fetch('/api/riwayat-voting', { headers });
                if (rr.ok) {
                    const rjson = await rr.json();
                    this.riwayatList = rjson.data ?? [];
                }
            } catch(e) {
                // silent
            } finally {
                this.loadingRiwayat = false;
            }
        },

        cancelEdit() {
            this.form = { ...this.profile, fotoPreview: null, fotoFile: null };
            this.editMode = false;
            this.errorMsg = '';
        },

        handlePhotoChange(event) {
            const file = event.target.files[0];
            if (!file) return;
            if (!['image/jpeg', 'image/png', 'image/webp'].includes(file.type)) {
                this.errorMsg = 'Format foto harus JPG, PNG, atau WEBP.';
                return;
            }
            if (file.size > 2 * 1024 * 1024) {
                this.errorMsg = 'Ukuran foto maksimal 2 MB.';
                return;
            }
            this.errorMsg = '';
            this.form.fotoFile = file;
            const reader = new FileReader();
            reader.onload = (e) => {
                this.form.fotoPreview = e.target.result;
            };
            reader.readAsDataURL(file);
        },

        async saveProfile() {
            // Karena belum ada PUT /api/profile di BE, kita simpan ke localStorage
            // dan update tampilan. Nanti saat BE siap tinggal ganti ke axios.put
            this.saving = true;
            this.errorMsg = '';

            // Simulasi save (update local state)
            await new Promise(r => setTimeout(r, 600));
            const updatedFoto = this.form.fotoPreview || this.profile.foto || null;
            this.profile = { ...this.profile, ...this.form, foto: updatedFoto };
            this.form.fotoPreview = null;
            this.form.fotoFile = null;

            // Update nama di localStorage user
            try {
                const u = JSON.parse(localStorage.getItem('vonic_user') || '{}');
                u.nama = this.form.nama;
                localStorage.setItem('vonic_user', JSON.stringify(u));
            } catch(e) {}

            this.editMode = false;
            this.saving = false;
            this.successMsg = 'Data terbaru Anda berhasil diperbarui.';
            setTimeout(() => { this.successMsg = ''; }, 4000);
        }
    };
}
</script>
@endsection
