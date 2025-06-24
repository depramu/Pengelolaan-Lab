<?php
// export_laporan_excel_kaupt.php (atau nama yang Anda pilih untuk file export KaUPT)

// 1. SERTAKAN FILE koneksi.php ANDA!
// Path disesuaikan jika file ini ada di CRUD/Laporan/ dan koneksi.php dua level di atas.
if (file_exists(__DIR__ . '/../../koneksi.php')) {
    include __DIR__ . '/../../koneksi.php';
} elseif (file_exists('../koneksi.php')) { 
    include '../koneksi.php';
} else {
    // Kirimkan pesan error sederhana yang bisa ditangkap browser jika file tidak bisa di-generate
    header("Content-Type: text/plain");
    die("Error: File koneksi.php tidak ditemukan untuk proses export.");
}

// 2. Ambil parameter dari URL
$jenisLaporan = isset($_GET['jenisLaporan']) ? $_GET['jenisLaporan'] : null;
$bulan = isset($_GET['bulan']) && $_GET['bulan'] !== '' ? (int)$_GET['bulan'] : null;
$tahun = isset($_GET['tahun']) && $_GET['tahun'] !== '' ? (int)$_GET['tahun'] : null;

// 3. Tentukan nama file Excel
$fileName = "Laporan_KaUPT_"; // Tambahkan _KaUPT untuk membedakan
if ($jenisLaporan) {
    $namaLaporanDeskriptif = ucwords(trim(strtolower(preg_replace('/(?<!^)[A-Z]/', '_$0', $jenisLaporan))));
    $fileName .= str_replace(' ', '_', $namaLaporanDeskriptif);
} else {
    $fileName .= "Tidak_Diketahui";
}

$laporanDenganFilterWaktuWajib = ['peminjamSeringMeminjam', 'barangSeringDipinjam', 'ruanganSeringDipinjam'];
if (in_array($jenisLaporan, $laporanDenganFilterWaktuWajib)) {
    if ($bulan && $tahun) {
        $fileName .= "_" . str_pad($bulan, 2, '0', STR_PAD_LEFT) . "_" . $tahun;
    } else {
        $fileName .= "_Filter_Tidak_Lengkap"; 
    }
} elseif (($jenisLaporan === 'dataBarang' || $jenisLaporan === 'dataRuangan') && $bulan && $tahun) {
    $fileName .= "_Periode_" . str_pad($bulan, 2, '0', STR_PAD_LEFT) . "_" . $tahun;
}
$fileName .= ".xls";

// 4. Set header HTTP
header("Content-Type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=\"$fileName\"");
header("Pragma: no-cache");
header("Expires: 0");

// 5. Memulai output tabel HTML
echo "<html xmlns:o=\"urn:schemas-microsoft-com:office:office\" xmlns:x=\"urn:schemas-microsoft-com:office:excel\" xmlns=\"http://www.w3.org/TR/REC-html40\">";
echo "<head><meta charset=\"utf-8\"><!--[if gte mso 9]><xml><x:ExcelWorkbook><x:ExcelWorksheets><x:ExcelWorksheet><x:Name>Laporan</x:Name><x:WorksheetOptions><x:DisplayGridlines/></x:WorksheetOptions></x:ExcelWorksheet></x:ExcelWorksheets></x:ExcelWorkbook></xml><![endif]--></head><body>";
echo "<table border='1'>";
echo "<thead>";

$stmt = null;
$params = [];
$headers = [];
$dataKeys = []; // PENTING: Definisikan dataKeys agar kolom tercetak sesuai header

