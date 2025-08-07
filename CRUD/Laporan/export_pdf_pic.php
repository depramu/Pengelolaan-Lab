<?php
// CRUD/Laporan/export_pdf_pic.php

// Mulai output buffering untuk mencegah error "headers already sent"
ob_start();

require_once __DIR__ . '/../../function/init.php';
require_once __DIR__ . '/../../function/TCPDF-main/tcpdf.php';

// Ambil semua parameter filter dari URL
$jenisLaporan = $_GET['jenisLaporan'] ?? null;
$bulan = (isset($_GET['bulan']) && $_GET['bulan'] !== '') ? (int)$_GET['bulan'] : null;
$tahun = (isset($_GET['tahun']) && $_GET['tahun'] !== '') ? (int)$_GET['tahun'] : null;
$lokasi = $_GET['lokasiBarang'] ?? null;
$kondisi = $_GET['kondisiRuangan'] ?? null;

if (!$jenisLaporan) {
    die("Error: Jenis laporan tidak ditentukan.");
}

// Map untuk judul, header, dan kunci data
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
if ($lokasi) { $reportTitle .= " - Lokasi " . htmlspecialchars($lokasi); }
if ($kondisi) { $reportTitle .= " - Kondisi " . htmlspecialchars($kondisi); }
if ($bulan && $tahun) {
    $namaBulan = [1=>'Januari',2=>'Februari',3=>'Maret',4=>'April',5=>'Mei',6=>'Juni',7=>'Juli',8=>'Agustus',9=>'September',10=>'Oktober',11=>'November',12=>'Desember'];
    $reportTitle .= " - " . ($namaBulan[$bulan] ?? '') . " " . $tahun;
} elseif ($tahun) {
    $reportTitle .= " - Tahun " . $tahun;
}

// === INI BAGIAN PALING PENTING: LOGIKA PENGAMBILAN DATA YANG LENGKAP ===
$dataResult = [];
if ($conn) {
    $query = '';
    $params = [];
    $execute = true;

    switch ($jenisLaporan) {
        case 'dataBarang':
            $query = "SELECT namaBarang, stokBarang, lokasiBarang FROM Barang WHERE isDeleted = 0";
            if (!empty($lokasi)) {
                $query .= " AND lokasiBarang = ?";
                $params[] = $lokasi;
            }
            $query .= " ORDER BY namaBarang ASC";
            break;

        case 'dataRuangan':
            $query = "SELECT namaRuangan, kondisiRuangan, ketersediaan FROM Ruangan WHERE 1=1";
            if (!empty($kondisi)) {
                $query .= " AND kondisiRuangan = ?";
                $params[] = $kondisi;
            }
            $query .= " ORDER BY namaRuangan ASC";
            break;
            
        case 'peminjamSeringMeminjam':
            if (!$tahun) { $execute = false; die("Tahun harus dipilih."); }
            $baseQuery = "
                SELECT
                    CASE WHEN P.nim IS NOT NULL THEN M.nama WHEN P.npk IS NOT NULL THEN K.nama END AS NamaPeminjam, 
                    CASE WHEN P.nim IS NOT NULL THEN 'Mahasiswa' WHEN P.npk IS NOT NULL THEN 'Karyawan' END AS JenisPeminjam,
                    COUNT(P.id_peminjaman) AS JumlahPeminjaman
                FROM (
                    SELECT idPeminjamanBrg AS id_peminjaman, nim, npk FROM Peminjaman_Barang WHERE YEAR(tglPeminjamanBrg) = ? %s
                    UNION ALL
                    SELECT idPeminjamanRuangan AS id_peminjaman, nim, npk FROM Peminjaman_Ruangan WHERE YEAR(tglPeminjamanRuangan) = ? %s
                ) AS P
                LEFT JOIN Mahasiswa AS M ON P.nim = M.nim 
                LEFT JOIN Karyawan AS K ON P.npk = K.npk 
                WHERE (CASE WHEN P.nim IS NOT NULL THEN M.nama WHEN P.npk IS NOT NULL THEN K.nama END) IS NOT NULL
                GROUP BY 
                    CASE WHEN P.nim IS NOT NULL THEN M.nama WHEN P.npk IS NOT NULL THEN K.nama END, 
                    CASE WHEN P.nim IS NOT NULL THEN 'Mahasiswa' WHEN P.npk IS NOT NULL THEN 'Karyawan' END
                ORDER BY JumlahPeminjaman DESC, NamaPeminjam ASC";
            if ($bulan) {
                $query = sprintf($baseQuery, "AND MONTH(tglPeminjamanBrg) = ?", "AND MONTH(tglPeminjamanRuangan) = ?");
                $params = [$tahun, $bulan, $tahun, $bulan];
            } else {
                $query = sprintf($baseQuery, "", "");
                $params = [$tahun, $tahun];
            }
            break;

        case 'barangSeringDipinjam':
            if (!$tahun) { $execute = false; die("Tahun harus dipilih."); }
            $query = "
                SELECT B.namaBarang, SUM(PB.jumlahBrg) AS TotalKuantitasDipinjam
                FROM Peminjaman_Barang AS PB INNER JOIN Barang AS B ON PB.idBarang = B.idBarang
                WHERE YEAR(PB.tglPeminjamanBrg) = ?";
            $params = [$tahun];
            if ($bulan) {
                $query .= " AND MONTH(PB.tglPeminjamanBrg) = ?";
                $params[] = $bulan;
            }
            $query .= " GROUP BY B.namaBarang ORDER BY TotalKuantitasDipinjam DESC, B.namaBarang ASC";
            break;

        case 'ruanganSeringDipinjam':
            if (!$tahun) { $execute = false; die("Tahun harus dipilih."); }
            $query = "
                SELECT R.namaRuangan, COUNT(PR.idPeminjamanRuangan) AS JumlahDipinjam
                FROM Peminjaman_Ruangan AS PR INNER JOIN Ruangan AS R ON PR.idRuangan = R.idRuangan
                WHERE YEAR(PR.tglPeminjamanRuangan) = ?";
            $params = [$tahun];
            if ($bulan) {
                $query .= " AND MONTH(PR.tglPeminjamanRuangan) = ?";
                $params[] = $bulan;
            }
            $query .= " GROUP BY R.namaRuangan ORDER BY JumlahDipinjam DESC, R.namaRuangan ASC";
            break;
            
        default:
            $execute = false;
            die("Jenis laporan tidak dikenal.");
    }

    if ($execute && !empty($query)) {
        $stmt = sqlsrv_query($conn, $query, $params);
        if ($stmt) {
            while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
                $dataResult[] = $row;
            }
            sqlsrv_free_stmt($stmt);
        } else {
            die("Query Gagal: " . print_r(sqlsrv_errors(), true));
        }
    }
    if($conn) { sqlsrv_close($conn); }
}

