<?php
include '../../koneksi.php';

if (isset($_POST['submit_pengembalian'])) {

    $idPeminjaman = $_POST['idPeminjamanRuangan'];
    $pesan_error = '';
    $uploadSukses = true;
    $namaFileSebelum = '';
    $namaFileSesudah = '';

    // Proses upload file "dokumentasiSebelum"
    if (isset($_FILES['dokSebelum']) && $_FILES['dokSebelum']['error'] == 0) {
        $target_dir = "../../uploads/dokumentasi/";
        // Pastikan folder uploads/dokumentasi ada
        if (!is_dir($target_dir)) {
            mkdir($target_dir, 0777, true);
        }
        $namaFileSebelum = $idPeminjaman . "_sebelum_" . time() . "_" . basename($_FILES["dokSebelum"]["name"]);
        $target_file_sebelum = $target_dir . $namaFileSebelum;
        if (!move_uploaded_file($_FILES["dokSebelum"]["tmp_name"], $target_file_sebelum)) {
            $pesan_error .= "Gagal upload file 'Sebelum'. ";
            $uploadSukses = false;
        }
    } else {
        $pesan_error .= "File 'Sebelum' wajib diupload. ";
        $uploadSukses = false;
    }

    // Proses upload file "dokumentasiSesudah"
    if (isset($_FILES['dokSesudah']) && $_FILES['dokSesudah']['error'] == 0) {
        $target_dir = "../../uploads/dokumentasi/";
        // Pastikan folder uploads/dokumentasi ada
        if (!is_dir($target_dir)) {
            mkdir($target_dir, 0777, true);
        }
        $namaFileSesudah = $idPeminjaman . "_sesudah_" . time() . "_" . basename($_FILES["dokSesudah"]["name"]);
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
        // Cek dulu apakah data pengembalian sudah ada, kalau ada kita UPDATE, kalau belum kita INSERT
        $sql_cek = "SELECT * FROM Pengembalian_Ruangan WHERE idPeminjamanRuangan = ?";
        $params_cek = array($idPeminjaman);
        $stmt_cek = sqlsrv_query($conn, $sql_cek, $params_cek);
        
        if(sqlsrv_has_rows($stmt_cek)){
            // Jika sudah ada, UPDATE
            $sql_query_db = "UPDATE Pengembalian_Ruangan SET dokumentasiSebelum = ?, dokumentasiSesudah = ? WHERE idPeminjamanRuangan = ?";
        } else {
            // Jika belum ada, INSERT
            $sql_query_db = "INSERT INTO Pengembalian_Ruangan (idPeminjamanRuangan, dokumentasiSebelum, dokumentasiSesudah) VALUES (?, ?, ?)";
        }
        
        $params_db = array($idPeminjaman, $namaFileSebelum, $namaFileSesudah);
        $stmt_db = sqlsrv_query($conn, $sql_query_db, $params_db);


        if ($stmt_db) {
            // --- INI PERUBAHANNYA, JEK! ---
            // Update status di tabel peminjaman jadi "Menunggu Pengecekan"
            $sql_update_status = "UPDATE Peminjaman_Ruangan SET statusPeminjaman = 'Menunggu Pengecekan' WHERE idPeminjamanRuangan = ?";
            $params_update = array($idPeminjaman);
            $stmt_update = sqlsrv_query($conn, $sql_update_status, $params_update);

            if ($stmt_update) {
                // Kalau update status berhasil, baru redirect
                header("Location: dataPeminjaman.php");
                exit();
            } else {
                echo "Data pengembalian berhasil disimpan, TAPI gagal update status peminjaman. Error: <pre>";
                print_r(sqlsrv_errors(), true);
                echo "</pre>";
            }
        } else {
            echo "Gagal menyimpan data pengembalian ke database. Error: <pre>";
            print_r(sqlsrv_errors(), true);
            echo "</pre>";
        }
    } else {
        echo "Gagal mengupload file. Alasan: " . $pesan_error;
    }
}
?> #065ba6  