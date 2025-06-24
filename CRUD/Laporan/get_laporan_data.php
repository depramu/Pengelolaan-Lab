<?php
// get_laporan_data.php

// Mengatur header respons sebagai JSON.
header('Content-Type: application/json');

// Menyertakan file koneksi.php. Path disesuaikan jika koneksi.php dua level di atas.
// (misal, jika get_laporan_data.php ada di Pengelolaan-Lab/CRUD/Laporan/ dan koneksi.php di Pengelolaan-Lab/)
if (file_exists(__DIR__ . '/../../koneksi.php')) {
    include __DIR__ . '/../../koneksi.php';
} else {
    echo json_encode(['status' => 'error', 'message' => 'File koneksi.php tidak ditemukan. Periksa path.']);
    exit;
}

// Inisialisasi respons default.
$response = ['status' => 'error', 'message' => 'Request tidak valid atau tidak ada data.', 'data' => []];

// Mengambil parameter dari URL.
$jenisLaporan = isset($_GET['jenisLaporan']) ? $_GET['jenisLaporan'] : null;
$bulan = isset($_GET['bulan']) && $_GET['bulan'] !== '' ? (int)$_GET['bulan'] : null;
$tahun = isset($_GET['tahun']) && $_GET['tahun'] !== '' ? (int)$_GET['tahun'] : null;

