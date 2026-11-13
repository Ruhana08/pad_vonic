@props([
  'label',
  'value',
  'sub' => null,
  'variant' => 'primary', // 'primary' | 'danger'
])

@php
  $valueColors = [
    'primary' => 'text-primary-1',
    'danger'  => 'text-danger',
  ];
@endphp

<div {{ $attributes->merge(['class' => 'flex flex-1 flex-col gap-2 rounded-xl border border-primary-1 bg-white p-4 shadow-control']) }}>
  <p class="text-xs font-semibold text-black">{{ $label }}</p>
  <p class="text-3xl font-bold {{ $valueColors[$variant] ?? 'text-primary-1' }}">{{ $value }}</p>
  @if ($sub)
    <p class="text-xs text-black">{{ $sub }}</p>
  @endif
</div>
