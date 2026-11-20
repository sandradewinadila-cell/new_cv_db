<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$current_page = basename($_SERVER['PHP_SELF']);

// Penentuan judul halaman dinamis
$page_titles = [
    'admin.php'              => 'Dashboard Overview',
    'admin_biodata.php'      => 'Kelola Biodata & Kontak',
    'admin_education.php'    => 'Kelola Riwayat Pendidikan',
    'admin_experience.php'   => 'Kelola Pengalaman & Organisasi',
    'admin_skills.php'       => 'Kelola Keahlian (Skills)',
    'admin_projects.php'     => 'Kelola Portofolio Proyek',
    'admin_certificates.php' => 'Kelola Sertifikat & Lisensi'
];

$current_title = $page_titles[$current_page] ?? 'Dashboard Admin';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($current_title); ?> - CV Admin</title>
    <!-- Theme CSS (LIVA UX Specialist) -->
    <link rel="stylesheet" href="assets/css/theme.css">
    <!-- FontAwesome 6 CDN -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Bootstrap 5 CSS CDN (Grid & Utilities) -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .topbar-admin {
            margin-left: 260px;
            padding: 1rem 2rem;
            min-height: 70px;
        }
        @media (max-width: 768px) {
            .topbar-admin {
                margin-left: 0;
            }
        }
    </style>
</head>
<body>

<!-- SIDEBAR ADMIN (Kiri) -->
<aside class="admin-sidebar">
    <div class="mb-4 px-2">
        <a href="admin.php" class="nav-logo d-flex align-items-center gap-2">
            <i class="fa-solid fa-code text-gradient" style="color: var(--accent-primary);"></i>
            <span>CV</span> Admin
        </a>
        <div class="small text-muted mt-1 px-1" style="font-size: 0.75rem;">Nadila Sandra Dewi</div>
    </div>

    <nav class="d-flex flex-column gap-1">
        <a href="admin.php" class="sidebar-item <?php echo $current_page === 'admin.php' ? 'active' : ''; ?>">
            <i class="fa-solid fa-gauge-high"></i>
            <span>Dashboard</span>
        </a>
        <a href="admin_biodata.php" class="sidebar-item <?php echo $current_page === 'admin_biodata.php' ? 'active' : ''; ?>">
            <i class="fa-solid fa-user"></i>
            <span>Biodata & Kontak</span>
        </a>
        <a href="admin_education.php" class="sidebar-item <?php echo $current_page === 'admin_education.php' ? 'active' : ''; ?>">
            <i class="fa-solid fa-graduation-cap"></i>
            <span>Pendidikan</span>
        </a>
        <a href="admin_experience.php" class="sidebar-item <?php echo $current_page === 'admin_experience.php' ? 'active' : ''; ?>">
            <i class="fa-solid fa-briefcase"></i>
            <span>Pengalaman</span>
        </a>
        <a href="admin_skills.php" class="sidebar-item <?php echo $current_page === 'admin_skills.php' ? 'active' : ''; ?>">
            <i class="fa-solid fa-star"></i>
            <span>Keahlian</span>
        </a>
        <a href="admin_projects.php" class="sidebar-item <?php echo $current_page === 'admin_projects.php' ? 'active' : ''; ?>">
            <i class="fa-solid fa-folder-open"></i>
            <span>Proyek</span>
        </a>
        <a href="admin_certificates.php" class="sidebar-item <?php echo $current_page === 'admin_certificates.php' ? 'active' : ''; ?>">
            <i class="fa-solid fa-award"></i>
            <span>Sertifikat</span>
        </a>
    </nav>

    <div class="mt-auto pt-4 px-2">
        <a href="index.php" target="_blank" class="sidebar-item" style="color: var(--accent-secondary);">
            <i class="fa-solid fa-arrow-up-right-from-square"></i>
            <span>Lihat Web CV</span>
        </a>
    </div>
</aside>

<!-- TOPBAR ADMIN (Atas) -->
<header class="modern-navbar topbar-admin">
    <div class="d-flex align-items-center">
        <h3 class="h4 mb-0 fw-bold"><?php echo htmlspecialchars($current_title); ?></h3>
    </div>
    <div class="d-flex align-items-center gap-3">
        <a href="index.php" target="_blank" class="btn-outline py-2 px-3 d-none d-md-inline-flex" style="font-size: 0.85rem;">
            <i class="fa-solid fa-eye me-2"></i> Pratinjau
        </a>
        <a href="logout.php" id="btn-logout" class="btn-outline py-2 px-3" style="font-size: 0.85rem; border-color: rgba(239,68,68,0.5); color: #fca5a5;">
            <i class="fa-solid fa-right-from-bracket me-2"></i> Logout
        </a>
    </div>
</header>

<!-- KONTEN ADMIN UTAMA (Buka tag) -->
<main class="admin-content">