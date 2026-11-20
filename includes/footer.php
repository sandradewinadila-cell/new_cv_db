</main>
<!-- End .admin-content -->

<!-- Bootstrap 5 JS Bundle CDN -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<!-- Script Konfirmasi Logout -->
<script>
document.addEventListener('DOMContentLoaded', function () {
    const logoutBtn = document.getElementById('btn-logout');
    if (logoutBtn) {
        logoutBtn.addEventListener('click', function (e) {
            e.preventDefault();
            const confirmed = confirm('Apakah Anda yakin ingin keluar?');
            if (confirmed) {
                window.location.href = 'logout.php';
            }
        });
    }
});
</script>
</body>
</html>