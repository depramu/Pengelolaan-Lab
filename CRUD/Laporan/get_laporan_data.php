<?php
// get_laporan_data.php

// Memberi tahu browser bahwa respons dari file ini adalah format JSON.
header('Content-Type: application/json');

// Memanggil file koneksi.php.
// Diasumsikan file ini ada 2 level di atas, relatif terhadap root project.
if (file_exists(__DIR__ . '/../../function/koneksi.php')) {
    include __DIR__ . '/../../function/koneksi.php';
} else {
    // Jika koneksi tidak ditemukan, kirim respons error dan hentikan.
    echo json_encode(['status' => 'error', 'message' => 'File koneksi.php tidak ditemukan.']);
    exit;
}

// Inisialisasi array respons default.
$response = ['status' => 'error', 'message' => 'Request tidak valid.', 'data' => []];

// Ambil parameter dari URL request.
$jenisLaporan = isset($_GET['jenisLaporan']) ? $_GET['jenisLaporan'] : null;
$bulan = (isset($_GET['bulan']) && $_GET['bulan'] !== '' && $_GET['bulan'] !== '0') ? (int)$_GET['bulan'] : null;
$tahun = isset($_GET['tahun']) && $_GET['tahun'] !== '' ? (int)$_GET['tahun'] : null;

// // Debug log (hapus jika sudah yakin)
file_put_contents(__DIR__ . '/debug.log', "jenisLaporan=$jenisLaporan, tahun=$tahun, bulan=$bulan\n", FILE_APPEND);

