<?php
// /CRUD/Laporan/export_laporan_excel_pic.php

// Memulai session dan memanggil file-file penting
// Asumsi init.php menangani koneksi dan otorisasi dasar
require_once __DIR__ . '/../../function/init.php';

// Ambil parameter dari URL
$jenisLaporan = isset($_GET['jenisLaporan']) ? $_GET['jenisLaporan'] : null;
$bulan = isset($_GET['bulan']) && $_GET['bulan'] !== '' ? (int)$_GET['bulan'] : null;
$tahun = isset($_GET['tahun']) && $_GET['tahun'] !== '' ? (int)$_GET['tahun'] : null;

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
    'dataBarang' => ["ID", "Nama Barang", "Stok", "Lokasi"],
    'dataRuangan' => ["ID", "Nama Ruangan", "Kondisi", "Ketersediaan"],
    'peminjamSeringMeminjam' => ["ID Peminjam", "Nama Peminjam", "Jenis", "Jumlah Peminjaman"],
    'barangSeringDipinjam' => ["ID Barang", "Nama Barang", "Total Kuantitas Dipinjam"],
    'ruanganSeringDipinjam' => ["ID Ruangan", "Nama Ruangan", "Jumlah Dipinjam"]
];

$keysMap = [
    'dataBarang' => ["idBarang", "namaBarang", "stokBarang", "lokasiBarang"],
    'dataRuangan' => ["idRuangan", "namaRuangan", "kondisiRuangan", "ketersediaan"],
    'peminjamSeringMeminjam' => ["IDPeminjam", "NamaPeminjam", "JenisPeminjam", "JumlahPeminjaman"],
    'barangSeringDipinjam' => ["idBarang", "namaBarang", "TotalKuantitasDipinjam"],
    'ruanganSeringDipinjam' => ["idRuangan", "namaRuangan", "JumlahDipinjam"]
];

$reportTitle = $reportTitles[$jenisLaporan] ?? 'Laporan';
$filename = "Laporan_" . $jenisLaporan;

// Tambahkan bulan dan tahun ke judul dan nama file jika ada
if ($bulan && $tahun) {
    // Array untuk mengubah nomor bulan menjadi nama bulan dalam Bahasa Indonesia
    $namaBulan = [
        1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April', 5 => 'Mei', 6 => 'Juni',
        7 => 'Juli', 8 => 'Agustus', 9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
    ];
    $reportTitle .= " - " . $namaBulan[$bulan] . " " . $tahun;
    $filename .= "_" . $bulan . "_" . $tahun;
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
            $query = "SELECT idBarang, namaBarang, stokBarang, lokasiBarang FROM Barang ORDER BY idBarang ASC";
            break;
        case 'dataRuangan':
            $query = "SELECT idRuangan, namaRuangan, kondisiRuangan, ketersediaan FROM Ruangan ORDER BY idRuangan ASC";
            break;
        case 'peminjamSeringMeminjam':
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
            break;
        case 'barangSeringDipinjam':
            $query = "
                SELECT PB.idBarang, B.namaBarang, SUM(PB.jumlahBrg) AS TotalKuantitasDipinjam
                FROM Peminjaman_Barang AS PB INNER JOIN Barang AS B ON PB.idBarang = B.idBarang
                WHERE YEAR(PB.tglPeminjamanBrg) = ? AND MONTH(PB.tglPeminjamanBrg) = ?
                GROUP BY PB.idBarang, B.namaBarang ORDER BY TotalKuantitasDipinjam DESC, B.namaBarang ASC;
            ";
            $params = [$tahun, $bulan];
            break;
        case 'ruanganSeringDipinjam':
            $query = "
                SELECT PR.idRuangan, R.namaRuangan, COUNT(PR.idpeminjamanRuangan) AS JumlahDipinjam
                FROM Peminjaman_Ruangan AS PR INNER JOIN Ruangan AS R ON PR.idRuangan = R.idRuangan
                WHERE YEAR(PR.tglPeminjamanRuangan) = ? AND MONTH(PR.tglPeminjamanRuangan) = ?
                GROUP BY PR.idRuangan, R.namaRuangan ORDER BY JumlahDipinjam DESC, R.namaRuangan ASC;
            ";
            $params = [$tahun, $bulan];
            break;
    }

    $stmt = sqlsrv_query($conn, $query, $params);
    if ($stmt) {
        while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
            $dataResult[] = $row;
        }
        sqlsrv_free_stmt($stmt);
    }
}

