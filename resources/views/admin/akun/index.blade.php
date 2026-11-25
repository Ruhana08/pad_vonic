@extends('layouts.admin')

@section('title', 'Pengelolaan Akun Sivitas DTEDI')

@section('content')
@php
  $field = 'h-10 w-full rounded-xl border border-grey-1 bg-white px-3.5 text-xs text-black placeholder:text-grey-2 shadow-xs focus:border-primary-1 focus:outline-none';
@endphp

<div class="space-y-6" x-data="{
  modalMode: null, // null, 'choose', 'manual', 'import_upload', 'import_preview', 'edit', 'delete'
  importFile: null,
  importFileName: '',
  manualData: {
    name: '',
    email: '',
    phone: '',
    role: 'dosen',
    unit: 'Teknologi Rekayasa Perangkat Lunak',
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
  previewRows: [],
  get validCount() {
    return this.previewRows.filter(r => r.valid).length;
  },
  get invalidCount() {
    return this.previewRows.filter(r => !r.valid).length;
  },
  handleFileUpload(e) {
    const f = e.target.files[0];
    if (!f) return;
    this.importFile = f;
    this.importFileName = f.name;
    const csvInput = document.getElementById('csvFileInput');
    if (csvInput) {
      const dt = new DataTransfer();
      dt.items.add(f);
      csvInput.files = dt.files;
    }
    const reader = new FileReader();
    reader.onload = (ev) => {
      const lines = (ev.target.result || '').split(/\r?\n/).filter(l => l.trim() !== '');
      const dataLines = lines.slice(1); // skip header
      this.previewRows = dataLines.map((line, i) => {
        const cols = line.split(',').map(c => c.trim());
        const nip   = cols[0] || '';
        const name  = cols[1] || '';
        const email = cols[2] || '';
        const unit  = cols[3] || '-';
        const phone = cols[4] || '-';
        const valid = Boolean(nip && name && email && email.includes('@'));
        return {
          no: String(i + 1).padStart(2, '0'),
          nip,
          name,
          email: email.toUpperCase(),
          unit,
          phone,
          valid
        };
      });
      this.modalMode = 'import_preview';
    };
    reader.readAsText(f);
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
      
      {{-- Tombol Utama Tambah Akun Baru (Size LG / h-11) --}}
      <button type="button"
              class="btn-gradient h-11 px-5 text-sm">
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
        class="flex flex-col lg:flex-row lg:items-center gap-3 w-full">
    <input type="hidden" name="tab" value="{{ $tab }}">

    {{-- Input Cari Nama --}}
    <div class="relative flex-1 min-w-[200px]">
      <div class="pointer-events-none absolute inset-y-0 left-3 flex items-center text-grey-2">
        <x-solar-magnifer-linear class="h-4 w-4" />
      </div>
      <input type="text" name="nama" value="{{ request('nama') }}"
             placeholder="Cari nama civitas..."
             class="{{ $field }} pl-9 w-full">
    </div>

    {{-- Filter Dropdowns & Tombol Aksi --}}
    <div class="flex flex-wrap items-center gap-3 shrink-0">
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
         class="btn-secondary h-10 px-5 text-sm inline-flex items-center justify-center">
        Reset Filter
      </a>

      {{-- Tombol Cari Data --}}
      <button type="submit"
              class="btn-secondary h-10 px-5 text-sm inline-flex items-center justify-center">
        Cari Data
      </button>
    </div>
  </form>

  {{-- Card Wrapper untuk Tab, Tabel, dan Pagination --}}
  <x-card class="p-6 shadow-sm">

    {{-- Tab Segmented Button: Dosen / Tenaga Kependidikan --}}
    <div class="flex items-center gap-3">
      @foreach (['dosen' => 'Dosen', 'tendik' => 'Tenaga Kependidikan'] as $key => $label)
        <a href="{{ route('admin.akun.index', ['tab' => $key]) }}"
           class="h-10 px-7 text-xs font-semibold {{ $tab === $key ? 'btn-primary' : 'btn-secondary' }}">
          {{ $label }}
        </a>
      @endforeach
    </div>

    {{-- Tabel Akun Civitas DTEDI (Proporsional dengan Horizontal Scroll) --}}
    <div class="mt-6 overflow-x-auto">
      <table class="w-full min-w-[1060px] border-separate border-spacing-y-2 text-xs">
        <colgroup>
          <col class="w-[56px]">               {{-- NO --}}
          <col class="min-w-[125px]">          {{-- ID --}}
          <col class="min-w-[200px]">          {{-- NAMA --}}
          <col class="min-w-[220px]">          {{-- EMAIL --}}
          <col class="min-w-[160px]">          {{-- UNIT KERJA --}}
          <col class="w-[140px]">              {{-- NO TELP --}}
          <col class="w-[95px]">               {{-- STATUS --}}
          <col class="w-[100px]">              {{-- AKSI --}}
        </colgroup>
        <thead>
          <tr class="bg-secondary-1 text-white text-xs font-bold tracking-wider text-center">
            <th class="rounded-l-sm py-3 px-3 text-center">NO.</th>
            <th class="py-3 px-3 text-center">ID</th>
            <th class="py-3 px-4 text-center">NAMA</th>
            <th class="py-3 px-4 text-center">EMAIL</th>
            <th class="py-3 px-3 text-center">UNIT KERJA</th>
            <th class="py-3 px-3 text-center">NO. TELP</th>
            <th class="py-3 px-3 text-center">STATUS</th>
            <th class="rounded-r-sm py-3 px-3 text-center">AKSI</th>
          </tr>
        </thead>
        <tbody>
          @forelse ($accounts as $a)
            <tr class="transition-colors duration-150 hover:bg-[#F3F7FF] bg-white">
              {{-- No. --}}
              <td class="border-b border-gray-100 py-3.5 px-3 text-left">
                <span class="inline-flex h-7 w-7 items-center justify-center rounded-xl border border-grey-1 bg-white text-xs font-semibold text-black shadow-xs">
                  {{ str_pad($accounts->firstItem() + $loop->index, 2, '0', STR_PAD_LEFT) }}
                </span>
              </td>

              {{-- ID --}}
              <td class="border-b border-gray-100 py-3.5 px-3 text-left font-medium text-black whitespace-nowrap">
                {{ $a['id'] }}
              </td>

              {{-- Nama --}}
              <td class="border-b border-gray-100 py-3.5 px-4 font-semibold text-black text-left">
                {{ $a['name'] }}
              </td>

              {{-- Email --}}
              <td class="border-b border-gray-100 py-3.5 px-4 text-left uppercase tracking-tight text-black text-xs">
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
                  <span class="inline-flex h-7 w-20 items-center justify-center rounded-full bg-success-soft text-xs font-semibold text-success">
                    Aktif
                  </span>
                @else
                  <span class="inline-flex h-7 w-20 items-center justify-center rounded-full bg-danger-soft text-xs font-semibold text-danger">
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
                  message="Data sivitas DTEDI belum tersedia atau tidak ada hasil yang sesuai dengan kriteria pencarian dan filter saat ini." />
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
    <form action="{{ route('admin.akun.store') }}" method="POST" class="p-8">
      @csrf
      {{-- Header & Tombol Aksi --}}
      <div class="flex items-center justify-between border-b border-grey-1 pb-4">
        <div>
          <h2 class="text-xl font-bold text-primary-1">Tambah Akun Civitas Baru</h2>
          <p class="mt-0.5 text-xs text-grey-deep">Lengkapi informasi dan data diri civitas di bawah ini untuk membuat akun baru.</p>
        </div>
        <div class="flex items-center gap-2">
          <button type="button" @click="modalMode = null"
                  class="rounded-xl border border-grey-1 px-4 py-2 text-xs font-semibold text-grey-deep hover:bg-gray-50 transition-all cursor-pointer">
            Batal
          </button>
          <button type="submit"
                  class="rounded-xl bg-primary-1 px-6 py-2 text-xs font-semibold text-white shadow-sm hover:bg-primary-4 transition-all cursor-pointer">
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
          <label class="inline-flex cursor-pointer items-center gap-2 rounded-xl bg-primary-1 px-4 py-2 text-xs font-semibold text-white hover:bg-primary-4 transition-all shadow-xs">
            <x-solar-upload-linear class="h-4 w-4" />
            <span>Upload Gambar</span>
            <input type="file" accept="image/*" class="hidden" @change="handleManualPhoto($event)">
          </label>
          <p class="mt-2 text-xs text-grey-2">Format JPG atau PNG, maksimal 2 MB. Gunakan foto resmi/formal.</p>
        </div>
      </div>

      {{-- Input Fields Sesuai Gambar Figma --}}
      <div class="mt-6 space-y-3.5">
        {{-- NIP / ID --}}
        <div class="flex items-center rounded-xl border border-grey-1 bg-white px-3.5 focus-within:border-primary-1">
          <span class="flex items-center gap-2 text-xs font-semibold text-black w-28 shrink-0 border-r border-gray-100 pr-2">
            <x-solar-card-linear class="h-4 w-4 text-primary-1" />
            <span>NIP / ID</span>
          </span>
          <input type="text" name="id" x-model="manualData.id" required
                 placeholder="Nomor identitas (contoh: 198501012010121001)"
                 class="h-10 w-full pl-3 text-xs text-black placeholder:text-grey-2 focus:outline-none">
        </div>

        {{-- Nama --}}
        <div class="flex items-center rounded-xl border border-grey-1 bg-white px-3.5 focus-within:border-primary-1">
          <span class="flex items-center gap-2 text-xs font-semibold text-black w-28 shrink-0 border-r border-gray-100 pr-2">
            <x-solar-user-linear class="h-4 w-4 text-primary-1" />
            <span>Nama</span>
          </span>
          <input type="text" name="name" x-model="manualData.name" required
                 placeholder="Masukkan nama lengkap dan gelar (contoh: Dr. Budi Santoso, M.T.)"
                 class="h-10 w-full pl-3 text-xs text-black placeholder:text-grey-2 focus:outline-none">
        </div>

        {{-- Email --}}
        <div class="flex items-center rounded-xl border border-grey-1 bg-white px-3.5 focus-within:border-primary-1">
          <span class="flex items-center gap-2 text-xs font-semibold text-black w-28 shrink-0 border-r border-gray-100 pr-2">
            <x-solar-letter-linear class="h-4 w-4 text-primary-1" />
            <span>Email</span>
          </span>
          <input type="email" name="email" x-model="manualData.email" required
                 placeholder="contoh@mail.ugm.ac.id"
                 class="h-10 w-full pl-3 text-xs text-black placeholder:text-grey-2 focus:outline-none">
        </div>

        {{-- No. Telp --}}
        <div class="flex items-center rounded-xl border border-grey-1 bg-white px-3.5 focus-within:border-primary-1">
          <span class="flex items-center gap-2 text-xs font-semibold text-black w-28 shrink-0 border-r border-gray-100 pr-2">
            <x-solar-phone-linear class="h-4 w-4 text-primary-1" />
            <span>No. Telp</span>
          </span>
          <input type="text" name="phone" x-model="manualData.phone"
                 placeholder="+62 812 3456 7890"
                 class="h-10 w-full pl-3 text-xs text-black placeholder:text-grey-2 focus:outline-none">
        </div>

        {{-- Pekerjaan / Role --}}
        <div x-data="{ openRole: false }" @click.outside="openRole = false" class="relative">
          <div class="flex items-center rounded-xl border border-grey-1 bg-white px-3.5 focus-within:border-primary-1">
            <span class="flex items-center gap-2 text-xs font-semibold text-black w-28 shrink-0 border-r border-gray-100 pr-2">
              <x-solar-case-linear class="h-4 w-4 text-primary-1" />
              <span>Kategori</span>
            </span>
            <input type="hidden" name="role" :value="manualData.role">
            <button type="button" @click="openRole = !openRole"
                    class="flex h-10 w-full items-center justify-between pl-3 text-xs font-medium text-black cursor-pointer focus:outline-none">
              <span x-text="manualData.role === 'tendik' ? 'Tenaga Kependidikan' : 'Dosen'"></span>
              <x-solar-alt-arrow-down-linear class="h-3.5 w-3.5 text-grey-2 transition-transform duration-200"
                                             ::class="openRole ? 'rotate-180 text-primary-1' : ''" />
            </button>
          </div>
          <div x-show="openRole" x-cloak
               x-transition:enter="transition ease-out duration-100"
               x-transition:enter-start="opacity-0 -translate-y-1"
               x-transition:enter-end="opacity-100 translate-y-0"
               class="absolute left-0 right-0 top-full z-50 mt-1.5 rounded-xl border border-grey-1 bg-white p-1.5 shadow-xl">
            <button type="button" @click="manualData.role = 'dosen'; openRole = false"
                    :class="manualData.role === 'dosen' ? 'bg-primary-3/50 text-primary-1 font-semibold' : 'text-black hover:bg-gray-50'"
                    class="flex w-full items-center rounded-lg px-3 py-2 text-left text-xs transition-colors cursor-pointer">
              Dosen
            </button>
            <button type="button" @click="manualData.role = 'tendik'; openRole = false"
                    :class="manualData.role === 'tendik' ? 'bg-primary-3/50 text-primary-1 font-semibold' : 'text-black hover:bg-gray-50'"
                    class="flex w-full items-center rounded-lg px-3 py-2 text-left text-xs transition-colors cursor-pointer">
              Tenaga Kependidikan
            </button>
          </div>
        </div>

        {{-- Unit Kerja --}}
        <div x-data="{ openUnit: false }" @click.outside="openUnit = false" class="relative">
          <div class="flex items-center rounded-xl border border-grey-1 bg-white px-3.5 focus-within:border-primary-1">
            <span class="flex items-center gap-2 text-xs font-semibold text-black w-28 shrink-0 border-r border-gray-100 pr-2">
              <x-solar-buildings-linear class="h-4 w-4 text-primary-1" />
              <span>Unit kerja</span>
            </span>
            <input type="hidden" name="unit" :value="manualData.unit">
            <button type="button" @click="openUnit = !openUnit"
                    class="flex h-10 w-full items-center justify-between pl-3 text-xs font-medium text-black cursor-pointer focus:outline-none">
              <span class="truncate" x-text="manualData.unit || '{{ $units[0] ?? '' }}'"></span>
              <x-solar-alt-arrow-down-linear class="h-3.5 w-3.5 shrink-0 text-grey-2 transition-transform duration-200"
                                             ::class="openUnit ? 'rotate-180 text-primary-1' : ''" />
            </button>
          </div>
          <div x-show="openUnit" x-cloak
               x-transition:enter="transition ease-out duration-100"
               x-transition:enter-start="opacity-0 translate-y-1"
               x-transition:enter-end="opacity-100 translate-y-0"
               class="absolute left-0 right-0 bottom-full z-50 mb-1.5 max-h-48 overflow-y-auto rounded-xl border border-grey-1 bg-white p-1.5 shadow-xl">
            @foreach ($units as $u)
              <button type="button" @click="manualData.unit = '{{ $u }}'; openUnit = false"
                      :class="manualData.unit === '{{ $u }}' ? 'bg-primary-3/50 text-primary-1 font-semibold' : 'text-black hover:bg-gray-50'"
                      class="flex w-full items-center rounded-lg px-3 py-2 text-left text-xs transition-colors cursor-pointer">
                {{ $u }}
              </button>
            @endforeach
          </div>
        </div>
      </div>
    </form>
  </x-modal>

  {{-- 2. MODAL UPLOAD DROPZONE CSV / PDF --}}
  <x-modal show="modalMode === 'import_upload'" close="modalMode = null" maxWidth="820px">
    <div class="p-8">
      <div class="flex items-center justify-between border-b border-grey-1 pb-4">
        <div>
          <h2 class="text-xl font-bold text-primary-1">Impor Data Akun Massal</h2>
          <p class="mt-0.5 text-xs text-grey-deep">Unggah berkas CSV atau PDF data civitas untuk mengimpor akun sekaligus.</p>
        </div>
        <button type="button" @click="modalMode = null" aria-label="Tutup" class="shrink-0 rounded-lg p-1.5 text-grey-2 hover:text-danger hover:bg-red-50 transition-colors cursor-pointer"><x-solar-close-circle-linear class="h-5 w-5" /></button>
      </div>

      {{-- Dropzone Area dengan Border Putus-putus --}}
      <label class="mt-6 flex flex-col items-center justify-center rounded-2xl border-2 border-dashed border-primary-2 bg-white p-14 text-center cursor-pointer hover:bg-primary-3/20 transition-all">
        <div class="flex h-20 w-20 items-center justify-center rounded-2xl bg-primary-3/40 text-primary-1 mb-4">
          <x-solar-gallery-wide-linear class="h-10 w-10 text-primary-2" />
        </div>
        <p class="text-base font-semibold text-black">Drag and Drop to Upload File</p>
        <p class="text-xs text-grey-2 mt-1">Or</p>
        <span class="mt-4 inline-flex items-center justify-center rounded-xl bg-primary-1 px-8 py-2.5 text-xs font-semibold text-white shadow-sm hover:bg-primary-4 transition-all">
          Upload
        </span>
        <span class="mt-3 text-xs text-grey-2">Mendukung format file .CSV atau .PDF (Maksimal 10 MB)</span>
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
                  class="rounded-xl border border-grey-1 px-4 py-2 text-sm font-semibold text-grey-deep hover:bg-gray-50 cursor-pointer">
            Ganti File
          </button>
          <button type="button" @click="document.getElementById('csvImportForm').submit()"
                  class="rounded-xl bg-primary-1 px-6 py-2 text-sm font-semibold text-white shadow-sm hover:bg-primary-4 transition-all cursor-pointer">
            Simpan
          </button>
        </div>
      </div>

      {{-- Notifikasi Info Status Validasi --}}
      <div class="mt-4 flex flex-wrap items-center gap-3">
        <span class="inline-flex items-center gap-1.5 rounded-full bg-success-soft px-3 py-1 text-xs font-semibold text-success">
          <x-solar-check-circle-bold class="h-4 w-4" />
          <span x-text="validCount + ' Data berhasil teridentifikasi'"></span>
        </span>
        <span x-show="invalidCount > 0" class="inline-flex items-center gap-1.5 rounded-full bg-danger-soft px-3 py-1 text-xs font-semibold text-danger">
          <x-solar-danger-circle-bold class="h-4 w-4" />
          <span x-text="invalidCount + ' Data duplikat / terdapat kesalahan format'"></span>
        </span>
      </div>

      {{-- Tabel Preview (Header Biru Gelap) --}}
      <div class="mt-4 overflow-y-auto overflow-x-auto flex-1 border border-grey-1 rounded-xl max-h-[360px]">
        <table class="w-full min-w-[750px] border-separate border-spacing-0 text-xs">
          <thead class="sticky top-0 z-10">
            <tr class="bg-secondary-1 text-white text-xs font-bold tracking-wider text-left">
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
                  <span class="inline-flex h-6 w-6 items-center justify-center rounded-xl border border-grey-1 bg-white text-xs font-semibold text-black"
                        x-text="row.no"></span>
                </td>
                {{-- Nama --}}
                <td class="py-3 px-4 font-semibold text-black text-left" x-text="row.name"></td>
                {{-- Email --}}
                <td class="py-3 px-4 text-black text-xs text-left uppercase" x-text="row.email"></td>
                {{-- Unit --}}
                <td class="py-3 px-3 text-left font-medium text-black" x-text="row.unit"></td>
                {{-- No. Telp --}}
                <td class="py-3 px-3 text-left font-medium text-black" x-text="row.phone"></td>
                {{-- Status Validasi Row --}}
                <td class="py-3 px-3 text-left">
                  <template x-if="row.valid">
                    <span class="inline-flex items-center rounded-full bg-success-soft px-2 py-0.5 text-xs font-bold text-success">
                      Valid
                    </span>
                  </template>
                  <template x-if="!row.valid">
                    <span class="inline-flex items-center rounded-full bg-danger-soft px-2 py-0.5 text-xs font-bold text-danger" title="Email sudah terdaftar">
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
          <div class="flex h-9 w-9 items-center justify-center rounded-xl bg-primary-3 text-primary-1">
            <x-solar-pen-new-square-linear class="h-5 w-5" />
          </div>
          <div>
            <h3 class="text-base font-bold text-black">Ubah Data Sivitas</h3>
            <p class="text-xs text-grey-2" x-text="'ID: ' + editData.id"></p>
          </div>
        </div>
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
          <div x-data="{ openEditUnit: false }" @click.outside="openEditUnit = false" class="relative">
            <label class="block text-xs font-semibold text-black mb-1">Unit Kerja</label>
            <input type="hidden" name="unit" :value="editData.unit">
            <button type="button" @click="openEditUnit = !openEditUnit"
                    class="{{ $field }} flex items-center justify-between text-left cursor-pointer">
              <span class="truncate" x-text="editData.unit || 'Pilih Unit Kerja'"></span>
              <x-solar-alt-arrow-down-linear class="h-3.5 w-3.5 shrink-0 text-grey-2 transition-transform duration-200"
                                             ::class="openEditUnit ? 'rotate-180 text-primary-1' : ''" />
            </button>
            <div x-show="openEditUnit" x-cloak
                 class="absolute left-0 right-0 top-full z-50 mt-1 max-h-44 overflow-y-auto rounded-xl border border-grey-1 bg-white p-1.5 shadow-xl">
              @foreach ($units as $u)
                <button type="button" @click="editData.unit = '{{ $u }}'; openEditUnit = false"
                        :class="editData.unit === '{{ $u }}' ? 'bg-primary-3/50 text-primary-1 font-semibold' : 'text-black hover:bg-gray-50'"
                        class="flex w-full items-center rounded-lg px-3 py-2 text-left text-xs transition-colors cursor-pointer">
                  {{ $u }}
                </button>
              @endforeach
            </div>
          </div>
          <div>
            <label class="block text-xs font-semibold text-black mb-1">No. WhatsApp</label>
            <input type="text" name="phone" x-model="editData.phone" class="{{ $field }}" required>
          </div>
        </div>

        {{-- Status Akun --}}
        <div x-data="{ openEditStatus: false }" @click.outside="openEditStatus = false" class="relative">
          <label class="block text-xs font-semibold text-black mb-1">Status Partisipasi</label>
          <input type="hidden" name="status" :value="editData.status">
          <button type="button" @click="openEditStatus = !openEditStatus"
                  class="{{ $field }} flex items-center justify-between text-left cursor-pointer">
            <span x-text="editData.status === 'Nonaktif' ? 'Nonaktif (Ditangguhkan)' : 'Aktif (Dapat Memilih)'"></span>
            <x-solar-alt-arrow-down-linear class="h-3.5 w-3.5 shrink-0 text-grey-2 transition-transform duration-200"
                                           ::class="openEditStatus ? 'rotate-180 text-primary-1' : ''" />
          </button>
          <div x-show="openEditStatus" x-cloak
               class="absolute left-0 right-0 bottom-full z-50 mb-1 rounded-xl border border-grey-1 bg-white p-1.5 shadow-xl">
            <button type="button" @click="editData.status = 'Aktif'; openEditStatus = false"
                    :class="editData.status === 'Aktif' ? 'bg-primary-3/50 text-primary-1 font-semibold' : 'text-black hover:bg-gray-50'"
                    class="flex w-full items-center rounded-lg px-3 py-2 text-left text-xs transition-colors cursor-pointer">
              Aktif (Dapat Memilih)
            </button>
            <button type="button" @click="editData.status = 'Nonaktif'; openEditStatus = false"
                    :class="editData.status === 'Nonaktif' ? 'bg-primary-3/50 text-primary-1 font-semibold' : 'text-black hover:bg-gray-50'"
                    class="flex w-full items-center rounded-lg px-3 py-2 text-left text-xs transition-colors cursor-pointer">
              Nonaktif (Ditangguhkan)
            </button>
          </div>
        </div>

        {{-- Tombol Batal & Simpan --}}
        <div class="mt-5 flex items-center justify-end gap-2 pt-3 border-t border-grey-1">
          <button type="button" @click="modalMode = null"
                  class="rounded-xl border border-grey-1 bg-white px-4 py-2 text-xs font-semibold text-grey-deep hover:bg-gray-50 transition-all cursor-pointer">
            Batal
          </button>
          <button type="submit"
                  class="rounded-xl bg-primary-1 px-5 py-2 text-xs font-semibold text-white shadow-primary-glow hover:bg-primary-4 transition-all cursor-pointer">
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

      <form :action="'{{ url('admin/akun') }}/' + deleteData.id" method="POST" class="mt-5 flex items-center justify-center gap-2 pt-2">
        @csrf
        @method('DELETE')
        <button type="button" @click="modalMode = null"
                class="rounded-xl border border-grey-1 bg-white px-4 py-2 text-xs font-semibold text-grey-deep hover:bg-gray-50 transition-all cursor-pointer">
          Batal
        </button>
        <button type="submit"
                class="rounded-xl bg-danger px-5 py-2 text-xs font-semibold text-white shadow-xs hover:bg-rose-700 transition-all cursor-pointer">
          Ya, Hapus
        </button>
      </form>
    </div>
  </x-modal>

  {{-- Hidden Form untuk Submit CSV Import --}}
  <form id="csvImportForm" method="POST" action="{{ route('admin.akun.import-csv') }}" enctype="multipart/form-data" class="hidden">
    @csrf
    <input type="file" name="csv_file" id="csvFileInput" accept=".csv,.txt">
  </form>

</div>
@endsection
