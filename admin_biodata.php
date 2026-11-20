<?php
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/upload_helper.php';

$success_msg = '';
$error_msg   = '';

// Logika GET Data Saat Ini
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
            'summary'      => '',
            'avatar'       => ''
        ];
    }
} catch (PDOException $e) {
    $error_msg = "Gagal mengambil data dari database: " . $e->getMessage();
    $profile = [];
}

// Logika POST: Update Data
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $full_name    = trim($_POST['full_name'] ?? '');
    $email        = trim($_POST['email'] ?? '');
    $phone        = trim($_POST['phone'] ?? '');
    $address      = trim($_POST['address'] ?? '');
    $linkedin_url = trim($_POST['linkedin_url'] ?? '');
    $github_url   = trim($_POST['github_url'] ?? '');
    $summary      = trim($_POST['summary'] ?? '');
    $avatar_path  = $profile['avatar'] ?? null;

    if (empty($full_name) || empty($email) || empty($phone) || empty($address) || empty($summary)) {
        $error_msg = "Harap lengkapi semua bidang yang wajib diisi.";
    } else {
        // Cek jika ada file avatar baru yang diupload
        if (isset($_FILES['avatar']) && $_FILES['avatar']['error'] !== UPLOAD_ERR_NO_FILE) {
            $upload_res = uploadImage($_FILES['avatar'], 'avatars');
            if ($upload_res['success']) {
                $avatar_path = 'assets/uploads/avatars/' . $upload_res['filename'];
            } else {
                $error_msg = $upload_res['error'];
            }
        }

        if (empty($error_msg)) {
            try {
                $stmt = $pdo->prepare("
                    UPDATE personal_info 
                    SET full_name = :full_name,
                        email = :email,
                        phone = :phone,
                        address = :address,
                        linkedin_url = :linkedin_url,
                        github_url = :github_url,
                        summary = :summary,
                        avatar = :avatar
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
                    'avatar'       => $avatar_path,
                ]);

                $profile['avatar'] = $avatar_path;
                $profile['full_name'] = $full_name;
                $profile['email'] = $email;
                $profile['phone'] = $phone;
                $profile['address'] = $address;
                $profile['linkedin_url'] = $linkedin_url;
                $profile['github_url'] = $github_url;
                $profile['summary'] = $summary;

                $success_msg = "Data biodata dan foto profil berhasil diperbarui!";
            } catch (PDOException $e) {
                $error_msg = "Gagal memperbarui data: " . $e->getMessage();
            }
        }
    }
}

require_once __DIR__ . '/includes/header.php';
?>

<div class="d-flex align-items-center justify-content-between flex-wrap gap-3 mb-4 fade-in-up">
    <div>
        <span class="section-label">Manajemen Profil</span>
        <h2 class="gradient-text mb-0">Biodata & Kontak</h2>
    </div>
    <!-- HANYA SATU tombol kembali -->
    <a href="admin.php" class="btn-outline" style="padding: 0.6rem 1.25rem; font-size: 0.85rem;">
        <i class="fa-solid fa-arrow-left me-2"></i> Kembali ke Dashboard
    </a>
</div>

<?php if (!empty($success_msg)): ?>
    <div style="background: rgba(34, 197, 94, 0.15); border: 1px solid #22c55e; color: #86efac; padding: 1rem; border-radius: 8px; margin-bottom: 1.5rem;" class="fade-in-up">
        <i class="fa-solid fa-circle-check me-2"></i> <?php echo htmlspecialchars($success_msg); ?>
    </div>
<?php endif; ?>

<?php if (!empty($error_msg)): ?>
    <div style="background: rgba(239, 68, 68, 0.15); border: 1px solid #ef4444; color: #fca5a5; padding: 1rem; border-radius: 8px; margin-bottom: 1.5rem;" class="fade-in-up">
        <i class="fa-solid fa-circle-exclamation me-2"></i> <?php echo htmlspecialchars($error_msg); ?>
    </div>
<?php endif; ?>

