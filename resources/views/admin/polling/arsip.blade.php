@extends('layouts.admin')

@section('title', 'Riwayat / Arsip')

@section('content')
@php
$th = 'px-3 py-2.5 text-center text-xs font-semibold border-l border-white/30';
$td = 'px-3 py-3 text-xs';
$date = 'rounded-sm border border-primary-1 px-3 py-1 text-xs';
$btn = 'flex h-9 items-center rounded-button bg-white px-5 text-xs font-semibold text-primary-1 shadow-type-3 disabled:pointer-events-none disabled:opacity-40';
@endphp

<div class="space-y-4">

  {{-- ===== Status Polling ===== --}}
  <x-card class="p-6">
    <div class="flex items-center justify-between gap-3">
      <h2 class="text-xl font-bold text-black">Status Polling</h2>
      <x-mini-pagination :paginator="$polls" />
    </div>

    <div class="mt-4 rounded-md border border-primary-1 p-2">
      <div class="flex justify-end">
        <a href="{{ route('admin.polling.create') }}" class="flex items-center gap-2 rounded-button bg-primary-1 px-5 py-2.5 text-sm font-semibold text-white shadow-type-1 hover:bg-primary-4">
          <svg class="size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round">
            <circle cx="12" cy="12" r="9" />
            <path d="M12 8v8M8 12h8" />
          </svg>
          Tambah Polling
        </a>
      </div>

      <div class="mt-3 overflow-x-auto">
        <table class="w-full min-w-[900px] border-separate border-spacing-0">
          <thead>
            <tr class="bg-primary-1 text-white">
              <th class="w-14 rounded-l-sm px-3 py-2.5 text-xs font-semibold">NO.</th>
              <th class="{{ $th }}">ID</th>
              <th class="{{ $th }}">POLLING</th>
              <th class="{{ $th }}">PERIODE</th>
              <th class="{{ $th }}">DURASI</th>
              <th class="{{ $th }}">STATUS</th>
              <th class="{{ $th }} rounded-r-sm">AKSI</th>
            </tr>
          </thead>
          <tbody>
            @foreach ($polls as $p)
            <tr>
              <td class="px-3 py-2">
                <span class="mx-auto flex size-9 items-center justify-center rounded-sm border border-primary-1 text-xs">
                  {{ str_pad($polls->firstItem() + $loop->index, 2, '0', STR_PAD_LEFT) }}
                </span>
              </td>
              <td class="{{ $td }}">{{ $p['id'] }}</td>
              <td class="{{ $td }}">{{ $p['name'] }}</td>
              <td class="{{ $td }}">{{ $p['year'] }}</td>
              <td class="{{ $td }}">
                <div class="flex items-center justify-center gap-4">
                  <span class="{{ $date }}">{{ $p['start'] }}</span>
                  <span>s.d.</span>
                  <span class="{{ $date }}">{{ $p['end'] }}</span>
                </div>
              </td>
              <td class="{{ $td }}">
                <select class="rounded-sm border-0 bg-gray-50 py-1.5 pl-3 pr-7 text-xs text-secondary-2 shadow-type-3">
                  @foreach (['Draft', 'Aktif', 'Selesai'] as $s)
                  <option @selected($p['status']===$s)>{{ $s }}</option>
                  @endforeach
                </select>
              </td>
              <td class="{{ $td }}">
                <div class="flex items-center justify-center gap-3 text-secondary-2">
                  <a href="#" aria-label="Lihat" class="hover:text-primary-1">
                    <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                      <path d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7S2 12 2 12z" />
                      <circle cx="12" cy="12" r="3" />
                    </svg>
                  </a>
                  <a href="#" aria-label="Edit" class="hover:text-primary-1">
                    <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                      <path d="M11 4H5a2 2 0 0 0-2 2v13a2 2 0 0 0 2 2h13a2 2 0 0 0 2-2v-6" />
                      <path d="M18.5 2.5a2.1 2.1 0 0 1 3 3L12 15l-4 1 1-4z" />
                    </svg>
                  </a>
                  <button type="button" aria-label="Hapus" class="hover:text-danger">
                    <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                      <path d="M3 6h18M8 6V4h8v2M6 6l1 14h10l1-14M10 11v5M14 11v5" />
                    </svg>
                  </button>
                </div>
              </td>
            </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    </div>
  </x-card>

  {{-- ===== Riwayat (tabel) + Akses ===== --}}
  <script>
    // @ts-nocheck
    window.riwayatData = {
      items: {!! json_encode($history, JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_TAG | JSON_HEX_AMP) !!},
      pub: {!! json_encode($publicIds) !!}
    };

    document.addEventListener('alpine:init', () => {
      Alpine.data('riwayat', () => ({
        items: window.riwayatData.items,
        pub: window.riwayatData.pub,
        snapshot: [],
        editMode: false,
        page: 1,
        perPage: 10,
        get totalPages() {
          return Math.max(1, Math.ceil(this.items.length / this.perPage))
        },
        get pageItems() {
          return this.items.slice((this.page - 1) * this.perPage, this.page * this.perPage)
        },
        get publik() {
          return this.pub.length
        },
        get privat() {
          return this.items.length - this.pub.length
        },
        get allOnPageChecked() {
          return this.pageItems.length > 0 && this.pageItems.every(h => this.pub.includes(h.id))
        },
        isPub(id) {
          return this.pub.includes(id)
        },
        toggle(id) {
          if (!this.editMode) return;
          this.pub = this.isPub(id) ? this.pub.filter(i => i !== id) : [...this.pub, id];
        },
        toggleAllOnPage() {
          const ids = this.pageItems.map(h => h.id);
          this.pub = this.allOnPageChecked ?
            this.pub.filter(i => !ids.includes(i)) :
            [...new Set([...this.pub, ...ids])];
        },
        changePerPage() {
          this.page = 1
        },
        startEdit() {
          this.snapshot = [...this.pub];
          this.editMode = true
        },
        cancel() {
          this.pub = this.snapshot;
          this.editMode = false
        },
        save() {
          // TODO: kirim this.pub ke server (fetch PATCH) saat database sudah ada
          this.editMode = false;
        }
      }));
    });
  </script>

  <div class="grid grid-cols-1 items-start gap-4 xl:grid-cols-[1fr_240px]" x-data="riwayat">

    <x-card class="p-6">
      <h2 class="text-xl font-bold text-black">Daftar Riwayat Pemilihan Destinasi Wisata Piknik DTEDI</h2>

      {{-- Paginasi atas --}}
      <div class="mt-4 flex flex-wrap items-center justify-between gap-3">
        <div class="flex items-center gap-2">
          <span class="{{ $btn }}">Tampilkan</span>
          <select x-model.number="perPage" x-on:change="changePerPage()"
            class="h-9 rounded-sm border border-primary-1 bg-white px-3 text-xs font-semibold text-primary-1 focus:outline-none">
            <option value="10">10</option>
            <option value="25">25</option>
            <option value="50">50</option>
          </select>
        </div>

        <div class="flex items-center gap-2">
          <button type="button" class="{{ $btn }}" x-bind:disabled="page === 1" x-on:click="page--">Sebelumnya</button>
          <span class="flex h-9 items-center rounded-sm border border-primary-1 bg-white px-3 text-xs font-semibold text-primary-1"
            x-text="page + '/' + totalPages"></span>
          <button type="button" class="{{ $btn }}" x-bind:disabled="page === totalPages" x-on:click="page++">Selanjutnya</button>
        </div>
      </div>

      {{-- Tabel --}}
      <div class="mt-4 overflow-x-auto rounded-md border border-primary-1 p-2">
        <table class="w-full min-w-[820px] border-separate border-spacing-0">
          <thead>
            <tr class="bg-primary-1 text-white">
              <th x-show="editMode" x-cloak class="w-12 rounded-l-sm px-3 py-2.5">
                <input type="checkbox" class="size-4 cursor-pointer accent-white"
                  x-bind:checked="allOnPageChecked" x-on:change="toggleAllOnPage()" aria-label="Pilih semua di halaman ini">
              </th>
              <th class="w-14 px-3 py-2.5 text-xs font-semibold" x-bind:class="editMode ? '' : 'rounded-l-sm'">NO.</th>
              <th class="{{ $th }}">JUDUL</th>
              <th class="{{ $th }}">DESKRIPSI</th>
              <th class="{{ $th }}">PERIODE</th>
              <th class="{{ $th }}">PARTISIPAN</th>
              <th class="{{ $th }}">AKSES</th>
              <th class="{{ $th }} rounded-r-sm">AKSI</th>
            </tr>
          </thead>
          <tbody>
            <template x-for="(h, i) in pageItems" x-bind:key="h.id">
              <tr x-bind:class="[editMode ? 'cursor-pointer hover:bg-primary-3/50' : '', editMode && isPub(h.id) ? 'bg-primary-3' : '']"
                x-on:click="toggle(h.id)">
                <td x-show="editMode" x-cloak class="px-3 py-2 text-center">
                  <input type="checkbox" class="size-4 cursor-pointer accent-[#196BEF]"
                    x-bind:checked="isPub(h.id)" x-on:click.stop="toggle(h.id)" aria-label="Jadikan publik">
                </td>
                <td class="px-3 py-2">
                  <span class="mx-auto flex size-9 items-center justify-center rounded-sm border border-primary-1 text-xs"
                    x-text="String((page - 1) * perPage + i + 1).padStart(2, '0')"></span>
                </td>
                <td class="{{ $td }} font-semibold" x-text="h.title"></td>
                <td class="{{ $td }} max-w-[220px] truncate text-grey-2" x-bind:title="h.description" x-text="h.description"></td>
                <td class="{{ $td }} text-center" x-text="h.period"></td>
                <td class="{{ $td }} text-center" x-text="h.participants"></td>
                <td class="{{ $td }} text-center">
                  <span class="inline-block min-w-[70px] rounded-full px-3 py-0.5 font-medium"
                    x-bind:class="isPub(h.id) ? 'bg-success-soft text-success' : 'bg-primary-3 text-secondary-2'"
                    x-text="isPub(h.id) ? 'Publik' : 'Private'"></span>
                </td>
                <td class="{{ $td }} text-center">
                  <a href="#" x-on:click.stop
                    class="inline-flex rounded-button bg-primary-1 px-4 py-1.5 text-xs font-semibold text-white hover:bg-primary-4">
                    Lihat Hasil
                  </a>
                </td>
              </tr>
            </template>
          </tbody>
        </table>
      </div>
    </x-card>

    {{-- Akses (sticky di bawah navbar) --}}
    <x-card class="p-4 xl:sticky xl:top-[88px]">
      <div class="flex items-center justify-between rounded-sm bg-white px-3 py-2 shadow-type-3">
        <span class="text-sm font-bold text-primary-1">Akses</span>
        <button type="button" x-show="!editMode" x-on:click="startEdit()" aria-label="Ubah akses"
          class="flex size-8 items-center justify-center rounded-sm border border-primary-1 text-primary-1 hover:bg-primary-3">
          <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M11 4H5a2 2 0 0 0-2 2v13a2 2 0 0 0 2 2h13a2 2 0 0 0 2-2v-6" />
            <path d="M18.5 2.5a2.1 2.1 0 0 1 3 3L12 15l-4 1 1-4z" />
          </svg>
        </button>
      </div>

      <div class="mt-3 space-y-2">
        <div class="flex items-center gap-2">
          <span class="flex-1 rounded-sm bg-primary-1 px-3 py-2 text-xs font-medium text-white">Publik</span>
          <span class="w-14 rounded-sm bg-white py-2 text-center text-xs shadow-type-3" x-text="publik"></span>
        </div>
        <div class="flex items-center gap-2">
          <span class="flex-1 rounded-sm bg-primary-1 px-3 py-2 text-xs font-medium text-white">Private</span>
          <span class="w-14 rounded-sm bg-white py-2 text-center text-xs shadow-type-3" x-text="privat"></span>
        </div>
      </div>

      <div x-show="editMode" x-cloak class="mt-4 space-y-2">
        <p class="text-[11px] leading-snug text-grey-2">Centang baris yang ingin dijadikan publik.</p>
        <button type="button" x-on:click="save()"
          class="w-full rounded-button bg-primary-1 py-2 text-xs font-semibold text-white hover:bg-primary-4">Simpan</button>
        <button type="button" x-on:click="cancel()"
          class="w-full rounded-button border border-grey-1 py-2 text-xs font-semibold text-grey-2 hover:bg-gray-50">Batal</button>
      </div>
    </x-card>
  </div>

</div>
@endsection