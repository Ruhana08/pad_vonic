@props(['name' => '', 'size' => 'md'])

@php
  $initials = collect(preg_split('/[\s,]+/', trim($name)))
      ->filter()->take(2)
      ->map(fn ($w) => mb_strtoupper(mb_substr($w, 0, 1)))
      ->implode('');

  $sizes = ['sm' => 'h-7 w-7 text-[10px]', 'md' => 'h-9 w-9 text-xs'];
@endphp

<span {{ $attributes->merge(['class' => 'inline-flex shrink-0 items-center justify-center rounded-full bg-primary-3 font-semibold text-primary-1 ' . ($sizes[$size] ?? $sizes['md'])]) }}>
  {{ $initials }}
</span>