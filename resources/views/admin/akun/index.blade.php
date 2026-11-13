@extends('layouts.admin')

@section('title', 'Pengelolaan Akun Sivitas DTEDI')

@section('content')
@php
  $field = 'h-10 w-full rounded-sm border border-grey-1 bg-white px-3.5 text-xs text-black placeholder:text-grey-2 shadow-xs focus:border-primary-1 focus:outline-none';
@endphp

<div class="space-y-6" x-data="{
  modalMode: null, // null, 'choose', 'manual', 'import_upload', 'import_preview', 'edit', 'delete'
  importFile: null,
  importFileName: '',
  manualData: {
    name: '',
    email: '',
    phone: '',
    role: 'Dosen',
    unit: 'TRIK',
    photo_preview: ''
  },
  editData: {
    id: '',
    name: '',
    email: '',
    phone: '',
    unit: '',
    status: 'Aktif'
  },
  deleteData: {
    id: '',
    name: ''
  },
  openEditModal(account) {
    this.editData = { ...account };
    this.modalMode = 'edit';
  },
  openDeleteModal(account) {
    this.deleteData = { id: account.id, name: account.name };
    this.modalMode = 'delete';
  },
  previewRows: [
    { no: '01', name: 'Melisa Danuar, S.T', email: 'MELISADANUAR@MAIL.UGM.AC.ID', unit: 'TRIK', phone: '+623897702722', valid: true },
    { no: '02', name: 'Budi Santoso, M.Kom', email: 'BUDISANTOSO@MAIL.UGM.AC.ID', unit: 'TEKNO', phone: '+623897702723', valid: true },
    { no: '03', name: 'Siti Rahmawati, M.Cs', email: 'SITI.RAHMA@MAIL.UGM.AC.ID', unit: 'TEDI', phone: '+623897702724', valid: true },
    { no: '04', name: 'Ahmad Fauzan, M.Eng', email: 'AHMADFAUZAN@MAIL.UGM.AC.ID', unit: 'TRIK', phone: '+623897702725', valid: true },
    { no: '05', name: 'Dewi Lestari, A.Md', email: 'DEWI.LESTARI@MAIL.UGM.AC.ID', unit: 'ADMIN', phone: '+623897702726', valid: true },
    { no: '06', name: 'Melisa Danuar, S.T', email: 'MELISADANUAR@MAIL.UGM.AC.ID', unit: 'TRIK', phone: '+623897702722', valid: false },
    { no: '07', name: 'Rian Pratama, S.Kom', email: 'RIAN.PRATAMA@MAIL.UGM.AC.ID', unit: 'TEKNO', phone: '+623897702728', valid: true },
    { no: '08', name: 'Nurul Hidayah, M.T', email: 'NURUL.HIDAYAH@MAIL.UGM.AC.ID', unit: 'TEDI', phone: '+623897702729', valid: true }
  ],
  handleFileUpload(e) {
    const f = e.target.files[0];
    if (f) {
      this.importFile = f;
      this.importFileName = f.name;
      this.modalMode = 'import_preview';
    }
  },
  handleManualPhoto(e) {
    const f = e.target.files[0];
    if (f) {
      this.manualData.photo_preview = URL.createObjectURL(f);
    }
  }
}">

  {{-- Header: Judul Halaman & Tombol Dropdown Tambah Akun Baru (Hover) --}}
  <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
    <div>
      <h1 class="text-2xl font-bold tracking-tight text-black">Pengelolaan Akun Sivitas DTEDI</h1>
      <p class="mt-1 text-sm text-primary-1">Kelola data akun, filter status, dan lakukan aksi pengelolaan dengan mudah.</p>
    </div>

    {{-- Dropdown Tombol Tambah Akun (Aktif Saat Hover) --}}
    <div class="relative inline-block"
         x-data="{ openDropdown: false }"
         @mouseenter="openDropdown = true"
         @mouseleave="openDropdown = false">
      
      {{-- Tombol Utama --}}
      <button type="button"
              class="inline-flex items-center gap-2 rounded-sm bg-gradient-to-r from-primary-1 to-secondary-1 px-5 py-2.5 text-xs font-semibold text-white shadow-primary-glow hover:opacity-95 transition-all">
        <x-solar-add-circle-linear class="h-4 w-4" />
        <span>Tambah Akun Baru</span>
        <x-solar-alt-arrow-down-linear class="h-3 w-3 ml-0.5 transition-transform duration-200"
                                       ::class="openDropdown ? 'rotate-180' : ''" />
      </button>

      {{-- Menu Dropdown yang Muncul Saat Hover (Gaya Navbar Profil) --}}
      <div x-show="openDropdown"
           x-cloak
           x-transition:enter="transition ease-out duration-150"
           x-transition:enter-start="opacity-0 -translate-y-1"
           x-transition:enter-end="opacity-100 translate-y-0"
           x-transition:leave="transition ease-in duration-100"
           x-transition:leave-start="opacity-100 translate-y-0"
           x-transition:leave-end="opacity-0 -translate-y-1"
           class="absolute right-0 top-full z-40 pt-2 w-56">

        <div class="flex flex-col gap-1.5 rounded-xl border border-gray-100 bg-white p-2 shadow-lg">
          {{-- Opsi 1: Input Manual --}}
          <button type="button"
                  @click="modalMode = 'manual'; openDropdown = false"
                  class="flex h-10 w-full items-center gap-2.5 rounded-lg px-3 text-sm font-medium text-gray-700 transition-colors hover:bg-primary-3 hover:text-primary-1 text-left">
            <x-solar-user-plus-linear class="h-4 w-4 shrink-0 text-primary-1" />
            <span>Input Manual</span>
          </button>

          {{-- Opsi 2: Impor CSV / PDF --}}
          <button type="button"
                  @click="modalMode = 'import_upload'; openDropdown = false"
                  class="flex h-10 w-full items-center gap-2.5 rounded-lg px-3 text-sm font-medium text-gray-700 transition-colors hover:bg-primary-3 hover:text-primary-1 text-left">
            <x-solar-document-add-linear class="h-4 w-4 shrink-0 text-primary-1" />
            <span>Impor CSV / PDF</span>
          </button>
        </div>
      </div>
    </div>
  </div>

  {{-- Filter & Form Pencarian --}}
  <form method="GET" action="{{ route('admin.akun.index') }}"
        class="flex flex-wrap items-center gap-3">
    <input type="hidden" name="tab" value="{{ $tab }}">

    {{-- Input Cari Nama --}}
    <div class="relative min-w-[240px] flex-1">
      <div class="pointer-events-none absolute inset-y-0 left-3 flex items-center text-grey-2">
        <x-solar-magnifer-linear class="h-4 w-4" />
      </div>
      <input type="text" name="nama" value="{{ request('nama') }}"
             placeholder="Cari nama civitas..."
             class="{{ $field }} pl-9">
    </div>

    {{-- Select Unit Kerja --}}
    <x-dropdown-select name="unit"
                       :value="request('unit')"
                       placeholder="Pilih unit kerja..."
                       :options="array_merge(['' => 'Semua Unit Kerja'], array_combine($units, $units))"
                       variant="default"
                       class="min-w-[160px]" />

    {{-- Select Status --}}
    <x-dropdown-select name="status"
                       :value="request('status')"
                       placeholder="Semua Status"
                       :options="['' => 'Semua Status', 'Aktif' => 'Aktif', 'Nonaktif' => 'Nonaktif']"
                       variant="default"
                       class="min-w-[140px]" />

    {{-- Tombol Reset Filter --}}
    <a href="{{ route('admin.akun.index', ['tab' => $tab]) }}"
       class="flex h-10 items-center justify-center rounded-sm border border-grey-1 bg-white px-5 text-xs font-semibold text-grey-deep shadow-xs hover:bg-gray-50 hover:text-black transition-all">
      Reset Filter
    </a>

    {{-- Tombol Cari Data --}}
    <button type="submit"
            class="flex h-10 items-center justify-center rounded-sm border border-grey-1 bg-white px-5 text-xs font-semibold text-primary-1 shadow-xs hover:border-primary-1 hover:bg-primary-3/30 transition-all">
      Cari Data
    </button>
  </form>

  {{-- Card Wrapper untuk Tab, Tabel, dan Pagination --}}
  <x-card class="p-6 border border-primary-1 shadow-sm">

    {{-- Tab Segmented Button: Dosen / Tenaga Kependidikan --}}
    <div class="flex items-center gap-3">
      @foreach (['dosen' => 'Dosen', 'tendik' => 'Tenaga Kependidikan'] as $key => $label)
        <a href="{{ route('admin.akun.index', ['tab' => $key]) }}"
           class="flex h-10 items-center justify-center rounded-sm px-7 text-xs font-semibold transition-all {{ $tab === $key ? 'bg-primary-1 text-white shadow-menu' : 'border border-primary-1 bg-white text-primary-1 hover:bg-primary-3/30' }}">
          {{ $label }}
        </a>
      @endforeach
    </div>

    {{-- Tabel Akun Civitas DTEDI (Header Biru Gelap dengan Fixed Table Layout agar Konsisten) --}}
    <div class="mt-6 overflow-x-auto">
      <table class="w-full min-w-[900px] table-fixed border-separate border-spacing-y-2 text-xs">
        <colgroup>
          <col class="w-[5%] min-w-[50px]">   {{-- NO --}}
          <col class="w-[12%] min-w-[110px]"> {{-- ID --}}
          <col class="w-[22%] min-w-[180px]"> {{-- NAMA --}}
          <col class="w-[25%] min-w-[200px]"> {{-- EMAIL --}}
          <col class="w-[10%] min-w-[90px]">  {{-- UNIT KERJA --}}
          <col class="w-[12%] min-w-[120px]"> {{-- NO TELP --}}
          <col class="w-[8%] min-w-[85px]">   {{-- STATUS --}}
          <col class="w-[6%] min-w-[80px]">   {{-- AKSI --}}
        </colgroup>
        <thead>
          <tr class="bg-secondary-1 text-white text-[11px] font-bold tracking-wider text-left">
            <th class="rounded-l-sm py-3 px-3 text-left">NO.</th>
            <th class="py-3 px-3 text-left">ID</th>
            <th class="py-3 px-4 text-left">NAMA</th>
            <th class="py-3 px-4 text-left">EMAIL</th>
            <th class="py-3 px-3 text-left">UNIT KERJA</th>
            <th class="py-3 px-3 text-left">NO. TELP</th>
            <th class="py-3 px-3 text-left">STATUS</th>
            <th class="rounded-r-sm py-3 px-3 text-left">AKSI</th>
          </tr>
        </thead>
        <tbody>
          @forelse ($accounts as $a)
            <tr class="transition-colors hover:bg-primary-3/20 bg-white">
              {{-- No. --}}
              <td class="border-b border-gray-100 py-3.5 px-3 text-left">
                <span class="inline-flex h-7 w-7 items-center justify-center rounded-sm border border-grey-1 bg-white text-xs font-semibold text-black shadow-xs">
                  {{ str_pad($accounts->firstItem() + $loop->index, 2, '0', STR_PAD_LEFT) }}
                </span>
              </td>

              {{-- ID --}}
              <td class="border-b border-gray-100 py-3.5 px-3 text-left font-medium text-black">
                {{ $a['id'] }}
              </td>

              {{-- Nama --}}
              <td class="border-b border-gray-100 py-3.5 px-4 font-semibold text-black text-left">
                {{ $a['name'] }}
              </td>

              {{-- Email --}}
              <td class="border-b border-gray-100 py-3.5 px-4 text-left uppercase tracking-tight text-black text-[11px]">
                {{ $a['email'] }}
              </td>

              {{-- Unit Kerja --}}
              <td class="border-b border-gray-100 py-3.5 px-3 text-left font-medium text-black">
                {{ $a['unit'] }}
              </td>

              {{-- No. Telp --}}
              <td class="border-b border-gray-100 py-3.5 px-3 text-left text-black font-medium">
                {{ $a['phone'] }}
              </td>

              {{-- Status Pill --}}
              <td class="border-b border-gray-100 py-3.5 px-3 text-left">
                @if ($a['status'] === 'Aktif')
                  <span class="inline-flex h-7 w-20 items-center justify-center rounded-full bg-success-soft text-[11px] font-semibold text-success">
                    Aktif
                  </span>
                @else
                  <span class="inline-flex h-7 w-20 items-center justify-center rounded-full bg-danger-soft text-[11px] font-semibold text-danger">
                    Nonaktif
                  </span>
                @endif
              </td>

              {{-- Aksi Edit & Hapus --}}
              <td class="border-b border-gray-100 py-3.5 px-3 text-left">
                <div class="flex items-center justify-start gap-2">
                  <x-action-button type="edit"
                                   @click="openEditModal({{ json_encode($a) }})"
                                   label="Ubah Data Sivitas" />
                  <x-action-button type="delete"
                                   @click="openDeleteModal({{ json_encode($a) }})"
                                   label="Hapus Akun" />
                </div>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="8" class="py-12 bg-white">
                <x-empty-state 
                  icon="solar-user-cross-linear"
                  title="Belum Ada Data Akun Sivitas"
                  message="Data sivitas DTEDI belum tersedia atau tidak ada hasil yang sesuai dengan kriteria pencarian dan filter saat ini."
                  actionText="+ Tambah Akun Baru"
                  actionClick="modalMode = 'manual'" />
              </td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>

    {{-- Footer Pagination --}}
    <div class="mt-6 pt-4 border-t border-gray-100">
      <x-table-pagination :paginator="$accounts" />
    </div>

  </x-card>

  {{-- ================= POP-UP MODAL MULTI-FLOW ================= --}}

  {{-- 1. MODAL FORM INPUT MANUAL (Sesuai Gambar Figma Kedua) --}}
  <x-modal show="modalMode === 'manual'" close="modalMode = null" maxWidth="820px">
    <div class="p-8">
      {{-- Header & Tombol Aksi --}}
      <div class="flex items-center justify-between border-b border-grey-1 pb-4">
        <div>
          <h2 class="text-xl font-bold text-primary-1">Tambah Akun Civitas Baru</h2>
          <p class="mt-0.5 text-xs text-grey-deep">Lengkapi informasi dan data diri civitas di bawah ini untuk membuat akun baru.</p>
        </div>
        <div class="flex items-center gap-2">
          <button type="button" @click="modalMode = null"
                  class="rounded-sm border border-grey-1 px-4 py-2 text-xs font-semibold text-grey-deep hover:bg-gray-50 transition-all">
            Batal
          </button>
          <button type="button" @click="alert('Data akun berhasil disimpan!'); modalMode = null"
                  class="rounded-sm bg-primary-1 px-6 py-2 text-xs font-semibold text-white shadow-sm hover:bg-primary-4 transition-all">
            Simpan
          </button>
        </div>
      </div>

      {{-- Bagian Upload Foto Profil --}}
      <div class="mt-6 flex items-center gap-5">
        <div class="h-28 w-28 shrink-0 overflow-hidden rounded-xl border border-grey-1 bg-gray-50 flex items-center justify-center">
          <template x-if="manualData.photo_preview">
            <img :src="manualData.photo_preview" class="h-full w-full object-cover" alt="Foto Civitas">
          </template>
          <template x-if="!manualData.photo_preview">
            <x-solar-user-linear class="h-12 w-12 text-grey-2" />
          </template>
        </div>

        <div>
          <label class="inline-flex cursor-pointer items-center gap-2 rounded-sm bg-primary-1 px-4 py-2 text-xs font-semibold text-white hover:bg-primary-4 transition-all shadow-xs">
            <x-solar-upload-linear class="h-4 w-4" />
            <span>Upload Gambar</span>
            <input type="file" accept="image/*" class="hidden" @change="handleManualPhoto($event)">
          </label>
          <p class="mt-2 text-[11px] text-grey-2">Format JPG atau PNG, maksimal 2 MB. Gunakan foto resmi/formal.</p>
        </div>
      </div>

      {{-- Input Fields Sesuai Gambar Figma --}}
      <div class="mt-6 space-y-3.5">
        {{-- Nama --}}
        <div class="flex items-center rounded-sm border border-grey-1 bg-white px-3.5 focus-within:border-primary-1">
          <span class="flex items-center gap-2 text-xs font-semibold text-black w-28 shrink-0 border-r border-gray-100 pr-2">
            <x-solar-user-linear class="h-4 w-4 text-primary-1" />
            <span>Nama</span>
          </span>
          <input type="text" x-model="manualData.name"
                 placeholder="Masukkan nama lengkap dan gelar (contoh: Dr. Budi Santoso, M.T.)"
                 class="h-10 w-full pl-3 text-xs text-black placeholder:text-grey-2 focus:outline-none">
        </div>

        {{-- Email --}}
        <div class="flex items-center rounded-sm border border-grey-1 bg-white px-3.5 focus-within:border-primary-1">
          <span class="flex items-center gap-2 text-xs font-semibold text-black w-28 shrink-0 border-r border-gray-100 pr-2">
            <x-solar-letter-linear class="h-4 w-4 text-primary-1" />
            <span>Email</span>
          </span>
          <input type="email" x-model="manualData.email"
                 placeholder="contoh@dtedi.ac.id"
                 class="h-10 w-full pl-3 text-xs text-black placeholder:text-grey-2 focus:outline-none">
        </div>

        {{-- No. Telp --}}
        <div class="flex items-center rounded-sm border border-grey-1 bg-white px-3.5 focus-within:border-primary-1">
          <span class="flex items-center gap-2 text-xs font-semibold text-black w-28 shrink-0 border-r border-gray-100 pr-2">
            <x-solar-phone-linear class="h-4 w-4 text-primary-1" />
            <span>No. Telp</span>
          </span>
          <input type="text" x-model="manualData.phone"
                 placeholder="+62 XXX XXX XXX"
                 class="h-10 w-full pl-3 text-xs text-black placeholder:text-grey-2 focus:outline-none">
        </div>

        {{-- Pekerjaan / Role --}}
        <div class="flex items-center rounded-sm border border-grey-1 bg-white px-3.5 focus-within:border-primary-1">
          <span class="flex items-center gap-2 text-xs font-semibold text-black w-28 shrink-0 border-r border-gray-100 pr-2">
            <x-solar-case-linear class="h-4 w-4 text-primary-1" />
            <span>Pekerjaan</span>
          </span>
          <select x-model="manualData.role" class="h-10 w-full pl-3 text-xs text-black focus:outline-none cursor-pointer bg-transparent">
            <option value="Dosen">Dosen</option>
            <option value="Tenaga Kependidikan">Tenaga Kependidikan</option>
            <option value="Staf Laboratorium">Staf Laboratorium</option>
          </select>
        </div>

        {{-- Unit Kerja --}}
        <div class="flex items-center rounded-sm border border-grey-1 bg-white px-3.5 focus-within:border-primary-1">
          <span class="flex items-center gap-2 text-xs font-semibold text-black w-28 shrink-0 border-r border-gray-100 pr-2">
            <x-solar-buildings-linear class="h-4 w-4 text-primary-1" />
            <span>Unit kerja</span>
          </span>
          <select x-model="manualData.unit" class="h-10 w-full pl-3 text-xs text-black focus:outline-none cursor-pointer bg-transparent">
            <option value="TRIK">TRIK</option>
            <option value="TEKNO">TEKNO</option>
            <option value="TEDI">TEDI</option>
            <option value="ADMIN">ADMIN</option>
          </select>
        </div>
      </div>
    </div>
  </x-modal>

  {{-- 2. MODAL UPLOAD DROPZONE CSV / PDF --}}
  <x-modal show="modalMode === 'import_upload'" close="modalMode = null" maxWidth="820px">
    <div class="p-8">
      <div class="flex items-center justify-between border-b border-grey-1 pb-4">
        <div>
          <h2 class="text-xl font-bold text-primary-1">Impor Data Akun Massal</h2>
          <p class="mt-0.5 text-xs text-grey-deep">Unggah berkas CSV atau PDF data civitas untuk mengimpor akun sekaligus.</p>
        </div>
        <button type="button" @click="modalMode = null" class="text-grey-2 hover:text-black text-2xl font-bold leading-none">&times;</button>
      </div>

      {{-- Dropzone Area dengan Border Putus-putus --}}
      <label class="mt-6 flex flex-col items-center justify-center rounded-2xl border-2 border-dashed border-primary-2 bg-white p-14 text-center cursor-pointer hover:bg-primary-3/20 transition-all">
        <div class="flex h-20 w-20 items-center justify-center rounded-2xl bg-primary-3/40 text-primary-1 mb-4">
          <x-solar-gallery-wide-linear class="h-10 w-10 text-primary-2" />
        </div>
        <p class="text-base font-semibold text-black">Drag and Drop to Upload File</p>
        <p class="text-xs text-grey-2 mt-1">Or</p>
        <span class="mt-4 inline-flex items-center justify-center rounded-sm bg-primary-1 px-8 py-2.5 text-xs font-semibold text-white shadow-sm hover:bg-primary-4 transition-all">
          Upload
        </span>
        <span class="mt-3 text-[11px] text-grey-2">Mendukung format file .CSV atau .PDF (Maksimal 10 MB)</span>
        <input type="file" accept=".csv, .pdf" class="hidden" @change="handleFileUpload($event)">
      </label>


    </div>
  </x-modal>

  {{-- 3. MODAL PREVIEW TABEL HASIL IMPOR --}}
  <x-modal show="modalMode === 'import_preview'" close="modalMode = null" maxWidth="820px">
    <div class="p-8 max-h-[85vh] flex flex-col">
      {{-- Header Modal Preview & Tombol Simpan --}}
      <div class="flex items-center justify-between border-b border-grey-1 pb-4">
        <div>
          <h2 class="text-xl font-bold text-primary-1">Preview Data Impor</h2>
          <p class="mt-0.5 text-xs text-grey-deep">Periksa kembali data dari file <strong x-text="importFileName || 'civitas_dtedi.csv'"></strong> sebelum disimpan ke sistem.</p>
        </div>
        
        <div class="flex items-center gap-2">
          <button type="button" @click="modalMode = 'import_upload'"
                  class="rounded-sm border border-grey-1 px-4 py-2 text-xs font-semibold text-grey-deep hover:bg-gray-50">
            Ganti File
          </button>
          <button type="button" @click="alert('Seluruh data impor yang valid berhasil disimpan!'); modalMode = null"
                  class="rounded-sm bg-primary-1 px-6 py-2 text-xs font-semibold text-white shadow-sm hover:bg-primary-4 transition-all">
            Simpan
          </button>
        </div>
      </div>

      {{-- Notifikasi Info Status Validasi --}}
      <div class="mt-4 flex flex-wrap items-center gap-3">
        <span class="inline-flex items-center gap-1.5 rounded-full bg-success-soft px-3 py-1 text-xs font-semibold text-success">
          <x-solar-check-circle-bold class="h-4 w-4" />
          <span>7 Data berhasil teridentifikasi</span>
        </span>
        <span class="inline-flex items-center gap-1.5 rounded-full bg-danger-soft px-3 py-1 text-xs font-semibold text-danger">
          <x-solar-danger-circle-bold class="h-4 w-4" />
          <span>1 Data duplikat / terdapat kesalahan format</span>
        </span>
      </div>

      {{-- Tabel Preview (Header Biru Gelap) --}}
      <div class="mt-4 overflow-y-auto overflow-x-auto flex-1 border border-grey-1 rounded-sm max-h-[360px]">
        <table class="w-full min-w-[750px] border-separate border-spacing-0 text-xs">
          <thead class="sticky top-0 z-10">
            <tr class="bg-secondary-1 text-white text-[11px] font-bold tracking-wider text-left">
              <th class="py-3 px-3 text-left w-14">NO.</th>
              <th class="py-3 px-4 text-left">NAMA</th>
              <th class="py-3 px-4 text-left">EMAIL</th>
              <th class="py-3 px-3 text-left w-28">UNIT KERJA</th>
              <th class="py-3 px-3 text-left w-36">NO. TELP</th>
              <th class="py-3 px-3 text-left w-24">STATUS</th>
            </tr>
          </thead>
          <tbody>
            <template x-for="(row, idx) in previewRows" :key="idx">
              <tr class="border-b border-gray-100 transition-colors"
                  :class="row.valid ? 'hover:bg-primary-3/20' : 'bg-red-50/70 hover:bg-red-100/60'">
                {{-- No. --}}
                <td class="py-3 px-3 text-left">
                  <span class="inline-flex h-6 w-6 items-center justify-center rounded-sm border border-grey-1 bg-white text-[11px] font-semibold text-black"
                        x-text="row.no"></span>
                </td>
                {{-- Nama --}}
                <td class="py-3 px-4 font-semibold text-black text-left" x-text="row.name"></td>
                {{-- Email --}}
                <td class="py-3 px-4 text-black text-[11px] text-left uppercase" x-text="row.email"></td>
                {{-- Unit --}}
                <td class="py-3 px-3 text-left font-medium text-black" x-text="row.unit"></td>
                {{-- No. Telp --}}
                <td class="py-3 px-3 text-left font-medium text-black" x-text="row.phone"></td>
                {{-- Status Validasi Row --}}
                <td class="py-3 px-3 text-left">
                  <template x-if="row.valid">
                    <span class="inline-flex items-center rounded-full bg-success-soft px-2 py-0.5 text-[10px] font-bold text-success">
                      Valid
                    </span>
                  </template>
                  <template x-if="!row.valid">
                    <span class="inline-flex items-center rounded-full bg-danger-soft px-2 py-0.5 text-[10px] font-bold text-danger" title="Email sudah terdaftar">
                      Duplikat
                    </span>
                  </template>
                </td>
              </tr>
            </template>
          </tbody>
        </table>
      </div>

      {{-- Footer Modal --}}
      <div class="mt-4 flex items-center pt-2 border-t border-grey-1">
        <span class="text-xs text-grey-deep">Total: <strong class="text-black" x-text="previewRows.length"></strong> baris ditemukan</span>
      </div>
    </div>
  </x-modal>

  {{-- Modal 4: Edit Akun Sivitas DTEDI --}}
  <x-modal show="modalMode === 'edit'" close="modalMode = null" maxWidth="md">
    <div class="p-6">
      <div class="flex items-center justify-between border-b border-grey-1 pb-3">
        <div class="flex items-center gap-2">
          <div class="flex h-9 w-9 items-center justify-center rounded-sm bg-primary-3 text-primary-1">
            <x-solar-pen-new-square-linear class="h-5 w-5" />
          </div>
          <div>
            <h3 class="text-base font-bold text-black">Ubah Data Sivitas</h3>
            <p class="text-[11px] text-grey-2" x-text="'ID: ' + editData.id"></p>
          </div>
        </div>
        <button type="button" @click="modalMode = null" class="text-grey-2 hover:text-black text-xl font-bold leading-none">&times;</button>
      </div>

      <form action="{{ route('admin.akun.store') }}" method="POST" class="mt-4 space-y-3.5">
        @csrf
        <input type="hidden" name="_method" value="POST">
        <input type="hidden" name="id" :value="editData.id">

        {{-- Nama Lengkap --}}
        <div>
          <label class="block text-xs font-semibold text-black mb-1">Nama Lengkap & Gelar</label>
          <input type="text" name="name" x-model="editData.name" class="{{ $field }}" required>
        </div>

        {{-- Email --}}
        <div>
          <label class="block text-xs font-semibold text-black mb-1">Email Institusi UGM</label>
          <input type="email" name="email" x-model="editData.email" class="{{ $field }}" required>
        </div>

        {{-- Unit Kerja & No Telp (Grid 2 Kolom) --}}
        <div class="grid grid-cols-2 gap-3">
          <div>
            <label class="block text-xs font-semibold text-black mb-1">Unit Kerja</label>
            <select name="unit" x-model="editData.unit" class="{{ $field }} cursor-pointer">
              @foreach ($units as $u)
                <option value="{{ $u }}">{{ $u }}</option>
              @endforeach
            </select>
          </div>
          <div>
            <label class="block text-xs font-semibold text-black mb-1">No. WhatsApp</label>
            <input type="text" name="phone" x-model="editData.phone" class="{{ $field }}" required>
          </div>
        </div>

        {{-- Status Akun --}}
        <div>
          <label class="block text-xs font-semibold text-black mb-1">Status Partisipasi</label>
          <select name="status" x-model="editData.status" class="{{ $field }} cursor-pointer">
            <option value="Aktif">Aktif (Dapat Memilih)</option>
            <option value="Nonaktif">Nonaktif (Ditangguhkan)</option>
          </select>
        </div>

        {{-- Tombol Batal & Simpan --}}
        <div class="mt-5 flex items-center justify-end gap-2 pt-3 border-t border-grey-1">
          <button type="button" @click="modalMode = null"
                  class="rounded-sm border border-grey-1 bg-white px-4 py-2 text-xs font-semibold text-grey-deep hover:bg-gray-50 transition-all cursor-pointer">
            Batal
          </button>
          <button type="button"
                  @click="modalMode = null; alert('Perubahan data akun sivitas berhasil disimpan!')"
                  class="rounded-sm bg-primary-1 px-5 py-2 text-xs font-semibold text-white shadow-primary-glow hover:bg-primary-4 transition-all cursor-pointer">
            Simpan Perubahan
          </button>
        </div>
      </form>
    </div>
  </x-modal>

  {{-- Modal 5: Konfirmasi Hapus Akun Sivitas --}}
  <x-modal show="modalMode === 'delete'" close="modalMode = null" maxWidth="sm">
    <div class="p-6 text-center">
      <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-full bg-rose-100 text-danger mb-3">
        <x-solar-trash-bin-trash-linear class="h-6 w-6" />
      </div>
      <h3 class="text-base font-bold text-black">Hapus Akun Sivitas?</h3>
      <p class="mt-1 text-xs text-grey-deep">
        Apakah Anda yakin ingin menghapus akun <strong class="text-black" x-text="deleteData.name"></strong>? Data yang dihapus tidak dapat dipulihkan.
      </p>

      <div class="mt-5 flex items-center justify-center gap-2 pt-2">
        <button type="button" @click="modalMode = null"
                class="rounded-sm border border-grey-1 bg-white px-4 py-2 text-xs font-semibold text-grey-deep hover:bg-gray-50 transition-all cursor-pointer">
          Batal
        </button>
        <button type="button"
                @click="modalMode = null; alert('Akun ' + deleteData.name + ' berhasil dihapus dari sistem.')"
                class="rounded-sm bg-danger px-5 py-2 text-xs font-semibold text-white shadow-xs hover:bg-rose-700 transition-all cursor-pointer">
          Ya, Hapus
        </button>
      </div>
    </div>
  </x-modal>

</div>
@endsection
