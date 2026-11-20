<?php
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/upload_helper.php';

$action = $_GET['action'] ?? '';
$id     = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$error_msg = '';

// Logika DELETE
if ($action === 'delete' && $id > 0) {
    // Ambil data proyek untuk menghapus berkas foto
    $stmt = $pdo->prepare("SELECT image FROM projects WHERE id = :id LIMIT 1");
    $stmt->execute(['id' => $id]);
    $item = $stmt->fetch();

    if ($item && !empty($item['image'])) {
        $file_to_delete = __DIR__ . '/../' . ltrim($item['image'], '/');
        if (file_exists($file_to_delete) && is_file($file_to_delete)) {
            @unlink($file_to_delete);
        }
    }

    $stmt = $pdo->prepare("DELETE FROM projects WHERE id = :id");
    $stmt->execute(['id' => $id]);
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

// Logika POST (Simpan: Insert / Update)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $post_id        = isset($_POST['id']) && !empty($_POST['id']) ? (int)$_POST['id'] : null;
    $project_name   = trim($_POST['project_name'] ?? '');
    $description    = trim($_POST['description'] ?? '');
    $tech_stack     = trim($_POST['tech_stack'] ?? '');
    $project_url    = trim($_POST['project_url'] ?? '');
    $repository_url = trim($_POST['repository_url'] ?? '');
    $image_path     = $_POST['old_image'] ?? null;

    if (empty($project_name) || empty($description) || empty($tech_stack)) {
        $error_msg = "Harap lengkapi semua bidang yang wajib diisi.";
    } else {
        $has_new_image = isset($_FILES['image']) && $_FILES['image']['error'] !== UPLOAD_ERR_NO_FILE;

        // Validasi wajib upload foto proyek untuk data baru
        if (!$post_id && !$has_new_image) {
            $error_msg = "Foto/cover proyek wajib diunggah untuk data baru.";
        }

        // Proses upload foto baru jika ada
        if (empty($error_msg) && $has_new_image) {
            $upload_res = uploadImage($_FILES['image'], 'projects');
            if ($upload_res['success']) {
                $new_image_path = 'assets/uploads/projects/' . $upload_res['filename'];

                // Hapus file lama hanya jika upload baru berhasil
                if ($post_id && !empty($image_path)) {
                    $old_file = __DIR__ . '/../' . ltrim($image_path, '/');
                    if (file_exists($old_file) && is_file($old_file)) {
                        @unlink($old_file);
                    }
                }

                $image_path = $new_image_path;
            } else {
                $error_msg = $upload_res['error'];
            }
        }

        if (empty($error_msg)) {
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
                    'image'          => $image_path,
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
                    'image'          => $image_path
                ]);
            }

            header('Location: admin_projects.php');
            exit;
        }
    }
}

// Logika READ All Data (Untuk Tabel)
$stmt = $pdo->query("SELECT * FROM projects ORDER BY id DESC");
$projects = $stmt->fetchAll();

require_once __DIR__ . '/includes/header.php';
?>

