<?php
require_once __DIR__ . '/../../../function/init.php';
authorize_role('PIC Aset');

$showModal = false;
$idPeminjamanBrg = $_GET['id'] ?? '';

if (empty($idPeminjamanBrg)) {
    die("Akses tidak valid. ID Peminjaman tidak ditemukan.");
}

// Get fresh data
$query_get = "SELECT pb.jumlahBrg, pb.sisaPinjaman, pb.idBarang, b.namaBarang
              FROM Peminjaman_Barang pb
              JOIN Barang b ON pb.idBarang = b.idBarang
              WHERE pb.idPeminjamanBrg = ?";
$params_get = [$idPeminjamanBrg];
$stmt_get = sqlsrv_query($conn, $query_get, $params_get);

if (!$stmt_get || !($data = sqlsrv_fetch_array($stmt_get, SQLSRV_FETCH_ASSOC))) {
    die("Data peminjaman tidak ditemukan.");
}

$idBarang = $data['idBarang'];
$jumlahBrg = $data['jumlahBrg'];
$sisaPinjaman = $data['sisaPinjaman'];
$namaBarang = $data['namaBarang'];

$nim = $data['nim'] ?? ''; // Pastikan $nim diinisialisasi, bisa dari session atau data yang diambil    

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $jumlahPengembalian = (int)$_POST['jumlahPengembalian'];
    $catatan = $_POST['catatanPengembalianBarang'];
    $kondisiBrg = $_POST['kondisiBrg'];

    // Validate input
    if (
        $jumlahPengembalian <= 0 || $jumlahPengembalian > $sisaPinjaman ||
        empty($kondisiBrg) || $kondisiBrg == 'Pilih Kondisi Barang'
    ) {
        $error = "Data tidak valid. Pastikan jumlah dan kondisi barang benar.";
    } else {
        sqlsrv_begin_transaction($conn);

        try {
            // 1. Check if return record exists
            $check_query = "SELECT idPengembalian FROM Pengembalian_Barang 
                            WHERE idPeminjamanBrg = ?";
            $check_stmt = sqlsrv_query($conn, $check_query, [$idPeminjamanBrg]);

            if ($check_stmt && sqlsrv_fetch($check_stmt)) {
                // UPDATE existing record
                $update_query = "UPDATE Pengembalian_Barang 
                                SET jumlahPengembalian = ?,
                                    kondisiBrg = ?,
                                    catatanPengembalianBarang = ?
                                WHERE idPeminjamanBrg = ?";
                $update_params = [$jumlahPengembalian, $kondisiBrg, $catatan, $idPeminjamanBrg];
                $stmt = sqlsrv_query($conn, $update_query, $update_params);
            } else {
                // INSERT new record
                $insert_query = "INSERT INTO Pengembalian_Barang 
                                 (idPeminjamanBrg, jumlahPengembalian, kondisiBrg, catatanPengembalianBarang)
                                 VALUES (?, ?, ?, ?)";
                $insert_params = [$idPeminjamanBrg, $jumlahPengembalian, $kondisiBrg, $catatan];
                $stmt = sqlsrv_query($conn, $insert_query, $insert_params);
            }

            // 2. Update remaining loan amount
            $sisaBaru = $sisaPinjaman - $jumlahPengembalian;
            $update_peminjaman = "UPDATE Peminjaman_Barang 
                                 SET sisaPinjaman = ? 
                                 WHERE idPeminjamanBrg = ?";
            sqlsrv_query($conn, $update_peminjaman, [$sisaBaru, $idPeminjamanBrg]);

            // 3. Update status
            $status = ($sisaBaru == 0) ? 'Telah Dikembalikan' : 'Sebagian Dikembalikan';
            $update_status = "UPDATE Status_Peminjaman 
                              SET statusPeminjaman = ? 
                              WHERE idPeminjamanBrg = ?";
            sqlsrv_query($conn, $update_status, [$status, $idPeminjamanBrg]);

            // 4. Update stock
            $update_stock = "UPDATE Barang 
                            SET stokBarang = stokBarang + ? 
                            WHERE idBarang = ?";
            sqlsrv_query($conn, $update_stock, [$jumlahPengembalian, $idBarang]);

            $untuk = $nim; // atau $_SESSION['user_role'] untuk peminjam
            $pesanNotif = "Barang dengan ID $idPeminjamanBrg telah dikembalikan.";
            $queryNotif = "INSERT INTO Notifikasi (pesan, status, untuk) VALUES (?, 'Belum Dibaca', ?)";
            sqlsrv_query($conn, $queryNotif, [$pesanNotif, $untuk]);
            sqlsrv_commit($conn);
            header("Location: pengembalianBarang.php?id=$idPeminjamanBrg&success=1");
            exit;
        } catch (Exception $e) {
            sqlsrv_rollback($conn);
            $error = "Gagal memproses pengembalian: " . $e->getMessage();
        }
    }
}

