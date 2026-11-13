@props([
  'show',
  'close' => 'modalMode = null',
  'maxWidth' => '820px',
])

@php
  $maxWidthClass = match ($maxWidth) {
    'sm'    => 'max-w-sm',
    'md'    => 'max-w-md',
    'lg'    => 'max-w-lg',
    'xl'    => 'max-w-xl',
    '2xl'   => 'max-w-2xl',
    '3xl'   => 'max-w-3xl',
    '4xl'   => 'max-w-4xl',
    '820px' => 'max-w-[820px]',
    default => 'max-w-[820px]',
  };
@endphp

{{-- Backdrop: fixed fullscreen overlay with blue tint + blur --}}
<div x-show="{{ $show }}"
     x-cloak
     x-transition:enter="transition ease-out duration-200"
     x-transition:enter-start="opacity-0"
     x-transition:enter-end="opacity-100"
     x-transition:leave="transition ease-in duration-150"
     x-transition:leave-start="opacity-100"
     x-transition:leave-end="opacity-0"
     @click.self="{{ $close }}"
     @keydown.escape.window="{{ $close }}"
     class="fixed inset-0 top-0 left-0 right-0 bottom-0 w-screen h-screen z-50 flex items-center justify-center p-4 sm:p-6 overflow-y-auto"
     style="position: fixed; inset: 0px; width: 100vw; height: 100vh; background-color: rgba(102, 156, 244, 0.45); backdrop-filter: blur(8px); -webkit-backdrop-filter: blur(8px);">

  {{-- Modal Card: constrained width, shadow-type-2, rounded --}}
  <div class="w-full {{ $maxWidthClass }} rounded-2xl bg-white overflow-hidden"
       style="box-shadow: var(--shadow-type-2, 0 4px 27.7px 0 rgba(0,0,0,.04), 0 46px 18px 0 rgba(0,0,0,.01), 0 26px 15px 0 rgba(0,0,0,.05), 0 11px 11px 0 rgba(0,0,0,.09), 0 3px 6px 0 rgba(0,0,0,.10));">
    {{ $slot }}
  </div>
</div>
