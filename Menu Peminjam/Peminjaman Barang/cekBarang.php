<?php
include '../../templates/header.php';

if (isset($_POST['submit'])) {
    $tglPeminjamanBrg = $_POST['tglPeminjamanBrg'];
    $_SESSION['tglPeminjamanBrg'] = $tglPeminjamanBrg;
    header('Location: lihatBarang.php');
    exit();
}

$tglPeminjamanBrg = $_SESSION['tglPeminjamanBrg'] ?? $_POST['tglPeminjamanBrg'] ?? '';
$query = "SELECT idBarang, namaBarang, lokasiBarang, stokBarang FROM Barang WHERE stokBarang > 0";
if ($tglPeminjamanBrg) {
    $query .= " AND tglPeminjamanBrg = ?";
    $params = array($tglPeminjamanBrg);
    $stmt = sqlsrv_query($conn, $query, $params);
} else {
    $stmt = sqlsrv_query($conn, $query);
}

include '../../templates/sidebar.php';
?>

<main class="col bg-white px-3 px-md-4 py-3 position-relative">
    <div class="mb-1">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="../dashboardPeminjam.php">Sistem Pengelolaan Lab</a></li>
                <li class="breadcrumb-item active" aria-current="page">Cek Barang</li>
            </ol>
        </nav>
    </div>

    <div class="container mt-4">
        <div class="row justify-content-center">
            <div class="col-md-8 col-lg-12" style="margin-right: 20px;">
                <div class="card border border-dark">
                    <div class="card-header bg-white border-bottom border-dark">
                        <span class="fw-semibold">Cek Barang</span>
                    </div>
                    <div class="card-body">
                        <form method="POST">
                            <div class="mb-2">
                                <label for="tglPeminjamanBrg" class="form-label">
                                    Pilih Tanggal Peminjaman <span id="error-message" style="color: red; display: none; margin-left: 10px;">*Harus Diisi</span>
                                </label>
                                <input type="date" class="form-control" id="tglPeminjamanBrg" name="tglPeminjamanBrg">
                            </div>
                            <div class="d-flex justify-content-end mt-4">
                                <button type="submit" class="btn btn-primary" name="submit">Cek</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<script>
    // Script validasi HANYA untuk form di halaman ini
    document.querySelector('form').addEventListener('submit', function(event) {
        let tglPeminjamanBrg = document.getElementById('tglPeminjamanBrg').value;
        let errorTanggal = document.getElementById('error-message');
        let isValid = true;

        if (tglPeminjamanBrg.trim() === '') {
            errorTanggal.style.display = 'inline';
            isValid = false;
        } else {
            errorTanggal.style.display = 'none'; // Sebaiknya disembunyikan jika valid
        }

        if (!isValid) {
            event.preventDefault(); // Mencegah submit jika ada input kosong
        }
    });
</script>



<?php
// Panggil template footer di paling akhir
include '../../templates/footer.php';
?>