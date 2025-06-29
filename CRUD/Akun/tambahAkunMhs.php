<?php
include '../../templates/header.php';

$showModal = false;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nim = $_POST['nim'];
    $nama = $_POST['nama'];
    $email = $_POST['email'];
    $jenisRole = $_POST['jenisRole'];
    $kataSandi = $_POST['kataSandi'];
    $konfirmasiSandi = $_POST['konfirmasiSandi'];

    $cekNim = sqlsrv_query($conn, "SELECT nim FROM Mahasiswa WHERE nim = ?", [$nim]);
    if ($cekNim && sqlsrv_has_rows($cekNim)) {
        $nimError = "*NIM sudah terdaftar";
    } else {
        $query = "INSERT INTO Mahasiswa (nim, nama, email, jenisRole, kataSandi) VALUES (?, ?, ?, ?, ?)";
        $params = [$nim, $nama, $email, $jenisRole, $kataSandi];
        $stmt = sqlsrv_query($conn, $query, $params);

        if ($stmt) {
            $showModal = true;
        } else {
            $error = "Gagal menambahkan akun.";
        }
    }
}
include '../../templates/sidebar.php';
?>

<main class="col bg-white px-4 py-3 position-relative">
    <h3 class="fw-semibold mb-3">Manajemen Akun Mahasiswa</h3>
    <div class="mb-3">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="../../Menu PIC/dashboardPIC.php">Sistem Pengelolaan Lab</a></li>
                <li class="breadcrumb-item"><a href="../../Menu PIC/manajemenAkunMhs.php">Manajemen Akun Mahasiswa</a></li>
                <li class="breadcrumb-item active" aria-current="page">Tambah Akun Mahasiswa</li>
            </ol>
        </nav>
    </div>
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
                        <span class="fw-bold">Tambah Akun Mahasiswa</span>
                    </div>
                    <div class="card-body">
                        <form method="POST">
                            <div class="mb-2 row">
                                <div class="col-md-6">
                                    <label for="nim" class="form-label fw-semibold d-flex align-items-center">NIM
                                        <span id="nimError" class="fw-normal text-danger ms-2" style="display:none;font-size:0.95em;"></span>
                                        <?php if (!empty($nimError)): ?>
                                            <span class="fw-normal text-danger ms-2" style="font-size:0.95em;"><?= $nimError ?></span>
                                        <?php endif; ?>
                                    </label>
                                    <input type="text" class="form-control" id="nim" name="nim" value="<?= isset($nim) ? htmlspecialchars($nim) : '' ?>">
                                </div>
                                <div class="col-md-6">
                                    <label for="nama" class="form-label fw-semibold d-flex align-items-center">Nama Lengkap
                                        <span id="namaError" class="fw-normal text-danger ms-2" style="display:none;font-size:0.95em;"></span>
                                    </label>
                                    <input type="text" class="form-control" id="nama" name="nama" value="<?= isset($nama) ? htmlspecialchars($nama) : '' ?>">
                                </div>
                            </div>
                            <div class="mb-2 row">
                                <div class="col-md-6">
                                    <label for="email" class="form-label fw-semibold d-flex align-items-center">Email
                                        <span id="emailError" class="fw-normal text-danger ms-2" style="display:none;font-size:0.95em;"></span>
                                    </label>
                                    <input type="text" class="form-control" id="email" name="email" value="<?= isset($email) ? htmlspecialchars($email) : '' ?>">
                                </div>
                                <div class="col-md-6 mb-2">
                                    <label for="jenisRole" class="form-label fw-semibold d-flex align-items-center">Role
                                        <span id="roleError" class="fw-normal text-danger ms-2" style="display:none;font-size:0.95em;"></span>
                                    </label>
                                    <select class="form-select" id="jenisRole" name="jenisRole">
                                        <option value="" disabled>Pilih Role</option>
                                        <option value="Peminjam">Peminjam</option>
                                    </select>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label for="kataSandi" class="form-label fw-semibold d-flex align-items-center">Kata Sandi
                                    <span id="passError" class="fw-normal text-danger ms-2" style="display:none;font-size:0.95em;"></span>
                                </label>
                                <input type="password" class="form-control" id="kataSandi" name="kataSandi" value="<?= isset($kataSandi) ? htmlspecialchars($kataSandi) : '' ?>">
                            </div>
                            <div class="mb-2">
                                <label for="konfirmasiSandi" class="form-label fw-semibold d-flex align-items-center">Konfirmasi Kata Sandi
                                    <span id="confPassError" class="fw-normal text-danger ms-2" style="display:none;font-size:0.95em;"></span>
                                </label>
                                <input type="password" class="form-control" id="konfirmasiSandi" name="konfirmasiSandi" value="<?= isset($konfirmasiSandi) ? htmlspecialchars($konfirmasiSandi) : '' ?>">
                            </div>

                            <div class="d-flex justify-content-between mt-4">
                                <a href="../../Menu PIC/manajemenAkunMhs.php" class="btn btn-secondary">Kembali</a>
                                <button type="submit" class="btn btn-primary">Tambah</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>