// 6. Logika untuk memilih query, header, dan dataKeys
if ($conn && $jenisLaporan) {
    try {
        if ($jenisLaporan === 'dataBarang') {
            $headers = ['ID Barang', 'Nama Barang', 'Stok Barang', 'Lokasi Barang'];
            $dataKeys = ['idBarang', 'namaBarang', 'stokBarang', 'lokasiBarang'];
            $query = "SELECT idBarang, namaBarang, stokBarang, lokasiBarang FROM Barang ORDER BY idBarang ASC";
            $stmt = sqlsrv_query($conn, $query);
        } 
        else if ($jenisLaporan === 'dataRuangan') {
            $headers = ['ID Ruangan', 'Nama Ruangan', 'Kondisi Ruangan', 'Ketersediaan'];
            $dataKeys = ['idRuangan', 'namaRuangan', 'kondisiRuangan', 'ketersediaan'];
            $query = "SELECT idRuangan, namaRuangan, kondisiRuangan, ketersediaan FROM Ruangan ORDER BY idRuangan ASC";
            $stmt = sqlsrv_query($conn, $query);
        }
        else if ($jenisLaporan === 'peminjamSeringMeminjam') {
            if ($bulan !== null && $tahun !== null) {
                $headers = ['ID Peminjam', 'Nama Peminjam', 'Jenis Peminjam', 'Jumlah Peminjaman'];
                $dataKeys = ['IDPeminjam', 'NamaPeminjam', 'JenisPeminjam', 'JumlahPeminjaman'];
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
            } else {
                $headers = ['Pesan']; $dataKeys = ['Pesan'];
                echo "<tr><th>Pesan</th></tr></thead><tbody><tr><td>Filter Bulan dan Tahun wajib dipilih untuk laporan Peminjam Sering Meminjam.</td></tr>";
            }
        }
        else if ($jenisLaporan === 'barangSeringDipinjam') {
            if ($bulan !== null && $tahun !== null) {
                $headers = ['ID Barang', 'Nama Barang', 'Total Kuantitas Dipinjam'];
                $dataKeys = ['idBarang', 'namaBarang', 'TotalKuantitasDipinjam'];
                $query = "
                    SELECT PB.idBarang, B.namaBarang, SUM(PB.jumlahBrg) AS TotalKuantitasDipinjam
                    FROM Peminjaman_Barang AS PB INNER JOIN Barang AS B ON PB.idBarang = B.idBarang
                    WHERE YEAR(PB.tglPeminjamanBrg) = ? AND MONTH(PB.tglPeminjamanBrg) = ?
                    GROUP BY PB.idBarang, B.namaBarang ORDER BY TotalKuantitasDipinjam DESC, B.namaBarang ASC;
                ";
                $params = [$tahun, $bulan];
                $stmt = sqlsrv_query($conn, $query, $params);
            } else {
                $headers = ['Pesan']; $dataKeys = ['Pesan'];
                echo "<tr><th>Pesan</th></tr></thead><tbody><tr><td>Filter Bulan dan Tahun wajib dipilih untuk laporan Barang Sering Dipinjam.</td></tr>";
            }
        }
        else if ($jenisLaporan === 'ruanganSeringDipinjam') {
             if ($bulan !== null && $tahun !== null) {
                $headers = ['ID Ruangan', 'Nama Ruangan', 'Jumlah Dipinjam'];
                $dataKeys = ['idRuangan', 'namaRuangan', 'JumlahDipinjam'];
                $query = "
                    SELECT PR.idRuangan, R.namaRuangan, COUNT(PR.idpeminjamanRuangan) AS JumlahDipinjam
                    FROM Peminjaman_Ruangan AS PR INNER JOIN Ruangan AS R ON PR.idRuangan = R.idRuangan
                    WHERE YEAR(PR.tglPeminjamanRuangan) = ? AND MONTH(PR.tglPeminjamanRuangan) = ?
                    GROUP BY PR.idRuangan, R.namaRuangan ORDER BY JumlahDipinjam DESC, R.namaRuangan ASC;
                ";
                $params = [$tahun, $bulan];
                $stmt = sqlsrv_query($conn, $query, $params);
            } else {
                $headers = ['Pesan']; $dataKeys = ['Pesan'];
                echo "<tr><th>Pesan</th></tr></thead><tbody><tr><td>Filter Bulan dan Tahun wajib dipilih untuk laporan Ruangan Sering Dipinjam.</td></tr>";
            }
        }
        else {
            echo "<tr><td colspan='1'>Jenis laporan tidak dikenal.</td></tr>";
        }

        if ($stmt && !empty($headers) && (count($params) == 0 || ($bulan !== null && $tahun !== null) ) ) { // Hanya proses jika statement ada dan headers tidak kosong, dan filter waktu (jika wajib) ada
            echo "<tr>";
            foreach ($headers as $header) {
                echo "<th>" . htmlspecialchars($header) . "</th>";
            }
            echo "</tr>";
            echo "</thead><tbody>";

            $adaData = false;
            while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
                $adaData = true;
                echo "<tr>";
                foreach ($dataKeys as $key) { // Cetak berdasarkan urutan dataKeys
                    echo "<td>" . htmlspecialchars($row[$key] ?? '') . "</td>";
                }
                echo "</tr>";
            }
            if (!$adaData) {
                echo "<tr><td colspan='" . count($headers) . "' align='center'>Tidak ada data untuk laporan ini pada periode yang dipilih.</td></tr>";
            }
            if ($stmt) sqlsrv_free_stmt($stmt);
        } elseif ($stmt === false) { // Query Gagal
             echo "<tr><th>Error</th></tr></thead><tbody><tr><td>Gagal menjalankan query.</td></tr>";
        }
        // Jika filter waktu wajib tidak ada, pesan sudah dicetak di atas, jadi tidak perlu 'else' khusus di sini.

    } catch (Exception $e) {
        echo "<tr><th colspan='1'>Error Server</th></tr></thead><tbody><tr><td colspan='1'>Terjadi kesalahan: " . htmlspecialchars($e->getMessage()) . "</td></tr>";
    }
    if ($conn) {
        sqlsrv_close($conn);
    }
} else {
    echo "<tr><th>Error</th></tr></thead><tbody><tr><td>";
    if (!$conn) echo "Koneksi database gagal.";
    else echo "Jenis laporan tidak disediakan.";
    echo "</td></tr>";
}

echo "</tbody></table></body></html>";
exit;
?>