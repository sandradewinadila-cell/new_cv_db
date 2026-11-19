-- Seeder Data for CV Dinamis Berbasis Web
USE `new_cv_db`;

-- 1. Seeder: users (Username: admin, Password: admin123)
INSERT INTO `users` (`username`, `password`) VALUES
('admin', '$2y$10$AQirs2IleBEdC8TKOCJ8ZOugCxyOvljAvd03USwuvOKiNCh7dOaKm');

-- 2. Seeder: personal_info (Profil Nadila Sandra Dewi)
INSERT INTO `personal_info` (`full_name`, `email`, `phone`, `address`, `linkedin_url`, `github_url`, `summary`, `avatar`) VALUES
(
    'Nadila Sandra Dewi',
    'sandradewinadila@gmail.com',
    '+62 81249648221',
    'Mojokerto',
    'https://linkedin.com/in/nadila-sandra-639090425/',
    'https://github.com/sandradewinadila-cell',
    'Siswa Rekayasa Perangkat Lunak (SMK Negeri 1 Dlanggu, 2024-2027) yang memiliki minat pada pengembangan web, desain antarmuka, dan teknologi. Senang mengeksplorasi hal baru, memecahkan masalah, dan menghasilkan solusi digital yang fungsional.',
    NULL
);

-- 3. Seeder: education
INSERT INTO `education` (`institution`, `degree_major`, `start_year`, `end_year`, `description`) VALUES
(
    'SMK Negeri 1 Dlanggu',
    'Rekayasa Perangkat Lunak',
    '2024',
    '2027',
    'Fokus keahlian pada pemrograman web, pemodelan basis data relasional, dan pengembangan antarmuka aplikasi.'
);

-- 4. Seeder: skills
INSERT INTO `skills` (`skill_name`, `category`, `proficiency_level`) VALUES
('HTML', 'Technical', 90),
('CSS', 'Technical', 85),
('PHP', 'Technical', 80),
('MySQL', 'Technical', 80),
('JavaScript', 'Technical', 75),
('Teamwork', 'Soft Skill', 90),
('Public Speaking', 'Soft Skill', 85);

-- 5. Seeder: experience (Dummy Example Placeholder)
INSERT INTO `experience` (`title`, `organization`, `type`, `start_date`, `end_date`, `description`) VALUES
(
    'Anggota Divisi IT & Dokumentasi',
    'OSIS SMK Negeri 1 Dlanggu',
    'Organisasi',
    'Agu 2024',
    'Sekarang',
    'Membantu pengelolaan materi visual digital sekolah, dokumentasi kegiatan, serta publikasi informasi daring.'
);

-- 6. Seeder: projects (Dummy Example)
INSERT INTO `projects` (`project_name`, `description`, `tech_stack`, `project_url`, `repository_url`, `image`) VALUES
(
    'CV Dinamis Berbasis Web',
    'Aplikasi portofolio dan resume dinamis yang dilengkapi panel admin CRUD serta fitur cetak/unduh PDF otomatis.',
    'PHP Native, MySQL, HTML5, CSS3, JavaScript, html2pdf.js',
    NULL,
    'https://github.com/sandradewinadila-cell/new_cv_db',
    NULL
);

-- 7. Seeder: certificates (Dummy Example)
INSERT INTO `certificates` (`certificate_name`, `issuer`, `issued_date`, `credential_url`, `certificate_file`) VALUES
(
    'Pelatihan Dasar Pengembangan Web',
    'Dicoding / Skilvul',
    '2024-10-15',
    NULL,
    NULL
);