// Proses hanya jika koneksi berhasil dan jenis laporan ada.
if ($conn && $jenisLaporan) {
    try {
        $dataResult = []; // Inisialisasi $dataResult di luar blok if agar selalu terdefinisi
        $params = [];     // Inisialisasi $params

        if ($jenisLaporan === 'dataBarang') {
            $query = "SELECT idBarang, namaBarang, stokBarang, lokasiBarang FROM Barang ORDER BY idBarang ASC";
            $stmt = sqlsrv_query($conn, $query);
        } 
        else if ($jenisLaporan === 'dataRuangan') {
            $query = "SELECT idRuangan, namaRuangan, kondisiRuangan, ketersediaan FROM Ruangan ORDER BY idRuangan ASC";
            $stmt = sqlsrv_query($conn, $query);
        }
        else if ($jenisLaporan === 'peminjamSeringMeminjam') {
            if ($bulan === null || $tahun === null) {
                $response['message'] = "Bulan dan Tahun harus dipilih untuk laporan Peminjam yang Sering Meminjam.";
                $stmt = false; // Set $stmt ke false agar tidak masuk ke loop fetch
            } else {
                // **PERBAIKAN NAMA KOLOM menjadi 'nama' sesuai screenshot database Anda**
                $query = "
                    SELECT
                        CASE WHEN P.nim IS NOT NULL THEN P.nim WHEN P.npk IS NOT NULL THEN P.npk END AS IDPeminjam,
                        CASE WHEN P.nim IS NOT NULL THEN M.nama WHEN P.npk IS NOT NULL THEN K.nama END AS NamaPeminjam, 
                        CASE WHEN P.nim IS NOT NULL THEN 'Mahasiswa' WHEN P.npk IS NOT NULL THEN 'Karyawan' END AS JenisPeminjam,
                        COUNT(P.id_peminjaman) AS JumlahPeminjaman
                    FROM (
                        SELECT idPeminjamanBrg AS id_peminjaman, nim, npk, tglPeminjamanBrg FROM Peminjaman_Barang WHERE YEAR(tglPeminjamanBrg) = ? AND MONTH(tglPeminjamanBrg) = ?
                        UNION ALL
                        SELECT idPeminjamanRuangan AS id_peminjaman, nim, npk, tglPeminjamanRuangan FROM Peminjaman_Ruangan WHERE YEAR(tglPeminjamanRuangan) = ? AND MONTH(tglPeminjamanRuangan) = ?
                    ) AS P
                    LEFT JOIN Mahasiswa AS M ON P.nim = M.nim 
                    LEFT JOIN Karyawan AS K ON P.npk = K.npk 
                    GROUP BY 
                        CASE WHEN P.nim IS NOT NULL THEN P.nim WHEN P.npk IS NOT NULL THEN P.npk END,
                        CASE WHEN P.nim IS NOT NULL THEN M.nama WHEN P.npk IS NOT NULL THEN K.nama END, 
                        CASE WHEN P.nim IS NOT NULL THEN 'Mahasiswa' WHEN P.npk IS NOT NULL THEN 'Karyawan' END
                    ORDER BY JumlahPeminjaman DESC, NamaPeminjam ASC;
                ";
                $params = [$tahun, $bulan, $tahun, $bulan]; 
                $stmt = sqlsrv_query($conn, $query, $params); 
            }
        }
        else if ($jenisLaporan === 'barangSeringDipinjam') {
            if ($bulan === null || $tahun === null) {
                $response['message'] = "Bulan dan Tahun harus dipilih untuk laporan Barang yang Sering Dipinjam.";
                $stmt = false;
            } else {
                $query = "
                    SELECT PB.idBarang, B.namaBarang, SUM(PB.jumlahBrg) AS TotalKuantitasDipinjam
                    FROM Peminjaman_Barang AS PB INNER JOIN Barang AS B ON PB.idBarang = B.idBarang
                    WHERE YEAR(PB.tglPeminjamanBrg) = ? AND MONTH(PB.tglPeminjamanBrg) = ?
                    GROUP BY PB.idBarang, B.namaBarang ORDER BY TotalKuantitasDipinjam DESC, B.namaBarang ASC;
                ";
                $params = [$tahun, $bulan];
                $stmt = sqlsrv_query($conn, $query, $params);
            }
        }
        else if ($jenisLaporan === 'ruanganSeringDipinjam') {
            if ($bulan === null || $tahun === null) {
                $response['message'] = "Bulan dan Tahun harus dipilih untuk laporan Ruangan yang Sering Dipinjam.";
                $stmt = false;
            } else {
                $query = "
                    SELECT PR.idRuangan, R.namaRuangan, COUNT(PR.idpeminjamanRuangan) AS JumlahDipinjam
                    FROM Peminjaman_Ruangan AS PR INNER JOIN Ruangan AS R ON PR.idRuangan = R.idRuangan
                    WHERE YEAR(PR.tglPeminjamanRuangan) = ? AND MONTH(PR.tglPeminjamanRuangan) = ?
                    GROUP BY PR.idRuangan, R.namaRuangan ORDER BY JumlahDipinjam DESC, R.namaRuangan ASC;
                ";
                $params = [$tahun, $bulan];
                $stmt = sqlsrv_query($conn, $query, $params);
            }
        }
        else {
            $response['message'] = 'Jenis laporan belum diimplementasikan atau tidak dikenal.';
            $stmt = false; // Tidak ada statement yang valid untuk dieksekusi
        }

        // Proses hasil query jika statement valid
        if ($stmt) { // Cek apakah $stmt adalah resource yang valid, bukan false
            while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
                // Fallback jika NamaPeminjam null (khusus untuk laporan peminjamSeringMeminjam)
                if ($jenisLaporan === 'peminjamSeringMeminjam' && array_key_exists('NamaPeminjam', $row) && $row['NamaPeminjam'] === null && isset($row['IDPeminjam'])) {
                    $row['NamaPeminjam'] = $row['IDPeminjam'] . " (Nama Tdk Ditemukan)";
                }
                $dataResult[] = $row;
            }
            sqlsrv_free_stmt($stmt);
            // Set status sukses hanya jika tidak ada pesan error sebelumnya (misalnya dari validasi bulan/tahun)
            // dan query tidak menghasilkan error SQL.
            if(empty($response['message']) || $response['message'] === 'Request tidak valid atau tidak ada data.'){
                 $response['status'] = 'success';
                 // Pesan sukses disesuaikan jika ada data atau tidak
                 $response['message'] = !empty($dataResult) ? 'Data berhasil diambil.' : 'Tidak ada data laporan untuk periode yang dipilih.';
            }
            $response['data'] = $dataResult;

        } elseif ($stmt === false && ($response['message'] === 'Request tidak valid atau tidak ada data.' || empty($response['message']))) {
            // Jika $stmt false karena query error (bukan karena validasi bulan/tahun yang pesan errornya sudah di-set)
            $errorDetails = sqlsrv_errors();
            $response['message'] = "Gagal menjalankan query untuk '$jenisLaporan': " . ($errorDetails ? print_r($errorDetails, true) : 'Unknown SQL Server error');
        }
        // Jika $stmt false karena validasi bulan/tahun, $response['message'] sudah di-set.

    } catch (Exception $e) {
        $response['message'] = 'Terjadi kesalahan server: ' . $e->getMessage();
    }
    if ($conn) {
        sqlsrv_close($conn);
    }
} else {
    if (!$conn && $jenisLaporan) {
        $response['message'] = "Koneksi ke database gagal. Periksa koneksi.php.";
    } elseif ($conn && !$jenisLaporan) {
        $response['message'] = "Parameter jenis laporan tidak disediakan.";
    } elseif (!$conn && !$jenisLaporan) {
        $response['message'] = "Koneksi DB gagal & jenis laporan tidak disediakan.";
    }
    // Tidak perlu mengubah response['message'] jika sudah ada pesan spesifik.
}

echo json_encode($response);
?>