<script>
    document.querySelector('form').addEventListener('submit', function(e) {
        let nim = document.getElementById('nim').value.trim();
        let nama = document.getElementById('nama').value.trim();
        let email = document.getElementById('email').value.trim();
        let jenisRole = document.getElementById('jenisRole').value;
        let pass = document.getElementById('kataSandi').value;
        let conf = document.getElementById('konfirmasiSandi').value;

        let nimError = document.getElementById('nimError');
        let namaError = document.getElementById('namaError');
        let emailError = document.getElementById('emailError');
        let roleError = document.getElementById('roleError');
        let passError = document.getElementById('passError');
        let confPassError = document.getElementById('confPassError');
        let passPattern = /^(?=.*[A-Za-z])(?=.*\d).{8,}$/;

        let valid = true;

        // Reset error messages
        nimError.style.display = 'none';
        namaError.style.display = 'none';
        emailError.style.display = 'none';
        roleError.style.display = 'none';
        passError.style.display = 'none';

        if (nim === "") {
            nimError.textContent = '*Harus diisi';
            nimError.style.display = 'inline';
            valid = false;
        } else if (!/^\d+$/.test(nim)) {
            nimError.textContent = '*Harus berupa angka';
            nimError.style.display = 'inline';
            valid = false;
        }

        if (nama === "") {
            namaError.textContent = '*Harus diisi';
            namaError.style.display = 'inline';
            valid = false;
        } else if (/\d/.test(nama)) {
            namaError.textContent = '*Harus berupa huruf';
            namaError.style.display = 'inline';
            valid = false;
        }

        if (email === "") {
            emailError.textContent = '*Harus diisi';
            emailError.style.display = 'inline';
            valid = false;
        } else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
            emailError.textContent = '*Format email tidak valid';
            emailError.style.display = 'inline';
            valid = false;
        }

        if (jenisRole === "") {
            roleError.textContent = '*Harus diisi';
            roleError.style.display = 'inline';
            valid = false;
        }

        if (pass === "") {
            passError.textContent = '*Harus diisi';
            passError.style.display = 'inline';
            valid = false;
        } else if (pass.length > 0 && pass.length < 8) {
            passError.textContent = '*Minimal 8 karakter';
            passError.style.display = 'inline';
            valid = false;
        } else if (!passPattern.test(pass)) {
            passError.textContent = '*Harus mengandung huruf dan angka';
            passError.style.display = 'inline';
            valid = false;
        }

        if (conf === "") {
            confPassError.textContent = '*Harus diisi';
            confPassError.style.display = 'inline';
            valid = false;
        } else if (pass !== "" && conf !== "" && pass !== conf) {
            confPassError.textContent = '*Tidak sesuai';
            confPassError.style.display = 'inline';
            valid = false;
        }

        if (!valid) e.preventDefault();
    });
</script>

<?php include '../../templates/footer.php'; ?>