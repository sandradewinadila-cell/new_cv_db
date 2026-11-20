<?php
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/header.php';
?>

<div class="mb-5 fade-in-up">
    <span class="section-label">Panel Kontrol</span>
    <h2 class="gradient-text mb-2">Dashboard Admin</h2>
    <p class="text-secondary mb-0">Kelola seluruh konten CV Anda di sini.</p>
</div>

<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 1.5rem;" class="fade-in-up">
    <!-- Card 1: Biodata & Kontak -->
    <a href="admin_biodata.php" style="text-decoration: none; color: inherit;">
        <div class="glass-card h-100 d-flex flex-column justify-content-between">
            <div>
                <div style="font-size: 2.75rem; color: var(--accent-primary); margin-bottom: 1.25rem;">
                    <i class="fa-solid fa-user"></i>
                </div>
                <h3 class="h4 mb-2 fw-bold">Biodata & Kontak</h3>
                <p class="small text-secondary mb-0" style="line-height: 1.6;">
                    Sunting informasi profil pribadi, kontak surel, telepon, dan tautan sosial media.
                </p>
            </div>
            <div class="mt-4 pt-3 d-flex align-items-center gap-2" style="color: var(--accent-primary); font-size: 0.85rem; font-weight: 600; border-top: 1px solid var(--border-glass);">
                <span>Kelola Profil</span>
                <i class="fa-solid fa-arrow-right"></i>
            </div>
        </div>
    </a>

    <!-- Card 2: Pendidikan & Pengalaman -->
    <a href="admin_education.php" style="text-decoration: none; color: inherit;">
        <div class="glass-card h-100 d-flex flex-column justify-content-between">
            <div>
                <div style="font-size: 2.75rem; color: var(--accent-secondary); margin-bottom: 1.25rem;">
                    <i class="fa-solid fa-graduation-cap"></i>
                </div>
                <h3 class="h4 mb-2 fw-bold">Pendidikan & Pengalaman</h3>
                <p class="small text-secondary mb-0" style="line-height: 1.6;">
                    Kelola rekam jejak sekolah, kegiatan organisasi, dan pengalaman kerja atau magang.
                </p>
            </div>
            <div class="mt-4 pt-3 d-flex align-items-center gap-2" style="color: var(--accent-secondary); font-size: 0.85rem; font-weight: 600; border-top: 1px solid var(--border-glass);">
                <span>Kelola Riwayat</span>
                <i class="fa-solid fa-arrow-right"></i>
            </div>
        </div>
    </a>

    <!-- Card 3: Keahlian (Skills) -->
    <a href="admin_skills.php" style="text-decoration: none; color: inherit;">
        <div class="glass-card h-100 d-flex flex-column justify-content-between">
            <div>
                <div style="font-size: 2.75rem; color: var(--accent-primary); margin-bottom: 1.25rem;">
                    <i class="fa-solid fa-star"></i>
                </div>
                <h3 class="h4 mb-2 fw-bold">Keahlian (Skills)</h3>
                <p class="small text-secondary mb-0" style="line-height: 1.6;">
                    Atur daftar technical skill, soft skill, dan tingkat persentase kemahiran Anda.
                </p>
            </div>
            <div class="mt-4 pt-3 d-flex align-items-center gap-2" style="color: var(--accent-primary); font-size: 0.85rem; font-weight: 600; border-top: 1px solid var(--border-glass);">
                <span>Kelola Skill</span>
                <i class="fa-solid fa-arrow-right"></i>
            </div>
        </div>
    </a>

    <!-- Card 4: Proyek & Sertifikat -->
    <a href="admin_projects.php" style="text-decoration: none; color: inherit;">
        <div class="glass-card h-100 d-flex flex-column justify-content-between">
            <div>
                <div style="font-size: 2.75rem; color: var(--accent-secondary); margin-bottom: 1.25rem;">
                    <i class="fa-solid fa-folder-open"></i>
                </div>
                <h3 class="h4 mb-2 fw-bold">Proyek & Sertifikat</h3>
                <p class="small text-secondary mb-0" style="line-height: 1.6;">
                    Publikasikan portofolio karya digital serta sertifikat lisensi kompetensi yang diraih.
                </p>
            </div>
            <div class="mt-4 pt-3 d-flex align-items-center gap-2" style="color: var(--accent-secondary); font-size: 0.85rem; font-weight: 600; border-top: 1px solid var(--border-glass);">
                <span>Kelola Portofolio</span>
                <i class="fa-solid fa-arrow-right"></i>
            </div>
        </div>
    </a>
</div>

<?php
require_once __DIR__ . '/includes/footer.php';
?>