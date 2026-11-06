@extends('layouts.focus')

@section('title', 'Profil Akun')
@section('page_title', 'Profil akun')
@section('max_width', 'max-w-[1400px]')

@section('header_actions')
  <a href="{{ url()->previous() === url()->current() ? route('admin.dashboard') : url()->previous() }}"
     class="flex items-center gap-2 px-3 py-2 text-sm font-semibold text-primary-1 hover:underline">
    <svg class="size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 12H5M11 6l-6 6 6 6"/></svg>
    Kembali
  </a>
  <a href="#"
     class="flex items-center gap-2 rounded-button border border-grey-1 bg-white px-5 py-2.5 text-sm font-semibold text-primary-1 hover:bg-primary-3">
    <svg class="size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="9" cy="8" r="3.5"/><path d="M2.5 20c0-3.5 2.9-6 6.5-6 1.2 0 2.3.3 3.2.8M18 8v6M15 11h6"/></svg>
    Tambahkan akun
  </a>
@endsection

@section('content')
@php
  $th = 'px-3 py-3 text-center text-sm font-semibold border-l border-white/30';
  $td = 'px-3 py-3 text-sm';
  $iconBox = 'flex size-12 shrink-0 items-center justify-center rounded-md bg-primary-3 text-secondary-2';
  $initials = collect(explode(' ', $profile['name']))->take(2)->map(fn ($w) => mb_substr($w, 0, 1))->join('');
@endphp

<p class="text-sm font-semibold uppercase tracking-wider text-primary-1">Akun Saya</p>
<h2 class="mt-2 text-5xl font-bold text-secondary-3">Identitas</h2>
<p class="mt-3 text-base text-grey-2">Kelola informasi profil dan pantau aktivitas akun Anda.</p>

