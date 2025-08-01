<?php
require_once __DIR__ . '/../../function/init.php';
authorize_role(['Peminjam']);

// Cek apakah data penting dari alur sebelumnya ada di session
if (empty($_SESSION['tglPeminjamanRuangan']) || empty($_SESSION['waktuMulai'])) {
    // Jika tidak ada, berarti pengguna belum memilih tanggal.
    // Arahkan kembali ke halaman pengecekan.
    header('Location: ' . BASE_URL . '/Menu/Menu Peminjam/Peminjaman Ruangan/cekRuangan.php');
    exit; // Wajib ada exit setelah header location
}

// Jika lolos pengecekan, baru ambil datanya
$idRuangan = $_GET['idRuangan'] ?? null;
if (empty($idRuangan)) {
    die("Error: ID Ruangan tidak ditemukan.");
}

// Ambil nama ruangan dari database
$namaRuangan = null;
$sqlNamaRuangan = "SELECT namaRuangan FROM Ruangan WHERE idRuangan = ?";
$stmtNamaRuangan = sqlsrv_query($conn, $sqlNamaRuangan, [$idRuangan]);
if ($stmtNamaRuangan && $rowNamaRuangan = sqlsrv_fetch_array($stmtNamaRuangan, SQLSRV_FETCH_ASSOC)) {
    $namaRuangan = $rowNamaRuangan['namaRuangan'];
} else {
    die("Error: Nama ruangan tidak ditemukan untuk ID tersebut.");
}


// Ambil data dari session (sekarang kita yakin datanya ada)
$tglPeminjamanRuangan = $_SESSION['tglPeminjamanRuangan'];
$waktuMulai = $_SESSION['waktuMulai'];
$waktuSelesai = $_SESSION['waktuSelesai'];
$nim = $_SESSION['nim'] ?? null;
$npk = $_SESSION['npk'] ?? null;


[$nim, $npk, $tglPeminjamanRuangan] = [
    $_SESSION['nim'] ?? null,
    $_SESSION['npk'] ?? null,
    $_SESSION['tglPeminjamanRuangan'] ?? '-'
];
$waktuMulai = $_SESSION['waktuMulai'] ?? null;
$waktuSelesai = $_SESSION['waktuSelesai'] ?? null;

// Ambil nama peminjam (mahasiswa/karyawan) dari database
$nama = '';
if (!empty($nim)) {
    $stmtNama = sqlsrv_query($conn, "SELECT nama FROM Mahasiswa WHERE nim = ?", [$nim]);
    if ($stmtNama && $rowNama = sqlsrv_fetch_array($stmtNama, SQLSRV_FETCH_ASSOC)) {
        $nama = $rowNama['nama'];
    }
} elseif (!empty($npk)) {
    $stmtNama = sqlsrv_query($conn, "SELECT nama FROM Karyawan WHERE npk = ?", [$npk]);
    if ($stmtNama && $rowNama = sqlsrv_fetch_array($stmtNama, SQLSRV_FETCH_ASSOC)) {
        $nama = $rowNama['nama'];
    }
}


// Auto-generate id Peminjaman Ruangan
$idPeminjamanRuangan = 'PJR001';
$sqlId = "SELECT TOP 1 idPeminjamanRuangan FROM Peminjaman_Ruangan ORDER BY idPeminjamanRuangan DESC";
$stmtId = sqlsrv_query($conn, $sqlId);
if ($stmtId && $rowId = sqlsrv_fetch_array($stmtId, SQLSRV_FETCH_ASSOC)) {
    $lastId = $rowId['idPeminjamanRuangan'];
    $num = intval(substr($lastId, 3));
    $newNum = $num + 1;
    $idPeminjamanRuangan = 'PJR' . str_pad($newNum, 3, '0', STR_PAD_LEFT);
}

$showModal = false;
$error = null;

