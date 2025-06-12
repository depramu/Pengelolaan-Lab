    <?php
    include '../../template/header.php';

    // Perbaikan: Ambil data dari tabel Peminjaman_Barang
    $idPeminjamanBrg = $_GET['id'] ?? '';
    $data = [];

    $showRejectedModal = false;
    $showModal = false;

    if (!empty($idPeminjamanBrg)) {
        $_SESSION['idPeminjamanBrg'] = $idPeminjamanBrg;

        $query = "SELECT * FROM Peminjaman_Barang WHERE idPeminjamanBrg = ?";
        $params = array($idPeminjamanBrg);
        $stmt = sqlsrv_query($conn, $query, $params);

        if ($stmt && sqlsrv_has_rows($stmt)) {
            $data = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);
        }
    }

    // Ekstrak data
    $idBarang = $data['idBarang'] ?? '';
    $nim = $data['nim'] ?? '';
    $npk = $data['npk'] ?? '';
    $tglPeminjamanBrg = isset($data['tglPeminjamanBrg']) ? $data['tglPeminjamanBrg']->format('Y-m-d') : '';
    $jumlahBrg = $data['jumlahBrg'] ?? '';
    $alasanPeminjamanBrg = $data['alasanPeminjamanBrg'] ?? '';
    $currentStatus = $data['statusPeminjaman'] ?? 'Diajukan';

    // Perbaikan: Proses persetujuan
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $query = "UPDATE Peminjaman_Barang 
                    SET statusPeminjaman = 'Sedang Dipinjam'
                    WHERE idPeminjamanBrg = ?";
        $params = array($idPeminjamanBrg);
        $stmt = sqlsrv_query($conn, $query, $params);

        if ($stmt) {
            $showModal = true;
        } else {
            $error = "Gagal melakukan pengajuan barang.";
            exit;
        }
    }
    include '../../template/sidebar.php';
    ?>
                <!-- Content Area -->
                <main class="col bg-white px-4 py-3 position-relative">
                    <div class="mb-3">
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="dashboardPIC.php">Sistem Pengelolaan Lab</a></li>
                                <li class="breadcrumb-item"><a href="peminjamanBarang.php">Peminjaman Barang</a></li>
                                <li class="breadcrumb-item active" aria-current="page">Pengajuan Barang</li>
                            </ol>
                        </nav>
                    </div>


                    <!-- Pengajuan Peminjaman Barang -->
                    <div class="container mt-4">
                        <div class="row justify-content-center">
                            <div class="col-md-8 col-lg-12" style="margin-right: 20px;">
                                <div class="card border border-dark">
                                    <div class="card-header bg-white border-bottom border-dark">
                                        <span class="fw-semibold">Pengajuan Peminjaman Barang</span>
                                    </div>
                                    <div class="card-body">
                                        <form method="POST">
                                            <input type="hidden" name="idPeminjamanBrg" value="<?= htmlspecialchars($idPeminjamanBrg) ?>">

                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="mb-2">
                                                        <label for="idBarang" class="form-label">ID Barang</label>
                                                        <input type="text" class="form-control" id="idBarang" name="idBarang" value="<?= htmlspecialchars($idBarang) ?>" disabled>
                                                    </div>
                                                    <div class="mb-2">
                                                        <label for="tglPeminjamanBrg" class="form-label">Tanggal Peminjaman</label>
                                                        <input type="text" class="form-control" id="tglPeminjamanBrg" name="tglPeminjamanBrg" value="<?= htmlspecialchars($tglPeminjamanBrg) ?>" disabled>
                                                    </div>
                                                    <div class="mb-2">
                                                        <label for="jumlahBrg" class="form-label">Jumlah Barang</label>
                                                        <input type="text" class="form-control" id="jumlahBrg" name="jumlahBrg" value="<?= htmlspecialchars($jumlahBrg) ?>" disabled>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="mb-2">
                                                        <label for="idPeminjamanBrgDisplay" class="form-label">ID Peminjaman Barang</label>
                                                        <input type="text" class="form-control" id="idPeminjamanBrgDisplay" value="<?= htmlspecialchars($idPeminjamanBrg) ?>" disabled>
                                                    </div>
                                                    <div class="mb-2">
                                                        <label for="nim" class="form-label">NIM</label>
                                                        <input type="text" class="form-control" id="nim" name="nim" value="<?= htmlspecialchars($nim) ?>" disabled>
                                                    </div>
                                                    <div class="mb-2">
                                                        <label for="npk" class="form-label">NPK</label>
                                                        <input type="text" class="form-control" id="npk" name="npk" value="<?= htmlspecialchars($npk) ?>" disabled>
                                                    </div>
                                                </div>
                                            </div>
                                            <!-- Alasan Peminjaman -->
                                            <div class="mb-2">
                                                <label for="alasanPeminjamanBrg" class="form-label">Alasan Peminjaman</label>
                                                <textarea class="form-control" id="alasanPeminjamanBrg" rows="3" style="width: 49%;" disabled><?= htmlspecialchars($alasanPeminjamanBrg) ?></textarea>
                                            </div>

                                            <div class="d-flex justify-content-end gap-2 mt-4">
                                                <!-- TOMBOL AKSI -->
                                                <div class="d-flex justify-content-end gap-2">
                                                    <!-- Link untuk Tolak -->
                                                    <a href="penolakanBarang.php?id=<?= htmlspecialchars($idPeminjamanBrg) ?>" class="btn btn-danger">Tolak</a>
                                                    <!-- Form untuk Setuju -->
                                                    <button type="submit" name="submit" class="btn btn-primary">Setuju</button>
                                                </div>
                                            </div>




                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </main>