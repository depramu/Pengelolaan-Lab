<?php
// /CRUD/Laporan/export_laporan_excel_pic.php

// Memulai session dan memanggil file-file penting
// Asumsi init.php menangani koneksi dan otorisasi dasar
require_once __DIR__ . '/../../function/init.php';

// Ambil parameter dari URL
$jenisLaporan = isset($_GET['jenisLaporan']) ? $_GET['jenisLaporan'] : null;
$bulan = (isset($_GET['bulan']) && $_GET['bulan'] !== '' && $_GET['bulan'] !== '0') ? (int)$_GET['bulan'] : null;
$tahun = isset($_GET['tahun']) && $_GET['tahun'] !== '' ? (int)$_GET['tahun'] : null;
$lokasiBarang = isset($_GET['lokasiBarang']) && $_GET['lokasiBarang'] !== '' ? $_GET['lokasiBarang'] : null;
$kondisiRuangan = isset($_GET['kondisiRuangan']) && $_GET['kondisiRuangan'] !== '' ? $_GET['kondisiRuangan'] : null;
$mode = isset($_GET['mode']) ? $_GET['mode'] : 'preview'; // 'preview' atau 'download'

if (!$jenisLaporan) {
    die("Error: Jenis laporan tidak ditentukan.");
}

// === LOGIKA UNTUK MEMBUAT JUDUL, NAMA FILE, DAN HEADERS ===
$reportTitles = [
    'dataBarang' => 'Data Barang',
    'dataRuangan' => 'Data Ruangan',
    'peminjamSeringMeminjam' => 'Peminjam yang Sering Meminjam',
    'barangSeringDipinjam' => 'Barang yang Sering Dipinjam',
    'ruanganSeringDipinjam' => 'Ruangan yang Sering Dipinjam'
];

$headersMap = [
    'dataBarang' => ["No", "Nama Barang", "Stok", "Lokasi"],
    'dataRuangan' => ["No", "Nama Ruangan", "Kondisi", "Ketersediaan"],
    'peminjamSeringMeminjam' => ["No", "Nama Peminjam", "Jenis", "Jumlah Peminjaman"],
    'barangSeringDipinjam' => ["No", "Nama Barang", "Total Kuantitas Dipinjam"],
    'ruanganSeringDipinjam' => ["No", "Nama Ruangan", "Jumlah Dipinjam"]
];

$keysMap = [
    'dataBarang' => ["namaBarang", "stokBarang", "lokasiBarang"],
    'dataRuangan' => ["namaRuangan", "kondisiRuangan", "ketersediaan"],
    'peminjamSeringMeminjam' => ["NamaPeminjam", "JenisPeminjam", "JumlahPeminjaman"],
    'barangSeringDipinjam' => ["namaBarang", "TotalKuantitasDipinjam"],
    'ruanganSeringDipinjam' => ["namaRuangan", "JumlahDipinjam"]
];

$reportTitle = $reportTitles[$jenisLaporan] ?? 'Laporan';
$filename = "Laporan_" . $jenisLaporan;

// Tambahkan filter ke judul dan nama file
$filterInfo = [];
if ($lokasiBarang) {
    $filterInfo[] = "Lokasi: " . $lokasiBarang;
    $filename .= "_Lokasi_" . str_replace([' ', '/', '\\'], ['_', '_', '_'], $lokasiBarang);
}
if ($kondisiRuangan) {
    $filterInfo[] = "Kondisi: " . $kondisiRuangan;
    $filename .= "_Kondisi_" . str_replace([' ', '/', '\\'], ['_', '_', '_'], $kondisiRuangan);
}

// Tambahkan bulan dan tahun ke judul dan nama file jika ada
if ($bulan && $tahun) {
    // Array untuk mengubah nomor bulan menjadi nama bulan dalam Bahasa Indonesia
    $namaBulan = [
        1 => 'Januari',
        2 => 'Februari',
        3 => 'Maret',
        4 => 'April',
        5 => 'Mei',
        6 => 'Juni',
        7 => 'Juli',
        8 => 'Agustus',
        9 => 'September',
        10 => 'Oktober',
        11 => 'November',
        12 => 'Desember'
    ];
    $reportTitle .= " - " . $namaBulan[$bulan] . " " . $tahun;
    $filename .= "_" . $bulan . "_" . $tahun;
} elseif ($tahun) {
    $reportTitle .= " - Tahun " . $tahun;
    $filename .= "_Tahun_" . $tahun;
}

