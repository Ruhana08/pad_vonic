@extends('layouts.admin')

@section('title', 'Kelola Akun Admin')

@section('content')
@php
  $field = 'h-10 w-full rounded-xl border border-grey-1 bg-white px-3.5 text-xs text-black placeholder:text-grey-2 shadow-xs focus:border-primary-1 focus:outline-none';
@endphp

<script>
function kelolaAdminPage(admins) {
  return {
    admins: admins || [],
    modalMode: null, // 'add' | 'edit' | 'delete'
    showAddPass: false,
    showAddConfirm: false,
    showEditPass: false,
    showEditConfirm: false,
    addData: {
      nama: '',
      email: '',
      password: '',
      password_confirmation: ''
    },
    editData: {
      id: '',
      nama: '',
      email: '',
      password: '',
      password_confirmation: ''
    },
    deleteData: {
      id: '',
      nama: '',
      email: ''
    },
    openAddModal() {
      this.addData = { nama: '', email: '', password: '', password_confirmation: '' };
      this.showAddPass = false;
      this.showAddConfirm = false;
      this.modalMode = 'add';
    },
    openEditModal(admin) {
      this.editData = {
        id: admin.id,
        nama: admin.nama,
        email: admin.email,
        password: '',
        password_confirmation: ''
      };
      this.showEditPass = false;
      this.showEditConfirm = false;
      this.modalMode = 'edit';
    },
    openDeleteModal(admin) {
      this.deleteData = { id: admin.id, nama: admin.nama, email: admin.email };
      this.modalMode = 'delete';
    }
  };
}
</script>

