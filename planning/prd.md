# Product Requirement Document (PRD)

## Proyek: CV Dinamis Berbasis Web

**Document Version:** 1.0.0  
**Target Profile:** Nadila Sandra Dewi  
**Author:** Senior Product Manager & System Architect  
**Status:** Approved / Ready for Implementation

---

### 1. Ringkasan Proyek

#### 1.1 Tujuan

Membangun platform Curriculum Vitae (CV) digital interaktif dan dinamis berbasis web yang profesional, responsif, dan mudah diperbarui secara mandiri oleh pemilik CV melalui panel admin tanpa perlu menyunting kode sumber.

#### 1.2 Lingkup Sistem

- **Sisi Publik:** Menampilkan profil profesional, riwayat pendidikan, pengalaman, keahlian teknis/non-teknis, portofolio proyek, dan sertifikasi milik Nadila Sandra Dewi, dilengkapi fitur ekspor/cetak dokumen ke format PDF secara instan.
- **Sisi Administratif:** Panel manajemen konten (CRUD) dengan autentikasi berbasis sesi untuk mengelola seluruh entitas data CV secara aman dan terstruktur.

---

### 2. Stack Teknologi

| Komponen               | Spesifikasi / Library                     | Keterangan                                                  |
| :--------------------- | :---------------------------------------- | :---------------------------------------------------------- |
| **Server Environment** | Laragon (Apache 2.4+, PHP 8.x)            | Lingkungan pengembangan lokal terintegrasi                  |
| **Backend Language**   | PHP Native (Procedural/Modular OOP)       | Logika pemrosesan data, autentikasi, dan routing sederhana  |
| **Database Engine**    | MySQL 8.0+ / MariaDB                      | Penyimpanan basis data relasional                           |
| **Frontend Core**      | HTML5, Modern CSS3, JavaScript (ES6)      | Struktur semantik, tata letak responsif, dan manipulasi DOM |
| **Styling Framework**  | Bootstrap 5 / Tailwind CSS (Opsional CDN) | UI konsisten untuk antarmuka publik dan dashboard admin     |
| **PDF Generation**     | `html2pdf.js` (Client-side)               | Konversi dokumen DOM CV langsung ke file PDF siap unduh     |

---

### 3. Skema Database

Sistem menggunakan database relasional bernama `new_cv_db`. Setiap tabel wajib memiliki Primary Key `id` bertipe `INT AUTO_INCREMENT`, serta audit timestamp `created_at` dan `updated_at`.

#### 3.1 Tabel `users`

Menyimpan kredensial administrator.

```sql
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);
```

#### 3.2 Tabel `personal_info`

Menyimpan data identitas diri utama (Single Row record).

```sql
CREATE TABLE personal_info (
    id INT AUTO_INCREMENT PRIMARY KEY,
    full_name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,
    phone VARCHAR(20) NOT NULL,
    address VARCHAR(150) NOT NULL,
    linkedin_url VARCHAR(255) NULL,
    github_url VARCHAR(255) NULL,
    summary TEXT NOT NULL,
    avatar VARCHAR(255) NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);
```

_Data Awal (Seeder):_

- `full_name`: "Nadila Sandra Dewi"
- `email`: "sandradewinadila@gmail.com"
- `phone`: "+62 81249648221"
- `address`: "Mojokerto"
- `linkedin_url`: "https://linkedin.com/in/nadila-sandra-639090425/"
- `github_url`: "https://github.com/sandradewinadila-cell"
- `summary`: "Siswa Rekayasa Perangkat Lunak (SMK Negeri 1 Dlanggu, 2024-2027) yang memiliki minat pada pengembangan web, desain antarmuka, dan teknologi. Senang mengeksplorasi hal baru, memecahkan masalah, dan menghasilkan solusi digital yang fungsional."

#### 3.3 Tabel `education`

Menyimpan riwayat jenjang pendidikan.