// Tambahkan filter info ke judul jika ada
if (!empty($filterInfo)) {
    $reportTitle .= " (" . implode(", ", $filterInfo) . ")";
}

$filename .= ".xls"; // Gunakan ekstensi .xls

// === LOGIKA PENGAMBILAN DATA (Diadaptasi dari get_laporan_data.php) ===
$dataResult = [];
if ($conn) {
    // Query dan parameter disesuaikan berdasarkan jenis laporan
    $query = '';
    $params = [];
    switch ($jenisLaporan) {
        case 'dataBarang':
            $query = "SELECT namaBarang, stokBarang, lokasiBarang FROM Barang WHERE isDeleted = 0";
            if ($lokasiBarang) {
                $query .= " AND lokasiBarang = ?";
                $params[] = $lokasiBarang;
            }
            $query .= " ORDER BY namaBarang ASC";
            break;
        case 'dataRuangan':
            $query = "SELECT namaRuangan, kondisiRuangan, ketersediaan FROM Ruangan";
            if ($kondisiRuangan) {
                $query .= " WHERE kondisiRuangan = ?";
                $params[] = $kondisiRuangan;
            }
            $query .= " ORDER BY namaRuangan ASC";
            break;
        case 'peminjamSeringMeminjam':
            if ($tahun && $bulan === null) {
                // Query tahunan
                $query = "
                    SELECT
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
                        CASE WHEN P.nim IS NOT NULL THEN M.nama WHEN P.npk IS NOT NULL THEN K.nama END, 
                        CASE WHEN P.nim IS NOT NULL THEN 'Mahasiswa' WHEN P.npk IS NOT NULL THEN 'Karyawan' END
                    ORDER BY JumlahPeminjaman DESC, NamaPeminjam ASC;
                ";
                $params = [$tahun, $tahun];
            } elseif ($tahun && $bulan !== null) {
                // Query bulanan
                $query = "
                    SELECT
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
                        CASE WHEN P.nim IS NOT NULL THEN M.nama WHEN P.npk IS NOT NULL THEN K.nama END, 
                        CASE WHEN P.nim IS NOT NULL THEN 'Mahasiswa' WHEN P.npk IS NOT NULL THEN 'Karyawan' END
                    ORDER BY JumlahPeminjaman DESC, NamaPeminjam ASC;
                ";
                $params = [$tahun, $bulan, $tahun, $bulan];
            }
            break;
        case 'barangSeringDipinjam':
            if ($tahun) {
                if ($bulan === null) {
                    $query = "
                        SELECT B.namaBarang, SUM(PB.kuantitasPeminjamanBrg) AS TotalKuantitasDipinjam
                        FROM Peminjaman_Barang AS PB INNER JOIN Barang AS B ON PB.idBarang = B.idBarang
                        WHERE YEAR(PB.tglPeminjamanBrg) = ?
                        GROUP BY B.namaBarang ORDER BY TotalKuantitasDipinjam DESC, B.namaBarang ASC;
                    ";
                    $params = [$tahun];
                } else {
                    $query = "
                        SELECT B.namaBarang, SUM(PB.kuantitasPeminjamanBrg) AS TotalKuantitasDipinjam
                        FROM Peminjaman_Barang AS PB INNER JOIN Barang AS B ON PB.idBarang = B.idBarang
                        WHERE YEAR(PB.tglPeminjamanBrg) = ? AND MONTH(PB.tglPeminjamanBrg) = ?
                        GROUP BY B.namaBarang ORDER BY TotalKuantitasDipinjam DESC, B.namaBarang ASC;
                    ";
                    $params = [$tahun, $bulan];
                }
            } else {
                // Tahun wajib dipilih
                echo "<p style='color:red;font-weight:bold;'>Tahun wajib dipilih untuk laporan ini.</p>";
                exit;
            }
            break;
        case 'ruanganSeringDipinjam':
            if ($tahun && $bulan === null) {
                $query = "
                    SELECT R.namaRuangan, COUNT(PR.idPeminjamanRuangan) AS JumlahDipinjam
                    FROM Peminjaman_Ruangan AS PR INNER JOIN Ruangan AS R ON PR.idRuangan = R.idRuangan
                    WHERE YEAR(PR.tglPeminjamanRuangan) = ?
                    GROUP BY R.namaRuangan ORDER BY JumlahDipinjam DESC, R.namaRuangan ASC;
                ";
                $params = [$tahun];
            } elseif ($tahun && $bulan !== null) {
                $query = "
                    SELECT R.namaRuangan, COUNT(PR.idPeminjamanRuangan) AS JumlahDipinjam
                    FROM Peminjaman_Ruangan AS PR INNER JOIN Ruangan AS R ON PR.idRuangan = R.idRuangan
                    WHERE YEAR(PR.tglPeminjamanRuangan) = ? AND MONTH(PR.tglPeminjamanRuangan) = ?
                    GROUP BY R.namaRuangan ORDER BY JumlahDipinjam DESC, R.namaRuangan ASC;
                ";
                $params = [$tahun, $bulan];
            }
            break;
    }

    // Eksekusi query
    if ($query) {
        $stmt = sqlsrv_query($conn, $query, $params);
        if ($stmt) {
            while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
                $dataResult[] = $row;
            }
        } else {
            echo "<p style='color:red;font-weight:bold;'>Error: Gagal mengeksekusi query.</p>";
            exit;
        }
    }
}

