@props(['variant' => 'panel'])

@php
  $variants = [
    'panel'    => 'border border-grey-1',
    'elevated' => 'border border-transparent shadow-type-3',
    'paper'    => 'border border-transparent shadow-type-2',
  ];
@endphp

<div {{ $attributes->merge(['class' => 'rounded-xl bg-white ' . ($variants[$variant] ?? $variants['panel'])]) }}>
  {{ $slot }}
</div>