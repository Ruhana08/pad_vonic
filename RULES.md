# 🎨 Vonic Admin Area - Structure & UI Guidelines

Dokumen ini berisi konvensi struktur folder Laravel, panduan tata letak (layout), ukuran *gap*, *padding*, serta penggunaan font untuk aplikasi Vonic.

---

## 📁 1. Konvensi Struktur Folder Blade (`resources/views/`)

Untuk menjaga kode tetap bersih (*clean code*) dan sesuai *best practice* Laravel:
* **Layout Partials** disimpan di `layouts/partials/` (elemen pembungkus halaman seperti navbar, sidebar, footer).
* **Blade Components** disimpan di `components/` (elemen UI reusabel seperti tombol, badge, input).
* **Modul Fitur** diletakkan di dalam folder fitur masing-masing (misal: `admin/`).

```text
resources/views/
├── components/                  <-- Komponen UI reusabel (dipanggil via <x-button>)
│   ├── button.blade.php
│   └── icon.blade.php
│
├── layouts/                     <-- Kerangka utama & partials layout
│   ├── admin.blade.php          <-- Master Layout Admin
│   └── partials/                <-- Partials pembungkus layout
│       ├── navbar.blade.php     <-- Top Navbar Fixed
│       ├── sidebar.blade.php    <-- Sidebar Navigation Fixed
│       └── footer.blade.php     <-- Footer Admin
│
└── admin/                       <-- Halaman spesifik fitur admin
    ├── dashboard.blade.php      <-- Halaman Dashboard (@extends('layouts.admin'))
    ├── polling/                 <-- Modul Polling
    └── akun/                    <-- Modul Akun Pengguna

## 1. Top Navbar Standard (`navbar.blade.php`)
* **Position:** Fixed full width (`fixed top-0 left-0 right-0 z-30`)
* **Height:** `h-[72px]` (72px)
* **Padding Outer:** `px-8` (32px)
* **Border:** `border-b border-gray-500`

### Sisi Kiri (Brand & Title)
* **Group Logo Gap:** `gap-3` (12px) — *Jarak antara Logo UGM dan Vonic Blue*
* **Logo to Title Gap:** `gap-6` (24px) — *Jarak antara Group Logo ke teks "Admin Area"*
* **Logo Dimensions:**
  * Logo UGM: `h-9`
  * Logo Vonic Blue: `h-8`
* **Title Font:** `font-sans text-2xl font-bold text-primary-1`

### Sisi Kanan (Controls & User Profile)
* **Control Group Gap:** `gap-3` (12px) — *Jarak antara Tombol Notifikasi dan Profile Dropdown*
* **Profile Internal Gap:** `gap-2.5` (10px) — *Jarak avatar ke teks nama user*

---

## 2. Sidebar Standard (`sidebar.blade.php`)
* **Position:** Fixed di bawah navbar (`fixed top-[72px] bottom-0 left-0 z-20`)
* **Width:** `w-[280px]` (280px)
* **Border:** `border-r border-gray-500`
* **Nav Padding:** `px-5 py-6`
* **Menu Item Spacing:** `gap-2.5` (10px antar item menu)

---

## 3. Font Family Usage
* **Primary (Sans):** `'Be Vietnam Pro', sans-serif` (`font-sans`)
  * *Digunakan untuk:* Teks menu, isi konten, tabel, tombol, dan instruksi UI.
* **Primary (Sans):** `'Be Vietnam Pro', sans-serif` (`font-sans`) - Digunakan untuk seluruh teks, branding, dan UI.


---

## 4. Spacing Quick Reference Table
| Komponen | Utility Class | Ukuran Pixels |
| :--- | :--- | :--- |
| Gap Antar Logo | `gap-3` | 12px |
| Gap Logo ke Judul Navbar | `gap-6` | 24px |
| Gap Tombol Navbar Kanan | `gap-3` | 12px |
| Gap Internal Profil | `gap-2.5` | 10px |
| Gap Item Menu Sidebar | `gap-2.5` | 10px |
| Padding Horizontal Outer | `px-8` | 32px |