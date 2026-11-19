📄 CV Dinamis Berbasis Web - Nadila Sandra Dewi

PHP Version
Database
Bootstrap
License: MIT

Aplikasi Curriculum Vitae (CV) digital interaktif dan dinamis berbasis web milik Nadila Sandra Dewi. Sistem ini dilengkapi dengan panel administrasi terproteksi untuk mengelola seluruh konten secara mandiri (Content Management System / CRUD) serta fitur cetak/ekspor dokumen langsung ke format PDF standar cetak A4.

🌟 Fitur Utama
📱 Halaman Publik Responsif (index.php): Desain modern dan adaptif di berbagai resolusi layar (desktop, tablet, dan smartphone).
🖨️ Ekspor PDF Instan: Didukung oleh library html2pdf.js untuk mengunduh salinan CV format A4 dengan tata letak profesional tanpa elemen non-cetak.
🔐 Sistem Autentikasi Aman: Login administrator berbasis sesi PHP (session_start()) dan enkripsi sandi menggunakan algoritma hashing modern (password_hash & password_verify).
🛠️ Dashboard Admin Modular (admin.php): Panel kendali intuitif untuk melakukan operasi CRUD (Create, Read, Update, Delete) pada:
👤 Biodata & Kontak: Profil ringkas, nomor telepon, surel, domisili, dan tautan sosial media.
🎓 Riwayat Pendidikan: Nama institusi, program kejuruan, rentang tahun, dan catatan kompetensi.
💼 Pengalaman & Organisasi: Posisi/peran, nama instansi, kategori (PKL/Organisasi/Project), masa bakti, dan deskripsi tugas.
⭐ Keahlian (Skills): Pengelompokan Technical dan Soft Skill lengkap dengan bilah progres kemahiran.
📂 Portofolio Proyek: Judul karya, deskripsi, teknologi yang digunakan, serta tautan repositori/demo langsung.
🏆 Sertifikat & Lisensi: Nama penghargaan, lembaga penerbit, tanggal terbit, dan tautan verifikasi kredensial.
🛠️ Teknologi yang Digunakan
Komponen Teknologi
Server Lingkungan Laragon
(Apache 2.4, PHP 8.1+)
Bahasa Pemrograman PHP Native (Prepared Statements via PDO)
Basis Data MySQL / MariaDB (Database Engine: InnoDB, Charset: utf8mb4)
Antarmuka (Frontend) HTML5 Semantik, CSS3 Modern, Bootstrap 5.3 CDN, Bootstrap Icons
Generator PDF html2pdf.js
(v0.10.1 via CDN)
📁 Struktur Folder
text
new_cv_db/
│
├── config/
│ └── database.php # Konfigurasi koneksi database MySQL menggunakan PDO
│
├── includes/
│ ├── auth.php # Middleware proteksi sesi administrator
│ ├── header.php # Template header & navigasi panel admin
│ └── footer.php # Template footer panel admin
│
├── assets/
│ ├── css/
│ │ ├── style.css # Styling utama halaman publik & aturan print/PDF
│ │ └── admin.css # Styling khusus antarmuka panel dashboard
│ └── js/
│ ├── cv-export.js # Script trigger konversi DOM ke file PDF
│ └── main.js # Interaktivitas JavaScript pendukung
│
├── sql/
│ ├── schema.sql # Skema DDL tabel database MySQL
│ └── seeder.sql # Data awal profil Nadila Sandra Dewi & kredensial admin
│
├── index.php # Halaman publik Curriculum Vitae
├── login.php # Halaman form login administrator
├── process_login.php # Handler verifikasi autentikasi login
├── logout.php # Handler destroy session dan keluar admin
├── admin.php # Dashboard utama navigasi modul admin
├── admin_biodata.php # Modul kelola data diri & kontak
├── admin_education.php # Modul CRUD riwayat pendidikan
├── admin_experience.php # Modul CRUD riwayat pengalaman/organisasi
├── admin_skills.php # Modul CRUD daftar keahlian
├── admin_projects.php # Modul CRUD portofolio proyek
├── admin_certificates.php # Modul CRUD sertifikat dan lisensi
├── prd.md # Dokumen spesifikasi kebutuhan produk (PRD)
└── README.md # Dokumentasi proyek
🚀 Panduan Instalasi (Localhost via Laragon)

Ikuti langkah-langkah berikut untuk menjalankan proyek di komputer lokal:

1. Klon Repositori

Buka terminal (Git Bash atau PowerShell) dan clone repositori ini ke folder root web server Laragon:

bash
cd C:\laragon\www
git clone https://github.com/sandradewinadila-cell/new_cv_db.git 2. Jalankan Laragon

Pastikan layanan Apache dan MySQL telah berstatus aktif (Start All) di Laragon.

3. Buat dan Impor Database
   Buka browser dan akses phpMyAdmin di http://localhost/phpmyadmin.
   Buat basis data baru dengan nama:
   sql
   new_cv_db
   Pilih database new_cv_db, buka tab Import, lalu impor file secara berurutan:
   sql/schema.sql (untuk membuat seluruh tabel sistem)
   sql/seeder.sql (untuk mengisi data default profil dan akun admin)
4. Akses Aplikasi
   Halaman Publik CV:
   Buka tautan http://localhost/new_cv_db/
   Halaman Login Administrator:
   Buka tautan http://localhost/new_cv_db/login.php

Kredensial Default Akun Admin:

Username: admin
Password: admin123
🔒 Keamanan Sistem
Menggunakan PDO Prepared Statements untuk mencegah celah kerentanan SQL Injection.
Menggunakan fungsi bawaan htmlspecialchars() pada output view untuk menangkal risiko Cross-Site Scripting (XSS).
Pengamanan rute admin menggunakan validasi ketat $\_SESSION['admin_logged_in'].
📄 Lisensi

Proyek ini dirilis di bawah lisensi MIT. Silakan gunakan, pelajari, dan kembangkan lebih lanjut untuk kebutuhan portofolio pribadi maupun akademik.
