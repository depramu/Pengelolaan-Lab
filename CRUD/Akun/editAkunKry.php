<?php
require_once __DIR__ . '/../../function/init.php'; // Penyesuaian: gunakan init.php untuk inisialisasi dan otorisasi
authorize_role(['PIC Aset']);

$npk = $_GET['id'] ?? null;
$showModal = false;

if (!$npk) {
    header('Location: ../../Menu PIC/manajemenAkunKry.php');
    exit;
}

$query = "SELECT * FROM Karyawan WHERE npk = ?";
$stmt = sqlsrv_query($conn, $query, [$npk]);
$data = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $npk = $_POST['npk'];
    $nama = $_POST['nama'];
    $email = $_POST['email'];
    $jenisRole = $_POST['jenisRole'];

    if (!empty($email)) {
        $query_update = "UPDATE Karyawan SET nama = ?, email = ?, jenisRole = ? WHERE npk = ?";
        $params_update = [$nama, $email, $jenisRole, $npk];
    } else {
        $query_update = "UPDATE Karyawan SET nama = ?, jenisRole = ? WHERE npk = ?";
        $params_update = [$nama, $jenisRole, $npk];
    }

    $stmt_update = sqlsrv_query($conn, $query_update, $params_update);

    if ($stmt_update) {
        $showModal = true;
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

include '../../templates/header.php';
include '../../templates/sidebar.php';
?>

<main class="col bg-white px-4 py-3 position-relative">
    <h3 class="fw-semibold mb-3">Manajemen Akun Karyawan</h3>
    <div class="mb-3">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?= BASE_URL ?>/Menu/Menu PIC/dashboardPIC.php">Sistem Pengelolaan Lab</a></li>
                <li class="breadcrumb-item"><a href="<?= BASE_URL ?>/Menu/Menu PIC/manajemenAkunKry.php">Manajemen Akun Karyawan</a></li>
                <li class="breadcrumb-item active" aria-current="page">Ubah Akun Karyawan</li>
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
            <div class="col-md-8 col-lg-12" style="margin-right: 20px;">
                <div class="card border border-dark">
                    <div class="card-header border-bottom border-dark text-white" style="background-color:rgb(9, 103, 185);">
                        <span class="fw-semibold">Ubah Akun Karyawan</span>
                    </div>
                    <div class="card-body">
                        <form id="formEditAkunKry" method="POST">
                            <div class="mb-3 row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="npk" class="form-label fw-semibold d-flex align-items-center">
                                            NPK
                                        </label>
                                        <input type="text" class="form-control protect-input d-block bg-light" id="npk" name="npk" value="<?= htmlspecialchars($npk) ?>">
                                    </div>
                                    <div class="mb-3">
                                        <label for="nama" class="form-label fw-semibold d-flex align-items-center">
                                            Nama Lengkap
                                        </label>
                                        <input type="text" class="form-control protect-input d-block bg-light" id="nama" name="nama" value="<?= htmlspecialchars($data['nama']) ?>">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="email" class="form-label fw-semibold d-flex align-items-center">
                                            Email
                                            <span id="emailError" class="fw-normal text-danger ms-2" style="display:none;font-size:0.95em;"></span>
                                        </label>
                                        <input type="text" class="form-control" id="email" name="email" placeholder="Masukkan email.." value="<?= htmlspecialchars($data['email']) ?>">
                                    </div>
                                    <div class="mb-3">
                                        <label for="jenisRole" class="form-label fw-semibold d-flex align-items-center">
                                            Jenis Role
                                        </label>
                                        <select class="form-select protect-input d-block bg-light" id="jenisRole" name="jenisRole">
                                            <option value="KA UPT" <?php if ($data['jenisRole'] == 'KA UPT') echo 'selected'; ?>>KA UPT</option>
                                            <option value="PIC Aset" <?php if ($data['jenisRole'] == 'PIC Aset') echo 'selected'; ?>>PIC Aset</option>
                                            <option value="Peminjam" <?php if ($data['jenisRole'] == 'Peminjam') echo 'selected'; ?>>Peminjam</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="d-flex justify-content-between mt-4">
                                <a href="<?= BASE_URL ?>/Menu/Menu PIC/manajemenAkunKry.php" class="btn btn-secondary">Kembali</a>
                                <button type="submit" class="btn btn-primary">Simpan</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<?php include '../../templates/footer.php'; ?>