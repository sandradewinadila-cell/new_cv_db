<?php
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/upload_helper.php';

// Pastikan kolom logo tersedia di tabel education jika belum ada
try {
    $pdo->exec("ALTER TABLE education ADD COLUMN logo VARCHAR(255) NULL AFTER degree_major");
} catch (PDOException $e) {
    // Kolom sudah ada atau query diabaikan
}

$action = $_GET['action'] ?? '';
$id     = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$error_msg = '';

// Logika DELETE
if ($action === 'delete' && $id > 0) {
    $stmt = $pdo->prepare("DELETE FROM education WHERE id = :id");
    $stmt->execute(['id' => $id]);
    header('Location: admin_education.php');
    exit;
}

// Logika READ Single Data (Mode Edit)
$edit_data = null;
if ($action === 'edit' && $id > 0) {
    $stmt = $pdo->prepare("SELECT * FROM education WHERE id = :id LIMIT 1");
    $stmt->execute(['id' => $id]);
    $edit_data = $stmt->fetch();
}

// Logika POST (Simpan: Insert / Update)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $post_id      = isset($_POST['id']) && !empty($_POST['id']) ? (int)$_POST['id'] : null;
    $institution  = trim($_POST['institution'] ?? '');
    $degree_major = trim($_POST['degree_major'] ?? '');
    $start_year   = trim($_POST['start_year'] ?? '');
    $end_year     = trim($_POST['end_year'] ?? '');
    $description  = trim($_POST['description'] ?? '');
    $logo_path    = $_POST['old_logo'] ?? null;

    if (empty($institution) || empty($degree_major) || empty($start_year) || empty($end_year)) {
        $error_msg = "Harap lengkapi semua bidang yang wajib diisi.";
    } else {
        $has_new_logo = isset($_FILES['logo']) && $_FILES['logo']['error'] !== UPLOAD_ERR_NO_FILE;

        // Validasi wajib upload logo saat tambah data baru
        if (!$post_id && !$has_new_logo) {
            $error_msg = "Logo Institusi wajib diunggah untuk data baru.";
        }

        // Proses upload jika ada file logo
        if (empty($error_msg) && $has_new_logo) {
            $upload_res = uploadImage($_FILES['logo'], 'education');
            if ($upload_res['success']) {
                $logo_path = 'assets/uploads/education/' . $upload_res['filename'];
            } else {
                $error_msg = $upload_res['error'];
            }
        }

        if (empty($error_msg)) {
            if ($post_id) {
                // UPDATE
                $stmt = $pdo->prepare("
                    UPDATE education 
                    SET institution = :institution, 
                        degree_major = :degree_major, 
                        logo = :logo,
                        start_year = :start_year, 
                        end_year = :end_year, 
                        description = :description 
                    WHERE id = :id
                ");
                $stmt->execute([
                    'institution'  => $institution,
                    'degree_major' => $degree_major,
                    'logo'         => $logo_path,
                    'start_year'   => $start_year,
                    'end_year'     => $end_year,
                    'description'  => $description,
                    'id'           => $post_id
                ]);
            } else {
                // INSERT
                $stmt = $pdo->prepare("
                    INSERT INTO education (institution, degree_major, logo, start_year, end_year, description) 
                    VALUES (:institution, :degree_major, :logo, :start_year, :end_year, :description)
                ");
                $stmt->execute([
                    'institution'  => $institution,
                    'degree_major' => $degree_major,
                    'logo'         => $logo_path,
                    'start_year'   => $start_year,
                    'end_year'     => $end_year,
                    'description'  => $description
                ]);
            }

            header('Location: admin_education.php');
            exit;
        }
    }
}

// Logika READ All Data (Untuk Tabel)
$stmt = $pdo->query("SELECT * FROM education ORDER BY start_year DESC, id DESC");
$educations = $stmt->fetchAll();

require_once __DIR__ . '/includes/header.php';
?>

<div class="d-flex align-items-center justify-content-between flex-wrap gap-3 mb-4 fade-in-up">
    <div>
        <span class="section-label">Manajemen Akademik</span>
        <h2 class="gradient-text mb-0">Riwayat Pendidikan</h2>
    </div>
    <!-- HANYA SATU tombol kembali -->
    <a href="admin.php" class="btn-outline" style="padding: 0.6rem 1.25rem; font-size: 0.85rem;">
        <i class="fa-solid fa-arrow-left me-2"></i> Kembali ke Dashboard
    </a>
</div>

<?php if (!empty($error_msg)): ?>
    <div style="background: rgba(239, 68, 68, 0.15); border: 1px solid #ef4444; color: #fca5a5; padding: 1rem; border-radius: 8px; margin-bottom: 1.5rem;" class="fade-in-up">
        <i class="fa-solid fa-circle-exclamation me-2"></i> <?php echo htmlspecialchars($error_msg); ?>
    </div>
<?php endif; ?>

<!-- KONTAINER FORM -->
<div class="glass-card fade-in-up">
    <h3 class="h5 mb-4" style="color: var(--accent-primary);">
        <i class="fa-solid fa-<?php echo $edit_data ? 'pen-to-square' : 'circle-plus'; ?> me-2"></i>
        <?php echo $edit_data ? 'Edit Data Pendidikan' : 'Tambah Data Pendidikan Baru'; ?>
    </h3>
    
    <form action="admin_education.php<?php echo $edit_data ? '?action=edit&id='.$edit_data['id'] : ''; ?>" method="POST" enctype="multipart/form-data">
        <?php if ($edit_data): ?>
            <input type="hidden" name="id" value="<?php echo htmlspecialchars($edit_data['id']); ?>">
            <input type="hidden" name="old_logo" value="<?php echo htmlspecialchars($edit_data['logo'] ?? ''); ?>">
        <?php endif; ?>

        <div class="row g-4">
            <div class="col-md-6">
                <label for="institution">Nama Institusi / Sekolah <span style="color: var(--accent-secondary);">*</span></label>
                <input type="text" class="form-control" id="institution" name="institution" 
                       value="<?php echo htmlspecialchars($edit_data['institution'] ?? ''); ?>" required placeholder="Contoh: SMK Negeri 1 Dlanggu">
            </div>

            <div class="col-md-6">
                <label for="degree_major">Jurusan / Program Studi <span style="color: var(--accent-secondary);">*</span></label>
                <input type="text" class="form-control" id="degree_major" name="degree_major" 
                       value="<?php echo htmlspecialchars($edit_data['degree_major'] ?? ''); ?>" required placeholder="Contoh: Rekayasa Perangkat Lunak">
            </div>

            <!-- Input File Logo Institusi -->
            <div class="col-12">
                <label for="logo">
                    Logo Institusi 
                    <?php if (empty($edit_data['logo'])): ?>
                        <span style="color: var(--accent-secondary);">* (Wajib Diunggah)</span>
                    <?php else: ?>
                        <span class="text-muted">(Opsional jika tidak ingin mengubah)</span>
                    <?php endif; ?>
                </label>

                <div class="d-flex align-items-center gap-3 flex-wrap">
                    <?php if (!empty($edit_data['logo'])): ?>
                        <div style="text-align: center;">
                            <img src="<?php echo htmlspecialchars($edit_data['logo']); ?>" alt="Logo Saat Ini" 
                                 style="width: 60px; height: 60px; object-fit: contain; background: rgba(255, 255, 255, 0.08); padding: 5px; border-radius: 8px; border: 1px solid var(--border-glass);">
                            <div class="small text-muted mt-1" style="font-size: 0.7rem;">Logo Terpasang</div>
                        </div>
                    <?php endif; ?>
                    <div class="flex-grow-1" style="min-width: 250px;">
                        <input type="file" class="form-control" id="logo" name="logo" accept=".jpg, .jpeg, .png, .webp" 
                               <?php echo empty($edit_data['logo']) ? 'required' : ''; ?>>
                        <small class="text-muted d-block mt-1">Format: JPG, JPEG, PNG, WEBP. Maksimal 2MB.</small>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <label for="start_year">Tahun Mulai <span style="color: var(--accent-secondary);">*</span></label>
                <input type="text" class="form-control" id="start_year" name="start_year" 
                       value="<?php echo htmlspecialchars($edit_data['start_year'] ?? ''); ?>" required placeholder="Contoh: 2024">
            </div>

            <div class="col-md-6">
                <label for="end_year">Tahun Selesai <span style="color: var(--accent-secondary);">*</span></label>
                <input type="text" class="form-control" id="end_year" name="end_year" 
                       value="<?php echo htmlspecialchars($edit_data['end_year'] ?? ''); ?>" required placeholder="Contoh: 2027 atau Sekarang">
            </div>

            <div class="col-12">
                <label for="description">Keterangan / Aktivitas Tambahan</label>
                <textarea class="form-control" id="description" name="description" rows="3" placeholder="Informasi fokus keahlian, prestasi, atau aktivitas..."><?php echo htmlspecialchars($edit_data['description'] ?? ''); ?></textarea>
            </div>

            <div class="col-12 d-flex gap-3 pt-2">
                <button type="submit" class="btn-primary">
                    <i class="fa-solid fa-floppy-disk me-2"></i> <?php echo $edit_data ? 'Simpan Perubahan' : 'Simpan Data'; ?>
                </button>
                <?php if ($edit_data): ?>
                    <a href="admin_education.php" class="btn-outline">Batal</a>
                <?php endif; ?>
            </div>
        </div>
    </form>
</div>

<!-- KONTAINER TABEL -->
<div class="glass-card fade-in-up" style="margin-top: 2rem; overflow-x: auto;">
    <h3 class="h5 mb-4" style="color: var(--text-primary);">
        <i class="fa-solid fa-list me-2" style="color: var(--accent-secondary);"></i>Daftar Riwayat Pendidikan
    </h3>
    <table style="width: 100%; border-collapse: collapse; color: var(--text-primary);">
        <thead>
            <tr>
                <th style="text-align: left; padding: 1rem; border-bottom: 1px solid var(--border-glass); color: var(--accent-primary); font-family: var(--font-main); width: 60px;">No</th>
                <th style="text-align: left; padding: 1rem; border-bottom: 1px solid var(--border-glass); color: var(--accent-primary); font-family: var(--font-main); width: 80px;">Logo</th>
                <th style="text-align: left; padding: 1rem; border-bottom: 1px solid var(--border-glass); color: var(--accent-primary); font-family: var(--font-main);">Institusi</th>
                <th style="text-align: left; padding: 1rem; border-bottom: 1px solid var(--border-glass); color: var(--accent-primary); font-family: var(--font-main);">Jurusan</th>
                <th style="text-align: left; padding: 1rem; border-bottom: 1px solid var(--border-glass); color: var(--accent-primary); font-family: var(--font-main);">Periode</th>
                <th style="text-align: left; padding: 1rem; border-bottom: 1px solid var(--border-glass); color: var(--accent-primary); font-family: var(--font-main);">Keterangan</th>
                <th style="text-align: center; padding: 1rem; border-bottom: 1px solid var(--border-glass); color: var(--accent-primary); font-family: var(--font-main); width: 160px;">Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($educations)): ?>
                <tr>
                    <td colspan="7" style="padding: 2rem 1rem; text-align: center; color: var(--text-muted);">Belum ada data pendidikan.</td>
                </tr>
            <?php else: ?>
                <?php foreach ($educations as $index => $item): ?>
                    <tr>
                        <td style="padding: 1rem; border-bottom: 1px solid var(--border-glass); color: var(--text-muted);"><?php echo $index + 1; ?></td>
                        <td style="padding: 1rem; border-bottom: 1px solid var(--border-glass);">
                            <?php if (!empty($item['logo'])): ?>
                                <img src="<?php echo htmlspecialchars($item['logo']); ?>" alt="Logo" style="width: 45px; height: 45px; object-fit: contain; background: rgba(255,255,255,0.06); padding: 4px; border-radius: 6px; border: 1px solid var(--border-glass);">
                            <?php else: ?>
                                <span class="text-muted" style="font-size: 0.8rem;">-</span>
                            <?php endif; ?>
                        </td>
                        <td style="padding: 1rem; border-bottom: 1px solid var(--border-glass); font-weight: 600;"><?php echo htmlspecialchars($item['institution']); ?></td>
                        <td style="padding: 1rem; border-bottom: 1px solid var(--border-glass);"><?php echo htmlspecialchars($item['degree_major']); ?></td>
                        <td style="padding: 1rem; border-bottom: 1px solid var(--border-glass);">
                            <span class="badge-glass"><?php echo htmlspecialchars($item['start_year'] . ' - ' . $item['end_year']); ?></span>
                        </td>
                        <td style="padding: 1rem; border-bottom: 1px solid var(--border-glass); font-size: 0.9rem; color: var(--text-secondary); max-width: 250px;">
                            <?php echo nl2br(htmlspecialchars($item['description'] ?? '-')); ?>
                        </td>
                        <td style="padding: 1rem; border-bottom: 1px solid var(--border-glass); text-align: center;">
                            <div class="d-flex justify-content-center gap-2">
                                <a href="?action=edit&id=<?php echo $item['id']; ?>" class="btn-outline" style="padding: 0.25rem 0.75rem; font-size: 0.8rem;">Edit</a>
                                <a href="?action=delete&id=<?php echo $item['id']; ?>" class="btn-outline" style="padding: 0.25rem 0.75rem; font-size: 0.8rem; border-color: #ef4444; color: #ef4444;" onclick="return confirm('Yakin hapus data pendidikan ini?')">Hapus</a>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php
require_once __DIR__ . '/includes/footer.php';
?>