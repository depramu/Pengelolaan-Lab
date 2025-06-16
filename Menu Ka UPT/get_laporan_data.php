<?php
// get_laporan_data.php
header('Content-Type: application/json');
include '../koneksi.php';

$response = ['status' => 'error', 'message' => 'Request tidak valid.', 'data' => []];

$jenisLaporan = isset($_GET['jenisLaporan']) ? $_GET['jenisLaporan'] : null;
$bulan = isset($_GET['bulan']) ? $_GET['bulan'] : null;
$tahun = isset($_GET['tahun']) ? $_GET['tahun'] : null;

if ($conn && $jenisLaporan) {
    try {
        if ($jenisLaporan === 'dataBarang') {
            // Query yang DIMODIFIKASI dengan pengurutan idBarang ASC:
            $query = "SELECT idBarang, namaBarang, stokBarang, lokasiBarang FROM Barang ORDER BY idBarang ASC";
            // Atau jika idBarang memiliki pola numerik di belakang dan Anda ingin pengurutan numerik yang lebih tepat
            // $query = "SELECT idBarang, namaBarang, stokBarang, lokasiBarang FROM Barang ORDER BY CAST(SUBSTRING(idBarang, 4, LEN(idBarang)) AS INT) ASC";
            
            $stmt = sqlsrv_query($conn, $query);

            if ($stmt === false) {
                $response['message'] = "Gagal menjalankan query Barang: " . print_r(sqlsrv_errors(), true);
            } else {
                $dataBarang = [];
                while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
                    $dataBarang[] = $row;
                }
                sqlsrv_free_stmt($stmt);
                $response['status'] = 'success';
                $response['message'] = 'Data barang berhasil diambil.';
                $response['data'] = $dataBarang;
            }
        } 
        // --- AWAL BLOK UNTUK DATA RUANGAN ---
        else if ($jenisLaporan === 'dataRuangan') {
            // Query untuk mengambil data dari tabel Ruangan, diurutkan berdasarkan idRuangan
            $query = "SELECT idRuangan, namaRuangan, kondisiRuangan, ketersediaan FROM Ruangan ORDER BY idRuangan ASC";
            $stmt = sqlsrv_query($conn, $query);

            if ($stmt === false) {
                $response['message'] = "Gagal menjalankan query Ruangan: " . print_r(sqlsrv_errors(), true);
            } else {
                $dataRuangan = [];
                while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
                    $dataRuangan[] = $row;
                }
                sqlsrv_free_stmt($stmt);
                $response['status'] = 'success';
                $response['message'] = 'Data ruangan berhasil diambil.';
                $response['data'] = $dataRuangan;
            }
        }
        // --- AKHIR BLOK UNTUK DATA RUANGAN ---
        // --- NANTI BISA TAMBAH ELSE IF UNTUK JENIS LAPORAN LAIN ---
        // else if ($jenisLaporan === 'peminjamSeringMeminjam') { ... }
        else {
            $response['message'] = 'Jenis laporan belum diimplementasikan atau tidak dikenal.';
        }
    } catch (Exception $e) {
        $response['message'] = 'Terjadi kesalahan server: ' . $e->getMessage();
    }
    sqlsrv_close($conn);
} else {
    if (!$conn) {
        $response['message'] = "Koneksi ke database gagal. Periksa koneksi.php.";
    } else {
        $response['message'] = "Parameter jenis laporan tidak disediakan.";
    }
}

echo json_encode($response);
?>