@props([
  'days' => 0,
  'hours' => 0,
  'minutes' => 0,
  'countdown' => null,
])

@php
  if (is_array($countdown)) {
    $days = $countdown['days'] ?? $days;
    $hours = $countdown['hours'] ?? $hours;
    $minutes = $countdown['minutes'] ?? $minutes;
  }
@endphp

<div {{ $attributes->class('flex items-center gap-3 w-full') }}>
  @foreach ([['Hari', $days], ['Jam', $hours], ['Menit', $minutes]] as [$label, $value])
    <div class="flex flex-1 flex-col items-center justify-center gap-1.5 rounded-xl border border-danger bg-white p-3 shadow-xs">
      <span class="text-3xl font-bold leading-none text-danger">{{ str_pad($value, 2, '0', STR_PAD_LEFT) }}</span>
      <span class="text-xs font-semibold text-black">{{ $label }}</span>
    </div>
  @endforeach
</div>
