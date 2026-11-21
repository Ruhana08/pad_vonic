@props([
  'name' => null,
  'value' => null,
  'options' => [], // array of ['value' => ..., 'label' => ...] or simple array
  'placeholder' => null,
  'autoSubmit' => false,
  'variant' => 'primary', // 'primary' (border biru & teks biru sesuai gambar) | 'default' (border grey-1)
  'size' => 'md', // 'sm', 'md'
  'disabled' => false,
])

@php
  $normalized = [];
  foreach ($options as $k => $v) {
    if (is_array($v)) {
      $normalized[] = ['value' => (string)($v['value'] ?? $k), 'label' => (string)($v['label'] ?? $v['value'] ?? $k)];
    } else {
      $val = is_numeric($k) ? (string)$v : (string)$k;
      $normalized[] = ['value' => $val, 'label' => (string)$v];
    }
  }

  // Cari label terpilih
  $selectedLabel = null;
  foreach ($normalized as $item) {
    if ((string)$item['value'] === (string)$value) {
      $selectedLabel = $item['label'];
      break;
    }
  }

  // Jika tidak ada yang cocok tapi ada placeholder, pakai placeholder. Jika tidak ada placeholder, default ke opsi pertama atau '10'
  if ($selectedLabel === null) {
    if ($placeholder !== null) {
      $selectedLabel = $placeholder;
    } elseif (!empty($normalized)) {
      $selectedLabel = $normalized[0]['label'];
      $value = $normalized[0]['value'];
    } else {
      $selectedLabel = '10';
      $value = '10';
    }
  }
@endphp

<div x-data="{
       open: false,
       disabled: {{ $disabled ? 'true' : 'false' }},
       selectedVal: '{{ (string)$value }}',
       selectedText: '{{ addslashes($selectedLabel) }}',
       options: {{ json_encode($normalized) }},
       select(val, lbl) {
         if (this.disabled) return;
         this.selectedVal = val;
         this.selectedText = lbl;
         this.open = false;
         $dispatch('change', val);
         @if ($autoSubmit)
           this.$nextTick(() => {
             if (this.$refs.hiddenInput && this.$refs.hiddenInput.form) {
               this.$refs.hiddenInput.form.submit();
             }
           });
         @endif
       }
     }"
     @click.outside="open = false"
     @keydown.escape.window="open = false"
     {{ $attributes->whereStartsWith('@') }}
     class="relative inline-block {{ $attributes->get('class') }}">

  @if ($name)
    <input type="hidden" name="{{ $name }}" :value="selectedVal" x-ref="hiddenInput">
  @endif

  {{-- Trigger Button Sesuai Gambar Figma --}}
  <button type="button"
          @click="if (!disabled) open = !open"
          :disabled="disabled"
          @class([
            'btn-secondary inline-flex items-center justify-between gap-2.5 px-3.5 text-xs',
            'h-10' => $size === 'md',
            'h-8' => $size === 'sm',
            '!border-primary-1 !text-primary-1' => $variant === 'primary',
          ])>
    <span x-text="selectedText" class="truncate"></span>
    <x-solar-alt-arrow-down-linear class="h-3.5 w-3.5 shrink-0 transition-transform duration-200"
                                   ::class="open ? 'rotate-180' : ''" />
  </button>

  {{-- Menu Pilihan Melayang (Dropdown Panel) --}}
  <div x-show="open" x-cloak
       x-transition:enter="transition ease-out duration-150"
       x-transition:enter-start="opacity-0 -translate-y-1 scale-95"
       x-transition:enter-end="opacity-100 translate-y-0 scale-100"
       x-transition:leave="transition ease-in duration-100"
       x-transition:leave-start="opacity-100 translate-y-0 scale-100"
       x-transition:leave-end="opacity-0 -translate-y-1 scale-95"
       class="absolute left-0 top-full z-40 mt-1 max-h-60 overflow-y-auto min-w-full rounded-xl border border-primary-1 bg-white shadow-menu">

    <div class="flex flex-col py-0.5">
      <template x-for="opt in options" :key="opt.value">
        <button type="button"
                @click="select(opt.value, opt.label)"
                class="flex h-8 w-full items-center px-3.5 text-xs font-semibold text-left transition-colors cursor-pointer"
                :class="selectedVal == opt.value
                  ? 'bg-primary-1 text-white'
                  : 'bg-white text-primary-1 hover:bg-primary-3/50'">
          <span x-text="opt.label" class="truncate"></span>
        </button>
      </template>
    </div>
  </div>
</div>
