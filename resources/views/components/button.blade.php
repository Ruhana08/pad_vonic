@props([
  'variant' => 'primary',
  'size'    => 'md',
])

@php
  $variants = [
    // 1. Primary: Solid warna utama
    'primary'   => 'bg-primary-1 text-white border border-primary-1 hover:bg-primary-4 active:bg-primary-4',
    
    // 2. Secondary (Outline): Border gray-500, teks hitam, hover background primary-3 & border primary-1
    'secondary' => 'border border-gray-500 bg-white text-black hover:border-primary-1 hover:bg-primary-3 hover:text-primary-1 active:border-primary-1 active:text-primary-1',
    
    // 3. Filled Soft / Ghost dengan Fill: Punya background lembut (primary-3), border & teks primary-1
    'filled'    => 'border border-primary-1 bg-primary-3 text-primary-1 hover:bg-primary-2 hover:text-white active:bg-primary-4 active:text-white shadow-type-3',
  ];

  $sizes = [
    'sm' => 'px-4 py-2 text-xs',
    'md' => 'px-5 py-2.5 text-sm',
    'lg' => 'px-6 py-3 text-base',
  ];

  $classes = 'inline-flex items-center justify-center gap-2 rounded-button font-medium transition-colors '
           . 'disabled:cursor-not-allowed disabled:opacity-40 '
           . ($sizes[$size] ?? $sizes['md']) . ' '
           . ($variants[$variant] ?? $variants['primary']);
@endphp

<button {{ $attributes->merge(['type' => 'button', 'class' => $classes]) }}>
  {{ $slot }}
</button>