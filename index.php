<?php
require_once __DIR__ . '/config/database.php';

// 1. Ambil data personal info (1 baris)
$stmt_profile = $pdo->query("SELECT * FROM personal_info WHERE id = 1 LIMIT 1");
$profile = $stmt_profile->fetch() ?: [];

// 2. Ambil data pendidikan
$stmt_edu = $pdo->query("SELECT * FROM education ORDER BY start_year DESC, id DESC");
$educations = $stmt_edu->fetchAll();

// 3. Ambil data pengalaman
$stmt_exp = $pdo->query("SELECT * FROM experience ORDER BY id DESC");
$experiences = $stmt_exp->fetchAll();

// 4. Ambil data keahlian
$stmt_skills = $pdo->query("SELECT * FROM skills ORDER BY category ASC, proficiency_level DESC, id DESC");
$skills = $stmt_skills->fetchAll();

// 5. Ambil data proyek
$stmt_projects = $pdo->query("SELECT * FROM projects ORDER BY id DESC");
$projects = $stmt_projects->fetchAll();

// 6. Ambil data sertifikat
$stmt_certs = $pdo->query("SELECT * FROM certificates ORDER BY issued_date DESC, id DESC");
$certificates = $stmt_certs->fetchAll();

// Helper URL gambar agar mendukung nama file langsung atau path relatif
function resolveImgUrl($path, $folder) {
    if (empty($path)) return '';
    if (strpos($path, '/') !== false) return $path;
    return 'assets/uploads/' . trim($folder, '/') . '/' . $path;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($profile['full_name'] ?? 'Nadila Sandra Dewi'); ?> - Personal Portfolio & CV</title>
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@400;500;600;700&family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">
    
    <!-- Theme CSS (LIVA UX Specialist) -->
    <link rel="stylesheet" href="assets/css/theme.css">
    
    <!-- FontAwesome 6 CDN -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Bootstrap 5 CSS CDN (Grid System & Utilities) -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- html2pdf.js CDN -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
    
    <style>
        .avatar-container {
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto;
        }
        .avatar-initials {
            width: 280px;
            height: 280px;
            border-radius: 50%;
            background: linear-gradient(135deg, rgba(192, 132, 252, 0.2), rgba(244, 114, 182, 0.2));
            border: 4px solid var(--accent-primary);
            box-shadow: var(--shadow-glow);
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: var(--font-main);
            font-size: 5rem;
            font-weight: 700;
            color: var(--accent-primary);
        }
        .contact-item {
            display: flex;
            align-items: center;
            gap: 1rem;
            color: var(--text-primary);
            text-decoration: none;
            transition: var(--transition);
        }
        .contact-item:hover {
            color: var(--accent-primary);
            transform: translateX(4px);
        }
        .contact-icon {
            width: 44px;
            height: 44px;
            border-radius: var(--radius-sm);
            background: rgba(192, 132, 252, 0.12);
            color: var(--accent-primary);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.25rem;
            border: 1px solid var(--border-glass);
            flex-shrink: 0;
        }
        .project-card {
            padding: 0 !important;
            overflow: hidden;
        }
        .project-card-body {
            padding: 1.75rem 2rem 2rem 2rem;
        }
        .project-links a {
            color: var(--text-secondary);
            text-decoration: none;
            transition: var(--transition);
            font-size: 1.1rem;
        }
        .project-links a:hover {
            color: var(--accent-primary);
            transform: translateY(-2px);
        }
        .section-gap {
            padding-top: 5rem;
            padding-bottom: 2rem;
        }
    </style>
</head>
<body>

<!-- NAVBAR (No Print) -->
<nav class="modern-navbar no-print">
    <div class="container d-flex justify-content-between align-items-center">
        <a href="#about" class="nav-logo">
            Nadila.<span>Sandra Dewi</span>
        </a>
        <div class="nav-links d-none d-md-flex align-items-center">
            <a href="#about">Tentang</a>
            <a href="#experience">Pengalaman</a>
            <a href="#skills">Keahlian</a>
            <a href="#projects">Proyek</a>
            <a href="#certificates">Sertifikat</a>
            <a href="#contact">Kontak</a>
        </div>
        <div class="d-flex align-items-center gap-2">
            <button id="btn-download-pdf" class="btn-primary" style="padding: 0.6rem 1.25rem; font-size: 0.85rem;">
                <i class="fa-solid fa-file-arrow-down me-2"></i> Unduh CV (PDF)
            </button>
        </div>
    </div>
</nav>

<!-- WRAPPER UTAMA KONTEN CV -->
<div id="cv-content">
    <div class="container py-4">

        <!-- HERO SECTION (#about) -->
        <section id="about" class="section-gap pt-4 pt-md-5">
            <div class="row align-items-center g-5">
                <div class="col-lg-7 order-2 order-lg-1">
                    <span class="section-label">Rekayasa Perangkat Lunak</span>
                    <h1 class="mb-3">
                        Halo, saya <span class="gradient-text"><?php echo htmlspecialchars($profile['full_name'] ?? 'Nadila Sandra Dewi'); ?></span>.
                    </h1>
                    <p class="lead mb-4 text-secondary" style="font-size: 1.15rem; line-height: 1.8;">
                        <?php echo nl2br(htmlspecialchars($profile['summary'] ?? 'Siswa Rekayasa Perangkat Lunak yang antusias dalam pengembangan web, perancangan antarmuka, dan arsitektur data.')); ?>
                    </p>
                    <div class="d-flex flex-wrap gap-3 no-print">
                        <button onclick="document.getElementById('btn-download-pdf').click();" class="btn-primary">
                            <i class="fa-solid fa-file-pdf me-2"></i> Unduh CV (PDF)
                        </button>
                        <a href="#contact" class="btn-outline">
                            <i class="fa-solid fa-paper-plane me-2"></i> Hubungi Saya
                        </a>
                    </div>
                </div>

                <div class="col-lg-5 order-1 order-lg-2 text-center">
                    <div class="avatar-container">
                        <?php 
                        $avatar_url = resolveImgUrl($profile['avatar'] ?? '', 'avatars');
                        if (!empty($profile['avatar'])): 
                        ?>
                            <img src="<?php echo htmlspecialchars($avatar_url); ?>" 
                                 alt="Foto Profil" 
                                 style="width: 280px; height: 280px; object-fit: cover; border-radius: 50%; border: 4px solid transparent; background: linear-gradient(#0a0118, #0a0118) padding-box, var(--gradient-main) border-box; box-shadow: var(--shadow-glow);" 
                                 onerror="this.style.display='none'">
                        <?php else: ?>
                            <div class="avatar-initials">
                                <?php 
                                $initials = 'NS';
                                if (!empty($profile['full_name'])) {
                                    $words = explode(' ', trim($profile['full_name']));
                                    $initials = strtoupper(substr($words[0], 0, 1) . (isset($words[1]) ? substr($words[1], 0, 1) : ''));
                                }
                                echo htmlspecialchars($initials);
                                ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </section>

        <!-- SECTION KONTAK (#contact, No Print) -->
        <section id="contact" class="my-5 no-print">
            <div class="glass-card">
                <div class="row g-4">
                    <div class="col-md-3 col-sm-6">
                        <a href="mailto:<?php echo htmlspecialchars($profile['email'] ?? ''); ?>" class="contact-item">
                            <div class="contact-icon"><i class="fa-solid fa-envelope"></i></div>
                            <div>
                                <small class="text-muted d-block">Email</small>
                                <span class="small fw-semibold"><?php echo htmlspecialchars($profile['email'] ?? 'Belum diisi'); ?></span>
                            </div>
                        </a>
                    </div>
                    <div class="col-md-3 col-sm-6">
                        <div class="contact-item">
                            <div class="contact-icon"><i class="fa-solid fa-phone"></i></div>
                            <div>
                                <small class="text-muted d-block">Telepon / WhatsApp</small>
                                <span class="small fw-semibold"><?php echo htmlspecialchars($profile['phone'] ?? 'Belum diisi'); ?></span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 col-sm-6">
                        <?php if (!empty($profile['linkedin_url'])): ?>
                            <a href="<?php echo htmlspecialchars($profile['linkedin_url']); ?>" target="_blank" class="contact-item">
                                <div class="contact-icon"><i class="fa-brands fa-linkedin-in"></i></div>
                                <div>
                                    <small class="text-muted d-block">LinkedIn</small>
                                    <span class="small fw-semibold">Connect Profile</span>
                                </div>
                            </a>
                        <?php else: ?>
                            <div class="contact-item">
                                <div class="contact-icon"><i class="fa-solid fa-location-dot"></i></div>
                                <div>
                                    <small class="text-muted d-block">Domisili</small>
                                    <span class="small fw-semibold"><?php echo htmlspecialchars($profile['address'] ?? 'Mojokerto'); ?></span>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                    <div class="col-md-3 col-sm-6">
                        <?php if (!empty($profile['github_url'])): ?>
                            <a href="<?php echo htmlspecialchars($profile['github_url']); ?>" target="_blank" class="contact-item">
                                <div class="contact-icon"><i class="fa-brands fa-github"></i></div>
                                <div>
                                    <small class="text-muted d-block">GitHub</small>
                                    <span class="small fw-semibold">Lihat Repositori</span>
                                </div>
                            </a>
                        <?php else: ?>
                            <div class="contact-item">
                                <div class="contact-icon"><i class="fa-solid fa-globe"></i></div>
                                <div>
                                    <small class="text-muted d-block">Status</small>
                                    <span class="small fw-semibold">Open to Work</span>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </section>

        <!-- SECTION PENDIDIKAN & PENGALAMAN (#experience) -->
        <section id="experience" class="section-gap">
            <div class="text-center mb-5">
                <span class="section-label">Riwayat & Rekam Jejak</span>
                <h2 class="gradient-text">Perjalanan Saya</h2>
            </div>

            <div class="row g-4">
                <!-- Kolom Pengalaman -->
                <div class="col-lg-6">
                    <h3 class="h4 mb-4 d-flex align-items-center gap-2">
                        <i class="fa-solid fa-briefcase text-gradient" style="color: var(--accent-primary);"></i>
                        Pengalaman & Organisasi
                    </h3>
                    <?php if (empty($experiences)): ?>
                        <div class="glass-card text-center text-muted py-4">Belum ada data pengalaman.</div>
                    <?php else: ?>
                        <div class="d-flex flex-column gap-3">
                            <?php foreach ($experiences as $exp): ?>
                                <div class="glass-card">
                                    <div class="d-flex justify-content-between align-items-start mb-2 flex-wrap gap-2">
                                        <h4 class="h5 mb-0 fw-bold"><?php echo htmlspecialchars($exp['title']); ?></h4>
                                        <span class="badge-glass"><?php echo htmlspecialchars($exp['start_date'] . ' - ' . $exp['end_date']); ?></span>
                                    </div>
                                    <div class="d-flex align-items-center gap-2 mb-3">
                                        <span style="color: var(--accent-secondary); font-weight: 500; font-size: 0.95rem;">
                                            <?php echo htmlspecialchars($exp['organization']); ?>
                                        </span>
                                        <span class="text-muted">&bull;</span>
                                        <span class="small text-muted"><?php echo htmlspecialchars($exp['type']); ?></span>
                                    </div>
                                    <p class="small text-secondary mb-0" style="line-height: 1.7;">
                                        <?php echo nl2br(htmlspecialchars($exp['description'])); ?>
                                    </p>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Kolom Pendidikan -->
                <div class="col-lg-6">
                    <h3 class="h4 mb-4 d-flex align-items-center gap-2">
                        <i class="fa-solid fa-graduation-cap text-gradient" style="color: var(--accent-secondary);"></i>
                        Riwayat Pendidikan
                    </h3>
                    <?php if (empty($educations)): ?>
                        <div class="glass-card text-center text-muted py-4">Belum ada data pendidikan.</div>
                    <?php else: ?>
                        <div class="d-flex flex-column gap-3">
                            <?php foreach ($educations as $edu): ?>
                                <div class="glass-card">
                                    <div class="d-flex align-items-start gap-3">
                                        <?php 
                                        $edu_logo = resolveImgUrl($edu['logo'] ?? '', 'education');
                                        if (!empty($edu['logo'])): 
                                        ?>
                                            <img src="<?php echo htmlspecialchars($edu_logo); ?>" 
                                                 alt="Logo" 
                                                 style="width: 50px; height: 50px; object-fit: contain; border-radius: 8px; background: white; padding: 4px; flex-shrink: 0;" 
                                                 onerror="this.style.display='none'">
                                        <?php endif; ?>
                                        
                                        <div class="flex-grow-1">
                                            <div class="d-flex justify-content-between align-items-start mb-1 flex-wrap gap-2">
                                                <h4 class="h5 mb-0 fw-bold"><?php echo htmlspecialchars($edu['institution']); ?></h4>
                                                <span class="badge-glass"><?php echo htmlspecialchars($edu['start_year'] . ' - ' . $edu['end_year']); ?></span>
                                            </div>
                                            <div class="mb-2" style="color: var(--accent-primary); font-weight: 500; font-size: 0.95rem;">
                                                <?php echo htmlspecialchars($edu['degree_major']); ?>
                                            </div>
                                            <?php if (!empty($edu['description'])): ?>
                                                <p class="small text-secondary mb-0" style="line-height: 1.7;">
                                                    <?php echo nl2br(htmlspecialchars($edu['description'])); ?>
                                                </p>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </section>

        <!-- SECTION KEAHLIAN (#skills) -->
        <section id="skills" class="section-gap">
            <div class="text-center mb-5">
                <span class="section-label">Keahlian & Kompetensi</span>
                <h2 class="gradient-text">Keahlian</h2>
            </div>

            <?php if (empty($skills)): ?>
                <div class="glass-card text-center text-muted py-4">Belum ada data keahlian.</div>
            <?php else: ?>
                <div class="row g-4">
                    <?php foreach ($skills as $sk): ?>
                        <div class="col-md-4 col-sm-6">
                            <div class="glass-card p-4">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <span class="fw-bold" style="font-family: var(--font-main); font-size: 1.05rem;">
                                        <?php echo htmlspecialchars($sk['skill_name']); ?>
                                    </span>
                                    <span class="badge-glass py-1 px-2" style="font-size: 0.7rem;">
                                        <?php echo htmlspecialchars($sk['category']); ?>
                                    </span>
                                </div>
                                <div class="d-flex justify-content-between align-items-center text-muted small mb-1">
                                    <span>Tingkat Kemahiran</span>
                                    <span class="fw-semibold" style="color: var(--accent-primary);"><?php echo (int)$sk['proficiency_level']; ?>%</span>
                                </div>
                                <div class="skill-bar">
                                    <div class="skill-fill" style="width: <?php echo (int)$sk['proficiency_level']; ?>%;"></div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </section>

        <!-- SECTION PROYEK (#projects) -->
        <section id="projects" class="section-gap">
            <div class="text-center mb-5">
                <span class="section-label">Karya & Eksplorasi Digital</span>
                <h2 class="gradient-text">Proyek Terpilih</h2>
            </div>

            <?php if (empty($projects)): ?>
                <div class="glass-card text-center text-muted py-4">Belum ada portofolio proyek.</div>
            <?php else: ?>
                <div class="row g-4">
                    <?php foreach ($projects as $proj): ?>
                        <div class="col-lg-4 col-md-6">
                            <div class="glass-card project-card h-100 d-flex flex-column justify-content-between">
                                <div>
                                    <?php 
                                    $proj_img = resolveImgUrl($proj['image'] ?? '', 'projects');
                                    if (!empty($proj['image'])): 
                                    ?>
                                        <img src="<?php echo htmlspecialchars($proj_img); ?>" 
                                             alt="Project" 
                                             style="width: 100%; height: 200px; object-fit: cover; border-radius: var(--radius-lg) var(--radius-lg) 0 0;" 
                                             onerror="this.style.display='none'">
                                    <?php endif; ?>

                                    <div class="project-card-body">
                                        <div class="d-flex justify-content-between align-items-start mb-2">
                                            <h4 class="h5 mb-0 fw-bold"><?php echo htmlspecialchars($proj['project_name']); ?></h4>
                                            <div class="project-links d-flex gap-2">
                                                <?php if (!empty($proj['repository_url'])): ?>
                                                    <a href="<?php echo htmlspecialchars($proj['repository_url']); ?>" target="_blank" title="GitHub Repository">
                                                        <i class="fa-brands fa-github"></i>
                                                    </a>
                                                <?php endif; ?>
                                                <?php if (!empty($proj['project_url'])): ?>
                                                    <a href="<?php echo htmlspecialchars($proj['project_url']); ?>" target="_blank" title="Live Preview">
                                                        <i class="fa-solid fa-arrow-up-right-from-square"></i>
                                                    </a>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                        <p class="small text-secondary mb-3" style="line-height: 1.7;">
                                            <?php echo nl2br(htmlspecialchars($proj['description'])); ?>
                                        </p>
                                        <div class="d-flex flex-wrap gap-1 mt-2">
                                            <?php 
                                            $techs = explode(',', $proj['tech_stack']);
                                            foreach ($techs as $tech): 
                                                $tech = trim($tech);
                                                if(!empty($tech)):
                                            ?>
                                                <span class="badge-glass"><?php echo htmlspecialchars($tech); ?></span>
                                            <?php 
                                                endif;
                                            endforeach; 
                                            ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </section>

        <!-- SECTION SERTIFIKAT (#certificates) -->
        <section id="certificates" class="section-gap">
            <div class="text-center mb-5">
                <span class="section-label">Pengakuan & Lisensi</span>
                <h2 class="gradient-text">Sertifikasi</h2>
            </div>

            <?php if (empty($certificates)): ?>
                <div class="glass-card text-center text-muted py-4">Belum ada data sertifikat.</div>
            <?php else: ?>
                <div class="row g-4">
                    <?php foreach ($certificates as $cert): ?>
                        <div class="col-md-6 col-lg-4">
                            <div class="glass-card h-100 d-flex flex-column justify-content-between p-4">
                                <div>
                                    <div class="d-flex align-items-center justify-content-between gap-2 mb-3">
                                        <span class="badge-glass">
                                            <?php echo !empty($cert['issued_date']) ? date('M Y', strtotime($cert['issued_date'])) : 'Terverifikasi'; ?>
                                        </span>
                                        <?php if (!empty($cert['credential_url'])): ?>
                                            <a href="<?php echo htmlspecialchars($cert['credential_url']); ?>" target="_blank" class="btn-outline py-1 px-2" style="font-size: 0.75rem;" title="Lihat Kredensial">
                                                <i class="fa-solid fa-arrow-up-right-from-square"></i>
                                            </a>
                                        <?php endif; ?>
                                    </div>

                                    <div class="d-flex align-items-start gap-3 mb-2">
                                        <?php 
                                        $cert_file = resolveImgUrl($cert['certificate_file'] ?? '', 'certificates');
                                        if (!empty($cert['certificate_file'])): 
                                        ?>
                                            <img src="<?php echo htmlspecialchars($cert_file); ?>" 
                                                 alt="Sertifikat" 
                                                 style="width: 60px; height: 80px; object-fit: cover; border-radius: 4px; border: 1px solid var(--border-glass); flex-shrink: 0;" 
                                                 onerror="this.style.display='none'">
                                        <?php else: ?>
                                            <div style="width: 44px; height: 44px; border-radius: var(--radius-sm); background: rgba(192, 132, 252, 0.12); color: var(--accent-primary); display: flex; align-items: center; justify-content: center; font-size: 1.25rem; border: 1px solid var(--border-glass); flex-shrink: 0;">
                                                <i class="fa-solid fa-certificate"></i>
                                            </div>
                                        <?php endif; ?>

                                        <div>
                                            <h4 class="h6 fw-bold mb-1"><?php echo htmlspecialchars($cert['certificate_name']); ?></h4>
                                            <p class="small text-muted mb-0"><?php echo htmlspecialchars($cert['issuer']); ?></p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </section>

    </div>
</div>

<!-- FOOTER (No Print) -->
<footer class="modern-footer no-print position-relative">
    <div class="container">
        <div class="footer-links mb-3">
            <?php if (!empty($profile['linkedin_url'])): ?>
                <a href="<?php echo htmlspecialchars($profile['linkedin_url']); ?>" target="_blank" class="mx-2 fs-5">
                    <i class="fa-brands fa-linkedin"></i>
                </a>
            <?php endif; ?>
            <?php if (!empty($profile['github_url'])): ?>
                <a href="<?php echo htmlspecialchars($profile['github_url']); ?>" target="_blank" class="mx-2 fs-5">
                    <i class="fa-brands fa-github"></i>
                </a>
            <?php endif; ?>
            <?php if (!empty($profile['email'])): ?>
                <a href="mailto:<?php echo htmlspecialchars($profile['email']); ?>" class="mx-2 fs-5">
                    <i class="fa-solid fa-envelope"></i>
                </a>
            <?php endif; ?>
        </div>
        <p class="mb-1 text-muted">
            &copy; <?php echo date('Y'); ?> <?php echo htmlspecialchars($profile['full_name'] ?? 'Nadila Sandra Dewi'); ?>. Hak cipta dilindungi undang-undang.
        </p>
        <p class="small text-muted mb-0">
            Dibuat menggunakan PHP Native, MySQL & LIVA UX Specialist Design Token.
        </p>
    </div>

    <!-- Hidden subtle login link in bottom right -->
    <a href="login.php" style="position: absolute; right: 20px; bottom: 15px; opacity: 0.3; font-size: 0.75rem; color: var(--text-muted); text-decoration: none; transition: var(--transition);" onmouseover="this.style.opacity=0.9" onmouseout="this.style.opacity=0.3" title="Area Administrator">
        <i class="fa-solid fa-lock me-1"></i> Admin
    </a>
</footer>

<!-- Bootstrap 5 JS CDN -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<!-- Export PDF Script -->
<script src="assets/js/cv-export.js"></script>
</body>
</html>