<div class="glass-card fade-in-up">
    <form action="admin_biodata.php" method="POST" enctype="multipart/form-data">
        <div class="row g-4">
            <!-- Upload Foto Profil / Avatar -->
            <div class="col-12">
                <label>Foto Profil (Avatar)</label>
                <div class="d-flex align-items-center gap-4 flex-wrap">
                    <?php if (!empty($profile['avatar'])): ?>
                        <div style="text-align: center;">
                            <img src="<?php echo htmlspecialchars($profile['avatar']); ?>" alt="Foto Profil" 
                                 style="width: 100px; height: 100px; border-radius: 50%; object-fit: cover; border: 2px solid var(--accent-primary); box-shadow: var(--shadow-glow);">
                            <div class="small text-muted mt-1" style="font-size: 0.75rem;">Foto Saat Ini</div>
                        </div>
                    <?php else: ?>
                        <div style="width: 100px; height: 100px; border-radius: 50%; background: rgba(255, 255, 255, 0.05); border: 2px dashed var(--border-glass); display: flex; align-items: center; justify-content: center; color: var(--text-muted); font-size: 2rem;">
                            <i class="fa-solid fa-user"></i>
                        </div>
                    <?php endif; ?>
                    
                    <div class="flex-grow-1" style="min-width: 250px;">
                        <input type="file" class="form-control" id="avatar" name="avatar" accept=".jpg, .jpeg, .png, .webp">
                        <small class="text-muted d-block mt-1">Format: JPG, JPEG, PNG, WEBP. Maksimal ukuran 2MB.</small>
                    </div>
                </div>
            </div>

            <div class="col-12">
                <label for="full_name">Nama Lengkap <span style="color: var(--accent-secondary);">*</span></label>
                <input type="text" class="form-control" id="full_name" name="full_name" 
                       value="<?php echo htmlspecialchars($profile['full_name'] ?? ''); ?>" required placeholder="Nama lengkap Anda">
            </div>

            <div class="col-md-6">
                <label for="email">Alamat Email <span style="color: var(--accent-secondary);">*</span></label>
                <input type="email" class="form-control" id="email" name="email" 
                       value="<?php echo htmlspecialchars($profile['email'] ?? ''); ?>" required placeholder="nama@email.com">
            </div>

            <div class="col-md-6">
                <label for="phone">Nomor Telepon / WhatsApp <span style="color: var(--accent-secondary);">*</span></label>
                <input type="text" class="form-control" id="phone" name="phone" 
                       value="<?php echo htmlspecialchars($profile['phone'] ?? ''); ?>" required placeholder="+62 812xxxx">
            </div>

            <div class="col-12">
                <label for="address">Kota Domisili / Alamat <span style="color: var(--accent-secondary);">*</span></label>
                <input type="text" class="form-control" id="address" name="address" 
                       value="<?php echo htmlspecialchars($profile['address'] ?? ''); ?>" required placeholder="Contoh: Mojokerto, Jawa Timur">
            </div>

            <div class="col-md-6">
                <label for="linkedin_url">Tautan Profil LinkedIn</label>
                <input type="url" class="form-control" id="linkedin_url" name="linkedin_url" 
                       value="<?php echo htmlspecialchars($profile['linkedin_url'] ?? ''); ?>" placeholder="https://linkedin.com/in/username">
            </div>

            <div class="col-md-6">
                <label for="github_url">Tautan Profil GitHub</label>
                <input type="url" class="form-control" id="github_url" name="github_url" 
                       value="<?php echo htmlspecialchars($profile['github_url'] ?? ''); ?>" placeholder="https://github.com/username">
            </div>

            <div class="col-12">
                <label for="summary">Ringkasan Profil (Summary) <span style="color: var(--accent-secondary);">*</span></label>
                <textarea class="form-control" id="summary" name="summary" rows="5" required placeholder="Deskripsikan latar belakang, minat kejuruan, dan tujuan karir Anda..."><?php echo htmlspecialchars($profile['summary'] ?? ''); ?></textarea>
            </div>

            <div class="col-12 pt-2">
                <button type="submit" class="btn-primary">
                    <i class="fa-solid fa-floppy-disk me-2"></i> Simpan Perubahan
                </button>
            </div>
        </div>
    </form>
</div>

<?php
require_once __DIR__ . '/includes/footer.php';
?>