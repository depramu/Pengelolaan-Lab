<?php
require_once __DIR__ . '/../../function/init.php'; // Pastikan path ke init.php sudah benar
authorize_role(['Peminjam']);

$showTable = false; // Defaultnya, tabel tidak ditampilkan
$selectedDay = '';
$selectedMonth = '';
$selectedYear = '';
$tglPeminjamanValue = ''; // Untuk mengisi value input setelah submit
$tglPeminjamanDisplay = ''; // Untuk input text, format d M Y

// 1. Handle form submission (jika ada)
if (isset($_POST['submit'])) {
    $tglPeminjaman = $_POST['tglPeminjamanBrg'] ?? '';
    if (!empty($tglPeminjaman)) {
        $_SESSION['tglPeminjamanBrg'] = $tglPeminjaman;
        $tglPeminjamanValue = $tglPeminjaman;

        // Format untuk input text: d M Y
        $parts = explode('-', $tglPeminjaman);
        if (count($parts) === 3) {
            $day = (int)$parts[0];
            $month = (int)$parts[1];
            $year = (int)$parts[2];
            $dateObj = DateTime::createFromFormat('!d-m-Y', sprintf('%02d-%02d-%04d', $day, $month, $year));
            if ($dateObj) {
                $tglPeminjamanDisplay = $dateObj->format('j M Y');
            }
        }
    } else {
        unset($_SESSION['tglPeminjamanBrg']);
        $tglPeminjamanValue = '';
        $tglPeminjamanDisplay = '';
    }
} elseif (!empty($_SESSION['tglPeminjamanBrg'])) {
    $tglPeminjamanValue = $_SESSION['tglPeminjamanBrg'];
    // Format untuk input text: d M Y
    $parts = explode('-', $tglPeminjamanValue);
    if (count($parts) === 3) {
        $day = (int)$parts[0];
        $month = (int)$parts[1];
        $year = (int)$parts[2];
        $dateObj = DateTime::createFromFormat('!d-m-Y', sprintf('%02d-%02d-%04d', $day, $month, $year));
        if ($dateObj) {
            $tglPeminjamanDisplay = $dateObj->format('j M Y');
        }
    }
}

if (!empty($_SESSION['tglPeminjamanBrg']) && substr_count($_SESSION['tglPeminjamanBrg'], '-') === 2) {
    $showTable = true;
    list($day, $month, $year) = explode('-', $_SESSION['tglPeminjamanBrg']);
    $selectedDay = (int)$day;
    $selectedMonth = (int)$month;
    $selectedYear = (int)$year;
} else {
    unset($_SESSION['tglPeminjamanBrg']); // Buang session jelek biar ngga looping error
}


// 3. Logika pagination dan query data (tidak ada perubahan di sini)
$perPage = 3;
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


include __DIR__ . '/../../templates/header.php';
include __DIR__ . '/../../templates/sidebar.php';
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

                    <div class="d-flex gap-2 align-items-center">
                        <input 
                            type="text" 
                            id="tglPeminjamanFlat" 
                            class="form-control" 
                            placeholder="dd-month-yyyy" 
                            style="max-width: 200px;"
                            value="<?= htmlspecialchars($tglPeminjamanDisplay) ?>"
                        >
                        <input 
                            type="hidden" 
                            id="tglPeminjamanBrg" 
                            name="tglPeminjamanBrg" 
                            value="<?= htmlspecialchars($tglPeminjamanValue) ?>"
                        >
                        <button type="submit" class="btn btn-primary ms-2" name="submit">Cek Ketersediaan</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div id="areaBarangTersedia" style="<?= $showTable ? 'display:block;' : 'display:none;' ?>">
        <h5 class="card-title mb-3 fw-semibold">Daftar Barang yang Tersedia</h5>

        <div class="table-responsive">
            <table class="table table-hover align-middle table-bordered">
                <thead class="table-light">
                    <tr class="text-center">
                        <th>No</th>
                        <th>Nama Barang</th>
                        <th>Stok Barang</th>
                        <th>Lokasi Barang</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $no = $offset + 1;
                    $hasData = false;
                    if ($result && sqlsrv_has_rows($result)) {
                        $hasData = true;
                        while ($row = sqlsrv_fetch_array($result, SQLSRV_FETCH_ASSOC)) {
                    ?>
                            <tr class="text-center">
                                <td><?= $no ?></td>
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
                            $no++;
                        }
                    }
                    if (!$hasData) {
                        echo '<tr><td colspan="5" class="text-center">Tidak ada barang yang tersedia</td></tr>';
                    }
                    ?>
                </tbody>
            </table>
        </div>

        <div>
            <?php
            generatePagination($page, $totalPages);
            $showTable = true;
            ?>
        </div>
    </div>
</main>

<?php
include __DIR__ . '/../../templates/footer.php';
?>