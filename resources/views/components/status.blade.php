@props(['tone' => 'success'])

@php
  $dots = ['success' => 'bg-success', 'danger' => 'bg-danger', 'neutral' => 'bg-grey-2'];
@endphp

<span {{ $attributes->merge(['class' => 'inline-flex items-center gap-1.5 text-sm text-grey-2']) }}>
  <span class="h-1.5 w-1.5 rounded-full {{ $dots[$tone] ?? $dots['success'] }}"></span>
  {{ $slot }}
</span>