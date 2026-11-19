<?php
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/config/database.php';

$action = $_GET['action'] ?? '';
$id     = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Logika DELETE
if ($action === 'delete' && $id > 0) {
    $stmt = $pdo->prepare("DELETE FROM education WHERE id = :id");
    $stmt->execute(['id' => $id]);
    header('Location: admin_education.php');
    exit;
}

// Logika POST (Simpan: Insert / Update)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $post_id      = isset($_POST['id']) && !empty($_POST['id']) ? (int)$_POST['id'] : null;
    $institution  = trim($_POST['institution'] ?? '');
    $degree_major = trim($_POST['degree_major'] ?? '');
    $start_year   = trim($_POST['start_year'] ?? '');
    $end_year     = trim($_POST['end_year'] ?? '');
    $description  = trim($_POST['description'] ?? '');

    if ($post_id) {
        // UPDATE
        $stmt = $pdo->prepare("
            UPDATE education 
            SET institution = :institution, 
                degree_major = :degree_major, 
                start_year = :start_year, 
                end_year = :end_year, 
                description = :description 
            WHERE id = :id
        ");
        $stmt->execute([
            'institution'  => $institution,
            'degree_major' => $degree_major,
            'start_year'   => $start_year,
            'end_year'     => $end_year,
            'description'  => $description,
            'id'           => $post_id
        ]);
    } else {
        // INSERT
        $stmt = $pdo->prepare("
            INSERT INTO education (institution, degree_major, start_year, end_year, description) 
            VALUES (:institution, :degree_major, :start_year, :end_year, :description)
        ");
        $stmt->execute([
            'institution'  => $institution,
            'degree_major' => $degree_major,
            'start_year'   => $start_year,
            'end_year'     => $end_year,
            'description'  => $description
        ]);
    }

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

// Logika READ All Data (Untuk Tabel)
$stmt = $pdo->query("SELECT * FROM education ORDER BY start_year DESC, id DESC");
$educations = $stmt->fetchAll();

require_once __DIR__ . '/includes/header.php';
?>

<div class="d-flex align-items-center justify-content-between mb-4">
    <h3 class="fw-bold mb-0">Manajemen Pendidikan</h3>
    <a href="admin.php" class="btn btn-secondary btn-sm">
        <i class="bi bi-arrow-left me-1"></i> Kembali ke Dashboard
    </a>
</div>

<!-- Form Section -->
<div class="card shadow-sm border-0 rounded-3 mb-4">
    <div class="card-header bg-white py-3">
        <h5 class="fw-bold mb-0 text-primary">
            <i class="bi bi-<?php echo $edit_data ? 'pencil-square' : 'plus-circle'; ?> me-2"></i>
            <?php echo $edit_data ? 'Edit Data Pendidikan' : 'Tambah Data Pendidikan'; ?>
        </h5>
    </div>
    <div class="card-body p-4">
        <form action="admin_education.php" method="POST">
            <?php if ($edit_data): ?>
                <input type="hidden" name="id" value="<?php echo htmlspecialchars($edit_data['id']); ?>">
            <?php endif; ?>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="institution" class="form-label fw-semibold">Nama Institusi / Sekolah <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="institution" name="institution" 
                           value="<?php echo htmlspecialchars($edit_data['institution'] ?? ''); ?>" required placeholder="Contoh: SMK Negeri 1 Dlanggu">
                </div>
                <div class="col-md-6 mb-3">
                    <label for="degree_major" class="form-label fw-semibold">Jurusan / Program Studi <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="degree_major" name="degree_major" 
                           value="<?php echo htmlspecialchars($edit_data['degree_major'] ?? ''); ?>" required placeholder="Contoh: Rekayasa Perangkat Lunak">
                </div>
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="start_year" class="form-label fw-semibold">Tahun Mulai <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="start_year" name="start_year" 
                           value="<?php echo htmlspecialchars($edit_data['start_year'] ?? ''); ?>" required placeholder="Contoh: 2024">
                </div>
                <div class="col-md-6 mb-3">
                    <label for="end_year" class="form-label fw-semibold">Tahun Selesai <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="end_year" name="end_year" 
                           value="<?php echo htmlspecialchars($edit_data['end_year'] ?? ''); ?>" required placeholder="Contoh: 2027 atau Sekarang">
                </div>
            </div>

            <div class="mb-3">
                <label for="description" class="form-label fw-semibold">Keterangan / Aktivitas</label>
                <textarea class="form-control" id="description" name="description" rows="3" placeholder="Informasi fokus keahlian atau pencapaian"><?php echo htmlspecialchars($edit_data['description'] ?? ''); ?></textarea>
            </div>

            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-save me-1"></i> <?php echo $edit_data ? 'Simpan Perubahan' : 'Tambah Data'; ?>
                </button>
                <?php if ($edit_data): ?>
                    <a href="admin_education.php" class="btn btn-light border">Batal</a>
                <?php endif; ?>
            </div>
        </form>
    </div>
</div>

<!-- Table Section -->
<div class="card shadow-sm border-0 rounded-3 mb-5">
    <div class="card-header bg-white py-3">
        <h5 class="fw-bold mb-0 text-dark">
            <i class="bi bi-mortarboard me-2"></i>Daftar Riwayat Pendidikan
        </h5>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-3" style="width: 50px;">No</th>
                        <th>Institusi</th>
                        <th>Jurusan</th>
                        <th>Periode</th>
                        <th>Keterangan</th>
                        <th class="text-center" style="width: 150px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($educations)): ?>
                        <tr>
                            <td colspan="6" class="text-center py-4 text-muted">Belum ada data pendidikan.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($educations as $index => $item): ?>
                            <tr>
                                <td class="ps-3 fw-semibold"><?php echo $index + 1; ?></td>
                                <td class="fw-bold"><?php echo htmlspecialchars($item['institution']); ?></td>
                                <td><?php echo htmlspecialchars($item['degree_major']); ?></td>
                                <td><span class="badge bg-secondary"><?php echo htmlspecialchars($item['start_year'] . ' - ' . $item['end_year']); ?></span></td>
                                <td class="small text-muted"><?php echo nl2br(htmlspecialchars($item['description'] ?? '-')); ?></td>
                                <td class="text-center">
                                    <a href="admin_education.php?action=edit&id=<?php echo $item['id']; ?>" class="btn btn-sm btn-outline-primary me-1">
                                        <i class="bi bi-pencil"></i> Edit
                                    </a>
                                    <a href="admin_education.php?action=delete&id=<?php echo $item['id']; ?>" 
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