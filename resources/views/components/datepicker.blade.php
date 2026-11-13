@props([
  'value' => '',
  'placeholder' => 'Pilih tanggal...',
])

<div x-data="{
       fp: null,
       val: '{{ $value }}'
     }"
     x-init="
       fp = flatpickr($refs.dateInput, {
         dateFormat: 'Y-m-d',
         altInput: true,
         altFormat: 'j F Y',
         defaultDate: val || null,
         onChange: (selectedDates, dateStr) => {
           val = dateStr;
           $dispatch('input', dateStr);
         }
       });
     "
     class="relative w-full">

  <div class="relative flex items-center">
    <div class="pointer-events-none absolute inset-y-0 left-3 flex items-center text-primary-1">
      <x-solar-calendar-linear class="h-4 w-4" />
    </div>

    <input type="text"
           x-ref="dateInput"
           x-model="val"
           {{ $attributes->whereDoesntStartWith('class') }}
           placeholder="{{ $placeholder }}"
           class="h-10 w-full rounded-sm border border-grey-1 bg-white pl-9 pr-3 text-xs font-semibold text-black placeholder:text-grey-2 shadow-xs transition-all focus:border-primary-1 focus:outline-none cursor-pointer">
  </div>
</div>
