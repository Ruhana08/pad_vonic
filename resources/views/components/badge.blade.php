@props(['tone' => 'primary'])

@php
  $tones = [
    'primary' => 'bg-primary-3 text-primary-1',
    'success' => 'bg-success-soft text-success',
    'danger'  => 'bg-danger-soft text-danger',
  ];
@endphp

<span {{ $attributes->merge(['class' => 'inline-flex items-center rounded-[6px] px-2 py-0.5 text-xs font-semibold ' . ($tones[$tone] ?? $tones['primary'])]) }}>
  {{ $slot }}
</span>