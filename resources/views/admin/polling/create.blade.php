@extends('layouts.focus')

@section('title', 'Form Pembuatan Polling')
@section('page_title', 'Form Pembuatan Polling')

@section('content')
@php
  $input  = 'w-full rounded-sm bg-white px-4 py-2.5 text-sm text-black shadow-type-3 placeholder:text-grey-2 focus:outline-none focus:ring-2 focus:ring-primary-1';
  $label  = 'block text-sm font-semibold text-primary-1';
  $ghost  = 'rounded-button border border-grey-1 bg-white px-4 py-2 text-xs font-semibold text-grey-2 hover:bg-gray-50 disabled:pointer-events-none disabled:opacity-40';
  $prim   = 'rounded-button bg-primary-1 px-6 py-2.5 text-sm font-semibold text-white hover:bg-primary-4';
  $outl   = 'rounded-button border border-primary-1 bg-white px-5 py-2.5 text-sm font-semibold text-primary-1 hover:bg-primary-3';
  $cancel = 'rounded-button px-5 py-2.5 text-sm font-semibold text-danger hover:bg-danger-soft/40';
  $panel  = 'rounded-md bg-primary-3 p-5';
  $tab    = 'rounded-button px-8 py-2.5 text-sm font-semibold text-primary-1';
@endphp

<link rel="stylesheet" href="{{ asset('vendor/quill.snow.css') }}">
<script src="{{ asset('vendor/quill.js') }}"></script>

