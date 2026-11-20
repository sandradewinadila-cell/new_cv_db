<?php
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/upload_helper.php';

$action = $_GET['action'] ?? '';
$id     = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$error_msg = '';

// Logika DELETE
if ($action === 'delete' && $id > 0) {
    // Ambil data sertifikat untuk menghapus berkas fisik jika ada
    $stmt = $pdo->prepare("SELECT certificate_file FROM certificates WHERE id = :id LIMIT 1");
    $stmt->execute(['id' => $id]);
    $item = $stmt->fetch();

    if ($item && !empty($item['certificate_file'])) {
        $file_to_delete = __DIR__ . '/../' . ltrim($item['certificate_file'], '/');
        if (file_exists($file_to_delete) && is_file($file_to_delete)) {
            @unlink($file_to_delete);
        }
    }

    $stmt = $pdo->prepare("DELETE FROM certificates WHERE id = :id");
    $stmt->execute(['id' => $id]);
    header('Location: admin_certificates.php');
    exit;
}

// Logika READ Single Data (Mode Edit)
$edit_data = null;
if ($action === 'edit' && $id > 0) {
    $stmt = $pdo->prepare("SELECT * FROM certificates WHERE id = :id LIMIT 1");
    $stmt->execute(['id' => $id]);
    $edit_data = $stmt->fetch();
}

