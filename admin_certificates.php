<?php
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/config/database.php';

$action = $_GET['action'] ?? '';
$id     = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Logika DELETE
if ($action === 'delete' && $id > 0) {
    $stmt = $pdo->prepare("DELETE FROM certificates WHERE id = :id");
    $stmt->execute(['id' => $id]);
    header('Location: admin_certificates.php');
    exit;
}

// Logika POST (Simpan: Insert / Update)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $post_id          = isset($_POST['id']) && !empty($_POST['id']) ? (int)$_POST['id'] : null;
    $certificate_name = trim($_POST['certificate_name'] ?? '');
    $issuer           = trim($_POST['issuer'] ?? '');
    $issued_date      = trim($_POST['issued_date'] ?? '');
    $credential_url   = trim($_POST['credential_url'] ?? '');
    $certificate_file = trim($_POST['certificate_file'] ?? '');

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
            'certificate_file' => !empty($certificate_file) ? $certificate_file : null,
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
            'certificate_file' => !empty($certificate_file) ? $certificate_file : null
        ]);
    }

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

// Logika READ All Data (Untuk Tabel)
$stmt = $pdo->query("SELECT * FROM certificates ORDER BY issued_date DESC, id DESC");
$certificates = $stmt->fetchAll();

require_once __DIR__ . '/includes/header.php';
?>

<div class="d-flex align-items-center justify-content-between mb-4">
    <h3 class="fw-bold mb-0">Manajemen Sertifikat & Lisensi</h3>
    <a href="admin.php" class="btn btn-secondary btn-sm">
        <i class="bi bi-arrow-left me-1"></i> Kembali ke Dashboard
    </a>
</div>

<!-- Form Section -->
<div class="card shadow-sm border-0 rounded-3 mb-4">
    <div class="card-header bg-white py-3">
        <h5 class="fw-bold mb-0 text-dark">
            <i class="bi bi-<?php echo $edit_data ? 'pencil-square' : 'plus-circle'; ?> me-2 text-success"></i>
            <?php echo $edit_data ? 'Edit Data Sertifikat' : 'Tambah Data Sertifikat'; ?>
        </h5>
    </div>
    <div class="card-body p-4">
        <form action="admin_certificates.php" method="POST">
            <?php if ($edit_data): ?>
                <input type="hidden" name="id" value="<?php echo htmlspecialchars($edit_data['id']); ?>">
            <?php endif; ?>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="certificate_name" class="form-label fw-semibold">Nama Sertifikat / Penghargaan <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="certificate_name" name="certificate_name" 
                           value="<?php echo htmlspecialchars($edit_data['certificate_name'] ?? ''); ?>" required placeholder="Contoh: Web Development Certificate">
                </div>
                <div class="col-md-6 mb-3">
                    <label for="issuer" class="form-label fw-semibold">Penerbit / Penyelenggara <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="issuer" name="issuer" 
                           value="<?php echo htmlspecialchars($edit_data['issuer'] ?? ''); ?>" required placeholder="Contoh: Dicoding / Skilvul / BDI">
                </div>
            </div>

            <div class="row">
                <div class="col-md-4 mb-3">
                    <label for="issued_date" class="form-label fw-semibold">Tanggal Terbit</label>
                    <input type="date" class="form-control" id="issued_date" name="issued_date" 
                           value="<?php echo htmlspecialchars($edit_data['issued_date'] ?? ''); ?>">
                </div>
                <div class="col-md-4 mb-3">
                    <label for="credential_url" class="form-label fw-semibold">URL Kredensial / Verifikasi</label>
                    <input type="url" class="form-control" id="credential_url" name="credential_url" 
                           value="<?php echo htmlspecialchars($edit_data['credential_url'] ?? ''); ?>" placeholder="https://example.com/verify/123">
                </div>
                <div class="col-md-4 mb-3">
                    <label for="certificate_file" class="form-label fw-semibold">URL / Nama File Sertifikat</label>
                    <input type="text" class="form-control" id="certificate_file" name="certificate_file" 
                           value="<?php echo htmlspecialchars($edit_data['certificate_file'] ?? ''); ?>" placeholder="https://... atau assets/certs/cert.pdf">
                </div>
            </div>

            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-success fw-semibold">
                    <i class="bi bi-save me-1"></i> <?php echo $edit_data ? 'Simpan Perubahan' : 'Tambah Sertifikat'; ?>
                </button>
                <?php if ($edit_data): ?>
                    <a href="admin_certificates.php" class="btn btn-light border">Batal</a>
                <?php endif; ?>
            </div>
        </form>
    </div>
</div>

<!-- Table Section -->
<div class="card shadow-sm border-0 rounded-3 mb-5">
    <div class="card-header bg-white py-3">
        <h5 class="fw-bold mb-0 text-dark">
            <i class="bi bi-award me-2 text-success"></i>Daftar Sertifikat
        </h5>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-3" style="width: 50px;">No</th>
                        <th>Nama Sertifikat</th>
                        <th>Penerbit</th>
                        <th>Tanggal Terbit</th>
                        <th>Kredensial</th>
                        <th class="text-center" style="width: 150px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($certificates)): ?>
                        <tr>
                            <td colspan="6" class="text-center py-4 text-muted">Belum ada data sertifikat.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($certificates as $index => $item): ?>
                            <tr>
                                <td class="ps-3 fw-semibold"><?php echo $index + 1; ?></td>
                                <td class="fw-bold"><?php echo htmlspecialchars($item['certificate_name']); ?></td>
                                <td><?php echo htmlspecialchars($item['issuer']); ?></td>
                                <td>
                                    <?php echo !empty($item['issued_date']) ? date('d M Y', strtotime($item['issued_date'])) : '-'; ?>
                                </td>
                                <td>
                                    <div class="d-flex gap-1">
                                        <?php if (!empty($item['credential_url'])): ?>
                                            <a href="<?php echo htmlspecialchars($item['credential_url']); ?>" target="_blank" class="btn btn-sm btn-outline-primary" title="Lihat Kredensial">
                                                <i class="bi bi-patch-check"></i> Link
                                            </a>
                                        <?php endif; ?>
                                        <?php if (!empty($item['certificate_file'])): ?>
                                            <a href="<?php echo htmlspecialchars($item['certificate_file']); ?>" target="_blank" class="btn btn-sm btn-outline-secondary" title="Lihat File">
                                                <i class="bi bi-file-earmark-pdf"></i> File
                                            </a>
                                        <?php endif; ?>
                                    </div>
                                </td>
                                <td class="text-center">
                                    <a href="admin_certificates.php?action=edit&id=<?php echo $item['id']; ?>" class="btn btn-sm btn-outline-primary me-1">
                                        <i class="bi bi-pencil"></i> Edit
                                    </a>
                                    <a href="admin_certificates.php?action=delete&id=<?php echo $item['id']; ?>" 
                                       class="btn btn-sm btn-outline-danger" 
                                       onclick="return confirm('Yakin ingin menghapus sertifikat ini?')">
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