<?php
// get_laporan_data.php

// Mengatur header respons sebagai JSON, memberitahu browser bahwa outputnya adalah JSON.
header('Content-Type: application/json');

// Menyertakan file koneksi.php yang berisi logika untuk terhubung ke database.
include '../koneksi.php'; // Pastikan path ini benar ke file koneksi Anda


// Jika ada error atau request tidak valid, respons ini yang akan dikirim.
$response = ['status' => 'error', 'message' => 'Request tidak valid.', 'data' => []];

// Mengambil parameter dari request AJAX (melalui URL, metode GET).
// Menggunakan operator null coalescing (??) untuk memberikan nilai default null jika parameter tidak ada.
$jenisLaporan = isset($_GET['jenisLaporan']) ? $_GET['jenisLaporan'] : null;
$bulan = isset($_GET['bulan']) ? (int)$_GET['bulan'] : null; // Konversi ke integer, karena bulan adalah angka
$tahun = isset($_GET['tahun']) ? (int)$_GET['tahun'] : null; // Konversi ke integer, karena tahun adalah angka

// Memastikan koneksi ke database ($conn) berhasil dan parameter $jenisLaporan ada.
if ($conn && $jenisLaporan) {
    try { // Blok try-catch untuk menangani potensi error saat eksekusi query atau proses lainnya.
        
        // Kondisi untuk jenis laporan 'dataBarang'.
        if ($jenisLaporan === 'dataBarang') {
            // Query SQL untuk mengambil semua data dari tabel 'Barang'.
            // Diurutkan berdasarkan 'idBarang' secara menaik (ASC).
            // Catatan: Query ini TIDAK menggunakan filter bulan dan tahun, jadi akan mengambil semua data master barang.
            $query = "SELECT idBarang, namaBarang, stokBarang, lokasiBarang FROM Barang ORDER BY idBarang ASC";
            
            // Menjalankan query SQL. Tidak ada parameter tambahan untuk query ini.
            $stmt = sqlsrv_query($conn, $query);

            // Memeriksa apakah query gagal dieksekusi.
            if ($stmt === false) {
                // Jika gagal, set pesan error di respons dengan detail error dari SQL Server.
                $response['message'] = "Gagal menjalankan query Data Barang: " . print_r(sqlsrv_errors(), true);
            } else {
                // Jika query berhasil, inisialisasi array untuk menyimpan hasil.
                $dataResult = [];
                // Mengambil setiap baris hasil query sebagai array asosiatif.
                while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
                    $dataResult[] = $row; // Tambahkan baris ke array hasil.
                }
                sqlsrv_free_stmt($stmt); // Membebaskan resource statement setelah selesai.
                $response['status'] = 'success'; // Set status respons menjadi 'success'.
                $response['message'] = 'Data barang berhasil diambil.';
                $response['data'] = $dataResult; // Masukkan data hasil query ke respons.
            }
        } 
        // Kondisi untuk jenis laporan 'dataRuangan'.
        else if ($jenisLaporan === 'dataRuangan') {
            // Query SQL untuk mengambil semua data dari tabel 'Ruangan'.
            // Diurutkan berdasarkan 'idRuangan' secara menaik (ASC).
            // Catatan: Query ini juga TIDAK menggunakan filter bulan dan tahun.
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
        // Kondisi untuk jenis laporan 'peminjamSeringMeminjam'.
        else if ($jenisLaporan === 'peminjamSeringMeminjam') {
            // Laporan ini memerlukan filter bulan dan tahun.
            if ($bulan === null || $tahun === null) {
                $response['message'] = "Bulan dan Tahun harus dipilih untuk laporan Peminjam yang Sering Meminjam.";
            } else {
                // Query SQL kompleks untuk menggabungkan peminjaman barang dan ruangan,
                // lalu menghitung total peminjaman per pengguna (Mahasiswa atau Karyawan).
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
                // Parameter untuk query, menggantikan placeholder '?'.
                // Urutan parameter HARUS sesuai dengan urutan placeholder '?' dalam query.
                $params = [$tahun, $bulan, $tahun, $bulan]; 
                
                // Menjalankan query SQL dengan parameter.
                $stmt = sqlsrv_query($conn, $query, $params); 

                if ($stmt === false) {
                    $response['message'] = "Gagal menjalankan query Peminjam Sering Meminjam: " . print_r(sqlsrv_errors(), true);
                } else {
                    $dataResult = [];
                    while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
                        // Fallback jika nama peminjam (hasil join) null, tampilkan ID saja.
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
        // Kondisi untuk jenis laporan 'barangSeringDipinjam'.
        else if ($jenisLaporan === 'barangSeringDipinjam') {
            if ($bulan === null || $tahun === null) {
                $response['message'] = "Bulan dan Tahun harus dipilih untuk laporan Barang yang Sering Dipinjam.";
            } else {
                // Query SQL untuk menghitung total kuantitas barang yang dipinjam,
                // dikelompokkan per barang, untuk periode tertentu.
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
        // Kondisi untuk jenis laporan 'ruanganSeringDipinjam'.
        else if ($jenisLaporan === 'ruanganSeringDipinjam') {
            if ($bulan === null || $tahun === null) {
                $response['message'] = "Bulan dan Tahun harus dipilih untuk laporan Ruangan yang Sering Dipinjam.";
            } else {
                // Query SQL untuk menghitung frekuensi peminjaman ruangan,
                // dikelompokkan per ruangan, untuk periode tertentu.
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
        // Jika jenis laporan tidak cocok dengan kondisi di atas.
        else {
            $response['message'] = 'Jenis laporan belum diimplementasikan atau tidak dikenal.';
        }
    } catch (Exception $e) { // Menangkap error umum.
        $response['message'] = 'Terjadi kesalahan server: ' . $e->getMessage();
    }
    // Menutup koneksi database setelah semua operasi selesai.
    if ($conn) {
        sqlsrv_close($conn);
    }
} else { // Jika koneksi gagal ATAU $jenisLaporan tidak ada.
    if (!$conn) {
        $response['message'] = "Koneksi ke database gagal. Periksa koneksi.php.";
    } else {
        $response['message'] = "Parameter jenis laporan tidak disediakan atau tidak valid.";
    }
}

// Mengirimkan respons sebagai string JSON.
echo json_encode($response);
?>