<div class="mt-8 grid grid-cols-1 items-start gap-6 lg:grid-cols-[360px_1fr]">

  {{-- ===== Kolom kiri ===== --}}
  <div class="space-y-5">

    <x-card class="p-6">
      <div class="flex items-center justify-between">
        <h3 class="text-xl font-bold text-secondary-3">Profil saya</h3>
        <svg class="size-6 text-primary-1" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="8" r="4"/><path d="M4 21c0-4 3.6-7 8-7s8 3 8 7"/></svg>
      </div>

      <div class="mt-5 aspect-[4/3] overflow-hidden rounded-md bg-primary-3">
        @if ($profile['photo'])
          <img src="{{ $profile['photo'] }}" alt="Foto profil" class="h-full w-full object-cover">
        @else
          <div class="flex h-full w-full items-center justify-center text-6xl font-bold text-primary-1">{{ $initials }}</div>
        @endif
      </div>

      <dl class="mt-6 space-y-5">
        <div class="flex items-center gap-4">
          <span class="{{ $iconBox }}">
            <svg class="size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="8" r="4"/><path d="M4 21c0-4 3.6-7 8-7s8 3 8 7"/></svg>
          </span>
          <div class="min-w-0">
            <dt class="text-sm text-grey-2">Nama</dt>
            <dd class="truncate text-base font-semibold text-black">{{ $profile['name'] }}</dd>
          </div>
        </div>

        <div class="flex items-center gap-4">
          <span class="{{ $iconBox }}">
            <svg class="size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="5" width="18" height="14" rx="2"/><path d="m3 7 9 6 9-6"/></svg>
          </span>
          <div class="min-w-0">
            <dt class="text-sm text-grey-2">Email</dt>
            <dd class="truncate text-base font-semibold text-black">{{ $profile['email'] }}</dd>
          </div>
        </div>

        <div class="flex items-center gap-4">
          <span class="{{ $iconBox }}">
            <svg class="size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="5" y="11" width="14" height="10" rx="2"/><path d="M8 11V8a4 4 0 0 1 8 0v3"/></svg>
          </span>
          <div>
            <dt class="text-sm text-grey-2">Password</dt>
            <dd class="text-base font-semibold tracking-widest text-black">****</dd>
          </div>
        </div>
      </dl>

      <a href="#"
         class="mt-6 flex items-center justify-center gap-2 rounded-button bg-primary-1 py-3.5 text-base font-semibold text-white hover:bg-primary-4">
        <svg class="size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M11 4H5a2 2 0 0 0-2 2v13a2 2 0 0 0 2 2h13a2 2 0 0 0 2-2v-6"/><path d="M18.5 2.5a2.1 2.1 0 0 1 3 3L12 15l-4 1 1-4z"/></svg>
        Edit Profile
      </a>
    </x-card>

    <x-card class="flex items-center justify-between gap-3 p-5">
      <div>
        <h3 class="text-base font-bold text-black">Sesi akun</h3>
        <p class="mt-1 text-sm text-grey-2">Keluar dari akun Anda.</p>
      </div>
      {{-- TODO: arahkan ke route logout (POST) saat autentikasi sudah ada --}}
      <form method="POST" action="#">
        @csrf
        <button type="submit"
                class="flex items-center gap-2 rounded-button bg-danger-soft/50 px-5 py-3 text-base font-semibold text-danger hover:bg-danger-soft">
          <svg class="size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4M16 17l5-5-5-5M21 12H9"/></svg>
          Log Out
        </button>
      </form>
    </x-card>
  </div>

  {{-- ===== Log aktivitas ===== --}}
  <x-card class="p-6">
    <div class="flex items-start justify-between gap-4">
      <div>
        <h3 class="text-2xl font-bold text-secondary-3">Log Aktivitas</h3>
        <p class="mt-1 text-sm text-grey-2">Riwayat aktivitas pada akun Anda.</p>
      </div>
      <span class="flex size-14 items-center justify-center rounded-md bg-primary-3 text-primary-1">
        <svg class="size-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 12a9 9 0 1 0 3-6.7L3 8"/><path d="M3 3v5h5M12 7v5l3 2"/></svg>
      </span>
    </div>

    <div class="mt-5 overflow-x-auto">
      <table class="w-full min-w-[820px] border-separate border-spacing-0">
        <thead>
          <tr class="bg-primary-1 text-white">
            <th class="w-16 rounded-l-md px-3 py-3 text-sm font-semibold">NO.</th>
            <th class="{{ $th }}">ID</th>
            <th class="{{ $th }}">DESKRIPSI</th>
            <th class="{{ $th }}">ADMIN</th>
            <th class="{{ $th }}">WAKTU</th>
            <th class="{{ $th }} rounded-r-md">STATUS</th>
          </tr>
        </thead>
        <tbody>
          @foreach ($logs as $log)
            <tr>
              <td class="px-3 py-2">
                <span class="mx-auto flex size-12 items-center justify-center rounded-md border border-primary-1 text-sm">
                  {{ str_pad($logs->firstItem() + $loop->index, 2, '0', STR_PAD_LEFT) }}
                </span>
              </td>
              <td class="{{ $td }}">{{ $log['id'] }}</td>
              <td class="{{ $td }}">{{ $log['desc'] }}</td>
              <td class="{{ $td }} text-xs uppercase">{{ $log['admin'] }}</td>
              <td class="{{ $td }} text-center text-primary-1">{{ $log['time'] }}</td>
              <td class="{{ $td }}">
                <span class="flex items-center justify-between gap-3 rounded-sm bg-primary-3 px-3 py-2.5">
                  {{ $log['status'] }}
                  <svg class="size-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round"><path d="m6 9 6 6 6-6"/></svg>
                </span>
              </td>
            </tr>
          @endforeach
        </tbody>
      </table>
    </div>

    {{-- Paginasi --}}
    @php
      $arrow = 'flex size-12 items-center justify-center rounded-md border border-grey-1 bg-white text-primary-1 hover:bg-primary-3';
      $off = 'pointer-events-none opacity-40';
    @endphp
    <div class="mt-5 flex items-center justify-between gap-3">
      <p class="text-sm text-grey-2">Halaman {{ $logs->currentPage() }} dari {{ $logs->lastPage() }}</p>

      <div class="flex items-center gap-2">
        <a href="{{ $logs->previousPageUrl() ?? '#' }}" aria-label="Sebelumnya"
           class="{{ $arrow }} {{ $logs->onFirstPage() ? $off : '' }}">
          <svg class="size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="m15 6-6 6 6 6"/></svg>
        </a>
        <span class="flex h-12 min-w-[76px] items-center justify-center rounded-md bg-primary-1 px-4 text-sm font-semibold text-white">
          {{ $logs->currentPage() }}/{{ $logs->lastPage() }}
        </span>
        <a href="{{ $logs->nextPageUrl() ?? '#' }}" aria-label="Selanjutnya"
           class="{{ $arrow }} {{ $logs->hasMorePages() ? '' : $off }}">
          <svg class="size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="m9 6 6 6-6 6"/></svg>
        </a>
      </div>
    </div>
  </x-card>

</div>
@endsection