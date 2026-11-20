<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true) {
    header('Location: admin.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Administrator - CV Dinamis</title>
    <!-- Google Fonts & Theme CSS -->
    <link rel="stylesheet" href="assets/css/theme.css">
    <!-- FontAwesome CDN -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Bootstrap 5 CSS CDN (hanya utility & grid) -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .login-wrapper {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1.5rem;
        }
        .login-card {
            width: 100%;
            max-width: 450px;
            padding: 3rem 2.5rem;
        }
        .password-toggle-wrapper {
            position: relative;
        }
        .password-toggle-btn {
            position: absolute;
            right: 14px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            color: var(--text-muted);
            cursor: pointer;
            padding: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: var(--transition);
        }
        .password-toggle-btn:hover {
            color: var(--accent-primary);
        }
        .alert-glass-danger {
            background: rgba(239, 68, 68, 0.15);
            border: 1px solid rgba(239, 68, 68, 0.3);
            color: #fca5a5;
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            border-radius: var(--radius-sm);
        }
    </style>
</head>
<body>

<div class="login-wrapper">
    <div class="glass-card login-card fade-in-up">
        <div class="text-center mb-4">
            <span class="section-label">Otentikasi Sistem</span>
            <h2 class="gradient-text fw-bold mb-1">Admin Panel</h2>
            <p class="small text-muted mb-0">Masuk untuk mengelola data CV dinamis</p>
        </div>

        <?php if (isset($_GET['error']) && $_GET['error'] === '1'): ?>
            <div class="alert alert-glass-danger alert-dismissible fade show small d-flex align-items-center mb-4" role="alert">
                <i class="fa-solid fa-circle-exclamation me-2"></i>
                <div>Username atau password salah!</div>
                <button type="button" class="btn-close btn-close-white ms-auto" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <form action="process_login.php" method="POST">
            <div class="mb-3">
                <label for="username">Username</label>
                <div class="position-relative">
                    <input type="text" class="form-control" id="username" name="username" placeholder="Masukkan username" required autofocus autocomplete="username">
                </div>
            </div>

            <div class="mb-4">
                <label for="password">Password</label>
                <div class="password-toggle-wrapper">
                    <input type="password" class="form-control" id="password" name="password" placeholder="Masukkan password" required autocomplete="current-password" style="padding-right: 42px;">
                    <button type="button" class="password-toggle-btn" id="btn-toggle-password" title="Lihat/Sembunyikan password">
                        <i class="fa-regular fa-eye" id="toggle-icon"></i>
                    </button>
                </div>
            </div>

            <button type="submit" class="btn-primary w-100 py-3 mb-3">
                <i class="fa-solid fa-arrow-right-to-bracket me-2"></i> Masuk Sekarang
            </button>
        </form>

        <div class="text-center mt-3">
            <a href="index.php" class="text-decoration-none small" style="color: var(--text-secondary); transition: var(--transition);">
                <i class="fa-solid fa-arrow-left me-1"></i> Kembali ke Halaman CV
            </a>
        </div>
    </div>
</div>

<!-- Bootstrap 5 JS CDN -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<!-- Vanilla JS Toggle Password -->
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const toggleBtn = document.getElementById('btn-toggle-password');
        const passwordInput = document.getElementById('password');
        const toggleIcon = document.getElementById('toggle-icon');

        if (toggleBtn && passwordInput && toggleIcon) {
            toggleBtn.addEventListener('click', function () {
                if (passwordInput.type === 'password') {
                    passwordInput.type = 'text';
                    toggleIcon.classList.remove('fa-eye');
                    toggleIcon.classList.add('fa-eye-slash');
                } else {
                    passwordInput.type = 'password';
                    toggleIcon.classList.remove('fa-eye-slash');
                    toggleIcon.classList.add('fa-eye');
                }
            });
        }
    });
</script>
</body>
</html>