$showModal = isset($_GET['success']);

include '../../../templates/header.php';
include '../../../templates/sidebar.php';
?>

<main class="col bg-white px-3 px-md-4 py-3 position-relative">
    <h3 class="fw-semibold mb-3">Peminjaman Barang</h3>
    <div class="mb-4">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?= BASE_URL ?>Menu/Menu PIC/dashboardPIC.php">Sistem Pengelolaan Lab</a></li>
                <li class="breadcrumb-item"><a href="<?= BASE_URL ?>Menu/Menu PIC/Peminjaman Barang/peminjamanBarang.php">Peminjaman Barang</a></li>
                <li class="breadcrumb-item active" aria-current="page">Pengembalian Barang</li>
            </ol>
        </nav>
    </div>

    <div class="container mt-4">
        <div class="row justify-content-center">
            <div class="col-md-8 col-lg-12" style="margin-right: 20px;">
                <div class="card border border-dark">
                    <div class="card-header border-bottom border-dark text-white" style="background-color:rgb(9, 103, 185);">
                        <span class="fw-semibold">Pengembalian Barang</span>
                    </div>

                    <div class="card-body scrollable-card-content">
                        <form id="formPengembalianBarang" method="POST">
                            <div class='mb-3 row'>
                                <div class="col-md-6">
                                    <label for="idPeminjamanBrg" class="form-label fw-semibold">ID Peminjaman</label>
                                    <input type="text" class="form-control protect-input d-block bg-light" id="idPeminjamanBrg" name="idPeminjamanBrg" value="<?= htmlspecialchars($idPeminjamanBrg) ?>">
                                </div>
                                <div class="col-md-6">
                                    <label for="namaBarang" class="form-label fw-semibold">Nama Barang</label>
                                    <input type="text" class="form-control protect-input d-block bg-light" id="namaBarang" name="namaBarang" value="<?= htmlspecialchars($namaBarang) ?>">
                                </div>
                            </div>
                            <div class="mb-3 row">
                                <div class="col-md-3">
                                    <label for="jumlahBrg" class="form-label fw-semibold">Jumlah Peminjaman</label>
                                    <input type="text" class="form-control protect-input d-block bg-light" id="jumlahBrg" name="jumlahBrg" value="<?= $jumlahBrg ?>">
                                    <input type="hidden" id="sisaPinjaman" value="<?= $sisaPinjaman ?>">
                                    <?php if ($sisaPinjaman == 0): ?>
                                        <span class="text-success small">Semua barang sudah dikembalikan.</span>
                                    <?php else: ?>
                                        <span class="text-primary small">Sisa yang harus dikembalikan: <?= $sisaPinjaman ?></span>
                                    <?php endif; ?>
                                </div>
                                <div class="col-md-3">
                                    <label for="jumlahPengembalian" class="form-label w-100 fw-semibold">Jumlah Pengembalian
                                        <span id="jumlahError" class="text-danger small mt-1 fw-normal" style="font-size:0.95em;display:none;"></span>
                                    </label>
                                    <div class="input-group" style="max-width: 140px;">
                                        <button class="btn btn-outline-secondary" type="button" onclick="changeStok(-1)">-</button>
                                        <input class="form-control text-center" id="jumlahPengembalian" name="jumlahPengembalian" value="0" min="0" max="<?= $sisaPinjaman ?>" style="max-width: 70px;">
                                        <button class="btn btn-outline-secondary" type="button" onclick="changeStok(1)">+</button>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label for="txtKondisi" class="form-label fw-semibold">Kondisi Barang
                                        <span id="kondisiError" class="text-danger small mt-1 fw-normal" style="font-size:0.95em;display:none;"></span>
                                    </label>
                                    <select class="form-select" id="txtKondisi" name="kondisiBrg">
                                        <option hidden selected>Pilih Kondisi Barang</option>
                                        <option value="Baik">Baik</option>
                                        <option value="Rusak">Rusak</option>
                                    </select>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label for="catatanPengembalianBarang" class="form-label fw-semibold">Catatan Pengembalian
                                    <span id="catatanError" class="text-danger small mt-1 fw-normal" style="font-size:0.95em;display:none;"></span>
                                </label>
                                <textarea type="text" class="form-control" id="catatanPengembalianBarang" name="catatanPengembalianBarang" rows="3" style="resize: none;" placeholder="Masukkan catatan pengembalian.."></textarea>
                            </div>
                            <div class="d-flex justify-content-between mt-4">
                                <a href="peminjamanBarang.php" class="btn btn-secondary">Kembali</a>
                                <button type="submit" class="btn btn-primary">Kirim</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

    </div>
</main>

<?php include '../../../templates/footer.php'; ?>