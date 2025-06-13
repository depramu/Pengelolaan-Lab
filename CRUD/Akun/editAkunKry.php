<?php
include '../../templates/header.php';

$npk = $_GET['id'] ?? null;

if (!$npk) {
    header('Location: ../../manajemenAkunKry.php');
    exit;
}

$showModal = false; // Initialize the modal visibility variable

$query = "SELECT * FROM Karyawan WHERE npk = ?";
$stmt = sqlsrv_query($conn, $query, [$npk]);
$data = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $npk_post = $_POST['npk']; // Menggunakan nama variabel berbeda untuk npk dari POST
    $namaKry = $_POST['namaKry'];
    $noHP = $_POST['noHP'];
    $jenisRole = $_POST['jenisRole'];
    $kataSandi = $_POST['kataSandi'];
    // $konfirmasiSandi = $_POST['konfirmasiSandi']; //Tidak digunakan di UPDATE jika hanya update kataSandi

    // Logika untuk update password: jika kataSandi diisi, maka update. Jika tidak, jangan update password.
    if (!empty($kataSandi)) {
        $query_update = "UPDATE Karyawan SET namaKry = ?, noHP = ?, jenisRole = ?, kataSandi = ? WHERE npk = ?";
        $params_update = [$namaKry, $noHP, $jenisRole, $kataSandi, $npk]; // $npk dari GET digunakan untuk WHERE clause
    } else {
        // Jika kataSandi tidak diisi, update data lain tanpa mengubah password
        $query_update = "UPDATE Karyawan SET namaKry = ?, noHP = ?, jenisRole = ? WHERE npk = ?";
        $params_update = [$namaKry, $noHP, $jenisRole, $npk]; // $npk dari GET digunakan untuk WHERE clause
    }

    $stmt_update = sqlsrv_query($conn, $query_update, $params_update);

    if ($stmt_update) {
        $showModal = true; // Set to true to show the modal
        // Re-fetch data to show updated values if staying on page, or remove if always redirecting
        $stmt = sqlsrv_query($conn, $query, [$npk]);
        $data = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);
    } else {
        $error = "Gagal mengubah data akun.";
        if (($errors = sqlsrv_errors()) != null) {
            foreach ($errors as $error_item) {
                $error .= "<br>SQLSTATE: " . $error_item['SQLSTATE'] . " Code: " . $error_item['code'] . " Message: " . $error_item['message'];
            }
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
                <li class="breadcrumb-item"><a href="../../Menu PIC/manajemenAkunKry.php">Manajemen Akun Karyawan</a></li>
                <li class="breadcrumb-item active" aria-current="page">Edit Akun Karyawan</li>
            </ol>
        </nav>
    </div>


    <!-- Edit Akun Karyawan -->
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
                        <span class="fw-semibold">Edit Akun Karyawan</span>
                    </div>
                    <div class="card-body">
                        <form method="POST">
                            <div class="mb-2 row">
                                <div class="col-md-6">
                                    <label for="npk" class="form-label">NPK</label>
                                    <input type="text" class="form-control" id="npk" name="npk" value="<?= htmlspecialchars($npk) ?>" disabled>
                                    <input type="hidden" name="npk" value="<?= htmlspecialchars($npk) ?>">
                                </div>
                                <div class="col-md-6">
                                    <label for="namaKry" class="form-label">Nama Lengkap</label>
                                    <input type="text" class="form-control" id="namaKry" name="namaKry" value="<?= htmlspecialchars($data['namaKry']) ?>" disabled>
                                    <input type="hidden" name="namaKry" value="<?= htmlspecialchars($data['namaKry']) ?>">
                                </div>
                            </div>
                            <div class="mb-2 row">
                                <div class="col-md-6">
                                    <label for="noHP" class="form-label">No HP</label>
                                    <input type="text" class="form-control" id="noHP" name="noHP" value="<?= htmlspecialchars($data['noHP']) ?>" disabled>
                                    <input type="hidden" name="noHP" value="<?= htmlspecialchars($data['noHP']) ?>">
                                </div>
                                <div class="col-md-6 mb-2">
                                    <label for="jenisRole" class="form-label">Jenis Role</label>
                                    <select class="form-select" id="jenisRole" name="jenisRole" disabled>
                                        <!-- <option value="" disabled selected>Pilih Role</option> -->
                                        <option value="PIC Aset" <?php if ($data['jenisRole'] == 'PIC Aset') echo 'selected'; ?>>PIC Aset</option>
                                        <option value="KA UPT" <?php if ($data['jenisRole'] == 'KA UPT') echo 'selected'; ?>>KA UPT</option>
                                    </select>
                                    <input type="hidden" name="jenisRole" value="<?= htmlspecialchars($data['jenisRole']) ?>">
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
                                        <a href="../../Menu PIC/manajemenAkunKry.php" class="btn btn-secondary">Kembali</a>
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
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <p>Data akun berhasil diubah.</p>
                    </div>
                    <div class="modal-footer">
                        <a href="../../Menu PIC/manajemenAkunKry.php" class="btn btn-primary">OK</a>
                    </div>
                </div>
            </div>
        </div>

    </div>
    <!-- End Edit Akun Karyawan -->


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


<?php include '../../templates/footer.php'; ?>