@props(['paginator'])

@php
  $btn = 'flex h-9 items-center rounded-sm bg-white px-5 text-xs font-semibold text-primary-1 shadow-menu hover:bg-primary-3/30 transition-all';
  $off = 'pointer-events-none opacity-40';
@endphp

<div {{ $attributes->class('flex flex-wrap items-center justify-between gap-3') }}>

  {{-- Tampilkan N --}}
  <form method="GET" class="flex items-center gap-2">
    @foreach (request()->except(['per_page', 'page']) as $key => $value)
      @if (is_string($value))
        <input type="hidden" name="{{ $key }}" value="{{ $value }}">
      @endif
    @endforeach

    <span class="text-xs font-medium text-black">Tampilkan</span>
    <x-dropdown-select name="per_page"
                       :value="$paginator->perPage()"
                       :options="[10 => '10', 25 => '25', 50 => '50']"
                       :autoSubmit="true"
                       variant="primary" />
  </form>

  {{-- Sebelumnya  1/6  Selanjutnya --}}
  <div class="flex items-center gap-2">
    <a href="{{ $paginator->previousPageUrl() ?? '#' }}"
       class="{{ $btn }} {{ $paginator->onFirstPage() ? $off : '' }}">Sebelumnya</a>

    <span class="flex h-9 items-center rounded-sm border border-primary-1 bg-white px-3.5 text-xs font-semibold text-primary-1 shadow-xs">
      {{ $paginator->currentPage() }}/{{ $paginator->lastPage() }}
    </span>

    <a href="{{ $paginator->nextPageUrl() ?? '#' }}"
       class="{{ $btn }} {{ $paginator->hasMorePages() ? '' : $off }}">Selanjutnya</a>
  </div>
</div>
