@props([
  'paginator' => null,           // Instance of LengthAwarePaginator (jika pakai Laravel backend)
  'perPage' => 10,               // Nilai aktif saat ini
  'options' => [10, 25, 50, 100],// Opsi jumlah data: 10, 25, 50, 100
  'paramName' => 'per_page',     // Nama query string parameter
  'autoSubmit' => true,          // Otomatis submit form bila berubah
  'disabled' => false,           // Disabled jika tidak ada data aktif
  // Mode Alpine / Client-side (opsional jika bukan Laravel paginator)
  'alpineModel' => null,         // e.g. 'perPage'
  'alpineChange' => null,        // e.g. 'changePerPage()'
  'alpinePage' => null,          // e.g. 'page'
  'alpineTotalPages' => null,    // e.g. 'totalPages'
  'alpinePrev' => null,          // e.g. 'page--'
  'alpineNext' => null,          // e.g. 'page++'
])

@php
  $isPaginator = $paginator instanceof \Illuminate\Pagination\LengthAwarePaginator;
  $currentPage = $isPaginator ? $paginator->currentPage() : 1;
  $lastPage    = $isPaginator ? $paginator->lastPage() : 1;
  $totalItems  = $isPaginator ? $paginator->total() : 0;
  $activeVal   = $isPaginator ? $paginator->perPage() : $perPage;

  // Jika paginator kosong (total = 0), otomatis disabled
  $isDisabled = $disabled || ($isPaginator && $totalItems === 0);

  // Buat array opsi 10, 25, 50, 100
  $opts = [];
  foreach ($options as $opt) {
    $opts[$opt] = (string)$opt;
  }

  $btnBase = 'flex h-10 w-10 items-center justify-center rounded-xl bg-white text-primary-1 shadow-menu transition-all';
  $pillBase = 'flex h-10 items-center rounded-xl border border-primary-1 bg-white px-4 text-sm font-semibold text-primary-1 shadow-xs';
@endphp

<div {{ $attributes->merge(['class' => 'w-full flex items-center justify-between gap-3']) }}>

  {{-- Sisi Kiri: Tampilkan + Dropdown [10, 25, 50, 100] (Tanpa kata "Pilih...") --}}
  <div class="flex items-center gap-2">
    @if ($alpineModel)
      {{-- Mode Alpine.js (Client-side) --}}
      <span class="flex h-10 items-center justify-center rounded-xl bg-white px-5 text-sm font-semibold text-primary-1 shadow-menu">
        Tampilkan
      </span>
      <x-dropdown-select :x-model.number="$alpineModel"
                         :x-on:change="$alpineChange"
                         :value="$activeVal"
                         :options="$opts"
                         :disabled="$isDisabled"
                         variant="primary" />
    @else
      {{-- Mode Laravel Form Submit (Server-side) --}}
      <form method="GET" class="flex items-center gap-2">
        @foreach (request()->except([$paramName, 'page', 'polling_page']) as $key => $value)
          @if (is_string($value))
            <input type="hidden" name="{{ $key }}" value="{{ $value }}">
          @endif
        @endforeach

        <span class="flex h-10 items-center justify-center rounded-xl bg-white px-5 text-sm font-semibold text-primary-1 shadow-menu">
          Tampilkan
        </span>
        <x-dropdown-select :name="$paramName"
                           :value="$activeVal"
                           :options="$opts"
                           :autoSubmit="$autoSubmit"
                           :disabled="$isDisabled"
                           variant="primary" />
      </form>
    @endif
  </div>

  {{-- Sisi Kanan: < | 1/1 atau 0/0 | > --}}
  <div class="flex items-center gap-2">
    @if ($alpineModel && $alpineTotalPages)
      {{-- Mode Alpine.js --}}
      <button type="button"
              aria-label="Sebelumnya"
              class="{{ $btnBase }} hover:bg-primary-3/30 disabled:opacity-40 disabled:pointer-events-none cursor-pointer"
              :disabled="{{ $isDisabled ? 'true' : ($alpinePage . ' <= 1') }}"
              @click="{{ $alpinePrev }}">
        <x-solar-alt-arrow-left-linear class="h-4 w-4" />
      </button>

      <span class="{{ $pillBase }} {{ $isDisabled ? 'opacity-40' : '' }}"
            @if ($isDisabled)
              x-text="'0/0'"
            @else
              x-text="{{ $alpinePage }} + '/' + {{ $alpineTotalPages }}"
            @endif>
        1/1
      </span>

      <button type="button"
              aria-label="Selanjutnya"
              class="{{ $btnBase }} hover:bg-primary-3/30 disabled:opacity-40 disabled:pointer-events-none cursor-pointer"
              :disabled="{{ $isDisabled ? 'true' : ($alpinePage . ' >= ' . $alpineTotalPages) }}"
              @click="{{ $alpineNext }}">
        <x-solar-alt-arrow-right-linear class="h-4 w-4" />
      </button>
    @else
      {{-- Mode Laravel LengthAwarePaginator --}}
      @if ($isDisabled)
        {{-- Disabled State Total: Tidak ada yang aktif --}}
        <span class="{{ $btnBase }} opacity-40 pointer-events-none" aria-hidden="true">
          <x-solar-alt-arrow-left-linear class="h-4 w-4" />
        </span>
        <span class="{{ $pillBase }} opacity-40">
          0/0
        </span>
        <span class="{{ $btnBase }} opacity-40 pointer-events-none" aria-hidden="true">
          <x-solar-alt-arrow-right-linear class="h-4 w-4" />
        </span>
      @else
        {{-- Normal Paginator --}}
        <a href="{{ $isPaginator ? ($paginator->previousPageUrl() ?? '#') : '#' }}"
           aria-label="Sebelumnya"
           class="{{ $btnBase }} {{ ($isPaginator && $paginator->onFirstPage()) ? 'opacity-40 pointer-events-none' : 'hover:bg-primary-3/30 cursor-pointer' }}">
          <x-solar-alt-arrow-left-linear class="h-4 w-4" />
        </a>

        <span class="{{ $pillBase }}">
          {{ $currentPage }}/{{ $lastPage }}
        </span>

        <a href="{{ $isPaginator ? ($paginator->nextPageUrl() ?? '#') : '#' }}"
           aria-label="Selanjutnya"
           class="{{ $btnBase }} {{ ($isPaginator && $paginator->hasMorePages()) ? 'hover:bg-primary-3/30 cursor-pointer' : 'opacity-40 pointer-events-none' }}">
          <x-solar-alt-arrow-right-linear class="h-4 w-4" />
        </a>
      @endif
    @endif
  </div>

</div>
