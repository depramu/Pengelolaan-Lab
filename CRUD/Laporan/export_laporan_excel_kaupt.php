<?php
// /CRUD/Laporan/export_laporan_excel_kaupt.php

// Memulai session dan memanggil file-file penting
require_once __DIR__ . '/../../function/init.php';

// ! PENTING: Pastikan hanya role 'Kepala UPT' yang bisa mengakses script ini
// Ganti 'Kepala UPT' jika nama role di sistem Anda berbeda.
authorize_role('Kepala UPT'); 

// Ambil parameter dari URL
$jenisLaporan = isset($_GET['jenisLaporan']) ? $_GET['jenisLaporan'] : null;
$bulan = (isset($_GET['bulan']) && $_GET['bulan'] !== '' && $_GET['bulan'] !== '0') ? (int)$_GET['bulan'] : null;
$tahun = isset($_GET['tahun']) && $_GET['tahun'] !== '' ? (int)$_GET['tahun'] : null;

if (!$jenisLaporan) {
    die("Error: Jenis laporan tidak ditentukan.");
}

// === Dari sini ke bawah, kodenya 99% SAMA DENGAN VERSI PIC ===
// Kita tidak perlu mengubahnya karena jenis laporan dan datanya sama.

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

if ($bulan && $tahun) {
    $namaBulan = [1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April', 5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus', 9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'];
    $reportTitle .= " - " . $namaBulan[$bulan] . " " . $tahun;
    $filename .= "_" . $bulan . "_" . $tahun;
}
$filename .= ".xls";

// === LOGIKA PENGAMBILAN DATA (Identik dengan PIC) ===
$dataResult = [];
if ($conn) {
    $query = '';
    $params = [];
    switch ($jenisLaporan) {
        case 'dataBarang':
            $query = "SELECT namaBarang, stokBarang, lokasiBarang FROM Barang ORDER BY namaBarang ASC";
            break;
        case 'dataRuangan':
            $query = "SELECT namaRuangan, kondisiRuangan, ketersediaan FROM Ruangan ORDER BY namaRuangan ASC";
            break;
        case 'peminjamSeringMeminjam':
            if ($tahun && $bulan === null) {
                $query = "SELECT CASE WHEN P.nim IS NOT NULL THEN M.nama WHEN P.npk IS NOT NULL THEN K.nama END AS NamaPeminjam, CASE WHEN P.nim IS NOT NULL THEN 'Mahasiswa' WHEN P.npk IS NOT NULL THEN 'Karyawan' END AS JenisPeminjam, COUNT(P.id_peminjaman) AS JumlahPeminjaman FROM (SELECT idPeminjamanBrg AS id_peminjaman, nim, npk FROM Peminjaman_Barang WHERE YEAR(tglPeminjamanBrg) = ? UNION ALL SELECT idPeminjamanRuangan AS id_peminjaman, nim, npk FROM Peminjaman_Ruangan WHERE YEAR(tglPeminjamanRuangan) = ?) AS P LEFT JOIN Mahasiswa AS M ON P.nim = M.nim LEFT JOIN Karyawan AS K ON P.npk = K.npk GROUP BY CASE WHEN P.nim IS NOT NULL THEN M.nama WHEN P.npk IS NOT NULL THEN K.nama END, CASE WHEN P.nim IS NOT NULL THEN 'Mahasiswa' WHEN P.npk IS NOT NULL THEN 'Karyawan' END ORDER BY JumlahPeminjaman DESC, NamaPeminjam ASC;";
                $params = [$tahun, $tahun];
            } elseif ($tahun && $bulan !== null) {
                $query = "SELECT CASE WHEN P.nim IS NOT NULL THEN M.nama WHEN P.npk IS NOT NULL THEN K.nama END AS NamaPeminjam, CASE WHEN P.nim IS NOT NULL THEN 'Mahasiswa' WHEN P.npk IS NOT NULL THEN 'Karyawan' END AS JenisPeminjam, COUNT(P.id_peminjaman) AS JumlahPeminjaman FROM (SELECT idPeminjamanBrg AS id_peminjaman, nim, npk FROM Peminjaman_Barang WHERE YEAR(tglPeminjamanBrg) = ? AND MONTH(tglPeminjamanBrg) = ? UNION ALL SELECT idPeminjamanRuangan AS id_peminjaman, nim, npk FROM Peminjaman_Ruangan WHERE YEAR(tglPeminjamanRuangan) = ? AND MONTH(tglPeminjamanRuangan) = ?) AS P LEFT JOIN Mahasiswa AS M ON P.nim = M.nim LEFT JOIN Karyawan AS K ON P.npk = K.npk GROUP BY CASE WHEN P.nim IS NOT NULL THEN M.nama WHEN P.npk IS NOT NULL THEN K.nama END, CASE WHEN P.nim IS NOT NULL THEN 'Mahasiswa' WHEN P.npk IS NOT NULL THEN 'Karyawan' END ORDER BY JumlahPeminjaman DESC, NamaPeminjam ASC;";
                $params = [$tahun, $bulan, $tahun, $bulan];
            }
            break;
        case 'barangSeringDipinjam':
            if ($tahun) {
                if ($bulan === null) {
                    $query = "SELECT B.namaBarang, SUM(PB.jumlahBrg) AS TotalKuantitasDipinjam FROM Peminjaman_Barang AS PB INNER JOIN Barang AS B ON PB.idBarang = B.idBarang WHERE YEAR(PB.tglPeminjamanBrg) = ? GROUP BY B.namaBarang ORDER BY TotalKuantitasDipinjam DESC, B.namaBarang ASC;";
                    $params = [$tahun];
                } else {
                    $query = "SELECT B.namaBarang, SUM(PB.jumlahBrg) AS TotalKuantitasDipinjam FROM Peminjaman_Barang AS PB INNER JOIN Barang AS B ON PB.idBarang = B.idBarang WHERE YEAR(PB.tglPeminjamanBrg) = ? AND MONTH(PB.tglPeminjamanBrg) = ? GROUP BY B.namaBarang ORDER BY TotalKuantitasDipinjam DESC, B.namaBarang ASC;";
                    $params = [$tahun, $bulan];
                }
            } else {
                echo "<p style='color:red;font-weight:bold;'>Tahun wajib dipilih untuk laporan ini.</p>";
                exit;
            }
            break;
        case 'ruanganSeringDipinjam':
            if ($tahun && $bulan === null) {
                $query = "SELECT R.namaRuangan, COUNT(PR.idpeminjamanRuangan) AS JumlahDipinjam FROM Peminjaman_Ruangan AS PR INNER JOIN Ruangan AS R ON PR.idRuangan = R.idRuangan WHERE YEAR(PR.tglPeminjamanRuangan) = ? GROUP BY R.namaRuangan ORDER BY JumlahDipinjam DESC, R.namaRuangan ASC;";
                $params = [$tahun];
            } elseif ($tahun && $bulan !== null) {
                $query = "SELECT R.namaRuangan, COUNT(PR.idpeminjamanRuangan) AS JumlahDipinjam FROM Peminjaman_Ruangan AS PR INNER JOIN Ruangan AS R ON PR.idRuangan = R.idRuangan WHERE YEAR(PR.tglPeminjamanRuangan) = ? AND MONTH(PR.tglPeminjamanRuangan) = ? GROUP BY R.namaRuangan ORDER BY JumlahDipinjam DESC, R.namaRuangan ASC;";
                $params = [$tahun, $bulan];
            }
            break;
    }
    if ($query) {
        $stmt = sqlsrv_query($conn, $query, $params);
        if ($stmt) { while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) { $dataResult[] = $row; } sqlsrv_free_stmt($stmt); }
    }
}