```sql
CREATE TABLE education (
    id INT AUTO_INCREMENT PRIMARY KEY,
    institution VARCHAR(150) NOT NULL,
    degree_major VARCHAR(100) NOT NULL,
    start_year VARCHAR(10) NOT NULL,
    end_year VARCHAR(10) NOT NULL,
    description TEXT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);
```

_Data Awal (Seeder):_

- `institution`: "SMK Negeri 1 Dlanggu"
- `degree_major`: "Rekayasa Perangkat Lunak"
- `start_year`: "2024"
- `end_year`: "2027"

#### 3.4 Tabel `experience`

Menyimpan pengalaman kerja, PKL/magang, atau organisasi.

```sql
CREATE TABLE experience (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(100) NOT NULL,
    organization VARCHAR(150) NOT NULL,
    type ENUM('PKL/Magang', 'Organisasi', 'Project Work') DEFAULT 'Organisasi',
    start_date VARCHAR(20) NOT NULL,
    end_date VARCHAR(20) NOT NULL,
    description TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);
```

#### 3.5 Tabel `skills`

Menyimpan keahlian teknis dan interpersonal.

```sql
CREATE TABLE skills (
    id INT AUTO_INCREMENT PRIMARY KEY,
    skill_name VARCHAR(50) NOT NULL,
    category ENUM('Technical', 'Soft Skill') NOT NULL DEFAULT 'Technical',
    proficiency_level INT DEFAULT 80, -- Rentang 1-100%
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);
```

_Data Awal (Seeder):_

- Technical: HTML, CSS, PHP, MySQL, JavaScript
- Soft Skill: Teamwork, Public Speaking

#### 3.6 Tabel `projects`

Menyimpan portofolio aplikasi dan karya digital.

```sql
CREATE TABLE projects (
    id INT AUTO_INCREMENT PRIMARY KEY,
    project_name VARCHAR(150) NOT NULL,
    description TEXT NOT NULL,
    tech_stack VARCHAR(255) NOT NULL,
    project_url VARCHAR(255) NULL,
    repository_url VARCHAR(255) NULL,
    image VARCHAR(255) NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);
```

#### 3.7 Tabel `certificates`

Menyimpan rekaman lisensi dan sertifikat kompetensi/kursus.

```sql
CREATE TABLE certificates (
    id INT AUTO_INCREMENT PRIMARY KEY,
    certificate_name VARCHAR(150) NOT NULL,
    issuer VARCHAR(100) NOT NULL,
    issued_date DATE NULL,
    credential_url VARCHAR(255) NULL,
    certificate_file VARCHAR(255) NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);
```

---

### 4. Fitur Utama & User Flow

```
[ Pengunjung ] ---> ( index.php ) ---> [ Tampilan CV Responsif ] ---> [ Klik 'Unduh PDF' (html2pdf.js) ]

[ Admin ] --------> ( login.php ) ---> [ Validasi Auth/Session ]
                                                |
                                                v
                                      ( admin.php / Dashboard )
                                                |
                                    +-----------+-----------+
                                    |                       |
                             [ Modul Profil/Skill ]   [ Modul Proyek/Sertifikat ]
                             [ Create/Read/Update ]   [ Create/Read/Update/Delete ]
```

#### 4.1 Halaman Publik (`index.php`)

- **Tampilan Informasi Lengkap:** Hero Section profil, data kontak, tautan sosial (LinkedIn & GitHub), ringkasan profesional, badge keahlian, timeline pendidikan, pengalaman, kartu proyek, dan daftar sertifikat.
- **Responsivitas:** Layout mobile-friendly yang rapi pada desktop, tablet, maupun smartphone.
- **Fitur Ekspor PDF:** Tombol "Unduh PDF" yang memanfaatkan `html2pdf.js`. Tombol memicu rendering kontainer `#cv-document` dengan format kertas A4, margin standar, dan memfilter elemen non-cetak (tombol download, navigasi admin).

#### 4.2 Halaman Autentikasi (`login.php`)

