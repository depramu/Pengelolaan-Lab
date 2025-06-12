<?php
include '../../templates/header.php';

$idPeminjamanRuangan = $_GET['id'] ?? '';
$query = "SELECT * FROM Penolakan WHERE idPeminjamanRuangan = ?";
$params = array($idPeminjamanRuangan);
$stmt = sqlsrv_query($conn, $query, $params);
$alasanPenolakan = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)['alasanPenolakan'] ?? '';


include '../../templates/sidebar.php';
?>
<main class="col bg-white px-4 py-3 position-relative">
    <div class="mb-3">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?= BASE_URL ?>/Menu PIC/dashboardPIC.php">Sistem Pengelolaan Lab</a></li>
                <li class="breadcrumb-item"><a href="<?= BASE_URL ?>/Menu PIC/Peminjaman Ruangan/peminjamanRuangan.php">Peminjaman Ruangan</a></li>
                <li class="breadcrumb-item active" aria-current="page">Penolakan Ruangan</li>
            </ol>
        </nav>
    </div>


    <!-- Penolakan -->
    <div class="container mt-4">
        <div class="row justify-content-center">
            <div class="col-md-8 col-lg-12 " style="margin-right: 20px;">
                <div class="card border border-dark">
                    <div class="card-header bg-white border-bottom border-dark">
                        <span class="fw-semibold">Penolakan Peminjaman Ruangan</span>
                    </div>
                    <div class="card-body">
                        <form method="GET">
                            <div class="mb-2">
                                <label class="form-label">ID Peminjaman Ruangan</label>
                                <input type="text" class="form-control" value="<?= htmlspecialchars($idPeminjamanRuangan) ?>" disabled>
                                <input type="hidden" name="idPeminjamanRuangan" value="<?= htmlspecialchars($idPeminjamanRuangan) ?>">
                            </div>

                            <div class="mb-2">
                                <label for="alasanPenolakan" class="form-label">Alasan Penolakan</label>
                                <textarea class="form-control" id="alasanPenolakan" name="alasanPenolakan" rows="3" style="resize: none;" disabled><?= htmlspecialchars($alasanPenolakan) ?></textarea>
                            </div>
                            <div class="d-flex justify-content-between mt-4">
                                <a href="peminjamanRuangan.php" class="btn btn-secondary">Kembali</a>
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
        const alasanField = document.getElementById('alasanPenolakan');
        const errorField = document.getElementById('alasanError');
        let valid = true;

        if (alasanField.value.trim() === '') {
            errorField.textContent = '*Harus Diisi';
            errorField.style.display = 'inline';
            valid = false;
        } else {
            errorField.textContent = '';
            errorField.style.display = 'none';
        }

        if (!valid) {
            e.preventDefault(); // Cegah form terkirim kalau tidak valid
        }
    });
</script>

<?php include '../../templates/footer.php'; ?>