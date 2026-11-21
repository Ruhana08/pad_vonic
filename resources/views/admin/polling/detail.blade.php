@extends('layouts.focus')

@section('title', 'Detail & Hasil Polling - ' . ($title ?? 'Vonic'))
@section('page_title', 'Detail Polling')
@section('max_width', 'max-w-[1360px]')

@section('content')
@php
  $th = 'py-3.5 px-3 text-center text-[11px] font-bold text-white tracking-wider';
  $td = 'py-3 px-3 text-center text-xs text-black';
@endphp

<div class="space-y-6" x-data="{
  modalRekap: false
}">

  {{-- Section Atas: Judul Polling & Grid (Partisipasi Polling + Waktu Tersisa) --}}
  <div class="space-y-6">
    {{-- Baris Judul Polling & Tombol Status/Aksi --}}
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
      <div>
        <div class="flex items-center gap-3">
          <h1 class="text-2xl font-bold tracking-tight text-black">{{ $title }}</h1>
          @if (($status ?? '') === 'aktif')
            <span class="inline-flex h-7 items-center rounded-full bg-emerald-100 px-3.5 text-xs font-bold text-emerald-700">
              Sedang Berlangsung
            </span>
          @else
            <span class="inline-flex h-7 items-center rounded-full bg-gray-200 px-3.5 text-xs font-bold text-grey-deep">
              Telah Selesai (Arsip)
            </span>
          @endif
        </div>
        <p class="text-xs text-grey-2 mt-1">{{ $subtitle ?? 'Rincian data dan hasil pemungutan suara.' }}</p>
      </div>

      <div>
        <a href="{{ route('admin.polling.arsip') }}"
           class="btn-secondary h-10 px-5 text-xs font-semibold">
          <x-solar-arrow-left-linear class="h-4 w-4" />
          <span>Kembali ke Tabel Polling</span>
        </a>
      </div>
    </div>

    {{-- Grid 2 Kolom: Kiri Partisipasi Polling, Kanan Waktu/Status --}}
    <div class="grid grid-cols-1 gap-6 lg:grid-cols-12 items-stretch">
      {{-- Kolom Kiri: Partisipasi Polling (8/12 atau 65% width) --}}
      <x-card class="p-6 lg:col-span-8 flex flex-col justify-between">
        <div>
          <h2 class="text-xl font-bold text-black">Partisipasi Polling</h2>
          <p class="text-xs text-primary-1 mt-1 font-medium">Ringkasan keterlibatan peserta dalam pemungutan suara ini.</p>
        </div>

        {{-- 3 Box: Partisipasi, Suara Masuk, Belum Memilih --}}
        <div class="mt-5 grid grid-cols-1 gap-4 sm:grid-cols-3">
          {{-- 1. Partisipasi --}}
          <x-card variant="stat">
            <p class="text-xs font-semibold text-black">Partisipasi</p>
            <p class="mt-2 text-3xl font-bold text-primary-1">{{ $totalVote['percent'] }}%</p>
            <p class="mt-2 text-[11px] text-grey-2">{{ $totalVote['received'] }} suara diterima</p>
          </x-card>

          {{-- 2. Suara Masuk --}}
          <x-card variant="stat">
            <p class="text-xs font-semibold text-black">Suara Masuk</p>
            <p class="mt-2 text-3xl font-bold text-primary-1">{{ $totalVote['received'] }}</p>
            <p class="mt-2 text-[11px] text-grey-2">Dari {{ $totalVote['received'] + $totalVote['pending'] }} peserta</p>
          </x-card>

          {{-- 3. Belum Memilih --}}
          <x-card variant="stat">
            <p class="text-xs font-semibold text-black">Belum Memilih</p>
            <p class="mt-2 text-3xl font-bold text-danger">{{ $totalVote['pending'] }}</p>
            <p class="mt-2 text-[11px] text-grey-2">{{ round(($totalVote['pending'] / max(1, $totalVote['received'] + $totalVote['pending'])) * 100) }}% peserta belum memilih</p>
          </x-card>
        </div>
      </x-card>

      {{-- Kolom Kanan: Waktu / Batas Pelaksanaan (4/12 width) --}}
      <x-card class="p-6 lg:col-span-4 flex flex-col justify-between">
        <div>
          <div class="flex items-center gap-2 text-xs font-bold text-primary-1">
            <x-solar-clock-circle-linear class="h-4 w-4" />
            <span>{{ ($status ?? '') === 'aktif' ? 'Waktu Tersisa' : 'Waktu Pelaksanaan' }}</span>
          </div>

          <div class="mt-4">
            @if (($status ?? '') === 'aktif')
              <x-countdown :countdown="$countdown" />
            @else
              <div class="flex items-center gap-2 py-4 text-sm font-semibold text-grey-deep">
                <x-solar-check-circle-bold class="h-6 w-6 text-emerald-600" />
                <span>Pemungutan suara telah ditutup.</span>
              </div>
            @endif
          </div>
        </div>

        <div class="mt-4 border-t border-grey-1/30 pt-3">
          <p class="text-[10px] text-grey-2 uppercase tracking-wider font-semibold">Batas Periode:</p>
          <p class="mt-0.5 text-xs font-bold text-black">
            @if (($deadline[0] ?? '-') === '-')
              <span class="text-grey-deep">Tanggal Tidak Aktif</span>
            @else
              {{ $deadline[0] ?? 'Jumat, 16 Okt 2026' }}, {{ $deadline[1] ?? '18.00 WIB' }}
            @endif
          </p>
        </div>
      </x-card>
    </div>
  </div>

  {{-- Card Mandiri Rekapitulasi Suara --}}
  <x-card id="rekapitulasi-suara" class="p-6 border-2 border-primary-1"
          x-data="{
            expandedCats: {},
            toggleCat(key) {
              this.expandedCats[key] = !this.expandedCats[key];
            },
            isExpanded(key) {
              return !!this.expandedCats[key];
            }
          }">
    <div class="flex flex-wrap items-center justify-between gap-3 border-b-2 border-gray-100 pb-3">
      <h3 class="text-xl font-bold text-black">Rekapitulasi Suara</h3>
      <div class="flex flex-wrap gap-2">
        <a href="{{ request()->fullUrlWithQuery(['kategori' => 'semua']) }}"
           class="rounded-xl px-5 py-2 text-xs font-semibold transition-all
                  {{ $kategori === 'semua' ? 'bg-primary-1 text-white shadow-sm' : 'bg-white text-grey-deep border border-grey-1 hover:bg-gray-50' }}">
          Semua kategori
        </a>
        @foreach (['peserta' => 'Peserta', 'wisata' => 'Tempat wisata', 'kaos' => 'Desain kaos'] as $key => $label)
          <a href="{{ request()->fullUrlWithQuery(['kategori' => $key]) }}"
             class="rounded-xl px-5 py-2 text-xs font-semibold transition-all
                    {{ $kategori === $key ? 'bg-primary-1 text-white shadow-sm' : 'bg-white text-grey-deep border border-grey-1 hover:bg-gray-50' }}">
            {{ $label }}
          </a>
        @endforeach
      </div>
    </div>

    <div class="mt-4 overflow-x-auto">
      <table class="w-full min-w-[850px] border-separate border-spacing-0 text-xs">
        <thead>
          <tr class="text-[11px] font-bold text-primary-1 text-center">
            <th class="py-3 px-4 text-center w-14">No.</th>
            <th class="py-3 px-4 text-center">Pilihan</th>
            <th class="py-3 px-4 text-center">Dosen</th>
            <th class="py-3 px-4 text-center">Tendik</th>
            <th class="py-3 px-4 text-center">Total suara</th>
            <th class="py-3 px-4 text-center">Proporsi</th>
          </tr>
        </thead>

        @forelse ($categories as $key => $cat)
          @continue($kategori !== 'semua' && $kategori !== $key)
          @php
            $totalOpts = $cat['options']->count();
          @endphp
          <tbody>
            {{-- Banner Pembatas Kategori --}}
            <tr class="no-hover">
              <td colspan="6" class="pt-3 pb-1 !bg-transparent">
                <div class="flex items-center justify-between rounded-xl bg-[#EAF2FF] px-4 py-2.5">
                  <span class="text-xs font-bold text-black">{{ $cat['label'] }}</span>
                  
                  @if ($totalOpts > 5)
                    <button type="button"
                            @click="toggleCat('{{ $key }}')"
                            class="text-xs font-semibold text-primary-1 hover:underline cursor-pointer flex items-center gap-1">
                      <span x-text="isExpanded('{{ $key }}') ? 'Tutup' : 'Lihat Semua'"></span>
                      <x-solar-alt-arrow-down-linear class="h-3.5 w-3.5 transition-transform duration-200"
                                                     ::class="isExpanded('{{ $key }}') ? 'rotate-180' : ''" />
                    </button>
                  @else
                    <span></span>
                  @endif
                </div>
              </td>
            </tr>

            {{-- Baris Pilihan --}}
            @foreach ($cat['options'] as $idx => $opt)
              <tr class="border-b border-gray-100 transition-colors duration-150 hover:bg-[#F3F7FF] {{ $loop->first ? 'bg-[#F9FBFF]' : '' }}"
                  @if ($idx >= 5)
                    x-show="isExpanded('{{ $key }}')"
                    x-cloak
                  @endif>
                <td class="py-3.5 px-4 text-left font-semibold {{ $loop->first ? 'text-primary-1' : 'text-grey-deep' }}">
                  {{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}
                </td>
                <td class="py-3.5 px-4 text-left font-semibold text-black">
                  <span>{{ $opt['name'] }}</span>
                  @if ($loop->first)
                    <span class="ml-2 inline-flex items-center rounded bg-primary-1 px-1.5 py-0.5 text-[10px] font-bold text-white">
                      Terbanyak
                    </span>
                  @endif
                </td>
                <td class="py-3.5 px-4 text-left font-medium text-black">{{ $opt['dosen_pct'] }}%</td>
                <td class="py-3.5 px-4 text-left font-medium text-black">{{ $opt['tendik_pct'] }}%</td>
                <td class="py-3.5 px-4 text-left font-bold text-black">{{ $opt['total'] }} suara</td>
                <td class="py-3.5 px-4 text-left font-bold text-primary-1">{{ $opt['proporsi'] }}%</td>
              </tr>
            @endforeach
          </tbody>
        @empty
          <tbody>
            <tr>
              <td colspan="6" class="py-12 bg-white text-center">
                <div class="flex flex-col items-center justify-center text-center">
                  <div class="flex h-12 w-12 items-center justify-center rounded-full bg-primary-3/50 text-primary-1 mb-3">
                    <x-solar-chart-linear class="h-6 w-6" />
                  </div>
                  <p class="text-sm font-bold text-black">Belum Ada Rekapitulasi Suara</p>
                  <p class="mt-1 text-xs text-grey-deep max-w-sm">
                    Data perolehan suara akan muncul di sini setelah pemilih mulai berpartisipasi.
                  </p>
                </div>
              </td>
            </tr>
          </tbody>
        @endforelse
      </table>
    </div>
  </x-card>

  {{-- Aktivitas Suara Masuk Card --}}
  <x-card id="aktivitas-terbaru" class="mt-4 p-6 border-2 border-primary-1">
    <div class="flex flex-wrap items-center justify-between gap-3 border-b-2 border-gray-100 pb-3">
      <div>
        <h3 class="text-xl font-bold text-black">Aktivitas Suara Pemilih</h3>
        <p class="text-xs text-grey-deep mt-1">Data suara masuk dan rincian pilihan sivitas.</p>
      </div>

      <div class="flex flex-wrap items-center gap-2">
        <a href="{{ route('admin.polling.aktif.export-kaos') }}"
           class="inline-flex items-center gap-2 rounded-xl bg-primary-1 px-4 py-2.5 text-xs font-semibold text-white shadow-primary-glow hover:bg-primary-4 transition-all cursor-pointer">
          <x-solar-download-square-linear class="h-4 w-4" />
          <span>Unduh Data Ukuran Kaos (CSV)</span>
        </a>
      </div>
    </div>

    <x-table-pagination :paginator="$activities" class="mt-4" />

    <div class="mt-4 overflow-x-auto">
      <table class="w-full min-w-[950px] border-separate border-spacing-y-2 text-xs">
        <thead>
          <tr class="bg-secondary-1 text-white text-[11px] font-bold tracking-wider text-center">
            <th class="rounded-l-sm py-3 px-3 text-center w-14">NO.</th>
            <th class="py-3 px-4 text-center">DOSEN / SIVITAS</th>
            <th class="py-3 px-4 text-center">EMAIL</th>
            <th class="py-3 px-3 text-center">PESERTA</th>
            <th class="py-3 px-3 text-center">TEMPAT WISATA</th>
            <th class="py-3 px-4 text-center">DESAIN KAOS</th>
            <th class="py-3 px-4 text-center">UKURAN</th>
            <th class="rounded-r-sm py-3 px-3 text-center w-28">STATUS</th>
          </tr>
        </thead>
        <tbody>
          @forelse ($activities as $a)
            <tr class="transition-colors duration-150 hover:bg-[#F3F7FF] bg-white">
              <td class="border-b border-gray-100 py-3 px-3 text-left">
                <span class="inline-flex h-7 w-7 items-center justify-center rounded-xl border border-grey-1 bg-white text-xs font-semibold text-black shadow-xs">
                  {{ str_pad($activities->firstItem() + $loop->index, 2, '0', STR_PAD_LEFT) }}
                </span>
              </td>

              <td class="border-b border-gray-100 py-3 px-4 font-semibold text-black text-left">
                {{ $a['name'] }}
              </td>

              <td class="border-b border-gray-100 py-3 px-4 text-[11px] text-grey-deep text-left">
                {{ $a['email'] }}
              </td>

              <td class="border-b border-gray-100 py-3 px-3 text-left font-medium text-black">
                {{ $a['peserta'] }}
              </td>

              <td class="border-b border-gray-100 py-3 px-3 text-left font-medium text-black">
                {{ $a['wisata'] }}
              </td>

              <td class="border-b border-gray-100 py-3 px-4 text-left font-semibold text-primary-1">
                {{ $a['kaos'] }}
              </td>

              <td class="border-b border-gray-100 py-3 px-4 text-left">
                <div class="flex flex-wrap items-center justify-start gap-2">
                  @foreach ($a['kaos_items'] as $item)
                    <div class="inline-flex items-center gap-1.5 bg-transparent py-0.5">
                      <span class="inline-flex h-7 min-w-[28px] px-1.5 items-center justify-center rounded-xl border border-primary-1 text-xs font-bold text-primary-1 bg-white shadow-xs">
                        {{ $item['size'] }}
                      </span>
                      <span class="text-xs font-semibold text-black">
                        {{ $item['jumlah'] }}
                      </span>
                    </div>
                  @endforeach
                </div>
              </td>

              <td class="border-b border-gray-100 py-3 px-3 text-left">
                <span class="inline-flex h-7 w-24 items-center justify-center rounded-full bg-success-soft text-[11px] font-semibold text-success">
                  {{ $a['status'] }}
                </span>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="8" class="py-12 bg-white">
                <x-empty-state 
                  icon="solar-user-cross-linear"
                  title="Belum Ada Suara yang Masuk"
                  message="Belum ada civitas yang memberikan suara pada sesi polling ini." />
              </td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </x-card>

</div>
@endsection