- **Validasi Kredensial:** Form username dan password. Password diverifikasi menggunakan `password_verify()`.
- **Manajemen Sesi:** Jika valid, inisialisasi sesi `$_SESSION['admin_logged_in'] = true`. Jika gagal, tampilkan pesan peringatan.
- **Proteksi Rute:** Pengalihan otomatis (_redirect_) pengguna yang belum login jika mencoba mengakses halaman admin. Mekanisme `logout.php` untuk menghancurkan sesi secara aman.

#### 4.3 Panel Dashboard Admin (`admin.php`)

- **Tabbed / Modular CRUD:** Antarmuka terpusat untuk mengelola 6 modul data:
  1. _Biodata & Kontak_ (Update)
  2. _Riwayat Pendidikan_ (Create, Read, Update, Delete)
  3. _Pengalaman Kerja/Organisasi_ (Create, Read, Update, Delete)
  4. _Keahlian_ (Create, Read, Update, Delete)
  5. _Proyek_ (Create, Read, Update, Delete + Upload Cover Gambar)
  6. _Sertifikat_ (Create, Read, Update, Delete + Link Kredensial)
- **Feedback Operasi:** Flash message (Notifikasi sukses/gagal operasi database).

---

### 5. Struktur Folder

```
new_cv_db/
│
├── config/
│   └── database.php           # Konfigurasi koneksi MySQL (PDO/MySQLi)
│
├── includes/
│   ├── auth.php               # Middleware pengecekan session admin
│   ├── header.php             # Komponen reusable header HTML & navbar
│   └── footer.php             # Komponen reusable footer & script bundle
│
├── assets/
│   ├── css/
│   │   ├── style.css          # Kustom styling halaman publik & print rules
│   │   └── admin.css          # Styling dashboard panel admin
│   ├── js/
│   │   ├── html2pdf.bundle.js # Library export PDF client-side
│   │   ├── cv-export.js       # Konfigurasi & trigger html2pdf
│   │   └── main.js            # Interaktivitas DOM & modal trigger
│   └── uploads/
│       ├── avatars/           # Direktori penyimpanan foto profil
│       ├── projects/          # Screenshot/thumbnail proyek
│       └── certificates/      # File berkas/gambar sertifikat
│
├── sql/
│   ├── schema.sql             # DDL pembuatan tabel-tabel database
│   └── seeder.sql             # DML data awal profil Nadila Sandra Dewi
│
├── index.php                  # Halaman utama (Public CV View)
├── login.php                  # Halaman autentikasi administrator
├── admin.php                  # Dashboard utama CRUD data
├── actions.php                # Handler proses POST (Create, Update, Delete)
├── logout.php                 # Script destroy session & redirect
├── prd.md                     # Dokumen spesifikasi kebutuhan produk
└── README.md                  # Panduan instalasi di Laragon
```

---

### 6. Roadmap Pengembangan

```
+-------------------------------------------------------------------------------+
|  FASE 1: MVP Core & Client-side PDF                                           |
|  - Setup lingkungan lokal Laragon, skema MySQL, dan struktur direktori.       |
|  - Implementasi CRUD lengkap pada admin.php untuk seluruh entitas profil.     |
|  - Pembuatan antarmuka publik responsif (index.php) berbasis data dinamis.    |
|  - Integrasi library html2pdf.js untuk fitur unduh PDF format A4.             |
+-------------------------------------------------------------------------------+
                                        |
                                        v
+-------------------------------------------------------------------------------+
|  FASE 2: Data Portability & Advanced Tooling                                  |
|  - Fitur "Export to JSON": Pengambilan seluruh isi database ke 1 file .json.   |
|  - Fitur "Import from JSON": Migrasi/restorasi data instan via form upload.   |
|  - Validasi schema JSON untuk mencegah anomali format saat proses import.     |
|  - Peningkatan sistem keamanan admin (CSRF Token & Sanitasi Input Ketat).     |
+-------------------------------------------------------------------------------+
```