<div class="space-y-6" x-data="kelolaAdminPage(@js($admins))">

  {{-- Header --}}
  <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
    <div>
      <div class="inline-flex items-center gap-2 rounded-full bg-primary-3/50 px-3 py-1 text-[11px] font-bold text-primary-1 mb-1.5">
        <x-solar-shield-user-bold class="h-3.5 w-3.5" />
        <span>Khusus Master Admin</span>
      </div>
      <h1 class="text-2xl font-bold tracking-tight text-black">Kelola Akun Admin</h1>
      <p class="mt-0.5 text-xs text-grey-deep">Daftarkan, perbarui, atau hapus akun administrator Vonic (wajib menggunakan email @mail.ugm.ac.id).</p>
    </div>

    <button type="button"
            @click="openAddModal()"
            class="btn-primary h-10 px-4 text-xs font-semibold shrink-0">
      <x-solar-user-plus-linear class="h-4 w-4" />
      <span>Tambah Admin Baru</span>
    </button>
  </div>

  {{-- Validation Error Alert --}}
  @if ($errors->any())
    <div class="rounded-xl border border-danger/30 bg-rose-50 px-4 py-3 text-xs text-danger">
      <p class="font-bold mb-1">Gagal menyimpan data admin:</p>
      <ul class="list-disc list-inside space-y-0.5">
        @foreach ($errors->all() as $err)
          <li>{{ $err }}</li>
        @endforeach
      </ul>
    </div>
  @endif

  {{-- Card Tabel Daftar Admin --}}
  <x-card class="p-6 shadow-sm">
    {{-- Search Bar --}}
    <form method="GET" action="{{ route('admin.kelola-admin.index') }}" class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
      <div class="relative flex-1 max-w-md">
        <div class="pointer-events-none absolute inset-y-0 left-3 flex items-center text-grey-2">
          <x-solar-magnifer-linear class="h-4 w-4" />
        </div>
        <input type="text"
               name="search"
               value="{{ $search }}"
               placeholder="Cari nama atau email admin..."
               class="h-10 w-full rounded-xl border border-grey-1 bg-white pl-9 pr-4 text-xs text-black placeholder:text-grey-2 shadow-xs focus:border-primary-1 focus:outline-none">
      </div>
      <div class="flex items-center gap-2">
        <button type="submit" class="btn-secondary h-10 px-4 text-xs font-semibold">Cari</button>
        @if ($search !== '')
          <a href="{{ route('admin.kelola-admin.index') }}" class="btn-secondary h-10 px-4 text-xs font-semibold">Reset</a>
        @endif
      </div>
    </form>

    {{-- Tabel Admin --}}
    <div class="mt-5 overflow-x-auto">
      <table class="w-full min-w-[760px] border-separate border-spacing-y-2 text-xs">
        <thead>
          <tr class="bg-secondary-1 text-white text-xs font-bold tracking-wider text-center">
            <th class="rounded-l-sm py-3 px-3 w-px whitespace-nowrap">NO.</th>
            <th class="py-3 px-4 text-left">NAMA ADMIN</th>
            <th class="py-3 px-4 text-left">EMAIL INSTITUSI</th>
            <th class="py-3 px-3 text-center w-px whitespace-nowrap">PERAN</th>
            <th class="py-3 px-4 text-center w-px whitespace-nowrap">TERDAFTAR</th>
            <th class="rounded-r-sm py-3 px-3 text-center w-px whitespace-nowrap">AKSI</th>
          </tr>
        </thead>
        <tbody>
          <template x-for="(adm, idx) in admins" :key="adm.id">
            <tr class="bg-white transition-colors duration-150 hover:bg-[#F3F7FF]">
              <td class="border-b border-gray-100 py-3.5 px-3 text-center">
                <span class="inline-flex h-7 w-7 items-center justify-center rounded-xl border border-grey-1 bg-white text-xs font-semibold text-black shadow-xs"
                      x-text="String(idx + 1).padStart(2, '0')"></span>
              </td>
              <td class="border-b border-gray-100 py-3.5 px-4 font-semibold text-black">
                <div class="flex items-center gap-2">
                  <span x-text="adm.nama"></span>
                  <span x-show="adm.is_self" x-cloak class="rounded-full bg-gray-100 px-2 py-0.5 text-[10px] font-semibold text-grey-deep">Anda</span>
                </div>
              </td>
              <td class="border-b border-gray-100 py-3.5 px-4 text-grey-deep" x-text="adm.email"></td>
              <td class="border-b border-gray-100 py-3.5 px-3 text-center">
                <span class="inline-flex items-center rounded-full px-3 py-1 text-[11px] font-semibold whitespace-nowrap"
                      :class="adm.is_master ? 'bg-amber-50 text-amber-700 border border-amber-200' : 'bg-primary-3/50 text-primary-1'"
                      x-text="adm.is_master ? 'Master Admin' : 'Admin'"></span>
              </td>
              <td class="border-b border-gray-100 py-3.5 px-4 text-center text-grey-deep" x-text="adm.created_at"></td>
              <td class="border-b border-gray-100 py-3.5 px-3 text-center">
                <div class="flex items-center justify-center gap-1.5">
                  <button type="button"
                          @click="openEditModal(adm)"
                          title="Edit Akun Admin"
                          class="btn-icon text-primary-1">
                    <x-solar-pen-new-square-linear class="h-4 w-4" />
                  </button>
                  <template x-if="!adm.is_master && !adm.is_self">
                    <button type="button"
                            @click="openDeleteModal(adm)"
                            title="Hapus Akun Admin"
                            class="btn-icon btn-icon-danger">
                      <x-solar-trash-bin-trash-linear class="h-4 w-4" />
                    </button>
                  </template>
                </div>
              </td>
            </tr>
          </template>
          <template x-if="admins.length === 0">
            <tr>
              <td colspan="6" class="py-10 text-center text-xs text-grey-deep bg-white">
                Tidak ada akun admin yang ditemukan.
              </td>
            </tr>
          </template>
        </tbody>
      </table>
    </div>
  </x-card>

  {{-- ================= MODAL TAMBAH / EDIT / HAPUS ADMIN ================= --}}
  <div x-show="modalMode" x-cloak
       class="fixed inset-0 z-50 flex items-center justify-center p-4"
       style="background-color: rgba(0,0,0,0.22); backdrop-filter: blur(3px);"
       @keydown.escape.window="modalMode = null">
    <div class="w-full max-w-[440px] rounded-2xl bg-white p-6 shadow-2xl" @click.outside="modalMode = null">

      {{-- Modal: Tambah Admin --}}
      <template x-if="modalMode === 'add'">
        <form method="POST" action="{{ route('admin.kelola-admin.store') }}" class="space-y-4">
          @csrf
          <div class="flex items-center justify-between border-b border-grey-1 pb-3">
            <div>
              <h3 class="text-base font-bold text-black">Tambah Akun Admin</h3>
              <p class="text-[11px] text-grey-deep mt-0.5">Akun baru dapat langsung login menggunakan email & kata sandi ini.</p>
            </div>
            <button type="button" @click="modalMode = null" class="text-grey-2 hover:text-black cursor-pointer">
              <x-solar-close-circle-linear class="h-5 w-5" />
            </button>
          </div>

          <div>
            <label class="block text-xs font-semibold text-black mb-1">Nama Lengkap</label>
            <input type="text" name="nama" x-model="addData.nama" required placeholder="Nama admin..." class="{{ $field }}">
          </div>

          <div>
            <label class="block text-xs font-semibold text-black mb-1">Email Institusi (@mail.ugm.ac.id)</label>
            <input type="email" name="email" x-model="addData.email" required placeholder="nama@mail.ugm.ac.id" class="{{ $field }}">
          </div>

          <div>
            <label class="block text-xs font-semibold text-black mb-1">Kata Sandi</label>
            <div class="relative">
              <input :type="showAddPass ? 'text' : 'password'" name="password" x-model="addData.password" required minlength="8" placeholder="Minimal 8 karakter" class="{{ $field }} pr-10">
              <button type="button" @click="showAddPass = !showAddPass" class="absolute inset-y-0 right-3 flex items-center text-grey-2 hover:text-black cursor-pointer">
                <x-solar-eye-closed-linear x-show="!showAddPass" class="h-4 w-4" />
                <x-solar-eye-linear x-show="showAddPass" x-cloak class="h-4 w-4" />
              </button>
            </div>
          </div>

          <div>
            <label class="block text-xs font-semibold text-black mb-1">Konfirmasi Kata Sandi</label>
            <div class="relative">
              <input :type="showAddConfirm ? 'text' : 'password'" name="password_confirmation" x-model="addData.password_confirmation" required minlength="8" placeholder="Ulangi kata sandi" class="{{ $field }} pr-10">
              <button type="button" @click="showAddConfirm = !showAddConfirm" class="absolute inset-y-0 right-3 flex items-center text-grey-2 hover:text-black cursor-pointer">
                <x-solar-eye-closed-linear x-show="!showAddConfirm" class="h-4 w-4" />
                <x-solar-eye-linear x-show="showAddConfirm" x-cloak class="h-4 w-4" />
              </button>
            </div>
          </div>

          <div class="pt-2 flex justify-end gap-2">
            <button type="button" @click="modalMode = null" class="btn-secondary h-10 px-4 text-xs font-semibold">Batal</button>
            <button type="submit" class="btn-primary h-10 px-5 text-xs font-semibold">Simpan Admin</button>
          </div>
        </form>
      </template>

      {{-- Modal: Edit Admin --}}
      <template x-if="modalMode === 'edit'">
        <form method="POST" :action="'/admin/kelola-admin/' + editData.id" class="space-y-4">
          @csrf
          @method('PUT')
          <div class="flex items-center justify-between border-b border-grey-1 pb-3">
            <div>
              <h3 class="text-base font-bold text-black">Edit Akun Admin</h3>
              <p class="text-[11px] text-grey-deep mt-0.5">Kosongkan kata sandi jika tidak ingin mengubahnya.</p>
            </div>
            <button type="button" @click="modalMode = null" class="text-grey-2 hover:text-black cursor-pointer">
              <x-solar-close-circle-linear class="h-5 w-5" />
            </button>
          </div>

          <div>
            <label class="block text-xs font-semibold text-black mb-1">Nama Lengkap</label>
            <input type="text" name="nama" x-model="editData.nama" required class="{{ $field }}">
          </div>

          <div>
            <label class="block text-xs font-semibold text-black mb-1">Email Institusi (@mail.ugm.ac.id)</label>
            <input type="email" name="email" x-model="editData.email" required class="{{ $field }}">
          </div>

          <div>
            <label class="block text-xs font-semibold text-black mb-1">Kata Sandi Baru <span class="text-grey-2 font-normal">(opsional)</span></label>
            <div class="relative">
              <input :type="showEditPass ? 'text' : 'password'" name="password" x-model="editData.password" minlength="8" placeholder="Kosongkan jika tetap" class="{{ $field }} pr-10">
              <button type="button" @click="showEditPass = !showEditPass" class="absolute inset-y-0 right-3 flex items-center text-grey-2 hover:text-black cursor-pointer">
                <x-solar-eye-closed-linear x-show="!showEditPass" class="h-4 w-4" />
                <x-solar-eye-linear x-show="showEditPass" x-cloak class="h-4 w-4" />
              </button>
            </div>
          </div>

          <div>
            <label class="block text-xs font-semibold text-black mb-1">Konfirmasi Kata Sandi Baru</label>
            <div class="relative">
              <input :type="showEditConfirm ? 'text' : 'password'" name="password_confirmation" x-model="editData.password_confirmation" minlength="8" placeholder="Ulangi jika mengubah kata sandi" class="{{ $field }} pr-10">
              <button type="button" @click="showEditConfirm = !showEditConfirm" class="absolute inset-y-0 right-3 flex items-center text-grey-2 hover:text-black cursor-pointer">
                <x-solar-eye-closed-linear x-show="!showEditConfirm" class="h-4 w-4" />
                <x-solar-eye-linear x-show="showEditConfirm" x-cloak class="h-4 w-4" />
              </button>
            </div>
          </div>

          <div class="pt-2 flex justify-end gap-2">
            <button type="button" @click="modalMode = null" class="btn-secondary h-10 px-4 text-xs font-semibold">Batal</button>
            <button type="submit" class="btn-primary h-10 px-5 text-xs font-semibold">Simpan Perubahan</button>
          </div>
        </form>
      </template>

      {{-- Modal: Hapus Admin --}}
      <template x-if="modalMode === 'delete'">
        <form method="POST" :action="'/admin/kelola-admin/' + deleteData.id" class="text-center space-y-4">
          @csrf
          @method('DELETE')
          <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-2xl bg-rose-50 border border-danger/20">
            <x-solar-trash-bin-trash-linear class="h-6 w-6 text-danger" />
          </div>
          <div>
            <h3 class="text-sm font-bold text-black">Hapus Akun Admin?</h3>
            <p class="mt-1.5 text-xs text-grey-deep leading-relaxed">
              Akun <strong class="text-black" x-text="deleteData.nama"></strong> (<span x-text="deleteData.email"></span>) akan dihapus permanen dan tidak bisa masuk ke panel admin lagi.
            </p>
          </div>
          <div class="pt-2 flex justify-center gap-2">
            <button type="button" @click="modalMode = null" class="btn-secondary h-9 px-4 text-xs font-semibold">Batal</button>
            <button type="submit" class="btn-danger h-9 px-5 text-xs font-semibold">Ya, Hapus</button>
          </div>
        </form>
      </template>

    </div>
  </div>

</div>
@endsection