// Logika pembuatan summary text (salin lengkap)
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

// === Membuat HTML untuk PDF ===
$html = "
<style>
    h2 { text-align: center; font-size: 16px; font-weight: bold; }
    p { text-align: center; font-size: 12px; }
    table { border-collapse: collapse; width: 100%; font-size: 10px; }
    th, td { border: 1px solid #333; padding: 5px; }
    th { background-color: #f2f2f2; font-weight: bold; text-align: center; }
</style>
<h2>" . htmlspecialchars($reportTitle) . "</h2>
<table>
    <thead>
        <tr>";
foreach ($headersMap[$jenisLaporan] as $header) {
    $html .= '<th>' . htmlspecialchars($header) . '</th>';
}
$html .= "   </tr>
    </thead>
    <tbody>";
if (empty($dataResult)) {
    $colspan = count($headersMap[$jenisLaporan]);
    $html .= "<tr><td colspan='{$colspan}' style='text-align:center;'>Tidak ada data untuk ditampilkan.</td></tr>";
} else {
    $no = 1;
    foreach ($dataResult as $row) {
        $html .= "<tr>";
        $html .= "<td style='text-align:center;'>" . $no++ . "</td>";
        foreach ($keysMap[$jenisLaporan] as $key) {
            $html .= '<td>' . htmlspecialchars($row[$key] ?? '') . '</td>';
        }
        $html .= "</tr>";
    }
}
$html .= "  </tbody>
</table>";

// Proses Pembuatan PDF dengan TCPDF
$pdf = new TCPDF('P', PDF_UNIT, 'A4', true, 'UTF-8', false);
$pdf->SetCreator('Sistem Pengelolaan Lab');
$pdf->SetTitle($reportTitle);
$pdf->setPrintHeader(false);
$pdf->setPrintFooter(true);
$pdf->AddPage();
$pdf->writeHTML($html, true, false, true, false, '');

// Hapus semua buffer yang mungkin sudah ada, lalu kirim PDF
ob_end_clean();

$pdf->Output('laporan_' . $jenisLaporan . '.pdf', 'I');
exit;