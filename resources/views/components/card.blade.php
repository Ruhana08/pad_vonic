@props([
  'variant' => 'panel', // 'hero-polling' | 'hero-faq' | 'panel' | 'showcase' | 'choice' | 'stepper' | 'stat' | 'info-box' | 'empty'
  'active'  => false,
])

@php
  $classes = match($variant) {
    'hero-polling' => 'card-hero-polling p-8 sm:p-9',
    'hero-faq'     => 'card-hero-faq p-6',
    'panel'        => 'card-panel p-6',
    'showcase'     => 'card-showcase p-6',
    'choice'       => 'card-choice p-4' . ($active ? ' is-active' : ''),
    'stepper'      => 'card-stepper p-3' . ($active ? ' is-active' : ''),
    'stat'         => 'card-stat p-4',
    'info-box'     => 'card-info-box p-3',
    'empty'        => 'card-empty-state p-8 sm:p-12',
    'elevated'     => 'card-panel p-6 shadow-type-3',
    'paper'        => 'card-panel p-6 shadow-type-2',
    default        => 'card-panel p-6',
  };
@endphp

<div {{ $attributes->merge(['class' => 'min-w-0 ' . $classes]) }}>
  {{ $slot }}
</div>
