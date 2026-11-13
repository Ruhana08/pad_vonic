@props(['paginator'])

@php
  $btn = 'flex h-8 w-8 items-center justify-center rounded-sm bg-primary-1 text-xs font-semibold text-white shadow-menu hover:bg-primary-4 transition-all';
  $pill = 'flex h-8 items-center justify-center rounded-sm bg-primary-1 px-4 text-xs font-bold text-white shadow-menu';
  $off = 'pointer-events-none opacity-40';
@endphp

<div {{ $attributes->class('flex items-center gap-1.5') }}>
  <a href="{{ $paginator->previousPageUrl() ?? '#' }}" aria-label="Sebelumnya"
     class="{{ $btn }} {{ $paginator->onFirstPage() ? $off : '' }}">
    <x-solar-alt-arrow-left-linear class="h-4 w-4" />
  </a>

  <span class="{{ $pill }}">{{ $paginator->currentPage() }}/{{ $paginator->lastPage() }}</span>

  <a href="{{ $paginator->nextPageUrl() ?? '#' }}" aria-label="Selanjutnya"
     class="{{ $btn }} {{ $paginator->hasMorePages() ? '' : $off }}">
    <x-solar-alt-arrow-right-linear class="h-4 w-4" />
  </a>
</div>
