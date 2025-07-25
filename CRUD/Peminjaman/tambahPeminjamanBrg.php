<?php
require_once __DIR__ . '/../../function/init.php';
authorize_role(['Peminjam']);

$showModal = false;
// Auto-generate ID Peminjaman Barang
$idPeminjamanBrg = 'PJB001';
$stmtId = sqlsrv_query($conn, "SELECT TOP 1 idPeminjamanBrg FROM Peminjaman_Barang WHERE idPeminjamanBrg LIKE 'PJB%' ORDER BY idPeminjamanBrg DESC");
if ($stmtId && $rowId = sqlsrv_fetch_array($stmtId, SQLSRV_FETCH_ASSOC)) {
    $num = intval(substr($rowId['idPeminjamanBrg'], 3)) + 1;
    $idPeminjamanBrg = 'PJB' . str_pad($num, 3, '0', STR_PAD_LEFT);
}

// Validasi ID Barang dari URL
$idBarang = $_GET['idBarang'] ?? null;
if (empty($idBarang)) {
    die("Error: ID Barang tidak ditemukan. Silakan kembali dan pilih barang yang ingin dipinjam.");
}

// Ambil detail barang
$stmtDetail = sqlsrv_query($conn, "SELECT namaBarang, stokBarang FROM Barang WHERE idBarang = ?", [$idBarang]);
if ($stmtDetail && $dataBarang = sqlsrv_fetch_array($stmtDetail, SQLSRV_FETCH_ASSOC)) {
    [$namaBarang, $stokTersedia] = [$dataBarang['namaBarang'], $dataBarang['stokBarang']];
} else {
    die("Error: Data untuk ID Barang '" . htmlspecialchars($idBarang) . "' tidak ditemukan di database.");
}

// Data sesi
[$nim, $npk, $tglPeminjamanBrg] = [
    $_SESSION['nim'] ?? null,
    $_SESSION['npk'] ?? null,
    $_SESSION['tglPeminjamanBrg'] ?? null
];

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

/// Inisialisasi variabel
$error = null;
$showModal = false;

//  stok barang
$sqlStok = "SELECT stokBarang FROM Barang WHERE idBarang = ?";
$paramsStok = [$idBarang];
$stmtStok = sqlsrv_query($conn, $sqlStok, $paramsStok);
$stokBarang = sqlsrv_fetch_array($stmtStok, SQLSRV_FETCH_ASSOC)['stokBarang'];
$stokBarangBaru = $stokBarang/2;

// Proses Peminjaman hanya jika metode POST
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $alasanPeminjamanBrg = $_POST['alasanPeminjamanBrg'];
    $jumlahBrg = (int)$_POST['jumlahBrg'];

    // Ubah format tanggal sebelum insert
    if ($tglPeminjamanBrg) {
        $dateObj = DateTime::createFromFormat('d-m-Y', $tglPeminjamanBrg);
        $tglPeminjamanBrgSQL = $dateObj ? $dateObj->format('Y-m-d') : null;
    } else {
        $tglPeminjamanBrgSQL = null;
    }

    // 1. Insert data peminjaman (tanpa statusPeminjaman)    
    $queryInsert = "INSERT INTO Peminjaman_Barang (idPeminjamanBrg, idBarang, tglPeminjamanBrg, nim, npk, jumlahBrg, sisaPinjaman, alasanPeminjamanBrg) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
    $paramsInsert = [$idPeminjamanBrg, $idBarang, $tglPeminjamanBrgSQL, $nim, $npk, $jumlahBrg, $jumlahBrg, $alasanPeminjamanBrg];
    $stmtInsert = sqlsrv_query($conn, $queryInsert, $paramsInsert);

    if ($stmtInsert) {
        // 2. Insert status peminjaman ke tabel Status_Peminjaman
        $queryInsertStatus = "INSERT INTO Status_Peminjaman (idPeminjamanBrg, statusPeminjaman) VALUES (?, ?)";
        $paramsInsertStatus = [$idPeminjamanBrg, 'Menunggu Persetujuan'];
        $stmtInsertStatus = sqlsrv_query($conn, $queryInsertStatus, $paramsInsertStatus);

        if ($stmtInsertStatus) {
            // 3. Jika insert berhasil, update stok barang
            $queryUpdate = "UPDATE Barang SET stokBarang = stokBarang - ? WHERE idBarang = ?";
            $paramsUpdate = [$jumlahBrg, $idBarang];
            $stmtUpdate = sqlsrv_query($conn, $queryUpdate, $paramsUpdate);

            if ($stmtUpdate) {
                $nama_peminjam = $nama;
                $untuk = 'PIC Aset'; // atau $_SESSION['user_role'] untuk peminjam
                $pesanNotif = "Pengajuan peminjaman barang oleh $nama_peminjam menunggu persetujuan.";
                $queryNotif = "INSERT INTO Notifikasi (pesan, status, untuk) VALUES (?, 'Belum Dibaca', ?)";
                sqlsrv_query($conn, $queryNotif, [$pesanNotif, $untuk]);
                $showModal = true;
            } else {
                $error = "Peminjaman tercatat, tetapi gagal mengupdate stok. Error: " . print_r(sqlsrv_errors(), true);
            }
        } else {
            $error = "Peminjaman tercatat, tetapi gagal mencatat status. Error: " . print_r(sqlsrv_errors(), true);
        }
    } else {
        $error = "Gagal menambahkan peminjaman barang. Error: " . print_r(sqlsrv_errors(), true);
    }
}


