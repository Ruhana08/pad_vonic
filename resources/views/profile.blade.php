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
                <div class="mb-6 flex justify-center">
                    <div class="relative">
                        <div class="h-24 w-24 overflow-hidden rounded-full border-4 border-primary-1/20 bg-gray-100 shadow-sm">
                            <template x-if="profile.foto">
                                <img :src="profile.foto" alt="Foto profil" class="h-full w-full object-cover" />
                            </template>
                            <template x-if="!profile.foto">
                                <div class="flex h-full w-full items-center justify-center bg-primary-3">
                                    <x-solar-user-bold class="h-10 w-10 text-primary-1" />
                                </div>
                            </template>
                        </div>
                    </div>
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
                                           x-text="item.periode_polling?.nama_periode ?? '—'"></p>
                                        <p class="mt-0.5 text-xs text-grey-2"
                                           x-text="item.paket_wisata?.nama_destinasi ? '🗺 ' + item.paket_wisata.nama_destinasi : ''"></p>
                                    </div>
                                    <a href="{{ route('riwayat') }}"
                                       class="btn-primary h-8 px-3 text-xs font-semibold ml-4 shrink-0">
                                        Lihat Riwayat
                                    </a>
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
        profile: {},
        form: {},
        riwayatList: [],

        async init() {
            const token = localStorage.getItem('vonic_token');
            // Guard dinonaktifkan sementara untuk preview tampilan
            // if (!token) {
            //     window.location.href = '{{ route('user.login') }}';
            //     return;
            // }
            const headers = { Authorization: 'Bearer ' + token };

            // Fetch profil
            try {
                const res = await axios.get('/api/profile', { headers });
                this.profile = res.data.data ?? {};
                this.form = { ...this.profile };
            } catch(e) {
                this.errorMsg = 'Gagal memuat data profil.';
            } finally {
                this.loading = false;
            }

            // Fetch riwayat
            try {
                const rr = await axios.get('/api/riwayat-voting', { headers });
                this.riwayatList = rr.data.data ?? [];
            } catch(e) {
                // silent
            } finally {
                this.loadingRiwayat = false;
            }
        },

        cancelEdit() {
            this.form = { ...this.profile };
            this.editMode = false;
            this.errorMsg = '';
        },

        async saveProfile() {
            // Karena belum ada PUT /api/profile di BE, kita simpan ke localStorage
            // dan update tampilan. Nanti saat BE siap tinggal ganti ke axios.put
            this.saving = true;
            this.errorMsg = '';

            // Simulasi save (update local state)
            await new Promise(r => setTimeout(r, 600));
            this.profile = { ...this.profile, ...this.form };

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