// Logika POST (Simpan: Insert / Update)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $post_id          = isset($_POST['id']) && !empty($_POST['id']) ? (int)$_POST['id'] : null;
    $certificate_name = trim($_POST['certificate_name'] ?? '');
    $issuer           = trim($_POST['issuer'] ?? '');
    $issued_date      = trim($_POST['issued_date'] ?? '');
    $credential_url   = trim($_POST['credential_url'] ?? '');
    $certificate_path = $_POST['old_certificate_file'] ?? null;

    if (empty($certificate_name) || empty($issuer)) {
        $error_msg = "Harap lengkapi nama sertifikat dan nama penerbit.";
    } else {
        $has_new_file = isset($_FILES['certificate_file']) && $_FILES['certificate_file']['error'] !== UPLOAD_ERR_NO_FILE;

        // Proses upload jika ada file baru (bersifat opsional)
        if ($has_new_file) {
            $upload_res = uploadImage($_FILES['certificate_file'], 'certificates');
            if ($upload_res['success']) {
                $new_cert_path = 'assets/uploads/certificates/' . $upload_res['filename'];

                // Hapus file lama jika upload file baru sukses
                if ($post_id && !empty($certificate_path)) {
                    $old_file = __DIR__ . '/../' . ltrim($certificate_path, '/');
                    if (file_exists($old_file) && is_file($old_file)) {
                        @unlink($old_file);
                    }
                }

                $certificate_path = $new_cert_path;
            } else {
                $error_msg = $upload_res['error'];
            }
        }

        if (empty($error_msg)) {
            if ($post_id) {
                // UPDATE
                $stmt = $pdo->prepare("
                    UPDATE certificates 
                    SET certificate_name = :certificate_name, 
                        issuer = :issuer, 
                        issued_date = :issued_date, 
                        credential_url = :credential_url, 
                        certificate_file = :certificate_file 
                    WHERE id = :id
                ");
                $stmt->execute([
                    'certificate_name' => $certificate_name,
                    'issuer'           => $issuer,
                    'issued_date'      => !empty($issued_date) ? $issued_date : null,
                    'credential_url'   => !empty($credential_url) ? $credential_url : null,
                    'certificate_file' => $certificate_path,
                    'id'               => $post_id
                ]);
            } else {
                // INSERT
                $stmt = $pdo->prepare("
                    INSERT INTO certificates (certificate_name, issuer, issued_date, credential_url, certificate_file) 
                    VALUES (:certificate_name, :issuer, :issued_date, :credential_url, :certificate_file)
                ");
                $stmt->execute([
                    'certificate_name' => $certificate_name,
                    'issuer'           => $issuer,
                    'issued_date'      => !empty($issued_date) ? $issued_date : null,
                    'credential_url'   => !empty($credential_url) ? $credential_url : null,
                    'certificate_file' => $certificate_path
                ]);
            }

            header('Location: admin_certificates.php');
            exit;
        }
    }
}

// Logika READ All Data (Untuk Tabel)
$stmt = $pdo->query("SELECT * FROM certificates ORDER BY issued_date DESC, id DESC");
$certificates = $stmt->fetchAll();

require_once __DIR__ . '/includes/header.php';
?>

<div class="d-flex align-items-center justify-content-between flex-wrap gap-3 mb-4 fade-in-up">
    <div>
        <span class="section-label">Manajemen Sertifikasi</span>
        <h2 class="gradient-text mb-0">Sertifikat & Lisensi</h2>
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
        <?php echo $edit_data ? 'Edit Data Sertifikat' : 'Tambah Sertifikat Baru'; ?>
    </h3>
    
    <form action="admin_certificates.php<?php echo $edit_data ? '?action=edit&id='.$edit_data['id'] : ''; ?>" method="POST" enctype="multipart/form-data">
        <?php if ($edit_data): ?>
            <input type="hidden" name="id" value="<?php echo htmlspecialchars($edit_data['id']); ?>">
            <input type="hidden" name="old_certificate_file" value="<?php echo htmlspecialchars($edit_data['certificate_file'] ?? ''); ?>">
        <?php endif; ?>

        <div class="row g-4">
            <div class="col-md-6">
                <label for="certificate_name">Nama Sertifikat / Penghargaan <span style="color: var(--accent-secondary);">*</span></label>
                <input type="text" class="form-control" id="certificate_name" name="certificate_name" 
                       value="<?php echo htmlspecialchars($edit_data['certificate_name'] ?? ''); ?>" required placeholder="Contoh: Web Development Certificate">
            </div>

            <div class="col-md-6">
                <label for="issuer">Penerbit / Penyelenggara <span style="color: var(--accent-secondary);">*</span></label>
                <input type="text" class="form-control" id="issuer" name="issuer" 
                       value="<?php echo htmlspecialchars($edit_data['issuer'] ?? ''); ?>" required placeholder="Contoh: Dicoding / Skilvul / BDI">
            </div>

            <div class="col-md-6">
                <label for="issued_date">Tanggal Terbit</label>
                <input type="date" class="form-control" id="issued_date" name="issued_date" 
                       value="<?php echo htmlspecialchars($edit_data['issued_date'] ?? ''); ?>">
            </div>

            <div class="col-md-6">
                <label for="credential_url">URL Kredensial / Verifikasi Online</label>
                <input type="url" class="form-control" id="credential_url" name="credential_url" 
                       value="<?php echo htmlspecialchars($edit_data['credential_url'] ?? ''); ?>" placeholder="https://example.com/verify/123">
            </div>

            <!-- Upload File/Foto Sertifikat (Opsional) -->
            <div class="col-12">
                <label for="certificate_file">
                    Berkas / Gambar Sertifikat <span class="text-muted">(Opsional)</span>
                </label>

                <div class="d-flex align-items-center gap-4 flex-wrap">
                    <?php if (!empty($edit_data['certificate_file'])): ?>
                        <div style="text-align: center;">
                            <?php 
                            $ext = strtolower(pathinfo($edit_data['certificate_file'], PATHINFO_EXTENSION));
                            if (in_array($ext, ['jpg', 'jpeg', 'png', 'webp'])):
                            ?>
                                <img src="<?php echo htmlspecialchars($edit_data['certificate_file']); ?>" alt="Berkas" 
                                     style="width: 100px; height: 70px; object-fit: cover; border-radius: 6px; border: 1px solid var(--border-glass);">
                            <?php else: ?>
                                <a href="<?php echo htmlspecialchars($edit_data['certificate_file']); ?>" target="_blank" class="btn-outline" style="padding: 0.5rem 1rem; font-size: 0.8rem;">
                                    <i class="fa-solid fa-file-pdf me-1"></i> Buka File
                                </a>
                            <?php endif; ?>
                            <div class="small text-muted mt-1" style="font-size: 0.7rem;">Berkas Terpasang</div>
                        </div>
                    <?php endif; ?>

                    <div class="flex-grow-1" style="min-width: 250px;">
                        <input type="file" class="form-control" id="certificate_file" name="certificate_file" accept=".jpg, .jpeg, .png, .webp">
                        <small class="text-muted d-block mt-1">Format: JPG, JPEG, PNG, WEBP. Maksimal ukuran 2MB.</small>
                    </div>
                </div>
            </div>

            <div class="col-12 d-flex gap-3 pt-2">
                <button type="submit" class="btn-primary">
                    <i class="fa-solid fa-floppy-disk me-2"></i> <?php echo $edit_data ? 'Simpan Perubahan' : 'Simpan Sertifikat'; ?>
                </button>
                <?php if ($edit_data): ?>
                    <a href="admin_certificates.php" class="btn-outline">Batal</a>
                <?php endif; ?>
            </div>
        </div>
    </form>
</div>

<!-- KONTAINER TABEL -->
<div class="glass-card fade-in-up" style="margin-top: 2rem; overflow-x: auto;">
    <h3 class="h5 mb-4" style="color: var(--text-primary);">
        <i class="fa-solid fa-award me-2" style="color: var(--accent-secondary);"></i>Daftar Sertifikat & Lisensi
    </h3>
    <table style="width: 100%; border-collapse: collapse; color: var(--text-primary);">
        <thead>
            <tr>
                <th style="text-align: left; padding: 1rem; border-bottom: 1px solid var(--border-glass); color: var(--accent-primary); font-family: var(--font-main); width: 60px;">No</th>
                <th style="text-align: left; padding: 1rem; border-bottom: 1px solid var(--border-glass); color: var(--accent-primary); font-family: var(--font-main);">Nama Sertifikat</th>
                <th style="text-align: left; padding: 1rem; border-bottom: 1px solid var(--border-glass); color: var(--accent-primary); font-family: var(--font-main);">Penerbit</th>
                <th style="text-align: left; padding: 1rem; border-bottom: 1px solid var(--border-glass); color: var(--accent-primary); font-family: var(--font-main);">Tanggal</th>
                <th style="text-align: left; padding: 1rem; border-bottom: 1px solid var(--border-glass); color: var(--accent-primary); font-family: var(--font-main);">Berkas/Kredensial</th>
                <th style="text-align: center; padding: 1rem; border-bottom: 1px solid var(--border-glass); color: var(--accent-primary); font-family: var(--font-main); width: 160px;">Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($certificates)): ?>
                <tr>
                    <td colspan="6" style="padding: 2rem 1rem; text-align: center; color: var(--text-muted);">Belum ada data sertifikat.</td>
                </tr>
            <?php else: ?>
                <?php foreach ($certificates as $index => $item): ?>
                    <tr>
                        <td style="padding: 1rem; border-bottom: 1px solid var(--border-glass); color: var(--text-muted);"><?php echo $index + 1; ?></td>
                        <td style="padding: 1rem; border-bottom: 1px solid var(--border-glass); font-weight: 600;">
                            <?php echo htmlspecialchars($item['certificate_name']); ?>
                        </td>
                        <td style="padding: 1rem; border-bottom: 1px solid var(--border-glass); color: var(--accent-secondary);">
                            <?php echo htmlspecialchars($item['issuer']); ?>
                        </td>
                        <td style="padding: 1rem; border-bottom: 1px solid var(--border-glass); font-size: 0.85rem; color: var(--text-muted);">
                            <?php echo !empty($item['issued_date']) ? date('d M Y', strtotime($item['issued_date'])) : '-'; ?>
                        </td>
                        <td style="padding: 1rem; border-bottom: 1px solid var(--border-glass);">
                            <div class="d-flex gap-2">
                                <?php if (!empty($item['certificate_file'])): ?>
                                    <a href="<?php echo htmlspecialchars($item['certificate_file']); ?>" target="_blank" class="btn-outline" style="padding: 0.25rem 0.5rem; font-size: 0.75rem;" title="Lihat Berkas">
                                        <i class="fa-solid fa-file-image"></i> Berkas
                                    </a>
                                <?php endif; ?>
                                <?php if (!empty($item['credential_url'])): ?>
                                    <a href="<?php echo htmlspecialchars($item['credential_url']); ?>" target="_blank" class="btn-outline" style="padding: 0.25rem 0.5rem; font-size: 0.75rem;" title="Kredensial Online">
                                        <i class="fa-solid fa-arrow-up-right-from-square"></i> Link
                                    </a>
                                <?php endif; ?>
                            </div>
                        </td>
                        <td style="padding: 1rem; border-bottom: 1px solid var(--border-glass); text-align: center;">
                            <div class="d-flex justify-content-center gap-2">
                                <a href="?action=edit&id=<?php echo $item['id']; ?>" class="btn-outline" style="padding: 0.25rem 0.75rem; font-size: 0.8rem;">Edit</a>
                                <a href="?action=delete&id=<?php echo $item['id']; ?>" class="btn-outline" style="padding: 0.25rem 0.75rem; font-size: 0.8rem; border-color: #ef4444; color: #ef4444;" onclick="return confirm('Yakin hapus sertifikat ini?')">Hapus</a>
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