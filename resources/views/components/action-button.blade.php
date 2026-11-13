@props([
  'type'     => 'edit', // 'view' | 'edit' | 'delete' | 'download' | 'upload' | 'publish' | 'stop'
  'href'     => null,
  'label'    => null,
  'color'    => null,
  'disabled' => false,
])

@php
  $configs = [
    'view' => [
      'color' => 'text-primary-1 border-gray-200 hover:border-primary-1 hover:bg-primary-3/40',
      'icon'  => 'solar-eye-linear',
      'label' => 'Lihat',
    ],
    'edit' => [
      'color' => 'text-primary-1 border-gray-200 hover:border-primary-1 hover:bg-primary-3/40',
      'icon'  => 'solar-pen-new-square-linear',
      'label' => 'Edit',
    ],
    'delete' => [
      'color' => 'text-danger border-gray-200 hover:border-danger hover:bg-rose-50',
      'icon'  => 'solar-trash-bin-trash-linear',
      'label' => 'Hapus',
    ],
    'download' => [
      'color' => 'text-primary-1 border-gray-200 hover:border-primary-1 hover:bg-primary-3/40',
      'icon'  => 'solar-download-linear',
      'label' => 'Unduh',
    ],
    'upload' => [
      'color' => 'text-primary-1 border-gray-200 hover:border-primary-1 hover:bg-primary-3/40',
      'icon'  => 'solar-upload-linear',
      'label' => 'Unggah',
    ],
    'publish' => [
      'color' => 'text-success border-gray-200 hover:border-success hover:bg-emerald-50',
      'icon'  => 'solar-upload-linear',
      'label' => 'Publikasikan',
    ],
    'stop' => [
      'color' => 'text-amber-600 border-gray-200 hover:border-amber-600 hover:bg-amber-50',
      'icon'  => 'solar-stop-circle-linear',
      'label' => 'Akhiri',
    ],
  ];

  $cfg = $configs[$type] ?? $configs['edit'];
  $ariaLabel = $label ?? $cfg['label'];
  $colorClass = $disabled
    ? 'border-gray-200 bg-gray-50/70 text-gray-300 cursor-not-allowed pointer-events-none'
    : ($color ?? $cfg['color']);
  $classes = "inline-flex h-7 w-7 items-center justify-center rounded-sm border bg-white shadow-xs transition-all {$colorClass}";
@endphp

@if ($href && !$disabled)
  <a href="{{ $href }}" aria-label="{{ $ariaLabel }}" title="{{ $ariaLabel }}" {{ $attributes->merge(['class' => $classes]) }}>
    <x-dynamic-component :component="$cfg['icon']" class="h-4 w-4 shrink-0" />
  </a>
@else
  <button type="button" aria-label="{{ $ariaLabel }}" title="{{ $ariaLabel }}" @disabled($disabled) {{ $attributes->merge(['class' => $classes]) }}>
    <x-dynamic-component :component="$cfg['icon']" class="h-4 w-4 shrink-0" />
  </button>
@endif
