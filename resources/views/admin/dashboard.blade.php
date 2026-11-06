@extends('layouts.admin')

@section('title', 'Dashboard')

@section('content')
<div class="space-y-4">

  {{-- Baris atas --}}
  <div class="grid grid-cols-1 gap-4 xl:grid-cols-[1fr_320px]">

    <x-card class="p-6">
      <x-section-header title="Dashboard"
        subtitle="Pantau dan kelola seluruh proses pemungutan suara DTEDI 2026."
        :divider="false" />

      <div class="mt-5 grid grid-cols-1 gap-4 md:grid-cols-2">
        {{-- Waktu tersisa --}}
        <x-card variant="elevated" class="p-6">
          <p class="text-xs font-bold uppercase text-primary-1">Waktu Tersisa</p>
          <x-countdown class="mt-5"
            :days="$countdown['days']" :hours="$countdown['hours']" :minutes="$countdown['minutes']" />
          <p class="mt-6 text-xs text-black">
            Berakhir pada {{ explode(',', $deadline, 2)[0] }}
            <span class="font-semibold text-primary-1">{{ trim(explode(',', $deadline, 2)[1]) }}</span>
          </p>
        </x-card>

        {{-- Total vote --}}
        <x-card variant="elevated" class="p-6">
          <p class="text-xs font-bold text-primary-1">Total Vote saat ini</p>
          <p class="mt-3 text-4xl font-semibold text-black">{{ $totalVote['percent'] }}%</p>
          <div class="mt-4 h-1 w-full overflow-hidden rounded-full bg-grey-1">
            <div class="h-full rounded-full bg-primary-1" style="width: {{ $totalVote['percent'] }}%"></div>
          </div>
          <p class="mt-5 text-xs text-black">
            <span class="font-bold">{{ $totalVote['received'] }}</span> votes diterima
            <span class="ml-3 text-primary-1">{{ $totalVote['pending'] }} peserta belum memilih</span>
          </p>
        </x-card>
      </div>
    </x-card>

    {{-- Aktivitas terbaru --}}
    <x-card class="p-4">
      <x-section-header title="Aktivitas Terbaru" class="!pb-2 [&_h2]:!text-sm" />

      <div class="mt-4 space-y-3">
        @foreach ($activities as $a)
          <div class="rounded-sm border border-grey-1 p-3">
            <div class="flex items-start gap-2">
              <x-avatar :name="$a['name']" />
              <div class="min-w-0 flex-1">
                <div class="flex items-center justify-between gap-2">
                  <p class="truncate text-xs font-semibold">{{ $a['name'] }}</p>
                  <span class="shrink-0 text-[10px] text-grey-2">{{ $a['time'] }}</span>
                </div>
                <p class="mt-1 flex items-center gap-1.5 text-xs text-grey-2">
                  <span class="size-1.5 rounded-full bg-success"></span>{{ $a['text'] }}
                </p>
              </div>
            </div>
          </div>
        @endforeach
      </div>

      <a href="#"
         class="mt-4 flex items-center justify-center gap-2 rounded-button border border-primary-1 py-2.5 text-xs font-medium text-primary-1 hover:bg-primary-3">
        Lihat Semua Aktivitas <span aria-hidden="true">→</span>
      </a>
    </x-card>
  </div>

  {{-- Ringkasan hasil --}}
  <x-card class="p-6">
    <x-section-header title="Ringkasan Hasil Pemungutan"
      subtitle="Pantau perolehan suara dari setiap kategori pemungutan yang sedang berlangsung." />

    <div class="mt-4 grid grid-cols-1 gap-4 lg:grid-cols-3">
      @foreach ($categories as $cat)
        @php $max = collect($cat['options'])->max('percent'); @endphp
        <div class="space-y-3 rounded-sm bg-primary-3 p-4">
          <h3 class="text-sm font-semibold text-secondary-3">{{ $cat['title'] }}</h3>
          @foreach ($cat['options'] as $opt)
            <x-option-card :name="$opt['name']" :percent="$opt['percent']"
                           :leading="$opt['percent'] === $max" />
          @endforeach
        </div>
      @endforeach
    </div>

    <a href="#"
       class="mt-4 flex w-full items-center justify-center rounded-button bg-primary-1 py-3 text-sm font-semibold text-white shadow-type-1 hover:bg-primary-4">
      Lihat Analisis Lengkap
    </a>
  </x-card>

</div>
@endsection