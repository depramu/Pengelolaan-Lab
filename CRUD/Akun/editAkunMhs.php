<?php
include '../../templates/header.php';

$nim = $_GET['id'] ?? null;

if (!$nim) {
    header('Location: ../../manajemenAkunMhs.php');
    exit;
}

$showModal = false; 

$query = "SELECT * FROM Mahasiswa WHERE nim = ?";
$stmt = sqlsrv_query($conn, $query, [$nim]);
$data = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nim = $_POST['nim']; 
    $nama = $_POST['nama'];
    $email = $_POST['email'];
    $jenisRole = $_POST['jenisRole'];
    $kataSandi = $_POST['kataSandi'];

    if (!empty($kataSandi)) {
        $query_update = "UPDATE Mahasiswa SET nama = ?, email = ?, jenisRole = ?, kataSandi = ? WHERE nim = ?";
        $params_update = [$nama, $email, $jenisRole, $kataSandi, $nim]; 
    } else {
        $query_update = "UPDATE Mahasiswa SET nama = ?, email = ?, jenisRole = ? WHERE nim = ?";
        $params_update = [$nama, $email, $jenisRole, $nim]; 
    }

    $stmt_update = sqlsrv_query($conn, $query_update, $params_update);

    if ($stmt_update) {
        $showModal = true;
        $stmt = sqlsrv_query($conn, $query, [$nim]);
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
                <li class="breadcrumb-item"><a href="../../Menu PIC/manajemenAkunMhs.php">Manajemen Akun Mahasiswa</a></li>
                <li class="breadcrumb-item active" aria-current="page">Edit Akun Mahasiswa</li>
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
                        <span class="fw-semibold">Edit Akun Mahasiswa</span>
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
                                    <label for="nama" class="form-label">Nama Lengkap</label>
                                    <input type="text" class="form-control" id="nama" name="nama" value="<?= htmlspecialchars($data['nama']) ?>" disabled>
                                    <input type="hidden" name="nama" value="<?= htmlspecialchars($data['nama']) ?>">
                                </div>
                            </div>
                            <div class="mb-2 row">
                                <div class="col-md-6">
                                    <label for="email" class="form-label">Email</label>
                                    <input type="text" class="form-control" id="email" name="email" value="<?= htmlspecialchars($data['email']) ?>" disabled>
                                    <input type="hidden" name="email" value="<?= htmlspecialchars($data['email']) ?>">
                                </div>
                                <div class="col-md-6 mb-2">
                                    <label for="jenisRole" class="form-label">Jenis Role</label>
                                    <select class="form-select" id="jenisRole" name="jenisRole" disabled>
                                        <!-- <option value="" disabled selected>Pilih Role</option> -->
                                         <option value="KA UPT" <?php if ($data['jenisRole'] == 'KA UPT') echo 'selected'; ?>>KA UPT</option>
                                        <option value="PIC Aset" <?php if ($data['jenisRole'] == 'PIC Aset') echo 'selected'; ?>>PIC Aset</option>
                                        <option value="Peminjam" <?php if ($data['jenisRole'] == 'Peminjam') echo 'selected'; ?>>Peminjam</option>
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
                                        <a href="../../Menu PIC/manajemenAkunMhs.php" class="btn btn-secondary">Kembali</a>
                                        <button type="submit" class="btn btn-primary">Simpan</button>
                                    </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <?php if (isset($showModal)) : ?>
            <script>
                let modal = new bootstrap.Modal(document.getElementById('successModal'));
                modal.show();
            </script>
        <?php endif; ?>

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
                        <a href="../../Menu PIC/manajemenAkunMhs.php" class="btn btn-primary">OK</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>
<!-- End Edit Akun Mahasiswa -->

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