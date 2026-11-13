@props([
  'title',
  'options' => [],
])

@php
  $sortedOptions = collect($options)->sortByDesc('percent')->values();
  $rank1 = $sortedOptions->first();
  $otherRanks = $sortedOptions->slice(1);
@endphp

<div {{ $attributes->merge(['class' => 'flex flex-col gap-4 rounded-md bg-primary-3 p-5']) }}>
  {{-- Header Kategori --}}
  <h3 class="text-base font-bold text-black">{{ $title }}</h3>

  {{-- Peringkat 01 Hero Card --}}
  @if ($rank1)
    <div class="flex flex-col gap-3 rounded-xl bg-white p-4 shadow-control">
      <span class="text-3xl font-bold leading-none text-primary-1">01</span>
      <p class="text-sm font-semibold leading-snug text-black">{{ $rank1['name'] }}</p>

      <div class="space-y-2">
        <p class="text-2xl font-bold text-primary-1">{{ $rank1['percent'] }}%</p>
        <div class="h-2 w-full overflow-hidden rounded-full bg-primary-track">
          <div class="h-full rounded-full bg-primary-1 transition-all duration-300" style="width: {{ $rank1['percent'] }}%"></div>
        </div>
      </div>
    </div>
  @endif

  {{-- Peringkat 02, 03, dst --}}
  @foreach ($otherRanks as $idx => $opt)
    <div class="flex flex-col gap-2 rounded-xl bg-white p-4 shadow-control">
      <div class="flex items-center justify-between">
        <span class="text-xl font-semibold text-grey-deep">{{ str_pad($idx + 2, 2, '0', STR_PAD_LEFT) }}</span>
        <span class="text-base font-bold text-black">{{ $opt['percent'] }}%</span>
      </div>
      <p class="text-sm font-medium text-black">{{ $opt['name'] }}</p>
    </div>
  @endforeach
</div>
