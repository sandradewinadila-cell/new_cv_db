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

<div class="d-flex align-items-center justify-content-between mb-4">
    <h3 class="fw-bold mb-0">Manajemen Keahlian (Skills)</h3>
    <a href="admin.php" class="btn btn-secondary btn-sm">
        <i class="bi bi-arrow-left me-1"></i> Kembali ke Dashboard
    </a>
</div>

<!-- Form Section -->
<div class="card shadow-sm border-0 rounded-3 mb-4">
    <div class="card-header bg-white py-3">
        <h5 class="fw-bold mb-0 text-dark">
            <i class="bi bi-<?php echo $edit_data ? 'pencil-square' : 'plus-circle'; ?> me-2 text-warning"></i>
            <?php echo $edit_data ? 'Edit Data Keahlian' : 'Tambah Data Keahlian'; ?>
        </h5>
    </div>
    <div class="card-body p-4">
        <form action="admin_skills.php" method="POST">
            <?php if ($edit_data): ?>
                <input type="hidden" name="id" value="<?php echo htmlspecialchars($edit_data['id']); ?>">
            <?php endif; ?>

            <div class="row">
                <div class="col-md-5 mb-3">
                    <label for="skill_name" class="form-label fw-semibold">Nama Keahlian <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="skill_name" name="skill_name" 
                           value="<?php echo htmlspecialchars($edit_data['skill_name'] ?? ''); ?>" required placeholder="Contoh: PHP / Public Speaking">
                </div>
                <div class="col-md-4 mb-3">
                    <label for="category" class="form-label fw-semibold">Kategori <span class="text-danger">*</span></label>
                    <select class="form-select" id="category" name="category" required>
                        <option value="Technical" <?php echo (($edit_data['category'] ?? '') === 'Technical') ? 'selected' : ''; ?>>Technical</option>
                        <option value="Soft Skill" <?php echo (($edit_data['category'] ?? '') === 'Soft Skill') ? 'selected' : ''; ?>>Soft Skill</option>
                    </select>
                </div>
                <div class="col-md-3 mb-3">
                    <label for="proficiency_level" class="form-label fw-semibold">Tingkat Kemahiran (1-100%) <span class="text-danger">*</span></label>
                    <input type="number" class="form-control" id="proficiency_level" name="proficiency_level" min="1" max="100" 
                           value="<?php echo htmlspecialchars($edit_data['proficiency_level'] ?? '80'); ?>" required>
                </div>
            </div>

            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-warning text-dark fw-semibold">
                    <i class="bi bi-save me-1"></i> <?php echo $edit_data ? 'Simpan Perubahan' : 'Tambah Keahlian'; ?>
                </button>
                <?php if ($edit_data): ?>
                    <a href="admin_skills.php" class="btn btn-light border">Batal</a>
                <?php endif; ?>
            </div>
        </form>
    </div>
</div>

<!-- Table Section -->
<div class="card shadow-sm border-0 rounded-3 mb-5">
    <div class="card-header bg-white py-3">
        <h5 class="fw-bold mb-0 text-dark">
            <i class="bi bi-star me-2 text-warning"></i>Daftar Keahlian
        </h5>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-3" style="width: 50px;">No</th>
                        <th>Nama Keahlian</th>
                        <th>Kategori</th>
                        <th>Tingkat Kemahiran</th>
                        <th class="text-center" style="width: 150px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($skills)): ?>
                        <tr>
                            <td colspan="5" class="text-center py-4 text-muted">Belum ada data keahlian.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($skills as $index => $item): ?>
                            <tr>
                                <td class="ps-3 fw-semibold"><?php echo $index + 1; ?></td>
                                <td class="fw-bold"><?php echo htmlspecialchars($item['skill_name']); ?></td>
                                <td>
                                    <span class="badge <?php echo $item['category'] === 'Technical' ? 'bg-primary' : 'bg-success'; ?>">
                                        <?php echo htmlspecialchars($item['category']); ?>
                                    </span>
                                </td>
                                <td style="width: 30%;">
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="progress flex-grow-1" style="height: 8px;">
                                            <div class="progress-bar bg-warning" role="progressbar" style="width: <?php echo (int)$item['proficiency_level']; ?>%;"></div>
                                        </div>
                                        <span class="small fw-semibold"><?php echo (int)$item['proficiency_level']; ?>%</span>
                                    </div>
                                </td>
                                <td class="text-center">
                                    <a href="admin_skills.php?action=edit&id=<?php echo $item['id']; ?>" class="btn btn-sm btn-outline-primary me-1">
                                        <i class="bi bi-pencil"></i> Edit
                                    </a>
                                    <a href="admin_skills.php?action=delete&id=<?php echo $item['id']; ?>" 
                                       class="btn btn-sm btn-outline-danger" 
                                       onclick="return confirm('Yakin ingin menghapus keahlian ini?')">
                                        <i class="bi bi-trash"></i> Hapus
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php
require_once __DIR__ . '/includes/footer.php';
?>