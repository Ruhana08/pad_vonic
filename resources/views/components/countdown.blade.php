@props(['days' => 0, 'hours' => 0, 'minutes' => 0])

<div {{ $attributes->class('flex items-start gap-3') }}>
  @foreach ([['HARI', $days], ['JAM', $hours], ['MENIT', $minutes]] as $i => [$label, $value])
    @if ($i > 0)
      <span class="text-3xl font-semibold leading-none text-black">:</span>
    @endif
    <div class="flex flex-col items-center gap-2">
      <span class="text-4xl font-semibold leading-none text-danger">{{ str_pad($value, 2, '0', STR_PAD_LEFT) }}</span>
      <span class="text-[10px] font-semibold text-black">{{ $label }}</span>
    </div>
  @endforeach
</div>