@props([
  'icon' => 'solar-inbox-linear',
  'title' => 'Belum Ada Data',
  'message' => 'Data saat ini masih kosong.',
  'actionText' => null,
  'actionUrl' => null,
  'actionClick' => null,
])

<div {{ $attributes->class('flex flex-col items-center justify-center py-12 px-4 text-center') }}>
  <div class="flex h-16 w-16 items-center justify-center rounded-2xl bg-primary-3/60 text-primary-1 mb-4 shadow-xs">
    <x-dynamic-component :component="$icon" class="h-8 w-8" />
  </div>

  <h3 class="text-base font-bold text-black">{{ $title }}</h3>
  <p class="mt-1 text-xs text-grey-2 max-w-sm">{{ $message }}</p>

  @if ($actionText)
    <div class="mt-5">
      @if ($actionUrl)
        <a href="{{ $actionUrl }}"
           class="inline-flex items-center gap-2 rounded-sm bg-primary-1 px-5 py-2.5 text-xs font-semibold text-white shadow-sm hover:bg-primary-4 transition-all">
          <span>{{ $actionText }}</span>
        </a>
      @elseif ($actionClick)
        <button type="button"
                @click="{{ $actionClick }}"
                class="inline-flex items-center gap-2 rounded-sm bg-primary-1 px-5 py-2.5 text-xs font-semibold text-white shadow-sm hover:bg-primary-4 transition-all">
          <span>{{ $actionText }}</span>
        </button>
      @endif
    </div>
  @endif
</div>
