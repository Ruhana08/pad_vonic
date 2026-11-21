@props([
  'items' => [
    [
      'q' => 'Bagaimana cara memberikan suara?',
      'a' => 'Klik tombol "Ikuti Voting" di halaman beranda saat polling aktif. Ikuti langkah pemilihan: tentukan rencana peserta, pilih paket destinasi wisata, pilih desain & ukuran kaos, lalu konfirmasi pilihan Anda.',
    ],
    [
      'q' => 'Apakah pilihan bisa diubah setelah dikirim?',
      'a' => 'Tidak. Setiap sivitas hanya dapat memberikan satu kali suara per periode polling. Pastikan pilihan Anda sudah benar sebelum menekan tombol kirim.',
    ],
    [
      'q' => 'Kapan hasil voting diumumkan?',
      'a' => 'Hasil perolehan suara akhir akan dirilis dan dapat dilihat pada halaman Riwayat Pemilihan setelah periode polling resmi ditutup oleh admin.',
    ],
    [
      'q' => 'Apa yang terjadi jika belum sempat memilih?',
      'a' => 'Jika Anda belum memberikan suara hingga batas waktu berakhir, hak suara Anda untuk periode tersebut akan hangus.',
    ],
  ],
])

{{-- 
     Container FAQ:
     - Rounded besar 20px
     - Background biru solid (#196BEF)
     - Padding lebih leluasa
--}}
<div {{ $attributes->merge(['class' => 'flex w-full lg:w-[540px] min-h-[420px] flex-col justify-between rounded-[24px] bg-primary-1 p-6 text-white shadow-type-1']) }}
     style="background-color: #196BEF;"
     x-data="{ activeAccordion: null }">

  <div class="flex flex-1 flex-col justify-between w-full space-y-2">
    @foreach ($items as $index => $item)
      @php 
        $idx = $index + 1; 
        $isLast = $loop->last;
      @endphp

      <div class="w-full flex-1 flex flex-col justify-center {{ !$isLast ? 'border-b border-white/20 pb-4' : 'pt-1' }}">
        <button type="button"
                @click="activeAccordion = (activeAccordion === {{ $idx }} ? null : {{ $idx }})"
                class="flex w-full items-center justify-between gap-4 text-left cursor-pointer group">
          
          <div class="flex items-center gap-4 flex-1 min-w-0">
            {{-- Kotak tanda tanya (?) dengan radius terkecil design system (radius-xs: 4px) --}}
            <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xs bg-secondary-1 text-base font-bold text-white shadow-[0_4px_12px_rgba(0,0,0,0.15)]"
                  style="background-color: #053788; border-radius: 4px;">
              ?
            </span>

            {{-- Judul Pertanyaan diperbesar (15px-16px font-bold) --}}
            <span class="text-[15px] sm:text-base font-bold text-white tracking-wide leading-snug">
              {{ $item['q'] }}
            </span>
          </div>

          {{-- Panah Chevron Putih lebih tebal --}}
          <x-solar-alt-arrow-down-linear
              class="h-5 w-5 shrink-0 text-white transition-transform duration-200"
              ::class="activeAccordion === {{ $idx }} ? 'rotate-180' : ''" />
        </button>

        {{-- Jawaban Accordion diperbesar (13px-14px) dan kontras tajam --}}
        <div x-show="activeAccordion === {{ $idx }}"
             x-cloak
             x-transition:enter="transition ease-out duration-150"
             x-transition:enter-start="opacity-0 -translate-y-1"
             x-transition:enter-end="opacity-100 translate-y-0"
             class="mt-3 pl-[60px] pr-4 text-sm leading-relaxed text-white/95 font-medium">
          {{ $item['a'] }}
        </div>
      </div>
    @endforeach
  </div>

</div>
