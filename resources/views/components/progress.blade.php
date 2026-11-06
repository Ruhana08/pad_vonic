@props(['value' => 0, 'tone' => 'primary'])

@php
  $v    = max(0, min(100, (float) $value));
  $fill = $tone === 'soft' ? 'bg-primary-2' : 'bg-primary-1';
@endphp

<div {{ $attributes->merge(['class' => 'h-1.5 w-full overflow-hidden rounded-full bg-grey-1']) }}
     role="progressbar" aria-valuemin="0" aria-valuemax="100" aria-valuenow="{{ $v }}">
  <div class="h-full rounded-full {{ $fill }}" @style(['width: ' . $v . '%'])></div>
</div>