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

<div class="d-flex align-items-center justify-content-between mb-4">
    <h3 class="fw-bold mb-0">Manajemen Pengalaman</h3>
    <a href="admin.php" class="btn btn-secondary btn-sm">
        <i class="bi bi-arrow-left me-1"></i> Kembali ke Dashboard
    </a>
</div>

<!-- Form Section -->
<div class="card shadow-sm border-0 rounded-3 mb-4">
    <div class="card-header bg-white py-3">
        <h5 class="fw-bold mb-0 text-success">
            <i class="bi bi-<?php echo $edit_data ? 'pencil-square' : 'plus-circle'; ?> me-2"></i>
            <?php echo $edit_data ? 'Edit Data Pengalaman' : 'Tambah Data Pengalaman'; ?>
        </h5>
    </div>
    <div class="card-body p-4">
        <form action="admin_experience.php" method="POST">
            <?php if ($edit_data): ?>
                <input type="hidden" name="id" value="<?php echo htmlspecialchars($edit_data['id']); ?>">
            <?php endif; ?>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="title" class="form-label fw-semibold">Posisi / Peran <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="title" name="title" 
                           value="<?php echo htmlspecialchars($edit_data['title'] ?? ''); ?>" required placeholder="Contoh: Web Developer Intern / Anggota IT">
                </div>
                <div class="col-md-6 mb-3">
                    <label for="organization" class="form-label fw-semibold">Nama Instansi / Perusahaan / Organisasi <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="organization" name="organization" 
                           value="<?php echo htmlspecialchars($edit_data['organization'] ?? ''); ?>" required placeholder="Contoh: OSIS SMK Negeri 1 Dlanggu">
                </div>
            </div>

            <div class="row">
                <div class="col-md-4 mb-3">
                    <label for="type" class="form-label fw-semibold">Kategori Pengalaman <span class="text-danger">*</span></label>
                    <select class="form-select" id="type" name="type" required>
                        <option value="Organisasi" <?php echo (($edit_data['type'] ?? '') === 'Organisasi') ? 'selected' : ''; ?>>Organisasi</option>
                        <option value="PKL/Magang" <?php echo (($edit_data['type'] ?? '') === 'PKL/Magang') ? 'selected' : ''; ?>>PKL/Magang</option>
                        <option value="Project Work" <?php echo (($edit_data['type'] ?? '') === 'Project Work') ? 'selected' : ''; ?>>Project Work</option>
                    </select>
                </div>
                <div class="col-md-4 mb-3">
                    <label for="start_date" class="form-label fw-semibold">Waktu Mulai <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="start_date" name="start_date" 
                           value="<?php echo htmlspecialchars($edit_data['start_date'] ?? ''); ?>" required placeholder="Contoh: Agu 2024">
                </div>
                <div class="col-md-4 mb-3">
                    <label for="end_date" class="form-label fw-semibold">Waktu Selesai <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="end_date" name="end_date" 
                           value="<?php echo htmlspecialchars($edit_data['end_date'] ?? ''); ?>" required placeholder="Contoh: Sekarang / Des 2024">
                </div>
            </div>

            <div class="mb-3">
                <label for="description" class="form-label fw-semibold">Deskripsi Tugas & Tanggung Jawab <span class="text-danger">*</span></label>
                <textarea class="form-control" id="description" name="description" rows="3" required placeholder="Jelaskan peran, tanggung jawab, dan hasil kerja"><?php echo htmlspecialchars($edit_data['description'] ?? ''); ?></textarea>
            </div>

            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-success">
                    <i class="bi bi-save me-1"></i> <?php echo $edit_data ? 'Simpan Perubahan' : 'Tambah Data'; ?>
                </button>
                <?php if ($edit_data): ?>
                    <a href="admin_experience.php" class="btn btn-light border">Batal</a>
                <?php endif; ?>
            </div>
        </form>
    </div>
</div>

<!-- Table Section -->
<div class="card shadow-sm border-0 rounded-3 mb-5">
    <div class="card-header bg-white py-3">
        <h5 class="fw-bold mb-0 text-dark">
            <i class="bi bi-briefcase me-2"></i>Daftar Pengalaman
        </h5>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-3" style="width: 50px;">No</th>
                        <th>Posisi & Organisasi</th>
                        <th>Kategori</th>
                        <th>Periode</th>
                        <th>Deskripsi</th>
                        <th class="text-center" style="width: 150px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($experiences)): ?>
                        <tr>
                            <td colspan="6" class="text-center py-4 text-muted">Belum ada data pengalaman.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($experiences as $index => $item): ?>
                            <tr>
                                <td class="ps-3 fw-semibold"><?php echo $index + 1; ?></td>
                                <td>
                                    <div class="fw-bold"><?php echo htmlspecialchars($item['title']); ?></div>
                                    <div class="small text-muted"><?php echo htmlspecialchars($item['organization']); ?></div>
                                </td>
                                <td>
                                    <span class="badge bg-info text-dark"><?php echo htmlspecialchars($item['type']); ?></span>
                                </td>
                                <td><span class="badge bg-secondary"><?php echo htmlspecialchars($item['start_date'] . ' - ' . $item['end_date']); ?></span></td>
                                <td class="small text-muted"><?php echo nl2br(htmlspecialchars($item['description'])); ?></td>
                                <td class="text-center">
                                    <a href="admin_experience.php?action=edit&id=<?php echo $item['id']; ?>" class="btn btn-sm btn-outline-primary me-1">
                                        <i class="bi bi-pencil"></i> Edit
                                    </a>
                                    <a href="admin_experience.php?action=delete&id=<?php echo $item['id']; ?>" 
                                       class="btn btn-sm btn-outline-danger" 
                                       onclick="return confirm('Yakin ingin menghapus data ini?')">
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