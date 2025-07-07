<?php
require_once __DIR__ . '/../../../function/init.php'; // Pastikan path ke init.php sudah benar

authorize_role(['Peminjam']);

$showTable = false; // Defaultnya, tabel tidak ditampilkan
$selectedDay = '';
$selectedMonth = '';
$selectedYear = '';

// 1. Handle form submission (jika ada)
if (isset($_POST['submit'])) {
    $tglPeminjaman = $_POST['tglPeminjamanBrg'] ?? '';
    if (!empty($tglPeminjaman)) {
        // Simpan tanggal yang baru di-submit ke session
        $_SESSION['tglPeminjamanBrg'] = $tglPeminjaman;
    } else {
        // Jika submit tapi kosong, hapus session agar kembali ke awal
        unset($_SESSION['tglPeminjamanBrg']);
    }
}

// 2. PERBAIKAN UTAMA: Cek session untuk menentukan state halaman
// Logika ini berjalan untuk GET (pagination) dan POST
if (!empty($_SESSION['tglPeminjamanBrg'])) {
    // Jika ada tanggal di session, berarti tabel harus selalu ditampilkan
    $showTable = true;

    // Ambil kembali tanggal dari session untuk mengisi ulang form date picker
    // agar pilihan tanggal tidak kembali ke hari ini.
    list($day, $month, $year) = explode('-', $_SESSION['tglPeminjamanBrg']);
    $selectedDay = (int)$day;
    $selectedMonth = (int)$month;
    $selectedYear = (int)$year;
}

// 3. Logika pagination dan query data (tidak ada perubahan di sini)
$perPage = 3; // Saya kembalikan ke 7, sesuaikan jika perlu
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
if ($page < 1) $page = 1;

$countQuery = "SELECT COUNT(*) AS total FROM Barang WHERE stokBarang > 0";
$countResult = sqlsrv_query($conn, $countQuery);
$countRow = sqlsrv_fetch_array($countResult, SQLSRV_FETCH_ASSOC);
$totalData = $countRow['total'];
$totalPages = ceil($totalData / $perPage);

$offset = ($page - 1) * $perPage;
$query = "SELECT idBarang, namaBarang, stokBarang, lokasiBarang FROM Barang WHERE stokBarang > 0 ORDER BY idBarang OFFSET ? ROWS FETCH NEXT ? ROWS ONLY";
$params = [$offset, $perPage];
$result = sqlsrv_query($conn, $query, $params);


include __DIR__ . '/../../../templates/header.php';
include __DIR__ . '/../../../templates/sidebar.php';
?>

<main class="col bg-white px-3 px-md-4 py-3 position-relative">
    <h3 class="fw-semibold mb-3">Peminjaman Barang</h3>

    <div class="mb-4">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item">
                    <a href="<?= BASE_URL ?>/Menu/Menu Peminjam/dashboardPeminjam.php">Sistem Pengelolaan Lab</a>
                </li>
                <li class="breadcrumb-item active" aria-current="page">Cek Barang</li>
            </ol>
        </nav>
    </div>

    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <h5 class="card-title mb-3 fw-semibold">Cek Ketersediaan Barang</h5>

            <form method="POST" id="formCekKetersediaanBarang" action="">
                <div class="mb-2">
                    <label class="form-label">
                        Pilih Tanggal Peminjaman
                        <span id="error-message" style="color: red; display: none; margin-left: 10px;" class="fw-normal"></span>
                    </label>

                    <div class="d-flex gap-2"
                        data-day="<?= htmlspecialchars($selectedDay) ?>"
                        data-month="<?= htmlspecialchars($selectedMonth) ?>"
                        data-year="<?= htmlspecialchars($selectedYear) ?>">
                        <select id="tglHari" class="form-select" style="width: 80px;"></select>
                        <select id="tglBulan" class="form-select" style="width: 100px;"></select>
                        <select id="tglTahun" class="form-select" style="width: 100px;"></select>
                        <input type="hidden" id="tglPeminjamanBrg" name="tglPeminjamanBrg">

                        <button type="submit" class="btn btn-primary align-items-end ms-auto" name="submit">Cek Ketersediaan</button>
                    </div>

                </div>
            </form>
        </div>
    </div>

    <div id="areaBarangTersedia" style="<?= $showTable ? 'display:block;' : 'display:none;' ?>">
        <h5 class="card-title mb-3 fw-senibold">Daftar Barang yang Tersedia</h5>

        <div class="table-responsive">
            <table class="table table-hover align-middle table-bordered">
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
                    if ($result && sqlsrv_has_rows($result)) {
                        $hasData = true;
                        while ($row = sqlsrv_fetch_array($result, SQLSRV_FETCH_ASSOC)) {
                    ?>
                            <tr class="text-center">
                                <td><?= htmlspecialchars($row['idBarang'] ?? '') ?></td>
                                <td><?= htmlspecialchars($row['namaBarang'] ?? '') ?></td>
                                <td><?= htmlspecialchars($row['stokBarang'] ?? '') ?></td>
                                <td><?= htmlspecialchars($row['lokasiBarang'] ?? '') ?></td>
                                <td class="td-aksi text-center align-middle">
                                    <a href="<?= BASE_URL ?>/CRUD/Peminjaman/tambahPeminjamanBrg.php?idBarang=<?= urlencode($row['idBarang']) ?>" class="d-inline-block">
                                        <img src="<?= BASE_URL ?>/icon/tandaplus.svg" class="plus-tambah w-25" alt="Tambah Peminjaman Barang" style="display: inline-block; vertical-align: middle;">
                                    </a>
                                </td>
                            </tr>
                    <?php
                        }
                    }
                    if (!$hasData) {
                        echo '<tr><td colspan="5" class="text-center">Tidak ada barang yang tersedia</td></tr>';
                    }
                    ?>
                </tbody>
            </table>
        </div>

        <div class="mt-3">
            <?php
            if ($totalPages > 1) {
                generatePagination($page, $totalPages);
                $showTable = true;
            }
            ?>
        </div>
    </div>
</main>

<?php
include __DIR__ . '/../../../templates/footer.php';
?>