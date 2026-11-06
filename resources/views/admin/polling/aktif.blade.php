@extends('layouts.admin')

@section('title', 'Polling Aktif')

@section('content')
@php
  $badge = [
    'peserta' => 'bg-success-soft text-black',
    'wisata'  => 'bg-[#4527E8] text-white',
    'kaos'    => 'bg-warning text-black',
  ];
  $icons = [
    'peserta' => '<circle cx="12" cy="12" r="9"/><circle cx="12" cy="10" r="3"/><path d="M6.5 18c1.5-2.5 3.5-3.5 5.5-3.5s4 1 5.5 3.5"/>',
    'wisata'  => '<path d="M12 21s-6-5.5-6-10a6 6 0 1 1 12 0c0 4.5-6 10-6 10z"/><circle cx="12" cy="11" r="2"/>',
    'kaos'    => '<path d="M8 3 3 6l2 4 3-1v12h8V9l3 1 2-4-5-3a4 4 0 0 1-8 0z"/>',
  ];
  $th = 'px-3 py-2.5 text-center text-xs font-semibold border-l border-white/30';
  $td = 'px-3 py-3 text-center text-xs';
@endphp

<x-card class="p-6">

  {{-- Judul halaman --}}
  <h1 class="border-b-4 border-grey-1 pb-3 text-lg font-bold text-black">Polling Aktif</h1>

  <h2 class="mt-6 text-3xl font-bold text-black">{{ $title }}</h2>
  <p class="mt-2 text-sm text-black">{{ $subtitle }}</p>

  {{-- Kartu statistik (sama seperti dashboard) --}}
  <div class="mt-5 grid grid-cols-1 gap-4 md:grid-cols-2">
    <x-card variant="elevated" class="p-6">
      <p class="flex items-center gap-2 text-xs font-bold uppercase text-primary-1">
        <svg class="size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 2"/></svg>
        Waktu Tersisa
      </p>
      <x-countdown class="mt-5 justify-center"
        :days="$countdown['days']" :hours="$countdown['hours']" :minutes="$countdown['minutes']" />
      <p class="mt-6 text-xs text-black">
        Berakhir pada {{ $deadline[0] }}
        <span class="ml-1 font-semibold text-primary-1">{{ $deadline[1] }}</span>
      </p>
    </x-card>

    <x-card variant="elevated" class="p-6">
      <p class="flex items-center gap-2 text-xs font-bold text-primary-1">
        <svg class="size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="9" cy="8" r="3.5"/><path d="M3 20c0-3.5 2.7-6 6-6 1.2 0 2.3.3 3.2.9M15 17l2 2 4-4"/></svg>
        Total Vote saat ini
      </p>
      <p class="mt-3 text-4xl font-semibold text-black">{{ $totalVote['percent'] }}%</p>
      <div class="mt-4 h-1 w-full overflow-hidden rounded-full bg-grey-1">
        <div class="h-full rounded-full bg-primary-1" style="width: {{ $totalVote['percent'] }}%"></div>
      </div>
      <p class="mt-5 flex justify-between text-xs text-black">
        <span><span class="font-bold">{{ $totalVote['received'] }}</span> votes diterima</span>
        <span class="text-primary-1">{{ $totalVote['pending'] }} peserta belum memilih</span>
      </p>
    </x-card>
  </div>

  {{-- Rekapitulasi suara --}}
  <div class="mt-8 flex flex-wrap items-center justify-between gap-3 border-b-4 border-grey-1 pb-3">
    <h3 class="text-xl font-bold text-black">Rekapitulasi Suara</h3>
    <div class="flex flex-wrap gap-3">
      @foreach (['semua' => 'Semua', 'peserta' => 'Peserta', 'wisata' => 'Tempat Wisata', 'kaos' => 'Desain Kaos'] as $key => $label)
        <a href="{{ route('admin.polling.aktif', ['kategori' => $key]) }}"
           class="rounded-button px-6 py-2 text-sm
                  {{ $kategori === $key ? 'bg-primary-1 text-white' : 'bg-white text-black shadow-type-3' }}">
          {{ $label }}
        </a>
      @endforeach
    </div>
  </div>

  <div class="mt-4 overflow-x-auto rounded-md border border-primary-1 p-2">
    <table class="w-full min-w-[900px] border-separate border-spacing-y-0 text-xs">
      <thead>
        <tr class="bg-primary-1 text-white">
          <th class="w-14 rounded-l-sm px-3 py-2.5 text-xs font-semibold">NO.</th>
          <th class="{{ $th }}">PILIHAN</th>
          <th class="{{ $th }}">
            KATEGORI
            <span class="ml-2 rounded-full bg-white/30 px-3 py-0.5">{{ strtoupper($kategori) }}</span>
          </th>
          <th class="{{ $th }}">DOSEN</th>
          <th class="{{ $th }}">TENDIK</th>
          <th class="{{ $th }}">TOTAL SUARA</th>
          <th class="{{ $th }} rounded-r-sm">PROPORSI</th>
        </tr>
      </thead>

      @foreach ($categories as $key => $cat)
        @continue($kategori !== 'semua' && $kategori !== $key)
        @php
          $rows = $kategori === 'semua' ? $cat['options']->take(3) : $cat['options'];
        @endphp
        <tbody>
          <tr>
            <td colspan="7" class="pt-4">
              <div class="flex items-center justify-center gap-2 rounded-sm border border-primary-1 py-2.5 text-xs font-bold uppercase text-primary-1">
                <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">{!! $icons[$key] !!}</svg>
                {{ $cat['label'] }}
              </div>
            </td>
          </tr>

          @foreach ($rows as $opt)
            <tr>
              <td class="px-3 py-2">
                <span class="mx-auto flex size-9 items-center justify-center rounded-sm border border-primary-1 text-xs">
                  {{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}
                </span>
              </td>
              <td class="{{ $td }}">{{ $opt['name'] }}</td>
              <td class="{{ $td }}">
                <span class="inline-block min-w-[130px] rounded-full px-3 py-0.5 font-medium uppercase {{ $badge[$key] }}">
                  {{ $cat['label'] }}
                </span>
              </td>
              <td class="{{ $td }}">{{ $opt['dosen_pct'] }}%</td>
              <td class="{{ $td }}">{{ $opt['tendik_pct'] }}%</td>
              <td class="{{ $td }} font-bold">{{ $opt['total'] }} vote</td>
              <td class="{{ $td }}">{{ $opt['proporsi'] }}%</td>
            </tr>
          @endforeach

          @if ($kategori === 'semua' && $cat['options']->count() > 3)
            <tr>
              <td colspan="7" class="px-1 pt-3 text-right">
                <a href="{{ route('admin.polling.aktif', ['kategori' => $key]) }}"
                   class="text-xs text-primary-1 underline">Lihat semua</a>
              </td>
            </tr>
          @endif
        </tbody>
      @endforeach
    </table>
  </div>
</x-card>

{{-- Aktivitas terbaru --}}
<x-card class="mt-4 p-6">
  <h3 class="text-xl font-bold text-black">Aktivitas Terbaru</h3>

  <x-table-pagination :paginator="$activities" class="mt-4" />

  <div class="mt-4 overflow-x-auto rounded-md border border-primary-1 p-2">
    <table class="w-full min-w-[900px] border-separate border-spacing-0 text-xs">
      <thead>
        <tr class="bg-primary-1 text-white">
          <th class="w-14 rounded-l-sm px-3 py-2.5 text-xs font-semibold">NO.</th>
          @foreach (['DOSEN', 'EMAIL', 'PESERTA', 'TEMPAT WISATA', 'KAOS', 'AKSI'] as $h)
            <th class="{{ $th }} {{ $loop->last ? 'rounded-r-sm' : '' }}">{{ $h }}</th>
          @endforeach
        </tr>
      </thead>
      <tbody>
        @foreach ($activities as $a)
          <tr>
            <td class="px-3 py-2">
              <span class="mx-auto flex size-9 items-center justify-center rounded-sm border border-primary-1">
                {{ str_pad($activities->firstItem() + $loop->index, 2, '0', STR_PAD_LEFT) }}
              </span>
            </td>
            <td class="px-3 py-3">{{ $a['name'] }}</td>
            <td class="px-3 py-3">{{ $a['email'] }}</td>
            <td class="{{ $td }}">{{ $a['peserta'] }}</td>
            <td class="{{ $td }}">{{ $a['wisata'] }}</td>
            <td class="{{ $td }}">{{ $a['kaos'] }}</td>
            <td class="{{ $td }}">
              <span class="inline-flex items-center gap-1">
                {{ $a['status'] }}
                <svg class="size-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><path d="m6 9 6 6 6-6"/></svg>
              </span>
            </td>
          </tr>
        @endforeach
      </tbody>
    </table>
  </div>
</x-card>
@endsection