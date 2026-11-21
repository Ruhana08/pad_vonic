# Vonic — Sistem E-Voting Destinasi & Kaos DTEDI SV UGM

Sistem informasi *E-Voting* resmi Departemen Teknik Elektro dan Informatika, Sekolah Vokasi, Universitas Gadjah Mada (**DTEDI SV UGM**) untuk penentuan destinasi piknik keluarga sivitas akademika dan pemilihan ukuran/desain kaos secara demokratis, transparan, dan terintegrasi akun Google SSO Sivitas UGM. Dibangun dengan arsitektur modern berbasis **Laravel 11**, **Tailwind CSS v4**, **Alpine.js**, dan komponen **Blade** modular yang berakar pada Design System terstandarisasi.

---

## 📑 Daftar Isi
- [Arsitektur Informasi (IA) & Routing](#-arsitektur-informasi-ia--routing)
- [Design System & Palet Warna](#-design-system--palet-warna)
- [Hierarki Tipografi](#-hierarki-tipografi)
- [Panduan Solar Icons](#-panduan-solar-icons)
- [Katalog Komponen Blade UI](#-katalog-komponen-blade-ui)
  - [1. Button Component (`x-button`)](#1-button-component-x-button)
  - [2. Status & Badge Component (`x-status` / `x-badge`)](#2-status--badge-component-x-status--x-badge)
  - [3. Card Component (`x-card`)](#3-card-component-x-card)
  - [4. Option Card (`x-option-card`)](#4-option-card-x-option-card)
  - [5. Leaderboard Card (`x-leaderboard-card`)](#5-leaderboard-card-x-leaderboard-card)
  - [6. Flash Message Toast (`x-flash-message`)](#6-flash-message-toast-x-flash-message)
  - [7. Navbar & Notifikasi Multi-Role](#7-navbar--notifikasi-multi-role)
- [Struktur Direktori Project](#-struktur-direktori-project)
- [Panduan Menjalankan Project](#-panduan-menjalankan-project)

---

## 🗺️ Arsitektur Informasi (IA) & Routing

Sistem Vonic membagi hak akses ke dalam dua area utama: **Sivitas (User Pemilih)** dan **Admin Area**.

### 1. Sivitas Akademika (User Area)
| Halaman | Tipe Akses | Rute URL | Deskripsi Utama |
| :--- | :--- | :--- | :--- |
| **Login Sivitas** | Guest | `/login` | Otentikasi sivitas via SSO Google Mail UGM (`@ugm.ac.id` / `@mail.ugm.ac.id`). |
| **Beranda User** | Authenticated / Guest Preview | `/home` | Banner polling aktif, countdown penutupan, FAQ panduan, & ringkasan riwayat. |
| **Bilik Voting** | Authenticated | `/voting?periode={id}` | Alur pemilihan bertahap: Pilihan Peserta, Destinasi Wisata, & Desain/Ukuran Kaos. |
| **Riwayat Pemilihan** | Authenticated | `/riwayat` | Arsip pemilihan periode selesai dan hasil destinasi terpilih. |
| **Profil Sivitas** | Authenticated | `/profile` | Informasi akun, status hak suara, dan riwayat partisipasi individual. |

### 2. Admin Area
| Halaman | Tipe Akses | Rute URL | Deskripsi Utama |
| :--- | :--- | :--- | :--- |
| **Admin Login** | Guest | `/admin/login` | Portal masuk pengelola sistem dengan kredensial email & password/OTP. |
| **Dashboard** | Admin Auth | `/admin/dashboard` | Metrik partisipasi cepat, grafik aktivitas terkini, dan status quick summary. |
| **Polling Aktif** | Admin Auth | `/admin/polling/aktif` | Live-count perolehan suara destinasi & kaos real-time, broadcast pengumuman, dan export CSV data kaos. |
| **Arsip Polling** | Admin Auth | `/admin/polling/arsip` | Manajemen status (Draft, Aktif, Selesai), filter periode, dan audit data. |
| **Buat Polling** | Admin Auth | `/admin/polling/create` | Form penyusunan periode baru, upload paket wisata, tanggal batas waktu, dan opsi kaos. |
| **Kelola Akun** | Admin Auth | `/admin/akun` | Daftar sivitas terdaftar, import massal via CSV, dan manajemen hak suara. |
| **Profil Admin** | Admin Auth | `/admin/profil` | Konfigurasi profil instansi, ganti password, dan penambahan admin baru. |

---

## 🎨 Design System & Palet Warna

> **Aturan Wajib:** Hindari penggunaan nilai Hex mentah (*hardcoded*) di dalam komponen. Selalu gunakan *utility classes* tema dari `resources/css/app.css` (Tailwind Theme Token).

### 1. Primary Colors (Identitas Utama UGM & Vonic)
| Warna Visual | Token Tailwind | Variabel CSS | Nilai Hex | Peruntukan Utama |
| :---: | :--- | :--- | :---: | :--- |
| ![#196BEF](https://placehold.co/24x24/196BEF/196BEF.png) | `primary-1` | `--color-primary-1` | `#196BEF` | Brand utama Vonic, Button Primary, tautan aktif, tab terpilih |
| ![#669CF4](https://placehold.co/24x24/669CF4/669CF4.png) | `primary-2` | `--color-primary-2` | `#669CF4` | Aksen border fokus, visual kartu sekunder |
| ![#EAF2FF](https://placehold.co/24x24/EAF2FF/EAF2FF.png) | `primary-3` | `--color-primary-3` | `#EAF2FF` | Surface tinted background, badge info, hover row |
| ![#0C5BDA](https://placehold.co/24x24/0C5BDA/0C5BDA.png) | `primary-4` | `--color-primary-4` | `#0C5BDA` | State hover button primary, teks penekanan khusus |
| ![#D6E4FA](https://placehold.co/24x24/D6E4FA/D6E4FA.png) | `primary-track` | `--color-primary-track`| `#D6E4FA` | Track background untuk progress bar perolehan suara |

### 2. Secondary Colors (Deep Navy Brand)
| Warna Visual | Token Tailwind | Variabel CSS | Nilai Hex | Peruntukan Utama |
| :---: | :--- | :--- | :---: | :--- |
| ![#053788](https://placehold.co/24x24/053788/053788.png) | `secondary-1` | `--color-secondary-1` | `#053788` | Warna dasar gradien hero, header banner UGM |
| ![#2F5085](https://placehold.co/24x24/2F5085/2F5085.png) | `secondary-2` | `--color-secondary-2` | `#2F5085` | Subtitle teks pekat, aksen batas kartu elegan |
| ![#082450](https://placehold.co/24x24/082450/082450.png) | `secondary-3` | `--color-secondary-3` | `#082450` | Background footer pekat & watermark kontras |

### 3. Neutral & Monokrom
| Warna Visual | Token Tailwind | Variabel CSS | Nilai Hex | Peruntukan Utama |
| :---: | :--- | :--- | :---: | :--- |
| ![#191919](https://placehold.co/24x24/191919/191919.png) | `black` | `--color-black` | `#191919` | Judul section utama, nama destinasi, headings |
| ![#4A4A4A](https://placehold.co/24x24/4A4A4A/4A4A4A.png) | `grey-dark` | `--color-grey-dark` | `#4A4A4A` | Teks paragraf, label form input |
| ![#6D6D6D](https://placehold.co/24x24/6D6D6D/6D6D6D.png) | `grey-deep` | `--color-grey-deep` | `#6D6D6D` | Deskripsi card, catatan pembantu, meta text |
| ![#848484](https://placehold.co/24x24/848484/848484.png) | `grey-2` | `--color-grey-2` | `#848484` | Placeholder input, ikon statis sekunder |
| ![#DBDBDB](https://placehold.co/24x24/DBDBDB/DBDBDB.png) | `grey-1` | `--color-grey-1` | `#DBDBDB` | Garis border standar, divider section |
| ![#FAFAFA](https://placehold.co/24x24/FAFAFA/FAFAFA.png) | `gray-50` | `--color-gray-50` | `#FAFAFA` | Surface background utama aplikasi |

### 4. Status & Feedback
| Warna Visual | Token Tailwind | Variabel CSS | Nilai Hex | Peruntukan |
| :---: | :--- | :--- | :---: | :--- |
| ![#188F00](https://placehold.co/24x24/188F00/188F00.png) | `success` | `--color-success` | `#188F00` | Status "Aktif", notifikasi sukses, pilihan terverifikasi |
| ![#DAFFD1](https://placehold.co/24x24/DAFFD1/DAFFD1.png) | `success-soft`| `--color-success-soft` | `#DAFFD1` | Background pill badge status "Aktif" |
| ![#FE2323](https://placehold.co/24x24/FE2323/FE2323.png) | `danger` | `--color-danger` | `#FE2323` | Status error, hapus data, batas waktu habis |
| ![#FFC2C2](https://placehold.co/24x24/FFC2C2/FFC2C2.png) | `danger-soft` | `--color-danger-soft` | `#FFC2C2` | Background alert danger & dismiss hover |
| ![#FFF70E](https://placehold.co/24x24/FFF70E/FFF70E.png) | `warning` | `--color-warning` | `#FFF70E` | Peringatan countdown H-1 penutupan voting |

---

## 🔤 Hierarki Tipografi

Font resmi aplikasi adalah **Be Vietnam Pro**. Semua teks telah distandarisasi untuk keterbacaan optimal pada antarmuka voting:

| Skala Tipografi | Kelas Tailwind | Ukuran / Line-Height | Weight | Contoh Penggunaan |
| :--- | :--- | :--- | :---: | :--- |
| **Display H-1** | `text-3xl font-bold` | 30px / 36px | Bold (700) | Judul utama Hero Banner Beranda Sivitas |
| **Page Title H-2**| `text-2xl font-bold` | 24px / 32px | Bold (700) | Judul halaman admin & header section voting |
| **Section Title H-3**| `text-xl font-bold` | 20px / 28px | Bold (700) | Sub-header kartu, modal dialog title |
| **Card Title H-4** | `text-base font-bold` | 16px / 24px | Bold (700) | Nama destinasi wisata & paket kegiatan |
| **Body Standard** | `text-sm font-medium` | 14px / 20px | Medium (500) | Teks isi artikel FAQ, navigasi menu, label form |
| **Body Small** | `text-xs font-normal` | 12px / 18px | Regular (400) | Deskripsi info kartu, subtitle profil, countdown |
| **Micro / Badge** | `text-[10px] font-bold` | 10px / 14px | Bold (700) | Status pill, chip kategori, counter notifikasi |

---

## ☀️ Panduan Solar Icons

Aplikasi terintegrasi dengan **Blade Solar Icons** (`codeat3/blade-solaricons`).

### 1. Gaya Ikon (Styles)
- **Linear (`*-linear`)**: Garis tepi presisi untuk tombol navigasi, menu input, dan layout form.
- **Bold (`*-bold`)**: Bentuk solid tebal untuk indikator status feedback, modal peringatan, dan badge aktif.

### 2. Cara Penggunaan dalam Blade
```blade
{{-- Icon Linear pada Tombol Navigasi --}}
<x-solar-bell-linear class="h-5 w-5 text-primary-1" />

{{-- Icon Bold pada Status / Feedback --}}
<x-solar-check-circle-bold class="h-5 w-5 text-success" />
<x-solar-cup-star-bold class="h-4 w-4 text-primary-1" />
<x-solar-danger-triangle-bold class="h-5 w-5 text-danger" />
```

---

## 📦 Katalog Komponen Blade UI

Seluruh komponen UI berada di direktori `resources/views/components/` dan dapat digunakan dengan awalan `x-`.

### 1. Button Component (`x-button`)
**Path:** `resources/views/components/button.blade.php`  
Tombol serbaguna yang otomatis mendukung render tag `<a>` jika memiliki parameter `href`, atau tag `<button>` jika bertindak sebagai form trigger.

#### A. Varian & Karakteristik
- **`primary` (Default):** Background `#196BEF`, teks putih, hover `#0C5BDA`.
- **`secondary`:** Background `#EAF2FF`, teks `#196BEF`, hover `#D6E4FA`.
- **`outline`:** Border `1px solid #DBDBDB`, background putih, teks `#4A4A4A`.
- **`danger`:** Background `#FE2323`, teks putih, hover kecerahan gelap.

#### B. Contoh Penggunaan
```blade
{{-- Tombol Utama dengan Ikon --}}
<x-button variant="primary" size="md" href="{{ route('voting') }}">
    <x-solar-play-bold class="h-4 w-4 mr-2" />
    Ikuti Voting Sekarang
</x-button>

{{-- Tombol Form Submit --}}
<x-button variant="primary" type="submit" size="sm">
    Simpan Perubahan
</x-button>
```

---

### 2. Status & Badge Component (`x-status` / `x-badge`)
**Path:** `resources/views/components/status.blade.php` & `resources/views/components/badge.blade.php`  
Pill badge untuk menampilkan status periode polling atau kategori sivitas.

```blade
{{-- Badge Status Polling --}}
<x-status status="aktif" />    {{-- Hijau soft: Aktif --}}
<x-status status="draft" />    {{-- Abu-abu: Draft --}}
<x-status status="selesai" />  {{-- Biru soft: Selesai --}}

{{-- Badge Kategori --}}
<x-badge variant="primary">Dosen & Tendik</x-badge>
<x-badge variant="secondary">Pantai & Bahari</x-badge>
```

---

### 3. Card Component (`x-card`)
**Path:** `resources/views/components/card.blade.php`  
Container dasar berstandar Figma dengan radius halus (`rounded-2xl`), shadow elegan, dan border tipis `border-gray-100`.

```blade
<x-card class="p-6">
    <h3 class="text-base font-bold text-black">Destinasi Wisata Unggulan</h3>
    <p class="mt-1 text-xs text-grey-deep">Informasi detail lokasi kegiatan DTEDI SV UGM.</p>
</x-card>
```

---

### 4. Option Card (`x-option-card`)
**Path:** `resources/views/components/option-card.blade.php`  
Kartu pilihan interaktif pada bilik suara (`/voting`) dengan state *selected*, rasio gambar 16:9, dan deskripsi fasilitas destinasi.

```blade
<x-option-card
    id="paket-1"
    title="Pantai Baron & Drini, Gunungkidul"
    subtitle="Paket Tour Pantai Selatan & Snorkeling"
    badge="Wisata Alam"
    :selected="false"
/>
```

---

### 5. Leaderboard Card (`x-leaderboard-card`)
**Path:** `resources/views/components/leaderboard-card.blade.php`  
Menampilkan peringkat teratas perolehan suara secara real-time pada dashboard admin dan rekap riwayat.

```blade
<x-leaderboard-card
    rank="1"
    title="Dataran Tinggi Dieng"
    votes="142 Suara"
    percentage="64"
    isWinner="true"
/>
```

---

### 6. Flash Message Toast (`x-flash-message`)
**Path:** `resources/views/components/flash-message.blade.php`  
Floating notification toast yang otomatis terpicu dari Laravel session (`success`, `error`, `warning`) dengan animasi slide dan tombol dismiss standar.

```blade
{{-- Disematkan di layouts/user.blade.php & layouts/admin.blade.php --}}
<x-flash-message />
```

---

### 7. Navbar & Notifikasi Multi-Role
Navbar responsif terstandarisasi dengan menu dropdown profil & panel notifikasi:
- **Sivitas Header (`layouts/user.blade.php`):**
  - Brand Logo UGM + Vonic.
  - Link Tab Navigasi: *Beranda* & *Riwayat Pemilihan*.
  - Lonceng Notifikasi Cerdas: Memberitahu H-1 voting ditutup, voting baru aktif, hasil rilis 🏆, dan info pemeliharaan sistem.
- **Admin Navbar (`layouts/partials/navbar.blade.php`):**
  - Notifikasi suara baru masuk real-time, milestone 80% partisipasi, dan log aktivitas admin.

---

## 📂 Struktur Direktori Project

```text
C:\laragon\www\vonic\
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── Admin/           # Controller Admin (Dashboard, PollingAktif, Arsip, Akun, Profil)
│   │   │   ├── Api/             # RESTful API Endpoint (Polling, Voting, Auth)
│   │   │   └── Auth/            # Auth Controller, Google OAuth SSO, OTP Verification
│   │   └── Middleware/         # Sanctum Auth & Admin Role Guards
│   └── Models/                  # Eloquent Models (PeriodePolling, PaketWisata, Kaos, Partisipasi)
├── bootstrap/                   # Laravel Application Bootstrap
├── config/                      # Layanan, Database, Services (Google Socialite, Session)
├── database/
│   ├── migrations/              # Skema Tabel Database Polling & Partisipasi
│   └── seeders/                 # Database Seeder Data Master Sivitas & Periode
├── public/
│   ├── build/                   # Asset bundle hasil build Vite (CSS, JS, Fonts)
│   └── images/                  # Aset logo resmi UGM, Vonic, ilustrasi & avatar
├── resources/
│   ├── css/
│   │   └── app.css              # Tailwind v4 Theme Tokens, Colors, Radius, & Elevation
│   ├── js/
│   │   ├── app.js               # Entry point Alpine.js & custom scripts
│   │   └── bootstrap.js         # Axios client configuration & global interceptor
│   └── views/
│       ├── admin/               # Halaman Administrasi Polling & Sistem
│       ├── auth/                # Halaman Login Sivitas, Admin Login, & Registrasi OTP
│       ├── components/          # Komponen Blade UI Reusable (Button, Card, Status, dsb)
│       ├── layouts/             # Master Layouts (user.blade.php, admin.blade.php, focus.blade.php)
│       ├── home.blade.php       # Beranda Sivitas
│       ├── voting.blade.php     # Bilik Suara Interaktif Multi-Step
│       └── riwayat.blade.php    # Arsip Pemilihan Periode Lampau
├── routes/
│   ├── api.php                  # Endpoint data JSON Polling, Vote, & Riwayat
│   └── web.php                  # Routing tampilan Blade & autentikasi Google SSO
└── vite.config.js               # Konfigurasi bundler Vite + Tailwind CSS v4
```

---

## 🚀 Panduan Menjalankan Project

### 1. Kebutuhan Lingkungan (Prerequisites)
- **PHP:** `^8.2` atau `8.3` (dengan ekstensi `pdo_mysql`, `openssl`, `mbstring`, `curl`)
- **Composer:** `^2.x`
- **Node.js & NPM:** `^18.x` atau `^20.x`
- **Database:** MySQL / MariaDB (via Laragon / XAMPP)

### 2. Langkah Instalasi

1. **Clone & Masuk ke Direktori Project:**
   ```bash
   cd C:\laragon\www\vonic
   ```

2. **Instalasi Dependensi PHP & Frontend:**
   ```bash
   composer install
   npm install
   ```

3. **Konfigurasi Environment (`.env`):**
   Salin file template `.env.example` ke `.env` dan sesuaikan koneksi database MySQL:
   ```env
   APP_NAME=Vonic
   APP_ENV=local
   APP_URL=http://127.0.0.1:8000

   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=vonic
   DB_USERNAME=root
   DB_PASSWORD=

   # Google OAuth Sivitas (Jika menggunakan SSO Google)
   GOOGLE_CLIENT_ID=your-client-id.apps.googleusercontent.com
   GOOGLE_CLIENT_SECRET=your-client-secret
   GOOGLE_REDIRECT_URI=http://127.0.0.1:8000/auth/google/callback
   ```

4. **Generate Application Key & Migrasi Database:**
   ```bash
   php artisan key:generate
   php artisan migrate --seed
   ```

5. **Kompilasi Aset Frontend:**
   ```bash
   # Mode pengembangan (Hot Reload)
   npm run dev

   # ATAU build aset produksi
   npm run build
   ```

6. **Menjalankan Server:**
   ```bash
   php artisan serve
   ```
   Aplikasi siap diakses melalui browser di: `http://127.0.0.1:8000`.

---

## 👥 Tim Pengembang

- **Departemen Teknik Elektro dan Informatika (DTEDI)**  
  Sekolah Vokasi, Universitas Gadjah Mada  
  *© 2026 DTEDI SV UGM. All rights reserved.*
