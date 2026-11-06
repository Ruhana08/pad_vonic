@extends('layouts.admin')

@section('title', 'Akun Pengguna')

@section('content')
@php
$field = 'h-9 w-full rounded-sm border border-grey-1 bg-white px-3 text-xs text-black placeholder:text-grey-2 focus:border-primary-1 focus:outline-none';
@endphp

<div class="space-y-4">

  {{-- Pencarian cepat --}}
  <x-card class="p-6">
    <h2 class="text-base font-bold text-secondary-3">Pencarian Cepat</h2>

    <form method="GET" action="{{ route('admin.akun.index') }}"
      class="mt-3 grid grid-cols-1 items-end gap-3 md:grid-cols-[1fr_1fr_1fr_auto_auto]">
      <input type="hidden" name="tab" value="{{ $tab }}">

      <label class="block">
        <span class="text-[10px] font-semibold uppercase text-black">Nama</span>
        <input type="text" name="nama" value="{{ request('nama') }}" placeholder="Cari nama civitas..." class="mt-1 {{ $field }}">
      </label>

      <label class="block">
        <span class="text-[10px] font-semibold uppercase text-black">Unit Kerja</span>
        <select name="unit" class="mt-1 {{ $field }}">
          <option value="">Pilih unit kerja...</option>
          @foreach ($units as $u)
          <option value="{{ $u }}" @selected(request('unit')===$u)>{{ $u }}</option>
          @endforeach
        </select>
      </label>

      <label class="block">
        <span class="text-[10px] font-semibold text-black">Status</span>
        <select name="status" class="mt-1 {{ $field }}">
          <option value="">Semua status</option>
          <option value="Aktif" @selected(request('status')==='Aktif' )>Aktif</option>
          <option value="Nonaktif" @selected(request('status')==='Nonaktif' )>Nonaktif</option>
        </select>
      </label>

      <a href="{{ route('admin.akun.index', ['tab' => $tab]) }}"
        class="flex h-9 items-center rounded-button border border-grey-1 px-5 text-xs font-semibold text-grey-2 hover:bg-gray-50">
        Reset Filter
      </a>

      <button type="submit"
        class="flex h-9 items-center justify-center gap-2 rounded-button bg-primary-1 px-5 text-xs font-semibold text-white hover:bg-primary-4">
        Cari Data
        <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round">
          <circle cx="11" cy="11" r="7" />
          <path d="m20 20-3.5-3.5" />
        </svg>
      </button>
    </form>
  </x-card>

  {{-- Daftar akun --}}
  <x-card class="p-6">
    <div class="flex items-start justify-between gap-4">
      <h2 class="text-base font-bold text-secondary-3">Daftar Akun Sivitas DTEDI</h2>
      <a href="#"
        class="flex items-center gap-2 rounded-button bg-primary-1 px-4 py-2.5 text-xs font-semibold text-white shadow-type-1 hover:bg-primary-4">
        <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round">
          <circle cx="12" cy="12" r="9" />
          <path d="M12 8v8M8 12h8" />
        </svg>
        Tambah Akun Baru
      </a>
    </div>

    {{-- Tab --}}
    <div class="mt-4 flex gap-3">
      @foreach (['dosen' => 'Dosen', 'tendik' => 'Tenaga Didik'] as $key => $label)
      <a href="{{ route('admin.akun.index', ['tab' => $key]) }}"
        class="rounded-button px-8 py-2 text-xs font-semibold text-primary-1
                  {{ $tab === $key ? 'border border-primary-1 bg-white' : 'bg-white shadow-type-3' }}">
        {{ $label }}
      </a>
      @endforeach
    </div>

    {{-- Tabel --}}
    <div class="mt-4 overflow-x-auto rounded-md border border-primary-3 p-2">
      <table class="w-full min-w-[900px] border-separate border-spacing-0 text-xs">
        <thead>
          <tr class="bg-primary-1 text-white">
            @foreach (['NO.', 'ID', 'NAMA', 'EMAIL', 'UNIT KERJA', 'NO. TELP', 'STATUS', 'AKSI'] as $i => $h)
            <th class="px-3 py-2.5 text-left font-semibold
                         {{ $i === 0 ? 'rounded-l-sm' : 'border-l border-white/30' }}
                         {{ $loop->last ? 'rounded-r-sm text-center' : '' }}
                         {{ in_array($h, ['UNIT KERJA', 'STATUS']) ? 'text-center' : '' }}">{{ $h }}</th>
            @endforeach
          </tr>
        </thead>
        <tbody>
          @forelse ($accounts as $a)
          <tr>
            <td class="border-b border-primary-3 px-3 py-3">
              <span class="flex size-7 items-center justify-center rounded-sm border border-primary-1 text-primary-1">
                {{ str_pad($accounts->firstItem() + $loop->index, 2, '0', STR_PAD_LEFT) }} </span>
            </td>
            <td class="border-b border-primary-3 px-3 py-3">{{ $a['id'] }}</td>
            <td class="border-b border-primary-3 px-3 py-3">{{ $a['name'] }}</td>
            <td class="border-b border-primary-3 px-3 py-3 text-[10px] uppercase">{{ $a['email'] }}</td>
            <td class="border-b border-primary-3 px-3 py-3 text-center">{{ $a['unit'] }}</td>
            <td class="border-b border-primary-3 px-3 py-3">{{ $a['phone'] }}</td>
            <td class="border-b border-primary-3 px-3 py-3 text-center">
              <select class="rounded-full border-0 py-1 pl-3 pr-6 text-[11px] font-medium text-secondary-2
                               {{ $a['status'] === 'Aktif' ? 'bg-primary-3' : 'bg-danger-soft' }}">
                <option @selected($a['status']==='Aktif' )>Aktif</option>
                <option @selected($a['status']==='Nonaktif' )>Nonaktif</option>
              </select>
            </td>
            <td class="border-b border-primary-3 px-3 py-3">
              <div class="flex items-center justify-center gap-3 text-secondary-2">
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
          @empty
          <tr>
            <td colspan="8" class="px-3 py-10 text-center text-grey-2">Tidak ada akun yang cocok dengan pencarian.</td>
          </tr>
          @endforelse
        </tbody>
      </table>
    </div>
    <x-table-pagination :paginator="$accounts" class="mt-4" />
  </x-card>

</div>
@endsection