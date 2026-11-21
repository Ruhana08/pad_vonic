@extends('layouts.focus')

@section('title', 'Form Pembuatan Polling')
@section('page_title', 'Form Pembuatan Polling')
@section('max_width', 'max-w-[1400px]')

@section('content')
@php
  $input  = 'w-full rounded-xl bg-white px-4 py-2.5 text-xs text-black border border-grey-1 shadow-xs placeholder:text-grey-2 focus:outline-none focus:border-primary-1';
  $label  = 'block text-xs font-semibold text-black mb-1.5';
@endphp



<script>
  const registerPollForm = () => {
    if (typeof Alpine === 'undefined') return;
    Alpine.data('pollForm', () => ({
      step: 1,
      uid: 100,
      errors: [],
      action: 'draft',
      dirty: false,
      leaving: false,
      modal: null,
      customSizeInput: '',
      allSizes: ['XS', 'S', 'M', 'L', 'XL', 'XXL', '3XL'],
      
      wisataCategories: [
        { value: '__custom__', label: '+ Tambahkan kategori lainnya (ketik manual)' },
        { value: 'Wisata Pantai & Bahari', label: 'Wisata Pantai & Bahari' },
        { value: 'Wisata Pegunungan & Dataran Tinggi', label: 'Wisata Pegunungan & Dataran Tinggi' },
        { value: 'Wisata Air Terjun (Curug)', label: 'Wisata Air Terjun (Curug)' },
        { value: 'Taman Hiburan (Theme Park)', label: 'Taman Hiburan (Theme Park)' },
        { value: 'Wisata Air (Waterpark)', label: 'Wisata Air (Waterpark)' },
        { value: 'Wisata Sejarah & Cagar Budaya', label: 'Wisata Sejarah & Cagar Budaya' },
        { value: 'Wisata Kuliner', label: 'Wisata Kuliner' },
        { value: 'Wisata Belanja (Shopping Tourism)', label: 'Wisata Belanja (Shopping Tourism)' },
        { value: 'Wisata Desa Adat', label: 'Wisata Desa Adat' },
        { value: 'Wisata Edukasi & Museum', label: 'Wisata Edukasi & Museum' },
        { value: 'Wisata Satwa & Konservasi', label: 'Wisata Satwa & Konservasi' },
        { value: 'Wisata Agro (Agrowisata)', label: 'Wisata Agro (Agrowisata)' },
        { value: 'Wisata Petualangan (Adventure)', label: 'Wisata Petualangan (Adventure)' },
        { value: 'Wisata Religi', label: 'Wisata Religi' },
        { value: 'Wisata Kebugaran (Wellness Tourism)', label: 'Wisata Kebugaran (Wellness Tourism)' }
      ],
      
      selectWisataCategory(opt, catVal) {
        if (catVal === '__custom__') {
          opt.isCustom = true;
          opt.label = '';
          opt.openDropdown = false;
        } else {
          opt.isCustom = false;
          opt.label = catVal;
          opt.openDropdown = false;
        }
      },

      // Katalog Riwayat Destinasi
      wisataLibrary: [
        {
          id: 1,
          name: 'Pantai Kartini',
          label: 'Wisata Pantai & Bahari',
          lokasi: 'Jepara, Jawa Tengah',
          jarak: 120,
          lama: '2 Hari',
          biaya: 500000,
          rating: 4.8,
          dikunjungi: 2,
          benefits: ['Transportasi Bus', 'Makan & Minum', 'Akomodasi Penginapan'],
          description: 'Wisata bahari dengan pemandangan pesisir utara Jawa.',
          tags: ['Pantai', 'Jepara']
        },
        {
          id: 2,
          name: 'Kepulauan Karimunjawa',
          label: 'Wisata Pantai & Bahari',
          lokasi: 'Karimunjawa, Jepara, Jawa Tengah',
          jarak: 180,
          lama: '3 Hari',
          biaya: 850000,
          rating: 4.9,
          dikunjungi: 1,
          benefits: ['Tiket Kapal', 'Snorkeling', 'Penginapan AC', 'Konsumsi'],
          description: 'Eksplorasi gugusan pulau dan keanekaragaman hayati bawah laut.',
          tags: ['Snorkeling', 'Pulau']
        },
        {
          id: 3,
          name: 'Candi Borobudur & Desa Wisata',
          label: 'Wisata Sejarah & Cagar Budaya',
          lokasi: 'Magelang, Jawa Tengah',
          jarak: 45,
          lama: '1 Hari',
          biaya: 350000,
          rating: 4.7,
          dikunjungi: 0,
          benefits: ['Tiket Masuk Candi', 'VW Safari Tour', 'Makan Siang'],
          description: 'Wisata edukasi sejarah candi megah dan suasana pedesaan.',
          tags: ['Budaya', 'Edukasi']
        },
        {
          id: 4,
          name: 'Kebun Teh Kemuning',
          label: 'Wisata Pegunungan & Dataran Tinggi',
          lokasi: 'Karanganyar, Jawa Tengah',
          jarak: 95,
          lama: '1 Hari',
          biaya: 300000,
          rating: 4.6,
          dikunjungi: 1,
          benefits: ['Jeep Tour', 'Makan Siang', 'Tiket Wahana'],
          description: 'Pemandangan alam perkebunan teh lereng gunung.',
          tags: ['Pegunungan', 'Alam']
        }
      ],

      // Kondisi Awal Bersih & Netral (Default Kosong)
      form: {
        identity: {
          title: '',
          description: '',
          start: { time: '08:00', date: '' },
          end:   { time: '18:00', date: '' },
        },
        peserta: {
          question: '',
          description: '',
          options: [
            { uid: 1, label: '' }
          ]
        },
        wisata: {
          question: '',
          description: '',
          options: [
            {
              uid: 2,
              name: '',
              label: '',
              isCustom: false,
              openDropdown: false,
              lokasi: '',
              jarak: '',
              lama: '',
              biaya: '',
              rating: '',
              dikunjungi: 0,
              benefits: [],
              benefitInput: '',
              description: '',
              tags: [],
              tagInput: '',
              photos: [
                { preview: '' },
                { preview: '' },
                { preview: '' }
              ]
            }
          ],
        },
        kaos: {
          question: '',
          description: '',
          chart_mode: 'manual',
          chart_image_preview: '',
          allow_custom_user_size: false,
          sizes: [],
          chart: {
            XS: { chest: '', length: '' }, S: { chest: '', length: '' }, M: { chest: '', length: '' },
            L: { chest: '', length: '' }, XL: { chest: '', length: '' }, XXL: { chest: '', length: '' },
            '3XL': { chest: '', length: '' },
          },
          options: [
            {
              uid: 3,
              name: '',
              photos: [
                { preview: '' },
                { preview: '' },
                { preview: '' }
              ]
            }
          ],
        },
      },

      init() {
        window.__pollFormScope = this.form;
        this.$watch('form', () => { this.dirty = true }, { deep: true });
        window.addEventListener('beforeunload', (e) => {
          if (this.dirty && !this.leaving) { e.preventDefault(); e.returnValue = ''; }
        });
      },

      payload() {
        return JSON.stringify(this.form, (k, v) => (k === 'preview' || k === 'tagInput' || k === 'benefitInput' || k === 'chart_image_preview') ? undefined : v);
      },

      // Dinamis Opsi Peserta
      addPeserta() {
        this.form.peserta.options.push({ uid: ++this.uid, label: '' });
      },
      removePeserta(i) {
        if (this.form.peserta.options.length > 1) {
          this.form.peserta.options.splice(i, 1);
        } else {
          this.form.peserta.options[0].label = '';
        }
      },

      // Wisata Dinamis & Riwayat
      showWisataMemory: false,
      selectedLibraryIds: [],

      toggleLibraryItem(libItem) {
        const idx = this.selectedLibraryIds.indexOf(libItem.id);
        if (idx > -1) {
          this.selectedLibraryIds.splice(idx, 1);
          const optIdx = this.form.wisata.options.findIndex(o => o.memoryId === libItem.id);
          if (optIdx > -1 && this.form.wisata.options.length > 1) {
            this.form.wisata.options.splice(optIdx, 1);
          } else if (optIdx > -1) {
            this.clearWisataOption(this.form.wisata.options[optIdx]);
          }
        } else {
          this.selectedLibraryIds.push(libItem.id);
          let targetOpt = this.form.wisata.options.find(o => !o.name && !o.memoryId);
          if (!targetOpt) {
            this.addWisata();
            targetOpt = this.form.wisata.options[this.form.wisata.options.length - 1];
          }
          this.applyFromLibrary(targetOpt, libItem);
          targetOpt.memoryId = libItem.id;
        }
      },

      clearWisataOption(opt) {
        opt.name = '';
        opt.label = '';
        opt.lokasi = '';
        opt.jarak = '';
        opt.lama = '';
        opt.biaya = '';
        opt.rating = '';
        opt.dikunjungi = 0;
        opt.benefits = [];
        opt.description = '';
        opt.tags = [];
        opt.memoryId = null;
        opt.photos = [{ preview: '' }, { preview: '' }, { preview: '' }];
      },

      addWisata() {
        this.form.wisata.options.push({
          uid: ++this.uid,
          memoryId: null,
          name: '',
          label: '',
          isCustom: false,
          openDropdown: false,
          lokasi: '',
          jarak: '',
          lama: '',
          biaya: '',
          rating: '',
          dikunjungi: 0,
          benefits: [],
          benefitInput: '',
          description: '',
          tags: [],
          tagInput: '',
          photos: [
            { preview: '' },
            { preview: '' },
            { preview: '' }
          ]
        });
      },
      removeWisata(i) {
        const opt = this.form.wisata.options[i];
        if (opt.memoryId) {
          const lIdx = this.selectedLibraryIds.indexOf(opt.memoryId);
          if (lIdx > -1) this.selectedLibraryIds.splice(lIdx, 1);
        }
        if (this.form.wisata.options.length > 1) {
          this.form.wisata.options.splice(i, 1);
        } else {
          this.clearWisataOption(this.form.wisata.options[0]);
        }
      },
      applyFromLibrary(opt, libItem) {
        if (!libItem) return;
        opt.name        = libItem.name;
        opt.label       = libItem.label;
        opt.lokasi      = libItem.lokasi;
        opt.jarak       = libItem.jarak;
        opt.lama        = libItem.lama;
        opt.biaya       = libItem.biaya;
        opt.rating      = libItem.rating;
        opt.dikunjungi  = libItem.dikunjungi;
        opt.benefits    = [...libItem.benefits];
        opt.description = libItem.description;
        opt.tags        = [...libItem.tags];
      },
      addTag(o) {
        const t = o.tagInput ? o.tagInput.trim().replace(/^#/, '') : '';
        if (t && !o.tags.includes(t)) o.tags.push(t);
        o.tagInput = '';
      },
      addBenefit(o) {
        const b = o.benefitInput ? o.benefitInput.trim() : '';
        if (b && !o.benefits.includes(b)) o.benefits.push(b);
        o.benefitInput = '';
      },
      removeBenefit(o, idx) {
        o.benefits.splice(idx, 1);
      },

      // Foto Wisata (Maks 3 per destinasi)
      pickWisataPhoto(o, pIdx, e) {
        const f = e.target.files[0];
        if (f) {
          o.photos[pIdx].preview = URL.createObjectURL(f);
        }
      },
      removeWisataPhoto(o, pIdx) {
        o.photos[pIdx].preview = '';
      },

      // Kaos Dinamis & Foto (Maks 3 per desain)
      get activeSizes() { return this.allSizes.filter(s => this.form.kaos.sizes.includes(s)) },
      toggleSize(s) {
        const a = this.form.kaos.sizes;
        this.form.kaos.sizes = a.includes(s) ? a.filter(x => x !== s) : [...a, s];
      },
      addCustomSize() {
        const val = this.customSizeInput.trim().toUpperCase();
        if (val && !this.allSizes.includes(val)) {
          this.allSizes.push(val);
          this.form.kaos.sizes.push(val);
          this.form.kaos.chart[val] = { chest: '', length: '' };
        }
        this.customSizeInput = '';
      },
      addKaos() {
        this.form.kaos.options.push({
          uid: ++this.uid,
          name: '',
          photos: [
            { preview: '' },
            { preview: '' },
            { preview: '' }
          ]
        });
      },
      removeKaos(i) {
        if (this.form.kaos.options.length > 1) {
          this.form.kaos.options.splice(i, 1);
        } else {
          this.form.kaos.options[0].name = '';
          this.form.kaos.options[0].photos = [{ preview: '' }, { preview: '' }, { preview: '' }];
        }
      },
      pickKaosPhoto(o, pIdx, e) {
        const f = e.target.files[0];
        if (f) {
          o.photos[pIdx].preview = URL.createObjectURL(f);
        }
      },
      removeKaosPhoto(o, pIdx) {
        o.photos[pIdx].preview = '';
      },
      pickChartImage(e) {
        const f = e.target.files[0];
        this.form.kaos.chart_image_preview = f ? URL.createObjectURL(f) : '';
      },

      // Validasi Form
      validate(mode) {
        const f = this.form, e = [];
        if (!f.identity.title.trim()) e.push({ step: 1, msg: 'Judul kegiatan voting wajib diisi.' });
        if (mode === 'draft') return e;

        if (!f.identity.start.date || !f.identity.end.date) {
          e.push({ step: 1, msg: 'Tanggal mulai dan tanggal selesai voting wajib ditentukan.' });
        } else if (f.identity.end.date < f.identity.start.date) {
          e.push({ step: 1, msg: 'Tanggal selesai tidak boleh mendahului tanggal mulai.' });
        }
        
        // Validasi Peserta
        if (!f.peserta.question.trim()) e.push({ step: 2, msg: 'Pertanyaan untuk kategori Rombongan Peserta wajib diisi.' });
        if (f.peserta.options.filter(o => o.label.trim()).length < 1) {
          e.push({ step: 2, msg: 'Tentukan minimal 1 opsi untuk kategori Rombongan Peserta.' });
        }
        
        // Validasi Wisata
        if (!f.wisata.question.trim()) e.push({ step: 2, msg: 'Pertanyaan untuk kategori Tempat Wisata wajib diisi.' });
        if (f.wisata.options.filter(o => o.name.trim()).length < 1) {
          e.push({ step: 2, msg: 'Tentukan minimal 1 opsi destinasi wisata.' });
        }
        
        // Validasi Kaos
        if (!f.kaos.question.trim()) e.push({ step: 2, msg: 'Pertanyaan untuk kategori Desain Kaos wajib diisi.' });
        if (f.kaos.chart_mode === 'manual' && f.kaos.sizes.length === 0) {
          e.push({ step: 2, msg: 'Pilih minimal satu ukuran kaos untuk tabel size chart.' });
        }
        if (f.kaos.options.filter(o => o.name.trim()).length < 1) {
          e.push({ step: 2, msg: 'Tentukan minimal 1 pilihan desain kaos.' });
        }
        return e;
      },

      submit(mode) {
        this.modal = null;
        this.errors = this.validate(mode);
        if (this.errors.length) {
          this.step = this.errors[0].step;
          window.scrollTo({ top: 0, behavior: 'smooth' });
          return;
        }
        this.action = mode;
        this.leaving = true;
        this.$nextTick(() => this.$refs.form.submit());
      },

      askCancel() {
        this.modal = 'cancel';
      },
      confirmCancel() {
        this.leaving = true;
        window.location.href = this.$root.dataset.cancelUrl;
      },
      askPublish() {
        this.errors = this.validate('publish');
        if (this.errors.length) {
          this.step = this.errors[0].step;
          window.scrollTo({ top: 0, behavior: 'smooth' });
          return;
        }
        this.modal = 'publish';
      },
    }));
  };

  if (window.Alpine) {
    registerPollForm();
  } else {
    document.addEventListener('alpine:init', registerPollForm);
  }

  document.addEventListener('turbo:load', () => {
    if (window.Alpine) registerPollForm();
  });
</script>

<form method="POST" action="{{ route('admin.polling.store') }}" enctype="multipart/form-data"
      data-cancel-url="{{ route('admin.polling.arsip') }}"
      x-data="pollForm" x-ref="form" x-on:submit.prevent>
  @csrf
  <input type="hidden" name="payload" x-bind:value="payload()">
  <input type="hidden" name="action" x-bind:value="action">

  {{-- Layout Grid: Sisi Kiri Form Utama, Sisi Kanan Action Sidebar Sticky --}}
  <div class="grid grid-cols-1 items-start gap-8 lg:grid-cols-[1fr_280px]">

    {{-- ============================================================
         KOLOM KIRI: FORM UTAMA
         ============================================================ --}}
    <div class="space-y-6">

      {{-- Header Konten & Segmentasi Step --}}
      <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
          <h1 class="text-2xl font-bold tracking-tight text-black">Form Pembuatan Polling</h1>
          <p class="mt-1 text-xs text-grey-2">Susun identitas voting, jadwal pelaksanaan, dan kategori pilihan suara.</p>
        </div>

        {{-- Segmented Controller Step 1 & Step 2 --}}
        <div class="inline-flex rounded-xl bg-gray-100 p-1 border border-grey-1 shadow-xs">
          <button type="button"
                  @click="step = 1"
                  :class="step === 1 ? 'bg-primary-1 text-white shadow-sm font-bold' : 'text-grey-deep hover:text-black font-semibold'"
                  class="flex h-9 items-center gap-2 rounded-lg px-4 text-xs transition-all cursor-pointer">
            <span class="flex h-5 w-5 items-center justify-center rounded-full text-[10px]"
                  :class="step === 1 ? 'bg-white/20 text-white' : 'bg-gray-200 text-grey-deep'">1</span>
            <span>Identitas Polling</span>
          </button>

          <button type="button"
                  @click="step = 2"
                  :class="step === 2 ? 'bg-primary-1 text-white shadow-sm font-bold' : 'text-grey-deep hover:text-black font-semibold'"
                  class="flex h-9 items-center gap-2 rounded-lg px-4 text-xs transition-all cursor-pointer">
            <span class="flex h-5 w-5 items-center justify-center rounded-full text-[10px]"
                  :class="step === 2 ? 'bg-white/20 text-white' : 'bg-gray-200 text-grey-deep'">2</span>
            <span>Kategori & Opsi</span>
          </button>
        </div>
      </div>

      {{-- Alert Error --}}
      @if ($errors->any())
        <div class="rounded-xl border border-danger/30 bg-red-50 p-4 text-xs text-danger shadow-xs">
          <p class="font-bold mb-1">Periksa isian form:</p>
          <ul class="list-inside list-disc space-y-0.5">
            @foreach ($errors->all() as $err) <li>{{ $err }}</li> @endforeach
          </ul>
        </div>
      @endif
      <div x-show="errors.length" x-cloak class="rounded-xl border border-danger/30 bg-red-50 p-4 text-xs text-danger shadow-xs">
        <p class="font-bold mb-1">Periksa isian berikut sebelum melanjutkan:</p>
        <ul class="list-inside list-disc space-y-0.5">
          <template x-for="e in errors"><li x-text="e.msg"></li></template>
        </ul>
      </div>

      {{-- ============================================================
           TAHAP 1: IDENTITAS & WAKTU POLLING
           ============================================================ --}}
      <div x-show="step === 1" x-cloak class="space-y-6">
        <x-card variant="panel" class="p-7">
          <div class="border-b border-grey-1 pb-4">
            <h2 class="text-base font-bold text-black">Informasi Umum Polling</h2>
            <p class="mt-0.5 text-xs text-grey-2">Tentukan judul kegiatan, petunjuk pemilih, dan rentang waktu pelaksanaan.</p>
          </div>

          <div class="mt-6 space-y-5">
            {{-- Judul Kegiatan --}}
            <div>
              <label class="{{ $label }}">Judul Kegiatan Polling</label>
              <input type="text" x-model="form.identity.title" class="{{ $input }}"
                     placeholder="Tuliskan judul kegiatan (contoh: Piknik & Gathering Tahunan DTEDI SV UGM)">
              <p class="mt-1 text-[11px] text-grey-2">*Judul ini akan menjadi nama utama pada kartu voting dan pengumuman.</p>
            </div>

            {{-- Deskripsi Panduan --}}
            <div>
              <label class="{{ $label }}">Deskripsi & Petunjuk Pemilih <span class="text-grey-2 font-normal">(opsional)</span></label>
              <textarea x-model="form.identity.description" rows="3" class="{{ $input }} resize-none"
                        placeholder="Tuliskan latar belakang kegiatan, ketentuan pemilihan, atau catatan penting..."></textarea>
            </div>

            {{-- Durasi Pelaksanaan --}}
            <div class="pt-2">
              <label class="{{ $label }}">Jadwal & Batas Waktu Pelaksanaan</label>
              <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                {{-- Waktu Mulai --}}
                <div class="rounded-xl border border-grey-1 bg-gray-50/60 p-4">
                  <div class="flex items-center gap-2 text-xs font-bold text-primary-1 mb-3">
                    <x-solar-calendar-linear class="h-4 w-4" />
                    <span>Waktu Mulai Voting</span>
                  </div>
                  <div class="grid grid-cols-1 sm:grid-cols-2 gap-2.5">
                    <div>
                      <span class="block text-[11px] text-grey-deep mb-1">Tanggal Mulai</span>
                      <input type="date" x-model="form.identity.start.date" class="{{ $input }}">
                    </div>
                    <div>
                      <span class="block text-[11px] text-grey-deep mb-1">Jam (WIB)</span>
                      <input type="time" x-model="form.identity.start.time" class="{{ $input }}">
                    </div>
                  </div>
                </div>

                {{-- Waktu Selesai --}}
                <div class="rounded-xl border border-grey-1 bg-gray-50/60 p-4">
                  <div class="flex items-center gap-2 text-xs font-bold text-danger mb-3">
                    <x-solar-clock-circle-linear class="h-4 w-4" />
                    <span>Batas Akhir / Penutupan</span>
                  </div>
                  <div class="grid grid-cols-1 sm:grid-cols-2 gap-2.5">
                    <div>
                      <span class="block text-[11px] text-grey-deep mb-1">Tanggal Selesai</span>
                      <input type="date" x-model="form.identity.end.date" class="{{ $input }}">
                    </div>
                    <div>
                      <span class="block text-[11px] text-grey-deep mb-1">Jam (WIB)</span>
                      <input type="time" x-model="form.identity.end.time" class="{{ $input }}">
                    </div>
                  </div>
                </div>
              </div>
            </div>

          </div>

          {{-- Navigasi Lanjut ke Tahap 2 --}}
          <div class="mt-8 flex justify-end border-t border-grey-1 pt-4">
            <button type="button" @click="step = 2" class="btn-primary h-10 px-6 text-xs font-semibold">
              <span>Lanjut ke Kategori & Opsi</span>
              <x-solar-arrow-right-linear class="h-4 w-4" />
            </button>
          </div>
        </x-card>
      </div>

      {{-- ============================================================
           TAHAP 2: KATEGORI & OPSI JAWABAN
           ============================================================ --}}
      <div x-show="step === 2" x-cloak class="space-y-6">

        {{-- 1. KATEGORI ROMBONGAN PESERTA --}}
        <x-card variant="panel" class="p-6">
          <div class="border-b border-grey-1 pb-3">
            <span class="text-[11px] font-bold text-primary-1 uppercase tracking-wider">Kategori 1</span>
            <h2 class="text-base font-bold text-black mt-0.5">Rombongan Peserta</h2>
            <p class="text-xs text-grey-2 mt-0.5">Pengelompokan keberangkatan rombongan kegiatan.</p>
          </div>

          {{-- Pertanyaan & Deskripsi --}}
          <div class="mt-5 space-y-4">
            <div>
              <label class="{{ $label }}">Pertanyaan Kategori</label>
              <input type="text" x-model="form.peserta.question" class="{{ $input }}"
                     placeholder="Tuliskan pertanyaan (contoh: Bagaimana preferensi pembagian rombongan keberangkatan?)">
            </div>

            <div>
              <label class="{{ $label }}">Deskripsi & Info Tambahan <span class="text-grey-2 font-normal">(opsional)</span></label>
              <textarea x-model="form.peserta.description" rows="3" class="{{ $input }} resize-none"
                        placeholder="Tambahkan petunjuk khusus atau penjelasan detail jika ada..."></textarea>
            </div>
          </div>

          {{-- Daftar Opsi Jawaban --}}
          <div class="mt-6 border-t border-grey-1 pt-5">
            <label class="{{ $label }}">Pilihan Opsi Jawaban</label>
            <div class="space-y-3">
              <template x-for="(o, i) in form.peserta.options" :key="o.uid">
                <div class="flex items-center gap-2.5">
                  <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl border border-primary-1 bg-primary-3/20 text-xs font-bold text-primary-1"
                        x-text="i + 1"></span>
                  <input type="text" x-model="o.label" class="{{ $input }} h-10"
                         :placeholder="'Tuliskan opsi jawaban ke-' + (i + 1) + ' (contoh: Dosen & Tendik dipisah)'">
                  
                  {{-- Tombol Hapus Konsisten Icon Silang X --}}
                  <template x-if="o.label.trim() !== '' || form.peserta.options.length > 1">
                    <button type="button" @click="removePeserta(i)"
                            class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl border border-grey-1 text-grey-2 hover:border-danger hover:text-danger hover:bg-rose-50 transition-colors cursor-pointer"
                            title="Hapus opsi ini">
                      <x-solar-close-circle-linear class="h-4 w-4" />
                    </button>
                  </template>
                </div>
              </template>
            </div>

            {{-- Tombol Tambah Opsi CUKUP DI BAWAH --}}
            <div class="mt-3">
              <button type="button" @click="addPeserta()"
                      class="btn-secondary h-9 px-4 text-xs font-semibold">
                <x-solar-add-circle-linear class="h-4 w-4 text-primary-1" />
                <span>+ Tambah Opsi Peserta</span>
              </button>
            </div>
          </div>
        </x-card>

        {{-- 2. KATEGORI TEMPAT WISATA --}}
        <x-card variant="panel" class="p-6">
          <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 border-b border-grey-1 pb-3">
            <div>
              <span class="text-[11px] font-bold text-primary-1 uppercase tracking-wider">Kategori 2</span>
              <h2 class="text-base font-bold text-black mt-0.5">Destinasi Tempat Wisata</h2>
              <p class="text-xs text-grey-2 mt-0.5">Pilihan tujuan wisata dan rincian fasilitas kegiatan.</p>
            </div>

            {{-- Drawer Trigger: Pilih dari Riwayat Destinasi --}}
            <div>
              <button type="button"
                      @click="showWisataMemory = !showWisataMemory"
                      class="btn-secondary h-9 px-3.5 text-xs font-semibold">
                <x-solar-history-linear class="h-4 w-4 text-primary-1" />
                <span x-text="showWisataMemory ? 'Tutup Pilihan Riwayat' : 'Pilih dari Riwayat Destinasi'"></span>
                <x-solar-alt-arrow-down-linear class="h-3.5 w-3.5 transition-transform duration-200"
                                               ::class="showWisataMemory ? 'rotate-180' : ''" />
              </button>
            </div>
          </div>

          {{-- DRAWER RIWAYAT DESTINASI --}}
          <div x-show="showWisataMemory" x-cloak
               x-transition:enter="transition ease-out duration-250"
               x-transition:enter-start="opacity-0 -translate-y-2"
               x-transition:enter-end="opacity-100 translate-y-0"
               class="mt-4 rounded-2xl border border-primary-1 bg-primary-3/15 p-5">
            <div class="flex items-center justify-between border-b border-primary-1/20 pb-2.5">
              <div>
                <h4 class="text-xs font-bold text-primary-1">Katalog Riwayat Destinasi Sebelumnya</h4>
                <p class="text-[11px] text-grey-deep">Centang destinasi untuk menyalin rinciannya langsung ke form opsi pilihan.</p>
              </div>
              <span class="rounded-full bg-white px-2.5 py-0.5 text-[11px] font-bold text-primary-1 border border-primary-1 shadow-xs"
                    x-text="selectedLibraryIds.length + ' Dipilih'"></span>
            </div>

            <div class="mt-3.5 grid grid-cols-1 gap-3 sm:grid-cols-2 lg:grid-cols-4">
              <template x-for="item in wisataLibrary" :key="item.id">
                <div @click="toggleLibraryItem(item)"
                     class="flex flex-col justify-between rounded-xl border bg-white p-3.5 shadow-xs transition-all cursor-pointer select-none"
                     :class="selectedLibraryIds.includes(item.id)
                       ? 'border-primary-1 ring-2 ring-primary-1 bg-primary-3/20'
                       : 'border-grey-1 hover:border-primary-1'">
                  
                  <div>
                    <div class="flex items-center justify-between gap-1">
                      <span class="rounded-md bg-primary-3/60 px-2 py-0.5 text-[10px] font-bold text-primary-1 truncate" x-text="item.label"></span>
                      <div class="flex h-4 w-4 shrink-0 items-center justify-center rounded-md border"
                           :class="selectedLibraryIds.includes(item.id) ? 'border-primary-1 bg-primary-1 text-white' : 'border-grey-1 bg-white'">
                        <template x-if="selectedLibraryIds.includes(item.id)">
                          <x-solar-check-read-linear class="h-3 w-3" />
                        </template>
                      </div>
                    </div>

                    <h5 class="mt-2 text-xs font-bold text-black" x-text="item.name"></h5>
                    <p class="text-[11px] text-grey-2 mt-0.5 truncate" x-text="item.lokasi"></p>
                  </div>

                  <div class="mt-3 border-t border-gray-100 pt-2 flex items-center justify-between text-[11px]">
                    <span class="font-bold text-amber-500" x-text="'★ ' + item.rating"></span>
                    <span class="font-bold text-primary-1" x-text="'Rp ' + Number(item.biaya).toLocaleString('id-ID')"></span>
                  </div>
                </div>
              </template>
            </div>
          </div>

          {{-- Pertanyaan & Deskripsi --}}
          <div class="mt-5 space-y-4">
            <div>
              <label class="{{ $label }}">Pertanyaan Kategori</label>
              <input type="text" x-model="form.wisata.question" class="{{ $input }}"
                     placeholder="Tuliskan pertanyaan (contoh: Pilih destinasi wisata kegiatan yang paling Anda minati:)">
            </div>

            <div>
              <label class="{{ $label }}">Deskripsi & Panduan Pemilihan Wisata <span class="text-grey-2 font-normal">(opsional)</span></label>
              <textarea x-model="form.wisata.description" rows="3" class="{{ $input }} resize-none"
                        placeholder="Informasi tambahan mengenai destinasi, fasilitas, atau ketentuan perjalanan..."></textarea>
            </div>
          </div>

          {{-- Daftar Opsi Tempat Wisata --}}
          <div class="mt-6 border-t border-grey-1 pt-5 space-y-4">
            <template x-for="(o, i) in form.wisata.options" :key="o.uid">
              <div class="rounded-xl border border-grey-1 bg-gray-50/50 p-5 shadow-xs">
                
                {{-- Header Opsi --}}
                <div class="flex items-center justify-between border-b border-grey-1 pb-3">
                  <div class="flex items-center gap-2">
                    <span class="flex h-6 w-6 items-center justify-center rounded-lg bg-primary-1 text-white text-[11px] font-bold"
                          x-text="i + 1"></span>
                    <span class="text-xs font-bold text-black" x-text="o.name ? o.name : 'Opsi Destinasi ' + (i + 1)"></span>
                  </div>

                  {{-- Tombol Hapus Konsisten Icon Silang X --}}
                  <template x-if="o.name.trim() !== '' || form.wisata.options.length > 1">
                    <button type="button" @click="removeWisata(i)"
                            class="flex h-7 w-7 items-center justify-center rounded-lg border border-grey-1 text-grey-2 hover:border-danger hover:text-danger hover:bg-rose-50 transition-colors cursor-pointer"
                            title="Hapus opsi ini">
                      <x-solar-close-circle-linear class="h-4 w-4" />
                    </button>
                  </template>
                </div>

                {{-- Field Nama & Kategori --}}
                <div class="mt-4 grid grid-cols-1 gap-3 sm:grid-cols-2">
                  <div>
                    <label class="{{ $label }}">Nama Destinasi Wisata</label>
                    <input type="text" x-model="o.name" class="{{ $input }}"
                           placeholder="Nama tempat (contoh: Pantai Kartini / Karimunjawa)">
                  </div>

                  <div>
                    <label class="{{ $label }}">Kategori Wisata</label>
                    <div class="relative">
                      <div x-show="!o.isCustom" class="relative" @click.outside="o.openDropdown = false">
                        <button type="button" @click="o.openDropdown = !o.openDropdown"
                                class="flex h-10 w-full items-center justify-between rounded-xl border border-grey-1 bg-white px-3 text-xs font-semibold text-black shadow-xs hover:border-primary-1 cursor-pointer">
                          <span x-text="o.label ? o.label : 'Pilih kategori wisata'"
                                :class="o.label ? 'text-black' : 'text-grey-2 font-normal'"
                                class="truncate"></span>
                          <x-solar-alt-arrow-down-linear class="h-3.5 w-3.5 shrink-0 text-grey-2 transition-transform duration-200"
                                                         ::class="o.openDropdown ? 'rotate-180 text-primary-1' : ''" />
                        </button>

                        <div x-show="o.openDropdown" x-cloak
                             class="absolute left-0 top-full z-40 mt-1 max-h-56 w-full overflow-y-auto rounded-xl border border-grey-1 bg-white p-1 shadow-lg">
                          <template x-for="cat in wisataCategories" :key="cat.value">
                            <button type="button" @click="selectWisataCategory(o, cat.value)"
                                    class="flex w-full items-center rounded-lg px-3 py-2 text-xs font-medium text-left transition-colors cursor-pointer"
                                    :class="o.label === cat.value ? 'bg-primary-1 text-white font-bold' : 'text-black hover:bg-gray-100'">
                              <span x-text="cat.label" class="truncate"></span>
                            </button>
                          </template>
                        </div>
                      </div>

                      <div x-show="o.isCustom" x-cloak class="relative flex items-center">
                        <input type="text" x-model="o.label" class="{{ $input }} pr-16"
                               placeholder="Ketik kategori manual...">
                        <button type="button" @click="o.isCustom = false; o.label = ''; o.openDropdown = true"
                                class="absolute right-2 text-[11px] font-bold text-primary-1 hover:underline cursor-pointer">
                          Pilih
                        </button>
                      </div>
                    </div>
                  </div>
                </div>

                {{-- Detail Lokasi, Jarak, Waktu Tempuh, Estimasi Biaya/Cashback --}}
                <div class="mt-3">
                  <label class="{{ $label }}">Alamat / Lokasi Lengkap</label>
                  <input type="text" x-model="o.lokasi" class="{{ $input }}"
                         placeholder="Lokasi (contoh: Jepara, Jawa Tengah)">
                </div>

                <div class="mt-3 grid grid-cols-1 gap-3 sm:grid-cols-3">
                  <div>
                    <label class="{{ $label }}">Jarak dari Kampus (Km)</label>
                    <input type="number" min="0" x-model="o.jarak" class="{{ $input }}" placeholder="Contoh: 120">
                  </div>
                  <div>
                    <label class="{{ $label }}">Estimasi Durasi Perjalanan</label>
                    <input type="text" x-model="o.lama" class="{{ $input }}" placeholder="Contoh: 2 Hari / 1 Hari">
                  </div>
                  <div>
                    <label class="{{ $label }}">Estimasi Biaya / Cashback (Rp)</label>
                    <input type="number" min="0" x-model="o.biaya" class="{{ $input }}" placeholder="Contoh: 500000">
                  </div>
                </div>

                {{-- Benefit / Fasilitas Paket --}}
                <div class="mt-3">
                  <label class="{{ $label }}">Fasilitas & Benefit Peserta</label>
                  <div class="flex flex-wrap items-center gap-1.5 rounded-xl border border-grey-1 bg-white p-2">
                    <template x-for="(b, bi) in o.benefits" :key="bi">
                      <span class="inline-flex items-center gap-1 rounded-md bg-primary-3/40 px-2.5 py-1 text-xs font-semibold text-primary-1">
                        <span x-text="b"></span>
                        <button type="button" @click="removeBenefit(o, bi)" class="text-danger hover:text-red-700 cursor-pointer ml-1">
                          &times;
                        </button>
                      </span>
                    </template>
                    <input type="text" x-model="o.benefitInput"
                           @keydown.enter.prevent="addBenefit(o)" @blur="addBenefit(o)"
                           class="min-w-[180px] flex-1 bg-transparent text-xs p-1 focus:outline-none"
                           placeholder="Ketik fasilitas lalu tekan Enter (contoh: Transportasi Bus, Makan, Penginapan)...">
                  </div>
                </div>

                {{-- UPLOAD FOTO TEMPAT WISATA (MAKSIMAL 3 FOTO) --}}
                <div class="mt-4 pt-3 border-t border-grey-1">
                  <div class="flex items-center justify-between mb-2">
                    <label class="{{ $label }} mb-0">Foto Destinasi Wisata <span class="text-grey-2 font-normal">(Maksimal 3 Foto, JPG/PNG maks 3MB)</span></label>
                    <span class="text-[11px] text-grey-deep">Akan ditampilkan di card pilihan wisata user</span>
                  </div>

                  <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                    <template x-for="(p, pIdx) in o.photos" :key="pIdx">
                      <div class="relative">
                        <label class="flex h-28 cursor-pointer items-center justify-center rounded-xl border border-dashed border-primary-1 bg-white hover:bg-primary-3/10 transition-colors overflow-hidden">
                          <template x-if="p.preview">
                            <img :src="p.preview" class="h-full w-full object-cover" alt="Foto Wisata">
                          </template>
                          <div x-show="!p.preview" class="text-center p-2 text-grey-2">
                            <x-solar-camera-linear class="mx-auto h-5 w-5 text-primary-1 mb-1" />
                            <span class="block text-[11px] font-semibold text-primary-1" x-text="'Foto ' + (pIdx + 1)"></span>
                            <span class="text-[10px] text-grey-2">Klik untuk unggah</span>
                          </div>
                          <input type="file" accept="image/*" class="hidden"
                                 :name="'wisata_image_' + o.uid + '_' + pIdx"
                                 @change="pickWisataPhoto(o, pIdx, $event)">
                        </label>
                        {{-- Hapus foto --}}
                        <template x-if="p.preview">
                          <button type="button" @click="removeWisataPhoto(o, pIdx)"
                                  class="absolute -top-1.5 -right-1.5 flex h-6 w-6 items-center justify-center rounded-full bg-white border border-danger/40 text-danger shadow-xs hover:bg-rose-50 cursor-pointer"
                                  title="Hapus foto ini">
                            <x-solar-close-circle-linear class="h-4 w-4" />
                          </button>
                        </template>
                      </div>
                    </template>
                  </div>
                </div>

              </div>
            </template>
          </div>

          {{-- Tombol Tambah Opsi CUKUP DI BAWAH --}}
          <div class="mt-4">
            <button type="button" @click="addWisata()"
                    class="btn-secondary h-9 px-4 text-xs font-semibold">
              <x-solar-add-circle-linear class="h-4 w-4 text-primary-1" />
              <span>+ Tambah Opsi Tempat Wisata</span>
            </button>
          </div>
        </x-card>

        {{-- 3. KATEGORI DESAIN KAOS & SIZE CHART --}}
        <x-card variant="panel" class="p-6">
          <div class="border-b border-grey-1 pb-3">
            <span class="text-[11px] font-bold text-primary-1 uppercase tracking-wider">Kategori 3</span>
            <h2 class="text-base font-bold text-black mt-0.5">Desain Kaos & Panduan Ukuran</h2>
            <p class="text-xs text-grey-2 mt-0.5">Pilihan desain baju seragam kegiatan beserta size chart.</p>
          </div>

          {{-- Pertanyaan & Deskripsi --}}
          <div class="mt-5 space-y-4">
            <div>
              <label class="{{ $label }}">Pertanyaan Kategori</label>
              <input type="text" x-model="form.kaos.question" class="{{ $input }}"
                     placeholder="Tuliskan pertanyaan (contoh: Tentukan pilihan desain kaos seragam kegiatan:)">
            </div>

            <div>
              <label class="{{ $label }}">Deskripsi & Ketentuan Ukuran Kaos <span class="text-grey-2 font-normal">(opsional)</span></label>
              <textarea x-model="form.kaos.description" rows="3" class="{{ $input }} resize-none"
                        placeholder="Informasi jenis bahan kain kaos, ketentuan pembagian ukuran, atau catatan ukuran keluarga..."></textarea>
            </div>
          </div>

          {{-- Mode Size Chart Toggle & Fleksibilitas Ukuran --}}
          <div class="mt-5 rounded-xl border border-grey-1 bg-gray-50/60 p-4">
            <span class="block text-xs font-bold text-black mb-2">Metode Size Chart:</span>
            <div class="flex flex-wrap items-center gap-5">
              <label class="flex items-center gap-2 text-xs font-semibold text-black cursor-pointer">
                <input type="radio" value="manual" x-model="form.kaos.chart_mode" name="chart_mode" class="accent-primary-1">
                <span>Tabel Ukuran Manual (cm)</span>
              </label>
              <label class="flex items-center gap-2 text-xs font-semibold text-black cursor-pointer">
                <input type="radio" value="image" x-model="form.kaos.chart_mode" name="chart_mode" class="accent-primary-1">
                <span>Unggah Gambar Panduan Ukuran</span>
              </label>
            </div>

            {{-- Mode Manual: Pilihan Ukuran & Input Ukuran Kustom Baru --}}
            <div x-show="form.kaos.chart_mode === 'manual'" class="mt-4 pt-3 border-t border-grey-1/60">
              <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 mb-2">
                <label class="{{ $label }} mb-0">Ukuran Standar yang Disediakan</label>
                <div class="flex items-center gap-2">
                  <input type="text" x-model="customSizeInput"
                         @keydown.enter.prevent="addCustomSize()"
                         placeholder="Tambah size lain (mis: 4XL/Anak-S)"
                         class="h-8 rounded-lg border border-grey-1 bg-white px-2.5 text-xs focus:border-primary-1 focus:outline-none w-48">
                  <button type="button" @click="addCustomSize()" class="btn-secondary h-8 px-2.5 text-xs font-semibold">
                    + Tambah
                  </button>
                </div>
              </div>

              <div class="flex flex-wrap gap-2">
                <template x-for="s in allSizes" :key="s">
                  <button type="button" @click="toggleSize(s)"
                          class="h-8 min-w-[36px] px-3 rounded-xl text-xs font-bold transition-all cursor-pointer"
                          :class="form.kaos.sizes.includes(s) ? 'btn-primary' : 'btn-secondary'"
                          x-text="s"></button>
                </template>
              </div>

              {{-- Checkbox Opsi: Izinkan User Input Ukuran Sendiri jika Tidak Ada di List --}}
              <div class="mt-3.5 flex items-center gap-2 bg-white p-2.5 rounded-xl border border-grey-1">
                <input type="checkbox" id="allow_custom_size" x-model="form.kaos.allow_custom_user_size" class="h-4 w-4 accent-primary-1 cursor-pointer">
                <label for="allow_custom_size" class="text-xs font-semibold text-black cursor-pointer select-none">
                  Izinkan peserta menambahkan ukuran kustom sendiri saat voting jika ukuran yang dicari tidak tersedia
                </label>
              </div>

              <div class="mt-3 overflow-x-auto rounded-xl border border-grey-1 bg-white p-2">
                <table class="w-full min-w-[380px] text-xs">
                  <thead>
                    <tr class="bg-gray-50 text-grey-deep border-b border-gray-100">
                      <th class="py-2 px-3 text-center font-bold">UKURAN</th>
                      <th class="py-2 px-3 text-center font-bold">LEBAR DADA (CM)</th>
                      <th class="py-2 px-3 text-center font-bold">PANJANG BADAN (CM)</th>
                    </tr>
                  </thead>
                  <tbody>
                    <template x-for="s in activeSizes" :key="s">
                      <tr class="border-b border-gray-50">
                        <td class="py-2 px-3 text-center font-bold text-primary-1" x-text="s"></td>
                        <td class="py-2 px-3">
                          <input type="number" min="0" x-model="form.kaos.chart[s].chest"
                                 class="h-8 w-full rounded-lg border border-grey-1 px-2 text-center text-xs focus:border-primary-1 focus:outline-none"
                                 placeholder="Lebar">
                        </td>
                        <td class="py-2 px-3">
                          <input type="number" min="0" x-model="form.kaos.chart[s].length"
                                 class="h-8 w-full rounded-lg border border-grey-1 px-2 text-center text-xs focus:border-primary-1 focus:outline-none"
                                 placeholder="Panjang">
                        </td>
                      </tr>
                    </template>
                  </tbody>
                </table>
              </div>
            </div>

            {{-- Mode Gambar: Upload File --}}
            <div x-show="form.kaos.chart_mode === 'image'" class="mt-4 pt-3 border-t border-grey-1/60">
              <label class="{{ $label }}">Berkas Gambar Panduan Ukuran</label>
              <label class="flex h-36 cursor-pointer items-center justify-center rounded-xl border border-dashed border-primary-1 bg-white hover:bg-primary-3/10 transition-colors">
                <template x-if="form.kaos.chart_image_preview">
                  <img :src="form.kaos.chart_image_preview" class="h-full w-full object-contain p-2" alt="Preview Chart">
                </template>
                <div x-show="!form.kaos.chart_image_preview" class="text-center p-3">
                  <x-solar-gallery-add-linear class="mx-auto h-6 w-6 text-primary-1 mb-1" />
                  <span class="block text-xs font-semibold text-primary-1">Pilih gambar panduan ukuran (PNG/JPG maks 3 MB)</span>
                </div>
                <input type="file" name="kaos_chart_image" accept="image/*" class="hidden" @change="pickChartImage($event)">
              </label>

              {{-- Checkbox Opsi Ukuran Kustom pada Mode Gambar juga --}}
              <div class="mt-3.5 flex items-center gap-2 bg-white p-2.5 rounded-xl border border-grey-1">
                <input type="checkbox" id="allow_custom_size_img" x-model="form.kaos.allow_custom_user_size" class="h-4 w-4 accent-primary-1 cursor-pointer">
                <label for="allow_custom_size_img" class="text-xs font-semibold text-black cursor-pointer select-none">
                  Izinkan peserta menambahkan ukuran kustom sendiri saat voting jika ukuran yang dicari tidak tersedia
                </label>
              </div>
            </div>
          </div>

          {{-- Daftar Opsi Desain Kaos --}}
          <div class="mt-6 border-t border-grey-1 pt-5">
            <label class="{{ $label }}">Pilihan Desain Kaos</label>
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
              <template x-for="(o, i) in form.kaos.options" :key="o.uid">
                <div class="rounded-xl border border-grey-1 bg-gray-50/50 p-4 shadow-xs">
                  <div class="flex items-center justify-between border-b border-grey-1 pb-2">
                    <span class="text-xs font-bold text-black" x-text="'Pilihan Desain ' + (i + 1)"></span>
                    
                    {{-- Tombol Hapus Konsisten Icon Silang X --}}
                    <template x-if="o.name.trim() !== '' || form.kaos.options.length > 1">
                      <button type="button" @click="removeKaos(i)"
                              class="flex h-7 w-7 items-center justify-center rounded-lg border border-grey-1 text-grey-2 hover:border-danger hover:text-danger hover:bg-rose-50 transition-colors cursor-pointer"
                              title="Hapus desain ini">
                        <x-solar-close-circle-linear class="h-4 w-4" />
                      </button>
                    </template>
                  </div>

                  <div class="mt-3">
                    <label class="{{ $label }}">Nama / Tema Desain</label>
                    <input type="text" x-model="o.name" class="{{ $input }}"
                           placeholder="Nama desain (contoh: Polo Navy Gathering)">
                  </div>

                  {{-- UPLOAD FOTO DESAIN KAOS (MAKSIMAL 3 FOTO) --}}
                  <div class="mt-3 pt-2">
                    <div class="flex items-center justify-between mb-1.5">
                      <label class="{{ $label }} mb-0">Foto Mockup Desain <span class="text-grey-2 font-normal">(Maksimal 3 Foto)</span></label>
                      <span class="text-[10px] text-grey-deep">Tampak Depan, Belakang, Detail</span>
                    </div>

                    <div class="grid grid-cols-3 gap-2">
                      <template x-for="(kp, kpIdx) in o.photos" :key="kpIdx">
                        <div class="relative">
                          <label class="flex h-24 cursor-pointer items-center justify-center rounded-xl border border-dashed border-primary-1 bg-white hover:bg-primary-3/10 transition-colors overflow-hidden">
                            <template x-if="kp.preview">
                              <img :src="kp.preview" class="h-full w-full object-cover" alt="Mockup Kaos">
                            </template>
                            <div x-show="!kp.preview" class="text-center p-1 text-grey-2">
                              <x-solar-camera-linear class="mx-auto h-4 w-4 text-primary-1 mb-0.5" />
                              <span class="block text-[10px] font-semibold text-primary-1" x-text="'Foto ' + (kpIdx + 1)"></span>
                            </div>
                            <input type="file" accept="image/*" class="hidden"
                                   :name="'kaos_image_' + o.uid + '_' + kpIdx"
                                   @change="pickKaosPhoto(o, kpIdx, $event)">
                          </label>
                          {{-- Tombol Hapus Foto X --}}
                          <template x-if="kp.preview">
                            <button type="button" @click="removeKaosPhoto(o, kpIdx)"
                                    class="absolute -top-1 -right-1 flex h-5 w-5 items-center justify-center rounded-full bg-white border border-danger/40 text-danger shadow-xs hover:bg-rose-50 cursor-pointer"
                                    title="Hapus foto ini">
                              <x-solar-close-circle-linear class="h-3.5 w-3.5" />
                            </button>
                          </template>
                        </div>
                      </template>
                    </div>
                  </div>

                </div>
              </template>
            </div>

            {{-- Tombol Tambah Opsi CUKUP DI BAWAH --}}
            <div class="mt-4">
              <button type="button" @click="addKaos()"
                      class="btn-secondary h-9 px-4 text-xs font-semibold">
                <x-solar-add-circle-linear class="h-4 w-4 text-primary-1" />
                <span>+ Tambah Desain Kaos</span>
              </button>
            </div>
          </div>
        </x-card>

        {{-- Navigasi Kembali ke Tahap 1 --}}
        <div class="flex justify-between items-center pt-2">
          <button type="button" @click="step = 1" class="btn-secondary h-10 px-5 text-xs font-semibold">
            <x-solar-arrow-left-linear class="h-4 w-4" />
            <span>Kembali ke Tahap 1</span>
          </button>
        </div>

      </div>

    </div>

    {{-- ============================================================
         KOLOM KANAN: SIDEBAR AKSI STICKY
         ============================================================ --}}
    <aside class="sticky top-[100px] space-y-4">
      <x-card variant="panel" class="p-5">
        <h3 class="text-xs font-bold uppercase tracking-wider text-black border-b border-grey-1 pb-3">
          Aksi Polling
        </h3>

        <div class="mt-4 space-y-2.5">
          <button type="button"
                  @click="askPublish()"
                  class="btn-primary h-11 w-full text-xs font-bold shadow-primary-glow">
            <x-solar-upload-linear class="h-4 w-4" />
            <span>Post Polling Sekarang</span>
          </button>

          <button type="button"
                  @click="submit('draft')"
                  class="btn-secondary h-11 w-full text-xs font-semibold">
            <x-solar-diskette-linear class="h-4 w-4 text-primary-1" />
            <span>Simpan sebagai Draft</span>
          </button>

          <button type="button"
                  @click="askCancel()"
                  class="btn-danger h-10 w-full text-xs font-semibold">
            <span>Batalkan Perubahan</span>
          </button>
        </div>

        <div class="mt-4 pt-3 border-t border-grey-1 text-[11px] text-grey-2 leading-relaxed">
          <p><span class="font-bold text-black">Catatan:</span> Polling yang diposting akan langsung dapat dilihat dan dipilih oleh sivitas sesuai jadwal yang ditentukan.</p>
        </div>
      </x-card>

      {{-- Ringkasan Status Tahapan --}}
      <x-card variant="panel" class="p-4 bg-gray-50/60">
        <span class="text-[11px] font-bold text-grey-deep uppercase tracking-wider block mb-2">Progres Form:</span>
        <div class="space-y-2 text-xs">
          <div class="flex items-center justify-between">
            <span class="text-black">1. Identitas & Waktu</span>
          <span class="font-bold" :class="(form.identity.title && form.identity.start.date && form.identity.end.date) ? 'text-emerald-600' : (form.identity.title ? 'text-amber-500' : 'text-grey-2')"
                  x-text="(form.identity.title && form.identity.start.date && form.identity.end.date) ? 'Lengkap' : (form.identity.title ? 'Belum ada jadwal' : 'Kosong')"></span>
          </div>
          <div class="flex items-center justify-between">
            <span class="text-black">2. Kategori Peserta</span>
            <span class="font-bold" :class="form.peserta.options.some(o => o.label) ? 'text-emerald-600' : 'text-grey-2'"
                  x-text="form.peserta.options.filter(o => o.label).length + ' Opsi'"></span>
          </div>
          <div class="flex items-center justify-between">
            <span class="text-black">3. Tempat Wisata</span>
            <span class="font-bold" :class="form.wisata.options.some(o => o.name) ? 'text-emerald-600' : 'text-grey-2'"
                  x-text="form.wisata.options.filter(o => o.name).length + ' Opsi'"></span>
          </div>
          <div class="flex items-center justify-between">
            <span class="text-black">4. Desain Kaos</span>
            <span class="font-bold" :class="form.kaos.options.some(o => o.name) ? 'text-emerald-600' : 'text-grey-2'"
                  x-text="form.kaos.options.filter(o => o.name).length + ' Opsi'"></span>
          </div>
        </div>
      </x-card>
    </aside>

  </div>

  {{-- ================= MODAL KONFIRMASI ================= --}}
  <div x-show="modal" x-cloak
       class="fixed inset-0 z-50 flex items-center justify-center p-4"
       style="background-color: rgba(0,0,0,0.18); backdrop-filter: blur(3px); -webkit-backdrop-filter: blur(3px);"
       x-on:keydown.escape.window="modal = null">
    <div class="w-full max-w-[380px] rounded-2xl bg-white p-6"
         style="box-shadow: 0 8px 40px 0 rgba(0,0,0,.12), 0 2px 8px 0 rgba(0,0,0,.08);"
         x-on:click.outside="modal = null">

      {{-- Modal: Batalkan Perubahan --}}
      <template x-if="modal === 'cancel'">
        <div>
          <div class="mx-auto mb-4 flex h-12 w-12 items-center justify-center rounded-2xl bg-rose-50 border border-danger/20">
            <x-solar-close-circle-linear class="h-6 w-6 text-danger" />
          </div>
          <h3 class="text-center text-sm font-bold text-black">Batalkan Pembuatan Polling?</h3>
          <p class="mt-2 text-center text-xs text-grey-deep leading-relaxed">Anda akan diarahkan kembali ke halaman daftar polling. Semua isian yang belum disimpan akan hilang.</p>
          <div class="mt-3 rounded-xl bg-amber-50 border border-amber-200 px-3 py-2.5">
            <p class="text-center text-xs text-amber-700 font-medium">💡 Simpan sebagai Draft agar data tidak hilang dan bisa dilanjutkan nanti.</p>
          </div>
          <div class="mt-5 flex justify-center gap-2">
            <button type="button" class="btn-secondary h-9 px-4 text-xs font-semibold" x-on:click="modal = null">Kembali</button>
            <button type="button" class="btn-secondary h-9 px-4 text-xs font-semibold" x-on:click="submit('draft')">Simpan Draft</button>
            <button type="button" class="btn-danger h-9 px-4 text-xs font-semibold" x-on:click="confirmCancel()">Ya, Batalkan</button>
          </div>
        </div>
      </template>

      {{-- Modal: Post Polling --}}
      <template x-if="modal === 'publish'">
        <div>
          <div class="mx-auto mb-4 flex h-12 w-12 items-center justify-center rounded-2xl bg-primary-3/30 border border-primary-1/20">
            <x-solar-upload-linear class="h-6 w-6 text-primary-1" />
          </div>
          <h3 class="text-center text-sm font-bold text-black">Publikasikan Polling Ini?</h3>
          <p class="mt-2 text-center text-xs text-grey-deep leading-relaxed">Polling akan resmi diterbitkan dan dapat diikuti oleh seluruh sivitas sesuai jadwal yang ditentukan.</p>
          <div class="mt-3 rounded-xl bg-primary-3/20 border border-primary-1/20 px-3 py-2.5">
            <p class="text-center text-xs text-primary-1 font-medium">⚡ Pastikan semua data sudah benar — polling aktif tidak dapat diubah.</p>
          </div>
          <div class="mt-5 flex justify-center gap-2">
            <button type="button" class="btn-secondary h-9 px-4 text-xs font-semibold" x-on:click="modal = null">Periksa Lagi</button>
            <button type="button" class="btn-primary h-9 px-5 text-xs font-semibold" x-on:click="submit('publish')">Ya, Publikasikan</button>
          </div>
        </div>
      </template>

    </div>
  </div>

</form>
@endsection
