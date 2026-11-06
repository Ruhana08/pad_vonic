@props(['name', 'percent', 'leading' => false])

<div {{ $attributes->class([
    'rounded-sm bg-white p-3 min-h-[88px]',
    'border border-primary-1' => $leading,
    'border border-transparent' => ! $leading,
]) }}>
  <div class="flex items-center justify-between gap-2 text-xs">
    <span class="text-black">{{ $name }}</span>
    @if ($leading)
      <span class="rounded bg-primary-3 px-2 py-0.5 font-semibold text-primary-1">{{ $percent }}%</span>
    @else
      <span class="font-semibold text-black">{{ $percent }}%</span>
    @endif
  </div>

  <div class="mt-3 h-1.5 w-full overflow-hidden rounded-full bg-grey-1">
    <div class="h-full rounded-full {{ $leading ? 'bg-primary-1' : 'bg-primary-2' }}"
         style="width: {{ $percent }}%"></div>
  </div>
</div>