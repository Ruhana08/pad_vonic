@props(['paginator'])

@php
  $btn = 'flex h-9 items-center rounded-button bg-white px-5 text-xs font-semibold text-primary-1 shadow-type-3';
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

    <span class="{{ $btn }}">Tampilkan</span>
    <select name="per_page" onchange="this.form.submit()"
            class="h-9 rounded-sm border border-primary-1 bg-white px-3 text-xs font-semibold text-primary-1 focus:outline-none">
      @foreach ([10, 25, 50] as $n)
        <option value="{{ $n }}" @selected($paginator->perPage() === $n)>{{ $n }}</option>
      @endforeach
    </select>
  </form>

  {{-- Sebelumnya  1/6  Selanjutnya --}}
  <div class="flex items-center gap-2">
    <a href="{{ $paginator->previousPageUrl() ?? '#' }}"
       class="{{ $btn }} {{ $paginator->onFirstPage() ? $off : '' }}">Sebelumnya</a>

    <span class="flex h-9 items-center rounded-sm border border-primary-1 bg-white px-3 text-xs font-semibold text-primary-1">
      {{ $paginator->currentPage() }}/{{ $paginator->lastPage() }}
    </span>

    <a href="{{ $paginator->nextPageUrl() ?? '#' }}"
       class="{{ $btn }} {{ $paginator->hasMorePages() ? '' : $off }}">Selanjutnya</a>
  </div>
</div>