<style>
  .ql-toolbar.ql-snow { border: 0; border-bottom: 1px solid #DBDBDB; }
  .ql-container.ql-snow { border: 0; font-family: inherit; font-size: .875rem; }
  .ql-editor { min-height: 110px; }
</style>

<script>
  window.initPollQuill = () => {
    if (typeof Quill === 'undefined') return;
    document.querySelectorAll('[data-editor]').forEach(el => {
      if (el.__quill_mounted) return;
      el.__quill_mounted = true;
      const path = el.dataset.path;
      const initial = el.dataset.initial || '';
      const q = new Quill(el, {
        theme: 'snow',
        placeholder: el.dataset.placeholder || '',
        modules: {
          toolbar: [
            ['bold', 'italic', 'underline', 'strike'],
            [{ list: 'ordered' }, { list: 'bullet' }],
            [{ header: [2, 3, false] }],
            [{ color: [] }, { background: [] }],
            ['link'],
            ['clean'],
          ],
        },
      });
      if (initial) q.clipboard.dangerouslyPasteHTML(initial);
      q.on('text-change', () => {
        const val = q.getText().trim() === '' ? '' : q.root.innerHTML;
        if (path && window.__pollFormScope) {
          const parts = path.split('.');
          let target = window.__pollFormScope;
          for (let i = 0; i < parts.length - 1; i++) {
            target = target[parts[i]];
          }
          target[parts[parts.length - 1]] = val;
        }
      });
    });
  };

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
      allSizes: ['XS', 'S', 'M', 'L', 'XL', 'XXL', '3XL'],
      
      // Memory / Master Data Tempat Wisata Tersimpan (Katalog)
      wisataLibrary: [
        {
          id: 1,
          name: 'Pantai Kartini',
          label: 'Pantai',
          lokasi: 'Pantai Kartini, Jepara, Jawa Tengah.',
          jarak: 120,
          lama: '2 Hari',
          biaya: 500000,
          rating: 4.8,
          dikunjungi: 2,
          benefits: ['Bis', 'Makan', 'Minum', 'Kamar 2 hari.'],
          description: 'Wisata pantai eksotis dengan pemandangan sunset dan wahana kura-kura raksasa.',
          tags: ['Pantai', 'Jepara', 'Keluarga']
        },
        {
          id: 2,
          name: 'Karimunjawa',
          label: 'Pantai',
          lokasi: 'Kepulauan Karimunjawa, Jepara, Jawa Tengah.',
          jarak: 180,
          lama: '3 Hari',
          biaya: 850000,
          rating: 4.9,
          dikunjungi: 1,
          benefits: ['Kapal Express', 'Snorkeling', 'Penginapan AC', 'Makan 6x', 'Dokumentasi Underwater'],
          description: 'Surga bawah laut dan gugusan pulau tropis yang memukau.',
          tags: ['Snorkeling', 'Kepulauan', 'Eksotis']
        },
        {
          id: 3,
          name: 'Candi Borobudur & VW Safari',
          label: 'Budaya',
          lokasi: 'Borobudur, Magelang, Jawa Tengah.',
          jarak: 45,
          lama: '1 Hari',
          biaya: 350000,
          rating: 4.7,
          dikunjungi: 0,
          benefits: ['VW Safari Trip', 'Tiket Masuk Borobudur', 'Makan Siang Resto Lokal', 'Snack Box'],
          description: 'Keliling desa wisata menikmati pemandangan alam dan sejarah candi megah.',
          tags: ['Budaya', 'Edukasi', 'Magelang']
        },
        {
          id: 4,
          name: 'Kebun Teh Kemuning & Lawu Park',
          label: 'Gunung',
          lokasi: 'Karanganyar, Lereng Gunung Lawu, Jawa Tengah.',
          jarak: 95,
          lama: '1 Hari',
          biaya: 300000,
          rating: 4.6,
          dikunjungi: 1,
          benefits: ['Jeep Tour', 'Makan Siang Kebun', 'Tiket Semua Wahana', 'Pemandu'],
          description: 'Hawa dingin pegunungan asri dengan hamparan kebun teh dan wahana outdoor.',
          tags: ['Gunung', 'Alam', 'Dingin']
        }
      ],

      form: {
        identity: {
          title: '', description: '',
          start: { time: '08:00', date: '' },
          end:   { time: '08:00', date: '' },
        },
        peserta: {
          question: '',
          options: [
            { uid: 1, label: '' },
            { uid: 2, label: '' }
          ]
        },
        wisata: {
          question: '',
          options: [
            {
              uid: 3,
              name: 'Pantai Kartini',
              label: 'Pantai',
              lokasi: 'Pantai Kartini, Jepara, Jawa Tengah.',
              jarak: 120,
              lama: '2 Hari',
              biaya: 500000,
              rating: 4.8,
              dikunjungi: 2,
              benefits: ['Bis', 'Makan', 'Minum', 'Kamar 2 hari.'],
              benefitInput: '',
              description: '',
              tags: ['Pantai', 'Jepara'],
              tagInput: ''
            },
            {
              uid: 4,
              name: '',
              label: '',
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
              tagInput: ''
            },
          ],
        },
        kaos: {
          question: '',
          chart_mode: 'manual', // 'manual' (tabel angka) atau 'image' (upload gambar)
          chart_image_preview: '',
          sizes: ['S', 'M', 'L', 'XL'],
          chart: {
            XS: { chest: '', length: '' }, S: { chest: '', length: '' }, M: { chest: '', length: '' },
            L: { chest: '', length: '' }, XL: { chest: '', length: '' }, XXL: { chest: '', length: '' },
            '3XL': { chest: '', length: '' },
          },
          options: [
            { uid: 5, name: '', preview: '' },
            { uid: 6, name: '', preview: '' },
          ],
        },
      },

      init() {
        window.__pollFormScope = this.form;
        this.$watch('form', () => { this.dirty = true }, { deep: true });
        window.addEventListener('beforeunload', (e) => {
          if (this.dirty && !this.leaving) { e.preventDefault(); e.returnValue = ''; }
        });
        this.$nextTick(() => window.initPollQuill && window.initPollQuill());
      },

      payload() {
        return JSON.stringify(this.form, (k, v) => (k === 'preview' || k === 'tagInput' || k === 'benefitInput' || k === 'chart_image_preview') ? undefined : v);
      },

      // Dinamis Opsi Peserta (bisa berapapun)
      addPeserta() {
        this.form.peserta.options.push({ uid: ++this.uid, label: '' });
      },
      removePeserta(i) {
        if (this.form.peserta.options.length > 2) {
          this.form.peserta.options.splice(i, 1);
        }
      },

      // Wisata Dinamis & Memory
      showWisataMemory: false,
      selectedLibraryIds: [1], // id 1 (Pantai Kartini) default terpilih

      toggleLibraryItem(libItem) {
        const idx = this.selectedLibraryIds.indexOf(libItem.id);
        if (idx > -1) {
          // Uncheck: hapus dari selected dan hapus opsi dari form jika ada
          this.selectedLibraryIds.splice(idx, 1);
          const optIdx = this.form.wisata.options.findIndex(o => o.memoryId === libItem.id);
          if (optIdx > -1 && this.form.wisata.options.length > 2) {
            this.form.wisata.options.splice(optIdx, 1);
          } else if (optIdx > -1) {
            // Jika sisa 2, kosongkan datanya saja
            this.form.wisata.options[optIdx].name = '';
            this.form.wisata.options[optIdx].memoryId = null;
          }
        } else {
          // Check: tambahkan ke selected dan masukkan ke form opsi
          this.selectedLibraryIds.push(libItem.id);
          // Cari apakah ada slot opsi kosong
          let targetOpt = this.form.wisata.options.find(o => !o.name);
          if (!targetOpt) {
            // Jika semua opsi terisi, otomatis tambah opsi baru
            this.addWisata();
            targetOpt = this.form.wisata.options[this.form.wisata.options.length - 1];
          }
          this.applyFromLibrary(targetOpt, libItem);
          targetOpt.memoryId = libItem.id;
        }
      },

      addWisata() {
        this.form.wisata.options.push({
          uid: ++this.uid,
          memoryId: null,
          name: '',
          label: '',
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
          tagInput: ''
        });
      },
      removeWisata(i) {
        if (this.form.wisata.options.length > 2) {
          this.form.wisata.options.splice(i, 1);
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

      // Kaos Dinamis & Chart Toggle
      get activeSizes() { return this.allSizes.filter(s => this.form.kaos.sizes.includes(s)) },
      toggleSize(s) {
        const a = this.form.kaos.sizes;
        this.form.kaos.sizes = a.includes(s) ? a.filter(x => x !== s) : [...a, s];
      },
      addKaos() { this.form.kaos.options.push({ uid: ++this.uid, name: '', preview: '' }) },
      removeKaos(i) { if (this.form.kaos.options.length > 2) this.form.kaos.options.splice(i, 1) },
      pickImage(o, e) {
        const f = e.target.files[0];
        o.preview = f ? URL.createObjectURL(f) : '';
      },
      pickChartImage(e) {
        const f = e.target.files[0];
        this.form.kaos.chart_image_preview = f ? URL.createObjectURL(f) : '';
      },

      // Validasi
      validate(mode) {
        const f = this.form, e = [];
        if (!f.identity.title.trim()) e.push({ step: 1, msg: 'Judul voting wajib diisi.' });
        if (mode === 'draft') return e;

        if (!f.identity.start.date || !f.identity.end.date) e.push({ step: 1, msg: 'Tanggal mulai dan selesai wajib diisi.' });
        else if (f.identity.end.date < f.identity.start.date) e.push({ step: 1, msg: 'Tanggal selesai tidak boleh sebelum tanggal mulai.' });
        
        // Validasi Peserta
        if (!f.peserta.question) e.push({ step: 2, msg: 'Pertanyaan kategori Peserta wajib diisi.' });
        if (f.peserta.options.some(o => !o.label.trim())) e.push({ step: 2, msg: 'Semua pilihan kategori Peserta wajib diisi.' });
        
        // Validasi Wisata
        if (!f.wisata.question) e.push({ step: 2, msg: 'Pertanyaan kategori Tempat Wisata wajib diisi.' });
        if (f.wisata.options.some(o => !o.name.trim())) e.push({ step: 2, msg: 'Semua opsi Tempat Wisata wajib punya nama.' });
        
        // Validasi Kaos
        if (!f.kaos.question) e.push({ step: 2, msg: 'Pertanyaan kategori Desain Kaos wajib diisi.' });
        if (f.kaos.chart_mode === 'manual' && f.kaos.sizes.length === 0) e.push({ step: 2, msg: 'Pilih minimal satu ukuran kaos untuk tabel size chart.' });
        if (f.kaos.options.some(o => !o.name.trim())) e.push({ step: 2, msg: 'Semua desain kaos wajib punya nama.' });
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
        if (!this.dirty) return this.confirmCancel();
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
    if (window.Alpine) {
      registerPollForm();
    }
    setTimeout(() => {
      window.initPollQuill && window.initPollQuill();
    }, 50);
  });
</script>

<form method="POST" action="{{ route('admin.polling.store') }}" enctype="multipart/form-data"
      data-cancel-url="{{ route('admin.polling.arsip') }}"
      x-data="pollForm" x-ref="form" x-on:submit.prevent>
  @csrf
  <input type="hidden" name="payload" x-bind:value="payload()">
  <input type="hidden" name="action" x-bind:value="action">

  <x-card variant="paper" class="p-6">

    {{-- Tab tahap + aksi --}}
    <div class="flex flex-wrap items-center justify-between gap-3">
      <div class="flex gap-3">
        <button type="button" class="{{ $tab }}" x-on:click="step = 1"
                x-bind:class="step === 1 ? 'border border-primary-1 bg-white' : 'bg-white shadow-type-3'">Tahap 1</button>
        <button type="button" class="{{ $tab }}" x-on:click="step = 2"
                x-bind:class="step === 2 ? 'border border-primary-1 bg-white' : 'bg-white shadow-type-3'">Tahap 2</button>
      </div>

      <div class="flex flex-wrap items-center gap-2">
        <button type="button" class="{{ $cancel }}" x-on:click="askCancel()">Batalkan</button>
        <button type="button" class="{{ $outl }}" x-on:click="submit('draft')">Simpan Draft</button>
        <button type="button" class="{{ $prim }} shadow-type-1" x-on:click="askPublish()">Post Polling</button>
      </div>
    </div>

    {{-- Error server --}}
    @if ($errors->any())
      <div class="mt-4 rounded-sm bg-danger-soft p-4 text-xs text-black">
        <ul class="list-inside list-disc">
          @foreach ($errors->all() as $err) <li>{{ $err }}</li> @endforeach
        </ul>
      </div>
    @endif
    {{-- Error klien --}}
    <div x-show="errors.length" x-cloak class="mt-4 rounded-sm bg-danger-soft p-4 text-xs text-black">
      <ul class="list-inside list-disc">
        <template x-for="e in errors"><li x-text="e.msg"></li></template>
      </ul>
    </div>

    {{-- ================= TAHAP 1: IDENTITAS ================= --}}
    <div x-show="step === 1" x-cloak class="mt-5 {{ $panel }}">
      <h2 class="text-2xl font-bold text-primary-1">Identitas Voting</h2>
      <p class="mt-1 text-xs text-black">Informasi utama mengenai topik pemilihan dan pedoman pemilih.</p>

      <div class="mt-6 space-y-5">
        <div>
          <label class="{{ $label }}">Judul Voting</label>
          <input type="text" x-model="form.identity.title" class="mt-2 {{ $input }}"
                 placeholder="Piknik & Gathering Tahunan DTEDI SV UGM 2026">
        </div>

        <div>
          <label class="{{ $label }}">Deskripsi</label>
          <div class="mt-2">
            <x-rich-editor path="form.identity.description" placeholder="Jelaskan tujuan voting ini..." />
          </div>
        </div>

        <div>
          <label class="{{ $label }}">Durasi Voting</label>
          <div class="mt-2 grid grid-cols-1 gap-4 md:grid-cols-2">
            @foreach (['start' => 'Mulai', 'end' => 'Selesai'] as $key => $title)
              <div class="rounded-xl border border-gray-100 bg-white p-4 shadow-sm">
                <div class="flex items-center gap-2">
                  <span class="flex h-5 w-5 items-center justify-center rounded-full bg-primary-3 text-primary-1 text-[10px] font-bold">
                    {{ $loop->iteration }}
                  </span>
                  <p class="text-xs font-bold uppercase tracking-wider text-primary-1">Waktu {{ $title }} Voting</p>
                </div>
                <div class="mt-3 grid grid-cols-1 gap-3 sm:grid-cols-2">
                  <div>
                    <label class="block text-[11px] font-medium text-grey-2 mb-1">Jam (WIB)</label>
                    <x-timepicker ::value="form.identity.{{ $key }}.time"
                                  @input="form.identity.{{ $key }}.time = $event.detail" />
                  </div>
                  <div>
                    <label class="block text-[11px] font-medium text-grey-2 mb-1">Tanggal</label>
                    <x-datepicker ::value="form.identity.{{ $key }}.date"
                                  @input="form.identity.{{ $key }}.date = $event.detail" />
                  </div>
                </div>
              </div>
            @endforeach
          </div>
        </div>
      </div>

      <div class="mt-6 flex justify-end">
        <button type="button" class="{{ $prim }}" x-on:click="step = 2">Lanjut ke Tahap 2</button>
      </div>
    </div>

    {{-- ================= TAHAP 2: BUAT POLLING ================= --}}
    <div x-show="step === 2" x-cloak class="mt-5 space-y-6">

      {{-- 1. Kategori Peserta (Dibuat Dinamis) --}}
      <section class="{{ $panel }}">
        <div class="flex items-center justify-between">
          <div>
            <h2 class="text-xl font-bold text-primary-1">1. Rombongan Peserta</h2>
            <p class="mt-1 text-xs text-black">Opsi peserta dinamis (bisa ditambah berapapun sesuai kebutuhan kegiatan).</p>
          </div>
          <button type="button" class="{{ $ghost }}" x-on:click="addPeserta()">+ Tambah Pilihan</button>
        </div>

        <label class="{{ $label }} mt-5">Pertanyaan</label>
        <div class="mt-2">
          <x-rich-editor path="form.peserta.question" placeholder="Tulis pertanyaan untuk kategori Peserta..." />
        </div>

        <label class="{{ $label }} mt-5">Pilihan Opsi</label>
        <div class="mt-2 grid grid-cols-1 gap-3 md:grid-cols-2">
          <template x-for="(o, i) in form.peserta.options" x-bind:key="o.uid">
            <div class="flex items-center gap-2">
              <span class="flex size-9 shrink-0 items-center justify-center rounded-sm border border-primary-1 bg-white text-xs font-semibold text-primary-1"
                    x-text="i + 1"></span>
              <input type="text" x-model="o.label" class="{{ $input }}"
                     x-bind:placeholder="'Opsi ' + (i + 1) + ' (misal: Dosen & Tendik dipisah)'">
              <button type="button" class="text-danger p-2 hover:bg-danger-soft/30 rounded-sm disabled:opacity-20"
                      x-bind:disabled="form.peserta.options.length <= 2"
                      x-on:click="removePeserta(i)" title="Hapus opsi">&times;</button>
            </div>
          </template>
        </div>
      </section>

      {{-- 2. Kategori Tempat Wisata (Lengkap dengan Memory Library + Field Gambar) --}}
      <section class="{{ $panel }}">
        <div class="flex flex-wrap items-center justify-between gap-3">
          <div>
            <h2 class="text-xl font-bold text-primary-1">2. Tempat Wisata</h2>
            <p class="mt-1 text-xs text-black">Kelola opsi destinasi wisata. Pilih destinasi dari riwayat kegiatan lalu atau buat opsi baru.</p>
          </div>
          <div class="flex items-center gap-2">
            {{-- Tombol Buka Panel Riwayat / Memory --}}
            <button type="button"
                    x-on:click="showWisataMemory = !showWisataMemory"
                    class="inline-flex items-center gap-2 rounded-sm border border-primary-1 bg-white px-3.5 py-2 text-xs font-semibold text-primary-1 shadow-xs hover:bg-primary-3/30 transition-all cursor-pointer">
              <x-solar-history-linear class="h-4 w-4" />
              <span x-text="showWisataMemory ? 'Tutup Riwayat Destinasi' : 'Pilih dari Riwayat Destinasi'"></span>
              <x-solar-alt-arrow-down-linear class="h-3.5 w-3.5 transition-transform duration-200"
                                             ::class="showWisataMemory ? 'rotate-180' : ''" />
            </button>

            <button type="button" class="{{ $ghost }}" x-on:click="addWisata()">+ Tambah Opsi Manual</button>
          </div>
        </div>

        {{-- SPACE / DRAWER TERBUKA UNTUK MEMILIH RIWAYAT / MEMORY DESTINASI --}}
        <div x-show="showWisataMemory" x-cloak
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 -translate-y-2 max-h-0"
             x-transition:enter-end="opacity-100 translate-y-0 max-h-[1000px]"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100 translate-y-0 max-h-[1000px]"
             x-transition:leave-end="opacity-0 -translate-y-2 max-h-0"
             class="mt-5 overflow-hidden rounded-xl border-2 border-dashed border-primary-2 bg-primary-3/30 p-5">

          <div class="flex items-center justify-between border-b border-primary-2/40 pb-3">
            <div>
              <h3 class="text-sm font-bold text-primary-1">Riwayat & Arsip Destinasi Wisata</h3>
              <p class="text-xs text-grey-deep">Centang destinasi yang ingin disertakan. Destinasi yang dicentang akan otomatis muncul di form opsi bawah untuk diedit.</p>
            </div>
            <span class="rounded-full bg-white px-3 py-1 text-xs font-bold text-primary-1 shadow-xs border border-primary-1">
              <span x-text="selectedLibraryIds.length"></span> Terpilih
            </span>
          </div>

          {{-- Grid Card Destinasi Riwayat dengan Checkbox --}}
          <div class="mt-4 grid grid-cols-1 gap-3.5 sm:grid-cols-2 lg:grid-cols-4">
            <template x-for="item in wisataLibrary" :key="item.id">
              <div @click="toggleLibraryItem(item)"
                   class="relative rounded-xl border bg-white p-4 shadow-sm transition-all cursor-pointer select-none"
                   :class="selectedLibraryIds.includes(item.id)
                     ? 'border-primary-1 ring-2 ring-primary-1 bg-primary-3/10'
                     : 'border-grey-1 hover:border-primary-2'">
                
                {{-- Header Item: Checkbox & Kategori Badge --}}
                <div class="flex items-center justify-between">
                  <span class="rounded-sm bg-primary-3 px-2 py-0.5 text-[10px] font-bold text-primary-1" x-text="item.label"></span>
                  
                  {{-- Checkbox Lingkaran/Kotak Centang --}}
                  <div class="flex h-5 w-5 items-center justify-center rounded-sm border transition-all"
                       :class="selectedLibraryIds.includes(item.id)
                         ? 'border-primary-1 bg-primary-1 text-white'
                         : 'border-grey-1 bg-white'">
                    <template x-if="selectedLibraryIds.includes(item.id)">
                      <x-solar-check-read-linear class="h-3.5 w-3.5" />
                    </template>
                  </div>
                </div>

                {{-- Nama & Lokasi --}}
                <h4 class="mt-2 text-xs font-bold text-black truncate" x-text="item.name"></h4>
                <p class="mt-0.5 text-[11px] text-grey-2 line-clamp-1" x-text="item.lokasi"></p>

                {{-- Meta Info: Rating & Cashback --}}
                <div class="mt-3 flex items-center justify-between border-t border-gray-100 pt-2 text-[11px]">
                  <span class="font-semibold text-amber-500" x-text="'★ ' + item.rating"></span>
                  <div class="text-right">
                    <span class="text-[9px] text-grey-deep block">Cashback</span>
                    <span class="font-bold text-primary-1" x-text="'Rp ' + Number(item.biaya).toLocaleString('id-ID')"></span>
                  </div>
                </div>
              </div>
            </template>
          </div>
        </div>

        <label class="{{ $label }} mt-6">Pertanyaan</label>
        <div class="mt-2">
          <x-rich-editor path="form.wisata.question" placeholder="Tulis pertanyaan untuk kategori Tempat Wisata..." />
        </div>

        <div class="mt-5 space-y-4">
          <template x-for="(o, i) in form.wisata.options" x-bind:key="o.uid">
            <div class="rounded-sm bg-white p-5 shadow-type-3">
              
              {{-- Header Opsi --}}
              <div class="flex flex-wrap items-center justify-between border-b border-grey-1 pb-3 gap-2">
                <div class="flex items-center gap-3">
                  <span class="text-xs font-bold text-primary-1" x-text="'Opsi ' + (i + 1)"></span>
                  <template x-if="o.name">
                    <span class="text-xs font-semibold text-black" x-text="'- ' + o.name"></span>
                  </template>
                </div>

                <button type="button" class="text-xs font-semibold text-danger disabled:pointer-events-none disabled:opacity-30 cursor-pointer"
                        x-bind:disabled="form.wisata.options.length <= 2" x-on:click="removeWisata(i)">Hapus Opsi</button>
              </div>

              {{-- Detail Bar Atas: Rating, Dikunjungi, Harga --}}
              <div class="mt-4 grid grid-cols-1 gap-3 sm:grid-cols-3 bg-gray-50 p-3 rounded-sm border border-grey-1">
                <div>
                  <label class="block text-[11px] font-semibold text-black">Rating Google</label>
                  <input type="number" step="0.1" min="0" max="5" x-model="o.rating"
                         class="mt-1 {{ $input }} !shadow-none border border-grey-1 py-1.5" placeholder="Contoh: 4.8">
                  <span class="text-[10px] text-grey-2">Otomatis / manual data rating</span>
                </div>
                <div>
                  <label class="block text-[11px] font-semibold text-black">Total Pernah Dikunjungi</label>
                  <input type="number" min="0" x-model="o.dikunjungi"
                         class="mt-1 {{ $input }} !shadow-none border border-grey-1 py-1.5" placeholder="0">
                  <span class="text-[10px] text-grey-2">Histori kunjungan kegiatan lalu</span>
                </div>
                <div>
                  <label class="block text-[11px] font-semibold text-black">Estimasi Cashback (Rp)</label>
                  <input type="number" min="0" x-model="o.biaya"
                         class="mt-1 {{ $input }} !shadow-none border border-grey-1 py-1.5" placeholder="500000">
                  <span class="text-[10px] text-grey-2">Perkiraan cashback yang didapat per peserta</span>
                </div>
              </div>

              {{-- Form Utama: Nama, Lokasi, Jarak, Lama Perjalanan --}}
              <div class="mt-4 grid grid-cols-1 gap-3 md:grid-cols-2">
                <div>
                  <label class="text-[11px] font-semibold text-black">Nama Tempat / Destinasi</label>
                  <input type="text" x-model="o.name" class="mt-1 {{ $input }} !shadow-none border border-grey-1" placeholder="Pantai Kartini">
                </div>
                <div>
                  <label class="text-[11px] font-semibold text-black">Label / Kategori Wisata</label>
                  <input type="text" x-model="o.label" list="label-wisata" class="mt-1 {{ $input }} !shadow-none border border-grey-1" placeholder="Pantai, Budaya, dll">
                </div>
              </div>

              <div class="mt-3">
                <label class="text-[11px] font-semibold text-black">Lokasi Lengkap</label>
                <input type="text" x-model="o.lokasi" class="mt-1 {{ $input }} !shadow-none border border-grey-1" placeholder="Pantai Kartini, Jepara, Jawa Tengah.">
              </div>

              <div class="mt-3 grid grid-cols-1 gap-3 sm:grid-cols-2">
                <div>
                  <label class="text-[11px] font-semibold text-black">Jarak (Km) <span class="text-primary-1 font-bold">*untuk filter jarak user</span></label>
                  <div class="flex items-center mt-1">
                    <input type="number" min="0" x-model="o.jarak" class="{{ $input }} !shadow-none border border-grey-1 rounded-r-none" placeholder="120">
                    <span class="bg-primary-3 text-primary-1 border border-l-0 border-grey-1 px-3 py-2.5 text-xs font-semibold rounded-r-sm">Km</span>
                  </div>
                </div>
                <div>
                  <label class="text-[11px] font-semibold text-black">Lama Perjalanan</label>
                  <input type="text" x-model="o.lama" class="mt-1 {{ $input }} !shadow-none border border-grey-1" placeholder="2 Hari / 1 Hari">
                </div>
              </div>

              {{-- Benefit / Fasilitas (List item sesuai gambar) --}}
              <div class="mt-3">
                <label class="block text-[11px] font-semibold text-black">Benefit / Fasilitas Paket</label>
                <div class="mt-1 flex flex-wrap items-center gap-1.5 rounded-sm border border-grey-1 p-2 bg-white">
                  <template x-for="(b, bi) in o.benefits" x-bind:key="bi">
                    <span class="inline-flex items-center gap-1 rounded bg-primary-3 px-2.5 py-1 text-xs font-medium text-primary-1">
                      <span x-text="'• ' + b"></span>
                      <button type="button" class="font-bold text-danger ml-1" x-on:click="removeBenefit(o, bi)">&times;</button>
                    </span>
                  </template>
                  <input type="text" x-model="o.benefitInput"
                         x-on:keydown.enter.prevent="addBenefit(o)" x-on:blur="addBenefit(o)"
                         class="min-w-[150px] flex-1 bg-transparent text-xs p-1 focus:outline-none"
                         placeholder="Ketik fasilitas lalu Enter (mis: Bis, Makan, Kamar)...">
                </div>
              </div>

              {{-- Deskripsi Singkat & Tag Filter Tambahan --}}
              <div class="mt-3">
                <label class="block text-[11px] font-semibold text-black">Deskripsi Tambahan</label>
                <textarea rows="2" x-model="o.description" class="mt-1 {{ $input }} !shadow-none border border-grey-1" placeholder="Keterangan singkat mengenai tempat wisata..."></textarea>
              </div>

              <div class="mt-3">
                <label class="block text-[11px] font-semibold text-black">Tag Tambahan <span class="font-normal text-grey-2">(Enter atau koma)</span></label>
                <div class="mt-1 flex flex-wrap items-center gap-1.5 rounded-sm border border-grey-1 p-2">
                  <template x-for="(t, ti) in o.tags" x-bind:key="t">
                    <span class="inline-flex items-center gap-1 rounded-full bg-gray-100 px-2.5 py-0.5 text-xs font-medium text-grey-deep">
                      <span x-text="'#' + t"></span>
                      <button type="button" class="font-bold" x-on:click="o.tags.splice(ti, 1)">&times;</button>
                    </span>
                  </template>
                  <input type="text" x-model="o.tagInput"
                         x-on:keydown.enter.prevent="addTag(o)" x-on:keydown.comma.prevent="addTag(o)" x-on:blur="addTag(o)"
                         class="min-w-[120px] flex-1 bg-transparent text-xs p-1 focus:outline-none" placeholder="Tambah tag...">
                </div>
              </div>

            </div>
          </template>
        </div>

        <datalist id="label-wisata">
          <option value="Pantai"><option value="Gunung"><option value="Budaya"><option value="Kuliner"><option value="Edukasi"><option value="Wahana">
        </datalist>

        <button type="button" class="{{ $ghost }} mt-4" x-on:click="addWisata()">+ Tambah Opsi Wisata</button>
      </section>

      {{-- 3. Kategori Desain Kaos (Size Chart Fleksibel: Tabel Manual atau Gambar) --}}
      <section class="{{ $panel }}">
        <div class="flex flex-wrap items-center justify-between gap-3">
          <div>
            <h2 class="text-xl font-bold text-primary-1">3. Desain Kaos & Size Chart</h2>
            <p class="mt-1 text-xs text-black">Size chart dapat dibuat manual melalui tabel angka ATAU cukup mengunggah gambar panduan ukuran.</p>
          </div>
          <button type="button" class="{{ $ghost }}" x-on:click="addKaos()">+ Tambah Desain Kaos</button>
        </div>

        <label class="{{ $label }} mt-5">Pertanyaan</label>
        <div class="mt-2">
          <x-rich-editor path="form.kaos.question" placeholder="Tulis pertanyaan untuk kategori Desain Kaos..." />
        </div>

        {{-- Toggle Mode Size Chart --}}
        <div class="mt-5 flex items-center gap-4 bg-white p-3 rounded-sm shadow-type-3">
          <span class="text-xs font-bold text-primary-1">Metode Size Chart:</span>
          <label class="flex items-center gap-2 cursor-pointer text-xs font-semibold text-black">
            <input type="radio" value="manual" x-model="form.kaos.chart_mode" name="chart_mode_choice" class="text-primary-1">
            <span>Tabel Angka Manual</span>
          </label>
          <label class="flex items-center gap-2 cursor-pointer text-xs font-semibold text-black">
            <input type="radio" value="image" x-model="form.kaos.chart_mode" name="chart_mode_choice" class="text-primary-1">
            <span>Unggah Gambar Panduan Ukuran</span>
          </label>
        </div>

        {{-- Pilihan Mode 1: Tabel Manual --}}
        <div x-show="form.kaos.chart_mode === 'manual'" class="mt-4">
          <label class="{{ $label }}">Ukuran yang tersedia</label>
          <div class="mt-2 flex flex-wrap gap-2">
            <template x-for="s in allSizes" x-bind:key="s">
              <button type="button" x-on:click="toggleSize(s)" x-text="s"
                      class="rounded-button px-4 py-1.5 text-xs font-semibold"
                      x-bind:class="form.kaos.sizes.includes(s) ? 'bg-primary-1 text-white' : 'bg-white text-primary-1 shadow-type-3'"></button>
            </template>
          </div>

          <label class="{{ $label }} mt-4">Size chart <span class="text-xs font-normal text-grey-2">(cm)</span></label>
          <div class="mt-2 overflow-x-auto rounded-sm bg-white p-2 shadow-type-3">
            <table class="w-full min-w-[420px] text-xs">
              <thead>
                <tr class="bg-primary-1 text-white">
                  <th class="rounded-l-sm px-3 py-2 text-center font-semibold">UKURAN</th>
                  <th class="px-3 py-2 text-center font-semibold">LEBAR DADA</th>
                  <th class="rounded-r-sm px-3 py-2 text-center font-semibold">PANJANG BADAN</th>
                </tr>
              </thead>
              <tbody>
                <template x-for="s in activeSizes" x-bind:key="s">
                  <tr>
                    <td class="px-3 py-2 text-center font-bold text-primary-1" x-text="s"></td>
                    <td class="px-3 py-2"><input type="number" min="0" x-model="form.kaos.chart[s].chest"
                         class="w-full rounded-sm border border-grey-1 px-3 py-1.5 text-center focus:outline-none focus:ring-2 focus:ring-primary-1"></td>
                    <td class="px-3 py-2"><input type="number" min="0" x-model="form.kaos.chart[s].length"
                         class="w-full rounded-sm border border-grey-1 px-3 py-1.5 text-center focus:outline-none focus:ring-2 focus:ring-primary-1"></td>
                  </tr>
                </template>
                <tr x-show="activeSizes.length === 0">
                  <td colspan="3" class="px-3 py-4 text-center text-grey-2">Pilih ukuran di atas dulu.</td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>

        {{-- Pilihan Mode 2: Upload Gambar Chart --}}
        <div x-show="form.kaos.chart_mode === 'image'" class="mt-4">
          <label class="{{ $label }}">Unggah Gambar Panduan / Size Chart Kaos</label>
          <label class="mt-2 flex h-48 cursor-pointer items-center justify-center overflow-hidden rounded-sm border border-dashed border-primary-1 bg-white text-xs text-primary-1 shadow-type-3">
            <img x-show="form.kaos.chart_image_preview" x-bind:src="form.kaos.chart_image_preview" class="h-full w-full object-contain" alt="Preview Chart">
            <div x-show="!form.kaos.chart_image_preview" class="text-center p-4">
              <span class="block font-semibold">Klik untuk memilih gambar size chart (PNG/JPG maks 3 MB)</span>
              <span class="text-grey-2 mt-1 block">Peserta dapat melihat gambar ini saat memilih ukuran di halaman voting</span>
            </div>
            <input type="file" name="kaos_chart_image" accept="image/*" class="hidden" x-on:change="pickChartImage($event)">
          </label>
        </div>

        {{-- Pilihan Desain Kaos (Dinamis berapapun) --}}
        <label class="{{ $label }} mt-6">Pilihan Desain Kaos</label>
        <div class="mt-2 grid grid-cols-1 gap-3 md:grid-cols-2">
          <template x-for="(o, i) in form.kaos.options" x-bind:key="o.uid">
            <div class="rounded-sm bg-white p-4 shadow-type-3">
              <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-primary-1" x-text="'Desain ' + (i + 1)"></span>
                <button type="button" class="text-xs font-semibold text-danger disabled:pointer-events-none disabled:opacity-30"
                        x-bind:disabled="form.kaos.options.length <= 2" x-on:click="removeKaos(i)">Hapus Desain</button>
              </div>

              <input type="text" x-model="o.name" class="mt-3 {{ $input }} !shadow-none border border-grey-1" placeholder="Nama Kaos (misal: Navy Polo Gathering)">

              <label class="mt-3 flex h-36 cursor-pointer items-center justify-center overflow-hidden rounded-sm border border-dashed border-primary-1 bg-primary-3 text-xs text-primary-1">
                <img x-show="o.preview" x-bind:src="o.preview" class="h-full w-full object-contain" alt="">
                <span x-show="!o.preview">Klik untuk unggah gambar desain (maks 2 MB)</span>
                <input type="file" accept="image/*" class="hidden"
                       x-bind:name="'kaos_image_' + o.uid" x-on:change="pickImage(o, $event)">
              </label>
            </div>
          </template>
        </div>

        <button type="button" class="{{ $ghost }} mt-4" x-on:click="addKaos()">+ Tambah Desain Kaos</button>
      </section>

      {{-- Navigasi Bawah --}}
      <div class="flex flex-wrap items-center justify-between gap-3 pt-3">
        <button type="button" class="{{ $ghost }}" x-on:click="step = 1">Kembali ke Tahap 1</button>
        <div class="flex flex-wrap items-center gap-2">
          <button type="button" class="{{ $cancel }}" x-on:click="askCancel()">Batalkan</button>
          <button type="button" class="{{ $outl }}" x-on:click="submit('draft')">Simpan Draft</button>
          <button type="button" class="{{ $prim }} shadow-type-1" x-on:click="askPublish()">Post Polling</button>
        </div>
      </div>
    </div>

  </x-card>

  {{-- ================= MODAL KONFIRMASI ================= --}}
  <div x-show="modal" x-cloak
       class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 p-4"
       x-on:keydown.escape.window="modal = null">
    <div class="w-full max-w-sm rounded-xl bg-white p-6 shadow-type-1" x-on:click.outside="modal = null">

      <template x-if="modal === 'cancel'">
        <div>
          <h3 class="text-lg font-bold text-black">Buang perubahan?</h3>
          <p class="mt-2 text-sm text-grey-2">Isian yang belum disimpan akan hilang. Kamu bisa menyimpannya sebagai draft dulu.</p>
          <div class="mt-5 flex flex-wrap justify-end gap-2">
            <button type="button" class="{{ $ghost }}" x-on:click="modal = null">Lanjut mengedit</button>
            <button type="button" class="{{ $outl }}" x-on:click="submit('draft')">Simpan Draft</button>
            <button type="button" class="rounded-button bg-danger px-5 py-2.5 text-sm font-semibold text-white" x-on:click="confirmCancel()">Buang</button>
          </div>
        </div>
      </template>

      <template x-if="modal === 'publish'">
        <div>
          <h3 class="text-lg font-bold text-black">Post polling ini?</h3>
          <p class="mt-2 text-sm text-grey-2">Polling akan diterbitkan dan bisa dilihat peserta sesuai jadwal voting. Kamu bisa memilih Simpan Draft kalau belum yakin.</p>
          <div class="mt-5 flex flex-wrap justify-end gap-2">
            <button type="button" class="{{ $ghost }}" x-on:click="modal = null">Periksa lagi</button>
            <button type="button" class="{{ $prim }}" x-on:click="submit('publish')">Ya, Post</button>
          </div>
        </div>
      </template>

    </div>
  </div>
</form>
@endsection
