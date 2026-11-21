# Vonic

Aplikasi web *e-voting* destinasi dan kaos DTEDI SV UGM. Repo: `Ruhana08/pad_vonic`.

> Dokumen ini ditulis dari kondisi kode saat ini. Bagian bertanda **TODO** belum terverifikasi dan perlu dilengkapi tim.

## Daftar Isi

- [Tech Stack](#tech-stack)
- [Menjalankan Project](#menjalankan-project)
- [Struktur Folder](#struktur-folder)
- [Design System](#design-system)
- [Komponen Blade](#komponen-blade)
- [Frontend: Axios dan Autentikasi](#frontend-axios-dan-autentikasi)
- [Alur Kerja Git](#alur-kerja-git)
- [Aturan Penulisan Kode FE](#aturan-penulisan-kode-fe)

## Tech Stack

| Bagian | Teknologi |
| :--- | :--- |
| Backend | Laravel (versi: **TODO**, cek `composer.json`), Laravel Sanctum |
| View | Blade + komponen anonim (`<x-...>`) |
| Styling | Tailwind CSS v4 (`@tailwindcss/vite`), tema di `resources/css/app.css` |
| Bundler | Vite 8 + `laravel-vite-plugin` |
| JS | Axios, Hotwired Turbo |
| Font | Be Vietnam Pro (Google Fonts) |
| Database | MySQL/MariaDB (Laragon) |

**TODO:** konfirmasi apakah Alpine.js dipakai (tidak ada di `package.json`, mungkin lewat CDN di layout) dan apakah paket ikon (misalnya Blade Solar Icons) ada di `composer.json`.

## Menjalankan Project

Kebutuhan: PHP sesuai versi Laravel, Composer, Node.js, MySQL (Laragon).

```bash
git clone https://github.com/Ruhana08/pad_vonic.git
cd pad_vonic

composer install
npm install

cp .env.example .env        # Windows PowerShell: copy .env.example .env
php artisan key:generate
```

Atur koneksi database di `.env`, lalu:

```bash
php artisan migrate         # tambahkan --seed bila ada seeder
npm run dev                 # development (Vite)
php artisan serve           # server Laravel, terminal terpisah
```

Build produksi: `npm run build`.

Jangan commit `.env` atau kredensial apa pun.

**TODO:** variabel `.env` khusus project (Google OAuth, dll.) dan nama database yang dipakai tim.

## Struktur Folder

Bagian frontend (`resources/`):

```text
resources/
├── css/
│   └── app.css                 # Tailwind v4: theme token, tipografi, tabel, card, button
├── js/
│   ├── app.js
│   └── bootstrap.js            # Konfigurasi Axios + interceptor
└── views/
    ├── home.blade.php
    ├── voting.blade.php
    ├── riwayat.blade.php
    ├── profile.blade.php
    ├── welcome.blade.php
    ├── test.blade.php          # halaman uji
    ├── admin/
    │   ├── dashboard.blade.php
    │   ├── preview.blade.php
    │   ├── profil.blade.php
    │   ├── akun/index.blade.php
    │   └── polling/            # aktif, arsip, create, detail
    ├── auth/                   # login, user-login, register, forgot-password, google-callback
    ├── components/             # komponen Blade reusable (lihat bagian Komponen)
    └── layouts/
        ├── admin.blade.php
        ├── auth.blade.php
        ├── focus.blade.php
        ├── user.blade.php
        ├── user-voting.blade.php
        └── partials/           # navbar, sidebar, footer
```

**TODO:** struktur backend (`app/`, `routes/`, `database/`) dan tabel rute URL. Tempel isi `routes/web.php` dan `routes/api.php` untuk melengkapinya.

## Design System

Semua token ada di blok `@theme` pada `resources/css/app.css`. **Pakai token/utility class, jangan hardcode hex di komponen.**

### Warna

| Kelompok | Token | Hex |
| :--- | :--- | :--- |
| Primary | `primary-1` | `#196BEF` |
| | `primary-2` | `#669CF4` |
| | `primary-3` | `#EAF2FF` |
| | `primary-4` | `#0C5BDA` |
| | `primary-track` | `#D6E4FA` |
| Secondary | `secondary-1` | `#053788` |
| | `secondary-2` | `#2F5085` |
| | `secondary-3` | `#082450` |
| Neutral | `black` | `#191919` |
| | `grey-dark` | `#4A4A4A` |
| | `grey-deep` | `#6D6D6D` |
| | `grey-2` | `#848484` |
| | `grey-1` | `#DBDBDB` |
| | `gray-50` | `#FAFAFA` |
| Aksen | `success` / `success-soft` | `#188F00` / `#DAFFD1` |
| | `danger` / `danger-soft` | `#FE2323` / `#FFC2C2` |
| | `warning` | `#FFF70E` |

Contoh pemakaian: `text-primary-1`, `bg-primary-3`, `border-grey-1`.

Gradien utama (Main-Blue): `linear-gradient(90deg, #053788 0%, #196BEF 100%)`, tersedia sebagai `.bg-gradient-main-blue`.

### Radius

`--radius-xs` 4px, `--radius-sm` 8px, `--radius-button` 8px, `--radius-md` 14px, `--radius-card` 16px, `--radius-lg` 24px.

### Shadow

`shadow-menu`, `shadow-nav`, `shadow-control`, `shadow-primary-glow`, `shadow-toast`, `shadow-type-1`, `shadow-type-2`, `shadow-type-3`.

### Tipografi

Font: **Be Vietnam Pro**. Heading otomatis ter-style lewat tag (`h1`...`h6`) atau class `.h1`...`.h6`. Teks isi memakai class `.body-1`...`.body-5`.

| Skala | Ukuran | Weight |
| :--- | :--- | :--- |
| `h1` | 32px | 700 |
| `h2` | 24px | 700 |
| `h3` | 20px | 600 |
| `h4` | 18px | 600 |
| `h5` | 16px | 600 |
| `h6` | 14px | 600 |
| `body-1` | 16px | 400 |
| `body-2` | 14px | 400 |
| `body-3` | 13px | 400 |
| `body-4` | 12px | 400 |
| `body-5` | 11px | 400 |

### Button

Dibuat sebagai class CSS di `app.css`. Semua punya state hover, active, dan disabled (`:disabled` atau `.disabled`). Radius `rounded-xl` (12px).

| Class | Fungsi |
| :--- | :--- |
| `.btn-gradient` | CTA utama dengan gradien Main-Blue |
| `.btn-primary` | Tombol solid biru |
| `.btn-secondary` / `.btn-outline` | Putih, border abu, hover biru |
| `.btn-danger` | Aksi berbahaya (soft merah, hover merah solid) |
| `.btn-icon`, `.btn-icon-danger` | Tombol ikon persegi 32px (aksi tabel/quick action) |

```html
<button class="btn-primary px-4 py-2">Simpan</button>
<button class="btn-outline px-4 py-2">Batal</button>
<button class="btn-icon btn-icon-danger" aria-label="Hapus">...</button>
```

### Card

| Class | Fungsi |
| :--- | :--- |
| `.card-hero-polling` | Hero polling aktif (border biru 2px) |
| `.card-hero-faq` | Hero FAQ (background primary) |
| `.card-panel` | Container section utama |
| `.card-showcase` | Item riwayat dan preview modal (ada hover shadow) |
| `.card-choice` | Kartu pilihan interaktif; state `.is-active` / `.active` / `[aria-checked="true"]` |
| `.card-stepper` | Kartu counter (ukuran kaos); state `.is-active` / `.active` |
| `.card-stat` | Kartu metrik/statistik |
| `.card-info-box` | Kotak info kecil/metadata |
| `.card-empty-state` | Empty state (border putus-putus) |

### Tabel (global)

Semua `<table>` otomatis distandarkan di `app.css`. Jadi **tidak perlu** menimpa padding, ukuran font, atau warna di tiap tabel.

- Header (`th`): 12px, bold, uppercase, rata tengah, padding 14px.
- Isi (`td`): 12px, warna `grey-dark`, tanpa `text-transform`.
- Baris `tbody tr` otomatis hover biru muda (`#EAF2FF`).
- Baris yang tidak boleh ikut aturan ini (misalnya banner sub-judul kategori): beri class **`no-hover`**.
- Elemen dengan class `bg-*` / `text-*` / `badge` di dalam sel tidak ditimpa warnanya.

Catatan: aturan tabel memakai `!important`. Kalau butuh pengecualian, pakai class `no-hover` atau struktur elemen yang dikecualikan, bukan menimpa dengan CSS tambahan.

## Komponen Blade

Lokasi: `resources/views/components/`. Dipakai dengan awalan `x-`, misalnya `<x-card>`.

| Komponen | Tag |
| :--- | :--- |
| action-button | `<x-action-button>` |
| avatar | `<x-avatar>` |
| badge | `<x-badge>` |
| button | `<x-button>` |
| card | `<x-card>` |
| countdown | `<x-countdown>` |
| datepicker | `<x-datepicker>` |
| dropdown-select | `<x-dropdown-select>` |
| empty-state | `<x-empty-state>` |
| faq-card | `<x-faq-card>` |
| flash-message | `<x-flash-message>` |
| icon | `<x-icon>` |
| leaderboard-card | `<x-leaderboard-card>` |
| mini-pagination | `<x-mini-pagination>` |
| modal | `<x-modal>` |
| modal-hasil-voting | `<x-modal-hasil-voting>` |
| option-card | `<x-option-card>` |
| progress | `<x-progress>` |
| rich-editor | `<x-rich-editor>` |
| section-header | `<x-section-header>` |
| stat-card | `<x-stat-card>` |
| status | `<x-status>` |
| table | `<x-table>` |
| table-pagination | `<x-table-pagination>` |
| timepicker | `<x-timepicker>` |

`Button.figma.ts` adalah file pemetaan komponen Button ke Figma (Code Connect), bukan komponen Blade.

**TODO:** dokumentasikan props tiap komponen (terutama `button`, `card`, `option-card`, `modal`, `table`, `status`). Cara cepat: buka file `.blade.php`-nya dan salin bagian `@props([...])`.

## Frontend: Axios dan Autentikasi

`resources/js/bootstrap.js` mengatur Axios secara global (`window.axios`):

- Header `X-Requested-With: XMLHttpRequest` selalu dikirim.
- Token dibaca dari `localStorage` (`vonic_token`) dan otomatis dikirim sebagai `Authorization: Bearer <token>`.
- Respons `401` (selain endpoint `/api/login`) menghapus `vonic_token` dan `vonic_user` dari `localStorage`.
- **Auto-redirect ke `/login?expired=1` saat ini dinonaktifkan** untuk tahap testing. Aktifkan lagi sebelum rilis (baris `window.location.href` yang di-comment).

## Alur Kerja Git

```text
main  <──── PR (rilis, oleh lead) ──── dev
                                        ▲
                           PR (FE) ─────┴───── PR (BE)
                              │                   │
                   feature/fe-nama-fitur   feature/be-nama-fitur
```

1. **Jangan push langsung ke `main` atau `dev`.** Semua lewat Pull Request.
2. Mulai dari `dev` terbaru, satu fitur satu branch:
   ```bash
   git checkout dev
   git pull origin dev
   git checkout -b feature/fe-nama-fitur
   ```
3. Commit sering dengan pesan jelas (`feat: ...`, `fix: ...`, `refactor: ...`).
4. Sebelum PR, tarik update dev supaya konflik selesai di branch sendiri:
   ```bash
   git fetch origin
   git merge origin/dev
   git push -u origin feature/fe-nama-fitur
   ```
5. Buka PR dengan **base: `dev`**, **compare: branch kamu**. Isi deskripsi (apa yang berubah, kenapa, cara tes) dan minta minimal 1 reviewer.
6. Kalau FE butuh endpoint BE, sepakati kontrak API dulu dan tulis di PR. BE masuk ke `dev` lebih dulu atau bersamaan.
7. Hanya lead yang membuka PR `dev` → `main`, setelah FE dan BE dites bareng di `dev`.
8. Jangan pakai `git push --force` di branch bersama.

Hotfix: buat branch `hotfix/nama-bug` dari `main`, PR ke `main`, lalu merge `main` ke `dev`.

## Aturan Penulisan Kode FE

- Pakai token warna, radius, dan shadow dari `app.css`. Jangan hardcode hex.
- Pakai class `.btn-*` dan `.card-*` yang sudah ada sebelum membuat style baru.
- Jangan override style tabel global di masing-masing halaman. Pakai `no-hover` untuk pengecualian.
- Komponen yang dipakai berulang dibuat di `resources/views/components/`, bukan disalin antar halaman.
- Semua request HTTP dari JS lewat `window.axios` agar token dan interceptor ikut berlaku.
- Pilih layout sesuai konteks: `user`, `user-voting`, `admin`, `auth`, atau `focus`.

## Tim

Departemen Teknik Elektro dan Informatika, Sekolah Vokasi, Universitas Gadjah Mada.