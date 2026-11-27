@props([
  'value' => '08:00',
  'placeholder' => '08:00',
])

<div x-data="{
       open: false,
       val: @js($value ?: '08:00'),
       hours: Array.from({ length: 24 }, (_, i) => String(i).padStart(2, '0')),
       minutes: Array.from({ length: 60 }, (_, i) => String(i).padStart(2, '0')),
       get currentHour() {
         const parts = String(this.val || '08:00').split(':');
         const h = parseInt(parts[0], 10);
         return isNaN(h) ? '08' : String(Math.min(23, Math.max(0, h))).padStart(2, '0');
       },
       get currentMinute() {
         const parts = String(this.val || '08:00').split(':');
         const m = parseInt(parts[1], 10);
         return isNaN(m) ? '00' : String(Math.min(59, Math.max(0, m))).padStart(2, '0');
       },
       setHour(h) {
         this.val = h + ':' + this.currentMinute;
         this.$dispatch('input', this.val);
       },
       setMinute(m) {
         this.val = this.currentHour + ':' + m;
         this.$dispatch('input', this.val);
       },
       onManualInput(e) {
         const v = e.target.value.trim();
         if (/^([01]\d|2[0-3]):([0-5]\d)$/.test(v)) {
           this.val = v;
           this.$dispatch('input', this.val);
         }
       }
     }"
     x-modelable="val"
     @click.outside="open = false"
     @keydown.escape.window="open = false"
     {{ $attributes->whereDoesntStartWith('class') }}
     class="relative w-full {{ $attributes->get('class') }}">

  {{-- Trigger Button (Sesuai Desain Dropdown Vonic) --}}
  <button type="button"
          @click="open = !open"
          class="flex h-10 w-full items-center justify-between gap-2 rounded-xl border bg-white px-3 text-xs font-semibold text-black shadow-xs transition-all hover:border-primary-1 focus:outline-none cursor-pointer"
          :class="open ? 'border-primary-1 ring-2 ring-primary-1/15' : 'border-grey-1'">
    <span class="flex items-center gap-2 truncate">
      <x-solar-clock-circle-linear class="h-4 w-4 shrink-0 text-primary-1" />
      <span x-text="(val || '{{ $placeholder }}') + ' WIB'" class="truncate"></span>
    </span>
    <x-solar-alt-arrow-down-linear class="h-3.5 w-3.5 shrink-0 text-grey-2 transition-transform duration-200"
                                   ::class="open ? 'rotate-180 text-primary-1' : ''" />
  </button>

  {{-- Dropdown Panel 24 Jam (00 - 23) --}}
  <div x-show="open" x-cloak
       x-transition:enter="transition ease-out duration-150"
       x-transition:enter-start="opacity-0 -translate-y-1 scale-95"
       x-transition:enter-end="opacity-100 translate-y-0 scale-100"
       x-transition:leave="transition ease-in duration-100"
       x-transition:leave-start="opacity-100 translate-y-0 scale-100"
       x-transition:leave-end="opacity-0 -translate-y-1 scale-95"
       class="absolute left-0 top-full z-50 mt-1.5 w-56 rounded-xl border border-grey-1 bg-white p-2.5 shadow-xl">

    {{-- Input Manual Cepat 24 Jam --}}
    <div class="mb-2 flex items-center justify-between gap-2 border-b border-gray-100 pb-2">
      <span class="text-[11px] font-semibold text-grey-deep">Format 24 Jam</span>
      <input type="text"
             :value="val"
             @change="onManualInput($event)"
             maxlength="5"
             placeholder="HH:MM"
             class="w-16 rounded-lg border border-grey-1 bg-gray-50 px-2 py-1 text-center text-xs font-bold text-primary-1 focus:border-primary-1 focus:bg-white focus:outline-none">
    </div>

    <div class="grid grid-cols-2 gap-2">
      {{-- Kolom Jam 00 - 23 --}}
      <div>
        <div class="mb-1 text-center text-[10px] font-bold uppercase tracking-wider text-grey-2">Jam</div>
        <div class="max-h-44 overflow-y-auto space-y-0.5 pr-1">
          <template x-for="h in hours" :key="h">
            <button type="button"
                    @click="setHour(h)"
                    class="w-full rounded-lg py-1.5 text-center text-xs transition-colors cursor-pointer"
                    :class="currentHour === h ? 'bg-primary-1 text-white font-bold' : 'text-black hover:bg-primary-3/40'">
              <span x-text="h"></span>
            </button>
          </template>
        </div>
      </div>

      {{-- Kolom Menit 00 - 59 --}}
      <div>
        <div class="mb-1 text-center text-[10px] font-bold uppercase tracking-wider text-grey-2">Menit</div>
        <div class="max-h-44 overflow-y-auto space-y-0.5 pr-1">
          <template x-for="m in minutes" :key="m">
            <button type="button"
                    @click="setMinute(m)"
                    class="w-full rounded-lg py-1.5 text-center text-xs transition-colors cursor-pointer"
                    :class="currentMinute === m ? 'bg-primary-1 text-white font-bold' : 'text-black hover:bg-primary-3/40'">
              <span x-text="m"></span>
            </button>
          </template>
        </div>
      </div>
    </div>

    <div class="mt-2 border-t border-gray-100 pt-2 flex justify-end">
      <button type="button"
              @click="open = false"
              class="rounded-lg bg-primary-3/60 px-3 py-1 text-[11px] font-bold text-primary-1 hover:bg-primary-1 hover:text-white transition-colors cursor-pointer">
        Selesai
      </button>
    </div>
  </div>
</div>
