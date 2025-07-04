<?php
require_once __DIR__ . '/../../../function/init.php';
authorize_role(['Peminjam']);

if (isset($_POST['submit'])) {
    $_SESSION['tglPeminjamanBrg'] = $_POST['tglPeminjamanBrg'] ?? '';
    header('Location: lihatBarang.php');
    exit();
}

// Pagination setup
$tglPeminjamanBrg = $_SESSION['tglPeminjamanBrg'] ?? '';
$perPage = 2;
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$offset = ($page - 1) * $perPage;

// Count total rows
$countQuery = "SELECT COUNT(*) AS total FROM Barang WHERE stokBarang > 0";
$countStmt = sqlsrv_query($conn, $countQuery);
$totalRows = 0;
if ($countStmt && $row = sqlsrv_fetch_array($countStmt, SQLSRV_FETCH_ASSOC)) {
    $totalRows = (int)$row['total'];
}
$totalPages = ceil($totalRows / $perPage);

// Fetch paginated data
$query = "SELECT idBarang, namaBarang, lokasiBarang, stokBarang FROM Barang WHERE stokBarang > 0 ORDER BY idBarang OFFSET ? ROWS FETCH NEXT ? ROWS ONLY";
$params = [$offset, $perPage];
$stmt = sqlsrv_query($conn, $query, $params);

include __DIR__ . '/../../../templates/header.php';
include __DIR__ . '/../../../templates/sidebar.php';
?>

<main class="col bg-white px-3 px-md-4 py-3 position-relative">
    <h3 class="fw-semibold mb-3">Peminjaman Barang</h3>
    <div class="mb-1">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?= BASE_URL ?>/Menu Peminjam/dashboardPeminjam.php">Sistem Pengelolaan Lab</a></li>
                <li class="breadcrumb-item active" aria-current="page">Cek Barang</li>
            </ol>
        </nav>
    </div>

    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <form method="POST" id="formCekKetersediaanBarang" action="">
                <h5 class="card-title mb-3 fw-bold">Cek Barang</h5>
                <div class="row g-3 align-items-end">
                    <!-- Tanggal Peminjaman -->
                    <div class="mb-2">
                        <label class="form-label fw-semibold">
                            Pilih Tanggal Peminjaman <span id="error-message" class="text-danger small mt-1 fw-normal" style="font-size: 0.95em; display:none;">*Harus Diisi</span>
                        </label>
                        <div class="d-flex gap-2">
                            <select id="tglHari" class="form-select" style="width: 80px;"></select>
                            <select id="tglBulan" class="form-select" style="width: 100px;"></select>
                            <select id="tglTahun" class="form-select" style="width: 100px;"></select>
                        </div>
                        <input type="hidden" id="tglPeminjamanBrg" name="tglPeminjamanBrg">
                    </div>
                </div>
            </form>
            <!-- Tombol Cek -->
            <div class="d-flex justify-content-end mt-4">
                <button type="submit" class="btn btn-primary" name="submit">Cek</button>
            </div>
        </div>
    </div>

    <!-- <div class="container mt-4">
        <div class="row justify-content-center">
            <div class="col-md-8 col-lg-12" style="margin-right: 20px;">
                <div class="card border border-dark">
                    <div class="card-header bg-white border-bottom border-dark">
                        <span class="fw-bold">Cek Barang</span>
                    </div>
                    <div class="card-body">
                        <form method="POST" id="formCekKetersediaanBarang" action="">
                            <div class="mb-2">
                                <label class="form-label fw-semibold">
                                    Pilih Tanggal Peminjaman <span id="error-message" class="text-danger small mt-1 fw-normal" style="font-size: 0.95em; display:none;">*Harus Diisi</span>
                                </label>
                                <div class="d-flex gap-2">
                                    <select id="tglHari" class="form-select" style="width: 80px;"></select>
                                    <select id="tglBulan" class="form-select" style="width: 100px;"></select>
                                    <select id="tglTahun" class="form-select" style="width: 100px;"></select>
                                </div>
                                <input type="hidden" id="tglPeminjamanBrg" name="tglPeminjamanBrg">
                            </div>
                            <div class="d-flex justify-content-end mt-4">
                                <button type="submit" class="btn btn-primary" name="submit">Cek</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div> -->
    <!-- Area Barang yang Tersedia -->
    <div id="areaBarangTersedia" style="display:none;">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h5 class="card-title mb-3 fw-bold">Daftar Barang yang Tersedia</h5>
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle table-bordered" id="tabelBarangTersedia">
                <thead class="table-light">
                    <tr class="text-center">
                        <th>ID Barang</th>
                        <th>Nama Barang</th>
                        <th>Stok Barang</th>
                        <th>Lokasi Barang</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $hasData = false;
                    if ($stmt === false) {
                        echo '<tr><td colspan="5" class="text-center text-danger">Gagal mengambil data dari database</td></tr>';
                    } else {
                        while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
                            $hasData = true;
                    ?>
                            <tr>
                                <td class="text-center"><?= htmlspecialchars($row['idBarang'] ?? '') ?></td>
                                <td><?= htmlspecialchars($row['namaBarang'] ?? '') ?></td>
                                <td class="text-center"><?= htmlspecialchars($row['stokBarang'] ?? '') ?></td>
                                <td class="text-center"><?= htmlspecialchars($row['lokasiBarang'] ?? '') ?></td>
                                <td class="td-aksi text-center align-middle">
                                    <a href="<?= BASE_URL ?>/CRUD/Peminjaman/tambahPeminjamanBrg.php?idBarang=<?= urlencode($row['idBarang']) ?>" class="d-inline-block">
                                        <img src="<?= BASE_URL ?>/icon/tandaplus.svg" class="plus-tambah w-25" alt="Tambah Peminjaman Barang" style="display: inline-block; vertical-align: middle;">
                                    </a>
                                </td>
                            </tr>
                    <?php
                        }
                        if (!$hasData) {
                            echo '<tr><td colspan="5" class="text-center">Tidak ada barang yang tersedia</td></tr>';
                        }
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
    <div id="paginationControlsContainer" class="mt-3" style="display:none;">
        <?php
        if ($totalPages > 1) {
            generatePagination($page, $totalPages);
        }
        ?>
    </div>
</main>

x

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Sembunyikan area barang tersedia dan pagination saat load
        document.getElementById('areaBarangTersedia').style.display = 'none';
        var pagin = document.getElementById('paginationControlsContainer');
        if (pagin) pagin.style.display = 'none';

        // Tangani klik tombol Cek
        const cekBtn = document.querySelector('button[name="submit"]');
        if (cekBtn) {
            cekBtn.addEventListener('click', function(e) {
                e.preventDefault();

                // Validasi tanggal 
                const hari = document.getElementById('tglHari').value;
                const bulan = document.getElementById('tglBulan').value;
                const tahun = document.getElementById('tglTahun').value;
                if (!hari || !bulan || !tahun) {
                    document.getElementById('error-message').style.display = 'inline';
                    return;
                } else {
                    document.getElementById('error-message').style.display = 'none';
                }

                // Set hidden input
                document.getElementById('tglPeminjamanBrg').value = `${tahun}-${bulan.padStart(2, '0')}-${hari.padStart(2, '0')}`;

                // Tampilkan daftar barang dan pagination
                document.getElementById('areaBarangTersedia').style.display = 'block';
                if (pagin) pagin.style.display = 'block';
                // Jika ingin reload data via AJAX, bisa tambahkan di sini
            });
        }
    });
</script>

<?php
include __DIR__ . '/../../../templates/footer.php';
?>