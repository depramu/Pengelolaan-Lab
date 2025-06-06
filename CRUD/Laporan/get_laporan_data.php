<?php
// get_laporan_data.php
header('Content-Type: application/json');
include '../../koneksi.php'; // Pastikan path ini benar

$response = ['status' => 'error', 'message' => 'Request tidak valid.', 'data' => []];

$jenisLaporan = isset($_GET['jenisLaporan']) ? $_GET['jenisLaporan'] : null;
$bulan = isset($_GET['bulan']) ? (int)$_GET['bulan'] : null; // Pastikan ini integer
$tahun = isset($_GET['tahun']) ? (int)$_GET['tahun'] : null; // Pastikan ini integer

if ($conn && $jenisLaporan) {
    try {
        if ($jenisLaporan === 'dataBarang') {
            $query = "SELECT idBarang, namaBarang, stokBarang, lokasiBarang FROM Barang ORDER BY idBarang ASC";
            $stmt = sqlsrv_query($conn, $query);

            if ($stmt === false) {
                $response['message'] = "Gagal menjalankan query Data Barang: " . print_r(sqlsrv_errors(), true);
            } else {
                $dataResult = [];
                while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
                    $dataResult[] = $row;
                }
                sqlsrv_free_stmt($stmt);
                $response['status'] = 'success';
                $response['message'] = 'Data barang berhasil diambil.';
                $response['data'] = $dataResult;
            }

        } 
        else if ($jenisLaporan === 'dataRuangan') {
            $query = "SELECT idRuangan, namaRuangan, kondisiRuangan, ketersediaan FROM Ruangan ORDER BY idRuangan ASC";
            $stmt = sqlsrv_query($conn, $query);

            if ($stmt === false) {
                $response['message'] = "Gagal menjalankan query Data Ruangan: " . print_r(sqlsrv_errors(), true);
            } else {
                $dataResult = [];
                while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
                    $dataResult[] = $row;
                }
                sqlsrv_free_stmt($stmt);
                $response['status'] = 'success';
                $response['message'] = 'Data ruangan berhasil diambil.';
                $response['data'] = $dataResult;
            }
        }
        else if ($jenisLaporan === 'peminjamSeringMeminjam') {
            if ($bulan === null || $tahun === null) {
                $response['message'] = "Bulan dan Tahun harus dipilih untuk laporan Peminjam yang Sering Meminjam.";
            } else {
                $query = "
                    SELECT
                        CASE WHEN P.nim IS NOT NULL THEN P.nim WHEN P.npk IS NOT NULL THEN P.npk END AS IDPeminjam,
                        CASE WHEN P.nim IS NOT NULL THEN M.namaMhs WHEN P.npk IS NOT NULL THEN K.namaKry END AS NamaPeminjam,
                        CASE WHEN P.nim IS NOT NULL THEN 'Mahasiswa' WHEN P.npk IS NOT NULL THEN 'Karyawan' END AS JenisPeminjam,
                        COUNT(P.id_peminjaman) AS JumlahPeminjaman
                    FROM (
                        SELECT idPeminjamanBrg AS id_peminjaman, nim, npk, tglPeminjamanBrg AS tgl_peminjaman FROM PENGELOLAAN_LAB.dbo.Peminjaman_Barang WHERE YEAR(tglPeminjamanBrg) = ? AND MONTH(tglPeminjamanBrg) = ?
                        UNION ALL
                        SELECT idPeminjamanRuangan AS id_peminjaman, nim, npk, tglPeminjamanRuangan AS tgl_peminjaman FROM PENGELOLAAN_LAB.dbo.Peminjaman_Ruangan WHERE YEAR(tglPeminjamanRuangan) = ? AND MONTH(tglPeminjamanRuangan) = ?
                    ) AS P
                    LEFT JOIN PENGELOLAAN_LAB.dbo.Mahasiswa AS M ON P.nim = M.nim
                    LEFT JOIN PENGELOLAAN_LAB.dbo.Karyawan AS K ON P.npk = K.npk
                    GROUP BY CASE WHEN P.nim IS NOT NULL THEN P.nim WHEN P.npk IS NOT NULL THEN P.npk END,
                             CASE WHEN P.nim IS NOT NULL THEN M.namaMhs WHEN P.npk IS NOT NULL THEN K.namaKry END,
                             CASE WHEN P.nim IS NOT NULL THEN 'Mahasiswa' WHEN P.npk IS NOT NULL THEN 'Karyawan' END
                    ORDER BY JumlahPeminjaman DESC, NamaPeminjam ASC;
                ";
                $params = [$tahun, $bulan, $tahun, $bulan]; 
                $stmt = sqlsrv_query($conn, $query, $params); 

                if ($stmt === false) {
                    $response['message'] = "Gagal menjalankan query Peminjam Sering Meminjam: " . print_r(sqlsrv_errors(), true);
                } else {
                    $dataResult = [];
                    while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
                        if (array_key_exists('NamaPeminjam', $row) && $row['NamaPeminjam'] === null) {
                            $row['NamaPeminjam'] = $row['IDPeminjam'] . " (Nama Tidak Ditemukan)";
                        }
                        $dataResult[] = $row;
                    }
                    sqlsrv_free_stmt($stmt);
                    $response['status'] = 'success';
                    $response['message'] = 'Data peminjam sering meminjam berhasil diambil.';
                    $response['data'] = $dataResult;
                }
            }
        }
        else if ($jenisLaporan === 'barangSeringDipinjam') {
            if ($bulan === null || $tahun === null) {
                $response['message'] = "Bulan dan Tahun harus dipilih untuk laporan Barang yang Sering Dipinjam.";
            } else {
                $query = "
                    SELECT
                        PB.idBarang,
                        B.namaBarang,
                        SUM(PB.jumlahBrg) AS TotalKuantitasDipinjam
                    FROM
                        PENGELOLAAN_LAB.dbo.Peminjaman_Barang AS PB
                    INNER JOIN
                        PENGELOLAAN_LAB.dbo.Barang AS B ON PB.idBarang = B.idBarang
                    WHERE
                        YEAR(PB.tglPeminjamanBrg) = ? AND MONTH(PB.tglPeminjamanBrg) = ?
                    GROUP BY
                        PB.idBarang,
                        B.namaBarang
                    ORDER BY
                        TotalKuantitasDipinjam DESC,
                        B.namaBarang ASC;
                ";
                $params = [$tahun, $bulan];
                $stmt = sqlsrv_query($conn, $query, $params);

                if ($stmt === false) {
                    $response['message'] = "Gagal menjalankan query Barang Sering Dipinjam: " . print_r(sqlsrv_errors(), true);
                } else {
                    $dataResult = [];
                    while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
                        $dataResult[] = $row;
                    }
                    sqlsrv_free_stmt($stmt);
                    $response['status'] = 'success';
                    $response['message'] = 'Data barang sering dipinjam berhasil diambil.';
                    $response['data'] = $dataResult;
                }
            }
        }
        // --- AWAL BLOK UNTUK JENIS LAPORAN "Ruangan yang Sering Dipinjam" ---
        else if ($jenisLaporan === 'ruanganSeringDipinjam') {
            if ($bulan === null || $tahun === null) {
                $response['message'] = "Bulan dan Tahun harus dipilih untuk laporan Ruangan yang Sering Dipinjam.";
            } else {
                $query = "
                    SELECT
                        PR.idRuangan,
                        R.namaRuangan,
                        COUNT(PR.idpeminjamanRuangan) AS JumlahDipinjam
                    FROM
                        PENGELOLAAN_LAB.dbo.Peminjaman_Ruangan AS PR
                    INNER JOIN
                        PENGELOLAAN_LAB.dbo.Ruangan AS R ON PR.idRuangan = R.idRuangan
                    WHERE
                        YEAR(PR.tglPeminjamanRuangan) = ? AND MONTH(PR.tglPeminjamanRuangan) = ?
                    GROUP BY
                        PR.idRuangan,
                        R.namaRuangan
                    ORDER BY
                        JumlahDipinjam DESC,
                        R.namaRuangan ASC;
                ";
                // Parameter untuk query ini adalah tahun dan bulan
                $params = [$tahun, $bulan];
                $stmt = sqlsrv_query($conn, $query, $params);

                if ($stmt === false) {
                    $response['message'] = "Gagal menjalankan query Ruangan Sering Dipinjam: " . print_r(sqlsrv_errors(), true);
                } else {
                    $dataResult = [];
                    while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
                        $dataResult[] = $row;
                    }
                    sqlsrv_free_stmt($stmt);
                    $response['status'] = 'success';
                    $response['message'] = 'Data ruangan sering dipinjam berhasil diambil.';
                    $response['data'] = $dataResult;
                }
            }
        }
        // --- AKHIR BLOK UNTUK JENIS LAPORAN "Ruangan yang Sering Dipinjam" ---
        else {
            $response['message'] = 'Jenis laporan belum diimplementasikan atau tidak dikenal.';
        }
    } catch (Exception $e) {
        $response['message'] = 'Terjadi kesalahan server: ' . $e->getMessage();
    }
    if ($conn) {
        sqlsrv_close($conn);
    }
} else {
    if (!$conn) {
        $response['message'] = "Koneksi ke database gagal. Periksa koneksi.php.";
    } else {
        $response['message'] = "Parameter jenis laporan tidak disediakan atau tidak valid.";
    }
}

echo json_encode($response);
?>