<div class="d-flex align-items-center justify-content-between flex-wrap gap-3 mb-4 fade-in-up">
    <div>
        <span class="section-label">Manajemen Portofolio</span>
        <h2 class="gradient-text mb-0">Proyek Portofolio</h2>
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
    <h3 class="h5 mb-4" style="color: var(--accent-secondary);">
        <i class="fa-solid fa-<?php echo $edit_data ? 'pen-to-square' : 'circle-plus'; ?> me-2"></i>
        <?php echo $edit_data ? 'Edit Data Proyek' : 'Tambah Proyek Baru'; ?>
    </h3>
    
    <form action="admin_projects.php<?php echo $edit_data ? '?action=edit&id='.$edit_data['id'] : ''; ?>" method="POST" enctype="multipart/form-data">
        <?php if ($edit_data): ?>
            <input type="hidden" name="id" value="<?php echo htmlspecialchars($edit_data['id']); ?>">
            <input type="hidden" name="old_image" value="<?php echo htmlspecialchars($edit_data['image'] ?? ''); ?>">
        <?php endif; ?>

        <div class="row g-4">
            <div class="col-md-6">
                <label for="project_name">Nama Proyek <span style="color: var(--accent-secondary);">*</span></label>
                <input type="text" class="form-control" id="project_name" name="project_name" 
                       value="<?php echo htmlspecialchars($edit_data['project_name'] ?? ''); ?>" required placeholder="Contoh: CV Dinamis Berbasis Web">
            </div>

            <div class="col-md-6">
                <label for="tech_stack">Teknologi yang Digunakan (Pisahkan Koma) <span style="color: var(--accent-secondary);">*</span></label>
                <input type="text" class="form-control" id="tech_stack" name="tech_stack" 
                       value="<?php echo htmlspecialchars($edit_data['tech_stack'] ?? ''); ?>" required placeholder="Contoh: PHP, MySQL, Bootstrap 5">
            </div>

            <!-- Upload Foto / Cover Proyek (Wajib) -->
            <div class="col-12">
                <label for="image">
                    Foto / Cover Proyek 
                    <?php if (empty($edit_data['image'])): ?>
                        <span style="color: var(--accent-secondary);">* (Wajib Diunggah)</span>
                    <?php else: ?>
                        <span class="text-muted">(Pilih file baru untuk mengganti foto)</span>
                    <?php endif; ?>
                </label>

                <div class="d-flex align-items-center gap-4 flex-wrap">
                    <?php if (!empty($edit_data['image'])): ?>
                        <div style="text-align: center;">
                            <img src="<?php echo htmlspecialchars($edit_data['image']); ?>" alt="Cover Proyek" 
                                 style="width: 150px; height: 95px; object-fit: cover; border-radius: 8px; border: 1px solid var(--border-glass); box-shadow: var(--shadow-glow);">
                            <div class="small text-muted mt-1" style="font-size: 0.75rem;">Foto Terpasang</div>
                        </div>
                    <?php endif; ?>

                    <div class="flex-grow-1" style="min-width: 250px;">
                        <input type="file" class="form-control" id="image" name="image" accept=".jpg, .jpeg, .png, .webp" 
                               <?php echo empty($edit_data['image']) ? 'required' : ''; ?>>
                        <small class="text-muted d-block mt-1">Format: JPG, JPEG, PNG, WEBP. Maksimal ukuran 2MB.</small>
                    </div>
                </div>
            </div>

            <div class="col-12">
                <label for="description">Deskripsi Proyek <span style="color: var(--accent-secondary);">*</span></label>
                <textarea class="form-control" id="description" name="description" rows="3" required placeholder="Jelaskan fungsi utama, fitur unggulan, dan dampak solusi dari proyek ini..."><?php echo htmlspecialchars($edit_data['description'] ?? ''); ?></textarea>
            </div>

            <div class="col-md-6">
                <label for="project_url">Demo / Live URL</label>
                <input type="url" class="form-control" id="project_url" name="project_url" 
                       value="<?php echo htmlspecialchars($edit_data['project_url'] ?? ''); ?>" placeholder="https://demo.example.com">
            </div>

            <div class="col-md-6">
                <label for="repository_url">Repository URL</label>
                <input type="url" class="form-control" id="repository_url" name="repository_url" 
                       value="<?php echo htmlspecialchars($edit_data['repository_url'] ?? ''); ?>" placeholder="https://github.com/username/repo">
            </div>

            <div class="col-12 d-flex gap-3 pt-2">
                <button type="submit" class="btn-primary">
                    <i class="fa-solid fa-floppy-disk me-2"></i> <?php echo $edit_data ? 'Simpan Perubahan' : 'Simpan Proyek'; ?>
                </button>
                <?php if ($edit_data): ?>
                    <a href="admin_projects.php" class="btn-outline">Batal</a>
                <?php endif; ?>
            </div>
        </div>
    </form>
</div>

