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
$lokasiBarang = isset($_GET['lokasiBarang']) && $_GET['lokasiBarang'] !== '' ? $_GET['lokasiBarang'] : null;
$kondisiRuangan = isset($_GET['kondisiRuangan']) && $_GET['kondisiRuangan'] !== '' ? $_GET['kondisiRuangan'] : null;

// Pagination parameters
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$itemsPerPage = 5; // Set to 5 items per page as requested
$offset = ($page - 1) * $itemsPerPage;

// Debug log (hapus jika sudah yakin)
file_put_contents(__DIR__ . '/debug.log', "jenisLaporan=$jenisLaporan, tahun=$tahun, bulan=$bulan, lokasiBarang=$lokasiBarang, kondisiRuangan=$kondisiRuangan, page=$page\n", FILE_APPEND);

// Lanjutkan hanya jika koneksi berhasil dan jenis laporan telah diberikan.
if ($conn && $jenisLaporan) {
    try {
        $stmt = null;     // Inisialisasi variabel statement.
        $params = [];     // Inisialisasi variabel parameter.
        $totalCount = 0;  // Untuk menghitung total records

        // Memilih query berdasarkan jenis laporan yang diminta.
        switch ($jenisLaporan) {
            case 'dataBarang':
                // Query untuk menghitung total records
                $countQuery = "SELECT COUNT(*) as total FROM Barang WHERE isDeleted = 0";
                if ($lokasiBarang) {
                    $countQuery .= " AND lokasiBarang = ?";
                }
                $countStmt = sqlsrv_query($conn, $countQuery, $lokasiBarang ? [$lokasiBarang] : []);
                if ($countStmt) {
                    $countRow = sqlsrv_fetch_array($countStmt, SQLSRV_FETCH_ASSOC);
                    $totalCount = $countRow['total'];
                }

                // Query untuk data dengan pagination
                $query = "SELECT idBarang, namaBarang, stokBarang, lokasiBarang FROM Barang WHERE isDeleted = 0";
                $params = [];
                if ($lokasiBarang) {
                    $query .= " AND lokasiBarang = ?";
                    $params[] = $lokasiBarang;
                }
                $query .= " ORDER BY idBarang ASC OFFSET ? ROWS FETCH NEXT ? ROWS ONLY";
                $params[] = $offset;
                $params[] = $itemsPerPage;
                $stmt = sqlsrv_query($conn, $query, $params);
                break;

            case 'dataRuangan':
                // Query untuk menghitung total records
                $countQuery = "SELECT COUNT(*) as total FROM Ruangan";
                if ($kondisiRuangan) {
                    $countQuery .= " WHERE kondisiRuangan = ?";
                }
                $countStmt = sqlsrv_query($conn, $countQuery, $kondisiRuangan ? [$kondisiRuangan] : []);
                if ($countStmt) {
                    $countRow = sqlsrv_fetch_array($countStmt, SQLSRV_FETCH_ASSOC);
                    $totalCount = $countRow['total'];
                }

                // Query untuk data dengan pagination
                $query = "SELECT idRuangan, namaRuangan, kondisiRuangan, ketersediaan FROM Ruangan";
                $params = [];
                if ($kondisiRuangan) {
                    $query .= " WHERE kondisiRuangan = ?";
                    $params[] = $kondisiRuangan;
                }
                $query .= " ORDER BY idRuangan ASC OFFSET ? ROWS FETCH NEXT ? ROWS ONLY";
                $params[] = $offset;
                $params[] = $itemsPerPage;
                $stmt = sqlsrv_query($conn, $query, $params);
                break;

            case 'peminjamSeringMeminjam':
                if ($tahun && $bulan === null) {
                    // Query untuk menghitung total records (tahunan)
                    $countQuery = "
                        SELECT COUNT(*) as total FROM (
                            SELECT
                                CASE WHEN P.nim IS NOT NULL THEN P.nim ELSE P.npk END AS IDPeminjam
                            FROM (
                                SELECT idPeminjamanBrg AS id_peminjaman, nim, npk FROM Peminjaman_Barang WHERE YEAR(tglPeminjamanBrg) = ?
                                UNION ALL
                                SELECT idPeminjamanRuangan AS id_peminjaman, nim, npk FROM Peminjaman_Ruangan WHERE YEAR(tglPeminjamanRuangan) = ?
                            ) AS P
                            LEFT JOIN Mahasiswa AS M ON P.nim = M.nim 
                            LEFT JOIN Karyawan AS K ON P.npk = K.npk 
                            GROUP BY 
                                CASE WHEN P.nim IS NOT NULL THEN P.nim ELSE P.npk END,
                                CASE WHEN P.nim IS NOT NULL THEN M.nama ELSE K.nama END, 
                                CASE WHEN P.nim IS NOT NULL THEN 'Mahasiswa' ELSE 'Karyawan' END
                        ) AS subquery
                    ";
                    $countStmt = sqlsrv_query($conn, $countQuery, [$tahun, $tahun]);
                    if ($countStmt) {
                        $countRow = sqlsrv_fetch_array($countStmt, SQLSRV_FETCH_ASSOC);
                        $totalCount = $countRow['total'];
                    }

                    // Query untuk data dengan pagination (tahunan)
                    $query = "
                        SELECT
                            CASE WHEN P.nim IS NOT NULL THEN P.nim ELSE P.npk END AS IDPeminjam,
                            CASE WHEN P.nim IS NOT NULL THEN M.nama ELSE K.nama END AS NamaPeminjam, 
                            CASE WHEN P.nim IS NOT NULL THEN 'Mahasiswa' ELSE 'Karyawan' END AS JenisPeminjam,
                            COUNT(P.id_peminjaman) AS JumlahPeminjaman
                        FROM (
                            SELECT idPeminjamanBrg AS id_peminjaman, nim, npk FROM Peminjaman_Barang WHERE YEAR(tglPeminjamanBrg) = ?
                            UNION ALL
                            SELECT idPeminjamanRuangan AS id_peminjaman, nim, npk FROM Peminjaman_Ruangan WHERE YEAR(tglPeminjamanRuangan) = ?
                        ) AS P
                        LEFT JOIN Mahasiswa AS M ON P.nim = M.nim 
                        LEFT JOIN Karyawan AS K ON P.npk = K.npk 
                        GROUP BY 
                            CASE WHEN P.nim IS NOT NULL THEN P.nim ELSE P.npk END,
                            CASE WHEN P.nim IS NOT NULL THEN M.nama ELSE K.nama END, 
                            CASE WHEN P.nim IS NOT NULL THEN 'Mahasiswa' ELSE 'Karyawan' END
                        ORDER BY JumlahPeminjaman DESC, NamaPeminjam ASC
                        OFFSET ? ROWS FETCH NEXT ? ROWS ONLY
                    ";
                    $params = [$tahun, $tahun, $offset, $itemsPerPage];
                    $stmt = sqlsrv_query($conn, $query, $params);
                } elseif ($tahun && $bulan !== null) {
                    // Query untuk menghitung total records (bulanan)
                    $countQuery = "
                        SELECT COUNT(*) as total FROM (
                            SELECT
                                CASE WHEN P.nim IS NOT NULL THEN P.nim ELSE P.npk END AS IDPeminjam
                            FROM (
                                SELECT idPeminjamanBrg AS id_peminjaman, nim, npk FROM Peminjaman_Barang WHERE YEAR(tglPeminjamanBrg) = ? AND MONTH(tglPeminjamanBrg) = ?
                                UNION ALL
                                SELECT idPeminjamanRuangan AS id_peminjaman, nim, npk FROM Peminjaman_Ruangan WHERE YEAR(tglPeminjamanRuangan) = ? AND MONTH(tglPeminjamanRuangan) = ?
                            ) AS P
                            LEFT JOIN Mahasiswa AS M ON P.nim = M.nim 
                            LEFT JOIN Karyawan AS K ON P.npk = K.npk 
                            GROUP BY 
                                CASE WHEN P.nim IS NOT NULL THEN P.nim ELSE P.npk END,
                                CASE WHEN P.nim IS NOT NULL THEN M.nama ELSE K.nama END, 
                                CASE WHEN P.nim IS NOT NULL THEN 'Mahasiswa' ELSE 'Karyawan' END
                        ) AS subquery
                    ";
                    $countStmt = sqlsrv_query($conn, $countQuery, [$tahun, $bulan, $tahun, $bulan]);
                    if ($countStmt) {
                        $countRow = sqlsrv_fetch_array($countStmt, SQLSRV_FETCH_ASSOC);
                        $totalCount = $countRow['total'];
                    }

                    // Query untuk data dengan pagination (bulanan)
                    $query = "
                        SELECT
                            CASE WHEN P.nim IS NOT NULL THEN P.nim ELSE P.npk END AS IDPeminjam,
                            CASE WHEN P.nim IS NOT NULL THEN M.nama ELSE K.nama END AS NamaPeminjam, 
                            CASE WHEN P.nim IS NOT NULL THEN 'Mahasiswa' ELSE 'Karyawan' END AS JenisPeminjam,
                            COUNT(P.id_peminjaman) AS JumlahPeminjaman
                        FROM (
                            SELECT idPeminjamanBrg AS id_peminjaman, nim, npk FROM Peminjaman_Barang WHERE YEAR(tglPeminjamanBrg) = ? AND MONTH(tglPeminjamanBrg) = ?
                            UNION ALL
                            SELECT idPeminjamanRuangan AS id_peminjaman, nim, npk FROM Peminjaman_Ruangan WHERE YEAR(tglPeminjamanRuangan) = ? AND MONTH(tglPeminjamanRuangan) = ?
                        ) AS P
                        LEFT JOIN Mahasiswa AS M ON P.nim = M.nim 
                        LEFT JOIN Karyawan AS K ON P.npk = K.npk 
                        GROUP BY 
                            CASE WHEN P.nim IS NOT NULL THEN P.nim ELSE P.npk END,
                            CASE WHEN P.nim IS NOT NULL THEN M.nama ELSE K.nama END, 
                            CASE WHEN P.nim IS NOT NULL THEN 'Mahasiswa' ELSE 'Karyawan' END
                        ORDER BY JumlahPeminjaman DESC, NamaPeminjam ASC
                        OFFSET ? ROWS FETCH NEXT ? ROWS ONLY
                    ";
                    $params = [$tahun, $bulan, $tahun, $bulan, $offset, $itemsPerPage];
                    $stmt = sqlsrv_query($conn, $query, $params);
                }
                break;

                case 'barangSeringDipinjam':
                    if ($tahun && $bulan === null) {
                        // Query untuk menghitung total records (tahunan)
                        $countQuery = "
                            SELECT COUNT(*) as total FROM (
                                SELECT B.namaBarang
                                FROM Peminjaman_Barang PB
                                INNER JOIN Barang B ON PB.idBarang = B.idBarang
                                WHERE YEAR(PB.tglPeminjamanBrg) = ?
                                GROUP BY B.namaBarang
                            ) AS subquery
                        ";
                        $countStmt = sqlsrv_query($conn, $countQuery, [$tahun]);
                        if ($countStmt) {
                            $countRow = sqlsrv_fetch_array($countStmt, SQLSRV_FETCH_ASSOC);
                            $totalCount = $countRow['total'];
                        }

                        // Query untuk data dengan pagination (tahunan)
                        $query = "
                            SELECT 
                                B.namaBarang,
                                SUM(PB.jumlahBrg) AS TotalKuantitasDipinjam
                            FROM Peminjaman_Barang PB
                            INNER JOIN Barang B ON PB.idBarang = B.idBarang
                            WHERE YEAR(PB.tglPeminjamanBrg) = ?
                            GROUP BY B.namaBarang
                            ORDER BY TotalKuantitasDipinjam DESC, B.namaBarang ASC
                            OFFSET ? ROWS FETCH NEXT ? ROWS ONLY
                        ";
                        $params = [$tahun, $offset, $itemsPerPage];
                        $stmt = sqlsrv_query($conn, $query, $params);
                    } elseif ($tahun && $bulan !== null) {
                        // Query untuk menghitung total records (bulanan)
                        $countQuery = "
                            SELECT COUNT(*) as total FROM (
                                SELECT B.namaBarang
                                FROM Peminjaman_Barang PB
                                INNER JOIN Barang B ON PB.idBarang = B.idBarang
                                WHERE YEAR(PB.tglPeminjamanBrg) = ? AND MONTH(PB.tglPeminjamanBrg) = ?
                                GROUP BY B.namaBarang
                            ) AS subquery
                        ";
                        $countStmt = sqlsrv_query($conn, $countQuery, [$tahun, $bulan]);
                        if ($countStmt) {
                            $countRow = sqlsrv_fetch_array($countStmt, SQLSRV_FETCH_ASSOC);
                            $totalCount = $countRow['total'];
                        }

                        // Query untuk data dengan pagination (bulanan)
                        $query = "
                            SELECT 
                                B.namaBarang,
                                SUM(PB.jumlahBrg) AS TotalKuantitasDipinjam
                            FROM Peminjaman_Barang PB
                            INNER JOIN Barang B ON PB.idBarang = B.idBarang
                            WHERE YEAR(PB.tglPeminjamanBrg) = ? AND MONTH(PB.tglPeminjamanBrg) = ?
                            GROUP BY B.namaBarang
                            ORDER BY TotalKuantitasDipinjam DESC, B.namaBarang ASC
                            OFFSET ? ROWS FETCH NEXT ? ROWS ONLY
                        ";
                        $params = [$tahun, $bulan, $offset, $itemsPerPage];
                        $stmt = sqlsrv_query($conn, $query, $params);
                    }
                    break;

            case 'ruanganSeringDipinjam':
                if ($tahun && $bulan === null) {
                    // Query untuk menghitung total records (tahunan)
                    $countQuery = "
                        SELECT COUNT(*) as total FROM (
                            SELECT R.namaRuangan
                            FROM Peminjaman_Ruangan PR
                            INNER JOIN Ruangan R ON PR.idRuangan = R.idRuangan
                            WHERE YEAR(PR.tglPeminjamanRuangan) = ?
                            GROUP BY R.namaRuangan
                        ) AS subquery
                    ";
                    $countStmt = sqlsrv_query($conn, $countQuery, [$tahun]);
                    if ($countStmt) {
                        $countRow = sqlsrv_fetch_array($countStmt, SQLSRV_FETCH_ASSOC);
                        $totalCount = $countRow['total'];
                    }

                    // Query untuk data dengan pagination (tahunan)
                    $query = "
                        SELECT 
                            R.namaRuangan,
                            COUNT(PR.idPeminjamanRuangan) AS JumlahDipinjam
                        FROM Peminjaman_Ruangan PR
                        INNER JOIN Ruangan R ON PR.idRuangan = R.idRuangan
                        WHERE YEAR(PR.tglPeminjamanRuangan) = ?
                        GROUP BY R.namaRuangan
                        ORDER BY JumlahDipinjam DESC, R.namaRuangan ASC
                        OFFSET ? ROWS FETCH NEXT ? ROWS ONLY
                    ";
                    $params = [$tahun, $offset, $itemsPerPage];
                    $stmt = sqlsrv_query($conn, $query, $params);
                } elseif ($tahun && $bulan !== null) {
                    // Query untuk menghitung total records (bulanan)
                    $countQuery = "
                        SELECT COUNT(*) as total FROM (
                            SELECT R.namaRuangan
                            FROM Peminjaman_Ruangan PR
                            INNER JOIN Ruangan R ON PR.idRuangan = R.idRuangan
                            WHERE YEAR(PR.tglPeminjamanRuangan) = ? AND MONTH(PR.tglPeminjamanRuangan) = ?
                            GROUP BY R.namaRuangan
                        ) AS subquery
                    ";
                    $countStmt = sqlsrv_query($conn, $countQuery, [$tahun, $bulan]);
                    if ($countStmt) {
                        $countRow = sqlsrv_fetch_array($countStmt, SQLSRV_FETCH_ASSOC);
                        $totalCount = $countRow['total'];
                    }

                    // Query untuk data dengan pagination (bulanan)
                    $query = "
                        SELECT 
                            R.namaRuangan,
                            COUNT(PR.idPeminjamanRuangan) AS JumlahDipinjam
                        FROM Peminjaman_Ruangan PR
                        INNER JOIN Ruangan R ON PR.idRuangan = R.idRuangan
                        WHERE YEAR(PR.tglPeminjamanRuangan) = ? AND MONTH(PR.tglPeminjamanRuangan) = ?
                        GROUP BY R.namaRuangan
                        ORDER BY JumlahDipinjam DESC, R.namaRuangan ASC
                        OFFSET ? ROWS FETCH NEXT ? ROWS ONLY
                    ";
                    $params = [$tahun, $bulan, $offset, $itemsPerPage];
                    $stmt = sqlsrv_query($conn, $query, $params);
                }
                break;
        }

        // Jika query berhasil dieksekusi, ambil data dan kirim respons.
        if ($stmt) {
            $data = [];
            while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
                $data[] = $row;
            }

            // Hitung total pages
            $totalPages = ceil($totalCount / $itemsPerPage);

            $response = [
                'status' => 'success',
                'message' => 'Data berhasil diambil.',
                'data' => $data,
                'pagination' => [
                    'currentPage' => $page,
                    'totalPages' => $totalPages,
                    'totalItems' => $totalCount,
                    'itemsPerPage' => $itemsPerPage
                ]
            ];
        } else {
            $response = [
                'status' => 'error',
                'message' => 'Gagal mengeksekusi query: ' . sqlsrv_errors(),
                'data' => []
            ];
        }
    } catch (Exception $e) {
        $response = [
            'status' => 'error',
            'message' => 'Terjadi kesalahan: ' . $e->getMessage(),
            'data' => []
        ];
    }
} else {
    $response = [
        'status' => 'error',
        'message' => 'Koneksi database gagal atau jenis laporan tidak diberikan.',
        'data' => []
    ];
}

// Kirim respons dalam format JSON.
echo json_encode($response);
