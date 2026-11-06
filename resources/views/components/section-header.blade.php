@props(['title', 'subtitle' => null, 'divider' => true])

<div {{ $attributes->class(['pb-4', 'border-b border-grey-1' => $divider]) }}>
  <div class="flex items-start justify-between gap-4">
    <div>
      <h2 class="text-xl font-bold text-secondary-3">{{ $title }}</h2>
      @if ($subtitle)
        <p class="mt-1 text-sm text-black">{{ $subtitle }}</p>
      @endif
    </div>
    {{ $slot }}
  </div>
</div>