// === LOGIKA SUMMARY TEXT (Identik dengan PIC) ===
$summaryText = '';
// ... (Kode untuk generate summary text bisa disalin penuh dari file PIC, tidak perlu diubah) ...
if (!empty($dataResult)) { switch ($jenisLaporan) { case 'dataBarang': $totalJenis = count($dataResult); $totalStok = array_reduce($dataResult, function ($sum, $item) { return $sum + (int)$item['stokBarang']; }, 0); $summaryText = "Total Jenis Barang: {$totalJenis}, Total Stok Barang: {$totalStok}"; break; case 'dataRuangan': $totalJenis = count($dataResult); $totalTersedia = count(array_filter($dataResult, function ($item) { return strtolower($item['ketersediaan']) === 'tersedia'; })); $summaryText = "Total Jenis Ruangan: {$totalJenis}, Total Ruangan yang Tersedia: {$totalTersedia}"; break; case 'peminjamSeringMeminjam': $totalPeminjaman = array_reduce($dataResult, function ($sum, $item) { return $sum + (int)$item['JumlahPeminjaman']; }, 0); $summaryText = "Total Peminjaman oleh Peminjam Teratas: {$totalPeminjaman}"; break; case 'barangSeringDipinjam': $totalKuantitas = array_reduce($dataResult, function ($sum, $item) { return $sum + (int)$item['TotalKuantitasDipinjam']; }, 0); $summaryText = "Total Kuantitas Barang yang Dipinjam: {$totalKuantitas}"; break; case 'ruanganSeringDipinjam': $totalDipinjam = array_reduce($dataResult, function ($sum, $item) { return $sum + (int)$item['JumlahDipinjam']; }, 0); $summaryText = "Total Ruangan yang Dipinjam: {$totalDipinjam}"; break; } }


