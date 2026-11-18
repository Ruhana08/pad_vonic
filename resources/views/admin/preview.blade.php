@extends('layouts.admin')
@section('title', '🧪 State & Copywriting Inspector')

@section('content')
<div class="space-y-6">

  {{-- Header --}}
  <div>
    <h1 class="text-2xl font-bold text-black">🧪 State & Copywriting Inspector</h1>
    <p class="mt-1 text-sm text-grey-deep">Klik tombol di bawah untuk langsung melihat setiap state tampilan di browser. Semua URL menggunakan query parameter — tidak perlu database.</p>
  </div>

  {{-- ===== SECTION 1: Halaman Arsip & Polling ===== --}}
  <x-card class="p-6 border border-primary-1 shadow-sm">
    <h2 class="text-lg font-bold text-primary-1 flex items-center gap-2">
      <x-solar-clipboard-list-linear class="h-5 w-5" />
      Halaman Status Polling & Arsip
    </h2>
    <p class="mt-1 text-xs text-grey-deep">Halaman <code>/admin/polling/arsip</code> — tabel polling aktif, draft, selesai, dan riwayat.</p>

    <div class="mt-4 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
      {{-- Normal (semua data) --}}
      <a href="{{ route('admin.polling.arsip') }}"
         class="group flex flex-col rounded-sm border border-grey-1 bg-white p-4 shadow-xs hover:border-primary-1 hover:shadow-primary-glow transition-all">
        <span class="text-xs font-bold text-black group-hover:text-primary-1">📊 Normal (Semua Data)</span>
        <span class="mt-1 text-[11px] text-grey-deep leading-snug">Tampilan default: 1 Aktif, 1 Draft, 3 Selesai, plus tabel riwayat lengkap.</span>
        <span class="mt-2 text-[10px] font-mono text-primary-1 bg-primary-3/50 rounded px-1.5 py-0.5 self-start">/admin/polling/arsip</span>
      </a>

      {{-- Empty State --}}
      <a href="{{ route('admin.polling.arsip', ['empty' => 1]) }}"
         class="group flex flex-col rounded-sm border border-grey-1 bg-white p-4 shadow-xs hover:border-primary-1 hover:shadow-primary-glow transition-all">
        <span class="text-xs font-bold text-black group-hover:text-primary-1">📭 Empty State (Kosong)</span>
        <span class="mt-1 text-[11px] text-grey-deep leading-snug">Semua tabel kosong. Lihat pesan "Belum Ada Polling yang Dibuat" dan ilustrasi empty state.</span>
        <span class="mt-2 text-[10px] font-mono text-primary-1 bg-primary-3/50 rounded px-1.5 py-0.5 self-start">/admin/polling/arsip?empty=1</span>
      </a>

      {{-- Tanpa Polling Aktif --}}
      <a href="{{ route('admin.polling.arsip', ['active' => 0]) }}"
         class="group flex flex-col rounded-sm border border-grey-1 bg-white p-4 shadow-xs hover:border-primary-1 hover:shadow-primary-glow transition-all">
        <span class="text-xs font-bold text-black group-hover:text-primary-1">🔓 Tidak Ada Polling Aktif</span>
        <span class="mt-1 text-[11px] text-grey-deep leading-snug">Polling Aktif dihilangkan. Tombol publish draft bisa diklik dan akan muncul popup "Jam Terlewat".</span>
        <span class="mt-2 text-[10px] font-mono text-primary-1 bg-primary-3/50 rounded px-1.5 py-0.5 self-start">/admin/polling/arsip?active=0</span>
      </a>

      {{-- Hanya Draft --}}
      <a href="{{ route('admin.polling.arsip', ['state' => 'draft']) }}"
         class="group flex flex-col rounded-sm border border-grey-1 bg-white p-4 shadow-xs hover:border-primary-1 hover:shadow-primary-glow transition-all">
        <span class="text-xs font-bold text-black group-hover:text-primary-1">📝 Hanya Draft</span>
        <span class="mt-1 text-[11px] text-grey-deep leading-snug">Filter hanya polling Draft. Lihat tombol Edit, Publish, dan Hapus yang aktif.</span>
        <span class="mt-2 text-[10px] font-mono text-primary-1 bg-primary-3/50 rounded px-1.5 py-0.5 self-start">/admin/polling/arsip?state=draft</span>
      </a>

      {{-- Hanya Selesai --}}
      <a href="{{ route('admin.polling.arsip', ['state' => 'selesai']) }}"
         class="group flex flex-col rounded-sm border border-grey-1 bg-white p-4 shadow-xs hover:border-primary-1 hover:shadow-primary-glow transition-all">
        <span class="text-xs font-bold text-black group-hover:text-primary-1">✅ Hanya Selesai</span>
        <span class="mt-1 text-[11px] text-grey-deep leading-snug">Filter hanya polling Selesai. Tombol Edit dan Publish disabled, Hapus tetap aktif.</span>
        <span class="mt-2 text-[10px] font-mono text-primary-1 bg-primary-3/50 rounded px-1.5 py-0.5 self-start">/admin/polling/arsip?state=selesai</span>
      </a>

      {{-- Hanya Aktif --}}
      <a href="{{ route('admin.polling.arsip', ['state' => 'aktif']) }}"
         class="group flex flex-col rounded-sm border border-grey-1 bg-white p-4 shadow-xs hover:border-primary-1 hover:shadow-primary-glow transition-all">
        <span class="text-xs font-bold text-black group-hover:text-primary-1">🟢 Hanya Aktif</span>
        <span class="mt-1 text-[11px] text-grey-deep leading-snug">Filter hanya polling Aktif. Lihat tombol Lihat aktif, Edit & Publish disabled, Hapus = hentikan.</span>
        <span class="mt-2 text-[10px] font-mono text-primary-1 bg-primary-3/50 rounded px-1.5 py-0.5 self-start">/admin/polling/arsip?state=aktif</span>
      </a>
    </div>
  </x-card>

  {{-- ===== SECTION 2: Dashboard ===== --}}
  <x-card class="p-6 border border-primary-1 shadow-sm">
    <h2 class="text-lg font-bold text-primary-1 flex items-center gap-2">
      <x-solar-chart-square-linear class="h-5 w-5" />
      Halaman Dashboard
    </h2>
    <p class="mt-1 text-xs text-grey-deep">Halaman <code>/admin/dashboard</code> — countdown, statistik vote, kategori, dan aktivitas.</p>

    <div class="mt-4 grid grid-cols-1 sm:grid-cols-2 gap-3">
      <a href="{{ route('admin.dashboard') }}"
         class="group flex flex-col rounded-sm border border-grey-1 bg-white p-4 shadow-xs hover:border-primary-1 hover:shadow-primary-glow transition-all">
        <span class="text-xs font-bold text-black group-hover:text-primary-1">📊 Normal (Ada Polling Aktif)</span>
        <span class="mt-1 text-[11px] text-grey-deep leading-snug">82% partisipasi, countdown 3 hari, 3 kategori voting, dan aktivitas terakhir.</span>
        <span class="mt-2 text-[10px] font-mono text-primary-1 bg-primary-3/50 rounded px-1.5 py-0.5 self-start">/admin/dashboard</span>
      </a>

      <a href="{{ route('admin.dashboard', ['empty' => 1]) }}"
         class="group flex flex-col rounded-sm border border-grey-1 bg-white p-4 shadow-xs hover:border-primary-1 hover:shadow-primary-glow transition-all">
        <span class="text-xs font-bold text-black group-hover:text-primary-1">📭 Kosong (Belum Ada Polling)</span>
        <span class="mt-1 text-[11px] text-grey-deep leading-snug">Semua statistik 0, tidak ada countdown, tidak ada aktivitas.</span>
        <span class="mt-2 text-[10px] font-mono text-primary-1 bg-primary-3/50 rounded px-1.5 py-0.5 self-start">/admin/dashboard?empty=1</span>
      </a>
    </div>
  </x-card>

  {{-- ===== SECTION 3: Akun Pengguna ===== --}}
  <x-card class="p-6 border border-primary-1 shadow-sm">
    <h2 class="text-lg font-bold text-primary-1 flex items-center gap-2">
      <x-solar-users-group-two-rounded-linear class="h-5 w-5" />
      Halaman Akun Pengguna
    </h2>
    <p class="mt-1 text-xs text-grey-deep">Halaman <code>/admin/akun</code> — daftar akun dosen dan tendik.</p>

    <div class="mt-4 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
      <a href="{{ route('admin.akun.index') }}"
         class="group flex flex-col rounded-sm border border-grey-1 bg-white p-4 shadow-xs hover:border-primary-1 hover:shadow-primary-glow transition-all">
        <span class="text-xs font-bold text-black group-hover:text-primary-1">📊 Normal (60 Akun Dosen)</span>
        <span class="mt-1 text-[11px] text-grey-deep leading-snug">Daftar penuh akun dummy dosen dengan pagination.</span>
        <span class="mt-2 text-[10px] font-mono text-primary-1 bg-primary-3/50 rounded px-1.5 py-0.5 self-start">/admin/akun</span>
      </a>

      <a href="{{ route('admin.akun.index', ['tab' => 'tendik']) }}"
         class="group flex flex-col rounded-sm border border-grey-1 bg-white p-4 shadow-xs hover:border-primary-1 hover:shadow-primary-glow transition-all">
        <span class="text-xs font-bold text-black group-hover:text-primary-1">👥 Tab Tendik</span>
        <span class="mt-1 text-[11px] text-grey-deep leading-snug">Beralih ke tab Tenaga Kependidikan.</span>
        <span class="mt-2 text-[10px] font-mono text-primary-1 bg-primary-3/50 rounded px-1.5 py-0.5 self-start">/admin/akun?tab=tendik</span>
      </a>

      <a href="{{ route('admin.akun.index', ['empty' => 1]) }}"
         class="group flex flex-col rounded-sm border border-grey-1 bg-white p-4 shadow-xs hover:border-primary-1 hover:shadow-primary-glow transition-all">
        <span class="text-xs font-bold text-black group-hover:text-primary-1">📭 Kosong (Belum Ada Akun)</span>
        <span class="mt-1 text-[11px] text-grey-deep leading-snug">Tabel akun kosong. Lihat pesan empty state.</span>
        <span class="mt-2 text-[10px] font-mono text-primary-1 bg-primary-3/50 rounded px-1.5 py-0.5 self-start">/admin/akun?empty=1</span>
      </a>
    </div>
  </x-card>

  {{-- ===== SECTION 4: Polling Aktif ===== --}}
  <x-card class="p-6 border border-primary-1 shadow-sm">
    <h2 class="text-lg font-bold text-primary-1 flex items-center gap-2">
      <x-solar-chart-linear class="h-5 w-5" />
      Halaman Polling Aktif (Analitik)
    </h2>
    <p class="mt-1 text-xs text-grey-deep">Halaman <code>/admin/polling/aktif</code> — statistik voting real-time.</p>

    <div class="mt-4 grid grid-cols-1 sm:grid-cols-2 gap-3">
      <a href="{{ route('admin.polling.aktif') }}"
         class="group flex flex-col rounded-sm border border-grey-1 bg-white p-4 shadow-xs hover:border-primary-1 hover:shadow-primary-glow transition-all">
        <span class="text-xs font-bold text-black group-hover:text-primary-1">📊 Normal (Voting Berjalan)</span>
        <span class="mt-1 text-[11px] text-grey-deep leading-snug">Piknik DTEDI 2026 — countdown aktif, 82% partisipasi, rekap kaos, tabel aktivitas.</span>
        <span class="mt-2 text-[10px] font-mono text-primary-1 bg-primary-3/50 rounded px-1.5 py-0.5 self-start">/admin/polling/aktif</span>
      </a>

      <a href="{{ route('admin.polling.aktif', ['empty' => 1]) }}"
         class="group flex flex-col rounded-sm border border-grey-1 bg-white p-4 shadow-xs hover:border-primary-1 hover:shadow-primary-glow transition-all">
        <span class="text-xs font-bold text-black group-hover:text-primary-1">📭 Kosong (Belum Ada Voting)</span>
        <span class="mt-1 text-[11px] text-grey-deep leading-snug">Semua statistik kosong. Countdown nol, tidak ada aktivitas.</span>
        <span class="mt-2 text-[10px] font-mono text-primary-1 bg-primary-3/50 rounded px-1.5 py-0.5 self-start">/admin/polling/aktif?empty=1</span>
      </a>
    </div>
  </x-card>

  {{-- ===== SECTION 5: Modal Pop Up Preview (Alpine.js) ===== --}}
  <x-card class="p-6 border border-primary-1 shadow-sm"
    x-data="{
      activeModal: null,
      previewImage: null,
      sampleHistory: {
        id: 1,
        title: 'Piknik DTEDI Periode 2026',
        description: 'Pemilihan paket wisata dan desain kaos tahunan civitas DTEDI.',
        start: '01/09/26',
        end: '12/09/26',
        full_duration: '1 September 2026 - 12 September 2026',
        participants: 120,
        peserta_type: 'Dosen dan Tenaga Kependidikan',
        kaos: {
          title: 'Kaos A',
          image: 'https://images.unsplash.com/photo-1507525428034-b723cf961d3e?auto=format&fit=crop&w=600&q=80',
          gallery: [
            'https://images.unsplash.com/photo-1519046904884-53103b34b206?auto=format&fit=crop&w=300&q=80',
            'https://images.unsplash.com/photo-1473186578172-c141e6798cf4?auto=format&fit=crop&w=300&q=80',
          ]
        },
        wisata: {
          title: 'Pantai Kartini',
          rating: '4,8',
          image: 'https://images.unsplash.com/photo-1507525428034-b723cf961d3e?auto=format&fit=crop&w=800&q=80',
          gallery: [
            'https://images.unsplash.com/photo-1519046904884-53103b34b206?auto=format&fit=crop&w=400&q=80',
            'https://images.unsplash.com/photo-1473186578172-c141e6798cf4?auto=format&fit=crop&w=400&q=80',
          ],
          lokasi: 'Pantai Kartini, Jepara, Jawa Tengah.',
          jarak: '120 Km',
          durasi: '2 Hari',
          dikunjungi: 2,
          benefits: ['Bis', 'Makan', 'Minum', 'Kamar 2 hari'],
          biaya: 'Rp 500.000'
        }
      }
    }">
    <h2 class="text-lg font-bold text-primary-1 flex items-center gap-2">
      <x-solar-window-frame-linear class="h-5 w-5" />
      Preview Semua Pop-Up Modal
    </h2>
    <p class="mt-1 text-xs text-grey-deep">Klik tombol untuk langsung membuka popup modal tanpa perlu navigasi ke halaman lain.</p>

    <div class="mt-4 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
      {{-- 1. Blocked Active --}}
      <button @click="activeModal = 'blocked_active'"
              class="text-left rounded-sm border border-grey-1 bg-white p-4 shadow-xs hover:border-amber-400 hover:shadow-md transition-all cursor-pointer">
        <span class="text-xs font-bold text-black">⚠️ Peringatan: 1 Aktif</span>
        <span class="mt-1 block text-[11px] text-grey-deep leading-snug">Mencoba publish draft saat ada polling aktif. Muncul peringatan "hanya 1 polling aktif".</span>
      </button>

      {{-- 2. Late Warning --}}
      <button @click="activeModal = 'late_warning'"
              class="text-left rounded-sm border border-grey-1 bg-white p-4 shadow-xs hover:border-blue-400 hover:shadow-md transition-all cursor-pointer">
        <span class="text-xs font-bold text-black">🕐 Waktu Mulai Terlewat</span>
        <span class="mt-1 block text-[11px] text-grey-deep leading-snug">Publish setelah jadwal waktu mulai. Pilihan: "Edit Waktu" atau "Tetap Post".</span>
      </button>

      {{-- 3. Delete Active --}}
      <button @click="activeModal = 'delete_active'"
              class="text-left rounded-sm border border-grey-1 bg-white p-4 shadow-xs hover:border-rose-400 hover:shadow-md transition-all cursor-pointer">
        <span class="text-xs font-bold text-black">🛑 Hapus Polling Aktif</span>
        <span class="mt-1 block text-[11px] text-grey-deep leading-snug">Konfirmasi khusus: menghentikan voting → status menjadi Selesai.</span>
      </button>

      {{-- 4. Delete Draft --}}
      <button @click="activeModal = 'delete_draft'"
              class="text-left rounded-sm border border-grey-1 bg-white p-4 shadow-xs hover:border-rose-400 hover:shadow-md transition-all cursor-pointer">
        <span class="text-xs font-bold text-black">🗑️ Hapus Draft</span>
        <span class="mt-1 block text-[11px] text-grey-deep leading-snug">Hapus polling draft secara permanen.</span>
      </button>

      {{-- 5. Delete Completed --}}
      <button @click="activeModal = 'delete_completed'"
              class="text-left rounded-sm border border-grey-1 bg-white p-4 shadow-xs hover:border-rose-400 hover:shadow-md transition-all cursor-pointer">
        <span class="text-xs font-bold text-black">🗑️ Hapus Arsip</span>
        <span class="mt-1 block text-[11px] text-grey-deep leading-snug">Hapus polling arsip (selesai) secara permanen.</span>
      </button>

      {{-- 6. Hasil Polling --}}
      <button @click="activeModal = 'view_result'"
              class="text-left rounded-sm border border-grey-1 bg-white p-4 shadow-xs hover:border-primary-1 hover:shadow-md transition-all cursor-pointer">
        <span class="text-xs font-bold text-black">🏆 Hasil Polling (Pemenang)</span>
        <span class="mt-1 block text-[11px] text-grey-deep leading-snug">Modal besar: kaos pemenang, destinasi wisata, foto clickable lightbox, cashback.</span>
      </button>
    </div>

    {{-- Modal 1: Blocked Active --}}
    <x-modal show="activeModal === 'blocked_active'" close="activeModal = null" maxWidth="sm">
      <div class="p-6 text-center">
        <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-full bg-amber-100 text-amber-600 mb-4">
          <x-solar-danger-circle-linear class="h-6 w-6" />
        </div>
        <h3 class="text-base font-bold text-black">Tidak Dapat Mempublikasikan</h3>
        <p class="mt-2 text-xs text-grey-deep leading-relaxed">
          Saat ini sudah ada voting yang sedang berjalan (<span class="font-semibold text-primary-1">Piknik DTEDI Periode 2026</span>). Sistem hanya mengizinkan <strong>1 polling aktif</strong> dalam satu waktu.
        </p>
        <div class="mt-6">
          <button type="button" @click="activeModal = null"
                  class="w-full rounded-sm bg-primary-1 py-2 text-xs font-semibold text-white shadow-menu hover:bg-primary-4 transition-all">
            Mengerti
          </button>
        </div>
      </div>
    </x-modal>

    {{-- Modal 2: Late Warning --}}
    <x-modal show="activeModal === 'late_warning'" close="activeModal = null" maxWidth="sm">
      <div class="p-6 text-center">
        <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-full bg-blue-100 text-primary-1 mb-4">
          <x-solar-clock-circle-linear class="h-6 w-6" />
        </div>
        <h3 class="text-base font-bold text-black">Waktu Mulai Telah Terlewat</h3>
        <p class="mt-2 text-xs text-grey-deep leading-relaxed">
          Waktu mulai polling ini sudah terlewat dari jadwal yang ditentukan. Durasi voting akan berjalan hingga waktu penutupan yang telah di-setting sebelumnya.
        </p>
        <div class="mt-6 flex items-center gap-3">
          <button type="button" @click="activeModal = null"
                  class="flex-1 rounded-sm border border-grey-1 bg-white py-2 text-xs font-semibold text-grey-deep shadow-xs hover:bg-gray-50 transition-all">
            Edit Waktu
          </button>
          <button type="button" @click="alert('Polling berhasil dipost!'); activeModal = null"
                  class="flex-1 rounded-sm bg-primary-1 py-2 text-xs font-semibold text-white shadow-menu hover:bg-primary-4 transition-all">
            Tetap Post
          </button>
        </div>
      </div>
    </x-modal>

    {{-- Modal 3: Delete Active --}}
    <x-modal show="activeModal === 'delete_active'" close="activeModal = null" maxWidth="sm">
      <div class="p-6 text-center">
        <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-full bg-rose-100 text-danger mb-4">
          <x-solar-danger-circle-linear class="h-6 w-6" />
        </div>
        <h3 class="text-base font-bold text-black">Hentikan Polling yang Sedang Aktif?</h3>
        <p class="mt-2 text-xs text-grey-deep leading-relaxed">
          Polling yang sedang aktif tidak dapat diedit agar integritas data pemilih tetap terjaga. Jika Anda menghapus polling ini sekarang, voting akan dihentikan dan <strong>statusnya akan langsung berubah menjadi Selesai</strong>.
        </p>
        <div class="mt-6 flex items-center gap-3">
          <button type="button" @click="activeModal = null"
                  class="flex-1 rounded-sm border border-grey-1 bg-white py-2 text-xs font-semibold text-grey-deep shadow-xs hover:bg-gray-50 transition-all">
            Batal
          </button>
          <button type="button" @click="alert('Polling dihentikan! Status berubah menjadi Selesai.'); activeModal = null"
                  class="flex-1 rounded-sm bg-danger py-2 text-xs font-semibold text-white shadow-menu hover:bg-red-700 transition-all">
            Ya, Hentikan
          </button>
        </div>
      </div>
    </x-modal>

    {{-- Modal 4: Delete Draft --}}
    <x-modal show="activeModal === 'delete_draft'" close="activeModal = null" maxWidth="sm">
      <div class="p-6 text-center">
        <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-full bg-rose-100 text-danger mb-4">
          <x-solar-trash-bin-trash-linear class="h-6 w-6" />
        </div>
        <h3 class="text-base font-bold text-black">Hapus Draft Polling?</h3>
        <p class="mt-2 text-xs text-grey-deep leading-relaxed">
          Apakah Anda yakin ingin menghapus data polling ini? Tindakan ini akan menghapus data secara permanen.
        </p>
        <div class="mt-6 flex items-center gap-3">
          <button type="button" @click="activeModal = null"
                  class="flex-1 rounded-sm border border-grey-1 bg-white py-2 text-xs font-semibold text-grey-deep shadow-xs hover:bg-gray-50 transition-all">
            Batal
          </button>
          <button type="button" @click="alert('Draft berhasil dihapus!'); activeModal = null"
                  class="flex-1 rounded-sm bg-danger py-2 text-xs font-semibold text-white shadow-menu hover:bg-red-700 transition-all">
            Ya, Hapus
          </button>
        </div>
      </div>
    </x-modal>

    {{-- Modal 5: Delete Completed --}}
    <x-modal show="activeModal === 'delete_completed'" close="activeModal = null" maxWidth="sm">
      <div class="p-6 text-center">
        <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-full bg-rose-100 text-danger mb-4">
          <x-solar-trash-bin-trash-linear class="h-6 w-6" />
        </div>
        <h3 class="text-base font-bold text-black">Hapus Arsip Polling?</h3>
        <p class="mt-2 text-xs text-grey-deep leading-relaxed">
          Apakah Anda yakin ingin menghapus data polling ini? Tindakan ini akan menghapus data secara permanen.
        </p>
        <div class="mt-6 flex items-center gap-3">
          <button type="button" @click="activeModal = null"
                  class="flex-1 rounded-sm border border-grey-1 bg-white py-2 text-xs font-semibold text-grey-deep shadow-xs hover:bg-gray-50 transition-all">
            Batal
          </button>
          <button type="button" @click="alert('Arsip berhasil dihapus!'); activeModal = null"
                  class="flex-1 rounded-sm bg-danger py-2 text-xs font-semibold text-white shadow-menu hover:bg-red-700 transition-all">
            Ya, Hapus
          </button>
        </div>
      </div>
    </x-modal>

    {{-- Modal 6: Hasil Polling (Full-sized) --}}
    <x-modal show="activeModal === 'view_result'" close="activeModal = null" maxWidth="820px">
      <div class="p-7 max-h-[90vh] overflow-y-auto">
        {{-- Header Modal --}}
        <div class="flex items-start justify-between pb-4">
          <div>
            <span class="text-xs font-semibold text-primary-1">Hasil vote</span>
            <h2 class="mt-1 text-2xl font-bold text-black" x-text="sampleHistory.title"></h2>
          </div>
          <button type="button" @click="activeModal = null"
                  class="flex h-8 w-8 items-center justify-center rounded-full text-grey-2 hover:bg-gray-100 hover:text-black transition-colors cursor-pointer">
            <x-solar-close-circle-linear class="h-6 w-6" />
          </button>
        </div>

        {{-- Overview --}}
        <div class="rounded-xl bg-primary-3/50 p-4 border border-primary-1/20">
          <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 items-center">
            <div class="space-y-1">
              <div class="flex items-center gap-1.5 text-xs font-semibold text-primary-1">
                <x-solar-calendar-linear class="h-4 w-4" />
                <span>Durasi Polling</span>
              </div>
              <p class="text-xs font-bold text-black" x-text="sampleHistory.full_duration"></p>
            </div>
            <div class="space-y-1 border-t sm:border-t-0 sm:border-l border-primary-1/20 sm:pl-5">
              <div class="flex items-center gap-1.5 text-xs font-semibold text-primary-1">
                <x-solar-user-linear class="h-4 w-4" />
                <span>Partisipan</span>
              </div>
              <p class="text-sm font-bold text-primary-1" x-text="sampleHistory.participants + ' Orang'"></p>
            </div>
            <div class="space-y-1 border-t sm:border-t-0 sm:border-l border-primary-1/20 sm:pl-5">
              <div class="flex items-center gap-1.5 text-xs font-semibold text-primary-1">
                <span>Peserta :</span>
              </div>
              <p class="text-xs font-medium text-black truncate" x-text="sampleHistory.peserta_type"></p>
            </div>
          </div>
        </div>

        {{-- Cards Grid --}}
        <div class="mt-6 grid grid-cols-1 md:grid-cols-12 gap-5 items-stretch">
          {{-- Kaos --}}
          <div class="md:col-span-5 flex flex-col space-y-2">
            <div class="flex items-center gap-2 text-sm font-bold text-primary-1">
              <x-solar-t-shirt-bold class="h-4 w-4 text-primary-1" />
              <span>Desain Kaos Wisata :</span>
            </div>
            <div class="flex-1 rounded-2xl border border-grey-1 bg-white p-4 shadow-sm flex flex-col justify-between">
              <div>
                <h4 class="text-base font-bold text-black" x-text="sampleHistory.kaos.title"></h4>
                <div class="mt-3 aspect-[4/3] w-full overflow-hidden rounded-xl bg-gray-100 cursor-pointer group relative"
                     @click="previewImage = sampleHistory.kaos.image">
                  <img :src="sampleHistory.kaos.image" alt="Kaos" class="h-full w-full object-cover group-hover:scale-105 transition-transform duration-300">
                  <div class="absolute inset-0 bg-black/0 group-hover:bg-black/20 transition-colors flex items-center justify-center opacity-0 group-hover:opacity-100">
                    <span class="rounded-full bg-white/90 p-2 text-black shadow-md">
                      <x-solar-magnifer-zoom-in-linear class="h-5 w-5" />
                    </span>
                  </div>
                </div>
                <div class="mt-3 grid grid-cols-2 gap-2">
                  <template x-for="(thumb, tIdx) in sampleHistory.kaos.gallery" :key="tIdx">
                    <div class="relative aspect-[4/3] overflow-hidden rounded-lg bg-gray-100 cursor-pointer group"
                         @click="previewImage = thumb">
                      <img :src="thumb" class="h-full w-full object-cover group-hover:scale-105 transition-transform duration-300">
                      <template x-if="tIdx === 1">
                        <div class="absolute inset-0 flex items-center justify-center bg-black/40 text-xs font-bold text-white">+2</div>
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

          {{-- Wisata --}}
          <div class="md:col-span-7 flex flex-col space-y-2">
            <div class="flex items-center gap-2 text-sm font-bold text-primary-1">
              <x-solar-map-point-bold class="h-4 w-4 text-primary-1" />
              <span>Destinasi Wisata :</span>
            </div>
            <div class="flex-1 rounded-2xl border border-grey-1 bg-white p-4 shadow-sm flex flex-col justify-between">
              <div>
                <div class="flex items-center justify-between">
                  <h4 class="text-base font-bold text-black" x-text="sampleHistory.wisata.title"></h4>
                  <div class="flex items-center gap-1 text-sm font-bold text-black">
                    <x-solar-star-bold class="h-4 w-4 text-warning" />
                    <span x-text="sampleHistory.wisata.rating"></span>
                  </div>
                </div>
                <div class="mt-3 grid grid-cols-1 sm:grid-cols-3 gap-2">
                  <div class="sm:col-span-2 aspect-[16/10] overflow-hidden rounded-xl bg-gray-100 cursor-pointer group relative"
                       @click="previewImage = sampleHistory.wisata.image">
                    <img :src="sampleHistory.wisata.image" alt="Wisata" class="h-full w-full object-cover group-hover:scale-105 transition-transform duration-300">
                    <div class="absolute inset-0 bg-black/0 group-hover:bg-black/20 transition-colors flex items-center justify-center opacity-0 group-hover:opacity-100">
                      <span class="rounded-full bg-white/90 p-2 text-black shadow-md">
                        <x-solar-magnifer-zoom-in-linear class="h-5 w-5" />
                      </span>
                    </div>
                  </div>
                  <div class="flex sm:flex-col gap-2">
                    <template x-for="(wThumb, wIdx) in sampleHistory.wisata.gallery" :key="wIdx">
                      <div class="relative flex-1 aspect-[16/10] overflow-hidden rounded-xl bg-gray-100 cursor-pointer group"
                           @click="previewImage = wThumb">
                        <img :src="wThumb" class="h-full w-full object-cover group-hover:scale-105 transition-transform duration-300">
                        <template x-if="wIdx === 1">
                          <div class="absolute inset-0 flex items-center justify-center bg-black/40 text-xs font-bold text-white">+2</div>
                        </template>
                      </div>
                    </template>
                  </div>
                </div>

                <div class="mt-4 space-y-2.5 text-xs">
                  <div class="flex justify-between items-start">
                    <div>
                      <span class="text-[11px] text-grey-deep">Lokasi</span>
                      <p class="font-medium text-black mt-0.5" x-text="sampleHistory.wisata.lokasi"></p>
                    </div>
                    <span class="text-[11px] text-primary-1 font-semibold" x-text="'Dikunjungi: ' + sampleHistory.wisata.dikunjungi"></span>
                  </div>
                  <div class="grid grid-cols-2 gap-4 pt-1">
                    <div>
                      <span class="text-[11px] text-grey-deep">Jarak</span>
                      <p class="font-bold text-black mt-0.5" x-text="sampleHistory.wisata.jarak"></p>
                    </div>
                    <div>
                      <span class="text-[11px] text-grey-deep">Lama Perjalanan</span>
                      <p class="font-bold text-black mt-0.5" x-text="sampleHistory.wisata.durasi"></p>
                    </div>
                  </div>
                  <div class="pt-1">
                    <span class="text-[11px] text-grey-deep">Benefit:</span>
                    <div class="mt-1.5 flex flex-wrap gap-1.5">
                      <template x-for="(ben, bIdx) in sampleHistory.wisata.benefits" :key="bIdx">
                        <span class="rounded-md border border-grey-1 bg-white px-2 py-0.5 text-[10px] font-medium text-grey-deep" x-text="ben"></span>
                      </template>
                    </div>
                  </div>
                </div>
              </div>
              <div class="mt-5 pt-3 border-t border-gray-100 flex items-center justify-between">
                <span class="text-xs font-semibold text-grey-deep">Cashback Peserta:</span>
                <span class="text-xl font-bold text-primary-1" x-text="sampleHistory.wisata.biaya"></span>
              </div>
            </div>
          </div>
        </div>
      </div>
    </x-modal>

    {{-- Lightbox --}}
    <x-modal show="Boolean(previewImage)" close="previewImage = null" maxWidth="lg">
      <div class="relative p-2 bg-black/90 rounded-2xl flex flex-col items-center">
        <button type="button" @click="previewImage = null"
                class="absolute top-4 right-4 z-10 flex h-8 w-8 items-center justify-center rounded-full bg-white/20 text-white hover:bg-white/40 transition-colors cursor-pointer">
          <x-solar-close-circle-linear class="h-6 w-6" />
        </button>
        <div class="w-full max-h-[80vh] flex items-center justify-center overflow-hidden rounded-xl p-2">
          <img :src="previewImage" alt="Preview Foto" class="max-h-[75vh] w-auto max-w-full object-contain rounded-lg shadow-2xl">
        </div>
      </div>
    </x-modal>
  </x-card>

  {{-- ===== Quick Cheat Sheet ===== --}}
  <x-card class="p-6 border border-gray-200 shadow-sm bg-gray-50">
    <h2 class="text-sm font-bold text-black">📋 Ringkasan Query Parameter</h2>
    <div class="mt-3 overflow-x-auto">
      <table class="w-full text-xs border-separate border-spacing-y-1">
        <thead>
          <tr class="text-left text-grey-deep">
            <th class="py-2 px-3 font-semibold">Parameter</th>
            <th class="py-2 px-3 font-semibold">Efek</th>
            <th class="py-2 px-3 font-semibold">Contoh URL</th>
          </tr>
        </thead>
        <tbody>
          <tr class="bg-white rounded-sm">
            <td class="py-2 px-3 font-mono text-primary-1">?empty=1</td>
            <td class="py-2 px-3">Semua data kosong → lihat empty state</td>
            <td class="py-2 px-3 font-mono text-[11px]">/admin/polling/arsip?empty=1</td>
          </tr>
          <tr class="bg-white rounded-sm">
            <td class="py-2 px-3 font-mono text-primary-1">?active=0</td>
            <td class="py-2 px-3">Hapus polling Aktif → test publish draft</td>
            <td class="py-2 px-3 font-mono text-[11px]">/admin/polling/arsip?active=0</td>
          </tr>
          <tr class="bg-white rounded-sm">
            <td class="py-2 px-3 font-mono text-primary-1">?state=draft</td>
            <td class="py-2 px-3">Filter hanya status Draft</td>
            <td class="py-2 px-3 font-mono text-[11px]">/admin/polling/arsip?state=draft</td>
          </tr>
          <tr class="bg-white rounded-sm">
            <td class="py-2 px-3 font-mono text-primary-1">?state=selesai</td>
            <td class="py-2 px-3">Filter hanya status Selesai</td>
            <td class="py-2 px-3 font-mono text-[11px]">/admin/polling/arsip?state=selesai</td>
          </tr>
          <tr class="bg-white rounded-sm">
            <td class="py-2 px-3 font-mono text-primary-1">?state=aktif</td>
            <td class="py-2 px-3">Filter hanya status Aktif</td>
            <td class="py-2 px-3 font-mono text-[11px]">/admin/polling/arsip?state=aktif</td>
          </tr>
        </tbody>
      </table>
    </div>
  </x-card>

</div>
@endsection
