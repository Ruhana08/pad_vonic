@props([
  'value' => '',
  'placeholder' => 'Pilih tanggal...',
])

<div x-data="{
       open: false,
       val: @js($value ?: ''),
       viewYear: new Date().getFullYear(),
       viewMonth: new Date().getMonth(),
       monthNames: ['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'],
       dayNames: ['Min','Sen','Sel','Rab','Kam','Jum','Sab'],
       init() {
         this.$watch('val', (v) => {
           if (v && /^\d{4}-\d{2}-\d{2}$/.test(v)) {
             const [y, m] = v.split('-').map(Number);
             this.viewYear = y;
             this.viewMonth = m - 1;
           }
         });
         if (this.val && /^\d{4}-\d{2}-\d{2}$/.test(this.val)) {
           const [y, m] = this.val.split('-').map(Number);
           this.viewYear = y;
           this.viewMonth = m - 1;
         }
       },
       get formattedLabel() {
         if (!this.val || !/^\d{4}-\d{2}-\d{2}$/.test(this.val)) return '';
         const [y, m, d] = this.val.split('-').map(Number);
         return d + ' ' + this.monthNames[m - 1] + ' ' + y;
       },
       get calendarDays() {
         const firstDay = new Date(this.viewYear, this.viewMonth, 1).getDay();
         const daysInMonth = new Date(this.viewYear, this.viewMonth + 1, 0).getDate();
         const cells = [];
         for (let i = 0; i < firstDay; i++) cells.push(null);
         for (let d = 1; d <= daysInMonth; d++) cells.push(d);
         return cells;
       },
       prevMonth() {
         if (this.viewMonth === 0) { this.viewMonth = 11; this.viewYear--; }
         else { this.viewMonth--; }
       },
       nextMonth() {
         if (this.viewMonth === 11) { this.viewMonth = 0; this.viewYear++; }
         else { this.viewMonth++; }
       },
       isSelected(day) {
         if (!day || !this.val) return false;
         const iso = this.viewYear + '-' + String(this.viewMonth + 1).padStart(2, '0') + '-' + String(day).padStart(2, '0');
         return this.val === iso;
       },
       isToday(day) {
         if (!day) return false;
         const now = new Date();
         return now.getFullYear() === this.viewYear && now.getMonth() === this.viewMonth && now.getDate() === day;
       },
       selectDay(day) {
         if (!day) return;
         this.val = this.viewYear + '-' + String(this.viewMonth + 1).padStart(2, '0') + '-' + String(day).padStart(2, '0');
         this.$dispatch('input', this.val);
         this.open = false;
       },
       selectToday() {
         const now = new Date();
         this.viewYear = now.getFullYear();
         this.viewMonth = now.getMonth();
         this.selectDay(now.getDate());
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
          class="flex h-10 w-full items-center justify-between gap-2 rounded-xl border bg-white px-3 text-xs font-semibold shadow-xs transition-all hover:border-primary-1 focus:outline-none cursor-pointer"
          :class="open ? 'border-primary-1 ring-2 ring-primary-1/15' : 'border-grey-1'">
    <span class="flex items-center gap-2 truncate">
      <x-solar-calendar-linear class="h-4 w-4 shrink-0 text-primary-1" />
      <span x-text="formattedLabel || '{{ $placeholder }}'"
            :class="formattedLabel ? 'text-black' : 'text-grey-2 font-normal'"
            class="truncate"></span>
    </span>
    <x-solar-alt-arrow-down-linear class="h-3.5 w-3.5 shrink-0 text-grey-2 transition-transform duration-200"
                                   ::class="open ? 'rotate-180 text-primary-1' : ''" />
  </button>

  {{-- Dropdown Kalender --}}
  <div x-show="open" x-cloak
       x-transition:enter="transition ease-out duration-150"
       x-transition:enter-start="opacity-0 -translate-y-1 scale-95"
       x-transition:enter-end="opacity-100 translate-y-0 scale-100"
       x-transition:leave="transition ease-in duration-100"
       x-transition:leave-start="opacity-100 translate-y-0 scale-100"
       x-transition:leave-end="opacity-0 -translate-y-1 scale-95"
       class="absolute left-0 top-full z-50 mt-1.5 w-72 rounded-xl border border-grey-1 bg-white p-3.5 shadow-xl">

    {{-- Header Bulan & Tahun --}}
    <div class="mb-3 flex items-center justify-between">
      <button type="button" @click="prevMonth()"
              class="flex h-7 w-7 items-center justify-center rounded-lg border border-grey-1 text-grey-deep hover:border-primary-1 hover:text-primary-1 transition-colors cursor-pointer">
        <x-solar-alt-arrow-left-linear class="h-3.5 w-3.5" />
      </button>
      <span class="text-xs font-bold text-black" x-text="monthNames[viewMonth] + ' ' + viewYear"></span>
      <button type="button" @click="nextMonth()"
              class="flex h-7 w-7 items-center justify-center rounded-lg border border-grey-1 text-grey-deep hover:border-primary-1 hover:text-primary-1 transition-colors cursor-pointer">
        <x-solar-alt-arrow-right-linear class="h-3.5 w-3.5" />
      </button>
    </div>

    {{-- Nama Hari --}}
    <div class="mb-1.5 grid grid-cols-7 gap-1 text-center">
      <template x-for="d in dayNames" :key="d">
        <span class="text-[10px] font-bold text-grey-2" x-text="d"></span>
      </template>
    </div>

    {{-- Grid Tanggal --}}
    <div class="grid grid-cols-7 gap-1 text-center">
      <template x-for="(day, idx) in calendarDays" :key="idx">
        <div>
          <template x-if="day !== null">
            <button type="button"
                    @click="selectDay(day)"
                    class="flex h-8 w-full items-center justify-center rounded-lg text-xs transition-colors cursor-pointer"
                    :class="isSelected(day)
                      ? 'bg-primary-1 font-bold text-white shadow-xs'
                      : (isToday(day) ? 'border border-primary-1 font-bold text-primary-1 hover:bg-primary-3/40' : 'text-black hover:bg-gray-100')">
              <span x-text="day"></span>
            </button>
          </template>
        </div>
      </template>
    </div>

    {{-- Footer Hari Ini --}}
    <div class="mt-2.5 flex items-center justify-between border-t border-gray-100 pt-2">
      <button type="button" @click="selectToday()"
              class="text-[11px] font-semibold text-primary-1 hover:underline cursor-pointer">
        Hari Ini
      </button>
      <button type="button" @click="open = false"
              class="rounded-lg bg-gray-100 px-2.5 py-1 text-[11px] font-semibold text-grey-deep hover:bg-gray-200 cursor-pointer">
        Tutup
      </button>
    </div>
  </div>
</div>
