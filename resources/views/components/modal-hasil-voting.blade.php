@props([
  'show' => 'false',
  'close' => '',
  'item' => 'activeHistoryItem',
])

{{-- ============================================================
     MODAL HASIL RESMI VOTING: SIMPLIFIED & CLEAN (GRAND DESIGN)
     ============================================================ --}}
<x-modal :show="$show" :close="$close" maxWidth="860px">
  <div class="p-6 sm:p-7 max-h-[90vh] overflow-y-auto" x-show="{{ $item }}"
       x-data="{
         lightboxOpen: false,
         lightboxImages: [],
         lightboxIndex: 0,
         getUniquePhotos(mainImg, galleryArr) {
           return [...new Set([mainImg, ...(Array.isArray(galleryArr) ? galleryArr : [])].filter(Boolean))];
         },
         openLightbox(imgs, idx) {
           const unique = [...new Set((Array.isArray(imgs) ? imgs : []).filter(Boolean))];
           if (!unique.length) return;
           this.lightboxImages = unique;
           this.lightboxIndex = idx >= 0 && idx < this.lightboxImages.length ? idx : 0;
           this.lightboxOpen = true;
         },
         closeLightbox() {
           this.lightboxOpen = false;
         },
         nextImg() {
           if (this.lightboxImages.length > 1) {
             this.lightboxIndex = (this.lightboxIndex + 1) % this.lightboxImages.length;
           }
         },
         prevImg() {
           if (this.lightboxImages.length > 1) {
             this.lightboxIndex = (this.lightboxIndex - 1 + this.lightboxImages.length) % this.lightboxImages.length;
           }
         }
       }">

    {{-- Header Modal --}}
    <div class="flex items-start justify-between pb-3">
      <div>
        <span class="text-xs font-semibold text-primary-1">Hasil vote</span>
        <h2 class="mt-0.5 text-xl sm:text-2xl font-bold text-black" x-text="{{ $item }}?.title || {{ $item }}?.nama_periode || 'Piknik DTEDI'"></h2>
      </div>
      <button type="button" @click="{{ $close }}"
              class="flex h-8 w-8 items-center justify-center rounded-full text-grey-2 hover:bg-gray-100 hover:text-black transition-colors cursor-pointer">
        <x-solar-close-circle-linear class="h-6 w-6" />
      </button>
    </div>

    {{-- Overview: 2 Kolom — Durasi + Partisipan --}}
    <div class="rounded-xl bg-[#F0F5FF] p-4 border border-blue-100/60">
      <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 items-center text-xs">
        <div class="space-y-1">
          <div class="flex items-center gap-1.5 font-semibold text-primary-1">
            <x-solar-calendar-linear class="h-4 w-4" />
            <span>Durasi Polling</span>
          </div>
          <p class="font-bold text-black" x-text="({{ $item }}?.start || {{ $item }}?.tanggal_mulai || '—') + ' - ' + ({{ $item }}?.end || {{ $item }}?.tanggal_selesai || '—')"></p>
        </div>

        <div class="space-y-1 border-t sm:border-t-0 sm:border-l border-blue-200/50 sm:pl-5">
          <div class="flex items-center gap-1.5 font-semibold text-primary-1">
            <x-solar-user-linear class="h-4 w-4" />
            <span>Partisipan</span>
          </div>
          <p class="text-sm font-bold text-primary-1" x-text="({{ $item }}?.participants ?? {{ $item }}?.total_suara ?? 0) + ' Orang'"></p>
        </div>
      </div>
    </div>

    {{-- State Kosong: Tidak ada suara masuk --}}
    <div x-show="({{ $item }}?.participants ?? {{ $item }}?.total_suara ?? 0) === 0" x-cloak
         class="mt-6 flex flex-col items-center justify-center rounded-2xl border border-dashed border-grey-1 bg-gray-50/70 p-10 text-center">
      <div class="mb-3 flex h-12 w-12 items-center justify-center rounded-full bg-primary-3/50 text-primary-1">
        <x-solar-chart-linear class="h-6 w-6" />
      </div>
      <h3 class="text-base font-bold text-black">Hasil voting kosong</h3>
      <p class="mt-1 max-w-sm text-xs text-grey-deep">
        Belum ada suara yang masuk pada periode polling ini sehingga belum ada hasil yang dapat ditampilkan.
      </p>
    </div>

    {{-- Kategori 1: Peserta (Reuse Card Pilihan Voting) --}}
    <div class="mt-5" x-show="({{ $item }}?.participants ?? {{ $item }}?.total_suara ?? 0) > 0">
      <h3 class="mb-2.5 flex items-center gap-1.5 text-sm font-bold text-primary-1">
        <x-solar-users-group-two-rounded-linear class="h-4 w-4" />
        <span>Peserta</span>
      </h3>
      <div class="card-choice is-active pointer-events-none">
        <span class="text-sm font-bold text-black" x-text="{{ $item }}?.peserta_type || 'Belum ada suara'"></span>
      </div>
    </div>

    {{-- Grid Dua Kolom: Desain Kaos & Destinasi Wisata (Reuse Card Pilihan Voting) --}}
    <div class="mt-5 grid grid-cols-1 md:grid-cols-12 gap-5 items-stretch"
         x-show="({{ $item }}?.participants ?? {{ $item }}?.total_suara ?? 0) > 0">

      {{-- Kategori 2: Desain Kaos (Reuse Card Voting Step 3) --}}
      <div class="md:col-span-5 flex flex-col">
        <h3 class="mb-2.5 flex items-center gap-1.5 text-sm font-bold text-primary-1">
          <x-solar-t-shirt-bold class="h-4 w-4" />
          <span>Desain Kaos</span>
        </h3>

        <div class="card-choice flex-1 flex flex-col justify-between"
             x-data="{ get kaosPhotos() { return getUniquePhotos({{ $item }}?.kaos?.image, {{ $item }}?.kaos?.gallery); } }">
          <div>
            <div class="flex items-center gap-2">
              <span class="text-sm font-bold text-black" x-text="{{ $item }}?.kaos?.title || '—'"></span>
            </div>

            {{-- Gambar Utama Kaos --}}
            <div class="mt-3 relative h-52 overflow-hidden rounded-[4px] bg-gray-100 cursor-pointer group"
                 @click="openLightbox(kaosPhotos.length ? kaosPhotos : ['https://images.unsplash.com/photo-1521572267360-ee0c2909d518?w=600&auto=format&fit=crop&q=80'], 0)">
              <img :src="kaosPhotos[0] || 'https://images.unsplash.com/photo-1521572267360-ee0c2909d518?w=600&auto=format&fit=crop&q=80'"
                   class="h-full w-full object-cover transition-transform duration-300 group-hover:scale-105">
              <div class="absolute inset-0 bg-black/0 transition-colors group-hover:bg-black/20 flex items-center justify-center opacity-0 group-hover:opacity-100">
                <span class="rounded-full bg-white/90 p-2 text-black shadow-md">
                  <x-solar-magnifer-zoom-in-linear class="h-4 w-4" />
                </span>
              </div>
            </div>

            {{-- Thumbnail Galeri Kaos (jika lebih dari 1 foto) --}}
            <template x-if="kaosPhotos.length > 1">
              <div class="mt-2 grid grid-cols-3 gap-1.5">
                <template x-for="(thumb, tIdx) in kaosPhotos.slice(1, 4)" :key="tIdx">
                  <div class="relative h-16 overflow-hidden rounded-xs bg-gray-100 cursor-pointer group"
                       @click="openLightbox(kaosPhotos, tIdx + 1)">
                    <img :src="thumb" class="h-full w-full object-cover transition-transform duration-300 group-hover:scale-105">
                    <template x-if="tIdx === 2 && kaosPhotos.length > 4">
                      <div class="absolute inset-0 flex items-center justify-center bg-black/55 text-[11px] font-bold text-white">
                        <span x-text="'+' + (kaosPhotos.length - 3)"></span>
                      </div>
                    </template>
                  </div>
                </template>
              </div>
            </template>
          </div>
        </div>
      </div>

      {{-- Kategori 3: Destinasi Wisata (Reuse Card Voting Step 2) --}}
      <div class="md:col-span-7 flex flex-col">
        <h3 class="mb-2.5 flex items-center gap-1.5 text-sm font-bold text-primary-1">
          <x-solar-map-point-bold class="h-4 w-4" />
          <span>Destinasi Wisata</span>
        </h3>

        <div class="card-choice flex-1 flex flex-col justify-between"
             x-data="{ get wisataPhotos() { return getUniquePhotos({{ $item }}?.wisata?.image, {{ $item }}?.wisata?.gallery); } }">
          <div>
            {{-- Header Card: Nama + Badge Kategori + Rating + Lokasi --}}
            <div>
              <div class="flex items-start justify-between gap-2">
                <h4 class="text-sm font-bold text-black" x-text="{{ $item }}?.wisata?.title || 'Destinasi Wisata'"></h4>
                <span class="rounded-md bg-primary-3/50 px-2 py-0.5 text-[10px] font-bold text-primary-1 shrink-0"
                      x-show="{{ $item }}?.wisata?.kategori"
                      x-text="{{ $item }}?.wisata?.kategori"></span>
              </div>
              <div class="mt-1 flex items-center gap-2 text-[11px] text-grey-deep">
                <span x-text="{{ $item }}?.wisata?.lokasi || {{ $item }}?.wisata?.provinsi || '—'"></span>
              </div>
            </div>

            {{-- Galeri Foto Miniatur (Identik Halaman Voting) --}}
            <div class="mt-3 overflow-hidden rounded-xs bg-gray-100 p-1">
              <template x-if="wisataPhotos.length > 1">
                <div class="grid grid-cols-3 gap-1.5">
                  <div class="col-span-2 relative h-36 overflow-hidden rounded-xs cursor-pointer group"
                       @click="openLightbox(wisataPhotos, 0)">
                    <img :src="wisataPhotos[0]" class="h-full w-full object-cover transition-transform duration-300 group-hover:scale-105">
                    <div class="absolute inset-0 bg-black/0 transition-colors group-hover:bg-black/20 flex items-center justify-center opacity-0 group-hover:opacity-100">
                      <span class="rounded-full bg-white/90 p-2 text-black shadow-md">
                        <x-solar-magnifer-zoom-in-linear class="h-4 w-4" />
                      </span>
                    </div>
                  </div>
                  <div class="flex flex-col gap-1.5">
                    <div class="relative h-[68px] overflow-hidden rounded-xs cursor-pointer group"
                         @click="openLightbox(wisataPhotos, 1)">
                      <img :src="wisataPhotos[1]" class="h-full w-full object-cover transition-transform duration-300 group-hover:scale-105">
                    </div>
                    <template x-if="wisataPhotos[2]">
                      <div class="relative h-[68px] overflow-hidden rounded-xs cursor-pointer group"
                           @click="openLightbox(wisataPhotos, 2)">
                        <img :src="wisataPhotos[2]" class="h-full w-full object-cover transition-transform duration-300 group-hover:scale-105">
                        <template x-if="wisataPhotos.length > 3">
                          <div class="absolute inset-0 flex items-center justify-center bg-black/55 text-[11px] font-bold text-white">
                            <span x-text="wisataPhotos.length + ' Foto'"></span>
                          </div>
                        </template>
                      </div>
                    </template>
                  </div>
                </div>
              </template>
              <template x-if="wisataPhotos.length <= 1">
                <div class="relative h-40 overflow-hidden rounded-xs cursor-pointer group"
                     @click="openLightbox(wisataPhotos.length ? wisataPhotos : ['https://images.unsplash.com/photo-1507525428034-b723cf961d3e?w=800&auto=format&fit=crop&q=80'], 0)">
                  <img :src="wisataPhotos[0] || 'https://images.unsplash.com/photo-1507525428034-b723cf961d3e?w=800&auto=format&fit=crop&q=80'"
                       class="h-full w-full object-cover transition-transform duration-300 group-hover:scale-105">
                  <div class="absolute inset-0 bg-black/0 transition-colors group-hover:bg-black/20 flex items-center justify-center opacity-0 group-hover:opacity-100">
                    <span class="rounded-full bg-white/90 p-2 text-black shadow-md">
                      <x-solar-magnifer-zoom-in-linear class="h-4 w-4" />
                    </span>
                  </div>
                </div>
              </template>
            </div>

            {{-- Info Metrik: Jarak, Durasi & Cashback (Identik Halaman Voting) --}}
            <div class="mt-3.5 grid grid-cols-3 divide-x divide-gray-100 rounded-xs border border-gray-200 bg-white text-center shadow-xs">
              <div class="py-2.5 px-1.5">
                <span class="block text-[10px] font-medium uppercase tracking-wider text-grey-2">Jarak</span>
                <strong class="mt-0.5 block text-xs font-bold text-black" x-text="{{ $item }}?.wisata?.jarak || '—'"></strong>
              </div>
              <div class="py-2.5 px-1.5">
                <span class="block text-[10px] font-medium uppercase tracking-wider text-grey-2">Durasi</span>
                <strong class="mt-0.5 block text-xs font-bold text-black" x-text="{{ $item }}?.wisata?.durasi || '—'"></strong>
              </div>
              <div class="py-2.5 px-1.5">
                <span class="block text-[10px] font-semibold uppercase tracking-wider text-primary-1">Cashback</span>
                <strong class="mt-0.5 block text-xs font-bold text-primary-1" x-text="{{ $item }}?.wisata?.biaya || '—'"></strong>
              </div>
            </div>

            {{-- Fasilitas Termasuk (Identik Halaman Voting) --}}
            <div class="mt-3 text-[11px]">
              <span class="font-bold text-grey-deep block mb-1">Fasilitas Termasuk:</span>
              <div class="rounded-xs border border-gray-200 bg-white p-2.5 text-black text-[11px] leading-relaxed">
                <template x-if="({{ $item }}?.wisata?.benefits || []).length > 0">
                  <div class="flex flex-wrap gap-1.5">
                    <template x-for="(ben, bIdx) in ({{ $item }}?.wisata?.benefits || [])" :key="bIdx">
                      <span class="inline-flex items-center rounded-xs bg-gray-100 px-2 py-0.5 text-[10px] font-medium text-grey-dark" x-text="ben"></span>
                    </template>
                  </div>
                </template>
                <template x-if="!({{ $item }}?.wisata?.benefits || []).length">
                  <span class="text-grey-2 italic">Tidak ada catatan fasilitas khusus</span>
                </template>
              </div>
            </div>
          </div>

          {{-- Footer Card: Cashback Peserta --}}
          <div class="mt-4 border-t border-gray-100 pt-3 flex items-center justify-between text-xs">
            <span class="font-semibold text-grey-deep">Cashback Peserta:</span>
            <span class="text-sm font-bold text-primary-1" x-text="{{ $item }}?.wisata?.biaya || '—'"></span>
          </div>
        </div>
      </div>

    </div>

    {{-- ============================================================
         MODAL LIGHTBOX SIMPLE: GESER KIRI / KANAN + CLOSE (X)
         ============================================================ --}}
    <div x-show="lightboxOpen" x-cloak
         class="fixed inset-0 z-[60] flex items-center justify-center p-4 bg-black/85 backdrop-blur-xs transition-opacity"
         @keydown.escape.window="closeLightbox()"
         @keydown.arrow-left.window="prevImg()"
         @keydown.arrow-right.window="nextImg()">

      <div class="fixed inset-0" @click="closeLightbox()"></div>

      <div class="relative z-10 max-w-4xl w-full flex items-center justify-center select-none" @click.stop>
        {{-- Tombol Tutup (X) --}}
        <button type="button" @click="closeLightbox()"
                aria-label="Tutup"
                class="absolute -top-12 right-0 flex h-9 w-9 items-center justify-center rounded-full bg-white/15 text-white hover:bg-white/30 transition-colors cursor-pointer">
          <x-solar-close-circle-linear class="h-6 w-6" />
        </button>

        {{-- Frame Gambar --}}
        <div class="relative w-full h-[75vh] max-h-[560px] flex items-center justify-center overflow-hidden rounded-xl bg-black">
          <img :src="lightboxImages[lightboxIndex]" alt="Preview Foto"
               class="max-h-full max-w-full object-contain">

          {{-- Geser Kiri --}}
          <template x-if="lightboxImages.length > 1">
            <button type="button" @click.stop="prevImg()"
                    aria-label="Sebelumnya"
                    class="absolute left-3 top-1/2 -translate-y-1/2 flex h-10 w-10 items-center justify-center rounded-full bg-black/60 text-white hover:bg-primary-1 transition-all shadow-md cursor-pointer">
              <x-solar-alt-arrow-left-linear class="h-5 w-5" />
            </button>
          </template>

          {{-- Geser Kanan --}}
          <template x-if="lightboxImages.length > 1">
            <button type="button" @click.stop="nextImg()"
                    aria-label="Selanjutnya"
                    class="absolute right-3 top-1/2 -translate-y-1/2 flex h-10 w-10 items-center justify-center rounded-full bg-black/60 text-white hover:bg-primary-1 transition-all shadow-md cursor-pointer">
              <x-solar-alt-arrow-right-linear class="h-5 w-5" />
            </button>
          </template>
        </div>
      </div>
    </div>

  </div>
</x-modal>
