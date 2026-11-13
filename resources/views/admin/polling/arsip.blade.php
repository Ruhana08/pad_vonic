@extends('layouts.admin')

@section('title', 'Status Polling & Riwayat')

@section('content')
@php
  // Cek apakah ada polling yang saat ini aktif
  $hasActivePolling = $polls->contains(fn($p) => $p['status'] === 'Aktif');
@endphp

<div class="space-y-6"
     x-data="{
       modalMode: null, // null | 'blocked_active' | 'late_warning' | 'delete_draft' | 'delete_active' | 'delete_completed' | 'view_result'
       targetPoll: null,
       activeHistoryItem: null,
       previewImage: null,

       openPostModal(poll) {
         this.targetPoll = poll;
         // Kondisi 1: Hanya 1 voting aktif at a time
         @if ($hasActivePolling)
           this.modalMode = 'blocked_active';
           return;
         @endif

         // Simulasi cek waktu setting polling vs waktu sekarang
         // Jika melewati jam setting (asumsi late)
         // Di sini kita trigger modal late_warning untuk verifikasi
         this.modalMode = 'late_warning';
       },

       confirmPostNow() {
         alert('Polling ' + this.targetPoll.name + ' berhasil dipost/dipublikasikan!');
         this.modalMode = null;
       },

       openDeleteModal(poll) {
         this.targetPoll = poll;
         if (poll.status === 'Aktif') {
           this.modalMode = 'delete_active';
         } else if (poll.status === 'Draft') {
           this.modalMode = 'delete_draft';
         } else {
           this.modalMode = 'delete_completed';
         }
       },

       confirmDelete() {
         if (this.modalMode === 'delete_active') {
           alert('Voting ' + this.targetPoll.name + ' telah dihentikan dan statusnya kini berubah menjadi Selesai.');
         } else {
           alert('Polling ' + this.targetPoll.name + ' berhasil dihapus.');
         }
         this.modalMode = null;
       },

       openResultModal(item) {
         this.activeHistoryItem = item;
         this.modalMode = 'view_result';
       }
     }">

  {{-- ===== SECTION 1: STATUS POLLING ===== --}}
  <div class="space-y-3">
    {{-- Baris Atas: Judul Status Polling & Tombol Tambah Polling --}}
    <div class="flex items-center justify-between">
      <h1 class="text-2xl font-bold tracking-tight text-black">Status Polling</h1>

      <x-button variant="gradient" :href="route('admin.polling.create')" data-turbo="false" class="px-5 py-2.5 text-xs font-semibold shadow-primary-glow">
        <x-solar-add-circle-linear class="h-4 w-4" />
        <span>Tambah Polling</span>
      </x-button>
    </div>

    {{-- Baris Filter/Pagination Atas (di luar card): Tampilkan [10] & Sebelumnya 1/6 Selanjutnya --}}
    <div class="flex flex-wrap items-center justify-between gap-3 pt-1">
      <form method="GET" class="flex items-center gap-2">
        @foreach (request()->except(['per_page', 'page']) as $key => $value)
          @if (is_string($value))
            <input type="hidden" name="{{ $key }}" value="{{ $value }}">
          @endif
        @endforeach

        <span class="flex h-9 items-center justify-center rounded-sm bg-white px-5 text-xs font-medium text-primary-1 shadow-menu">
          Tampilkan
        </span>
        <x-dropdown-select name="per_page"
                           :value="$polls->perPage()"
                           :options="[10 => '10', 25 => '25', 50 => '50']"
                           :autoSubmit="true"
                           variant="primary" />
      </form>

      <div class="flex items-center gap-2">
        <a href="{{ $polls->previousPageUrl() ?? '#' }}"
           class="flex h-9 items-center rounded-sm bg-white px-5 text-xs font-medium text-primary-1 shadow-menu hover:bg-primary-3/30 transition-all {{ $polls->onFirstPage() ? 'pointer-events-none opacity-40' : '' }}">
          Sebelumnya
        </a>

        <span class="flex h-9 items-center rounded-sm border border-primary-1 bg-white px-3.5 text-xs font-semibold text-primary-1 shadow-xs">
          {{ $polls->currentPage() }}/{{ $polls->lastPage() }}
        </span>

        <a href="{{ $polls->nextPageUrl() ?? '#' }}"
           class="flex h-9 items-center rounded-sm bg-white px-5 text-xs font-medium text-primary-1 shadow-menu hover:bg-primary-3/30 transition-all {{ $polls->hasMorePages() ? '' : 'pointer-events-none opacity-40' }}">
          Selanjutnya
        </a>
      </div>
    </div>

    {{-- Card Tabel Status Polling --}}
    <x-card class="p-6 border border-primary-1 shadow-sm">
      <div class="overflow-x-auto">
        <table class="w-full min-w-[900px] border-separate border-spacing-y-2 text-xs">
          <thead>
            <tr class="bg-secondary-1 text-white text-[11px] font-bold tracking-wider">
              <th class="rounded-l-sm py-3 px-3 text-center w-14">NO.</th>
              <th class="py-3 px-3 text-center w-28">ID</th>
              <th class="py-3 px-4 text-left">POLLING</th>
              <th class="py-3 px-4 text-center w-40">WAKTU MULAI</th>
              <th class="py-3 px-4 text-center w-40">WAKTU SELESAI</th>
              <th class="py-3 px-3 text-center w-28">STATUS</th>
              <th class="rounded-r-sm py-3 px-3 text-left w-36">AKSI</th>
            </tr>
          </thead>
          <tbody>
            @forelse ($polls as $p)
              <tr class="transition-colors hover:bg-primary-3/20">
                {{-- No. --}}
                <td class="border-b border-gray-100 py-3.5 px-3 text-center">
                  <span class="inline-flex h-7 w-7 items-center justify-center rounded-sm border border-grey-1 bg-white text-xs font-semibold text-black shadow-xs">
                    {{ str_pad($polls->firstItem() + $loop->index, 2, '0', STR_PAD_LEFT) }}
                  </span>
                </td>

                {{-- ID --}}
                <td class="border-b border-gray-100 py-3.5 px-3 text-center font-medium text-black">
                  {{ $p['id'] }}
                </td>

                {{-- Polling Name (Bisa diklik untuk melihat analitik jika Aktif atau Selesai) --}}
                <td class="border-b border-gray-100 py-3.5 px-4 font-semibold text-black">
                  @if ($p['status'] === 'Aktif' || $p['status'] === 'Selesai')
                    <a href="{{ route('admin.polling.aktif') }}" class="hover:text-primary-1 hover:underline transition-colors" title="Lihat Statistik & Analitik Lengkap">
                      {{ $p['name'] }}
                    </a>
                  @else
                    <span>{{ $p['name'] }}</span>
                  @endif
                </td>

                {{-- Waktu Mulai --}}
                <td class="border-b border-gray-100 py-3.5 px-4 text-center">
                  <span class="inline-flex h-7 items-center rounded-sm border border-primary-1 bg-white px-2.5 text-[11px] font-medium text-black shadow-xs">
                    {{ $p['start'] }}
                  </span>
                </td>

                {{-- Waktu Selesai --}}
                <td class="border-b border-gray-100 py-3.5 px-4 text-center">
                  <span class="inline-flex h-7 items-center rounded-sm border border-primary-1 bg-white px-2.5 text-[11px] font-medium text-black shadow-xs">
                    {{ $p['end'] }}
                  </span>
                </td>

                {{-- Status Pill --}}
                <td class="border-b border-gray-100 py-3.5 px-3 text-center">
                  @php
                    $statusStyle = match($p['status']) {
                      'Aktif'   => 'bg-emerald-100 text-emerald-700',
                      'Draft'   => 'bg-amber-100 text-amber-700',
                      default   => 'bg-gray-100 text-grey-deep',
                    };
                  @endphp
                  <span class="inline-flex h-7 items-center justify-center rounded-full px-4 text-[11px] font-semibold {{ $statusStyle }}">
                    {{ $p['status'] }}
                  </span>
                </td>

                {{-- Aksi: 4 slot tombol konsisten (Lihat Hasil Analitik, Edit, Post/Upload, Hapus) --}}
                <td class="border-b border-gray-100 py-3.5 px-3 text-left">
                  <div class="flex items-center justify-start gap-2">
                    @if ($p['status'] === 'Draft')
                      {{-- Draft: Lihat disabled, Edit aktif, Post aktif, Hapus aktif --}}
                      <x-action-button type="view" :disabled="true" label="Hasil analitik belum tersedia untuk draft" />
                      <x-action-button type="edit" :href="route('admin.polling.create', ['id' => $p['id']])" data-turbo="false" label="Edit Polling" />
                      <x-action-button type="upload" label="Publikasikan Polling"
                                       @click="openPostModal({{ json_encode($p) }})" />
                      <x-action-button type="delete" label="Hapus Polling"
                                       @click="openDeleteModal({{ json_encode($p) }})" />
                    @elseif ($p['status'] === 'Aktif')
                      {{-- Aktif: Lihat aktif (buka analitik full), Edit disabled, Post disabled, Hapus aktif (menghentikan voting) --}}
                      <x-action-button type="view" :href="route('admin.polling.aktif')" label="Lihat Rincian & Analitik Voting" />
                      <x-action-button type="edit" :disabled="true" label="Tidak dapat mengedit polling yang sedang aktif" />
                      <x-action-button type="upload" :disabled="true" label="Polling sudah aktif" />
                      <x-action-button type="delete" label="Hentikan / Akhiri Polling"
                                       @click="openDeleteModal({{ json_encode($p) }})" />
                    @else
                      {{-- Selesai: Lihat aktif (buka analitik arsip), Edit disabled, Post disabled, Hapus aktif --}}
                      <x-action-button type="view" :href="route('admin.polling.aktif')" label="Lihat Rincian & Analitik Voting" />
                      <x-action-button type="edit" :disabled="true" label="Polling yang telah selesai tidak dapat diedit" />
                      <x-action-button type="upload" :disabled="true" label="Polling sudah selesai" />
                      <x-action-button type="delete" label="Hapus Polling"
                                       @click="openDeleteModal({{ json_encode($p) }})" />
                    @endif
                  </div>
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="7" class="py-12 bg-white">
                  <x-empty-state 
                    icon="solar-clipboard-list-linear"
                    title="Belum Ada Polling yang Dibuat"
                    message="Saat ini belum ada sesi pemilihan atau kegiatan polling aktif. Mulai buat polling pertama untuk civitas DTEDI."
                    actionText="+ Buat Polling Baru"
                    :actionUrl="route('admin.polling.create')" />
                </td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </x-card>
  </div>

  {{-- ===== SECTION 2: RIWAYAT PEMILIHAN & AKSES ===== --}}
  <script>
    document.addEventListener('alpine:init', () => {
      Alpine.data('riwayat', () => ({
        items: [],
        pub: [],
        snapshot: [],
        editMode: false,
        page: 1,
        perPage: 10,

        init() {
          this.items = JSON.parse(this.$root.dataset.items || '[]');
          this.pub = JSON.parse(this.$root.dataset.pub || '[]');
        },

        get totalPages() {
          return Math.max(1, Math.ceil(this.items.length / this.perPage));
        },
        get pageItems() {
          return this.items.slice((this.page - 1) * this.perPage, this.page * this.perPage);
        },
        get publik() {
          return this.pub.length;
        },
        get privat() {
          return this.items.length - this.pub.length;
        },
        get allOnPageChecked() {
          return this.pageItems.length > 0 && this.pageItems.every(h => this.pub.includes(h.id));
        },
        isPub(id) {
          return this.pub.includes(id);
        },
        toggle(id) {
          if (!this.editMode) return;
          this.pub = this.isPub(id) ? this.pub.filter(i => i !== id) : [...this.pub, id];
        },
        toggleAllOnPage() {
          const ids = this.pageItems.map(h => h.id);
          this.pub = this.allOnPageChecked
            ? this.pub.filter(i => !ids.includes(i))
            : [...new Set([...this.pub, ...ids])];
        },
        changePerPage() {
          this.page = 1;
        },
        startEdit() {
          this.snapshot = [...this.pub];
          this.editMode = true;
        },
        cancel() {
          this.pub = this.snapshot;
          this.editMode = false;
        },
        save() {
          this.editMode = false;
        }
      }));
    });
  </script>

  <div class="grid grid-cols-1 items-start gap-4 xl:grid-cols-[1fr_240px]" x-data="riwayat"
       data-items="{{ json_encode($history) }}" data-pub="{{ json_encode($publicIds) }}">

    {{-- Card Tabel Riwayat (Gaya identik dengan Status Polling) --}}
    <x-card class="p-6 border border-primary-1 shadow-sm">
      <h2 class="text-xl font-bold text-black">Daftar Riwayat Pemilihan Destinasi Wisata Piknik DTEDI</h2>

      {{-- Kontrol Pagination Riwayat --}}
      <div class="mt-4 flex flex-wrap items-center justify-between gap-3">
        <div class="flex items-center gap-2">
          <span class="flex h-9 items-center justify-center rounded-sm bg-white px-5 text-xs font-medium text-primary-1 shadow-menu">
            Tampilkan
          </span>
          <x-dropdown-select x-model.number="perPage"
                             x-on:change="changePerPage()"
                             :value="10"
                             :options="[10 => '10', 25 => '25', 50 => '50']"
                             variant="primary" />
        </div>

        <div class="flex items-center gap-2">
          <button type="button" class="flex h-9 items-center rounded-sm bg-white px-5 text-xs font-medium text-primary-1 shadow-menu hover:bg-primary-3/30 disabled:opacity-40 disabled:pointer-events-none transition-all"
                  x-bind:disabled="page === 1" x-on:click="page--">Sebelumnya</button>
          <span class="flex h-9 items-center rounded-sm border border-primary-1 bg-white px-3.5 text-xs font-semibold text-primary-1 shadow-xs"
                x-text="page + '/' + totalPages"></span>
          <button type="button" class="flex h-9 items-center rounded-sm bg-white px-5 text-xs font-medium text-primary-1 shadow-menu hover:bg-primary-3/30 disabled:opacity-40 disabled:pointer-events-none transition-all"
                  x-bind:disabled="page === totalPages" x-on:click="page++">Selanjutnya</button>
        </div>
      </div>

      {{-- Tabel Riwayat: Kolom PERIODE dihapus, Aksi diubah menjadi Hasil Polling --}}
      <div class="mt-4 overflow-x-auto">
        <table class="w-full min-w-[820px] border-separate border-spacing-y-2 text-xs">
          <thead>
            <tr class="bg-secondary-1 text-white text-[11px] font-bold tracking-wider">
              <th x-show="editMode" x-cloak class="rounded-l-sm py-3 px-3 text-center w-12">
                <input type="checkbox" class="size-4 cursor-pointer accent-white"
                       x-bind:checked="allOnPageChecked" x-on:change="toggleAllOnPage()" aria-label="Pilih semua di halaman ini">
              </th>
              <th class="py-3 px-3 text-center w-14" x-bind:class="editMode ? '' : 'rounded-l-sm'">NO.</th>
              <th class="py-3 px-4 text-left">JUDUL</th>
              <th class="py-3 px-4 text-left">DESKRIPSI</th>
              <th class="py-3 px-4 text-center w-40">WAKTU MULAI</th>
              <th class="py-3 px-4 text-center w-40">WAKTU SELESAI</th>
              <th class="py-3 px-3 text-center w-28">AKSES</th>
              <th class="rounded-r-sm py-3 px-3 text-center w-32">AKSI</th>
            </tr>
          </thead>
          <tbody>
            <template x-for="(h, i) in pageItems" x-bind:key="h.id">
              <tr class="transition-colors hover:bg-primary-3/20"
                  x-bind:class="[editMode ? 'cursor-pointer hover:bg-primary-3/50' : '', editMode && isPub(h.id) ? 'bg-primary-3' : '']"
                  x-on:click="toggle(h.id)">
                <td x-show="editMode" x-cloak class="border-b border-gray-100 py-3.5 px-3 text-center">
                  <input type="checkbox" class="size-4 cursor-pointer accent-[#196BEF]"
                         x-bind:checked="isPub(h.id)" x-on:click.stop="toggle(h.id)" aria-label="Jadikan publik">
                </td>
                <td class="border-b border-gray-100 py-3.5 px-3 text-center">
                  <span class="inline-flex h-7 w-7 items-center justify-center rounded-sm border border-grey-1 bg-white text-xs font-semibold text-black shadow-xs"
                        x-text="String((page - 1) * perPage + i + 1).padStart(2, '0')"></span>
                </td>
                <td class="border-b border-gray-100 py-3.5 px-4 font-semibold text-black">
                  <a :href="'{{ route('admin.polling.aktif') }}'" class="hover:text-primary-1 hover:underline transition-colors" title="Buka Detail Analitik" x-text="h.title"></a>
                </td>
                <td class="border-b border-gray-100 py-3.5 px-4 max-w-[220px] truncate text-grey-deep"
                    x-bind:title="h.description" x-text="h.description"></td>
                {{-- Waktu Mulai --}}
                <td class="border-b border-gray-100 py-3.5 px-4 text-center">
                  <span class="inline-flex h-7 items-center rounded-sm border border-primary-1 bg-white px-2.5 text-[11px] font-medium text-black shadow-xs" x-text="h.start"></span>
                </td>
                {{-- Waktu Selesai --}}
                <td class="border-b border-gray-100 py-3.5 px-4 text-center">
                  <span class="inline-flex h-7 items-center rounded-sm border border-primary-1 bg-white px-2.5 text-[11px] font-medium text-black shadow-xs" x-text="h.end"></span>
                </td>
                {{-- Akses Pill --}}
                <td class="border-b border-gray-100 py-3.5 px-3 text-center">
                  <span class="inline-flex h-7 w-20 items-center justify-center rounded-full text-[11px] font-semibold"
                        x-bind:class="isPub(h.id) ? 'bg-success-soft text-success' : 'bg-primary-3 text-primary-1'"
                        x-text="isPub(h.id) ? 'Publik' : 'Private'"></span>
                </td>
                {{-- Tombol Aksi: Hasil Polling (Membuka Quick-View Pemenang) --}}
                <td class="border-b border-gray-100 py-3.5 px-3 text-center">
                  <button type="button"
                          @click.stop="openResultModal(h)"
                          class="inline-flex items-center justify-center rounded-sm bg-primary-1 px-3 py-1.5 text-xs font-semibold text-white shadow-menu hover:bg-primary-4 transition-all cursor-pointer">
                    Hasil Polling
                  </button>
                </td>
              </tr>
            </template>
          </tbody>
        </table>
      </div>
    </x-card>

    {{-- Panel Samping: Akses (Publik / Private) --}}
    <x-card class="p-5 border border-primary-1 shadow-sm xl:sticky xl:top-[112px]">
      <div class="flex items-center justify-between pb-3 border-b border-gray-100">
        <span class="text-sm font-bold text-primary-1">Akses</span>
        <button type="button" x-show="!editMode" x-on:click="startEdit()" aria-label="Ubah akses"
                class="flex h-7 w-7 items-center justify-center rounded-sm border border-grey-1 text-primary-1 shadow-xs hover:border-primary-1 hover:bg-primary-3/30 transition-all">
          <x-solar-pen-new-square-linear class="h-4 w-4" />
        </button>
      </div>

      <div class="mt-4 space-y-2.5">
        <div class="flex items-center gap-2">
          <span class="flex-1 rounded-sm bg-primary-1 px-3 py-2 text-xs font-medium text-white shadow-xs">Publik</span>
          <span class="min-w-14 rounded-sm border border-grey-1 bg-white px-2 py-2 text-center text-xs font-bold text-black shadow-xs leading-none" x-text="publik">0</span>
        </div>
        <div class="flex items-center gap-2">
          <span class="flex-1 rounded-sm bg-primary-1 px-3 py-2 text-xs font-medium text-white shadow-xs">Private</span>
          <span class="min-w-14 rounded-sm border border-grey-1 bg-white px-2 py-2 text-center text-xs font-bold text-black shadow-xs leading-none" x-text="privat">0</span>
        </div>
      </div>

      <div x-show="editMode" x-cloak class="mt-5 space-y-2 border-t border-gray-100 pt-4">
        <p class="text-[11px] leading-snug text-grey-deep">Centang baris yang ingin dijadikan publik.</p>
        <button type="button" x-on:click="save()"
                class="w-full rounded-sm bg-primary-1 py-2 text-xs font-semibold text-white shadow-menu hover:bg-primary-4 transition-all">
          Simpan
        </button>
        <button type="button" x-on:click="cancel()"
                class="w-full rounded-sm border border-grey-1 bg-white py-2 text-xs font-semibold text-grey-deep shadow-xs hover:bg-gray-50 transition-all">
          Batal
        </button>
      </div>
    </x-card>
  </div>

  {{-- ===== POPUP MODAL DIALOGS ===== --}}

  {{-- 1. Peringatan: Sudah Ada Voting Aktif --}}
  <x-modal show="modalMode === 'blocked_active'" close="modalMode = null" maxWidth="sm">
    <div class="p-6 text-center">
      <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-full bg-amber-100 text-amber-600 mb-4">
        <x-solar-danger-circle-linear class="h-6 w-6" />
      </div>
      <h3 class="text-base font-bold text-black">Tidak Dapat Mempublikasikan</h3>
      <p class="mt-2 text-xs text-grey-deep leading-relaxed">
        Saat ini sudah ada voting yang sedang berjalan (<span class="font-semibold text-primary-1">Piknik DTEDI Periode 2026</span>). Sistem hanya mengizinkan <strong>1 polling aktif</strong> dalam satu waktu.
      </p>
      <div class="mt-6">
        <button type="button"
                @click="modalMode = null"
                class="w-full rounded-sm bg-primary-1 py-2 text-xs font-semibold text-white shadow-menu hover:bg-primary-4 transition-all">
          Mengerti
        </button>
      </div>
    </div>
  </x-modal>

  {{-- 2. Konfirmasi: Jam Setting Sudah Terlewat --}}
  <x-modal show="modalMode === 'late_warning'" close="modalMode = null" maxWidth="sm">
    <div class="p-6 text-center">
      <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-full bg-blue-100 text-primary-1 mb-4">
        <x-solar-clock-circle-linear class="h-6 w-6" />
      </div>
      <h3 class="text-base font-bold text-black">Waktu Mulai Telah Terlewat</h3>
      <p class="mt-2 text-xs text-grey-deep leading-relaxed">
        Waktu mulai polling ini sudah terlewat dari jadwal yang ditentukan. Durasi voting akan berjalan hingga waktu penutupan yang telah di-setting sebelumnya.
      </p>
      <div class="mt-6 flex items-center gap-3">
        <a :href="'{{ route('admin.polling.create') }}?id=' + (targetPoll ? targetPoll.id : '')"
           class="flex-1 rounded-sm border border-grey-1 bg-white py-2 text-xs font-semibold text-grey-deep shadow-xs hover:bg-gray-50 transition-all text-center">
          Edit Waktu
        </a>
        <button type="button"
                @click="confirmPostNow()"
                class="flex-1 rounded-sm bg-primary-1 py-2 text-xs font-semibold text-white shadow-menu hover:bg-primary-4 transition-all">
          Tetap Post
        </button>
      </div>
    </div>
  </x-modal>

  {{-- 3. Konfirmasi Hapus: Polling Berstatus Aktif (Mengubah status menjadi Selesai) --}}
  <x-modal show="modalMode === 'delete_active'" close="modalMode = null" maxWidth="sm">
    <div class="p-6 text-center">
      <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-full bg-rose-100 text-danger mb-4">
        <x-solar-danger-circle-linear class="h-6 w-6" />
      </div>
      <h3 class="text-base font-bold text-black">Hentikan Polling yang Sedang Aktif?</h3>
      <p class="mt-2 text-xs text-grey-deep leading-relaxed">
        Polling yang sedang aktif tidak dapat diedit agar integritas data pemilih tetap terjaga. Jika Anda menghapus polling ini sekarang, voting akan dihentikan dan <strong>statusnya akan langsung berubah menjadi Selesai</strong>.
      </p>
      <div class="mt-6 flex items-center gap-3">
        <button type="button"
                @click="modalMode = null"
                class="flex-1 rounded-sm border border-grey-1 bg-white py-2 text-xs font-semibold text-grey-deep shadow-xs hover:bg-gray-50 transition-all">
          Batal
        </button>
        <button type="button"
                @click="confirmDelete()"
                class="flex-1 rounded-sm bg-danger py-2 text-xs font-semibold text-white shadow-menu hover:bg-red-700 transition-all">
          Ya, Hentikan
        </button>
      </div>
    </div>
  </x-modal>

  {{-- 4. Konfirmasi Hapus: Polling Draft atau Selesai (Hapus Permanen) --}}
  <x-modal show="modalMode === 'delete_draft' || modalMode === 'delete_completed'" close="modalMode = null" maxWidth="sm">
    <div class="p-6 text-center">
      <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-full bg-rose-100 text-danger mb-4">
        <x-solar-trash-bin-trash-linear class="h-6 w-6" />
      </div>
      <h3 class="text-base font-bold text-black" x-text="modalMode === 'delete_draft' ? 'Hapus Draft Polling?' : 'Hapus Arsip Polling?'"></h3>
      <p class="mt-2 text-xs text-grey-deep leading-relaxed">
        Apakah Anda yakin ingin menghapus data polling ini? Tindakan ini akan menghapus data secara permanen.
      </p>
      <div class="mt-6 flex items-center gap-3">
        <button type="button"
                @click="modalMode = null"
                class="flex-1 rounded-sm border border-grey-1 bg-white py-2 text-xs font-semibold text-grey-deep shadow-xs hover:bg-gray-50 transition-all">
          Batal
        </button>
        <button type="button"
                @click="confirmDelete()"
                class="flex-1 rounded-sm bg-danger py-2 text-xs font-semibold text-white shadow-menu hover:bg-red-700 transition-all">
          Ya, Hapus
        </button>
      </div>
    </div>
  </x-modal>

  {{-- 5. Quick-View Pop Up: Hasil Pemenang Polling Sesuai Desain Figma --}}
  <x-modal show="modalMode === 'view_result'" close="modalMode = null" maxWidth="820px">
    <div class="p-7 max-h-[90vh] overflow-y-auto" x-show="activeHistoryItem">
      {{-- Header Modal --}}
      <div class="flex items-start justify-between pb-4">
        <div>
          <span class="text-xs font-semibold text-primary-1">Hasil vote</span>
          <h2 class="mt-1 text-2xl font-bold text-black" x-text="activeHistoryItem?.title"></h2>
        </div>
        <button type="button" @click="modalMode = null"
                class="flex h-8 w-8 items-center justify-center rounded-full text-grey-2 hover:bg-gray-100 hover:text-black transition-colors cursor-pointer">
          <x-solar-close-circle-linear class="h-6 w-6" />
        </button>
      </div>

      {{-- Overview Event Box (Durasi, Partisipan, Peserta) --}}
      <div class="rounded-xl bg-primary-3/50 p-4 border border-primary-1/20">
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 items-center">
          {{-- Durasi Pelaksanaan --}}
          <div class="space-y-1">
            <div class="flex items-center gap-1.5 text-xs font-semibold text-primary-1">
              <x-solar-calendar-linear class="h-4 w-4" />
              <span>Durasi Polling</span>
            </div>
            <p class="text-xs font-bold text-black" x-text="activeHistoryItem?.start + ' - ' + activeHistoryItem?.end"></p>
          </div>

          {{-- Partisipan --}}
          <div class="space-y-1 border-t sm:border-t-0 sm:border-l border-primary-1/20 sm:pl-5">
            <div class="flex items-center gap-1.5 text-xs font-semibold text-primary-1">
              <x-solar-user-linear class="h-4 w-4" />
              <span>Partisipan</span>
            </div>
            <p class="text-sm font-bold text-primary-1" x-text="activeHistoryItem?.participants + ' Orang'"></p>
          </div>

          {{-- Peserta --}}
          <div class="space-y-1 border-t sm:border-t-0 sm:border-l border-primary-1/20 sm:pl-5">
            <div class="flex items-center gap-1.5 text-xs font-semibold text-primary-1">
              <span>Peserta :</span>
            </div>
            <p class="text-xs font-medium text-black truncate" x-text="activeHistoryItem?.peserta_type || 'Dosen dan Tenaga Didik'"></p>
          </div>
        </div>
      </div>

      {{-- Cards Grid Hasil Pemenang --}}
      <div class="mt-6 grid grid-cols-1 md:grid-cols-12 gap-5 items-stretch">
        
        {{-- Kolom Kiri: Desain Kaos Wisata (5/12) --}}
        <div class="md:col-span-5 flex flex-col space-y-2">
          <div class="flex items-center gap-2 text-sm font-bold text-primary-1">
            <x-solar-t-shirt-bold class="h-4 w-4 text-primary-1" />
            <span>Desain Kaos Wisata :</span>
          </div>

          <div class="flex-1 rounded-2xl border border-grey-1 bg-white p-4 shadow-sm flex flex-col justify-between">
            <div class="flex flex-col flex-1 justify-between">
              <div>
                <h4 class="text-base font-bold text-black" x-text="activeHistoryItem?.kaos?.title || 'Kaos A'"></h4>
                
                {{-- Gambar Utama Kaos (Clickable untuk Zoom / Fullscreen Preview) --}}
                <div class="mt-3 aspect-[4/3] w-full overflow-hidden rounded-xl bg-gray-100 cursor-pointer group relative"
                     @click="previewImage = activeHistoryItem?.kaos?.image">
                  <img :src="activeHistoryItem?.kaos?.image" alt="Kaos Pemenang" class="h-full w-full object-cover group-hover:scale-105 transition-transform duration-300">
                  <div class="absolute inset-0 bg-black/0 group-hover:bg-black/20 transition-colors flex items-center justify-center opacity-0 group-hover:opacity-100">
                    <span class="rounded-full bg-white/90 p-2 text-black shadow-md">
                      <x-solar-magnifer-zoom-in-linear class="h-5 w-5" />
                    </span>
                  </div>
                </div>

                {{-- Galeri Foto Tambahan (Clickable) --}}
                <div class="mt-3 grid grid-cols-2 gap-2">
                  <template x-for="(thumb, tIdx) in (activeHistoryItem?.kaos?.gallery || [])" :key="tIdx">
                    <div class="relative aspect-[4/3] overflow-hidden rounded-lg bg-gray-100 cursor-pointer group"
                         @click="previewImage = thumb">
                      <img :src="thumb" class="h-full w-full object-cover group-hover:scale-105 transition-transform duration-300">
                      <template x-if="tIdx === 1">
                        <div class="absolute inset-0 flex items-center justify-center bg-black/40 text-xs font-bold text-white group-hover:bg-black/50 transition-colors">
                          +2
                        </div>
                      </template>
                    </div>
                  </template>
                </div>
              </div>

              {{-- Ruang bawah diisi badge agar proporsional dan tidak ada blank space --}}
              <div class="mt-4 rounded-xl bg-gray-50 p-3 border border-gray-100">
                <span class="text-[11px] font-semibold text-grey-deep">Kategori Pilihan:</span>
                <p class="mt-0.5 text-xs font-bold text-primary-1">Pilihan Teratas Civitas DTEDI</p>
              </div>
            </div>
          </div>
        </div>

        {{-- Kolom Kanan: Destinasi Wisata Pemenang (7/12) --}}
        <div class="md:col-span-7 flex flex-col space-y-2">
          <div class="flex items-center gap-2 text-sm font-bold text-primary-1">
            <x-solar-map-point-bold class="h-4 w-4 text-primary-1" />
            <span>Destinasi Wisata :</span>
          </div>

          <div class="flex-1 rounded-2xl border border-grey-1 bg-white p-4 shadow-sm flex flex-col justify-between">
            <div>
              {{-- Header Wisata & Rating --}}
              <div class="flex items-center justify-between">
                <h4 class="text-base font-bold text-black" x-text="activeHistoryItem?.wisata?.title || 'Pantai Kartini'"></h4>
                <div class="flex items-center gap-1 text-sm font-bold text-black">
                  <x-solar-star-bold class="h-4 w-4 text-warning" />
                  <span x-text="activeHistoryItem?.wisata?.rating || '4,8'"></span>
                </div>
              </div>

              {{-- Gambar Utama & Galeri Wisata (Clickable) --}}
              <div class="mt-3 grid grid-cols-1 sm:grid-cols-3 gap-2">
                <div class="sm:col-span-2 aspect-[16/10] overflow-hidden rounded-xl bg-gray-100 cursor-pointer group relative"
                     @click="previewImage = activeHistoryItem?.wisata?.image">
                  <img :src="activeHistoryItem?.wisata?.image" alt="Wisata Pemenang" class="h-full w-full object-cover group-hover:scale-105 transition-transform duration-300">
                  <div class="absolute inset-0 bg-black/0 group-hover:bg-black/20 transition-colors flex items-center justify-center opacity-0 group-hover:opacity-100">
                    <span class="rounded-full bg-white/90 p-2 text-black shadow-md">
                      <x-solar-magnifer-zoom-in-linear class="h-5 w-5" />
                    </span>
                  </div>
                </div>
                <div class="flex sm:flex-col gap-2">
                  <template x-for="(wThumb, wIdx) in (activeHistoryItem?.wisata?.gallery || [])" :key="wIdx">
                    <div class="relative flex-1 aspect-[16/10] overflow-hidden rounded-xl bg-gray-100 cursor-pointer group"
                         @click="previewImage = wThumb">
                      <img :src="wThumb" class="h-full w-full object-cover group-hover:scale-105 transition-transform duration-300">
                      <template x-if="wIdx === 1">
                        <div class="absolute inset-0 flex items-center justify-center bg-black/40 text-xs font-bold text-white group-hover:bg-black/50 transition-colors">
                          +2
                        </div>
                      </template>
                    </div>
                  </template>
                </div>
              </div>

              {{-- Detail Wisata (Lokasi, Jarak, Durasi) --}}
              <div class="mt-4 space-y-2.5 text-xs">
                <div class="flex justify-between items-start">
                  <div>
                    <span class="text-[11px] text-grey-deep">Lokasi</span>
                    <p class="font-medium text-black mt-0.5" x-text="activeHistoryItem?.wisata?.lokasi || 'Jepara, Jawa Tengah'"></p>
                  </div>
                  <span class="text-[11px] text-primary-1 font-semibold" x-text="'Dikunjungi: ' + (activeHistoryItem?.wisata?.dikunjungi || 2)"></span>
                </div>

                <div class="grid grid-cols-2 gap-4 pt-1">
                  <div>
                    <span class="text-[11px] text-grey-deep">Jarak</span>
                    <p class="font-bold text-black mt-0.5" x-text="activeHistoryItem?.wisata?.jarak || '120 Km'"></p>
                  </div>
                  <div>
                    <span class="text-[11px] text-grey-deep">Lama Perjalanan</span>
                    <p class="font-bold text-black mt-0.5" x-text="activeHistoryItem?.wisata?.durasi || '2 Hari'"></p>
                  </div>
                </div>

                {{-- Benefit Tags --}}
                <div class="pt-1">
                  <span class="text-[11px] text-grey-deep">Benefit:</span>
                  <div class="mt-1.5 flex flex-wrap gap-1.5">
                    <template x-for="(ben, bIdx) in (activeHistoryItem?.wisata?.benefits || [])" :key="bIdx">
                      <span class="rounded-md border border-grey-1 bg-white px-2 py-0.5 text-[10px] font-medium text-grey-deep" x-text="ben"></span>
                    </template>
                  </div>
                </div>
              </div>
            </div>

            {{-- Estimasi Cashback di Pojok Kanan Bawah --}}
            <div class="mt-5 pt-3 border-t border-gray-100 flex items-center justify-between">
              <span class="text-xs font-semibold text-grey-deep">Cashback Peserta:</span>
              <span class="text-xl font-bold text-primary-1" x-text="activeHistoryItem?.wisata?.biaya || 'Rp 500.000'"></span>
            </div>
          </div>
        </div>

      </div>

    </div>
  </x-modal>

  {{-- 6. Lightbox Fullscreen Preview Foto saat diklik --}}
  <x-modal show="Boolean(previewImage)" close="previewImage = null" maxWidth="lg">
    <div class="relative p-2 bg-black/90 rounded-2xl flex flex-col items-center">
      <button type="button"
              @click="previewImage = null"
              class="absolute top-4 right-4 z-10 flex h-8 w-8 items-center justify-center rounded-full bg-white/20 text-white hover:bg-white/40 transition-colors cursor-pointer">
        <x-solar-close-circle-linear class="h-6 w-6" />
      </button>
      <div class="w-full max-h-[80vh] flex items-center justify-center overflow-hidden rounded-xl p-2">
        <img :src="previewImage" alt="Preview Foto Fullscreen" class="max-h-[75vh] w-auto max-w-full object-contain rounded-lg shadow-2xl">
      </div>
    </div>
  </x-modal>

</div>
@endsection