<!-- KONTAINER TABEL -->
<div class="glass-card fade-in-up" style="margin-top: 2rem; overflow-x: auto;">
    <h3 class="h5 mb-4" style="color: var(--text-primary);">
        <i class="fa-solid fa-folder-open me-2" style="color: var(--accent-primary);"></i>Daftar Proyek Portofolio
    </h3>
    <table style="width: 100%; border-collapse: collapse; color: var(--text-primary);">
        <thead>
            <tr>
                <th style="text-align: left; padding: 1rem; border-bottom: 1px solid var(--border-glass); color: var(--accent-primary); font-family: var(--font-main); width: 60px;">No</th>
                <th style="text-align: left; padding: 1rem; border-bottom: 1px solid var(--border-glass); color: var(--accent-primary); font-family: var(--font-main); width: 120px;">Cover</th>
                <th style="text-align: left; padding: 1rem; border-bottom: 1px solid var(--border-glass); color: var(--accent-primary); font-family: var(--font-main);">Nama Proyek</th>
                <th style="text-align: left; padding: 1rem; border-bottom: 1px solid var(--border-glass); color: var(--accent-primary); font-family: var(--font-main);">Teknologi</th>
                <th style="text-align: left; padding: 1rem; border-bottom: 1px solid var(--border-glass); color: var(--accent-primary); font-family: var(--font-main);">Deskripsi</th>
                <th style="text-align: left; padding: 1rem; border-bottom: 1px solid var(--border-glass); color: var(--accent-primary); font-family: var(--font-main);">Tautan</th>
                <th style="text-align: center; padding: 1rem; border-bottom: 1px solid var(--border-glass); color: var(--accent-primary); font-family: var(--font-main); width: 160px;">Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($projects)): ?>
                <tr>
                    <td colspan="7" style="padding: 2rem 1rem; text-align: center; color: var(--text-muted);">Belum ada portofolio proyek.</td>
                </tr>
            <?php else: ?>
                <?php foreach ($projects as $index => $item): ?>
                    <tr>
                        <td style="padding: 1rem; border-bottom: 1px solid var(--border-glass); color: var(--text-muted);"><?php echo $index + 1; ?></td>
                        <td style="padding: 1rem; border-bottom: 1px solid var(--border-glass);">
                            <?php if (!empty($item['image'])): ?>
                                <img src="<?php echo htmlspecialchars($item['image']); ?>" alt="Cover" style="width: 80px; height: 50px; object-fit: cover; border-radius: 6px; border: 1px solid var(--border-glass);">
                            <?php else: ?>
                                <span class="text-muted" style="font-size: 0.8rem;">-</span>
                            <?php endif; ?>
                        </td>
                        <td style="padding: 1rem; border-bottom: 1px solid var(--border-glass); font-weight: 600;">
                            <?php echo htmlspecialchars($item['project_name']); ?>
                        </td>
                        <td style="padding: 1rem; border-bottom: 1px solid var(--border-glass);">
                            <div class="d-flex flex-wrap gap-1">
                                <?php 
                                $tech_items = explode(',', $item['tech_stack']);
                                foreach ($tech_items as $t): 
                                    $t = trim($t);
                                    if(!empty($t)):
                                ?>
                                    <span class="badge-glass" style="font-size: 0.7rem;"><?php echo htmlspecialchars($t); ?></span>
                                <?php 
                                    endif;
                                endforeach; 
                                ?>
                            </div>
                        </td>
                        <td style="padding: 1rem; border-bottom: 1px solid var(--border-glass); font-size: 0.9rem; color: var(--text-secondary); max-width: 250px;">
                            <?php echo nl2br(htmlspecialchars($item['description'])); ?>
                        </td>
                        <td style="padding: 1rem; border-bottom: 1px solid var(--border-glass);">
                            <div class="d-flex gap-2">
                                <?php if (!empty($item['project_url'])): ?>
                                    <a href="<?php echo htmlspecialchars($item['project_url']); ?>" target="_blank" class="btn-outline" style="padding: 0.25rem 0.5rem; font-size: 0.75rem;" title="Live Preview">
                                        <i class="fa-solid fa-arrow-up-right-from-square"></i>
                                    </a>
                                <?php endif; ?>
                                <?php if (!empty($item['repository_url'])): ?>
                                    <a href="<?php echo htmlspecialchars($item['repository_url']); ?>" target="_blank" class="btn-outline" style="padding: 0.25rem 0.5rem; font-size: 0.75rem;" title="GitHub">
                                        <i class="fa-brands fa-github"></i>
                                    </a>
                                <?php endif; ?>
                            </div>
                        </td>
                        <td style="padding: 1rem; border-bottom: 1px solid var(--border-glass); text-align: center;">
                            <div class="d-flex justify-content-center gap-2">
                                <a href="?action=edit&id=<?php echo $item['id']; ?>" class="btn-outline" style="padding: 0.25rem 0.75rem; font-size: 0.8rem;">Edit</a>
                                <a href="?action=delete&id=<?php echo $item['id']; ?>" class="btn-outline" style="padding: 0.25rem 0.75rem; font-size: 0.8rem; border-color: #ef4444; color: #ef4444;" onclick="return confirm('Yakin hapus proyek ini?')">Hapus</a>
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