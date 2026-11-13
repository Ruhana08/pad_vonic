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
      'bg'     => 'bg-white',
      'border' => 'border-success/30',
      'icon'   => 'solar-check-circle-bold',
      'iconColor' => 'text-success',
      'bar'    => 'bg-success',
      'badge'  => 'bg-success-soft text-success',
      'title'  => 'Berhasil'
    ],
    'error' => [
      'bg'     => 'bg-white',
      'border' => 'border-danger/30',
      'icon'   => 'solar-danger-circle-bold',
      'iconColor' => 'text-danger',
      'bar'    => 'bg-danger',
      'badge'  => 'bg-danger-soft text-danger',
      'title'  => 'Terjadi Kesalahan'
    ],
    'warning' => [
      'bg'     => 'bg-white',
      'border' => 'border-amber-400/50',
      'icon'   => 'solar-info-circle-bold',
      'iconColor' => 'text-amber-500',
      'bar'    => 'bg-amber-500',
      'badge'  => 'bg-amber-100 text-amber-700',
      'title'  => 'Perhatian'
    ],
    'info' => [
      'bg'     => 'bg-white',
      'border' => 'border-primary-1/30',
      'icon'   => 'solar-info-circle-bold',
      'iconColor' => 'text-primary-1',
      'bar'    => 'bg-primary-1',
      'badge'  => 'bg-primary-3 text-primary-1',
      'title'  => 'Informasi'
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
       x-transition:enter="transition ease-out duration-300"
       x-transition:enter-start="opacity-0 translate-y-[-16px] sm:translate-y-0 sm:translate-x-4"
       x-transition:enter-end="opacity-100 translate-y-0 sm:translate-x-0"
       x-transition:leave="transition ease-in duration-200"
       x-transition:leave-start="opacity-100 scale-100"
       x-transition:leave-end="opacity-0 scale-95"
       class="fixed top-6 right-6 z-50 max-w-sm w-full overflow-hidden rounded-xl border {{ $styles['border'] }} {{ $styles['bg'] }} p-4 shadow-toast"
       role="alert">

    {{-- Progress bar timer 5 detik --}}
    <div class="absolute bottom-0 left-0 h-1 w-full bg-gray-100 overflow-hidden">
      <div class="h-full {{ $styles['bar'] }} animate-[progress_5s_linear_forwards] origin-left"></div>
    </div>

    <div class="flex items-start gap-3">
      {{-- Icon Type --}}
      <div class="shrink-0 mt-0.5">
        @if ($flashType === 'success')
          <x-solar-check-circle-bold class="h-5 w-5 {{ $styles['iconColor'] }}" />
        @elseif ($flashType === 'error')
          <x-solar-danger-circle-bold class="h-5 w-5 {{ $styles['iconColor'] }}" />
        @elseif ($flashType === 'warning')
          <x-solar-info-circle-bold class="h-5 w-5 {{ $styles['iconColor'] }}" />
        @else
          <x-solar-info-circle-bold class="h-5 w-5 {{ $styles['iconColor'] }}" />
        @endif
      </div>

      {{-- Message Body --}}
      <div class="flex-1 min-w-0 pr-2">
        <div class="flex items-center gap-2">
          <span class="inline-flex rounded-full px-2 py-0.5 text-[10px] font-bold uppercase tracking-wider {{ $styles['badge'] }}">
            {{ $styles['title'] }}
          </span>
        </div>
        <p class="mt-1 text-xs text-grey-deep leading-relaxed font-medium">
          {{ $flashMessage }}
        </p>
      </div>

      {{-- Close Cross Button (Tombol Silang) --}}
      <button type="button"
              @click="close()"
              aria-label="Tutup notifikasi"
              class="shrink-0 text-grey-2 hover:text-black hover:bg-gray-100 rounded-md p-1 transition-colors cursor-pointer">
        <x-solar-close-circle-linear class="h-4 w-4" />
      </button>
    </div>
  </div>
@endif

<style>
  @keyframes progress {
    from { width: 100%; }
    to { width: 0%; }
  }
</style>
