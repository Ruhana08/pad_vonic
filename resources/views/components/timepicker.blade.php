@props([
  'value' => '08:00',
  'placeholder' => '08:00',
])

<div x-data="{
       fp: null,
       val: '{{ $value }}'
     }"
     x-init="
       fp = flatpickr($refs.timeInput, {
         enableTime: true,
         noCalendar: true,
         dateFormat: 'H:i',
         time_24hr: true,
         defaultDate: val || '08:00',
         onChange: (selectedDates, timeStr) => {
           val = timeStr;
           $dispatch('input', timeStr);
         }
       });
     "
     class="relative w-full">

  <div class="relative flex items-center">
    <div class="pointer-events-none absolute inset-y-0 left-3 flex items-center text-primary-1">
      <x-solar-clock-circle-linear class="h-4 w-4" />
    </div>

    <input type="text"
           x-ref="timeInput"
           x-model="val"
           {{ $attributes->whereDoesntStartWith('class') }}
           placeholder="{{ $placeholder }}"
           class="h-10 w-full rounded-sm border border-grey-1 bg-white pl-9 pr-3 text-xs font-semibold text-black placeholder:text-grey-2 shadow-xs transition-all focus:border-primary-1 focus:outline-none cursor-pointer">
  </div>
</div>
