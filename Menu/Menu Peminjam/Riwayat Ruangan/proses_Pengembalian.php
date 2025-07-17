<?php
include '../../../function/init.php';

define('UPLOAD_DIR', __DIR__ . '/../../../uploads/dokumentasi/');


error_log("--- START: proses_Pengembalian.php (Timestamp: " . date('Y-m-d H:i:s') . ") ---");
error_log("POST Data: " . print_r($_POST, true));
error_log("FILES Data: " . print_r($_FILES, true));
error_log("UPLOAD_DIR: " . UPLOAD_DIR);

$showModal = false; // Tambahkan variabel showModal

if (isset($_POST['submit_pengembalian'])) {

    $idPeminjaman = $_POST['idPeminjamanRuangan'];
    $pesan_error = '';
    $uploadSukses = true;
    $namaFileSebelum = '';
    $namaFileSesudah = '';

    if (isset($_FILES['dokSebelum']) && $_FILES['dokSebelum']['error'] == 0) {
        $target_dir = UPLOAD_DIR;
        if (!is_dir($target_dir)) {
            mkdir($target_dir, 0755, true);
        }
        $namaFileSebelum = "$idPeminjaman" . "_sebelum_" . time() . "_" . basename($_FILES["dokSebelum"]["name"]);
        $target_file_sebelum = $target_dir . $namaFileSebelum;
        if (!move_uploaded_file($_FILES["dokSebelum"]["tmp_name"], $target_file_sebelum)) {
            $pesan_error .= "Gagal upload file 'Sebelum'. ";
            $uploadSukses = false;
        }
    } else {
        $pesan_error .= "File 'Sebelum' wajib diupload. ";
        $uploadSukses = false;
    }

    if (isset($_FILES['dokSesudah']) && $_FILES['dokSesudah']['error'] == 0) {
        $target_dir = UPLOAD_DIR;
        if (!is_dir($target_dir)) {
            mkdir($target_dir, 0755, true);
        }
        $namaFileSesudah = "$idPeminjaman" . "_sesudah_" . time() . "_" . basename($_FILES["dokSesudah"]["name"]);
        $target_file_sesudah = $target_dir . $namaFileSesudah;
        if (!move_uploaded_file($_FILES["dokSesudah"]["tmp_name"], $target_file_sesudah)) {
            $pesan_error .= "Gagal upload file 'Sesudah'. ";
            $uploadSukses = false;
        }
    } else {
        $pesan_error .= "File 'Sesudah' wajib diupload. ";
        $uploadSukses = false;
    }

    if ($uploadSukses) {
        $sql_cek = "SELECT idPeminjamanRuangan FROM Pengembalian_Ruangan WHERE idPeminjamanRuangan = ?";
        $params_cek = [$idPeminjaman];
        $stmt_cek = sqlsrv_query($conn, $sql_cek, $params_cek);

        if (sqlsrv_has_rows($stmt_cek)) {
            $sql_query_db = "UPDATE Pengembalian_Ruangan SET dokumentasiSebelum = ?, dokumentasiSesudah = ? WHERE idPeminjamanRuangan = ?";
            $params_db = [$namaFileSebelum, $namaFileSesudah, $idPeminjaman];
        } else {
            $sql_query_db = "INSERT INTO Pengembalian_Ruangan (idPeminjamanRuangan, dokumentasiSebelum, dokumentasiSesudah) VALUES (?, ?, ?)";
            $params_db = [$idPeminjaman, $namaFileSebelum, $namaFileSesudah];
        }

        $stmt_db = sqlsrv_query($conn, $sql_query_db, $params_db);

        if ($stmt_db) {
            // Cek apakah sudah ada status peminjaman untuk id ini
            $cekStatusSql = "SELECT COUNT(*) as jumlah FROM Status_Peminjaman WHERE idPeminjamanRuangan = ?";
            $cekStatusParams = [$idPeminjaman];
            $cekStatusStmt = sqlsrv_query($conn, $cekStatusSql, $cekStatusParams);
            $sudahAdaStatus = false;
            if ($cekStatusStmt && ($cekStatusRow = sqlsrv_fetch_array($cekStatusStmt, SQLSRV_FETCH_ASSOC))) {
                $sudahAdaStatus = $cekStatusRow['jumlah'] > 0;
            }

            if ($sudahAdaStatus) {
                // Update status peminjaman menjadi 'Menunggu Pengecekan'
                $sql_update_status = "UPDATE Status_Peminjaman SET statusPeminjaman = ? WHERE idPeminjamanRuangan = ?";
                $params_update = ['Menunggu Pengecekan', $idPeminjaman];
            } else {
                // Insert status peminjaman baru
                $sql_update_status = "INSERT INTO Status_Peminjaman (idPeminjamanRuangan, statusPeminjaman) VALUES (?, ?)";
                $params_update = [$idPeminjaman, 'Menunggu Pengecekan'];
            }
            
            $stmt_update = sqlsrv_query($conn, $sql_update_status, $params_update);

            if ($stmt_update) {
                $untuk = 'PIC Aset';
                $pesanNotif = "Pengembalian ruangan dengan ID $idPeminjaman telah diajukan oleh peminjam.";
                $queryNotif = "INSERT INTO Notifikasi (pesan, status, untuk) VALUES (?, 'Belum Dibaca', ?)";
                sqlsrv_query($conn, $queryNotif, [$pesanNotif, $untuk]);
                // Sukses, showModal true (bisa digunakan di halaman redirect)
                header("Location: formDetailRiwayatRuangan.php?idPeminjamanRuangan=$idPeminjaman&success=1");
                exit();
            } else {
                echo "Data pengembalian berhasil disimpan, TAPI gagal update status peminjaman. Error: <pre>";
                print_r(sqlsrv_errors(), true);
                echo "</pre>";
            }
        } else {
            echo "Gagal menyimpan data pengembalian ke database. Error: <pre>";
            echo print_r(sqlsrv_errors(), true);
            echo "</pre>";
        }
    } else {
        echo "Gagal mengupload file. Alasan: " . $pesan_error;
    }
}
?>