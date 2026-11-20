<?php
/**
 * Helper Fungsi Upload Gambar
 * Proyek CV Dinamis
 * 
 * @param array $file Array dari $_FILES['nama_input']
 * @param string $folder_name Subfolder di assets/uploads/ (misal: 'avatars', 'education')
 * @return array ['success' => bool, 'filename' => string, 'error' => string]
 */
function uploadImage($file, $folder_name) {
    // 1. Validasi error upload bawaan PHP
    if (!isset($file) || $file['error'] !== UPLOAD_ERR_OK) {
        return [
            'success' => false,
            'error'   => 'Terjadi kesalahan saat mengunggah berkas atau berkas tidak dipilih.'
        ];
    }

    // 2. Validasi tipe ekstensi berkas
    $allowed_extensions = ['jpg', 'jpeg', 'png', 'webp'];
    $file_extension     = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

    if (!in_array($file_extension, $allowed_extensions)) {
        return [
            'success' => false,
            'error'   => 'Format berkas tidak didukung. Hanya berkas JPG, JPEG, PNG, dan WEBP yang diizinkan.'
        ];
    }

    // 3. Validasi ukuran maksimal (2MB = 2.000.000 bytes)
    if ($file['size'] > 2000000) {
        return [
            'success' => false,
            'error'   => 'Ukuran berkas terlalu besar. Maksimal ukuran berkas adalah 2MB.'
        ];
    }

    // 4. Siapkan folder tujuan
    $target_dir = __DIR__ . '/../assets/uploads/' . trim($folder_name, '/') . '/';
    if (!is_dir($target_dir)) {
        mkdir($target_dir, 0777, true);
    }

    // 5. Generate nama berkas unik dan aman
    $clean_original_name = preg_replace("/[^a-zA-Z0-9.]/", "_", basename($file['name']));
    $new_filename        = uniqid() . '_' . $clean_original_name;
    $target_filepath     = $target_dir . $new_filename;

    // 6. Pindahkan berkas dari temp ke folder tujuan
    if (move_uploaded_file($file['tmp_name'], $target_filepath)) {
        return [
            'success'  => true,
            'filename' => $new_filename
        ];
    }

    return [
        'success' => false,
        'error'   => 'Gagal memindahkan berkas yang diunggah ke direktori tujuan.'
    ];
}