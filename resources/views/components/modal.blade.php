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

{{-- Backdrop: transparan + blur minimal --}}
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
     style="position: fixed; inset: 0px; width: 100vw; height: 100vh; background-color: rgba(0,0,0,0.18); backdrop-filter: blur(3px); -webkit-backdrop-filter: blur(3px);">

  {{-- Modal Card --}}
  <div class="w-full {{ $maxWidthClass }} rounded-2xl bg-white overflow-hidden"
       style="box-shadow: 0 8px 40px 0 rgba(0,0,0,.12), 0 2px 8px 0 rgba(0,0,0,.08);">
    {{ $slot }}
  </div>
</div>
