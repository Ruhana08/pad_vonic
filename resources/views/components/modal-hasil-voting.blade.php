@props([
  'show' => 'false',
  'close' => '',
  'item' => 'activeHistoryItem',
])

{{-- Modal Quick-View Hasil Pemenang Polling (Identik Admin Arsip) --}}
<x-modal :show="$show" :close="$close" maxWidth="820px">
  <div class="p-7 max-h-[90vh] overflow-y-auto" x-show="{{ $item }}">
    {{-- Header Modal --}}
    <div class="flex items-start justify-between pb-4">
      <div>
        <span class="text-xs font-semibold text-primary-1">Hasil vote</span>
        <h2 class="mt-1 text-2xl font-bold text-black" x-text="{{ $item }}?.title"></h2>
      </div>
      <button type="button" @click="{{ $close }}"
              class="flex h-8 w-8 items-center justify-center rounded-full text-grey-2 hover:bg-gray-100 hover:text-danger transition-colors cursor-pointer">
        <x-solar-close-circle-linear class="h-5 w-5" />
      </button>
    </div>

    {{-- Overview Event Box (Durasi, Partisipan, Peserta) --}}
    <div class="rounded-xl bg-primary-3/50 p-4 border border-primary-1/20">
      <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 items-center">
        {{-- Durasi Pelaksanaan --}}
        <div class="space-y-1">
          <div class="flex items-center gap-1.5 text-xs font-semibold text-primary-1">
            <x-solar-calendar-linear class="h-4 w-4" />
            <span>Durasi Polling</span>
          </div>
          <p class="text-xs font-bold text-black" x-text="({{ $item }}?.start || '') + ' - ' + ({{ $item }}?.end || '')"></p>
        </div>

        {{-- Partisipan --}}
        <div class="space-y-1 border-t sm:border-t-0 sm:border-l border-primary-1/20 sm:pl-5">
          <div class="flex items-center gap-1.5 text-xs font-semibold text-primary-1">
            <x-solar-user-linear class="h-4 w-4" />
            <span>Partisipan</span>
          </div>
          <p class="text-sm font-bold text-primary-1" x-text="({{ $item }}?.participants || 0) + ' Orang'"></p>
        </div>

        {{-- Peserta --}}
        <div class="space-y-1 border-t sm:border-t-0 sm:border-l border-primary-1/20 sm:pl-5">
          <div class="flex items-center gap-1.5 text-xs font-semibold text-primary-1">
            <span>Peserta :</span>
          </div>
          <p class="text-xs font-medium text-black truncate" x-text="{{ $item }}?.peserta_type || 'Dosen dan Tenaga Didik'"></p>
        </div>
      </div>
    </div>

    {{-- Cards Grid Hasil Pemenang --}}
    <div class="mt-6 grid grid-cols-1 md:grid-cols-12 gap-5 items-stretch">
      
      {{-- Kolom Kiri: Desain Kaos Wisata (5/12) --}}
      <div class="md:col-span-5 flex flex-col space-y-2">
        <div class="flex items-center gap-2 text-sm font-bold text-primary-1">
          <x-solar-t-shirt-bold class="h-4 w-4 text-primary-1" />
          <span>Desain Kaos Wisata :</span>
        </div>

        <div class="flex-1 rounded-2xl border border-grey-1 bg-white p-4 shadow-xs flex flex-col justify-between">
          <div class="flex flex-col flex-1 justify-between">
            <div>
              <h4 class="text-base font-bold text-black" x-text="{{ $item }}?.kaos?.title || 'Kaos Desain Utama'"></h4>
              
              {{-- Gambar Utama Kaos --}}
              <div class="mt-3 aspect-[4/3] w-full overflow-hidden rounded-xl bg-gray-100 cursor-pointer group relative"
                   @click="previewImage = {{ $item }}?.kaos?.image">
                <img :src="{{ $item }}?.kaos?.image" alt="Kaos Pemenang" class="h-full w-full object-cover group-hover:scale-105 transition-transform duration-300">
                <div class="absolute inset-0 bg-black/0 group-hover:bg-black/20 transition-colors flex items-center justify-center opacity-0 group-hover:opacity-100">
                  <span class="rounded-full bg-white/90 p-2 text-black shadow-md">
                    <x-solar-magnifer-zoom-in-linear class="h-5 w-5" />
                  </span>
                </div>
              </div>

              {{-- Galeri Foto Tambahan --}}
              <div class="mt-3 grid grid-cols-2 gap-2">
                <template x-for="(thumb, tIdx) in ({{ $item }}?.kaos?.gallery || [])" :key="tIdx">
                  <div class="relative aspect-[4/3] overflow-hidden rounded-lg bg-gray-100 cursor-pointer group"
                       @click="previewImage = thumb">
                    <img :src="thumb" class="h-full w-full object-cover group-hover:scale-105 transition-transform duration-300">
                    <template x-if="tIdx === 1">
                      <div class="absolute inset-0 flex items-center justify-center bg-black/40 text-xs font-bold text-white group-hover:bg-black/50 transition-colors">
                        +2
                      </div>
                    </template>
                  </div>
                </template>
              </div>
            </div>

            <div class="mt-4 rounded-xl bg-gray-50 p-3 border border-gray-100">
              <span class="text-[11px] font-semibold text-grey-deep">Kategori Pilihan:</span>
              <p class="mt-0.5 text-xs font-bold text-primary-1">Pilihan Teratas Civitas DTEDI</p>
            </div>
          </div>
        </div>
      </div>

      {{-- Kolom Kanan: Destinasi Wisata Pemenang (7/12) --}}
      <div class="md:col-span-7 flex flex-col space-y-2">
        <div class="flex items-center gap-2 text-sm font-bold text-primary-1">
          <x-solar-map-point-bold class="h-4 w-4 text-primary-1" />
          <span>Destinasi Wisata :</span>
        </div>

        <div class="flex-1 rounded-2xl border border-grey-1 bg-white p-4 shadow-xs flex flex-col justify-between">
          <div>
            {{-- Header Wisata & Rating --}}
            <div class="flex items-center justify-between">
              <h4 class="text-base font-bold text-black" x-text="{{ $item }}?.wisata?.title || 'Destinasi Wisata Terpilih'"></h4>
              <div class="flex items-center gap-1 text-sm font-bold text-black">
                <x-solar-star-bold class="h-4 w-4 text-warning" />
                <span x-text="{{ $item }}?.wisata?.rating || '4,8'"></span>
              </div>
            </div>

            {{-- Gambar Utama & Galeri Wisata --}}
            <div class="mt-3 grid grid-cols-1 sm:grid-cols-3 gap-2">
              <div class="sm:col-span-2 aspect-[16/10] overflow-hidden rounded-xl bg-gray-100 cursor-pointer group relative"
                   @click="previewImage = {{ $item }}?.wisata?.image">
                <img :src="{{ $item }}?.wisata?.image" alt="Wisata Pemenang" class="h-full w-full object-cover group-hover:scale-105 transition-transform duration-300">
                <div class="absolute inset-0 bg-black/0 group-hover:bg-black/20 transition-colors flex items-center justify-center opacity-0 group-hover:opacity-100">
                  <span class="rounded-full bg-white/90 p-2 text-black shadow-md">
                    <x-solar-magnifer-zoom-in-linear class="h-5 w-5" />
                  </span>
                </div>
              </div>
              <div class="flex sm:flex-col gap-2">
                <template x-for="(wThumb, wIdx) in ({{ $item }}?.wisata?.gallery || [])" :key="wIdx">
                  <div class="relative flex-1 aspect-[16/10] overflow-hidden rounded-xl bg-gray-100 cursor-pointer group"
                       @click="previewImage = wThumb">
                    <img :src="wThumb" class="h-full w-full object-cover group-hover:scale-105 transition-transform duration-300">
                    <template x-if="wIdx === 1">
                      <div class="absolute inset-0 flex items-center justify-center bg-black/40 text-xs font-bold text-white group-hover:bg-black/50 transition-colors">
                        +2
                      </div>
                    </template>
                  </div>
                </template>
              </div>
            </div>

            {{-- Detail Wisata --}}
            <div class="mt-4 space-y-2.5 text-xs">
              <div class="flex justify-between items-start">
                <div>
                  <span class="text-[11px] text-grey-deep">Lokasi</span>
                  <p class="font-medium text-black mt-0.5" x-text="{{ $item }}?.wisata?.lokasi || 'Jawa Tengah'"></p>
                </div>
                <span class="text-[11px] text-primary-1 font-semibold" x-text="'Dikunjungi: ' + ({{ $item }}?.wisata?.dikunjungi || 1) + 'x'"></span>
              </div>

              <div class="grid grid-cols-2 gap-4 pt-1">
                <div>
                  <span class="text-[11px] text-grey-deep">Jarak</span>
                  <p class="font-bold text-black mt-0.5" x-text="{{ $item }}?.wisata?.jarak || '120 Km'"></p>
                </div>
                <div>
                  <span class="text-[11px] text-grey-deep">Lama Perjalanan</span>
                  <p class="font-bold text-black mt-0.5" x-text="{{ $item }}?.wisata?.durasi || '2 Hari'"></p>
                </div>
              </div>

              {{-- Benefit Tags --}}
              <div class="pt-1">
                <span class="text-[11px] text-grey-deep">Benefit:</span>
                <div class="mt-1.5 flex flex-wrap gap-1.5">
                  <template x-for="(ben, bIdx) in ({{ $item }}?.wisata?.benefits || [])" :key="bIdx">
                    <span class="rounded-md border border-grey-1 bg-white px-2 py-0.5 text-[10px] font-medium text-grey-deep" x-text="ben"></span>
                  </template>
                </div>
              </div>
            </div>
          </div>

          {{-- Biaya / Anggaran --}}
          <div class="mt-4 pt-3 border-t border-gray-100 flex items-center justify-between">
            <span class="text-xs text-grey-deep">Estimasi Biaya Paket :</span>
            <span class="text-sm font-bold text-primary-1" x-text="{{ $item }}?.wisata?.biaya || 'Rp 500.000'"></span>
          </div>
        </div>
      </div>

    </div>
  </div>
</x-modal>