// Lanjutkan hanya jika koneksi berhasil dan jenis laporan telah diberikan.
if ($conn && $jenisLaporan) {
    try {
        $stmt = null;     // Inisialisasi variabel statement.
        $params = [];     // Inisialisasi variabel parameter.

        // Memilih query berdasarkan jenis laporan yang diminta.
        switch ($jenisLaporan) {
            case 'dataBarang':
                $query = "SELECT idBarang, namaBarang, stokBarang, lokasiBarang FROM Barang ORDER BY idBarang ASC";
                $stmt = sqlsrv_query($conn, $query);
                break;
            case 'dataRuangan':
                $query = "SELECT idRuangan, namaRuangan, kondisiRuangan, ketersediaan FROM Ruangan ORDER BY idRuangan ASC";
                $stmt = sqlsrv_query($conn, $query);
                break;
            case 'peminjamSeringMeminjam':
                if ($tahun && $bulan === null) {
                    // Query tahunan
                    $query = "
                        SELECT
                            CASE WHEN P.nim IS NOT NULL THEN P.nim WHEN P.npk IS NOT NULL THEN P.npk END AS IDPeminjam,
                            CASE WHEN P.nim IS NOT NULL THEN M.nama WHEN P.npk IS NOT NULL THEN K.nama END AS NamaPeminjam, 
                            CASE WHEN P.nim IS NOT NULL THEN 'Mahasiswa' WHEN P.npk IS NOT NULL THEN 'Karyawan' END AS JenisPeminjam,
                            COUNT(P.id_peminjaman) AS JumlahPeminjaman
                        FROM (
                            SELECT idPeminjamanBrg AS id_peminjaman, nim, npk FROM Peminjaman_Barang WHERE YEAR(tglPeminjamanBrg) = ?
                            UNION ALL
                            SELECT idPeminjamanRuangan AS id_peminjaman, nim, npk FROM Peminjaman_Ruangan WHERE YEAR(tglPeminjamanRuangan) = ?
                        ) AS P
                        LEFT JOIN Mahasiswa AS M ON P.nim = M.nim 
                        LEFT JOIN Karyawan AS K ON P.npk = K.npk 
                        GROUP BY 
                            CASE WHEN P.nim IS NOT NULL THEN P.nim WHEN P.npk IS NOT NULL THEN P.npk END,
                            CASE WHEN P.nim IS NOT NULL THEN M.nama WHEN P.npk IS NOT NULL THEN K.nama END, 
                            CASE WHEN P.nim IS NOT NULL THEN 'Mahasiswa' WHEN P.npk IS NOT NULL THEN 'Karyawan' END
                        ORDER BY JumlahPeminjaman DESC, NamaPeminjam ASC;
                    ";
                    $params = [$tahun, $tahun];
                    $stmt = sqlsrv_query($conn, $query, $params);
                } elseif ($tahun && $bulan !== null) {
                    // Query bulanan
                    $query = "
                        SELECT
                            CASE WHEN P.nim IS NOT NULL THEN P.nim WHEN P.npk IS NOT NULL THEN P.npk END AS IDPeminjam,
                            CASE WHEN P.nim IS NOT NULL THEN M.nama WHEN P.npk IS NOT NULL THEN K.nama END AS NamaPeminjam, 
                            CASE WHEN P.nim IS NOT NULL THEN 'Mahasiswa' WHEN P.npk IS NOT NULL THEN 'Karyawan' END AS JenisPeminjam,
                            COUNT(P.id_peminjaman) AS JumlahPeminjaman
                        FROM (
                            SELECT idPeminjamanBrg AS id_peminjaman, nim, npk FROM Peminjaman_Barang WHERE YEAR(tglPeminjamanBrg) = ? AND MONTH(tglPeminjamanBrg) = ?
                            UNION ALL
                            SELECT idPeminjamanRuangan AS id_peminjaman, nim, npk FROM Peminjaman_Ruangan WHERE YEAR(tglPeminjamanRuangan) = ? AND MONTH(tglPeminjamanRuangan) = ?
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
                } else {
                    $response['message'] = "Tahun harus dipilih untuk laporan ini.";
                    $stmt = false;
                }
                break;
            case 'barangSeringDipinjam':
                if ($tahun && $bulan === null) {
                    $query = "
                        SELECT PB.idBarang, B.namaBarang, SUM(PB.jumlahBrg) AS TotalKuantitasDipinjam
                        FROM Peminjaman_Barang AS PB INNER JOIN Barang AS B ON PB.idBarang = B.idBarang
                        WHERE YEAR(PB.tglPeminjamanBrg) = ?
                        GROUP BY PB.idBarang, B.namaBarang ORDER BY TotalKuantitasDipinjam DESC, B.namaBarang ASC;
                    ";
                    $params = [$tahun];
                    $stmt = sqlsrv_query($conn, $query, $params);
                } elseif ($tahun && $bulan !== null) {
                    $query = "
                        SELECT PB.idBarang, B.namaBarang, SUM(PB.jumlahBrg) AS TotalKuantitasDipinjam
                        FROM Peminjaman_Barang AS PB INNER JOIN Barang AS B ON PB.idBarang = B.idBarang
                        WHERE YEAR(PB.tglPeminjamanBrg) = ? AND MONTH(PB.tglPeminjamanBrg) = ?
                        GROUP BY PB.idBarang, B.namaBarang ORDER BY TotalKuantitasDipinjam DESC, B.namaBarang ASC;
                    ";
                    $params = [$tahun, $bulan];
                    $stmt = sqlsrv_query($conn, $query, $params);
                } else {
                    $response['message'] = "Tahun harus dipilih untuk laporan ini.";
                    $stmt = false;
                }
                break;
            case 'ruanganSeringDipinjam':
                if ($tahun && $bulan === null) {
                    $query = "
                        SELECT PR.idRuangan, R.namaRuangan, COUNT(PR.idpeminjamanRuangan) AS JumlahDipinjam
                        FROM Peminjaman_Ruangan AS PR INNER JOIN Ruangan AS R ON PR.idRuangan = R.idRuangan
                        WHERE YEAR(PR.tglPeminjamanRuangan) = ?
                        GROUP BY PR.idRuangan, R.namaRuangan ORDER BY JumlahDipinjam DESC, R.namaRuangan ASC;
                    ";
                    $params = [$tahun];
                    $stmt = sqlsrv_query($conn, $query, $params);
                } elseif ($tahun && $bulan !== null) {
                    $query = "
                        SELECT PR.idRuangan, R.namaRuangan, COUNT(PR.idpeminjamanRuangan) AS JumlahDipinjam
                        FROM Peminjaman_Ruangan AS PR INNER JOIN Ruangan AS R ON PR.idRuangan = R.idRuangan
                        WHERE YEAR(PR.tglPeminjamanRuangan) = ? AND MONTH(PR.tglPeminjamanRuangan) = ?
                        GROUP BY PR.idRuangan, R.namaRuangan ORDER BY JumlahDipinjam DESC, R.namaRuangan ASC;
                    ";
                    $params = [$tahun, $bulan];
                    $stmt = sqlsrv_query($conn, $query, $params);
                } else {
                    $response['message'] = "Tahun harus dipilih untuk laporan ini.";
                    $stmt = false;
                }
                break;
            default:
                $response['message'] = 'Jenis laporan belum diimplementasikan atau tidak dikenal.';
                $stmt = false;
                break;
        }

        if ($stmt) { // Jika query valid dan dijalankan (bukan false)
            $dataResult = [];
            while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
                $dataResult[] = $row;
            }
            sqlsrv_free_stmt($stmt);
            $response['status'] = 'success';
            $response['message'] = 'Data laporan berhasil diambil.';
            $response['data'] = $dataResult;
        } elseif ($stmt === false && $response['message'] === 'Request tidak valid.') { 
            // Jika query gagal dan tidak ada pesan error spesifik sebelumnya
            $errorDetails = sqlsrv_errors();
            $response['message'] = "Gagal menjalankan query untuk '$jenisLaporan': " . ($errorDetails ? print_r($errorDetails, true) : 'Kesalahan SQL Server tidak diketahui.');
        }
        // Jika $stmt false karena validasi bulan/tahun, $response['message'] sudah terisi dan akan dikirim.
    } catch (Exception $e) {
        $response['message'] = 'Terjadi kesalahan server: ' . $e->getMessage();
    }
    if ($conn) {
        sqlsrv_close($conn);
    }
} else {
    // Penanganan error koneksi atau parameter tidak valid
}
echo json_encode($response);
?>