// === LOGIKA UNTUK MEMBUAT SUMMARY TEXT (Diadaptasi dari main.js) ===
$summaryText = '';
if (!empty($dataResult)) {
    switch ($jenisLaporan) {
        case 'dataBarang':
            $totalJenis = count($dataResult);
            $totalStok = array_reduce($dataResult, function ($sum, $item) {
                return $sum + (int)$item['stokBarang'];
            }, 0);
            $summaryText = "Total Jenis Barang: {$totalJenis}, Total Stok Barang: {$totalStok}";
            break;
        case 'dataRuangan':
            $totalJenis = count($dataResult);
            $totalTersedia = count(array_filter($dataResult, function ($item) {
                return strtolower($item['ketersediaan']) === 'tersedia';
            }));
            $summaryText = "Total Jenis Ruangan: {$totalJenis}, Total Ruangan yang Tersedia: {$totalTersedia}";
            break;
        case 'peminjamSeringMeminjam':
            $totalPeminjaman = array_reduce($dataResult, function ($sum, $item) {
                return $sum + (int)$item['JumlahPeminjaman'];
            }, 0);
            $summaryText = "Total Peminjaman oleh Peminjam Teratas: {$totalPeminjaman}";
            break;
        case 'barangSeringDipinjam':
            $totalKuantitas = array_reduce($dataResult, function ($sum, $item) {
                return $sum + (int)$item['TotalKuantitasDipinjam'];
            }, 0);
            $summaryText = "Total Kuantitas Barang yang Dipinjam: {$totalKuantitas}";
            break;
        case 'ruanganSeringDipinjam':
            $totalDipinjam = array_reduce($dataResult, function ($sum, $item) {
                return $sum + (int)$item['JumlahDipinjam'];
            }, 0);
            $summaryText = "Total Ruangan yang Dipinjam: {$totalDipinjam}";
            break;
    }
}


// === OUTPUT KE EXCEL ===

// Cek mode operasi: 'preview' (default) atau 'download'
$mode = isset($_GET['mode']) ? $_GET['mode'] : 'preview';

if ($mode === 'download') {
    // Jika mode adalah download, kirim header untuk mengunduh file
    header("Content-Type: application/vnd.ms-excel");
    header("Content-Disposition: attachment; filename=\"{$filename}\"");
} else {
    // Jika mode adalah preview, kita akan membungkusnya dengan struktur HTML lengkap
    // agar bisa menampilkan halaman dengan benar, termasuk judul tab browser.
    echo "<!DOCTYPE html>";
    echo "<html lang='id'>";
    echo "<head>";
    echo "<meta charset='UTF-8'>";
    echo "<title>Preview Laporan - " . htmlspecialchars($reportTitle) . "</title>";
    // Kita bisa menambahkan sedikit style agar terlihat lebih baik
    echo "<style>
            body { font-family: sans-serif; margin: 20px; }
            table { border-collapse: collapse; width: 100%; }
            th, td { border: 1px solid #dddddd; text-align: left; padding: 8px; }
            th { background-color: #f2f2f2; }
            h2, p { margin-bottom: 10px; }
            .download-btn {
                display: inline-block;
                padding: 10px 15px;
                margin-top: 20px;
                background-color: #28a745; /* Warna hijau success */
                color: white;
                text-decoration: none;
                border-radius: 5px;
                font-weight: bold;
            }
          </style>";
    echo "</head>";
    echo "<body>";
}

// === BAGIAN INI SAMA UNTUK KEDUA MODE ===
// Cetak Judul, Ringkasan, dan Tabel Laporan
echo "<h2>" . htmlspecialchars($reportTitle) . "</h2>";
echo "<p>" . htmlspecialchars($summaryText) . "</p>";

// Buat tabel dengan border agar rapi
echo "<table border='1'>";
echo "<thead><tr>";
foreach ($headersMap[$jenisLaporan] as $header) {
    echo "<th>" . htmlspecialchars($header) . "</th>";
}
echo "</tr></thead>";
echo "<tbody>";
if (empty($dataResult)) {
    echo "<tr><td colspan='" . count($headersMap[$jenisLaporan]) . "'>Tidak ada data ditemukan.</td></tr>";
} else {
    $keys = $keysMap[$jenisLaporan];
    foreach ($dataResult as $row) {
        echo "<tr>";
        foreach ($keys as $key) {
            echo "<td>" . htmlspecialchars($row[$key] ?? '') . "</td>";
        }
        echo "</tr>";
    }
}
echo "</tbody>";
echo "</table>";
// === AKHIR BAGIAN YANG SAMA ===


// Jika dalam mode preview, tambahkan tombol download
if ($mode === 'preview') {
    // Tombol ini akan mengarah ke URL yang sama, tetapi dengan tambahan &mode=download
    $downloadUrl = htmlspecialchars($_SERVER['REQUEST_URI'] . '&mode=download');
    echo "<a href='{$downloadUrl}' class='download-btn'>Download sebagai Excel (.xls)</a>";
    echo "</body>";
    echo "</html>";
}

exit;