@extends('layouts.focus')

@section('title', 'Profil Akun')
@section('page_title', 'Profil akun')
@section('max_width', 'max-w-[1360px]')

@section('header_actions')
  <a href="{{ url()->previous() === url()->current() ? route('admin.dashboard') : url()->previous() }}"
     class="flex items-center gap-2 text-xs font-semibold text-primary-1 hover:underline">
    <x-solar-arrow-left-linear class="h-4 w-4" />
    <span>Kembali</span>
  </a>

  <button type="button"
          @click="activeView = 'tambah_akun'"
          class="inline-flex items-center gap-2 rounded-sm border border-grey-1 bg-white px-4 py-2 text-xs font-semibold text-primary-1 shadow-xs hover:border-primary-1 hover:bg-primary-3/30 transition-all">
    <x-solar-user-plus-linear class="h-4 w-4 text-primary-1" />
    <span>Tambahkan akun</span>
  </button>
@endsection

@section('content')
<div x-data="{
  currentProfile: {
    name: '{{ $profile['name'] }}',
    email: '{{ $profile['email'] }}',
    photo: '{{ $profile['photo'] ?? '' }}'
  },
  editForm: {
    name: '{{ $profile['name'] }}',
    email: '{{ $profile['email'] }}',
    current_password: '',
    password: '',
    password_confirmation: '',
    photo_preview: ''
  },
  addAccountForm: {
    email: '',
    password: '',
    showPassword: false
  },
  showCurrentPw: false,
  showNewPw: false,
  showConfirmPw: false,
  handlePhotoUpload(e) {
    const f = e.target.files[0];
    if (f) {
      this.editForm.photo_preview = URL.createObjectURL(f);
    }
  },
  saveProfile() {
    this.currentProfile.name = this.editForm.name;
    this.currentProfile.email = this.editForm.email;
    if (this.editForm.photo_preview) {
      this.currentProfile.photo = this.editForm.photo_preview;
    }
    alert('Perubahan profil berhasil disimpan!');
    activeView = 'identitas';
  },
  submitAddAccount() {
    alert('Akun baru ' + this.addAccountForm.email + ' berhasil ditambahkan!');
    this.addAccountForm.email = '';
    this.addAccountForm.password = '';
    activeView = 'identitas';
  }
}">

  {{-- ============================================================
       VIEW 1: IDENTITAS (Halaman Utama Profil & Log Aktivitas)
       ============================================================ --}}
  <div x-show="activeView === 'identitas'" x-cloak>
    <div>
      <h1 class="text-3xl font-bold tracking-tight text-[#082450]">Identitas</h1>
      <p class="mt-1.5 text-xs text-grey-2">Kelola informasi profil dan pantau aktivitas akun Anda.</p>
    </div>

    <div class="mt-8 grid grid-cols-1 items-start gap-6 lg:grid-cols-[330px_1fr]">

      {{-- Kolom Kiri: Profil Saya Card & Sesi Akun --}}
      <div class="space-y-5">
        {{-- Card Profil Saya --}}
        <div class="rounded-2xl border border-transparent bg-white p-6 shadow-sm">
          <div class="flex items-center justify-between">
            <h2 class="text-base font-bold text-black">Profil saya</h2>
            <x-solar-user-linear class="h-5 w-5 text-primary-1" />
          </div>

          {{-- Foto Profil Pantai / Formal --}}
          <div class="mt-4 aspect-[16/10] w-full overflow-hidden rounded-xl border border-grey-1 bg-gradient-to-tr from-sky-200 to-blue-400">
            <template x-if="currentProfile.photo">
              <img :src="currentProfile.photo" alt="Foto Profil" class="h-full w-full object-cover">
            </template>
            <template x-if="!currentProfile.photo">
              <div class="relative h-full w-full bg-cover bg-center" style="background-image: url('https://images.unsplash.com/photo-1507525428034-b723cf961d3e?auto=format&fit=crop&w=800&q=80');">
              </div>
            </template>
          </div>

          {{-- Data Detail User --}}
          <div class="mt-5 space-y-4">
            {{-- Nama --}}
            <div class="flex items-start gap-3.5">
              <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-primary-3/50 text-primary-1">
                <x-solar-user-linear class="h-4 w-4" />
              </div>
              <div class="min-w-0">
                <p class="text-[11px] text-grey-2">Nama</p>
                <p class="truncate text-xs font-semibold text-black" x-text="currentProfile.name"></p>
              </div>
            </div>

            {{-- Email --}}
            <div class="flex items-start gap-3.5">
              <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-primary-3/50 text-primary-1">
                <x-solar-letter-linear class="h-4 w-4" />
              </div>
              <div class="min-w-0">
                <p class="text-[11px] text-grey-2">Email</p>
                <p class="truncate text-xs font-semibold text-black" x-text="currentProfile.email"></p>
              </div>
            </div>

            {{-- Password Masked --}}
            <div class="flex items-start gap-3.5">
              <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-primary-3/50 text-primary-1">
                <x-solar-lock-linear class="h-4 w-4" />
              </div>
              <div>
                <p class="text-[11px] text-grey-2">Password</p>
                <p class="text-xs font-semibold tracking-widest text-black">****</p>
              </div>
            </div>
          </div>

          {{-- Tombol Edit Profile --}}
          <button type="button"
                  @click="activeView = 'edit_profil'"
                  class="mt-6 flex h-11 w-full items-center justify-center gap-2 rounded-sm bg-primary-1 text-xs font-semibold text-white shadow-sm hover:bg-primary-4 transition-all">
            <x-solar-pen-new-square-linear class="h-4 w-4" />
            <span>Edit Profile</span>
          </button>
        </div>

        {{-- Sesi Akun Card --}}
        <div class="flex items-center justify-between rounded-2xl border border-transparent bg-white p-5 shadow-sm">
          <div>
            <h3 class="text-xs font-bold text-black">Sesi akun</h3>
            <p class="mt-0.5 text-[11px] text-grey-2">Keluar dari akun Anda.</p>
          </div>
          <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit"
                    class="flex items-center gap-2 rounded-sm border border-danger/30 bg-[#FFECEC] px-4 py-2 text-xs font-semibold text-danger hover:bg-danger-soft transition-all cursor-pointer">
              <x-solar-logout-2-linear class="h-3.5 w-3.5" />
              <span>Log Out</span>
            </button>
          </form>
        </div>
      </div>

      {{-- Kolom Kanan: Log Aktivitas Table Card --}}
      <div class="rounded-2xl border border-transparent bg-white p-6 shadow-sm">
        <div class="flex items-start justify-between">
          <div>
            <h2 class="text-lg font-bold text-black">Log Aktivitas</h2>
            <p class="mt-0.5 text-xs text-grey-2">Riwayat aktivitas pada akun Anda.</p>
          </div>
          <div class="flex h-9 w-9 items-center justify-center rounded-full bg-primary-3/60 text-primary-1">
            <x-solar-history-linear class="h-5 w-5" />
          </div>
        </div>

        {{-- Tabel Log Aktivitas Header Biru Gelap --}}
        <div class="mt-6 overflow-x-auto">
          <table class="w-full min-w-[700px] border-separate border-spacing-y-2 text-xs">
            <thead>
              <tr class="bg-primary-1 text-white text-[11px] font-bold tracking-wider text-left">
                <th class="rounded-l-sm py-3 px-3 text-left w-14">NO.</th>
                <th class="py-3 px-3 text-left w-28">ID</th>
                <th class="py-3 px-4 text-left">DESKRIPSI</th>
                <th class="py-3 px-4 text-left">ADMIN</th>
                <th class="py-3 px-3 text-left w-36">WAKTU</th>
                <th class="rounded-r-sm py-3 px-3 text-left w-32">STATUS</th>
              </tr>
            </thead>
            <tbody>
              @forelse ($logs as $log)
                <tr class="transition-colors hover:bg-primary-3/20 bg-white">
                  {{-- No. --}}
                  <td class="border-b border-gray-100 py-3 px-3 text-left">
                    <span class="inline-flex h-7 w-7 items-center justify-center rounded-sm border border-grey-1 bg-white text-[11px] font-semibold text-black">
                      {{ str_pad($logs->firstItem() + $loop->index, 2, '0', STR_PAD_LEFT) }}
                    </span>
                  </td>

                  {{-- ID --}}
                  <td class="border-b border-gray-100 py-3 px-3 text-left font-medium text-black">
                    {{ $log['id'] }}
                  </td>

                  {{-- Deskripsi --}}
                  <td class="border-b border-gray-100 py-3 px-4 font-semibold text-black text-left">
                    {{ $log['desc'] }}
                  </td>

                  {{-- Admin --}}
                  <td class="border-b border-gray-100 py-3 px-4 text-left uppercase tracking-tight text-black text-[11px]">
                    {{ $log['admin'] }}
                  </td>

                  {{-- Waktu --}}
                  <td class="border-b border-gray-100 py-3 px-3 text-left font-medium text-black">
                    {{ $log['time'] }}
                  </td>

                  {{-- Status Dropdown/Pill --}}
                  <td class="border-b border-gray-100 py-3 px-3 text-left">
                    <div class="inline-flex items-center justify-between gap-2 rounded-sm border border-grey-1 bg-gray-50/70 px-3 py-1.5 text-[11px] font-semibold text-black">
                      <span>{{ $log['status'] }}</span>
                      <x-solar-alt-arrow-down-linear class="h-3 w-3 text-grey-2" />
                    </div>
                  </td>
                </tr>
              @empty
                <tr>
                  <td colspan="6" class="py-12 bg-white">
                    <x-empty-state 
                      icon="solar-history-linear"
                      title="Belum Ada Riwayat Aktivitas"
                      message="Semua aktivitas autentikasi, perubahan akun, atau pengelolaan polling Anda akan tercatat secara kronologis di sini." />
                  </td>
                </tr>
              @endforelse
            </tbody>
          </table>
        </div>

        {{-- Footer Paginasi Log --}}
        <div class="mt-6 flex items-center justify-between border-t border-gray-100 pt-4">
          <p class="text-xs text-grey-2">Halaman {{ $logs->currentPage() }} dari {{ $logs->lastPage() }}</p>

          <div class="flex items-center gap-2">
            <a href="{{ $logs->previousPageUrl() ?? '#' }}"
               class="flex h-9 w-9 items-center justify-center rounded-sm border border-grey-1 bg-white text-primary-1 shadow-xs hover:bg-primary-3/30 transition-all {{ $logs->onFirstPage() ? 'opacity-40 pointer-events-none' : '' }}">
              <x-solar-alt-arrow-left-linear class="h-4 w-4" />
            </a>
            <span class="flex h-9 min-w-[64px] items-center justify-center rounded-sm bg-primary-1 px-3 text-xs font-semibold text-white">
              {{ $logs->currentPage() }}/{{ $logs->lastPage() }}
            </span>
            <a href="{{ $logs->nextPageUrl() ?? '#' }}"
               class="flex h-9 w-9 items-center justify-center rounded-sm border border-grey-1 bg-white text-primary-1 shadow-xs hover:bg-primary-3/30 transition-all {{ $logs->hasMorePages() ? '' : 'opacity-40 pointer-events-none' }}">
              <x-solar-alt-arrow-right-linear class="h-4 w-4" />
            </a>
          </div>
        </div>

      </div>

    </div>
  </div>


  {{-- ============================================================
       VIEW 2: EDIT PROFIL
       ============================================================ --}}
  <div x-show="activeView === 'edit_profil'" x-cloak>
    <div>
      <h1 class="text-3xl font-bold tracking-tight text-[#082450]">Edit Profil</h1>
      <p class="mt-1.5 text-xs text-grey-2">Perbarui informasi profil dan jaga keamanan akun Anda.</p>
    </div>

    <div class="mt-8 grid grid-cols-1 items-start gap-6 lg:grid-cols-[330px_1fr]">

      {{-- Sisi Kiri: Foto Profil Card --}}
      <div class="rounded-2xl border border-transparent bg-white p-6 shadow-sm">
        <div class="flex items-center justify-between">
          <h2 class="text-base font-bold text-black">Foto profil</h2>
          <x-solar-user-linear class="h-5 w-5 text-primary-1" />
        </div>

        <div class="mt-4 aspect-[16/10] w-full overflow-hidden rounded-xl border border-grey-1 bg-gradient-to-tr from-sky-200 to-blue-400">
          <template x-if="editForm.photo_preview">
            <img :src="editForm.photo_preview" alt="Foto Profil" class="h-full w-full object-cover">
          </template>
          <template x-if="!editForm.photo_preview && currentProfile.photo">
            <img :src="currentProfile.photo" alt="Foto Profil" class="h-full w-full object-cover">
          </template>
          <template x-if="!editForm.photo_preview && !currentProfile.photo">
            <div class="relative h-full w-full bg-cover bg-center" style="background-image: url('https://images.unsplash.com/photo-1507525428034-b723cf961d3e?auto=format&fit=crop&w=800&q=80');">
            </div>
          </template>
        </div>

        {{-- Tombol Ubah Foto --}}
        <label class="mt-5 flex h-11 w-full cursor-pointer items-center justify-center gap-2 rounded-sm border border-grey-1 bg-white text-xs font-semibold text-primary-1 shadow-xs hover:border-primary-1 hover:bg-primary-3/30 transition-all">
          <x-solar-pen-new-square-linear class="h-4 w-4" />
          <span>Ubah foto</span>
          <input type="file" accept="image/*" class="hidden" @change="handlePhotoUpload($event)">
        </label>
        <p class="mt-2 text-center text-[11px] text-grey-2">Format JPG atau PNG. Maksimal 2 MB.</p>

        <div class="mt-6 border-t border-gray-100 pt-4">
          <p class="truncate text-xs font-bold text-black" x-text="editForm.name"></p>
          <p class="truncate text-[11px] text-grey-2" x-text="editForm.email"></p>
        </div>
      </div>

      {{-- Sisi Kanan: Form Informasi Pribadi & Password Card --}}
      <div class="rounded-2xl border border-transparent bg-white p-7 shadow-sm">
        
        {{-- Section 1: Informasi Pribadi --}}
        <div class="flex items-start justify-between">
          <div>
            <h2 class="text-base font-bold text-black">Informasi pribadi</h2>
            <p class="mt-0.5 text-xs text-grey-2">Sesuaikan nama dan email yang terhubung dengan akun Anda.</p>
          </div>
          <x-solar-user-linear class="h-5 w-5 text-primary-1" />
        </div>

        <div class="mt-5 grid grid-cols-1 gap-4 sm:grid-cols-2">
          <div>
            <label class="block text-xs font-semibold text-black mb-1.5">Nama</label>
            <input type="text" x-model="editForm.name"
                   class="h-11 w-full rounded-sm border border-grey-1 bg-white px-3.5 text-xs text-black placeholder:text-grey-2 shadow-xs focus:border-primary-1 focus:outline-none">
          </div>
          <div>
            <label class="block text-xs font-semibold text-black mb-1.5">Email</label>
            <input type="email" x-model="editForm.email"
                   class="h-11 w-full rounded-sm border border-grey-1 bg-white px-3.5 text-xs text-black placeholder:text-grey-2 shadow-xs focus:border-primary-1 focus:outline-none">
          </div>
        </div>

        {{-- Section 2: Ubah Password --}}
        <div class="mt-8 border-t border-gray-100 pt-6">
          <div class="flex items-center justify-between">
            <div>
              <h2 class="text-base font-bold text-black">Ubah password</h2>
              <p class="mt-0.5 text-xs text-grey-2">Isi bagian ini hanya jika Anda ingin mengganti password.</p>
            </div>
            <span class="text-xs text-grey-2">Opsional</span>
          </div>

          <div class="mt-5 space-y-4">
            {{-- Password Saat Ini --}}
            <div>
              <label class="block text-xs font-semibold text-black mb-1.5">Password saat ini</label>
              <div class="relative">
                <input :type="showCurrentPw ? 'text' : 'password'" x-model="editForm.current_password"
                       placeholder="••••"
                       class="h-11 w-full rounded-sm border border-grey-1 bg-white px-3.5 pr-10 text-xs text-black placeholder:text-grey-2 shadow-xs focus:border-primary-1 focus:outline-none">
                <button type="button" @click="showCurrentPw = !showCurrentPw"
                        class="absolute inset-y-0 right-3 flex items-center text-grey-2 hover:text-black">
                  <x-solar-eye-linear x-show="!showCurrentPw" class="h-4 w-4" />
                  <x-solar-eye-closed-linear x-show="showCurrentPw" class="h-4 w-4" />
                </button>
              </div>
            </div>

            {{-- Password Baru & Konfirmasi Password Baru --}}
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
              <div>
                <label class="block text-xs font-semibold text-black mb-1.5">Password baru</label>
                <div class="relative">
                  <input :type="showNewPw ? 'text' : 'password'" x-model="editForm.password"
                         placeholder="••••••••"
                         class="h-11 w-full rounded-sm border border-grey-1 bg-white px-3.5 pr-10 text-xs text-black placeholder:text-grey-2 shadow-xs focus:border-primary-1 focus:outline-none">
                  <button type="button" @click="showNewPw = !showNewPw"
                          class="absolute inset-y-0 right-3 flex items-center text-grey-2 hover:text-black">
                    <x-solar-eye-linear x-show="!showNewPw" class="h-4 w-4" />
                    <x-solar-eye-closed-linear x-show="showNewPw" class="h-4 w-4" />
                  </button>
                </div>
                <p class="mt-1.5 text-[11px] text-grey-2">Gunakan minimal 8 karakter.</p>
              </div>

              <div>
                <label class="block text-xs font-semibold text-black mb-1.5">Konfirmasi password baru</label>
                <div class="relative">
                  <input :type="showConfirmPw ? 'text' : 'password'" x-model="editForm.password_confirmation"
                         placeholder="••••••••"
                         class="h-11 w-full rounded-sm border border-grey-1 bg-white px-3.5 pr-10 text-xs text-black placeholder:text-grey-2 shadow-xs focus:border-primary-1 focus:outline-none">
                  <button type="button" @click="showConfirmPw = !showConfirmPw"
                          class="absolute inset-y-0 right-3 flex items-center text-grey-2 hover:text-black">
                    <x-solar-eye-linear x-show="!showConfirmPw" class="h-4 w-4" />
                    <x-solar-eye-closed-linear x-show="showConfirmPw" class="h-4 w-4" />
                  </button>
                </div>
                <p class="mt-1.5 text-[11px] text-grey-2">Masukkan kembali password baru Anda.</p>
              </div>
            </div>
          </div>
        </div>

        {{-- Banner Edukasi: Jaga Kerahasiaan Akun --}}
        <div class="mt-6 flex items-start gap-3 rounded-xl bg-primary-3/50 p-4 border border-primary-2/30">
          <x-solar-shield-check-linear class="h-5 w-5 shrink-0 text-primary-1 mt-0.5" />
          <div>
            <h4 class="text-xs font-bold text-black">Jaga kerahasiaan akun</h4>
            <p class="text-[11px] text-grey-deep mt-0.5">Gunakan password yang unik dan jangan bagikan kepada siapa pun.</p>
          </div>
        </div>

        {{-- Tombol Aksi Batal & Simpan Perubahan --}}
        <div class="mt-8 flex justify-end items-center gap-3">
          <button type="button"
                  @click="activeView = 'identitas'"
                  class="rounded-sm border border-grey-1 bg-white px-6 py-2.5 text-xs font-semibold text-grey-deep hover:bg-gray-50 transition-all">
            Batal
          </button>
          <button type="button"
                  @click="saveProfile()"
                  class="rounded-sm bg-primary-1 px-7 py-2.5 text-xs font-semibold text-white shadow-sm hover:bg-primary-4 transition-all">
            Simpan perubahan
          </button>
        </div>

      </div>

    </div>
  </div>


  {{-- ============================================================
       VIEW 3: BUAT AKUN BARU / TAMBAHKAN AKUN
       ============================================================ --}}
  <div x-show="activeView === 'tambah_akun'" x-cloak>
    <div>
      <p class="text-xs font-bold uppercase tracking-wider text-primary-1">AKUN SAYA</p>
      <h1 class="mt-1 text-3xl font-bold tracking-tight text-[#082450]">Buat Akun Baru</h1>
      <p class="mt-1.5 text-xs text-grey-2">Masuk ke akun Vonic lain yang sudah terdaftar tanpa keluar dari akun saat ini.</p>
    </div>

    <div class="mt-8 grid grid-cols-1 items-start gap-6 lg:grid-cols-[330px_1fr]">

      {{-- Sisi Kiri: Akun Saat Ini Card --}}
      <div class="rounded-2xl border border-transparent bg-white p-6 shadow-sm">
        <div class="flex items-center justify-between">
          <h2 class="text-base font-bold text-black">Akun saat ini</h2>
          <x-solar-user-linear class="h-5 w-5 text-primary-1" />
        </div>

        <div class="mt-4 aspect-[16/10] w-full overflow-hidden rounded-xl border border-grey-1 bg-gradient-to-tr from-sky-200 to-blue-400">
          <template x-if="currentProfile.photo">
            <img :src="currentProfile.photo" alt="Foto Profil" class="h-full w-full object-cover">
          </template>
          <template x-if="!currentProfile.photo">
            <div class="relative h-full w-full bg-cover bg-center" style="background-image: url('https://images.unsplash.com/photo-1507525428034-b723cf961d3e?auto=format&fit=crop&w=800&q=80');">
            </div>
          </template>
        </div>

        <div class="mt-4">
          <p class="truncate text-xs font-bold text-black" x-text="currentProfile.name"></p>
          <p class="truncate text-[11px] text-grey-2" x-text="currentProfile.email"></p>
        </div>

        <div class="mt-4 flex items-center gap-1.5 text-[11px] font-semibold text-primary-1">
          <x-solar-check-circle-bold class="h-4 w-4 text-primary-1" />
          <span>Sedang digunakan</span>
        </div>
      </div>

      {{-- Sisi Kanan: Form Tambahkan Akun Baru Card --}}
      <div class="rounded-2xl border border-transparent bg-white p-8 shadow-sm">
        <div class="flex items-start justify-between">
          <div>
            <h2 class="text-base font-bold text-black">Tambahkan Akun Baru</h2>
            <p class="mt-0.5 text-xs text-grey-2">Gunakan email dan password akun Vonic Anda.</p>
          </div>
          <div class="flex h-9 w-9 items-center justify-center rounded-full bg-primary-3/60 text-primary-1">
            <x-solar-user-plus-linear class="h-5 w-5" />
          </div>
        </div>

        <form @submit.prevent="submitAddAccount()" class="mt-8 space-y-5 max-w-xl mx-auto">
          {{-- Email --}}
          <div>
            <label class="block text-xs font-semibold text-black mb-1.5">Email</label>
            <div class="relative flex items-center">
              <div class="pointer-events-none absolute inset-y-0 left-3.5 flex items-center text-grey-2">
                <x-solar-letter-linear class="h-4 w-4" />
              </div>
              <input type="email" x-model="addAccountForm.email" required
                     placeholder="Masukkan email akun lain"
                     class="h-11 w-full rounded-sm border border-grey-1 bg-white pl-10 pr-3.5 text-xs text-black placeholder:text-grey-2 shadow-xs focus:border-primary-1 focus:outline-none">
            </div>
          </div>

          {{-- Password --}}
          <div>
            <label class="block text-xs font-semibold text-black mb-1.5">Password</label>
            <div class="relative flex items-center">
              <div class="pointer-events-none absolute inset-y-0 left-3.5 flex items-center text-grey-2">
                <x-solar-lock-linear class="h-4 w-4" />
              </div>
              <input :type="addAccountForm.showPassword ? 'text' : 'password'" x-model="addAccountForm.password" required
                     placeholder="••••••••"
                     class="h-11 w-full rounded-sm border border-grey-1 bg-white pl-10 pr-10 text-xs text-black placeholder:text-grey-2 shadow-xs focus:border-primary-1 focus:outline-none">
              <button type="button" @click="addAccountForm.showPassword = !addAccountForm.showPassword"
                      class="absolute inset-y-0 right-3.5 flex items-center text-grey-2 hover:text-black">
                <x-solar-eye-linear x-show="!addAccountForm.showPassword" class="h-4 w-4" />
                <x-solar-eye-closed-linear x-show="addAccountForm.showPassword" class="h-4 w-4" />
              </button>
            </div>
          </div>

          <div class="flex justify-end">
            <a href="#" class="text-[11px] font-semibold text-primary-1 hover:underline">
              Lupa password?
            </a>
          </div>

          {{-- Tombol Submit & Batal --}}
          <div class="pt-2 space-y-3">
            <button type="submit"
                    class="h-11 w-full rounded-sm bg-primary-1 text-xs font-semibold text-white shadow-sm hover:bg-primary-4 transition-all">
              Masuk & tambahkan akun
            </button>

            <button type="button"
                    @click="activeView = 'identitas'"
                    class="h-11 w-full rounded-sm border border-grey-1 bg-white text-xs font-semibold text-grey-deep hover:bg-gray-50 transition-all">
              Batal
            </button>
          </div>

          <div class="pt-4 flex items-center justify-center gap-2 text-[11px] text-grey-2">
            <x-solar-lock-linear class="h-3.5 w-3.5" />
            <span>Informasi masuk Anda dijaga kerahasiaannya.</span>
          </div>
        </form>

      </div>

    </div>
  </div>

</div>
@endsection
