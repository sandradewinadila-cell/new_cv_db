<?php
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/header.php';
?>

<div class="row mb-4">
    <div class="col-12">
        <h2 class="fw-bold text-dark">Selamat Datang, Admin!</h2>
        <p class="text-muted">Kelola seluruh informasi dan data CV dinamis melalui menu navigasi modul di bawah ini.</p>
    </div>
</div>

<div class="row g-4">
    <!-- Card 1: Biodata & Kontak -->
    <div class="col-12 col-sm-6 col-md-3">
        <a href="admin_biodata.php" class="text-decoration-none">
            <div class="card h-100 shadow-sm border-0 border-top border-4 border-primary hover-shadow transition" style="cursor: pointer;">
                <div class="card-body text-center p-4">
                    <div class="mb-3 text-primary">
                        <i class="bi bi-person-circle" style="font-size: 2.5rem;"></i>
                    </div>
                    <h5 class="card-title fw-bold text-dark mb-1">Biodata & Kontak</h5>
                    <p class="card-text text-muted small">Kelola data diri, kontak, dan tautan sosial.</p>
                </div>
            </div>
        </a>
    </div>

    <!-- Card 2: Pendidikan & Pengalaman -->
    <div class="col-12 col-sm-6 col-md-3">
        <a href="admin_education.php" class="text-decoration-none">
            <div class="card h-100 shadow-sm border-0 border-top border-4 border-success hover-shadow transition" style="cursor: pointer;">
                <div class="card-body text-center p-4">
                    <div class="mb-3 text-success">
                        <i class="bi bi-briefcase-fill" style="font-size: 2.5rem;"></i>
                    </div>
                    <h5 class="card-title fw-bold text-dark mb-1">Pendidikan & Pengalaman</h5>
                    <p class="card-text text-muted small">Kelola riwayat sekolah, PKL, dan organisasi.</p>
                </div>
            </div>
        </a>
    </div>

    <!-- Card 3: Keahlian (Skills) -->
    <div class="col-12 col-sm-6 col-md-3">
        <a href="admin_skills.php" class="text-decoration-none">
            <div class="card h-100 shadow-sm border-0 border-top border-4 border-warning hover-shadow transition" style="cursor: pointer;">
                <div class="card-body text-center p-4">
                    <div class="mb-3 text-warning">
                        <i class="bi bi-star-fill" style="font-size: 2.5rem;"></i>
                    </div>
                    <h5 class="card-title fw-bold text-dark mb-1">Keahlian (Skills)</h5>
                    <p class="card-text text-muted small">Kelola hard skill, soft skill, dan tingkat kemahiran.</p>
                </div>
            </div>
        </a>
    </div>

    <!-- Card 4: Proyek -->
    <div class="col-12 col-sm-6 col-md-3">
        <a href="admin_projects.php" class="text-decoration-none">
            <div class="card h-100 shadow-sm border-0 border-top border-4 border-info hover-shadow transition" style="cursor: pointer;">
                <div class="card-body text-center p-4">
                    <div class="mb-3 text-info">
                        <i class="bi bi-folder-fill" style="font-size: 2.5rem;"></i>
                    </div>
                    <h5 class="card-title fw-bold text-dark mb-1">Proyek</h5>
                    <p class="card-text text-muted small">Kelola portofolio karya.</p>
                </div>
            </div>
        </a>
    </div>

        <!-- Card 5: Sertifikasi -->
    <div class="col-12 col-sm-6 col-md-3">
        <a href="admin_certificates.php" class="text-decoration-none">
            <div class="card h-100 shadow-sm border-0 border-top border-4 border-info hover-shadow transition" style="cursor: pointer;">
                <div class="card-body text-center p-4">
                    <div class="mb-3 text-info">
                        <i class="bi bi-folder-fill" style="font-size: 2.5rem;"></i>
                    </div>
                    <h5 class="card-title fw-bold text-dark mb-1">Sertifikasi</h5>
                    <p class="card-text text-muted small">Kelola sertifikasi berkas.</p>
                </div>
            </div>
        </a>
    </div>
</div>

<?php
require_once __DIR__ . '/includes/footer.php';
?>