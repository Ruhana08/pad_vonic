@props(['paginator'])

@php
  $btn = 'flex h-9 min-w-9 items-center justify-center rounded-button bg-primary-1 px-3 text-xs font-semibold text-white hover:bg-primary-4';
  $off = 'pointer-events-none opacity-40';
@endphp

<div {{ $attributes->class('flex items-center gap-2') }}>
  <a href="{{ $paginator->previousPageUrl() ?? '#' }}" aria-label="Sebelumnya"
     class="{{ $btn }} {{ $paginator->onFirstPage() ? $off : '' }}">&lt;</a>

  <span class="{{ $btn }} px-5">{{ $paginator->currentPage() }}/{{ $paginator->lastPage() }}</span>

  <a href="{{ $paginator->nextPageUrl() ?? '#' }}" aria-label="Selanjutnya"
     class="{{ $btn }} {{ $paginator->hasMorePages() ? '' : $off }}">&gt;</a>
</div>