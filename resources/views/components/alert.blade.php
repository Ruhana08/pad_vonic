@props([
  'type' => 'error', // error | success | info
  'message' => null,
])

@php
  $styles = [
    'error'   => 'border-danger/25 bg-red-50 text-danger',
    'success' => 'border-success/25 bg-green-50 text-success',
    'info'    => 'border-primary-1/25 bg-primary-3 text-primary-1',
  ][$type] ?? 'border-danger/25 bg-red-50 text-danger';
@endphp

<div {{ $attributes->merge(['class' => "w-full rounded-[10px] border px-4 py-3 text-xs font-medium flex items-center gap-2.5 $styles"]) }} role="alert">
  @if ($type === 'success')
    <x-solar-check-circle-bold class="h-4 w-4 shrink-0" />
  @elseif ($type === 'info')
    <x-solar-info-circle-bold class="h-4 w-4 shrink-0" />
  @else
    <x-solar-danger-triangle-bold class="h-4 w-4 shrink-0" />
  @endif
  <span>{{ $message ?? $slot }}</span>
</div>
