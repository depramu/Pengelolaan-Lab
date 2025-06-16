<?php
include '../../templates/header.php';

$showModal = false;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $npk = $_POST['npk'];
    $nama = $_POST['nama'];
    $email = $_POST['email'];
    $jenisRole = $_POST['jenisRole'];
    $kataSandi = $_POST['kataSandi'];
    $konfirmasiSandi = $_POST['konfirmasiSandi'];

    $query = "INSERT INTO Karyawan (npk, nama, email, jenisRole, kataSandi) VALUES (?, ?, ?, ?, ?)";
    $params = [$npk, $nama, $email, $jenisRole, $kataSandi];
    $stmt = sqlsrv_query($conn, $query, $params);

    if ($stmt) {
        $showModal = true;
    } else {
        $error = "Gagal menambahkan akun.";
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
                <li class="breadcrumb-item active" aria-current="page">Tambah Akun</li>
            </ol>
        </nav>
    </div>


    <!-- Tambah Akun -->
    <div class="container mt-4">
        <?php if (isset($error)) : ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <?php echo $error; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <div class="row justify-content-center">
            <div class="col-md-8 col-lg-12 " style="margin-right: 20px;">
                <div class="card border border-dark">
                    <div class="card-header bg-white border-bottom border-dark">
                        <span class="fw-semibold">Tambah Akun</span>
                    </div>
                    <div class="card-body">
                        <form method="POST">
                            <div class="mb-2 row">
                                <div class="col-md-6">
                                    <label for="npk" class="form-label d-flex align-items-center">NPK
                                        <span id="npkError" class="text-danger ms-2" style="display:none;font-size:0.95em;"></span>
                                    </label>
                                    <input type="text" class="form-control" id="npk" name="npk">
                                </div>
                                <div class="col-md-6">
                                    <label for="nama" class="form-label d-flex align-items-center">Nama Lengkap
                                        <span id="namaError" class="text-danger ms-2" style="display:none;font-size:0.95em;"></span>
                                    </label>
                                    <input type="text" class="form-control" id="nama" name="nama">
                                </div>
                            </div>
                            <div class="mb-2 row">
                                <div class="col-md-6">
                                    <label for="email" class="form-label d-flex align-items-center">Email
                                        <span id="emailError" class="text-danger ms-2" style="display:none;font-size:0.95em;"></span>
                                    </label>
                                    <input type="text" class="form-control" id="email" name="email">
                                </div>
                                <div class="col-md-6 mb-2">
                                    <label for="jenisRole" class="form-label d-flex align-items-center">Role
                                        <span id="roleError" class="text-danger ms-2" style="display:none;font-size:0.95em;">*Harus diisi</span>
                                    </label>
                                    <select class="form-select" id="jenisRole" name="jenisRole">
                                        <option value="" disabled selected>Pilih Role</option>
                                        <option value="KA UPT">KA UPT</option>
                                        <option value="PIC Aset">PIC Aset</option>
                                        <option value="Peminjam">Peminjam</option>
                                    </select>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label for="kataSandi" class="form-label d-flex align-items-center">Kata Sandi
                                    <span id="passLengthError" class="text-danger ms-2" style="display:none;font-size:0.95em;">*Minimal 8 karakter</span>
                                    <span id="passError" class="text-danger ms-2" style="display:none;font-size:0.95em;">*Harus diisi</span>
                                </label>
                                <input type="password" class="form-control" id="kataSandi" name="kataSandi">
                            </div>
                            <div class="mb-2">
                                <label for="konfirmasiSandi" class="form-label d-flex align-items-center">Konfirmasi Kata Sandi
                                    <span id="passMatchError" class="text-danger ms-2" style="display:none;font-size:0.95em;">*Tidak sesuai</span>
                                    <span id="confPassError" class="text-danger ms-2" style="display:none;font-size:0.95em;">*Harus diisi</span>
                                </label>
                                <input type="password" class="form-control" id="konfirmasiSandi" name="konfirmasiSandi">
                            </div>

                            <div class="d-flex justify-content-between mt-4">
                                <a href="../../Menu PIC/manajemenAkunKry.php" class="btn btn-secondary">Kembali</a>
                                <button type="submit" class="btn btn-primary">Tambah</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
<<<<<<< HEAD
=======

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
                        <a href="../../Menu PIC/manajemenAkunKry.php"><button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button></a>
                    </div>
                    <div class="modal-body">
                        <p>Data akun berhasil ditambahkan.</p>
                    </div>
                    <div class="modal-footer">
                        <a href="../../Menu PIC/manajemenAkunKry.php" class="btn btn-primary">OK</a>
                    </div>
                </div>
            </div>
        </div>
>>>>>>> 9bc69401f031569cbc533de5d9a01bb0348554f3
    </div>
</main>
<!-- End Tambah Akun -->

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script>
    document.querySelector('form').addEventListener('submit', function(e) {
        let npk = document.getElementById('npk').value.trim();
        let nama = document.getElementById('nama').value.trim();
        let email = document.getElementById('email').value.trim();
        let jenisRole = document.getElementById('jenisRole').value;
        let pass = document.getElementById('kataSandi').value;
        let conf = document.getElementById('konfirmasiSandi').value;

        let npkError = document.getElementById('npkError');
        let namaError = document.getElementById('namaError');
        let emailError = document.getElementById('emailError');
        let roleError = document.getElementById('roleError');
        let passError = document.getElementById('passError');
        let passMatchError = document.getElementById('passMatchError');
        let confPassError = document.getElementById('confPassError');
        let passLengthError = document.getElementById('passLengthError');

        let valid = true;

        // Reset error
        npkError.style.display = 'none';
        namaError.style.display = 'none';
        emailError.style.display = 'none';
        passError.style.display = 'none';
        confPassError.style.display = 'none';
        passMatchError.style.display = 'none';
        passLengthError.style.display = 'none';
        roleError.style.display = 'none';

        if (npk === "") {
            npkError.textContent = '*Harus diisi';
            npkError.style.display = 'block';
            valid = false;
        } else if (!/^\d+$/.test(npk)) {
            npkError.textContent = '*Harus berupa angka';
            npkError.style.display = 'block';
            valid = false;
        }

        if (nama === "") {
            namaError.textContent = '*Harus diisi';
            namaError.style.display = 'block';
            valid = false;
        } else if (/\d/.test(nama)) {
            namaError.textContent = '*Harus berupa huruf';
            namaError.style.display = 'block';
            valid = false;
        }

        if (email === "") {
            emailError.textContent = '*Harus diisi';
            emailError.style.display = 'block';
            valid = false;
        } else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
            emailError.textContent = '*Format email tidak valid';
            emailError.style.display = 'block';
            valid = false;
        }

        // Role wajib diisi
        if (jenisRole === "") {
            roleError.style.display = 'block';
            valid = false;
        }

        // Password wajib diisi dan minimal 8 karakter
        if (pass === "") {
            passError.style.display = 'block';
            valid = false;
        }
        if (pass.length > 0 && pass.length < 8) {
            passLengthError.style.display = 'block';
            valid = false;
        }

        // Konfirmasi password wajib diisi
        if (conf === "") {
            confPassError.style.display = 'block';
            valid = false;
        }

        // Password dan konfirmasi harus sama
        if (pass !== "" && conf !== "" && pass !== conf) {
            passMatchError.style.display = 'block';
            valid = false;
        }

        if (!valid) e.preventDefault();
    });
</script>

<?php include '../../templates/footer.php'; ?>