include '../../templates/header.php';
include '../../templates/sidebar.php';

?>

<main class="col bg-white px-3 px-md-4 py-3 position-relative">
    <h3 class="fw-semibold mb-3">Peminjaman Barang</h3>
    <div class="mb-3">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?= BASE_URL ?>/Menu/Menu Peminjam/dashboardPeminjam.php">Sistem Pengelolaan Lab</a></li>
                <li class="breadcrumb-item"><a href="<?= BASE_URL ?>/Menu/Menu Peminjam/cekBarang.php">Cek Barang</a></li>
                <li class="breadcrumb-item active" aria-current="page">Pengajuan Peminjaman Barang</li>
            </ol>
        </nav>
    </div>
    <div class="container mt-4">

        <div class="row justify-content-center">
            <div class="col-md-8 col-lg-12" style="margin-right: 20px;">
                <div class="card border border-dark">
                    <div class="card-header border-bottom border-dark text-white" style="background-color:rgb(9, 103, 185);">
                        <span class="fw-semibold">Peminjaman Barang</span>
                    </div>
                    <div class="card-body">

                        <?php if (!empty($error)): ?>
                            <div class="alert alert-danger" role="alert">
                                <?= $error ?>
                            </div>
                        <?php endif; ?>

                        <form id="formTambahPeminjamanBrg" method="POST">
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="namaBarang" class="form-label fw-semibold">Nama Barang</label>
                                        <input type="text" class="form-control-plaintext" name="namaBarang_display" value="<?= $namaBarang ?>">
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label fw-semibold">Tanggal Peminjaman</label>
                                        <input type="text" class="form-control-plaintext" name="tglDisplay" value="<?php if (!empty($tglPeminjamanBrg)) {
                                                                                                                                            $dateObj = DateTime::createFromFormat('d-m-Y', $tglPeminjamanBrg);
                                                                                                                                            echo $dateObj ? $dateObj->format('d M Y') : htmlspecialchars($tglPeminjamanBrg);
                                                                                                                                        } ?>">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="alasanPeminjamanBrg" class="form-label fw-semibold">
                                            Alasan Peminjaman <span id="alasanError" class="text-danger small mt-1 fw-normal" style="font-size: 0.95em; display:none;">*Harus Diisi</span>
                                        </label>
                                        <textarea class="form-control" id="alasanPeminjamanBrg" name="alasanPeminjamanBrg" rows="1" placeholder="Masukkan alasan peminjaman.."></textarea>
                                    </div>
                                    <div class="mb-3">
                                        <label for="jumlahBrg" class="form-label w-100 fw-semibold">
                                            Jumlah Peminjaman
                                            <span id="jumlahError" class="text-danger small mt-1 fw-normal" style="font-size: 0.95em; display:none;">*Jumlah harus lebih dari 0.</span>
                                        </label>
                                        <div class="input-group" style="max-width: 140px;">
                                            <button class="btn btn-outline-secondary" type="button" onclick="changeStok(-1)">-</button>
                                            <input class="form-control text-center" id="jumlahBrg" name="jumlahBrg" value="0" min="0" style="max-width: 70px;">
                                            <button class="btn btn-outline-secondary" type="button" onclick="changeStok(1)">+</button>
                                        </div>
                                        <small class="text-muted">Stok tersedia: <span id="stokBarang"><?= $stokBarangBaru ?></span></small>
                                    </div>
                                </div>


                                <div class="d-flex justify-content-between mt-3">
                                    <a href="<?= BASE_URL ?>/Menu/Menu Peminjam/cekBarang.php" class="btn btn-secondary">Kembali</a>
                                    <button type="submit" class="btn btn-primary">Ajukan Peminjaman</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
</main>


<?php
include '../../templates/footer.php';

?>