// === GENERATE EXCEL FILE ===
if (!empty($dataResult)) {
    if ($mode === 'download') {
        // Set header untuk download file Excel
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Cache-Control: max-age=0');
    } else {
        // Mode preview - tampilkan HTML dengan styling
        echo '<!DOCTYPE html>';
        echo '<html>';
        echo '<head>';
        echo '<title>Preview Laporan</title>';
        echo '<style>';
        echo 'body { font-family: Arial, sans-serif; margin: 20px; }';
        echo 'table { border-collapse: collapse; width: 100%; margin-top: 20px; }';
        echo 'th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }';
        echo 'th { background-color: #f2f2f2; font-weight: bold; }';
        echo '.title { font-size: 18px; font-weight: bold; text-align: center; margin-bottom: 20px; }';
        echo '.download-btn { background-color: #4CAF50; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; display: inline-block; margin: 10px 0; }';
        echo '.download-btn:hover { background-color: #45a049; }';
        echo '</style>';
        echo '</head>';
        echo '<body>';

        // Download button
        $downloadUrl = $_SERVER['REQUEST_URI'];
        if (strpos($downloadUrl, 'mode=preview') !== false) {
            $downloadUrl = str_replace('mode=preview', 'mode=download', $downloadUrl);
        } else {
            $downloadUrl .= '&mode=download';
        }
        echo '<a href="' . $downloadUrl . '" class="download-btn">Download Excel</a>';

        echo '<div class="title">' . $reportTitle . '</div>';
    }

    // Output HTML table yang bisa dibuka di Excel
    echo '<table border="1">';

    // Header dengan judul laporan (hanya untuk download mode)
    if ($mode === 'download') {
        echo '<tr><td colspan="' . count($headersMap[$jenisLaporan]) . '" style="font-weight:bold;text-align:center;font-size:14px;">' . $reportTitle . '</td></tr>';
        echo '<tr><td colspan="' . count($headersMap[$jenisLaporan]) . '"></td></tr>';
    }

    // Header kolom
    echo '<tr>';
    foreach ($headersMap[$jenisLaporan] as $header) {
        echo '<td style="font-weight:bold;background-color:#f0f0f0;">' . $header . '</td>';
    }
    echo '</tr>';

    // Data rows
    foreach ($dataResult as $index => $row) {
        echo '<tr>';
        echo '<td>' . ($index + 1) . '</td>'; // Nomor urut
        foreach ($keysMap[$jenisLaporan] as $key) {
            echo '<td>' . ($row[$key] ?? '') . '</td>';
        }
        echo '</tr>';
    }

    echo '</table>';

    if ($mode === 'preview') {
        echo '</body>';
        echo '</html>';
    }
} else {
    if ($mode === 'preview') {
        echo '<!DOCTYPE html>';
        echo '<html>';
        echo '<head>';
        echo '<title>Preview Laporan</title>';
        echo '<style>';
        echo 'body { font-family: Arial, sans-serif; margin: 20px; }';
        echo '.error { color: red; font-weight: bold; }';
        echo '</style>';
        echo '</head>';
        echo '<body>';
        echo '<p class="error">Tidak ada data untuk diekspor.</p>';
        echo '</body>';
        echo '</html>';
    } else {
        echo "<p style='color:red;font-weight:bold;'>Tidak ada data untuk diekspor.</p>";
    }
}
