<?php
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/config/database.php';

$success_msg = '';
$error_msg   = '';

// Logika POST: Update Data
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $full_name    = trim($_POST['full_name'] ?? '');
    $email        = trim($_POST['email'] ?? '');
    $phone        = trim($_POST['phone'] ?? '');
    $address      = trim($_POST['address'] ?? '');
    $linkedin_url = trim($_POST['linkedin_url'] ?? '');
    $github_url   = trim($_POST['github_url'] ?? '');
    $summary      = trim($_POST['summary'] ?? '');

    if (empty($full_name) || empty($email) || empty($phone) || empty($address) || empty($summary)) {
        $error_msg = "Harap lengkapi semua bidang yang wajib diisi.";
    } else {
        try {
            $stmt = $pdo->prepare("
                UPDATE personal_info 
                SET full_name = :full_name,
                    email = :email,
                    phone = :phone,
                    address = :address,
                    linkedin_url = :linkedin_url,
                    github_url = :github_url,
                    summary = :summary
                WHERE id = 1
            ");

            $stmt->execute([
                'full_name'    => $full_name,
                'email'        => $email,
                'phone'        => $phone,
                'address'      => $address,
                'linkedin_url' => !empty($linkedin_url) ? $linkedin_url : null,
                'github_url'   => !empty($github_url) ? $github_url : null,
                'summary'      => $summary,
            ]);

            $success_msg = "Data berhasil diperbarui!";
        } catch (PDOException $e) {
            $error_msg = "Gagal memperbarui data: " . $e->getMessage();
        }
    }
}

// Logika GET: Read Data
try {
    $stmt = $pdo->query("SELECT * FROM personal_info WHERE id = 1 LIMIT 1");
    $profile = $stmt->fetch();

    if (!$profile) {
        $profile = [
            'full_name'    => '',
            'email'        => '',
            'phone'        => '',
            'address'      => '',
            'linkedin_url' => '',
            'github_url'   => '',
            'summary'      => ''
        ];
    }
} catch (PDOException $e) {
    $error_msg = "Gagal mengambil data dari database: " . $e->getMessage();
    $profile = [];
}

require_once __DIR__ . '/includes/header.php';
?>

<div class="row justify-content-center">
    <div class="col-12 col-lg-8">
        <div class="d-flex align-items-center justify-content-between mb-4">
            <h3 class="fw-bold mb-0">Edit Biodata & Kontak</h3>
            <a href="admin.php" class="btn btn-secondary btn-sm">
                <i class="bi bi-arrow-left me-1"></i> Kembali ke Dashboard
            </a>
        </div>

        <?php if (!empty($success_msg)): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="bi bi-check-circle-fill me-2"></i> <?php echo htmlspecialchars($success_msg); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <?php if (!empty($error_msg)): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="bi bi-exclamation-triangle-fill me-2"></i> <?php echo htmlspecialchars($error_msg); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <div class="card shadow-sm border-0 rounded-3 mb-5">
            <div class="card-body p-4">
                <form action="admin_biodata.php" method="POST">
                    <div class="mb-3">
                        <label for="full_name" class="form-label fw-semibold">Nama Lengkap <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="full_name" name="full_name" 
                               value="<?php echo htmlspecialchars($profile['full_name'] ?? ''); ?>" required>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="email" class="form-label fw-semibold">Email <span class="text-danger">*</span></label>
                            <input type="email" class="form-control" id="email" name="email" 
                                   value="<?php echo htmlspecialchars($profile['email'] ?? ''); ?>" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="phone" class="form-label fw-semibold">Nomor Telepon / WhatsApp <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="phone" name="phone" 
                                   value="<?php echo htmlspecialchars($profile['phone'] ?? ''); ?>" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="address" class="form-label fw-semibold">Kota / Alamat <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="address" name="address" 
                               value="<?php echo htmlspecialchars($profile['address'] ?? ''); ?>" required>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="linkedin_url" class="form-label fw-semibold">LinkedIn URL</label>
                            <input type="url" class="form-control" id="linkedin_url" name="linkedin_url" 
                                   placeholder="https://linkedin.com/in/username"
                                   value="<?php echo htmlspecialchars($profile['linkedin_url'] ?? ''); ?>">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="github_url" class="form-label fw-semibold">GitHub URL</label>
                            <input type="url" class="form-control" id="github_url" name="github_url" 
                                   placeholder="https://github.com/username"
                                   value="<?php echo htmlspecialchars($profile['github_url'] ?? ''); ?>">
                        </div>
                    </div>

                    <div class="mb-4">
                        <label for="summary" class="form-label fw-semibold">Ringkasan Profil (Summary) <span class="text-danger">*</span></label>
                        <textarea class="form-control" id="summary" name="summary" rows="5" required><?php echo htmlspecialchars($profile['summary'] ?? ''); ?></textarea>
                        <div class="form-text">Tuliskan deskripsi singkat mengenai latar belakang, minat kejuruan, dan tujuan karir Anda.</div>
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary px-4">
                            <i class="bi bi-save me-1"></i> Simpan Perubahan
                        </button>
                        <a href="admin.php" class="btn btn-secondary px-4">
                            Kembali ke Dashboard
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php
require_once __DIR__ . '/includes/footer.php';
?>