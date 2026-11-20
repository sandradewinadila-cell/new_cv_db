<?php
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/config/database.php';

$action = $_GET['action'] ?? '';
$id     = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Logika DELETE
if ($action === 'delete' && $id > 0) {
    $stmt = $pdo->prepare("DELETE FROM experience WHERE id = :id");
    $stmt->execute(['id' => $id]);
    header('Location: admin_experience.php');
    exit;
}

// Logika POST (Simpan: Insert / Update)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $post_id      = isset($_POST['id']) && !empty($_POST['id']) ? (int)$_POST['id'] : null;
    $title        = trim($_POST['title'] ?? '');
    $organization = trim($_POST['organization'] ?? '');
    $type         = trim($_POST['type'] ?? 'Organisasi');
    $start_date   = trim($_POST['start_date'] ?? '');
    $end_date     = trim($_POST['end_date'] ?? '');
    $description  = trim($_POST['description'] ?? '');

    if ($post_id) {
        // UPDATE
        $stmt = $pdo->prepare("
            UPDATE experience 
            SET title = :title, 
                organization = :organization, 
                type = :type, 
                start_date = :start_date, 
                end_date = :end_date, 
                description = :description 
            WHERE id = :id
        ");
        $stmt->execute([
            'title'        => $title,
            'organization' => $organization,
            'type'         => $type,
            'start_date'   => $start_date,
            'end_date'     => $end_date,
            'description'  => $description,
            'id'           => $post_id
        ]);
    } else {
        // INSERT
        $stmt = $pdo->prepare("
            INSERT INTO experience (title, organization, type, start_date, end_date, description) 
            VALUES (:title, :organization, :type, :start_date, :end_date, :description)
        ");
        $stmt->execute([
            'title'        => $title,
            'organization' => $organization,
            'type'         => $type,
            'start_date'   => $start_date,
            'end_date'     => $end_date,
            'description'  => $description
        ]);
    }

    header('Location: admin_experience.php');
    exit;
}

// Logika READ Single Data (Mode Edit)
$edit_data = null;
if ($action === 'edit' && $id > 0) {
    $stmt = $pdo->prepare("SELECT * FROM experience WHERE id = :id LIMIT 1");
    $stmt->execute(['id' => $id]);
    $edit_data = $stmt->fetch();
}

// Logika READ All Data (Untuk Tabel)
$stmt = $pdo->query("SELECT * FROM experience ORDER BY id DESC");
$experiences = $stmt->fetchAll();

require_once __DIR__ . '/includes/header.php';
?>

<div class="d-flex align-items-center justify-content-between flex-wrap gap-3 mb-4 fade-in-up">
    <div>
        <span class="section-label">Manajemen Karir</span>
        <h2 class="gradient-text mb-0">Pengalaman & Organisasi</h2>
    </div>
    <!-- HANYA SATU tombol kembali -->
    <a href="admin.php" class="btn-outline" style="padding: 0.6rem 1.25rem; font-size: 0.85rem;">
        <i class="fa-solid fa-arrow-left me-2"></i> Kembali ke Dashboard
    </a>
</div>

<!-- KONTAINER FORM -->
<div class="glass-card fade-in-up">
    <h3 class="h5 mb-4" style="color: var(--accent-secondary);">
        <i class="fa-solid fa-<?php echo $edit_data ? 'pen-to-square' : 'circle-plus'; ?> me-2"></i>
        <?php echo $edit_data ? 'Edit Data Pengalaman' : 'Tambah Pengalaman Baru'; ?>
    </h3>
    
    <form action="admin_experience.php" method="POST">
        <?php if ($edit_data): ?>
            <input type="hidden" name="id" value="<?php echo htmlspecialchars($edit_data['id']); ?>">
        <?php endif; ?>

        <div class="row g-4">
            <div class="col-md-6">
                <label for="title">Posisi / Peran <span style="color: var(--accent-secondary);">*</span></label>
                <input type="text" class="form-control" id="title" name="title" 
                       value="<?php echo htmlspecialchars($edit_data['title'] ?? ''); ?>" required placeholder="Contoh: Web Developer Intern / Anggota IT">
            </div>

            <div class="col-md-6">
                <label for="organization">Instansi / Perusahaan / Organisasi <span style="color: var(--accent-secondary);">*</span></label>
                <input type="text" class="form-control" id="organization" name="organization" 
                       value="<?php echo htmlspecialchars($edit_data['organization'] ?? ''); ?>" required placeholder="Contoh: OSIS SMK Negeri 1 Dlanggu">
            </div>

            <div class="col-md-4">
                <label for="type">Kategori Pengalaman <span style="color: var(--accent-secondary);">*</span></label>
                <select class="form-control" id="type" name="type" required>
                    <option value="Organisasi" <?php echo (($edit_data['type'] ?? '') === 'Organisasi') ? 'selected' : ''; ?>>Organisasi</option>
                    <option value="PKL/Magang" <?php echo (($edit_data['type'] ?? '') === 'PKL/Magang') ? 'selected' : ''; ?>>PKL/Magang</option>
                    <option value="Project Work" <?php echo (($edit_data['type'] ?? '') === 'Project Work') ? 'selected' : ''; ?>>Project Work</option>
                </select>
            </div>

            <div class="col-md-4">
                <label for="start_date">Waktu Mulai <span style="color: var(--accent-secondary);">*</span></label>
                <input type="text" class="form-control" id="start_date" name="start_date" 
                       value="<?php echo htmlspecialchars($edit_data['start_date'] ?? ''); ?>" required placeholder="Contoh: Agu 2024">
            </div>

            <div class="col-md-4">
                <label for="end_date">Waktu Selesai <span style="color: var(--accent-secondary);">*</span></label>
                <input type="text" class="form-control" id="end_date" name="end_date" 
                       value="<?php echo htmlspecialchars($edit_data['end_date'] ?? ''); ?>" required placeholder="Contoh: Sekarang / Des 2024">
            </div>

            <div class="col-12">
                <label for="description">Deskripsi Tanggung Jawab & Kontribusi <span style="color: var(--accent-secondary);">*</span></label>
                <textarea class="form-control" id="description" name="description" rows="3" required placeholder="Jelaskan peran spesifik dan hasil kontribusi Anda..."><?php echo htmlspecialchars($edit_data['description'] ?? ''); ?></textarea>
            </div>

            <div class="col-12 d-flex gap-3 pt-2">
                <button type="submit" class="btn-primary">
                    <i class="fa-solid fa-floppy-disk me-2"></i> <?php echo $edit_data ? 'Simpan Perubahan' : 'Simpan Data'; ?>
                </button>
                <?php if ($edit_data): ?>
                    <a href="admin_experience.php" class="btn-outline">Batal</a>
                <?php endif; ?>
            </div>
        </div>
    </form>
</div>

<!-- KONTAINER TABEL -->
<div class="glass-card fade-in-up" style="margin-top: 2rem; overflow-x: auto;">
    <h3 class="h5 mb-4" style="color: var(--text-primary);">
        <i class="fa-solid fa-list me-2" style="color: var(--accent-primary);"></i>Daftar Pengalaman
    </h3>
    <table style="width: 100%; border-collapse: collapse; color: var(--text-primary);">
        <thead>
            <tr>
                <th style="text-align: left; padding: 1rem; border-bottom: 1px solid var(--border-glass); color: var(--accent-primary); font-family: var(--font-main); width: 60px;">No</th>
                <th style="text-align: left; padding: 1rem; border-bottom: 1px solid var(--border-glass); color: var(--accent-primary); font-family: var(--font-main);">Posisi & Lembaga</th>
                <th style="text-align: left; padding: 1rem; border-bottom: 1px solid var(--border-glass); color: var(--accent-primary); font-family: var(--font-main);">Kategori</th>
                <th style="text-align: left; padding: 1rem; border-bottom: 1px solid var(--border-glass); color: var(--accent-primary); font-family: var(--font-main);">Periode</th>
                <th style="text-align: left; padding: 1rem; border-bottom: 1px solid var(--border-glass); color: var(--accent-primary); font-family: var(--font-main);">Deskripsi</th>
                <th style="text-align: center; padding: 1rem; border-bottom: 1px solid var(--border-glass); color: var(--accent-primary); font-family: var(--font-main); width: 160px;">Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($experiences)): ?>
                <tr>
                    <td colspan="6" style="padding: 2rem 1rem; text-align: center; color: var(--text-muted);">Belum ada data pengalaman.</td>
                </tr>
            <?php else: ?>
                <?php foreach ($experiences as $index => $item): ?>
                    <tr>
                        <td style="padding: 1rem; border-bottom: 1px solid var(--border-glass); color: var(--text-muted);"><?php echo $index + 1; ?></td>
                        <td style="padding: 1rem; border-bottom: 1px solid var(--border-glass);">
                            <div style="font-weight: 600;"><?php echo htmlspecialchars($item['title']); ?></div>
                            <div style="font-size: 0.85rem; color: var(--accent-secondary);"><?php echo htmlspecialchars($item['organization']); ?></div>
                        </td>
                        <td style="padding: 1rem; border-bottom: 1px solid var(--border-glass);">
                            <span class="badge-glass"><?php echo htmlspecialchars($item['type']); ?></span>
                        </td>
                        <td style="padding: 1rem; border-bottom: 1px solid var(--border-glass); font-size: 0.85rem; color: var(--text-muted);">
                            <?php echo htmlspecialchars($item['start_date'] . ' - ' . $item['end_date']); ?>
                        </td>
                        <td style="padding: 1rem; border-bottom: 1px solid var(--border-glass); font-size: 0.9rem; color: var(--text-secondary); max-width: 250px;">
                            <?php echo nl2br(htmlspecialchars($item['description'])); ?>
                        </td>
                        <td style="padding: 1rem; border-bottom: 1px solid var(--border-glass); text-align: center;">
                            <div class="d-flex justify-content-center gap-2">
                                <a href="?action=edit&id=<?php echo $item['id']; ?>" class="btn-outline" style="padding: 0.25rem 0.75rem; font-size: 0.8rem;">Edit</a>
                                <a href="?action=delete&id=<?php echo $item['id']; ?>" class="btn-outline" style="padding: 0.25rem 0.75rem; font-size: 0.8rem; border-color: #ef4444; color: #ef4444;" onclick="return confirm('Yakin hapus data pengalaman ini?')">Hapus</a>
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