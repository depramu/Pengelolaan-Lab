<?php
include '../../templates/header.php';

$idRuangan = $_GET['id'] ?? null;

if (!$idRuangan) {
    header('Location: ../../Menu PIC/manajemenRuangan.php');
    exit;
}

$query = "SELECT * FROM Ruangan WHERE idRuangan = ?";
$stmt = sqlsrv_query($conn, $query, [$idRuangan]);
$data = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);

if (!$data) {
    // Handle case where ID doesn't exist in DB
    header('Location: ../../Menu PIC/manajemenRuangan.php');
    exit;
}

$showModal = false; // For success modal
$error = ''; // For error messages

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $namaRuangan = $_POST['namaRuangan']; // This is from a hidden input, effectively not changed by user via this form.
    $kondisiRuangan = $_POST['kondisiRuangan'];
    $ketersediaan = $_POST['ketersediaan'];

    // Basic validation example
    if (empty($kondisiRuangan) || empty($ketersediaan)) {
        $error = "Kondisi dan Ketersediaan ruangan harus dipilih.";
    } else {
        $updateQuery = "UPDATE Ruangan SET namaRuangan = ?, kondisiRuangan = ?, ketersediaan = ? WHERE idRuangan= ?";
        $params = [$namaRuangan, $kondisiRuangan, $ketersediaan, $idRuangan];
        $updateStmt = sqlsrv_query($conn, $updateQuery, $params);

        if ($updateStmt) {
            $showModal = true;
            $stmt = sqlsrv_query($conn, $query, [$idRuangan]);
            $data = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);
        } else {
            $error = "Gagal mengubah data ruangan. Error: " . print_r(sqlsrv_errors(), true);
        }
    }
}



include '../../templates/sidebar.php';
?>

            <!-- Content Area -->
            <main class="col bg-white px-4 py-3 position-relative">
                <div class="mb-3">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="../../Menu PIC/dashboardPIC.php">Sistem Pengelolaan Lab</a></li>
                            <li class="breadcrumb-item"><a href="../../Menu PIC/manajemenRuangan.php">Manajemen Ruangan</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Edit Ruangan</li>
                        </ol>
                    </nav>
                </div>


                <!-- Edit Ruangan -->
                <div class="container mt-4">
                    <?php if (!empty($error)) : ?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <?php echo htmlspecialchars($error); ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    <?php endif; ?>

                    <div class="row justify-content-center">
                        <div class="col-md-8 col-lg-12" style="margin-right: 20px;">
                            <div class="card border border-dark">
                                <div class="card-header bg-white border-bottom border-dark">
                                    <span class="fw-semibold">Edit Ruangan</span>
                                </div>
                                <div class="card-body">
                                    <form method="POST">
                                        <div class="mb-2">
                                            <label class="form-label">ID Ruangan</label>
                                            <div class="form-control-plaintext"><?= htmlspecialchars($idRuangan) ?></div>
                                            <input type="hidden" name="idRuangan" value="<?= htmlspecialchars($idRuangan) ?>">
                                        </div>
                                        <div class="mb-2">
                                            <label class="form-label">Nama Ruangan
                                                <span id="namaError" class="text-danger ms-2" style="display:none;font-size:0.95em;">*Harus diisi</span>
                                            </label>
                                            <div class="form-control-plaintext"><?= htmlspecialchars($data['namaRuangan']) ?></div>
                                            <input type="hidden" id="namaRuangan" name="namaRuangan" value="<?= htmlspecialchars($data['namaRuangan']) ?>">
                                        </div>
                                        <div class="mb-2">
                                            <label for="kondisiRuangan" class="form-label">Kondisi Ruangan
                                                <span id="kondisiError" class="text-danger ms-2" style="display:none;font-size:0.95em;">*Harus diisi</span>
                                            </label>
                                            <select class="form-select" id="kondisiRuangan" name="kondisiRuangan">
                                                <option disabled selected>Pilih Kondisi</option>
                                                <option value="Baik" <?php if (isset($data['kondisiRuangan']) && $data['kondisiRuangan'] == 'Baik') echo 'selected'; ?>>Baik</option>
                                                <option value="Rusak" <?php if (isset($data['kondisiRuangan']) && $data['kondisiRuangan'] == 'Rusak') echo 'selected'; ?>>Rusak</option>
                                            </select>
                                        </div>
                                        <div class="mb-2">
                                            <label for="ketersediaan" class="form-label">Ketersediaan Ruangan
                                                <span id="ketersediaanError" class="text-danger ms-2" style="display:none;font-size:0.95em;">*Harus diisi</span>
                                            </label>
                                            <select class="form-select" id="ketersediaan" name="ketersediaan">
                                                <option disabled selected>Pilih Ketersediaan</option>
                                                <option value="Tersedia" <?php if (isset($data['ketersediaan']) && $data['ketersediaan'] == 'Tersedia') echo 'selected'; ?>>Tersedia</option>
                                                <option value="Tidak Tersedia" <?php if (isset($data['ketersediaan']) && $data['ketersediaan'] == 'Tidak Tersedia') echo 'selected'; ?>>Tidak Tersedia</option>
                                            </select>
                                        </div>
                                        <div class="d-flex justify-content-between mt-4">
                                            <a href="../../Menu PIC/manajemenRuangan.php" class="btn btn-secondary">Kembali</a>
                                            <button type="submit" class="btn btn-primary">Simpan</button>
                                        </div>
                                    </form>
                                </div>
                            </div>  
                        </div>
                    </div>

                    <!-- Modal Berhasil -->
                    <div class="modal fade" id="successModal" tabindex="-1">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="confirmModalLabel">Berhasil</h5>
                                    <a href="../../Menu PIC/manajemenRuangan.php"><button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button></a>
                                </div>
                                <div class="modal-body">
                                    <p>Data ruangan berhasil diubah.</p>
                                </div>
                                <div class="modal-footer">
                                    <a href="../../Menu PIC/manajemenRuangan.php" class="btn btn-primary">OK</a>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
                <!-- End Edit Ruangan -->


            </main>
    <script>
        document.querySelector('form').addEventListener('submit', function(e) {
            let valid = true;

            // Nama Ruangan
            const nama = document.getElementById('namaRuangan');
            const namaError = document.getElementById('namaError');
            if (nama && nama.value.trim() === '') {
                namaError.style.display = 'inline';
                valid = false;
            } else if (namaError) {
                namaError.style.display = 'none';
            }

            // Kondisi Ruangan
            const kondisi = document.getElementById('kondisiRuangan');
            const kondisiError = document.getElementById('kondisiError');
            if (kondisi && (!kondisi.value || kondisi.value === 'Pilih Kondisi')) {
                kondisiError.style.display = 'inline';
                valid = false;
            } else if (kondisiError) {
                kondisiError.style.display = 'none';
            }

            // Ketersediaan Ruangan
            const ketersediaan = document.getElementById('ketersediaan');
            const ketersediaanError = document.getElementById('ketersediaanError');
            if (ketersediaan && (!ketersediaan.value || ketersediaan.value === 'Pilih Ketersediaan')) {
                ketersediaanError.style.display = 'inline';
                valid = false;
            } else if (ketersediaanError) {
                ketersediaanError.style.display = 'none';
            }

            if (!valid) e.preventDefault();
        });
    </script>
<?php include '../../templates/footer.php'; ?>