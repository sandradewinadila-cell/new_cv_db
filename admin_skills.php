<?php
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/config/database.php';

$action = $_GET['action'] ?? '';
$id     = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Logika DELETE
if ($action === 'delete' && $id > 0) {
    $stmt = $pdo->prepare("DELETE FROM skills WHERE id = :id");
    $stmt->execute(['id' => $id]);
    header('Location: admin_skills.php');
    exit;
}

// Logika POST (Simpan: Insert / Update)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $post_id           = isset($_POST['id']) && !empty($_POST['id']) ? (int)$_POST['id'] : null;
    $skill_name        = trim($_POST['skill_name'] ?? '');
    $category          = trim($_POST['category'] ?? 'Technical');
    $proficiency_level = isset($_POST['proficiency_level']) ? (int)$_POST['proficiency_level'] : 80;

    if ($post_id) {
        // UPDATE
        $stmt = $pdo->prepare("
            UPDATE skills 
            SET skill_name = :skill_name, 
                category = :category, 
                proficiency_level = :proficiency_level 
            WHERE id = :id
        ");
        $stmt->execute([
            'skill_name'        => $skill_name,
            'category'          => $category,
            'proficiency_level' => $proficiency_level,
            'id'                => $post_id
        ]);
    } else {
        // INSERT
        $stmt = $pdo->prepare("
            INSERT INTO skills (skill_name, category, proficiency_level) 
            VALUES (:skill_name, :category, :proficiency_level)
        ");
        $stmt->execute([
            'skill_name'        => $skill_name,
            'category'          => $category,
            'proficiency_level' => $proficiency_level
        ]);
    }

    header('Location: admin_skills.php');
    exit;
}

// Logika READ Single Data (Mode Edit)
$edit_data = null;
if ($action === 'edit' && $id > 0) {
    $stmt = $pdo->prepare("SELECT * FROM skills WHERE id = :id LIMIT 1");
    $stmt->execute(['id' => $id]);
    $edit_data = $stmt->fetch();
}

// Logika READ All Data (Untuk Tabel)
$stmt = $pdo->query("SELECT * FROM skills ORDER BY category ASC, proficiency_level DESC, id DESC");
$skills = $stmt->fetchAll();

require_once __DIR__ . '/includes/header.php';
?>

<div class="d-flex align-items-center justify-content-between flex-wrap gap-3 mb-4 fade-in-up">
    <div>
        <span class="section-label">Manajemen Kompetensi</span>
        <h2 class="gradient-text mb-0">Keahlian (Skills)</h2>
    </div>
    <!-- HANYA SATU tombol kembali -->
    <a href="admin.php" class="btn-outline" style="padding: 0.6rem 1.25rem; font-size: 0.85rem;">
        <i class="fa-solid fa-arrow-left me-2"></i> Kembali ke Dashboard
    </a>
</div>

<!-- KONTAINER FORM -->
<div class="glass-card fade-in-up">
    <h3 class="h5 mb-4" style="color: var(--accent-primary);">
        <i class="fa-solid fa-<?php echo $edit_data ? 'pen-to-square' : 'circle-plus'; ?> me-2"></i>
        <?php echo $edit_data ? 'Edit Data Keahlian' : 'Tambah Keahlian Baru'; ?>
    </h3>
    
    <form action="admin_skills.php" method="POST">
        <?php if ($edit_data): ?>
            <input type="hidden" name="id" value="<?php echo htmlspecialchars($edit_data['id']); ?>">
        <?php endif; ?>

        <div class="row g-4">
            <div class="col-md-5">
                <label for="skill_name">Nama Keahlian <span style="color: var(--accent-secondary);">*</span></label>
                <input type="text" class="form-control" id="skill_name" name="skill_name" 
                       value="<?php echo htmlspecialchars($edit_data['skill_name'] ?? ''); ?>" required placeholder="Contoh: PHP / Public Speaking">
            </div>

            <div class="col-md-4">
                <label for="category">Kategori <span style="color: var(--accent-secondary);">*</span></label>
                <select class="form-control" id="category" name="category" required>
                    <option value="Technical" <?php echo (($edit_data['category'] ?? '') === 'Technical') ? 'selected' : ''; ?>>Technical</option>
                    <option value="Soft Skill" <?php echo (($edit_data['category'] ?? '') === 'Soft Skill') ? 'selected' : ''; ?>>Soft Skill</option>
                </select>
            </div>

            <div class="col-md-3">
                <label for="proficiency_level">Tingkat Kemahiran (1-100%) <span style="color: var(--accent-secondary);">*</span></label>
                <input type="number" class="form-control" id="proficiency_level" name="proficiency_level" min="1" max="100" 
                       value="<?php echo htmlspecialchars($edit_data['proficiency_level'] ?? '80'); ?>" required>
            </div>

            <div class="col-12 d-flex gap-3 pt-2">
                <button type="submit" class="btn-primary">
                    <i class="fa-solid fa-floppy-disk me-2"></i> <?php echo $edit_data ? 'Simpan Perubahan' : 'Simpan Keahlian'; ?>
                </button>
                <?php if ($edit_data): ?>
                    <a href="admin_skills.php" class="btn-outline">Batal</a>
                <?php endif; ?>
            </div>
        </div>
    </form>
</div>

<!-- KONTAINER TABEL -->
<div class="glass-card fade-in-up" style="margin-top: 2rem; overflow-x: auto;">
    <h3 class="h5 mb-4" style="color: var(--text-primary);">
        <i class="fa-solid fa-list me-2" style="color: var(--accent-secondary);"></i>Daftar Keahlian
    </h3>
    <table style="width: 100%; border-collapse: collapse; color: var(--text-primary);">
        <thead>
            <tr>
                <th style="text-align: left; padding: 1rem; border-bottom: 1px solid var(--border-glass); color: var(--accent-primary); font-family: var(--font-main); width: 60px;">No</th>
                <th style="text-align: left; padding: 1rem; border-bottom: 1px solid var(--border-glass); color: var(--accent-primary); font-family: var(--font-main);">Nama Keahlian</th>
                <th style="text-align: left; padding: 1rem; border-bottom: 1px solid var(--border-glass); color: var(--accent-primary); font-family: var(--font-main);">Kategori</th>
                <th style="text-align: left; padding: 1rem; border-bottom: 1px solid var(--border-glass); color: var(--accent-primary); font-family: var(--font-main);">Tingkat Kemahiran</th>
                <th style="text-align: center; padding: 1rem; border-bottom: 1px solid var(--border-glass); color: var(--accent-primary); font-family: var(--font-main); width: 160px;">Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($skills)): ?>
                <tr>
                    <td colspan="5" style="padding: 2rem 1rem; text-align: center; color: var(--text-muted);">Belum ada data keahlian.</td>
                </tr>
            <?php else: ?>
                <?php foreach ($skills as $index => $item): ?>
                    <tr>
                        <td style="padding: 1rem; border-bottom: 1px solid var(--border-glass); color: var(--text-muted);"><?php echo $index + 1; ?></td>
                        <td style="padding: 1rem; border-bottom: 1px solid var(--border-glass); font-weight: 600;"><?php echo htmlspecialchars($item['skill_name']); ?></td>
                        <td style="padding: 1rem; border-bottom: 1px solid var(--border-glass);">
                            <span class="badge-glass"><?php echo htmlspecialchars($item['category']); ?></span>
                        </td>
                        <td style="padding: 1rem; border-bottom: 1px solid var(--border-glass); width: 35%;">
                            <div class="d-flex align-items-center gap-3">
                                <div class="skill-bar flex-grow-1" style="margin-top: 0;">
                                    <div class="skill-fill" style="width: <?php echo (int)$item['proficiency_level']; ?>%;"></div>
                                </div>
                                <span style="font-size: 0.85rem; font-weight: 600; color: var(--accent-primary); width: 40px; text-align: right;"><?php echo (int)$item['proficiency_level']; ?>%</span>
                            </div>
                        </td>
                        <td style="padding: 1rem; border-bottom: 1px solid var(--border-glass); text-align: center;">
                            <div class="d-flex justify-content-center gap-2">
                                <a href="?action=edit&id=<?php echo $item['id']; ?>" class="btn-outline" style="padding: 0.25rem 0.75rem; font-size: 0.8rem;">Edit</a>
                                <a href="?action=delete&id=<?php echo $item['id']; ?>" class="btn-outline" style="padding: 0.25rem 0.75rem; font-size: 0.8rem; border-color: #ef4444; color: #ef4444;" onclick="return confirm('Yakin hapus keahlian ini?')">Hapus</a>
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