@props([
  'variant' => 'primary', // 'primary' | 'secondary' | 'outline' | 'gradient' | 'danger'
  'size'    => 'md',      // 'sm' | 'md' | 'lg'
  'href'    => null,
])

@php
  $variantClasses = match($variant) {
    'gradient'  => 'btn-gradient',
    'primary'   => 'btn-primary',
    'secondary', 'outline' => 'btn-secondary',
    'danger'    => 'btn-danger',
    default     => 'btn-primary',
  };

  $sizeClasses = match($size) {
    'sm' => 'px-3.5 py-2 text-xs',
    'md' => 'px-5 py-2.5 text-xs sm:text-sm',
    'lg' => 'px-7 py-3.5 text-sm sm:text-base',
    default => 'px-5 py-2.5 text-xs sm:text-sm',
  };

  $classes = "{$variantClasses} {$sizeClasses}";
@endphp

@if ($href)
  <a href="{{ $href }}" {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
  </a>
@else
  <button {{ $attributes->merge(['type' => 'button', 'class' => $classes]) }}>
    {{ $slot }}
  </button>
@endif
