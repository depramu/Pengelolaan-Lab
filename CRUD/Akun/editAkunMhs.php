<?php
include '../../templates/header.php';

$nim = $_GET['id'] ?? null;

if (!$nim) {
    // Redirect to a more appropriate page if nim is not found, e.g., the main mahasiswa management page
    header('Location: ../../Menu PIC/manajemenAkunMhs.php');
    exit;
}

$showModal = false; // Initialize the modal visibility variable

$query = "SELECT * FROM Mahasiswa WHERE nim = ?";
$stmt = sqlsrv_query($conn, $query, [$nim]);
$data = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);

if (!$data) {   
    // Handle case where NIM doesn't exist in DB, redirect or show error
    // For now, redirecting back to management page
    header('Location: ../../Menu PIC/manajemenAkunMhs.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // No need to fetch nim from POST if it's from URL and disabled in form, use $nim from GET
    $namaMhs = $_POST['namaMhs']; // Should also be from $data if disabled, or ensure it's submitted if editable
    $kataSandi = $_POST['kataSandi'];
    // konfirmasiSandi is only for client-side validation, not stored

    // Assuming nim and namaMhs are not changed through this form as they are disabled
    // If they were changeable, they should be part of the $params array
    $updateQuery = "UPDATE Mahasiswa SET kataSandi = ? WHERE nim = ?";
    $params = [$kataSandi, $nim];
    $updateStmt = sqlsrv_query($conn, $updateQuery, $params);

    if ($updateStmt) {
        $showModal = true; // Set to true to show the modal
        // Re-fetch data to show updated values if needed, though kataSandi isn't displayed directly
        $stmt = sqlsrv_query($conn, $query, [$nim]); // Re-run original query
        $data = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);
    } else {
        $error = "Gagal mengubah data akun. Error: " . print_r(sqlsrv_errors(), true); // More detailed error
    }
}


include '../../template/sidebar.php';
?>
            <!-- Content Area -->
            <main class="col bg-white px-4 py-3 position-relative">
                <div class="mb-3">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="../../Menu PIC/dashboardPIC.php">Sistem Pengelolaan Lab</a></li>
                            <li class="breadcrumb-item"><a href="../../Menu PIC/manajemenAkunMhs.php">Manajemen Akun Mahasiswa</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Edit Akun</li>
                        </ol>
                    </nav>
                </div>


                <!-- Edit Akun Mahasiswa -->
                <div class="container mt-4">
                    <?php if (isset($error)) : ?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <?php echo $error; ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    <?php endif; ?>

                    <div class="row justify-content-center">
                        <div class="col-md-8 col-lg-12" style="margin-right: 20px;">
                            <div class="card border border-dark">
                                <div class="card-header bg-white border-bottom border-dark">
                                    <span class="fw-semibold">Edit Akun</span>
                                </div>
                                <div class="card-body">
                                    <form method="POST">
                                        <div class="mb-2 row">
                                            <div class="col-md-6">
                                                <label for="nim" class="form-label">NIM</label>
                                                <input type="text" class="form-control" id="nim" name="nim" value="<?= htmlspecialchars($nim) ?>" disabled>
                                                <input type="hidden" name="nim" value="<?= htmlspecialchars($nim) ?>">
                                            </div>
                                            <div class="col-md-6">
                                                <label for="namaMhs" class="form-label">Nama Lengkap</label>
                                                <input type="text" class="form-control" id="namaMhs" name="namaMhs" value="<?= htmlspecialchars($data['namaMhs']) ?>" disabled>
                                                <input type="hidden" name="namaMhs" value="<?= htmlspecialchars($data['namaMhs']) ?>">
                                            </div>
                                        </div>
                                        <div class="mb-2">
                                            <label for="kataSandi" class="form-label d-flex align-items-center">Kata Sandi
                                                <span class="text-danger ms-2" id="passError" style="display: none;">*Harus diisi</span>
                                                <span class="text-danger ms-2" id="passLengthError" style="display: none;">*Minimal 8 karakter</span>
                                            </label>
                                            <input type="password" class="form-control" id="kataSandi" name="kataSandi" value="<?= htmlspecialchars($data['kataSandi']) ?>">
                                        </div>
                                        <div class="mb-2">
                                            <label for="konfirmasiSandi" class="form-label d-flex align-items-center">Konfirmasi Kata Sandi
                                                <span class="text-danger ms-2" id="confPassError" style="display: none;">*Harus diisi</span>
                                                <span class="text-danger ms-2" id="passMatchError" style="display: none;">*Tidak sesuai</span>
                                            </label>
                                            <input type="password" class="form-control" id="konfirmasiSandi" name="konfirmasiSandi" value="<?= htmlspecialchars($data['kataSandi']) ?>">
                                            <div class="d-flex justify-content-between mt-4">
                                                <a href="../../Menu PIC/manajemenAkunMhs.php" class="btn btn-secondary">Kembali</a>
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
                                    <a href="../../Menu PIC/manajemenAkunMhs.php"><button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button></a>
                                </div>
                                <div class="modal-body">
                                    <p>Data akun berhasil diubah.</p>
                                </div>
                                <div class="modal-footer">
                                    <a href="../../Menu PIC/manajemenAkunMhs.php" class="btn btn-primary">OK</a>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
                <!-- End Edit Akun Mahasiswa -->


            </main>

        <script>
            document.querySelector('form').addEventListener('submit', function(e) {
                let pass = document.getElementById('kataSandi').value;
                let conf = document.getElementById('konfirmasiSandi').value;

                let passError = document.getElementById('passError');
                let passMatchError = document.getElementById('passMatchError');
                let confPassError = document.getElementById('confPassError');
                let passLengthError = document.getElementById('passLengthError');

                let valid = true;

                // Reset error
                passError.style.display = 'none';
                confPassError.style.display = 'none';
                passMatchError.style.display = 'none';
                passLengthError.style.display = 'none';

                if (pass === "") {
                    passError.style.display = 'block';
                    valid = false;
                }
                if (pass.length > 0 && pass.length < 8) {
                    passLengthError.style.display = 'block';
                    valid = false;
                }
                if (conf === "") {
                    confPassError.style.display = 'block';
                    valid = false;
                }
                if (pass !== "" && conf !== "" && pass !== conf) {
                    passMatchError.style.display = 'block';
                    valid = false;
                }

                if (!valid) e.preventDefault();
            });
        </script>

</body>

</html>