# === OUTPUT KE EXCEL ===
# Cek mode operasi: 'preview' (default) atau 'download'
$mode = isset($_GET['mode']) ? $_GET['mode'] : 'preview';

if ($mode === 'download') {
    # Jika mode adalah download, kirim header untuk mengunduh file
    header("Content-Type: application/vnd.ms-excel");
    header("Content-Disposition: attachment; filename=\"{$filename}\"");
} else {
    # Jika mode adalah preview, kita akan membungkusnya dengan struktur HTML lengkap
    # agar bisa menampilkan halaman dengan benar, termasuk judul tab browser.
    echo "<!DOCTYPE html>";
    echo "<html lang='id'>";
    echo "<head>";
    echo "<meta charset='UTF-8'>";
    echo "<title>Preview Laporan - " . htmlspecialchars($reportTitle) . "</title>";
    # Kita bisa menambahkan sedikit style agar terlihat lebih baik
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

# === BAGIAN INI SAMA UNTUK KEDUA MODE ===
# Cetak Judul, Ringkasan, dan Tabel Laporan
echo "<h2>" . htmlspecialchars($reportTitle) . "</h2>";
echo "<p>" . htmlspecialchars($summaryText) . "</p>";

# Buat tabel dengan border agar rapi
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
    $no = 1;
    foreach ($dataResult as $row) {
        echo "<tr>";
        echo "<td>" . $no++ . "</td>"; // Kolom No
        foreach ($keys as $key) {
            echo "<td>" . htmlspecialchars($row[$key] ?? '') . "</td>";
        }
        echo "</tr>";
    }
}
echo "</tbody>";
echo "</table>";
# === AKHIR BAGIAN YANG SAMA ===


# Jika dalam mode preview, tambahkan tombol download
if ($mode === 'preview') {
    $downloadUrl = $_SERVER['REQUEST_URI'];
    $downloadUrl = preg_replace('/&mode=download/', '', $downloadUrl);
    if ($bulan === null) {
        $downloadUrl = preg_replace('/&bulan=[^&]*/', '', $downloadUrl);
    }
    $downloadUrl .= (strpos($downloadUrl, '?') !== false ? '&' : '?') . 'mode=download';
    $downloadUrl = htmlspecialchars($downloadUrl);

    echo "<a href='{$downloadUrl}' class='download-btn'>Download sebagai Excel (.xls)</a>";
    echo "</body>";
    echo "</html>";
}

exit;