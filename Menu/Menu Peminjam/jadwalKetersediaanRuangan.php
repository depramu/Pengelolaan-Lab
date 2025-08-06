<?php
require_once __DIR__ . '/../../function/init.php';
authorize_role(['Peminjam']);

$tanggal = $_GET['tanggal'] ?? date('Y-m-d'); // Ambil dari URL, kalau nggak ada pakai hari ini
$minTanggal = date('Y-m-d');  // Hari ini (batas minimal)
$prevTanggal = date('Y-m-d', strtotime($tanggal . ' -1 day')); // Tanggal sebelumnya
$nextTanggal = date('Y-m-d', strtotime($tanggal . ' +1 day')); // Tanggal sesudahnya

// Tombol 'Previous' di-disable kalau tanggal sebelumnya lebih kecil dari hari ini
$isPrevDisabled = ($prevTanggal < $minTanggal);


// Query ke stored procedure
$ruanganQuery = "exec sp_jadwal_ketersediaan_ruangan @tanggal = '$tanggal'";
$ruanganResult = sqlsrv_query($conn, $ruanganQuery);

// Ambil kolom (ruangan) dari hasil query
$columns = [];
if ($ruanganResult) {
    $fieldCount = sqlsrv_num_fields($ruanganResult);
    for ($i = 0; $i < $fieldCount; $i++) {
        $fieldMeta = sqlsrv_field_metadata($ruanganResult)[$i];
        $columns[] = $fieldMeta['Name'];
    }
}

// Ambil data jadwal
$jadwal = [];
while ($row = sqlsrv_fetch_array($ruanganResult, SQLSRV_FETCH_ASSOC)) {
    $jadwal[] = $row;
}

include __DIR__ . '/../../templates/header.php';
include __DIR__ . '/../../templates/sidebar.php';
?>
<main class="col bg-white px-3 px-md-4 py-3">
    <h4 class="fw-semibold mb-3">Jadwal Ketersediaan Ruangan</h4>

    <div class="mb-4">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?= BASE_URL ?>/Menu/Menu Peminjam/dashboardPeminjam.php">Sistem Pengelolaan Lab</a></li>
                <li class="breadcrumb-item"><a href="<?= BASE_URL ?>/Menu/Menu Peminjam/Peminjaman Ruangan/cekRuangan.php">Cek Ruangan</a></li>
                <li class="breadcrumb-item active" aria-current="page">Jadwal Ruangan</li>
            </ol>
        </nav>
    </div>



    <form method="GET" class="mb-3 d-flex gap-2 align-items-center">
        <button type="submit" name="tanggal" value="<?= $prevTanggal ?>" class="btn btn-outline-primary" <?= $isPrevDisabled ? 'disabled' : '' ?>>&#8592; Back</button>
        <input type="date" value="<?= htmlspecialchars($tanggal) ?>" class="form-control" style="width:auto;" readonly>
        <button type="submit" name="tanggal" value="<?= $nextTanggal ?>" class="btn btn-outline-primary">&#8594; Next</button>
    </form>

    <div class="table-responsive" style="max-height: calc(100vh - 180px - 132px); overflow-y: auto;">
        <table class="table table-bordered text-center align-middle">
            <thead class="table-light">
                <tr>
                    <?php foreach ($columns as $col): ?>
                        <th><?= htmlspecialchars($col) ?></th>
                    <?php endforeach; ?>
                </tr>
            </thead>
        
            <tbody>
                <?php foreach ($jadwal as $row): ?>
                    <tr>
                        <?php foreach ($columns as $col): ?>
                            <?php
                            $val = $row[$col];
                            if ($col === 'Waktu') {
                                $class = '';
                            } else {
                                $class = $val === 'Tersedia' ? 'bg-success' : 'bg-danger';
                            }
                            ?>
                            <td class="<?= $class ?>"><?php if ($col === 'Waktu') echo htmlspecialchars($val); ?></td>
                        <?php endforeach; ?>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
            <!-- Legenda warna -->
    <div class="mb-2 d-flex align-items-center gap-3">
        <span class="d-inline-block" style="width: 24px; height: 24px; background: #198754; border-radius: 4px; border: 1px solid #ccc;"></span>
        <span class="me-3">Tersedia</span>
        <span class="d-inline-block" style="width: 24px; height: 24px; background: #dc3545; border-radius: 4px; border: 1px solid #ccc;"></span>
        <span>Tidak Tersedia</span>
    </div>
        <a href="<?= BASE_URL ?>/Menu/Menu Peminjam/cekRuangan.php" class="btn btn-secondary">Kembali</a>
    </div>
</main>
<?php include __DIR__ . '/../../templates/footer.php'; ?>
