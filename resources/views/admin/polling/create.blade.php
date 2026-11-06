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

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/quill@2.0.3/dist/quill.snow.css">
<script src="https://cdn.jsdelivr.net/npm/quill@2.0.3/dist/quill.js"></script>

<style>
  .ql-toolbar.ql-snow { border: 0; border-bottom: 1px solid #DBDBDB; }
  .ql-container.ql-snow { border: 0; font-family: inherit; font-size: .875rem; }
  .ql-editor { min-height: 110px; }
</style>

<script>
  window.mountQuill = (el, initial, onChange) => {
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
    q.on('text-change', () => onChange(q.getText().trim() === '' ? '' : q.root.innerHTML));
  };

  document.addEventListener('alpine:init', () => {
    Alpine.data('pollForm', () => ({
      step: 1,
      uid: 100,
      errors: [],
      action: 'draft',
      dirty: false,
      leaving: false,
      modal: null,
      cancelUrl: @json(route('admin.polling.arsip')),
      allSizes: ['XS', 'S', 'M', 'L', 'XL', 'XXL', '3XL'],
      form: {
        identity: {
          title: '', description: '',
          start: { time: '08:00', date: '' },
          end:   { time: '08:00', date: '' },
        },
        peserta: { question: '', options: [{ uid: 1, label: '' }, { uid: 2, label: '' }] },
        wisata: {
          question: '',
          options: [
            { uid: 3, name: '', description: '', tags: [], tagInput: '', label: '' },
            { uid: 4, name: '', description: '', tags: [], tagInput: '', label: '' },
          ],
        },
        kaos: {
          question: '',
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
        this.$watch('form', () => { this.dirty = true }, { deep: true });
        window.addEventListener('beforeunload', (e) => {
          if (this.dirty && !this.leaving) { e.preventDefault(); e.returnValue = ''; }
        });
      },

      payload() {
        return JSON.stringify(this.form, (k, v) => (k === 'preview' || k === 'tagInput') ? undefined : v);
      },

      // Wisata
      addWisata() { this.form.wisata.options.push({ uid: ++this.uid, name: '', description: '', tags: [], tagInput: '', label: '' }) },
      removeWisata(i) { if (this.form.wisata.options.length > 2) this.form.wisata.options.splice(i, 1) },
      addTag(o) {
        const t = o.tagInput.trim().replace(/^#/, '');
        if (t && !o.tags.includes(t)) o.tags.push(t);
        o.tagInput = '';
      },

      // Kaos
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

      // Validasi: draft hanya butuh judul, post butuh semuanya
      validate(mode) {
        const f = this.form, e = [];
        if (!f.identity.title.trim()) e.push({ step: 1, msg: 'Judul voting wajib diisi.' });
        if (mode === 'draft') return e;

        if (!f.identity.start.date || !f.identity.end.date) e.push({ step: 1, msg: 'Tanggal mulai dan selesai wajib diisi.' });
        else if (f.identity.end.date < f.identity.start.date) e.push({ step: 1, msg: 'Tanggal selesai tidak boleh sebelum tanggal mulai.' });
        if (!f.peserta.question) e.push({ step: 2, msg: 'Pertanyaan kategori Peserta wajib diisi.' });
        if (f.peserta.options.some(o => !o.label.trim())) e.push({ step: 2, msg: 'Kedua pilihan Peserta wajib diisi.' });
        if (!f.wisata.question) e.push({ step: 2, msg: 'Pertanyaan kategori Tempat Wisata wajib diisi.' });
        if (f.wisata.options.some(o => !o.name.trim())) e.push({ step: 2, msg: 'Semua opsi Tempat Wisata wajib punya nama.' });
        if (!f.kaos.question) e.push({ step: 2, msg: 'Pertanyaan kategori Desain Kaos wajib diisi.' });
        if (f.kaos.sizes.length === 0) e.push({ step: 2, msg: 'Pilih minimal satu ukuran kaos.' });
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

      // Batalkan: konfirmasi hanya jika ada perubahan
      askCancel() {
        if (!this.dirty) return this.confirmCancel();
        this.modal = 'cancel';
      },
      confirmCancel() {
        this.leaving = true;
        window.location.href = this.cancelUrl;
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
  });
</script>

<form method="POST" action="{{ route('admin.polling.store') }}" enctype="multipart/form-data"
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

    {{-- ================= TAHAP 1 ================= --}}
    <div x-show="step === 1" class="mt-5 {{ $panel }}">
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
              <div class="rounded-sm bg-white p-3 shadow-type-3">
                <p class="text-[11px] font-bold uppercase text-primary-1">{{ $title }}</p>
                <div class="mt-2 flex gap-2">
                  <input type="time" x-model="form.identity.{{ $key }}.time"
                         class="w-1/2 rounded-sm bg-primary-3 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary-1">
                  <input type="date" x-model="form.identity.{{ $key }}.date"
                         class="w-1/2 rounded-sm bg-primary-3 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary-1">
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

    {{-- ================= TAHAP 2 ================= --}}
    <div x-show="step === 2" x-cloak class="mt-5 space-y-5">

      {{-- Peserta --}}
      <section class="{{ $panel }}">
        <h2 class="text-xl font-bold text-primary-1">Rombongan Peserta</h2>
        <p class="mt-1 text-xs text-black">Kategori ini hanya memiliki 2 pilihan.</p>

        <label class="{{ $label }} mt-5">Pertanyaan</label>
        <div class="mt-2">
          <x-rich-editor path="form.peserta.question" placeholder="Tulis pertanyaan untuk kategori Peserta..." />
        </div>

        <label class="{{ $label }} mt-5">Pilihan</label>
        <div class="mt-2 grid grid-cols-1 gap-3 md:grid-cols-2">
          <template x-for="(o, i) in form.peserta.options" x-bind:key="o.uid">
            <div class="flex items-center gap-3">
              <span class="flex size-9 shrink-0 items-center justify-center rounded-sm border border-primary-1 bg-white text-xs"
                    x-text="i + 1"></span>
              <input type="text" x-model="o.label" class="{{ $input }}"
                     x-bind:placeholder="i === 0 ? 'Dosen dan tendik dipisah' : 'Dosen dan tendik digabung'">
            </div>
          </template>
        </div>
      </section>

      {{-- Tempat Wisata --}}
      <section class="{{ $panel }}">
        <h2 class="text-xl font-bold text-primary-1">Tempat Wisata</h2>
        <p class="mt-1 text-xs text-black">Tag dan label dipakai sebagai filter di halaman user. Minimal 2 opsi.</p>

        <label class="{{ $label }} mt-5">Pertanyaan</label>
        <div class="mt-2">
          <x-rich-editor path="form.wisata.question" placeholder="Tulis pertanyaan untuk kategori Tempat Wisata..." />
        </div>

        <div class="mt-5 space-y-3">
          <template x-for="(o, i) in form.wisata.options" x-bind:key="o.uid">
            <div class="rounded-sm bg-white p-4 shadow-type-3">
              <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-primary-1" x-text="'Opsi ' + (i + 1)"></span>
                <button type="button" class="text-xs font-semibold text-danger disabled:pointer-events-none disabled:opacity-30"
                        x-bind:disabled="form.wisata.options.length <= 2" x-on:click="removeWisata(i)">Hapus</button>
              </div>

              <div class="mt-3 grid grid-cols-1 gap-3 md:grid-cols-[2fr_1fr]">
                <div>
                  <label class="text-[11px] font-semibold text-black">Nama tempat</label>
                  <input type="text" x-model="o.name" class="mt-1 {{ $input }} !shadow-none border border-grey-1" placeholder="Pantai Soleram">
                </div>
                <div>
                  <label class="text-[11px] font-semibold text-black">Label</label>
                  <input type="text" x-model="o.label" list="label-wisata" class="mt-1 {{ $input }} !shadow-none border border-grey-1" placeholder="Pantai">
                </div>
              </div>

              <label class="mt-3 block text-[11px] font-semibold text-black">Deskripsi singkat</label>
              <textarea rows="2" x-model="o.description" class="mt-1 {{ $input }} !shadow-none border border-grey-1"></textarea>

              <label class="mt-3 block text-[11px] font-semibold text-black">Tag <span class="font-normal text-grey-2">(Enter atau koma untuk menambah)</span></label>
              <div class="mt-1 flex flex-wrap items-center gap-2 rounded-sm border border-grey-1 px-3 py-2">
                <template x-for="(t, ti) in o.tags" x-bind:key="t">
                  <span class="inline-flex items-center gap-1 rounded-full bg-primary-3 px-3 py-1 text-xs font-medium text-primary-1">
                    <span x-text="'#' + t"></span>
                    <button type="button" class="font-bold" x-on:click="o.tags.splice(ti, 1)" aria-label="Hapus tag">&times;</button>
                  </span>
                </template>
                <input type="text" x-model="o.tagInput"
                       x-on:keydown.enter.prevent="addTag(o)" x-on:keydown.comma.prevent="addTag(o)" x-on:blur="addTag(o)"
                       class="min-w-[120px] flex-1 bg-transparent text-sm focus:outline-none" placeholder="Tambah tag...">
              </div>
            </div>
          </template>
        </div>

        <datalist id="label-wisata">
          <option value="Pantai"><option value="Gunung"><option value="Budaya"><option value="Kuliner"><option value="Edukasi"><option value="Wahana">
        </datalist>

        <button type="button" class="{{ $ghost }} mt-4" x-on:click="addWisata()">+ Tambah opsi wisata</button>
      </section>

      {{-- Desain Kaos --}}
      <section class="{{ $panel }}">
        <h2 class="text-xl font-bold text-primary-1">Desain Kaos</h2>
        <p class="mt-1 text-xs text-black">Size chart tampil di halaman user, dan user memilih ukuran saat memberikan suara. Minimal 2 desain.</p>

        <label class="{{ $label }} mt-5">Pertanyaan</label>
        <div class="mt-2">
          <x-rich-editor path="form.kaos.question" placeholder="Tulis pertanyaan untuk kategori Desain Kaos..." />
        </div>

        <label class="{{ $label }} mt-5">Ukuran yang tersedia</label>
        <div class="mt-2 flex flex-wrap gap-2">
          <template x-for="s in allSizes" x-bind:key="s">
            <button type="button" x-on:click="toggleSize(s)" x-text="s"
                    class="rounded-button px-4 py-1.5 text-xs font-semibold"
                    x-bind:class="form.kaos.sizes.includes(s) ? 'bg-primary-1 text-white' : 'bg-white text-primary-1 shadow-type-3'"></button>
          </template>
        </div>

        <label class="{{ $label }} mt-5">Size chart <span class="text-xs font-normal text-grey-2">(cm)</span></label>
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

        <label class="{{ $label }} mt-5">Pilihan desain</label>
        <div class="mt-2 grid grid-cols-1 gap-3 md:grid-cols-2">
          <template x-for="(o, i) in form.kaos.options" x-bind:key="o.uid">
            <div class="rounded-sm bg-white p-4 shadow-type-3">
              <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-primary-1" x-text="'Desain ' + (i + 1)"></span>
                <button type="button" class="text-xs font-semibold text-danger disabled:pointer-events-none disabled:opacity-30"
                        x-bind:disabled="form.kaos.options.length <= 2" x-on:click="removeKaos(i)">Hapus</button>
              </div>

              <input type="text" x-model="o.name" class="mt-3 {{ $input }} !shadow-none border border-grey-1" placeholder="Premium Zip-Up Hoodie">

              <label class="mt-3 flex h-36 cursor-pointer items-center justify-center overflow-hidden rounded-sm border border-dashed border-primary-1 bg-primary-3 text-xs text-primary-1">
                <img x-show="o.preview" x-bind:src="o.preview" class="h-full w-full object-contain" alt="">
                <span x-show="!o.preview">Klik untuk unggah gambar desain (maks 2 MB)</span>
                <input type="file" accept="image/*" class="hidden"
                       x-bind:name="'kaos_image_' + o.uid" x-on:change="pickImage(o, $event)">
              </label>
            </div>
          </template>
        </div>

        <button type="button" class="{{ $ghost }} mt-4" x-on:click="addKaos()">+ Tambah desain</button>
      </section>

      <div class="flex flex-wrap items-center justify-between gap-3">
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