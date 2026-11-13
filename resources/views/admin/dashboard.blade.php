@extends('layouts.admin')

@section('title', 'Dashboard')

@section('content')
<div class="space-y-4">

  {{-- Baris Atas (Top Row) --}}
  <div class="grid grid-cols-1 gap-4 xl:grid-cols-[1fr_320px]">

    {{-- Ringkasan Pemungutan Suara --}}
    <x-card class="flex flex-col justify-between p-6 shadow-sm">
      <div class="space-y-1">
        <h1 class="text-2xl font-bold text-black">Ringkasan Pemungutan Suara</h1>
        <p class="text-sm text-primary-1">Pantau partisipasi, suara masuk, dan peserta yang belum memilih dalam satu tampilan.</p>
      </div>

      <div class="mt-6 grid grid-cols-1 gap-4 sm:grid-cols-3">
        <x-stat-card 
          label="Partisipasi" 
          :value="$totalVote['percent'] . '%'" 
          :sub="$totalVote['received'] . ' suara diterima'" 
        />
        <x-stat-card 
          label="Suara Masuk" 
          :value="$totalVote['received']" 
          :sub="'Dari ' . ($totalVote['received'] + $totalVote['pending']) . ' peserta'" 
        />
        <x-stat-card 
          label="Belum Memilih" 
          :value="$totalVote['pending']" 
          :sub="round(($totalVote['pending'] / max(1, $totalVote['received'] + $totalVote['pending'])) * 100) . '% peserta belum memilih'" 
          variant="danger" 
        />
      </div>
    </x-card>

    {{-- Waktu Tersisa --}}
    <x-card class="flex flex-col justify-between p-6 shadow-sm">
      <div class="flex items-center gap-2.5">
        <svg class="h-5 w-5 text-primary-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <circle cx="12" cy="12" r="10" stroke-width="2"/>
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6l4 2"/>
        </svg>
        <h2 class="text-lg font-bold text-primary-1">Waktu Tersisa</h2>
      </div>

      <div class="my-4">
        <x-countdown :days="$countdown['days']" :hours="$countdown['hours']" :minutes="$countdown['minutes']" />
      </div>

      <div class="rounded-xl bg-gray-50/80 p-3">
        <p class="text-xs font-semibold text-primary-1">Penutupan</p>
        <p class="mt-1 text-sm font-semibold text-black">{{ $deadline }}</p>
      </div>
    </x-card>

  </div>

  {{-- Baris Bawah (Bottom Row) --}}
  <div class="grid grid-cols-1 gap-4 xl:grid-cols-[1fr_320px]">

    {{-- Hasil Sementara (Leaderboard Grid) --}}
    <x-card class="p-6 shadow-sm">
      <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
          <h2 class="text-2xl font-bold text-black">Hasil Sementara</h2>
          <p class="mt-1 text-sm text-primary-1">Prioritas utama untuk memantau pilihan teratas di setiap kategori.</p>
        </div>
        <x-button variant="gradient" :href="route('admin.polling.aktif') . '#rekapitulasi-suara'" class="px-6 py-2.5 text-sm">
          Lihat Semua
        </x-button>
      </div>

      {{-- Grid 3 Kategori Menggunakan Komponen Reusable --}}
      <div class="mt-6 grid grid-cols-1 gap-4 lg:grid-cols-3">
        @foreach ($categories as $cat)
          <x-leaderboard-card :title="$cat['title']" :options="$cat['options']" />
        @endforeach
      </div>
    </x-card>

    {{-- Aktivitas Terbaru --}}
    <x-card class="flex flex-col justify-between p-6 shadow-sm">
      <div>
        <div class="flex items-center justify-between">
          <h2 class="text-xl font-bold text-black">Aktivitas Terbaru</h2>
          <a href="{{ route('admin.polling.aktif') }}#aktivitas-terbaru" aria-label="Lihat Semua Aktivitas" class="text-primary-1 hover:text-primary-4 transition-colors">
            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
            </svg>
          </a>
        </div>

        <div class="mt-4 space-y-3">
          @foreach ($activities as $a)
            <div class="flex items-center gap-3 rounded-xl bg-[#fafafa] p-3 shadow-xs">
              <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-primary-3 text-xs font-bold text-primary-1">
                <x-avatar :name="$a['name']" size="sm" />
              </span>
              <div class="min-w-0 flex-1">
                <div class="flex items-center justify-between gap-1">
                  <p class="truncate text-sm font-semibold text-black">{{ $a['name'] }}</p>
                </div>
                <p class="text-xs text-grey-2">{{ $a['time'] }}</p>
                <div class="mt-1 flex items-center gap-1.5 text-xs text-grey-2">
                  <span class="h-1.5 w-1.5 rounded-full bg-emerald-500"></span>
                  <span>{{ $a['text'] }}</span>
                </div>
              </div>
            </div>
          @endforeach
        </div>
      </div>

    </x-card>

  </div>

</div>
@endsection