// Penyesuaian proses submit form peminjaman ruangan
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $alasanPeminjamanRuangan = trim($_POST['alasanPeminjamanRuangan'] ?? '');

    if ($alasanPeminjamanRuangan === '') {
        $error = "Alasan peminjaman ruangan tidak boleh kosong";
    } else {
        // Format tanggal dan waktu sebelum insert
        $tglForSQL = null;
        $waktuMulaiForSQL = null;
        $waktuSelesaiForSQL = null;

        if ($tglPeminjamanRuangan) {
            $dateObj = DateTime::createFromFormat('d-m-Y', $tglPeminjamanRuangan);
            $tglForSQL = $dateObj ? $dateObj->format('d-m-y') : null;
        }
        if ($waktuMulai) {
            $waktuMulaiForSQL = $waktuMulai;
        }
        if ($waktuSelesai) {
            $waktuSelesaiForSQL = $waktuSelesai;
        }

        // Query INSERT ke tabel Peminjaman_Ruangan (tanpa statusPeminjaman)
        $queryInsert = "INSERT INTO Peminjaman_Ruangan 
            (idPeminjamanRuangan, idRuangan, tglPeminjamanRuangan, nim, npk, waktuMulai, waktuSelesai, alasanPeminjamanRuangan) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        $paramsInsert = [
            $idPeminjamanRuangan,
            $idRuangan,
            $tglPeminjamanRuangan,
            $nim,
            $npk,
            $waktuMulaiForSQL,
            $waktuSelesaiForSQL,
            $alasanPeminjamanRuangan
        ];

        $stmtInsert = sqlsrv_query($conn, $queryInsert, $paramsInsert);

        if ($stmtInsert) {
            // Insert status peminjaman ke tabel Status_Peminjaman
            $queryInsertStatus = "INSERT INTO Status_Peminjaman (idPeminjamanRuangan, statusPeminjaman) VALUES (?, ?)";
            $paramsInsertStatus = [$idPeminjamanRuangan, 'Menunggu Persetujuan'];
            $stmtInsertStatus = sqlsrv_query($conn, $queryInsertStatus, $paramsInsertStatus);

            if ($stmtInsertStatus) {
                // Update status ketersediaan ruangan jika insert berhasil
                $ketersediaanQuery = "UPDATE Ruangan SET ketersediaan = 'Tidak Tersedia' WHERE idRuangan = ?";
                $stmtUpdate = sqlsrv_query($conn, $ketersediaanQuery, [$idRuangan]);
                if ($stmtUpdate) {
                    $nama_peminjam = $nama;
                    $untuk = 'PIC Aset'; // atau $_SESSION['user_role'] untuk peminjam
                    $pesanNotif = "Pengajuan peminjaman ruangan oleh $nama_peminjam menunggu persetujuan.";
                    $queryNotif = "INSERT INTO Notifikasi (pesan, status, untuk) VALUES (?, 'Belum Dibaca', ?)";
                    sqlsrv_query($conn, $queryNotif, [$pesanNotif, $untuk]);
                    $showModal = true;
                } else {
                    $error = "Peminjaman berhasil dicatat, tetapi gagal memperbarui status ruangan. Error: " . print_r(sqlsrv_errors(), true);
                }
            } else {
                $error = "Peminjaman berhasil dicatat, tetapi gagal mencatat status. Error: " . print_r(sqlsrv_errors(), true);
            }
        } else {
            $error = "Gagal mengajukan peminjaman ruangan. Error: " . print_r(sqlsrv_errors(), true);
        }
    }
    if (!empty($error)) {
        echo "<div class='alert alert-danger'>{$error}</div>";
    }
}

include '../../templates/header.php';
include '../../templates/sidebar.php';
?>

<main class="col bg-white px-3 px-md-4 py-3 position-relative">
    <h3 class="fw-semibold mb-3">Peminjaman Ruangan</h3>
    <div class="mb-2">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?= BASE_URL ?>/Menu/Menu Peminjam/dashboardPeminjam.php">Sistem Pengelolaan Lab</a></li>
                <li class="breadcrumb-item"><a href="<?= BASE_URL ?>/Menu/Menu Peminjam/cekRuangan.php">Cek Ruangan</a></li>
                <li class="breadcrumb-item active" aria-current="page">Pengajuan Peminjaman Ruangan</li>
            </ol>
        </nav>
    </div>

    <div class="container mt-4">
        <div class="row justify-content-center">
            <div class="col-md-8 col-lg-12" style="margin-right: 20px;">
                <div class="card border border-dark">
                    <div class="card-header border-bottom border-dark text-white" style="background-color:rgb(9, 103, 185);">
                        <span class="fw-semibold">Pengajuan Peminjaman Ruangan</span>
                    </div>
                    <div class="card-body">
                        <form id="formTambahPeminjamanRuangan" method="POST">
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="namaRuangan" class="form-label fw-semibold">Nama Ruangan</label>
                                        <input type="text" class="form-control protect-input d-block bg-light" id="namaRuangan" name="namaRuangan" value="<?= $namaRuangan ?>">
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label fw-semibold">Tanggal Peminjaman</label>
                                        <input type="text" class="form-control protect-input d-block bg-light" name="tglDisplay" value="<?php if (!empty($tglPeminjamanRuangan)) {
                                                                                                                                            $dateObj = DateTime::createFromFormat('Y-m-d', $tglPeminjamanRuangan);
                                                                                                                                            echo $dateObj ? $dateObj->format('d M Y') : htmlspecialchars($tglPeminjamanRuangan);
                                                                                                                                        } ?>">
                                    </div>
                                    <div class="mb-3">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <label for="waktuMulai" class="form-label fw-semibold">Waktu Mulai</label>
                                                <input type="text" class="form-control protect-input" id="waktuMulai" name="waktuMulai" value="<?= $waktuMulai ?>">
                                            </div>
                                            <div class="col-md-6">
                                                <label for="waktuSelesai" class="form-label fw-semibold">Waktu Selesai</label>
                                                <input type="text" class="form-control protect-input" id="waktuSelesai" name="waktuSelesai" value="<?= $waktuSelesai ?>">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="alasanPeminjamanRuangan" class="form-label fw-semibold">
                                            Alasan Peminjaman <span id="error-message" class="text-danger small mt-1 fw-normal" style="font-size: 0.95em; display:none;">*Harus Diisi</span>
                                        </label>
                                        <textarea class="form-control" id="alasanPeminjamanRuangan" name="alasanPeminjamanRuangan" rows="1" placeholder="Masukkan alasan peminjaman.."></textarea>
                                    </div>
                                </div>

                                <div class="d-flex justify-content-between mt-3">
                                    <a href="<?= BASE_URL ?>/Menu/Menu Peminjam/cekRuangan.php" class="btn btn-secondary">Kembali</a>
                                    <button type="submit" class="btn btn-primary">Ajukan Peminjaman</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

</main>



<?php
include '../../templates/footer.php';
?>