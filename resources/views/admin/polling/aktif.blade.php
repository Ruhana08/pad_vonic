@extends('layouts.admin')

@section('title', 'Polling Aktif - Piknik DTEDI Periode 2026')

@section('content')
@php
  $th = 'py-3.5 px-3 text-center text-[11px] font-bold text-white tracking-wider';
  $td = 'py-3 px-3 text-center text-xs text-black';
@endphp

<div class="space-y-6" x-data="{
  modalRekap: false,
  modalAnnounce: false,
  isAnnounced: false
}">

  {{-- Section Atas: Judul Polling & Grid (Partisipasi Polling + Waktu Tersisa) --}}
  <div class="space-y-6">
    {{-- Baris Judul Polling & Tombol Aksi --}}
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
      <h1 class="text-2xl font-bold tracking-tight text-black">{{ $title }}</h1>

      <div class="flex items-center gap-3">
        <template x-if="!isAnnounced">
          <button type="button"
                  @click="modalAnnounce = true"
                  class="inline-flex items-center gap-2 rounded-sm bg-gradient-to-r from-primary-1 to-secondary-1 px-5 py-2.5 text-xs font-semibold text-white shadow-primary-glow hover:opacity-95 transition-all cursor-pointer">
            <x-solar-cup-star-linear class="h-4 w-4" />
            <span>Umumkan Hasil Voting</span>
          </button>
        </template>

        <template x-if="isAnnounced">
          <span class="inline-flex items-center gap-2 rounded-sm bg-success-soft border border-success/40 px-4 py-2 text-xs font-bold text-success">
            <x-solar-check-circle-bold class="h-4 w-4" />
            <span>Hasil Telah Diumumkan</span>
          </span>
        </template>
      </div>
    </div>

    {{-- Grid 2 Kolom: Kiri Partisipasi Polling, Kanan Waktu Tersisa --}}
    <div class="grid grid-cols-1 gap-6 lg:grid-cols-12 items-stretch">
      {{-- Kolom Kiri: Partisipasi Polling (8/12 atau 65% width) --}}
      <x-card class="p-6 lg:col-span-8 flex flex-col justify-between">
        <div>
          <h2 class="text-xl font-bold text-black">Partisipasi Polling</h2>
          <p class="text-xs text-primary-1 mt-1 font-medium">Ringkasan keterlibatan peserta selama pemungutan suara berlangsung.</p>
        </div>

        {{-- 3 Box: Partisipasi, Suara Masuk, Belum Memilih --}}
        <div class="mt-5 grid grid-cols-1 gap-4 sm:grid-cols-3">
          {{-- 1. Partisipasi --}}
          <div class="rounded-xl border border-primary-1 bg-white p-4">
            <p class="text-xs font-semibold text-black">Partisipasi</p>
            <p class="mt-2 text-3xl font-bold text-primary-1">{{ $totalVote['percent'] }}%</p>
            <p class="mt-2 text-[11px] text-grey-2">{{ $totalVote['received'] }} suara diterima</p>
          </div>

          {{-- 2. Suara Masuk --}}
          <div class="rounded-xl border border-primary-1 bg-white p-4">
            <p class="text-xs font-semibold text-black">Suara Masuk</p>
            <p class="mt-2 text-3xl font-bold text-primary-1">{{ $totalVote['received'] }}</p>
            <p class="mt-2 text-[11px] text-grey-2">Dari {{ $totalVote['received'] + $totalVote['pending'] }} peserta</p>
          </div>

          {{-- 3. Belum Memilih --}}
          <div class="rounded-xl border border-primary-1 bg-white p-4">
            <p class="text-xs font-semibold text-black">Belum Memilih</p>
            <p class="mt-2 text-3xl font-bold text-danger">{{ $totalVote['pending'] }}</p>
            <p class="mt-2 text-[11px] text-grey-2">{{ round(($totalVote['pending'] / max(1, $totalVote['received'] + $totalVote['pending'])) * 100) }}% peserta belum memilih</p>
          </div>
        </div>
      </x-card>

      {{-- Kolom Kanan: Waktu Tersisa Card (4/12 width) --}}
      <x-card class="p-6 lg:col-span-4 flex flex-col justify-between">
        <div>
          <div class="flex items-center gap-2 text-xs font-bold text-primary-1">
            <x-solar-clock-circle-linear class="h-4 w-4" />
            <span>Waktu Tersisa</span>
          </div>

          <div class="mt-4">
            <x-countdown :countdown="$countdown" />
          </div>
        </div>

        <div class="mt-4 border-t border-grey-1/30 pt-3">
          <p class="text-[10px] text-grey-2 uppercase tracking-wider font-semibold">Penutupan:</p>
          <p class="mt-0.5 text-xs font-bold text-black">{{ $deadline[0] ?? 'Jumat, 16 Okt 2026' }}, {{ $deadline[1] ?? '18.00 WIB' }}</p>
        </div>
      </x-card>
    </div>
  </div>

  {{-- Card Mandiri Rekapitulasi Suara (Border Stroke Biru Paling Luar Sesuai Figma) --}}
  <x-card id="rekapitulasi-suara" class="p-6 border-2 border-primary-1">
    <div class="flex flex-wrap items-center justify-between gap-3 border-b-2 border-gray-100 pb-3">
      <h3 class="text-xl font-bold text-black">Rekapitulasi Suara</h3>
      <div class="flex flex-wrap gap-2">
        <a href="{{ route('admin.polling.aktif', ['kategori' => 'semua']) }}"
           class="rounded-sm px-5 py-2 text-xs font-semibold transition-all
                  {{ $kategori === 'semua' ? 'bg-primary-1 text-white shadow-sm' : 'bg-white text-grey-deep border border-grey-1 hover:bg-gray-50' }}">
          Semua kategori
        </a>
        @foreach (['peserta' => 'Peserta', 'wisata' => 'Tempat wisata', 'kaos' => 'Desain kaos'] as $key => $label)
          <a href="{{ route('admin.polling.aktif', ['kategori' => $key]) }}"
             class="rounded-sm px-5 py-2 text-xs font-semibold transition-all
                    {{ $kategori === $key ? 'bg-primary-1 text-white shadow-sm' : 'bg-white text-grey-deep border border-grey-1 hover:bg-gray-50' }}">
            {{ $label }}
          </a>
        @endforeach
      </div>
    </div>

    <div class="mt-4 overflow-x-auto">
      <table class="w-full min-w-[850px] border-separate border-spacing-0 text-xs">
        {{-- Header Kolom Ringan (Teks Biru) --}}
        <thead>
          <tr class="text-[11px] font-bold text-primary-1 text-left">
            <th class="py-3 px-4 text-left w-14">No.</th>
            <th class="py-3 px-4 text-left">Pilihan</th>
            <th class="py-3 px-4 text-left">Dosen</th>
            <th class="py-3 px-4 text-left">Tendik</th>
            <th class="py-3 px-4 text-left">Total suara</th>
            <th class="py-3 px-4 text-left">Proporsi</th>
          </tr>
        </thead>

        @foreach ($categories as $key => $cat)
          @continue($kategori !== 'semua' && $kategori !== $key)
          @php
            $rows = $kategori === 'semua' ? $cat['options']->take(3) : $cat['options'];
          @endphp
          <tbody>
            {{-- Banner Pembatas Kategori (Biru Muda Lembut Sesuai Desain Gambar) --}}
            <tr>
              <td colspan="6" class="pt-3 pb-1">
                <div class="flex items-center justify-between rounded-sm bg-[#EAF2FF] px-4 py-2.5">
                  <span class="text-xs font-bold text-black">{{ $cat['label'] }}</span>
                  @if ($kategori === 'semua' && $cat['options']->count() > 3)
                    <a href="{{ route('admin.polling.aktif', ['kategori' => $key]) }}"
                       class="text-xs font-semibold text-primary-1 hover:underline">Lihat Semua</a>
                  @else
                    <span class="text-[11px] font-semibold text-primary-1">Urutan Suara Terbanyak</span>
                  @endif
                </div>
              </td>
            </tr>

            {{-- Baris Pilihan (Fokus Suara Terbanyak: Baris Pertama Tebal / Leading) --}}
            @foreach ($rows as $opt)
              <tr class="border-b border-gray-100 transition-colors hover:bg-primary-3/20 {{ $loop->first ? 'bg-[#F9FBFF]' : '' }}">
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
        @endforeach
      </table>
    </div>

    {{-- Baris Footer Paginasi Rekapitulasi Suara (Sesuai Desain Figma) --}}
    <div class="mt-5 flex flex-wrap items-center justify-between gap-3 border-t border-gray-100 pt-4">
      <div class="flex items-center gap-2">
        <span class="text-xs font-medium text-black">Tampilkan</span>
        <x-dropdown-select name="rekap_per_page"
                           value="10"
                           :options="[10 => '10', 25 => '25', 50 => '50']"
                           variant="primary" />
      </div>

      <div class="flex items-center gap-2">
        <span class="flex h-9 items-center rounded-sm bg-white px-5 text-xs font-semibold text-primary-1 shadow-menu opacity-40 pointer-events-none">
          Sebelumnya
        </span>
        <span class="flex h-9 items-center rounded-sm border border-primary-1 bg-white px-3.5 text-xs font-semibold text-primary-1 shadow-xs">
          1/6
        </span>
        <span class="flex h-9 items-center rounded-sm bg-white px-5 text-xs font-semibold text-primary-1 shadow-menu hover:bg-primary-3/30 transition-all cursor-pointer">
          Selanjutnya
        </span>
      </div>
    </div>
  </x-card>

  {{-- Aktivitas Terbaru & Pengelolaan Kaos Card dengan Border Stroke Biru Sesuai Desain Figma --}}
  <x-card id="aktivitas-terbaru" class="mt-4 p-6 border-2 border-primary-1">
    <div class="flex flex-wrap items-center justify-between gap-3 border-b-2 border-gray-100 pb-3">
      <div>
        <h3 class="text-xl font-bold text-black">Aktivitas Terbaru</h3>
        <p class="text-xs text-grey-deep mt-1">Data suara masuk dan rincian ukuran pakaian keluarga peserta.</p>
      </div>

      {{-- Tombol Aksi Unduh Data Kaos (Sesuai Gaya Desain Tombol Figma: rounded-sm shadow-menu/primary-glow) --}}
      <div class="flex flex-wrap items-center gap-2">
        <a href="{{ route('admin.polling.aktif.export-kaos') }}"
           class="inline-flex items-center gap-2 rounded-sm bg-primary-1 px-4 py-2.5 text-xs font-semibold text-white shadow-primary-glow hover:bg-primary-4 transition-all cursor-pointer">
          <x-solar-download-square-linear class="h-4 w-4" />
          <span>Unduh Data Ukuran Kaos (CSV)</span>
        </a>
      </div>
    </div>

    <x-table-pagination :paginator="$activities" class="mt-4" />

    {{-- Tabel Aktivitas dengan Header Biru Gelap (bg-secondary-1) Sesuai Permintaan --}}
    <div class="mt-4 overflow-x-auto">
      <table class="w-full min-w-[950px] border-separate border-spacing-y-2 text-xs">
        <thead>
          <tr class="bg-secondary-1 text-white text-[11px] font-bold tracking-wider text-left">
            <th class="rounded-l-sm py-3 px-3 text-left w-14">NO.</th>
            <th class="py-3 px-4 text-left">DOSEN</th>
            <th class="py-3 px-4 text-left">EMAIL</th>
            <th class="py-3 px-3 text-left">PESERTA</th>
            <th class="py-3 px-3 text-left">TEMPAT WISATA</th>
            <th class="py-3 px-4 text-left">DESAIN KAOS</th>
            <th class="py-3 px-4 text-left">UKURAN</th>
            <th class="rounded-r-sm py-3 px-3 text-left w-28">STATUS</th>
          </tr>
        </thead>
        <tbody>
          @forelse ($activities as $a)
            <tr class="transition-colors hover:bg-primary-3/20 bg-white">
              {{-- No. --}}
              <td class="border-b border-gray-100 py-3 px-3 text-left">
                <span class="inline-flex h-7 w-7 items-center justify-center rounded-sm border border-grey-1 bg-white text-xs font-semibold text-black shadow-xs">
                  {{ str_pad($activities->firstItem() + $loop->index, 2, '0', STR_PAD_LEFT) }}
                </span>
              </td>

              {{-- Dosen / Nama --}}
              <td class="border-b border-gray-100 py-3 px-4 font-semibold text-black text-left">
                {{ $a['name'] }}
              </td>

              {{-- Email --}}
              <td class="border-b border-gray-100 py-3 px-4 text-[11px] text-grey-deep text-left">
                {{ $a['email'] }}
              </td>

              {{-- Pilihan Peserta --}}
              <td class="border-b border-gray-100 py-3 px-3 text-left font-medium text-black">
                {{ $a['peserta'] }}
              </td>

              {{-- Pilihan Wisata --}}
              <td class="border-b border-gray-100 py-3 px-3 text-left font-medium text-black">
                {{ $a['wisata'] }}
              </td>

              {{-- Kolom Desain Kaos --}}
              <td class="border-b border-gray-100 py-3 px-4 text-left font-semibold text-primary-1">
                {{ $a['kaos'] }}
              </td>

              {{-- Kolom Ukuran (Badge Stroke + Kuantitas) --}}
              <td class="border-b border-gray-100 py-3 px-4 text-left">
                <div class="flex flex-wrap items-center justify-start gap-1.5">
                  @foreach ($a['kaos_items'] as $item)
                    <div class="inline-flex items-center gap-1 bg-white px-1.5 py-0.5 rounded-sm">
                      <span class="inline-flex h-5 w-6 items-center justify-center rounded-sm border border-primary-1 text-[11px] font-bold text-primary-1 bg-primary-3/40">
                        {{ $item['size'] }}
                      </span>
                      <span class="text-[11px] font-semibold text-black">
                        {{ $item['jumlah'] }}
                      </span>
                    </div>
                  @endforeach
                </div>
              </td>

              {{-- Status Pill --}}
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
                  message="Belum ada civitas yang memberikan suara pada sesi polling ini. Rekapitulasi pilihan akan otomatis muncul begitu peserta mulai memilih." />
              </td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </x-card>

  {{-- Modal Ringkasan Produksi Kaos untuk Vendor --}}
  <x-modal show="modalRekap" close="modalRekap = false" maxWidth="lg">
    <div class="p-6">
      <div class="flex items-center justify-between border-b border-grey-1 pb-3">
        <div>
          <h3 class="text-lg font-bold text-black">Ringkasan Total Ukuran Kaos</h3>
          <p class="text-xs text-grey-2">Total kebutuhan kaos per ukuran untuk vendor konveksi.</p>
        </div>
        <button type="button" @click="modalRekap = false" class="text-grey-2 hover:text-black text-xl font-bold leading-none">&times;</button>
      </div>

      <div class="mt-4">
        <table class="w-full text-xs border border-grey-1 rounded-sm overflow-hidden text-left">
          <thead>
            <tr class="bg-secondary-1 text-white text-left">
              <th class="py-2.5 px-4 text-left font-semibold">UKURAN KAOS</th>
              <th class="py-2.5 px-4 text-left font-semibold">JUMLAH (PCS)</th>
            </tr>
          </thead>
          <tbody>
            @foreach ($rekapKaos as $size => $qty)
              <tr class="border-b border-gray-100 {{ $loop->even ? 'bg-gray-50' : 'bg-white' }}">
                <td class="py-2.5 px-4 font-bold text-primary-1 text-left">{{ $size }}</td>
                <td class="py-2.5 px-4 text-left font-semibold text-black">{{ $qty }} helai</td>
              </tr>
            @endforeach
            <tr class="bg-primary-3 font-bold text-primary-1">
              <td class="py-3 px-4 text-left">TOTAL KESELURUHAN</td>
              <td class="py-3 px-4 text-left text-sm">{{ $totalKaos }} helai</td>
            </tr>
          </tbody>
        </table>
      </div>

      <div class="mt-5 flex justify-end gap-2">
        <a href="{{ route('admin.polling.aktif.export-kaos', ['type' => 'vendor']) }}"
           class="rounded-sm bg-primary-1 px-4 py-2 text-xs font-semibold text-white hover:bg-primary-4">
          Unduh Rekap Vendor (CSV)
        </a>
      </div>
    </div>
  </x-modal>

  {{-- Modal Konfirmasi & Publikasi Pengumuman Hasil Voting --}}
  <x-modal show="modalAnnounce" close="modalAnnounce = false" maxWidth="820px">
    <div class="p-8">
      {{-- Header Modal --}}
      <div class="flex items-center justify-between border-b border-grey-1 pb-4">
        <div class="flex items-center gap-3">
          <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-primary-3/60 text-primary-1">
            <x-solar-cup-star-linear class="h-6 w-6" />
          </div>
          <div>
            <h2 class="text-xl font-bold text-primary-1">Umumkan Hasil Resmi Voting</h2>
            <p class="text-xs text-grey-deep mt-0.5">Tetapkan dan umumkan pemenang dari 3 kategori ke seluruh sivitas DTEDI.</p>
          </div>
        </div>
        <button type="button" @click="modalAnnounce = false" class="text-grey-2 hover:text-black text-2xl font-bold leading-none">&times;</button>
      </div>

      {{-- Informasi Rangkuman Pemenang Tiap Kategori --}}
      <div class="mt-6">
        <p class="text-xs font-bold uppercase tracking-wider text-black mb-3">Ringkasan Pemenang Suara Terbanyak (Rank #1):</p>

        <div class="grid grid-cols-1 gap-3.5 sm:grid-cols-3">
          {{-- Kategori 1: Peserta --}}
          <div class="rounded-xl border-2 border-primary-1 bg-primary-3/30 p-4 shadow-xs">
            <span class="rounded-sm bg-primary-1 px-2 py-0.5 text-[10px] font-bold text-white uppercase tracking-wider">
              Kategori 1: Peserta
            </span>
            <h4 class="mt-3 text-sm font-bold text-black">Dosen dan Tendik Dipisah</h4>
            <div class="mt-3 flex items-center justify-between text-xs border-t border-primary-2/30 pt-2">
              <span class="text-grey-deep">Perolehan</span>
              <span class="font-bold text-primary-1">63% (138 suara)</span>
            </div>
          </div>

          {{-- Kategori 2: Tempat Wisata --}}
          <div class="rounded-xl border-2 border-primary-1 bg-primary-3/30 p-4 shadow-xs">
            <span class="rounded-sm bg-primary-1 px-2 py-0.5 text-[10px] font-bold text-white uppercase tracking-wider">
              Kategori 2: Wisata
            </span>
            <h4 class="mt-3 text-sm font-bold text-black">Pantai Kartini, Jepara</h4>
            <div class="mt-3 flex items-center justify-between text-xs border-t border-primary-2/30 pt-2">
              <span class="text-grey-deep">Perolehan</span>
              <span class="font-bold text-primary-1">40% (88 suara)</span>
            </div>
          </div>

          {{-- Kategori 3: Desain Kaos --}}
          <div class="rounded-xl border-2 border-primary-1 bg-primary-3/30 p-4 shadow-xs">
            <span class="rounded-sm bg-primary-1 px-2 py-0.5 text-[10px] font-bold text-white uppercase tracking-wider">
              Kategori 3: Kaos
            </span>
            <h4 class="mt-3 text-sm font-bold text-black">Navy Polo Gathering</h4>
            <div class="mt-3 flex items-center justify-between text-xs border-t border-primary-2/30 pt-2">
              <span class="text-grey-deep">Perolehan</span>
              <span class="font-bold text-primary-1">57% (125 suara)</span>
            </div>
          </div>
        </div>
      </div>

      {{-- Alert Pemberitahuan Konsekuensi --}}
      <div class="mt-6 flex items-start gap-3 rounded-xl bg-amber-50 p-4 border border-amber-200">
        <x-solar-danger-circle-bold class="h-5 w-5 shrink-0 text-amber-600 mt-0.5" />
        <div class="text-xs text-amber-900">
          <p class="font-bold">Konfirmasi Publikasi Pengumuman</p>
          <p class="mt-0.5 text-[11px] leading-relaxed text-amber-800">
            Setelah diumumkan, sesi polling akan otomatis ditutup dan dikunci. Seluruh sivitas akan menerima notifikasi pengumuman hasil resmi, dan rekaman kegiatan akan dipindahkan ke arsip.
          </p>
        </div>
      </div>

      {{-- Tombol Aksi Batal & Konfirmasi --}}
      <div class="mt-7 flex items-center justify-end gap-3 pt-3 border-t border-grey-1">
        <button type="button" @click="modalAnnounce = false"
                class="rounded-sm border border-grey-1 bg-white px-5 py-2.5 text-xs font-semibold text-grey-deep hover:bg-gray-50 transition-all cursor-pointer">
          Batal
        </button>

        <button type="button"
                @click="isAnnounced = true; modalAnnounce = false; alert('Hasil resmi voting berhasil diumumkan ke seluruh sivitas DTEDI!');"
                class="inline-flex items-center gap-2 rounded-sm bg-gradient-to-r from-primary-1 to-secondary-1 px-6 py-2.5 text-xs font-semibold text-white shadow-primary-glow hover:opacity-95 transition-all cursor-pointer">
          <x-solar-check-circle-bold class="h-4 w-4" />
          <span>Konfirmasi & Umumkan Sekarang</span>
        </button>
      </div>
    </div>
  </x-modal>

</div>
@endsection
