@props([
  'columns' => [], // array of ['key' => ..., 'label' => ..., 'align' => 'left'|'center'|'right', 'width' => '...']
  'striped' => false,
  'minWidth' => '700px',
  'headerBg' => 'bg-secondary-1', // Default biru gelap Figma (#053788)
  'headerAlign' => 'center',      // Judul kolom default CENTER sesuai permintaan
])

<div {{ $attributes->class('overflow-x-auto') }}>
  <table class="w-full border-separate border-spacing-y-2 text-xs" style="min-width: {{ $minWidth }};">
    {{-- Header Tabel (Judul Kolom Center) --}}
    <thead>
      <tr class="{{ $headerBg }} text-white text-xs font-bold tracking-wider">
        @if (isset($header))
          {{ $header }}
        @else
          @foreach ($columns as $col)
            @php
              // Jika spesifik ditentukan di col['align'], gunakan itu; jika tidak, gunakan $headerAlign ('center')
              $align = $col['align'] ?? $headerAlign;
              $alignClass = match($align) {
                'left' => 'text-left',
                'right' => 'text-right',
                default => 'text-center',
              };
              $isFirst = $loop->first;
              $isLast = $loop->last;
            @endphp
            <th @class([
                  'py-3 px-3.5',
                  $alignClass,
                  'rounded-l-sm' => $isFirst,
                  'rounded-r-sm' => $isLast,
                  $col['class'] ?? '',
                ])
                @if (isset($col['width'])) style="width: {{ $col['width'] }};" @endif>
              {{ $col['label'] ?? '' }}
            </th>
          @endforeach
        @endif
      </tr>
    </thead>

    {{-- Body Tabel --}}
    <tbody>
      {{ $slot }}
    </tbody>
  </table>
</div>
