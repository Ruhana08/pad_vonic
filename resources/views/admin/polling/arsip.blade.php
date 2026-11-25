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
         if (!this.targetPoll?.id_periode) return;
         this.$refs.publishForm.action = '/admin/polling/' + this.targetPoll.id_periode + '/publish';
         this.$refs.publishForm.submit();
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
         if (!this.targetPoll?.id_periode) return;
         this.$refs.deleteForm.action = '/admin/polling/' + this.targetPoll.id_periode;
         this.$refs.deleteForm.submit();
       },

       openResultModal(item) {
         this.activeHistoryItem = item;
         this.modalMode = 'view_result';
       }
     }">

  {{-- ===== SECTION 1: STATUS POLLING ===== --}}
  <div class="space-y-3">
    {{-- Baris Atas: Judul Status Polling --}}
    <div>
      <h1 class="text-2xl font-bold tracking-tight text-black">Status Polling</h1>
    </div>

    {{-- Baris Filter/Pagination Atas (di luar card): Tampilkan [10] & Sebelumnya 1/6 Selanjutnya --}}
    <x-table-pagination :paginator="$polls" paramName="per_page" class="pt-1" />

    {{-- Card Tabel Status Polling --}}
    <x-card class="p-6 shadow-sm">
      <div class="overflow-x-auto">
        <table class="w-full min-w-[920px] border-separate border-spacing-y-2 text-xs">
          <thead>
            <tr class="bg-secondary-1 text-white text-xs font-bold tracking-wider text-center">
              <th class="rounded-l-sm py-3 px-3 text-center w-px whitespace-nowrap">NO.</th>
              <th class="py-3 px-3 text-center min-w-[120px] whitespace-nowrap">ID</th>
              <th class="py-3 px-4 text-center min-w-[240px]">POLLING</th>
              <th class="py-3 px-4 text-center w-px whitespace-nowrap">WAKTU MULAI</th>
              <th class="py-3 px-4 text-center w-px whitespace-nowrap">WAKTU SELESAI</th>
              <th class="py-3 px-3 text-center w-px whitespace-nowrap">STATUS</th>
              <th class="rounded-r-sm py-3 px-3 text-center w-px whitespace-nowrap">AKSI</th>
            </tr>
          </thead>
          <tbody>
            @forelse ($polls as $p)
              <tr class="transition-colors duration-150 hover:bg-[#F3F7FF] bg-white">
                {{-- No. --}}
                <td class="border-b border-gray-100 py-3.5 px-3 text-center">
                  <span class="inline-flex h-7 w-7 items-center justify-center rounded-xl border border-grey-1 bg-white text-xs font-semibold text-black shadow-xs">
                    {{ str_pad($polls->firstItem() + $loop->index, 2, '0', STR_PAD_LEFT) }}
                  </span>
                </td>

                {{-- ID --}}
                <td class="border-b border-gray-100 py-3.5 px-3 text-center font-medium text-black whitespace-nowrap">
                  {{ $p['id'] }}
                </td>

                {{-- Polling Name (Bisa diklik untuk melihat analitik jika Aktif atau Selesai) --}}
                <td class="border-b border-gray-100 py-3.5 px-4 font-semibold text-black">
                  @if ($p['status'] === 'Aktif' || $p['status'] === 'Selesai')
                    <a href="{{ route('admin.polling.detail', ['id' => $p['id']]) }}" class="hover:text-primary-1 hover:underline transition-colors" title="Lihat Statistik & Analitik Lengkap">
                      {{ $p['name'] }}
                    </a>
                  @else
                    <span>{{ $p['name'] }}</span>
                  @endif
                </td>

                {{-- Waktu Mulai --}}
                <td class="border-b border-gray-100 py-3.5 px-4 text-center">
                  <span class="inline-flex h-7 items-center rounded-xl border border-primary-1 bg-white px-2.5 text-xs font-medium text-black shadow-xs">
                    {{ $p['start'] }}
                  </span>
                </td>

                {{-- Waktu Selesai --}}
                <td class="border-b border-gray-100 py-3.5 px-4 text-center">
                  <span class="inline-flex h-7 items-center rounded-xl border border-primary-1 bg-white px-2.5 text-xs font-medium text-black shadow-xs">
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
                  <span class="inline-flex h-7 items-center justify-center rounded-full px-4 text-xs font-semibold {{ $statusStyle }}">
                    {{ $p['status'] }}
                  </span>
                </td>

                {{-- Aksi: 4 slot tombol konsisten (Lihat Hasil Analitik, Edit, Post/Upload, Hapus) --}}
                <td class="border-b border-gray-100 py-3.5 px-3 text-left">
                  <div class="flex items-center justify-start gap-2">
                    @if ($p['status'] === 'Draft')
                      {{-- Draft: Lihat -> edit draft, Edit aktif, Post aktif, Hapus aktif --}}
                      <x-action-button type="view" :href="route('admin.polling.create', ['id' => $p['id_periode']])" data-turbo="false" label="Lihat & Lanjutkan Draft Polling" />
                      <x-action-button type="edit" :href="route('admin.polling.create', ['id' => $p['id_periode']])" data-turbo="false" label="Edit Polling" />
                      <x-action-button type="upload" label="Publikasikan Polling"
                                       @click="openPostModal({{ json_encode($p) }})" />
                      <x-action-button type="delete" label="Hapus Polling"
                                       @click="openDeleteModal({{ json_encode($p) }})" />
                    @elseif ($p['status'] === 'Aktif')
                      {{-- Aktif: Lihat -> ke halaman Polling Aktif (dengan sidebar) --}}
                      <x-action-button type="view" :href="route('admin.polling.aktif')" label="Lihat Halaman Polling Aktif" />
                      <x-action-button type="edit" :disabled="true" label="Tidak dapat mengedit polling yang sedang aktif" />
                      <x-action-button type="upload" :disabled="true" label="Polling sudah aktif" />
                      <x-action-button type="delete" label="Hentikan / Akhiri Polling"
                                       @click="openDeleteModal({{ json_encode($p) }})" />
                    @else
                      {{-- Selesai: Lihat -> ke halaman Detail Arsip (tanpa sidebar) --}}
                      <x-action-button type="view" :href="route('admin.polling.detail', ['id' => $p['id_periode']])" label="Lihat Rincian & Analitik Voting" />
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
    function riwayat() {
      return {
        items: @json($history),
        pub: @json($publicIds),
        snapshot: [],
        editMode: false,
        page: 1,
        perPage: 10,

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
      };
    }
  </script>

  <div class="grid grid-cols-1 items-start gap-4 xl:grid-cols-[1fr_240px]" x-data="riwayat()">

    {{-- Card Tabel Riwayat (Border Biru Tebal Sesuai Status Polling & Figma) --}}
    <x-card class="p-6 shadow-sm">
      <h2 class="text-xl font-bold text-black">Daftar Riwayat Pemilihan Destinasi Wisata Piknik DTEDI</h2>

      {{-- Kontrol Pagination Riwayat Menggunakan Komponen Reusable --}}
      <x-table-pagination class="mt-4"
                          alpineModel="perPage"
                          alpineChange="changePerPage()"
                          alpinePage="page"
                          alpineTotalPages="totalPages"
                          alpinePrev="page--"
                          alpineNext="page++"
                          :disabled="count($history) === 0" />

      {{-- Tabel Riwayat: Kolom PERIODE dihapus, Aksi diubah menjadi Hasil Polling --}}
      <div class="mt-4 overflow-x-auto">
        <table class="w-full min-w-[1050px] border-separate border-spacing-y-2 text-xs">
          <thead>
            <tr class="bg-secondary-1 text-white text-xs font-bold tracking-wider text-center">
              <th x-show="editMode" x-cloak class="rounded-l-sm py-3 px-3 text-center w-px whitespace-nowrap">
                <input type="checkbox" class="size-4 cursor-pointer accent-white"
                       x-bind:checked="allOnPageChecked" x-on:change="toggleAllOnPage()" aria-label="Pilih semua di halaman ini">
              </th>
              <th class="py-3 px-3 text-center w-px whitespace-nowrap" x-bind:class="editMode ? '' : 'rounded-l-sm'">NO.</th>
              <th class="py-3 px-4 text-center min-w-[200px]">JUDUL</th>
              <th class="py-3 px-4 text-center min-w-[280px]">DESKRIPSI</th>
              <th class="py-3 px-4 text-center w-px whitespace-nowrap">WAKTU MULAI</th>
              <th class="py-3 px-4 text-center w-px whitespace-nowrap">WAKTU SELESAI</th>
              <th class="py-3 px-3 text-center w-px whitespace-nowrap">AKSES</th>
              <th class="rounded-r-sm py-3 px-3 text-center w-px whitespace-nowrap">AKSI</th>
            </tr>
          </thead>
          <tbody>
            <template x-for="(h, i) in pageItems" x-bind:key="h.id">
              <tr class="transition-colors duration-150 hover:bg-[#F3F7FF] bg-white"
                  x-bind:class="[editMode ? 'cursor-pointer hover:bg-primary-3/50' : '', editMode && isPub(h.id) ? 'bg-primary-3' : '']"
                  x-on:click="toggle(h.id)">
                <td x-show="editMode" x-cloak class="border-b border-gray-100 py-3.5 px-3 text-center">
                  <input type="checkbox" class="size-4 cursor-pointer accent-[#196BEF]"
                         x-bind:checked="isPub(h.id)" x-on:click.stop="toggle(h.id)" aria-label="Jadikan publik">
                </td>
                <td class="border-b border-gray-100 py-3.5 px-3 text-center">
                  <span class="inline-flex h-7 w-7 items-center justify-center rounded-xl border border-grey-1 bg-white text-xs font-semibold text-black shadow-xs"
                        x-text="String((page - 1) * perPage + i + 1).padStart(2, '0')"></span>
                </td>
                <td class="border-b border-gray-100 py-3.5 px-4 font-semibold text-black">
                  <a :href="'{{ route('admin.polling.aktif') }}'" class="hover:text-primary-1 hover:underline transition-colors" title="Buka Detail Analitik" x-text="h.title"></a>
                </td>
                <td class="border-b border-gray-100 py-3.5 px-4 text-grey-deep leading-relaxed"
                    x-bind:title="h.description" x-text="h.description"></td>
                {{-- Waktu Mulai --}}
                <td class="border-b border-gray-100 py-3.5 px-4 text-center">
                  <span class="inline-flex h-7 items-center rounded-xl border border-primary-1 bg-white px-2.5 text-xs font-medium text-black shadow-xs" x-text="h.start"></span>
                </td>
                {{-- Waktu Selesai --}}
                <td class="border-b border-gray-100 py-3.5 px-4 text-center">
                  <span class="inline-flex h-7 items-center rounded-xl border border-primary-1 bg-white px-2.5 text-xs font-medium text-black shadow-xs" x-text="h.end"></span>
                </td>
                {{-- Akses Pill --}}
                <td class="border-b border-gray-100 py-3.5 px-3 text-center">
                  <span class="inline-flex h-7 w-20 items-center justify-center rounded-full text-xs font-semibold"
                        x-bind:class="isPub(h.id) ? 'bg-success-soft text-success' : 'bg-primary-3 text-primary-1'"
                        x-text="isPub(h.id) ? 'Publik' : 'Private'"></span>
                </td>
                {{-- Tombol Aksi: Hasil Polling (Membuka Quick-View Pemenang) --}}
                <td class="border-b border-gray-100 py-3.5 px-3 text-center">
                  <button type="button"
                          @click.stop="openResultModal(h)"
                          class="inline-flex items-center justify-center whitespace-nowrap rounded-xl bg-primary-1 px-3.5 py-1.5 text-xs font-semibold text-white shadow-menu hover:bg-primary-4 transition-all cursor-pointer">
                    Hasil Polling
                  </button>
                </td>
              </tr>
            </template>
            <template x-if="items.length === 0">
              <tr>
                <td colspan="8" class="py-10 bg-white text-center">
                  <div class="flex flex-col items-center justify-center text-center">
                    <div class="flex h-12 w-12 items-center justify-center rounded-full bg-primary-3/50 text-primary-1 mb-3">
                      <x-solar-history-linear class="h-6 w-6" />
                    </div>
                    <p class="text-sm font-bold text-black">Belum Ada Riwayat Arsip Polling</p>
                    <p class="mt-1 text-xs text-grey-deep max-w-sm">
                      Polling yang telah selesai dilaksanakan akan otomatis masuk dan tercatat di daftar riwayat arsip ini.
                    </p>
                  </div>
                </td>
              </tr>
            </template>
          </tbody>
        </table>
      </div>
    </x-card>

    {{-- Panel Samping: Akses (Publik / Private) --}}
    <x-card class="p-5 shadow-sm xl:sticky xl:top-[112px]">
      <div class="flex items-center justify-between pb-3 border-b border-gray-100">
        <span class="text-sm font-bold text-primary-1">Akses</span>
        <button type="button" x-show="!editMode" x-on:click="startEdit()" aria-label="Ubah akses"
                class="flex h-7 w-7 items-center justify-center rounded-xl border border-grey-1 text-primary-1 shadow-xs hover:border-primary-1 hover:bg-primary-3/30 transition-all">
          <x-solar-pen-new-square-linear class="h-4 w-4" />
        </button>
      </div>

      <div class="mt-4 space-y-2.5">
        <div class="flex items-center gap-2">
          <span class="flex-1 rounded-xl bg-primary-1 px-3 py-2 text-xs font-medium text-white shadow-xs">Publik</span>
          <span class="min-w-14 rounded-xl border border-grey-1 bg-white px-2 py-2 text-center text-xs font-bold text-black shadow-xs leading-none" x-text="publik">0</span>
        </div>
        <div class="flex items-center gap-2">
          <span class="flex-1 rounded-xl bg-primary-1 px-3 py-2 text-xs font-medium text-white shadow-xs">Private</span>
          <span class="min-w-14 rounded-xl border border-grey-1 bg-white px-2 py-2 text-center text-xs font-bold text-black shadow-xs leading-none" x-text="privat">0</span>
        </div>
      </div>

      <div x-show="editMode" x-cloak class="mt-5 space-y-2 border-t border-gray-100 pt-4">
        <p class="text-xs leading-snug text-grey-deep">Centang baris yang ingin dijadikan publik.</p>
        <button type="button" x-on:click="save()"
                class="w-full rounded-xl bg-primary-1 py-2 text-xs font-semibold text-white shadow-menu hover:bg-primary-4 transition-all">
          Simpan
        </button>
        <button type="button" x-on:click="cancel()"
                class="w-full rounded-xl border border-grey-1 bg-white py-2 text-xs font-semibold text-grey-deep shadow-xs hover:bg-gray-50 transition-all">
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
                class="w-full rounded-xl bg-primary-1 py-2 text-xs font-semibold text-white shadow-menu hover:bg-primary-4 transition-all">
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
           class="flex-1 rounded-xl border border-grey-1 bg-white py-2 text-xs font-semibold text-grey-deep shadow-xs hover:bg-gray-50 transition-all text-center">
          Edit Waktu
        </a>
        <button type="button"
                @click="confirmPostNow()"
                class="flex-1 rounded-xl bg-primary-1 py-2 text-xs font-semibold text-white shadow-menu hover:bg-primary-4 transition-all">
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
                class="flex-1 rounded-xl border border-grey-1 bg-white py-2 text-xs font-semibold text-grey-deep shadow-xs hover:bg-gray-50 transition-all">
          Batal
        </button>
        <button type="button"
                @click="confirmDelete()"
                class="flex-1 rounded-xl bg-danger py-2 text-xs font-semibold text-white shadow-menu hover:bg-red-700 transition-all">
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
                class="flex-1 rounded-xl border border-grey-1 bg-white py-2 text-xs font-semibold text-grey-deep shadow-xs hover:bg-gray-50 transition-all">
          Batal
        </button>
        <button type="button"
                @click="confirmDelete()"
                class="flex-1 rounded-xl bg-danger py-2 text-xs font-semibold text-white shadow-menu hover:bg-red-700 transition-all">
          Ya, Hapus
        </button>
      </div>
    </div>
  </x-modal>

  {{-- Hidden forms untuk Publish & Delete Polling --}}
  <form x-ref="publishForm" method="POST" action="" class="hidden">
    @csrf
  </form>
  <form x-ref="deleteForm" method="POST" action="" class="hidden">
    @csrf
    @method('DELETE')
  </form>

  {{-- 5. Quick-View Pop Up: Hasil Pemenang Polling (Shared Component = User) --}}
  <x-modal-hasil-voting show="modalMode === 'view_result'" close="modalMode = null" item="activeHistoryItem" />


</div>
@endsection
