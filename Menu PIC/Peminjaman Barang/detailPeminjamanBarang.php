    <?php
    include '../../templates/header.php';
    $idPeminjamanBrg = $_GET['id'] ?? '';
    $data = [];

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


    include '../../templates/sidebar.php';
    ?>
    <main class="col bg-white px-4 py-3 position-relative">
        <h3 class="fw-semibold mb-3">Detail Peminjaman Barang</h3>
        <div class="mb-3">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="<?= BASE_URL ?>/Menu PIC/dashboardPIC.php">Sistem Pengelolaan Lab</a></li>
                    <li class="breadcrumb-item"><a href="<?= BASE_URL ?>/Menu PIC/Peminjaman Barang/peminjamanBarang.php">Peminjaman Barang</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Detail Peminjaman Barang</li>
                </ol>
            </nav>
        </div>

        <div class="container mt-4">
            <div class="row justify-content-center">
                <div class="col-md-8 col-lg-12" style="margin-right: 20px;">
                    <div class="card border border-dark">
                        <div class="card-header bg-white border-bottom border-dark">
                            <span class="fw-semibold">Pengajuan Peminjaman Barang</span>
                        </div>
                        <div class="card-body">
                            <form method="POST">

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-2">
                                            <label for="idBarang" class="form-label fw-bold">ID Barang</label>
                                            <div class="form-control-plaintext"><?= htmlspecialchars($idBarang) ?></div>
                                            <input type="hidden" class="form-control" id="idBarang" name="idBarang" value="<?= htmlspecialchars($idBarang) ?>">
                                        </div>
                                        <div class="mb-2">
                                            <label for="tglPeminjamanBrg" class="form-label fw-bold">Tanggal Peminjaman</label>
                                            <div class="form-control-plaintext"><?= htmlspecialchars($tglPeminjamanBrg) ?></div>
                                            <input type="hidden" class="form-control" id="tglPeminjamanBrg" name="tglPeminjamanBrg" value="<?= htmlspecialchars($tglPeminjamanBrg) ?>">
                                        </div>
                                        <div class="mb-2">
                                            <label for="jumlahBrg" class="form-label fw-bold">Jumlah Barang</label>
                                            <div class="form-control-plaintext"><?= htmlspecialchars($jumlahBrg) ?></div>
                                            <input type="hidden" class="form-control" id="jumlahBrg" name="jumlahBrg" value="<?= htmlspecialchars($jumlahBrg) ?>">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-2">
                                            <label for="idPeminjamanBrgDisplay" class="form-label fw-bold">ID Peminjaman Barang</label>
                                            <div class="form-control-plaintext"><?= htmlspecialchars($idPeminjamanBrg) ?></div>
                                            <input type="hidden" class="form-control" id="idPeminjamanBrgDisplay" value="<?= htmlspecialchars($idPeminjamanBrg) ?>">
                                        </div>
                                        <div class="mb-2">
                                            <label for="nim" class="form-label fw-bold">NIM</label>
                                            <div class="form-control-plaintext"><?= htmlspecialchars($nim) ?></div>
                                            <input type="hidden" class="form-control" id="nim" name="nim" value="<?= htmlspecialchars($nim) ?>">
                                        </div>
                                        <div class="mb-2">
                                            <label for="npk" class="form-label fw-bold">NPK</label>
                                            <input type="hidden" class="form-control" id="npk" name="npk" value="<?= htmlspecialchars($npk) ?>">
                                        </div>
                                    </div>
                                </div>
                                <!-- Alasan Peminjaman -->
                                <div class="mb-2">
                                    <label for="alasanPeminjamanBrg" class="form-label fw-bold">Alasan Peminjaman</label>
                                    <div class="form-control-plaintext"><?= htmlspecialchars($alasanPeminjamanBrg) ?></div>
                                    <textarea class="form-control" id="alasanPeminjamanBrg" rows="3" style="width: 49%;" hidden><?= htmlspecialchars($alasanPeminjamanBrg) ?></textarea>
                                </div>

                                <div class="d-flex justify-content-start gap-2 mt-4">
                                    <div class="d-flex justify-content-between mt-4">
                                        <a href="<?= BASE_URL ?>/Menu PIC/Peminjaman Barang/peminjamanBarang.php" class="btn btn-secondary">Kembali</a>
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