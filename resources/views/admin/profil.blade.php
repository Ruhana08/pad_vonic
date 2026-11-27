@extends('layouts.focus')

@section('title', 'Profil Akun')
@section('page_title', 'Profil akun')
@section('max_width', 'max-w-[1360px]')

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
  photoFailed: false,
  removeFoto: false,
  getInitials(name) {
    const parts = String(name || '').trim().split(/\s+/).filter(Boolean);
    if (!parts.length) return 'A';
    if (parts.length === 1) return parts[0].charAt(0).toUpperCase();
    return (parts[0].charAt(0) + parts[parts.length - 1].charAt(0)).toUpperCase();
  },
  deleteLogTarget: null,
  hiddenLogs: [],
  confirmDeleteLog() {
    if (this.deleteLogTarget) {
      this.hiddenLogs.push(this.deleteLogTarget);
      this.deleteLogTarget = null;
    }
  },
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
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
      <div>
        <h1 class="text-3xl font-bold tracking-tight text-[#082450]">Identitas</h1>
        <p class="mt-1.5 text-xs text-grey-2">Kelola informasi profil dan pantau aktivitas akun Anda.</p>
      </div>

      <div class="flex items-center gap-3">
        <a href="{{ url()->previous() === url()->current() ? route('admin.dashboard') : url()->previous() }}"
           class="btn-secondary h-10 px-4 text-xs font-semibold">
          <x-solar-arrow-left-linear class="h-4 w-4" />
          <span>Kembali</span>
        </a>

        <button type="button"
                @click="activeView = 'tambah_akun'"
                class="btn-secondary h-10 px-4 text-xs font-semibold">
          <x-solar-user-plus-linear class="h-4 w-4 text-primary-1" />
          <span>Tambahkan akun</span>
        </button>
      </div>
    </div>

    <div class="mt-8 grid grid-cols-1 items-start gap-6 lg:grid-cols-[330px_1fr]">

      {{-- Kolom Kiri: Profil Saya Card & Sesi Akun --}}
      <div class="space-y-5">
        {{-- Card Profil Saya --}}
        <div class="rounded-2xl border border-grey-1 bg-white p-6 shadow-type-1">
          <div class="flex items-center justify-between">
            <h2 class="text-base font-bold text-black">Profil saya</h2>
            <x-solar-user-linear class="h-5 w-5 text-primary-1" />
          </div>

          {{-- Foto Profil Lingkaran (Konsisten dengan Profil User) --}}
          <div class="mt-5 flex justify-center">
            <div class="h-24 w-24 overflow-hidden rounded-full border-4 border-primary-1/20 bg-primary-1 shadow-sm flex items-center justify-center">
              <template x-if="currentProfile.photo && !photoFailed">
                <img :src="currentProfile.photo" alt="Foto Profil" x-on:error="photoFailed = true" class="h-full w-full object-cover">
              </template>
              <template x-if="!currentProfile.photo || photoFailed">
                <div class="flex h-full w-full items-center justify-center bg-primary-1 text-white text-2xl font-bold tracking-wide select-none">
                  <span x-text="getInitials(currentProfile.name)"></span>
                </div>
              </template>
            </div>
          </div>

          {{-- Data Detail User --}}
          <div class="mt-5 space-y-4">
            {{-- Nama --}}
            <div class="flex items-start gap-3.5">
              <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-primary-3/50 text-primary-1">
                <x-solar-user-linear class="h-4 w-4" />
              </div>
              <div class="min-w-0">
                <p class="text-xs text-grey-2">Nama</p>
                <p class="truncate text-xs font-semibold text-black" x-text="currentProfile.name"></p>
              </div>
            </div>

            {{-- Email --}}
            <div class="flex items-start gap-3.5">
              <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-primary-3/50 text-primary-1">
                <x-solar-letter-linear class="h-4 w-4" />
              </div>
              <div class="min-w-0">
                <p class="text-xs text-grey-2">Email</p>
                <p class="truncate text-xs font-semibold text-black" x-text="currentProfile.email"></p>
              </div>
            </div>

            {{-- Password Masked --}}
            <div class="flex items-start gap-3.5">
              <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-primary-3/50 text-primary-1">
                <x-solar-lock-linear class="h-4 w-4" />
              </div>
              <div>
                <p class="text-xs text-grey-2">Password</p>
                <p class="text-xs font-semibold tracking-widest text-black">****</p>
              </div>
            </div>
          </div>

          {{-- Tombol Edit Profile --}}
          <button type="button"
                  @click="activeView = 'edit_profil'"
                  class="btn-primary mt-6 h-11 w-full text-xs">
            <x-solar-pen-new-square-linear class="h-4 w-4" />
            <span>Edit Profile</span>
          </button>
        </div>

        {{-- Sesi Akun Card --}}
        <div class="card-panel flex items-center justify-between p-5">
          <div>
            <h3 class="text-xs font-bold text-black">Sesi akun</h3>
            <p class="mt-0.5 text-xs text-grey-2">Keluar dari akun Anda.</p>
          </div>
          <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit"
                    class="btn-danger h-9 px-4 text-xs">
              <x-solar-logout-2-linear class="h-3.5 w-3.5" />
              <span>Log Out</span>
            </button>
          </form>
        </div>
      </div>

      {{-- Kolom Kanan: Log Aktivitas Table Card --}}
      <div class="rounded-2xl border border-grey-1 bg-white p-6 shadow-type-1">
        <div class="flex items-start justify-between">
          <div>
            <h2 class="text-lg font-bold text-black">Log Aktivitas</h2>
            <p class="mt-0.5 text-xs text-grey-2">Riwayat aktivitas pada akun Anda.</p>
          </div>
          <div class="flex h-9 w-9 items-center justify-center rounded-full bg-primary-3/60 text-primary-1">
            <x-solar-history-linear class="h-5 w-5" />
          </div>
        </div>

        {{-- Tabel Log Aktivitas Header Biru Gelap Menggunakan Komponen Reusable <x-table> --}}
        <div class="mt-6">
          <x-table :columns="[
            ['label' => 'NO.', 'width' => '56px'],
            ['label' => 'ID', 'width' => '112px'],
            ['label' => 'DESKRIPSI'],
            ['label' => 'ADMIN'],
            ['label' => 'WAKTU', 'width' => '144px'],
            ['label' => 'AKSI', 'width' => '96px'],
          ]" minWidth="700px" headerBg="bg-secondary-1">
            @forelse ($logs as $log)
              <tr x-show="!hiddenLogs.includes('{{ $log['id'] }}')" class="transition-colors duration-150 hover:bg-[#F3F7FF] bg-white">
                {{-- No. --}}
                <td class="border-b border-gray-100 py-3 px-3 text-left">
                  <span class="inline-flex h-7 w-7 items-center justify-center rounded-xl border border-grey-1 bg-white text-xs font-semibold text-black">
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
                <td class="border-b border-gray-100 py-3 px-4 text-left uppercase tracking-tight text-black text-xs">
                  {{ $log['admin'] }}
                </td>

                {{-- Waktu --}}
                <td class="border-b border-gray-100 py-3 px-3 text-left font-medium text-black">
                  {{ $log['time'] }}
                </td>

                {{-- Aksi: Hapus Log --}}
                <td class="border-b border-gray-100 py-3 px-3 text-left">
                  <div class="flex items-center gap-1.5">
                    <x-action-button type="delete" label="Hapus log ini"
                                     @click="deleteLogTarget = '{{ $log['id'] }}'" />
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
          </x-table>
        </div>

        {{-- Footer Paginasi Log Menggunakan Komponen Reusable --}}
        <div class="mt-6 border-t border-gray-100 pt-4">
          <x-table-pagination :paginator="$logs" paramName="page" />
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
      <div class="rounded-2xl border border-grey-1 bg-white p-6 shadow-type-1">
        <div class="flex items-center justify-between">
          <h2 class="text-base font-bold text-black">Foto profil</h2>
          <x-solar-user-linear class="h-5 w-5 text-primary-1" />
        </div>

        <div class="mt-6 flex flex-col items-center">
          <div class="relative">
            <div class="h-24 w-24 overflow-hidden rounded-full border-4 border-primary-1/20 bg-primary-1 shadow-sm flex items-center justify-center">
              <template x-if="!removeFoto && !photoFailed && (editForm.photo_preview || currentProfile.photo)">
                <img :src="editForm.photo_preview || currentProfile.photo" alt="Foto Profil" x-on:error="photoFailed = true" class="h-full w-full object-cover">
              </template>
              <template x-if="removeFoto || photoFailed || (!editForm.photo_preview && !currentProfile.photo)">
                <div class="flex h-full w-full items-center justify-center bg-primary-1 text-white text-2xl font-bold tracking-wide select-none">
                  <span x-text="getInitials(editForm.name || currentProfile.name)"></span>
                </div>
              </template>
            </div>

            {{-- Tombol Ikon Kamera Ubah Foto (Konsisten dengan Profil User) --}}
            <label class="absolute -bottom-1 -right-1 flex h-8 w-8 cursor-pointer items-center justify-center rounded-full border-2 border-white bg-primary-1 text-white shadow-sm transition-colors hover:bg-primary-4"
                   title="Ubah Foto Profil">
              <x-solar-camera-linear class="h-4 w-4" />
              <input type="file" accept="image/jpeg,image/png,image/webp" class="hidden" @change="removeFoto = false; photoFailed = false; handlePhotoUpload($event)">
            </label>
          </div>
          <p class="mt-2.5 text-center text-[11px] text-grey-2">Format JPG/PNG, maksimal 2 MB.</p>
          <button type="button"
                  x-show="!removeFoto && !photoFailed && (editForm.photo_preview || currentProfile.photo)"
                  @click="removeFoto = true; editForm.photo_preview = ''"
                  class="mt-1 inline-flex items-center gap-1 text-[11px] font-semibold text-danger hover:underline cursor-pointer">
            <x-solar-trash-bin-trash-linear class="h-3.5 w-3.5" />
            <span>Hapus Foto Profil</span>
          </button>
        </div>

        <div class="mt-6 border-t border-gray-100 pt-4">
          <p class="truncate text-xs font-bold text-black" x-text="editForm.name"></p>
          <p class="truncate text-xs text-grey-2" x-text="editForm.email"></p>
        </div>
      </div>

      {{-- Sisi Kanan: Form Informasi Pribadi & Password Card --}}
      <form action="{{ route('admin.profil.update') }}" method="POST" class="rounded-2xl border border-transparent bg-white p-7 shadow-sm">
        @csrf
        <input type="hidden" name="foto_base64" :value="editForm.photo_preview || ''">
        <input type="hidden" name="remove_foto" :value="removeFoto ? '1' : '0'">
        
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
            <input type="text" name="nama" x-model="editForm.name" required
                   class="h-11 w-full rounded-xl border border-grey-1 bg-white px-3.5 text-xs text-black placeholder:text-grey-2 shadow-xs focus:border-primary-1 focus:outline-none">
          </div>
          <div>
            <label class="block text-xs font-semibold text-black mb-1.5">Email</label>
            <input type="email" name="email" x-model="editForm.email" required
                   class="h-11 w-full rounded-xl border border-grey-1 bg-white px-3.5 text-xs text-black placeholder:text-grey-2 shadow-xs focus:border-primary-1 focus:outline-none">
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
                       class="h-11 w-full rounded-xl border border-grey-1 bg-white px-3.5 pr-10 text-xs text-black placeholder:text-grey-2 shadow-xs focus:border-primary-1 focus:outline-none">
                <button type="button" @click="showCurrentPw = !showCurrentPw"
                        class="absolute inset-y-0 right-3 flex items-center text-grey-2 hover:text-black">
                  <x-solar-eye-closed-linear x-show="!showCurrentPw" class="h-4 w-4" />
                  <x-solar-eye-linear x-show="showCurrentPw" class="h-4 w-4" />
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
                         class="h-11 w-full rounded-xl border border-grey-1 bg-white px-3.5 pr-10 text-xs text-black placeholder:text-grey-2 shadow-xs focus:border-primary-1 focus:outline-none">
                  <button type="button" @click="showNewPw = !showNewPw"
                          class="absolute inset-y-0 right-3 flex items-center text-grey-2 hover:text-black">
                    <x-solar-eye-closed-linear x-show="!showNewPw" class="h-4 w-4" />
                    <x-solar-eye-linear x-show="showNewPw" class="h-4 w-4" />
                  </button>
                </div>
                <p class="mt-1.5 text-xs text-grey-2">Gunakan minimal 8 karakter.</p>
              </div>

              <div>
                <label class="block text-xs font-semibold text-black mb-1.5">Konfirmasi password baru</label>
                <div class="relative">
                  <input :type="showConfirmPw ? 'text' : 'password'" x-model="editForm.password_confirmation"
                         placeholder="••••••••"
                         class="h-11 w-full rounded-xl border border-grey-1 bg-white px-3.5 pr-10 text-xs text-black placeholder:text-grey-2 shadow-xs focus:border-primary-1 focus:outline-none">
                  <button type="button" @click="showConfirmPw = !showConfirmPw"
                          class="absolute inset-y-0 right-3 flex items-center text-grey-2 hover:text-black">
                    <x-solar-eye-closed-linear x-show="!showConfirmPw" class="h-4 w-4" />
                    <x-solar-eye-linear x-show="showConfirmPw" class="h-4 w-4" />
                  </button>
                </div>
                <p class="mt-1.5 text-xs text-grey-2">Masukkan kembali password baru Anda.</p>
              </div>
            </div>
          </div>
        </div>

        {{-- Banner Edukasi: Jaga Kerahasiaan Akun --}}
        <div class="mt-6 flex items-start gap-3 rounded-xl bg-primary-3/50 p-4 border border-primary-2/30">
          <x-solar-shield-check-linear class="h-5 w-5 shrink-0 text-primary-1 mt-0.5" />
          <div>
            <h4 class="text-xs font-bold text-black">Jaga kerahasiaan akun</h4>
            <p class="text-xs text-grey-deep mt-0.5">Gunakan password yang unik dan jangan bagikan kepada siapa pun.</p>
          </div>
        </div>

        {{-- Tombol Aksi Batal & Simpan Perubahan --}}
        <div class="mt-8 flex justify-end items-center gap-3">
          <button type="button"
                  @click="activeView = 'identitas'"
                  class="btn-secondary h-10 px-6 text-xs">
            Batal
          </button>
          <button type="submit"
                  class="btn-primary h-10 px-7 text-xs">
            Simpan perubahan
          </button>
        </div>

      </form>

    </div>
  </div>


  {{-- ============================================================
       VIEW 3: BUAT AKUN BARU / DAFTARKAN ADMIN BARU
       ============================================================ --}}
  <div x-show="activeView === 'tambah_akun'" x-cloak>
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
      <div>
        <h1 class="mt-1 text-3xl font-bold tracking-tight text-[#082450]">Daftarkan Admin Baru</h1>
        <p class="mt-1.5 text-xs text-grey-2">Daftarkan akun administrator baru untuk membantu mengelola sistem polling Vonic DTEDI.</p>
      </div>

      <div>
        <button type="button"
                @click="activeView = 'identitas'"
                class="btn-secondary h-10 px-4 text-xs font-semibold">
          <x-solar-arrow-left-linear class="h-4 w-4" />
          <span>Kembali</span>
        </button>
      </div>
    </div>

    <div class="mt-8 grid grid-cols-1 items-start gap-6 lg:grid-cols-[330px_1fr]">

      {{-- Sisi Kiri: Akun Saat Ini Card --}}
      <div class="rounded-2xl border border-grey-1 bg-white p-6 shadow-type-1">
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
          <p class="truncate text-xs text-grey-2" x-text="currentProfile.email"></p>
        </div>

        <div class="mt-4 flex items-center gap-1.5 text-xs font-semibold text-primary-1">
          <x-solar-check-circle-bold class="h-4 w-4 text-primary-1" />
          <span>Admin Aktif</span>
        </div>
      </div>

      {{-- Sisi Kanan: Form Pendaftaran Admin Baru --}}
      <div class="rounded-2xl border border-grey-1 bg-white p-8 shadow-sm">
        <div class="flex items-start justify-between border-b border-gray-100 pb-4">
          <div>
            <h2 class="text-base font-bold text-black">Form Registrasi Admin</h2>
            <p class="mt-0.5 text-xs text-grey-2">Isi identitas dan kredensial untuk admin baru DTEDI SV UGM.</p>
          </div>
          <div class="flex h-9 w-9 items-center justify-center rounded-full bg-primary-3/60 text-primary-1">
            <x-solar-user-plus-linear class="h-5 w-5" />
          </div>
        </div>

        <form action="{{ route('admin.profil.admin.store') }}" method="POST" class="mt-6 space-y-4 max-w-xl mx-auto">
          @csrf
          
          {{-- Nama Lengkap --}}
          <div>
            <label class="block text-xs font-semibold text-black mb-1.5">Nama Lengkap Admin <span class="text-danger">*</span></label>
            <div class="relative flex items-center">
              <div class="pointer-events-none absolute inset-y-0 left-3.5 flex items-center text-grey-2">
                <x-solar-user-linear class="h-4 w-4" />
              </div>
              <input type="text" name="nama" required
                     placeholder="contoh: Dr. Ir. Budi Santoso, M.Eng."
                     class="h-11 w-full rounded-xl border border-grey-1 bg-white pl-10 pr-3.5 text-xs text-black placeholder:text-grey-2 shadow-xs focus:border-primary-1 focus:outline-none">
            </div>
          </div>

          {{-- Email Institusi --}}
          <div>
            <label class="block text-xs font-semibold text-black mb-1.5">Email Resmi UGM <span class="text-danger">*</span></label>
            <div class="relative flex items-center">
              <div class="pointer-events-none absolute inset-y-0 left-3.5 flex items-center text-grey-2">
                <x-solar-letter-linear class="h-4 w-4" />
              </div>
              <input type="email" name="email" required
                     placeholder="namaadmin@ugm.ac.id"
                     class="h-11 w-full rounded-xl border border-grey-1 bg-white pl-10 pr-3.5 text-xs text-black placeholder:text-grey-2 shadow-xs focus:border-primary-1 focus:outline-none">
            </div>
            <p class="mt-1 text-[11px] text-grey-2">Wajib berakhiran @ugm.ac.id atau @mail.ugm.ac.id</p>
          </div>

          {{-- Unit Kerja --}}
          <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
              <label class="block text-xs font-semibold text-black mb-1.5">Unit Kerja</label>
              <input type="text" name="unit_kerja" value="Departemen TEDIV"
                     class="h-11 w-full rounded-xl border border-grey-1 bg-white px-3.5 text-xs text-black shadow-xs focus:border-primary-1 focus:outline-none">
            </div>
            <div>
              <label class="block text-xs font-semibold text-black mb-1.5">No. Telepon / WhatsApp</label>
              <input type="text" name="no_telp" placeholder="08123456789"
                     class="h-11 w-full rounded-xl border border-grey-1 bg-white px-3.5 text-xs text-black placeholder:text-grey-2 shadow-xs focus:border-primary-1 focus:outline-none">
            </div>
          </div>

          {{-- Password Baru --}}
          <div>
            <label class="block text-xs font-semibold text-black mb-1.5">Password Admin <span class="text-danger">*</span></label>
            <div class="relative flex items-center">
              <div class="pointer-events-none absolute inset-y-0 left-3.5 flex items-center text-grey-2">
                <x-solar-lock-linear class="h-4 w-4" />
              </div>
              <input :type="addAccountForm.showPassword ? 'text' : 'password'" name="password" required
                     placeholder="Minimal 8 karakter"
                     class="h-11 w-full rounded-xl border border-grey-1 bg-white pl-10 pr-10 text-xs text-black placeholder:text-grey-2 shadow-xs focus:border-primary-1 focus:outline-none">
              <button type="button" @click="addAccountForm.showPassword = !addAccountForm.showPassword"
                      class="absolute inset-y-0 right-3.5 flex items-center text-grey-2 hover:text-black">
                <x-solar-eye-closed-linear x-show="!addAccountForm.showPassword" class="h-4 w-4" />
                <x-solar-eye-linear x-show="addAccountForm.showPassword" class="h-4 w-4" />
              </button>
            </div>
          </div>

          {{-- Konfirmasi Password --}}
          <div>
            <label class="block text-xs font-semibold text-black mb-1.5">Konfirmasi Password <span class="text-danger">*</span></label>
            <div class="relative flex items-center">
              <div class="pointer-events-none absolute inset-y-0 left-3.5 flex items-center text-grey-2">
                <x-solar-lock-linear class="h-4 w-4" />
              </div>
              <input :type="addAccountForm.showPassword ? 'text' : 'password'" name="password_confirmation" required
                     placeholder="Ulangi password di atas"
                     class="h-11 w-full rounded-xl border border-grey-1 bg-white pl-10 pr-3.5 text-xs text-black placeholder:text-grey-2 shadow-xs focus:border-primary-1 focus:outline-none">
            </div>
          </div>

          {{-- Tombol Submit & Batal --}}
          <div class="pt-4 space-y-2.5">
            <button type="submit"
                    class="btn-primary h-11 w-full text-xs font-semibold">
              <x-solar-user-plus-linear class="h-4 w-4" />
              <span>Daftarkan Akun Admin</span>
            </button>

            <button type="button"
                    @click="activeView = 'identitas'"
                    class="btn-secondary h-11 w-full text-sm">
              Batal
            </button>
          </div>

          <div class="pt-3 flex items-center justify-center gap-2 text-[11px] text-grey-2">
            <x-solar-shield-check-linear class="h-3.5 w-3.5 text-primary-1" />
            <span>Kredensial admin baru terenkripsi secara aman di database Vonic.</span>
          </div>
        </form>

      </div>

    </div>
  </div>

  {{-- Modal Konfirmasi Hapus Log Aktivitas --}}
  <x-modal show="Boolean(deleteLogTarget)" close="deleteLogTarget = null" maxWidth="sm">
    <div class="p-6 text-center">
      <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-full bg-rose-100 text-danger mb-3">
        <x-solar-trash-bin-trash-linear class="h-6 w-6" />
      </div>
      <h3 class="text-base font-bold text-black">Hapus Log Aktivitas?</h3>
      <p class="mt-1.5 text-xs text-grey-deep leading-relaxed">
        Apakah Anda yakin ingin menghapus catatan log <strong class="text-black" x-text="deleteLogTarget"></strong> dari tampilan riwayat?
      </p>
      <div class="mt-5 flex items-center justify-center gap-2 pt-2">
        <button type="button" @click="deleteLogTarget = null"
                class="btn-secondary h-9 px-4 text-xs font-semibold">
          Batal
        </button>
        <button type="button" @click="confirmDeleteLog()"
                class="btn-danger h-9 px-5 text-xs font-semibold">
          Ya, Hapus
        </button>
      </div>
    </div>
  </x-modal>

</div>
@endsection
