# 🌸 Melati V2 — Portal Layanan TI Biro TIK BPK RI

[![Laravel 12.0](https://img.shields.io/badge/Laravel-12.0-red?style=flat-square&logo=laravel)](https://laravel.com)
[![Vite](https://img.shields.io/badge/Vite-7.0-purple?style=flat-square&logo=vite)](https://vite.dev)
[![Tailwind CSS 4.0](https://img.shields.io/badge/Tailwind_CSS-4.0-blue?style=flat-square&logo=tailwind-css)](https://tailwindcss.com)
[![Alpine.js](https://img.shields.io/badge/Alpine.js-3.x-emerald?style=flat-square&logo=alpine.js)](https://alpinejs.dev)

**Melati V2** adalah portal terintegrasi layanan bantuan Teknologi Informasi (IT Helpdesk & Service Desk) yang dirancang khusus untuk lingkungan internal **Badan Pemeriksa Keuangan Republik Indonesia (BPK RI)**. Aplikasi ini mempermudah pelaporan insiden, permintaan layanan, pengelolaan antrean tiket bantuan, hingga penugasan kerja solver TI secara cerdas dan efisien.

---

## 🚀 Fitur Utama & Keunggulan

### 🤖 1. Asisten Virtual Layanan TI (Gemini AI Chatbot)
* **Diagnosis Cerdas:** Pengguna dapat menceritakan kendala dengan bahasa sehari-hari. AI akan menganalisis masalah secara langsung dan menyarankan solusi troubleshooting mandiri.
* **Unggah & Tempel (Paste) Screenshot:** Pengguna dapat menempelkan screenshot (`Ctrl + V`) atau mengunggah gambar/foto kendala. AI akan secara otomatis menganalisis tampilan error atau masalah visual untuk memberikan arahan yang tepat.
* **Deteksi GIF Otomatis:** Mengamankan pengiriman dengan membatasi format `.gif` baik di frontend maupun backend.
* **Aturan Validasi & Troubleshooting Pintar:**
  * **Verifikasi Foto Perangkat Keras:** Untuk kendala hardware (seperti Laptop, Router, Proyektor, Mikrofon, Smartboard), AI menuntut unggahan foto fisik kerusakan atau bukti penyerahan ke Biro TI sebelum tombol pembuatan tiket diaktifkan.
  * **Pohon Keputusan Wi-Fi:** Penanganan kendala Wi-Fi lambat dilakukan bertahap (Solusi Awal -> Konfirmasi Lokasi Menara/Lantai -> Penawaran Pembuatan Tiket).
* **Auto-Routing & Auto-Fill Tiket:** AI secara cerdas menganalisis masalah, menentukan kategori/sub-kategori yang sesuai dari katalog layanan BPK RI, dan mengisi formulir pengaduan secara terstruktur.

### 👥 2. Workflow Kolaboratif Berbasis Peran (Roles)
* **Pelapor (Pengguna):** Mengajukan tiket baru, berdiskusi melalui obrolan langsung, dan melacak kemajuan pengerjaan tiket.
* **Operator Biro TI:** Memiliki visibilitas penuh atas seluruh tiket masuk, memindahkan rute subbagian (*Reassign*), atau langsung menugaskan tiket ke solver tertentu.
* **Kasubbag (Dispatcher):** Memverifikasi tiket masuk, mendistribusikan penugasan solver, mengembalikan tiket tidak layak ke operator, mengonfirmasi penyelesaian tiket, serta memantau peringatan visual tiket terlambat (*Overdue*).
* **Solver (Petugas Lapangan):** Mengambil tiket secara mandiri (*Claim*), menulis progress log penanganan (*Tindaklanjuti*), eskalasi balik ke Kasubbag jika memerlukan arahan khusus, dan menyelesaikan tiket.

### ⚡ 3. Real-time Notification & Busy Status Limit
* **Notification Bell & Clickable Alert:** Notifikasi instan di dalam aplikasi bagi pelapor, kasubbag, dan solver ketika terjadi perubahan status atau penugasan tiket baru, yang kini dapat diklik langsung untuk menuju detail tiket.
* **Busy Status Indicator:** Mencegah overload kerja solver dengan membatasi jumlah tiket aktif maksimal **6 tiket**. Status beban kerja diwakili indikator warna dinamis:
  * 🟢 **Low** (0-2 tiket aktif)
  * 🟡 **Med** (3-5 tiket aktif)
  * 🔴 **Hi / Penuh** (6 atau lebih tiket aktif — melarang solver mengambil tiket baru).
* **Multi-Solver Reopen Notifications:** Pengguna atau pihak berwenang dapat membuka kembali tiket (*Reopen*). Saat tiket di-reopen, notifikasi otomatis akan dikirim ke kedua solver (utama dan sekunder) yang sebelumnya ditugaskan.

### 📍 4. Rute Berbasis Lokasi (Location-Based Routing)
Aplikasi secara cerdas mengarahkan tiket berdasarkan lokasi pengguna:
* **Kantor Pusat:** Tiket pengguna dari Kantor Pusat otomatis diarahkan ke Kasubbag Pelayanan TIK Pusat (`k2`).
* **Kantor Perwakilan (Bisa Remote):** Jika kendala dari pegawai Kantor Perwakilan dapat diselesaikan jarak jauh (`bisa_remote = true`), tiket diarahkan ke unit pusat (`k2`) untuk mempercepat resolusi.
* **Kantor Perwakilan (Fisik / Non-Remote):** Jika kendala membutuhkan penanganan fisik di tempat (`bisa_remote = false`), tiket otomatis dialirkan ke PLTI (Pusat Layanan TI Perwakilan - `plti`) setempat.

### 📊 5. Otomasi FAQ (Gemini Auto-FAQ) & Ekspor Dataset AI
* **Sintesis FAQ Otomatis:** Setiap kali 10 tiket selesai dengan Kategori, Sub-layanan, dan Layanan yang sama, AI Gemini otomatis mensintesis deskripsi keluhan dan solusi penyelesaian tersebut menjadi sebuah artikel FAQ baru untuk Knowledge Base.
* **Ekspor Dataset AI (JSON Lines):** Operator dapat mengekspor data tiket terselesaikan ke format `.jsonl` (prompt-completion) untuk kebutuhan training model AI.

### ⏰ 6. Eskalasi Tiket Overdue Otomatis
* **Batas Waktu Respons Kasubbag:** Tiket berstatus `Pending` di antrean Kasubbag selama lebih dari 24 jam akan secara otomatis teridentifikasi.
* **Penandaan & Notifikasi Overdue:** Sistem mengubah status tiket menjadi `Overdue`, membuat log komentar sistem otomatis, serta mengirimkan notifikasi real-time kepada Kasubbag yang bertanggung jawab dan seluruh Operator Biro TI.

---

## 🗺️ Alur Tiket Melati V2 (Ticket Lifecycle)

```mermaid
graph TD
    A[Pengguna: Ajukan Tiket via Chat/Form] --> B{Operator TI}
    B -- Reassign Rute --> B
    B -- Tugaskan Solver Langsung --> D[Solver: Dikerjakan]
    B -- Teruskan Rute --> C{Kasubbag}
    C -- Tolak / Balikkan --> B
    C -- Tugaskan Solver --> D
    D -- Claim Mandiri / Ambil --> D
    D -- Tindaklanjuti / Progress Log --> D
    D -- Eskalasi Ulang --> C
    D -- Laporkan Selesai --> E[Selesai]
    E -- Reopen (Otorisasi Terbatas) --> D
```

---

## 🛠️ Spesifikasi Teknologi Stack

* **Framework Backend:** Laravel 12.0 (PHP 8.2+)
* **Asset Bundler:** Vite 7.0
* **CSS Framework:** Tailwind CSS 4.0
* **Frontend Logic & Reactivity:** Alpine.js 3.x
* **AI Engine:** Google Gemini API (menggunakan model `gemini-3.5-flash` dengan fallback `gemini-2.5-flash` & `gemini-2.5-flash-lite`)
* **Icons:** Lucide Icons
* **Database:** SQLite (Default / Local file database)

---

## 🔑 Akun Uji Coba (Demo Credentials)

Untuk memudahkan peninjauan alur kerja multi-role, gunakan akun seed berikut:

| Peran (Role) | Username | Password | Deskripsi / Subbagian / Lokasi |
|---|---|---|---|
| **Operator TI** | `admin` | `admin123` | Operator Utama Biro TI (Pusat) |
| **Pengguna 1** | `budi` | `budi123` | Pegawai BPK (Pelapor 1 - Kantor Pusat) |
| **Pengguna 2** | `siti` | `siti123` | Pegawai BPK (Pelapor 2 - Kantor Pusat) |
| **Pengguna 3** | `budipusat` | `password` | Pegawai BPK (Kantor Pusat) |
| **Pengguna 4** | `ahmadperwakilan` | `password` | Pegawai BPK (Kantor Perwakilan) |
| **Kasubbag 1** | `kasubbag.infrastruktur` | `pass123` | Kasubbag Jaringan & Infrastruktur (Pusat - `k1`) |
| **Kasubbag 2** | `kasubbag.pelayanan` | `pass123` | Kasubbag Pelayanan TIK (Pusat - `k2`) |
| **Kasubbag 3** | `andiplti` | `password` | Kasubbag PLTI (Kantor Perwakilan - `plti`) |
| **Solver 1** | `solver.infra.1` | `solver123` | Solver Jaringan & Infrastruktur (Pusat) |
| **Solver 2** | `solver.tik.1` | `solver123` | Solver Pelayanan TIK (Pusat) |

---

## ⚙️ Petunjuk Instalasi & Menjalankan Lokal

Ikuti langkah-langkah berikut untuk setup environment lokal Anda:

### 1. Kloning Repository
```bash
git clone https://github.com/IvandraJulio/TEHMELATI.git
cd TEHMELATI
```

### 2. Jalankan Perintah Setup
Gunakan script composer `setup` yang telah dikonfigurasi untuk otomatis menginstal seluruh dependensi PHP & Node.js, men-copy `.env`, men-generate key, membuat file database SQLite, serta melakukan migrasi database:
```bash
composer run setup
```

### 3. Konfigurasi Google Gemini API Key
Untuk mengaktifkan Asisten Virtual AI, buka file `.env` yang baru digenerate dan tambahkan API Key Google Gemini Anda:
```env
GEMINI_API_KEY=your_gemini_api_key_here
```

### 4. Seed Database dengan Data Uji Coba
Jalankan seeder untuk mengisi tabel user demo dan mengimpor **50 tiket riwayat layanan**:
```bash
php artisan db:seed
```

### 5. Jalankan Development Server
Gunakan script terintegrasi untuk menjalankan Laravel Server, Queue listener, dan Vite Dev Server secara bersamaan dalam satu command:

* **Sistem Operasi Windows:**
  ```bash
  composer run dev-win
  ```
* **Sistem Operasi macOS / Linux:**
  ```bash
  composer run dev
  ```

Akses portal melalui browser Anda di alamat: **`http://127.0.0.1:8000`**

### 6. Menjalankan Perintah Eskalasi Overdue (Manual/Cron)
Untuk memproses tiket `Pending` yang melebihi 24 jam secara manual agar berubah menjadi `Overdue`:
```bash
php artisan app:escalate-overdue-tickets
```

---

## 📝 Catatan Tambahan (Developer Note)
* **Queue Listener:** Beberapa notifikasi dan proses latar belakang bergantung pada Queue. Pastikan tab server development tetap aktif agar `php artisan queue:listen` terus memproses antrean job.
* **MIME Verification:** Pengunggahan gambar divalidasi ketat di level frontend dan controller backend untuk mencegah format gif atau file non-gambar masuk ke prompt multimodal AI.
