@props([
  'type' => 'success', // success | error | warning | info
  'message' => null,
])

@php
  $flashMessage = $message ?? session('success') ?? session('status') ?? session('error') ?? session('warning') ?? session('info');
  $flashType = $message ? $type : (
    session('success') || session('status') ? 'success' : (
      session('error') ? 'error' : (
        session('warning') ? 'warning' : 'info'
      )
    )
  );

  $styles = [
    'success' => [
      'bg'        => 'bg-white',
      'border'    => 'border-success/30',
      'iconColor' => 'text-success',
      'titleColor'=> 'text-success',
      'title'     => 'Berhasil',
    ],
    'error' => [
      'bg'        => 'bg-white',
      'border'    => 'border-danger/30',
      'iconColor' => 'text-danger',
      'titleColor'=> 'text-danger',
      'title'     => 'Terjadi Kesalahan',
    ],
    'warning' => [
      'bg'        => 'bg-white',
      'border'    => 'border-primary-1/30',
      'iconColor' => 'text-primary-1',
      'titleColor'=> 'text-primary-1',
      'title'     => 'Perhatian',
    ],
    'info' => [
      'bg'        => 'bg-white',
      'border'    => 'border-primary-1/30',
      'iconColor' => 'text-primary-1',
      'titleColor'=> 'text-primary-1',
      'title'     => 'Informasi',
    ],
  ][$flashType] ?? $styles['success'];
@endphp

@if ($flashMessage)
  <div x-data="{
         show: true,
         timeout: null,
         init() {
           this.timeout = setTimeout(() => {
             this.show = false;
           }, 5000);
         },
         close() {
           this.show = false;
           if (this.timeout) clearTimeout(this.timeout);
         }
       }"
       x-show="show"
       x-transition:enter="transition ease-out duration-200"
       x-transition:enter-start="opacity-0 translate-y-[-8px]"
       x-transition:enter-end="opacity-100 translate-y-0"
       x-transition:leave="transition ease-in duration-150"
       x-transition:leave-start="opacity-100"
       x-transition:leave-end="opacity-0"
       class="fixed top-6 right-6 z-50 max-w-sm w-full rounded-[10px] border {{ $styles['border'] }} {{ $styles['bg'] }} p-4 shadow-toast"
       role="alert">

    <div class="flex items-start gap-3">
      <div class="shrink-0 mt-0.5">
        @if ($flashType === 'success')
          <x-solar-check-circle-bold class="h-5 w-5 {{ $styles['iconColor'] }}" />
        @elseif ($flashType === 'error')
          <x-solar-danger-circle-bold class="h-5 w-5 {{ $styles['iconColor'] }}" />
        @else
          <x-solar-info-circle-bold class="h-5 w-5 {{ $styles['iconColor'] }}" />
        @endif
      </div>

      <div class="flex-1 min-w-0 pr-2">
        <p class="text-xs font-bold {{ $styles['titleColor'] }}">{{ $styles['title'] }}</p>
        <p class="mt-0.5 text-xs text-grey-deep leading-relaxed font-medium">
          {{ $flashMessage }}
        </p>
      </div>

      <button type="button"
              @click="close()"
              aria-label="Tutup notifikasi"
              class="shrink-0 text-grey-2 hover:text-black hover:bg-gray-100 rounded-[8px] p-1 transition-colors cursor-pointer">
        <x-solar-close-circle-linear class="h-4 w-4" />
      </button>
    </div>
  </div>
@endif
