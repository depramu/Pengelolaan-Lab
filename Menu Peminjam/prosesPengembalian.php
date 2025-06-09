<?php
session_start(); // Mulai sesi untuk menyimpan pesan notifikasi
include '../koneksi.php';

// Cek apakah tombol submit sudah ditekan
if (isset($_POST['submit_pengembalian'])) {

    // 1. Ambil data dari form
    $idPeminjamanRuangan = $_POST['idPeminjamanRuangan'];

    // --- (OPSIONAL TAPI DIREKOMENDASIKAN) ---
    // Jika Anda ingin menambahkan field catatan dan k  ondisi di form, 
    // Anda bisa mengambil datanya seperti ini:
    // $catatan = $_POST['catatanPengembalian'];
    // $kondisi = $_POST['kondisiRuangan'];
    // Lihat Langkah 3 untuk cara menambahkannya di form HTML.
    // Untuk sekarang kita set default saja.
    $catatan = "Tidak ada catatan tambahan.";
    $kondisi = "Baik";

    // 2. Proses Upload File Dokumentasi
    $uploadDir = '../uploads/ruangan/'; // Pastikan folder ini ada dan bisa ditulisi (writable)
    if (!file_exists($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }

    $dokSebelum_path = null;
    $dokSesudah_path = null;
    $errors = [];

    // Fungsi untuk memproses upload
    function prosesUpload($fileInputName, $uploadDir)
    {
        if (isset($_FILES[$fileInputName]) && $_FILES[$fileInputName]['error'] == 0) {
            $fileName = basename($_FILES[$fileInputName]['name']);
            $fileTmpName = $_FILES[$fileInputName]['tmp_name'];
            $fileSize = $_FILES[$fileInputName]['size'];
            $fileType = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

            // Validasi tipe file
            $allowedTypes = ['jpg', 'jpeg', 'png', 'heif'];
            if (!in_array($fileType, $allowedTypes)) {
                return ['error' => "Format file untuk {$fileInputName} tidak valid. Harus .jpg, .jpeg, .png, atau .heif."];
            }

            // Validasi ukuran file (misal: maks 5MB)
            if ($fileSize > 5 * 1024 * 1024) {
                return ['error' => "Ukuran file untuk {$fileInputName} terlalu besar. Maks 5MB."];
            }

            // Buat nama file unik untuk menghindari tumpang tindih
            $newFileName = uniqid() . '_' . $fileName;
            $targetPath = $uploadDir . $newFileName;

            if (move_uploaded_file($fileTmpName, $targetPath)) {
                return ['path' => $targetPath];
            } else {
                return ['error' => "Gagal memindahkan file {$fileInputName}."];
            }
        } else {
            return ['error' => "Anda harus mengupload file untuk {$fileInputName}."];
        }
    }

    $resultSebelum = prosesUpload('dokSebelum', $uploadDir);
    if (isset($resultSebelum['error'])) {
        $errors[] = $resultSebelum['error'];
    } else {
        $dokSebelum_path = $resultSebelum['path'];
    }

    $resultSesudah = prosesUpload('dokSesudah', $uploadDir);
    if (isset($resultSesudah['error'])) {
        $errors[] = $resultSesudah['error'];
    } else {
        $dokSesudah_path = $resultSesudah['path'];
    }


    // 3. Jika tidak ada error pada upload, lanjutkan ke database
    if (empty($errors)) {

        // Gunakan Transaksi untuk memastikan kedua query berhasil
        sqlsrv_begin_transaction($conn);

        // Query 1: INSERT ke tabel Pengembalian_Ruangan
        $sql_insert = "INSERT INTO Pengembalian_Ruangan 
                        (idPeminjamanRuangan, dokumentasiSebelum, dokumentasiSesudah, catatanPengembalianRuangan, kondisiRuangan) 
                       VALUES (?, ?, ?, ?, ?)";
        $params_insert = array(
            $idPeminjamanRuangan,
            $dokSebelum_path,
            $dokSesudah_path,
            $catatan,
            $kondisi
        );
        $stmt_insert = sqlsrv_query($conn, $sql_insert, $params_insert);

        // Query 2: UPDATE status di tabel Peminjaman_Ruangan
        $sql_update = "UPDATE Peminjaman_Ruangan SET statusPeminjaman = ? WHERE idPeminjamanRuangan = ?";
        $newStatus = "Telah Dikembalikan"; // atau 'Dikembalikan'
        $params_update = array($newStatus, $idPeminjamanRuangan);
        $stmt_update = sqlsrv_query($conn, $sql_update, $params_update);

        // Cek apakah kedua query berhasil
        if ($stmt_insert && $stmt_update) {
            // Jika berhasil, commit transaksi
            sqlsrv_commit($conn);
            $_SESSION['message'] = "Pengembalian ruangan berhasil diproses.";
            header("Location: riwayatRuangan.php"); // Redirect ke halaman riwayat
            exit();
        } else {
            // Jika salah satu gagal, rollback transaksi
            sqlsrv_rollback($conn);

            // Hapus file yang sudah ter-upload jika query gagal
            if (file_exists($dokSebelum_path)) unlink($dokSebelum_path);
            if (file_exists($dokSesudah_path)) unlink($dokSesudah_path);

            $_SESSION['error'] = "Gagal memproses pengembalian. Error: " . print_r(sqlsrv_errors(), true);
            header("Location: pengembalianRuangan.php?id=" . $idPeminjamanRuangan); // Kembali ke form dengan pesan error
            exit();
        }
    } else {
        // Jika ada error upload, kembali ke form dan tampilkan error
        $_SESSION['error'] = implode("<br>", $errors);
        header("Location: pengembalianRuangan.php?id=" . $idPeminjamanRuangan);
        exit();
    }
} else {
    // Jika file diakses langsung tanpa submit form
    die("Akses tidak diizinkan.");
}
