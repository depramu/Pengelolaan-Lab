<?php
include '../../templates/header.php';
$idRuangan = $_GET['idRuangan'] ?? null;
if (empty($idRuangan)) {
    die("Error: ID Ruangan tidak ditemukan. Silakan kembali dan pilih ruangan yang ingin dipinjam.");
}

// Auto-generate id Peminjaman Ruangan dari database SQL Server
$idPeminjamanRuangan = 'PJR001';
$sqlId = "SELECT TOP 1 idPeminjamanRuangan FROM Peminjaman_Ruangan WHERE idPeminjamanRuangan LIKE 'PJR%' ORDER BY idPeminjamanRuangan DESC";
$stmtId = sqlsrv_query($conn, $sqlId);
if ($stmtId && $rowId = sqlsrv_fetch_array($stmtId, SQLSRV_FETCH_ASSOC)) {
    $lastId = $rowId['idPeminjamanRuangan']; // contoh: PJR012
    $num = intval(substr($lastId, 3));
    $newNum = $num + 1;
    $idPeminjamanRuangan = 'PJR' . str_pad($newNum, 3, '0', STR_PAD_LEFT);
}

$showModal = false;
$nim = $_SESSION['nim'] ?? null;
$npk = $_SESSION['npk'] ?? null;

$tglPeminjamanRuangan = $_SESSION['tglPeminjamanRuangan'] ?? null;
$waktuMulai = $_SESSION['waktuMulai'] ?? null;
$waktuSelesai = $_SESSION['waktuSelesai'] ?? null;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $alasanPeminjamanRuangan = $_POST['alasanPeminjamanRuangan'];

    if (empty($alasanPeminjamanRuangan)) {
        $error = "Alasan peminjaman ruangan tidak boleh kosong";
    } else {
        $sqlPeminjamanRuangan = "INSERT INTO Peminjaman_Ruangan (idPeminjamanRuangan, idRuangan, nim, npk, tglPeminjamanRuangan, waktuMulai, waktuSelesai, alasanPeminjamanRuangan, statusPeminjaman) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $params = [$idPeminjamanRuangan, $idRuangan, $nim, $npk, $tglPeminjamanRuangan, $waktuMulai, $waktuSelesai, $alasanPeminjamanRuangan, 'Menunggu Persetujuan'];
        $stmtPeminjamanRuangan = sqlsrv_query($conn, $sqlPeminjamanRuangan, $params);

        $ketersediaan = "UPDATE Ruangan SET ketersediaan = 'Tidak Tersedia' WHERE idRuangan = '$idRuangan'";
        $stmtKetersediaan = sqlsrv_query($conn, $ketersediaan);

        if ($stmtPeminjamanRuangan) {
            $showModal = true;
        } else {
            $error = "Gagal mengajukan peminjaman ruangan";
        }
    }
}
?>
<main class="col bg-white px-3 px-md-4 py-3 position-relative">
    <div class="mb-2">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?= BASE_URL ?>/Menu Peminjam/dashboardPeminjam.php">Sistem Pengelolaan Lab</a></li>
                <li class="breadcrumb-item"><a href="<?= BASE_URL ?>/Menu Peminjam/Peminjaman Ruangan/cekRuangan.php">Cek Ruangan</a></li>
                <li class="breadcrumb-item"><a href="<?= BASE_URL ?>/Menu Peminjam/Peminjaman Ruangan/lihatRuangan.php">Lihat Ruangan</a></li>
                <li class="breadcrumb-item active" aria-current="page">Pengajuan Peminjaman Ruangan</li>
            </ol>
        </nav>
    </div>

    <div class="container mt-4">
        <div class="row justify-content-center">
            <div class="col-md-8 col-lg-12" style="margin-right: 20px;">
                <div class="card border border-dark">
                    <div class="card-header bg-white border-bottom border-dark">
                        <span class="fw-semibold">PengajuanPeminjaman Ruangan</span>
                    </div>
                    <div class="card-body">
                        <form method="POST">

                            <div class="row">
                                <!-- ID Peminjaman & ID Ruangan (Auto Increment, Disabled Input) -->
                                <div class="col-md-6">
                                    <div class="mb-2" style="max-width: 400px;">
                                        <label for="idPeminjamanRuangan" class="form-label">ID Peminjaman</label>
                                        <input type="text" class="form-control" id="idPeminjamanRuangan" name="idPeminjamanRuangan" value="<?= $idPeminjamanRuangan ?>" disabled>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-2" style="max-width: 400px;">
                                        <label for="idRuangan" class="form-label">ID Ruangan</label>
                                        <input type="hidden" name="idRuangan" value="<?= $idRuangan ?>">
                                        <input type="text" class="form-control" id="idRuangan" name="idRuangan" value="<?= $idRuangan ?>" disabled>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <!-- Tanggal Peminjaman & NIM (Auto Increment, Disabled Input) -->
                                <div class="col-md-6">
                                    <div class="mb-2" style="max-width: 400px;">
                                        <label for="tglPeminjamanRuangan" class="form-label">Tanggal Peminjaman</label>
                                        <input type="text" class="form-control" id="tglPeminjamanRuangan" name="tglPeminjamanRuangan" value="<?= $tglPeminjamanRuangan ?>" disabled>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-2" style="max-width: 400px;">
                                        <label for="nim" class="form-label">NIM</label>
                                        <input type="text" class="form-control" id="nim" name="nim" value="<?= $nim ?>" disabled>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <!-- Waktu Peminjaman & NPK (Auto Increment, Disabled Input) -->
                                <div class="col-md-6">
                                    <div class="mb-2" style="max-width: 400px;">
                                        <label for="waktuMulai" class="form-label">Waktu Mulai</label>
                                        <input type="text" class="form-control" id="waktuMulai" name="waktuMulai" value="<?= $waktuMulai ?>" disabled>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-2" style="max-width: 400px;">
                                        <label for="npk" class="form-label">NPK</label>
                                        <input type="text" class="form-control" id="npk" name="npk" value="<?= $npk ?>" disabled>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-2" style="max-width: 400px;">
                                        <label for="waktuSelesai" class="form-label">Waktu Selesai</label>
                                        <input type="text" class="form-control" id="waktuSelesai" name="waktuSelesai" value="<?= $waktuSelesai ?>" disabled>
                                    </div>
                                </div>
                                <!-- Alasan Peminjaman -->
                                <div class="col-md-6">
                                    <label for="alasanPeminjamanRuangan" class="form-label">Alasan Peminjaman</label>
                                    <span id="error-message" style="color: red; display: none; margin-left: 10px;">*Harus Diisi</span>
                                    <textarea class="form-control" id="alasanPeminjamanRuangan" name="alasanPeminjamanRuangan" rows="2" style="max-width: 400px;"></textarea>

                                    <!--validasi kolom harus diisi -->
                                    <script>
                                        document.getElementById('alasanPeminjamanRuangan').addEventListener('input', function() {
                                            let alasanPeminjamanRuangan = document.getElementById('alasanPeminjamanRuangan').value;
                                            let errorMessage = document.getElementById('error-message');

                                            if (alasanPeminjamanRuangan.trim() === '') {
                                                errorMessage.style.display = 'inline';
                                            } else {
                                                errorMessage.style.display = 'inline';
                                            }
                                        });

                                        document.querySelector('form').addEventListener('submit', function(event) {
                                            let alasanPeminjamanRuangan = document.getElementById('alasanPeminjamanRuangan').value;
                                            let errorMessage = document.getElementById('error-message');

                                            if (alasanPeminjamanRuangan.trim() === '') {
                                                errorMessage.style.display = 'inline';
                                                event.preventDefault(); // Mencegah form dikirim jika input kosong
                                            }
                                        });
                                    </script>

                                </div>
                            </div>

                            <div class="d-flex justify-content-between mt-4">
                                <a href="../../Menu Peminjam/lihatRuangan.php" class="btn btn-secondary">Kembali</a>
                                <button type="submit" class="btn btn-primary">Ajukan Peminjaman</button>
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