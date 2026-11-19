<?php
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/config/database.php';

$action = $_GET['action'] ?? '';
$id     = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Logika DELETE
if ($action === 'delete' && $id > 0) {
    $stmt = $pdo->prepare("DELETE FROM projects WHERE id = :id");
    $stmt->execute(['id' => $id]);
    header('Location: admin_projects.php');
    exit;
}

// Logika POST (Simpan: Insert / Update)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $post_id        = isset($_POST['id']) && !empty($_POST['id']) ? (int)$_POST['id'] : null;
    $project_name   = trim($_POST['project_name'] ?? '');
    $description    = trim($_POST['description'] ?? '');
    $tech_stack     = trim($_POST['tech_stack'] ?? '');
    $project_url    = trim($_POST['project_url'] ?? '');
    $repository_url = trim($_POST['repository_url'] ?? '');
    $image          = trim($_POST['image'] ?? '');

    if ($post_id) {
        // UPDATE
        $stmt = $pdo->prepare("
            UPDATE projects 
            SET project_name = :project_name, 
                description = :description, 
                tech_stack = :tech_stack, 
                project_url = :project_url, 
                repository_url = :repository_url, 
                image = :image 
            WHERE id = :id
        ");
        $stmt->execute([
            'project_name'   => $project_name,
            'description'    => $description,
            'tech_stack'     => $tech_stack,
            'project_url'    => !empty($project_url) ? $project_url : null,
            'repository_url' => !empty($repository_url) ? $repository_url : null,
            'image'          => !empty($image) ? $image : null,
            'id'             => $post_id
        ]);
    } else {
        // INSERT
        $stmt = $pdo->prepare("
            INSERT INTO projects (project_name, description, tech_stack, project_url, repository_url, image) 
            VALUES (:project_name, :description, :tech_stack, :project_url, :repository_url, :image)
        ");
        $stmt->execute([
            'project_name'   => $project_name,
            'description'    => $description,
            'tech_stack'     => $tech_stack,
            'project_url'    => !empty($project_url) ? $project_url : null,
            'repository_url' => !empty($repository_url) ? $repository_url : null,
            'image'          => !empty($image) ? $image : null
        ]);
    }

    header('Location: admin_projects.php');
    exit;
}

// Logika READ Single Data (Mode Edit)
$edit_data = null;
if ($action === 'edit' && $id > 0) {
    $stmt = $pdo->prepare("SELECT * FROM projects WHERE id = :id LIMIT 1");
    $stmt->execute(['id' => $id]);
    $edit_data = $stmt->fetch();
}

// Logika READ All Data (Untuk Tabel)
$stmt = $pdo->query("SELECT * FROM projects ORDER BY id DESC");
$projects = $stmt->fetchAll();

require_once __DIR__ . '/includes/header.php';
?>

<div class="d-flex align-items-center justify-content-between mb-4">
    <h3 class="fw-bold mb-0">Manajemen Proyek Portofolio</h3>
    <a href="admin.php" class="btn btn-secondary btn-sm">
        <i class="bi bi-arrow-left me-1"></i> Kembali ke Dashboard
    </a>
</div>

<!-- Form Section -->
<div class="card shadow-sm border-0 rounded-3 mb-4">
    <div class="card-header bg-white py-3">
        <h5 class="fw-bold mb-0 text-dark">
            <i class="bi bi-<?php echo $edit_data ? 'pencil-square' : 'plus-circle'; ?> me-2 text-info"></i>
            <?php echo $edit_data ? 'Edit Data Proyek' : 'Tambah Data Proyek'; ?>
        </h5>
    </div>
    <div class="card-body p-4">
        <form action="admin_projects.php" method="POST">
            <?php if ($edit_data): ?>
                <input type="hidden" name="id" value="<?php echo htmlspecialchars($edit_data['id']); ?>">
            <?php endif; ?>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="project_name" class="form-label fw-semibold">Nama Proyek <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="project_name" name="project_name" 
                           value="<?php echo htmlspecialchars($edit_data['project_name'] ?? ''); ?>" required placeholder="Contoh: CV Dinamis Berbasis Web">
                </div>
                <div class="col-md-6 mb-3">
                    <label for="tech_stack" class="form-label fw-semibold">Teknologi yang Digunakan <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="tech_stack" name="tech_stack" 
                           value="<?php echo htmlspecialchars($edit_data['tech_stack'] ?? ''); ?>" required placeholder="Contoh: PHP, MySQL, Bootstrap 5">
                </div>
            </div>

            <div class="mb-3">
                <label for="description" class="form-label fw-semibold">Deskripsi Proyek <span class="text-danger">*</span></label>
                <textarea class="form-control" id="description" name="description" rows="3" required placeholder="Jelaskan tujuan aplikasi, fitur utama, dan solusi yang diberikan"><?php echo htmlspecialchars($edit_data['description'] ?? ''); ?></textarea>
            </div>

            <div class="row">
                <div class="col-md-4 mb-3">
                    <label for="project_url" class="form-label fw-semibold">Demo / Live URL</label>
                    <input type="url" class="form-control" id="project_url" name="project_url" 
                           value="<?php echo htmlspecialchars($edit_data['project_url'] ?? ''); ?>" placeholder="https://example.com/demo">
                </div>
                <div class="col-md-4 mb-3">
                    <label for="repository_url" class="form-label fw-semibold">Repository URL</label>
                    <input type="url" class="form-control" id="repository_url" name="repository_url" 
                           value="<?php echo htmlspecialchars($edit_data['repository_url'] ?? ''); ?>" placeholder="https://github.com/username/project">
                </div>
                <div class="col-md-4 mb-3">
                    <label for="image" class="form-label fw-semibold">URL Cover / Gambar</label>
                    <input type="text" class="form-control" id="image" name="image" 
                           value="<?php echo htmlspecialchars($edit_data['image'] ?? ''); ?>" placeholder="https://... atau assets/img/cover.png">
                </div>
            </div>

            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-info text-dark fw-semibold">
                    <i class="bi bi-save me-1"></i> <?php echo $edit_data ? 'Simpan Perubahan' : 'Tambah Proyek'; ?>
                </button>
                <?php if ($edit_data): ?>
                    <a href="admin_projects.php" class="btn btn-light border">Batal</a>
                <?php endif; ?>
            </div>
        </form>
    </div>
</div>

<!-- Table Section -->
<div class="card shadow-sm border-0 rounded-3 mb-5">
    <div class="card-header bg-white py-3">
        <h5 class="fw-bold mb-0 text-dark">
            <i class="bi bi-folder me-2 text-info"></i>Daftar Proyek Portofolio
        </h5>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-3" style="width: 50px;">No</th>
                        <th>Nama Proyek</th>
                        <th>Teknologi</th>
                        <th>Deskripsi</th>
                        <th>Tautan</th>
                        <th class="text-center" style="width: 150px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($projects)): ?>
                        <tr>
                            <td colspan="6" class="text-center py-4 text-muted">Belum ada data proyek.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($projects as $index => $item): ?>
                            <tr>
                                <td class="ps-3 fw-semibold"><?php echo $index + 1; ?></td>
                                <td>
                                    <div class="fw-bold"><?php echo htmlspecialchars($item['project_name']); ?></div>
                                    <?php if (!empty($item['image'])): ?>
                                        <small class="text-muted"><i class="bi bi-image"></i> Gambar tersedia</small>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <span class="badge bg-light text-dark border"><?php echo htmlspecialchars($item['tech_stack']); ?></span>
                                </td>
                                <td class="small text-muted" style="max-width: 250px;">
                                    <?php echo nl2br(htmlspecialchars($item['description'])); ?>
                                </td>
                                <td>
                                    <div class="d-flex gap-1">
                                        <?php if (!empty($item['project_url'])): ?>
                                            <a href="<?php echo htmlspecialchars($item['project_url']); ?>" target="_blank" class="btn btn-sm btn-outline-secondary" title="Demo">
                                                <i class="bi bi-link-45deg"></i>
                                            </a>
                                        <?php endif; ?>
                                        <?php if (!empty($item['repository_url'])): ?>
                                            <a href="<?php echo htmlspecialchars($item['repository_url']); ?>" target="_blank" class="btn btn-sm btn-outline-dark" title="GitHub">
                                                <i class="bi bi-github"></i>
                                            </a>
                                        <?php endif; ?>
                                    </div>
                                </td>
                                <td class="text-center">
                                    <a href="admin_projects.php?action=edit&id=<?php echo $item['id']; ?>" class="btn btn-sm btn-outline-primary me-1">
                                        <i class="bi bi-pencil"></i> Edit
                                    </a>
                                    <a href="admin_projects.php?action=delete&id=<?php echo $item['id']; ?>" 
                                       class="btn btn-sm btn-outline-danger" 
                                       onclick="return confirm('Yakin ingin menghapus proyek ini?')">
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