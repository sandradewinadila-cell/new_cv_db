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
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Curriculum Vitae - <?php echo htmlspecialchars($profile['full_name'] ?? 'Nadila Sandra Dewi'); ?></title>
    <!-- Bootstrap 5 CSS CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons CDN -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <!-- Custom Style -->
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body class="bg-light text-dark">

<!-- Action Bar (No Print) -->
<div class="action-bar-container no-print py-3 bg-white border-bottom shadow-sm sticky-top">
    <div class="container d-flex justify-content-between align-items-center">
        <span class="fw-bold text-primary fs-5">
            <i class="bi bi-file-earmark-person me-2"></i>Curriculum Vitae
        </span>
        <div class="d-flex gap-2">
            <button id="btn-download-pdf" class="btn btn-primary fw-semibold shadow-sm">
                <i class="bi bi-file-earmark-pdf-fill me-1"></i> Unduh CV (PDF)
            </button>
            <a href="login.php" class="btn btn-outline-secondary btn-sm d-flex align-items-center">
                <i class="bi bi-shield-lock me-1"></i> Admin
            </a>
        </div>
    </div>
</div>

<!-- Main Document Wrapper for PDF & Screen -->
<div class="cv-wrapper py-4 py-md-5">
    <div class="container">
        <div class="cv-container bg-white shadow-sm rounded-3 p-4 p-md-5 mx-auto" id="cv-content">
            
            <!-- Header / Hero Section -->
            <header class="border-bottom pb-4 mb-4">
                <div class="row align-items-center">
                    <div class="col-md-8">
                        <h1 class="display-6 fw-bold text-dark mb-1">
                            <?php echo htmlspecialchars($profile['full_name'] ?? 'Nadila Sandra Dewi'); ?>
                        </h1>
                        <p class="text-primary fw-semibold fs-5 mb-3">
                            Web Developer & UI/UX Enthusiast
                        </p>
                        <p class="text-secondary lead fs-6 mb-0 text-justify">
                            <?php echo nl2br(htmlspecialchars($profile['summary'] ?? '')); ?>
                        </p>
                    </div>
                    <div class="col-md-4 mt-4 mt-md-0">
                        <div class="contact-info-card p-3 rounded-3 bg-light">
                            <ul class="list-unstyled mb-0 small text-secondary">
                                <?php if (!empty($profile['email'])): ?>
                                    <li class="mb-2 text-break">
                                        <i class="bi bi-envelope-fill text-primary me-2"></i>
                                        <a href="mailto:<?php echo htmlspecialchars($profile['email']); ?>" class="text-decoration-none text-secondary">
                                            <?php echo htmlspecialchars($profile['email']); ?>
                                        </a>
                                    </li>
                                <?php endif; ?>
                                <?php if (!empty($profile['phone'])): ?>
                                    <li class="mb-2">
                                        <i class="bi bi-telephone-fill text-primary me-2"></i>
                                        <span><?php echo htmlspecialchars($profile['phone']); ?></span>
                                    </li>
                                <?php endif; ?>
                                <?php if (!empty($profile['address'])): ?>
                                    <li class="mb-2">
                                        <i class="bi bi-geo-alt-fill text-primary me-2"></i>
                                        <span><?php echo htmlspecialchars($profile['address']); ?></span>
                                    </li>
                                <?php endif; ?>
                                <?php if (!empty($profile['linkedin_url'])): ?>
                                    <li class="mb-2 text-break">
                                        <i class="bi bi-linkedin text-primary me-2"></i>
                                        <a href="<?php echo htmlspecialchars($profile['linkedin_url']); ?>" target="_blank" class="text-decoration-none text-secondary">
                                            LinkedIn Profile
                                        </a>
                                    </li>
                                <?php endif; ?>
                                <?php if (!empty($profile['github_url'])): ?>
                                    <li class="text-break">
                                        <i class="bi bi-github text-dark me-2"></i>
                                        <a href="<?php echo htmlspecialchars($profile['github_url']); ?>" target="_blank" class="text-decoration-none text-secondary">
                                            GitHub Profile
                                        </a>
                                    </li>
                                <?php endif; ?>
                            </ul>
                        </div>
                    </div>
                </div>
            </header>

            <div class="row g-4">
                <!-- Kolom Kiri: Pendidikan & Pengalaman -->
                <div class="col-lg-7">
                    
                    <!-- Section Pengalaman -->
                    <section class="mb-4 pb-2">
                        <h4 class="section-title fw-bold text-dark mb-3">
                            <i class="bi bi-briefcase-fill text-primary me-2"></i>Pengalaman & Organisasi
                        </h4>
                        <?php if (empty($experiences)): ?>
                            <p class="text-muted small">Belum ada riwayat pengalaman.</p>
                        <?php else: ?>
                            <div class="timeline">
                                <?php foreach ($experiences as $exp): ?>
                                    <div class="timeline-item mb-3 pb-2 border-start border-2 border-primary ps-3 position-relative">
                                        <div class="d-flex justify-content-between align-items-baseline mb-1">
                                            <h6 class="fw-bold mb-0 text-dark"><?php echo htmlspecialchars($exp['title']); ?></h6>
                                            <span class="badge bg-light text-secondary border small">
                                                <?php echo htmlspecialchars($exp['start_date'] . ' - ' . $exp['end_date']); ?>
                                            </span>
                                        </div>
                                        <div class="text-primary small fw-semibold mb-1">
                                            <?php echo htmlspecialchars($exp['organization']); ?>
                                            <span class="badge bg-secondary-subtle text-secondary ms-1"><?php echo htmlspecialchars($exp['type']); ?></span>
                                        </div>
                                        <p class="small text-muted mb-0"><?php echo nl2br(htmlspecialchars($exp['description'])); ?></p>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </section>

                    <!-- Section Pendidikan -->
                    <section class="mb-4">
                        <h4 class="section-title fw-bold text-dark mb-3">
                            <i class="bi bi-mortarboard-fill text-primary me-2"></i>Riwayat Pendidikan
                        </h4>
                        <?php if (empty($educations)): ?>
                            <p class="text-muted small">Belum ada riwayat pendidikan.</p>
                        <?php else: ?>
                            <div class="timeline">
                                <?php foreach ($educations as $edu): ?>
                                    <div class="timeline-item mb-3 border-start border-2 border-primary ps-3 position-relative">
                                        <div class="d-flex justify-content-between align-items-baseline mb-1">
                                            <h6 class="fw-bold mb-0 text-dark"><?php echo htmlspecialchars($edu['institution']); ?></h6>
                                            <span class="badge bg-light text-secondary border small">
                                                <?php echo htmlspecialchars($edu['start_year'] . ' - ' . $edu['end_year']); ?>
                                            </span>
                                        </div>
                                        <div class="text-primary small fw-semibold mb-1">
                                            <?php echo htmlspecialchars($edu['degree_major']); ?>
                                        </div>
                                        <?php if (!empty($edu['description'])): ?>
                                            <p class="small text-muted mb-0"><?php echo nl2br(htmlspecialchars($edu['description'])); ?></p>
                                        <?php endif; ?>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </section>

                    <!-- Section Proyek -->
                    <section class="mb-4">
                        <h4 class="section-title fw-bold text-dark mb-3">
                            <i class="bi bi-folder-fill text-primary me-2"></i>Proyek Portofolio
                        </h4>
                        <?php if (empty($projects)): ?>
                            <p class="text-muted small">Belum ada proyek yang ditambahkan.</p>
                        <?php else: ?>
                            <div class="row g-3">
                                <?php foreach ($projects as $proj): ?>
                                    <div class="col-12">
                                        <div class="border rounded p-3 h-100 bg-white">
                                            <div class="d-flex justify-content-between align-items-start mb-1">
                                                <h6 class="fw-bold mb-0 text-dark"><?php echo htmlspecialchars($proj['project_name']); ?></h6>
                                                <div class="d-flex gap-2">
                                                    <?php if (!empty($proj['repository_url'])): ?>
                                                        <a href="<?php echo htmlspecialchars($proj['repository_url']); ?>" target="_blank" class="text-dark small text-decoration-none" title="GitHub">
                                                            <i class="bi bi-github"></i>
                                                        </a>
                                                    <?php endif; ?>
                                                    <?php if (!empty($proj['project_url'])): ?>
                                                        <a href="<?php echo htmlspecialchars($proj['project_url']); ?>" target="_blank" class="text-primary small text-decoration-none" title="Live Demo">
                                                            <i class="bi bi-box-arrow-up-right"></i>
                                                        </a>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                            <p class="small text-muted mb-2"><?php echo nl2br(htmlspecialchars($proj['description'])); ?></p>
                                            <div>
                                                <span class="badge bg-primary-subtle text-primary border border-primary-subtle small">
                                                    <?php echo htmlspecialchars($proj['tech_stack']); ?>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </section>

                </div>

                <!-- Kolom Kanan: Keahlian & Sertifikat -->
                <div class="col-lg-5">
                    
                    <!-- Section Keahlian -->
                    <section class="mb-4 pb-2">
                        <h4 class="section-title fw-bold text-dark mb-3">
                            <i class="bi bi-stars text-primary me-2"></i>Keahlian (Skills)
                        </h4>
                        <?php if (empty($skills)): ?>
                            <p class="text-muted small">Belum ada data keahlian.</p>
                        <?php else: ?>
                            <div class="skills-wrapper">
                                <?php foreach ($skills as $sk): ?>
                                    <div class="mb-3">
                                        <div class="d-flex justify-content-between align-items-center mb-1">
                                            <span class="small fw-bold text-dark"><?php echo htmlspecialchars($sk['skill_name']); ?></span>
                                            <span class="small text-muted"><?php echo (int)$sk['proficiency_level']; ?>%</span>
                                        </div>
                                        <div class="progress" style="height: 6px;">
                                            <div class="progress-bar bg-primary" role="progressbar" style="width: <?php echo (int)$sk['proficiency_level']; ?>%;"></div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </section>

                    <!-- Section Sertifikat -->
                    <section class="mb-4">
                        <h4 class="section-title fw-bold text-dark mb-3">
                            <i class="bi bi-award-fill text-primary me-2"></i>Sertifikat & Lisensi
                        </h4>
                        <?php if (empty($certificates)): ?>
                            <p class="text-muted small">Belum ada data sertifikat.</p>
                        <?php else: ?>
                            <div class="list-group list-group-flush">
                                <?php foreach ($certificates as $cert): ?>
                                    <div class="list-group-item px-0 py-2 border-bottom">
                                        <div class="d-flex justify-content-between align-items-start">
                                            <div>
                                                <h6 class="fw-bold mb-0 text-dark small"><?php echo htmlspecialchars($cert['certificate_name']); ?></h6>
                                                <div class="text-muted" style="font-size: 0.8rem;">
                                                    <?php echo htmlspecialchars($cert['issuer']); ?> 
                                                    <?php if (!empty($cert['issued_date'])): ?>
                                                        &bull; <?php echo date('M Y', strtotime($cert['issued_date'])); ?>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                            <?php if (!empty($cert['credential_url'])): ?>
                                                <a href="<?php echo htmlspecialchars($cert['credential_url']); ?>" target="_blank" class="btn btn-sm btn-outline-primary py-0 px-2" style="font-size: 0.75rem;">
                                                    Lihat
                                                </a>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </section>

                </div>
            </div>

            <!-- Footer CV -->
            <footer class="border-top pt-3 mt-4 text-center text-muted small">
                <p class="mb-0">&copy; <?php echo date('Y'); ?> <?php echo htmlspecialchars($profile['full_name'] ?? 'Nadila Sandra Dewi'); ?>. Hak Cipta Dilindungi.</p>
            </footer>

        </div>
    </div>
</div>

<!-- Footer Website (No Print) -->
<footer class="no-print py-4 text-center text-muted small bg-white border-top">
    <div class="container">
        <p class="mb-1">CV Dinamis Berbasis Web &bull; Dibangun dengan PHP Native & MySQL</p>
        <a href="login.php" class="text-decoration-none text-secondary">
            <i class="bi bi-lock me-1"></i>Panel Administrator
        </a>
    </div>
</footer>

<!-- html2pdf.js CDN -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
<!-- Bootstrap 5 JS Bundle CDN -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<!-- Export PDF Script -->
<script src="assets/js/cv-export.js"></script>
</body>
</html>