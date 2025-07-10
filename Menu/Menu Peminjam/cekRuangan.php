<?php
require_once __DIR__ . '/../../function/init.php';

authorize_role(['Peminjam']);

$showTable = false;
$selectedDay = '';
$selectedMonth = '';
$selectedYear = '';
$selectedJamMulai = '';
$selectedMenitMulai = '';
$selectedJamSelesai = '';
$selectedMenitSelesai = '';

// 1. Handle form submission (jika ada)
if (isset($_POST['submit'])) {
    $tglPeminjaman = $_POST['tglPeminjamanRuangan'] ?? '';
    $jamMulai = $_POST['jam_dari'] ?? '';
    $menitMulai = $_POST['menit_dari'] ?? '';
    $jamSelesai = $_POST['jam_sampai'] ?? '';
    $menitSelesai = $_POST['menit_sampai'] ?? '';

    if (!empty($tglPeminjaman) && $jamMulai !== '' && $menitMulai !== '' && $jamSelesai !== '' && $menitSelesai !== '') {
        $_SESSION['tglPeminjamanRuangan'] = $tglPeminjaman;
        $_SESSION['waktuMulai'] = $jamMulai . ':' . $menitMulai;
        $_SESSION['waktuSelesai'] = $jamSelesai . ':' . $menitSelesai;
    } else {
        unset($_SESSION['tglPeminjamanRuangan'], $_SESSION['waktuMulai'], $_SESSION['waktuSelesai']);
    }
}

// 2. Cek session untuk menentukan state halaman
if (!empty($_SESSION['tglPeminjamanRuangan'])) {
    $showTable = true;
    // Ambil kembali tanggal dari session untuk mengisi ulang form date picker
    list($day, $month, $year) = explode('-', $_SESSION['tglPeminjamanRuangan']);
    $selectedDay = (int)$day;
    $selectedMonth = (int)$month;
    $selectedYear = (int)$year;
}
if (!empty($_SESSION['waktuMulai'])) {
    list($selectedJamMulai, $selectedMenitMulai) = explode(':', $_SESSION['waktuMulai']);
}
if (!empty($_SESSION['waktuSelesai'])) {
    list($selectedJamSelesai, $selectedMenitSelesai) = explode(':', $_SESSION['waktuSelesai']);
}

$perPage = 2;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
if ($page < 1) $page = 1;



// Hitung total data ruangan yang tersedia
$countQuery = "SELECT COUNT(*) AS total FROM Ruangan WHERE ketersediaan = 'Tersedia'";
$countResult = sqlsrv_query($conn, $countQuery);
$countRow = sqlsrv_fetch_array($countResult, SQLSRV_FETCH_ASSOC);
$totalData = $countRow['total'] ?? 0;
$totalPages = ceil($totalData / $perPage);

generatePagination($page, $totalPages);

// Ambil data ruangan sesuai halaman
$offset = ($page - 1) * $perPage;
$query = "SELECT idRuangan, namaRuangan, kondisiRuangan, ketersediaan 
          FROM Ruangan 
          WHERE ketersediaan = 'Tersedia'
          ORDER BY idRuangan
          OFFSET ? ROWS FETCH NEXT ? ROWS ONLY";
$params = [$offset, $perPage];
$result = sqlsrv_query($conn, $query, $params);


include __DIR__ . '/../../templates/header.php';
include __DIR__ . '/../../templates/sidebar.php';
?>

<main class="col bg-white px-3 px-md-4 py-3 position-relative">
    <h3 class="fw-semibold mb-3">Peminjaman Ruangan</h3>
    <div class="mb-4">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?= BASE_URL ?>/Menu/Menu Peminjam/dashboardPeminjam.php">Sistem Pengelolaan Lab</a></li>
                <li class="breadcrumb-item active" aria-current="page">Cek Ruangan</li>
            </ol>
        </nav>
    </div>

    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <h5 class="card-title mb-3 fw-semibold">Cek Ketersediaan Ruangan</h5>

            <?php
            // Ambil tanggal dari $_POST, kalau kosong pakai $_SESSION
            if (!empty($_POST['tglHari']) && !empty($_POST['tglBulan']) && !empty($_POST['tglTahun'])) {
                $tglHari = $_POST['tglHari'];
                $tglBulan = $_POST['tglBulan'];
                $tglTahun = $_POST['tglTahun'];
            } elseif (!empty($_SESSION['tglPeminjamanRuangan'])) {
                list($tglHari, $tglBulan, $tglTahun) = explode('-', $_SESSION['tglPeminjamanRuangan']);
            } else {
                $tglHari = $tglBulan = $tglTahun = '';
            }

            $jamDari = $_POST['jam_dari'] ?? ($selectedJamMulai ?? '');
            $menitDari = $_POST['menit_dari'] ?? ($selectedMenitMulai ?? '');
            $jamSampai = $_POST['jam_sampai'] ?? ($selectedJamSelesai ?? '');
            $menitSampai = $_POST['menit_sampai'] ?? ($selectedMenitSelesai ?? '');
            ?>

            <form method="POST" id="formCekKetersediaanRuangan" action="">
                <div class="mb-2">
                    <label class="form-label">
                        Pilih Tanggal Peminjaman
                        <span id="error-message" style="color: red; display: none; margin-left: 10px;" class="fw-normal"></span>
                    </label>
                    <div class="d-flex gap-2"
                        data-day="<?= htmlspecialchars($selectedDay) ?>"
                        data-month="<?= htmlspecialchars($selectedMonth) ?>"
                        data-year="<?= htmlspecialchars($selectedYear) ?>">
                        <select id="tglHari" name="tglHari" class="form-select" style="width: 80px;"></select>
                        <select id="tglBulan" name="tglBulan" class="form-select" style="width: 100px;"></select>
                        <select id="tglTahun" name="tglTahun" class="form-select" style="width: 100px;"></select>
                        <input type="hidden" id="tglPeminjamanRuangan" name="tglPeminjamanRuangan">
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label" for="jam_dari">
                            Waktu Mulai
                            <span id="error-waktu-mulai" style="color: red; display: none; margin-left: 10px;" class="fw-normal">*Harus diisi</span>
                        </label>
                        <div class="d-flex gap-2"
                            data-jam="<?= htmlspecialchars($selectedJamMulai) ?>"
                            data-menit="<?= htmlspecialchars($selectedMenitMulai) ?>">
                            <select id="jam_dari" name="jam_dari" class="form-select" style="width: 100px;"></select>
                            <select id="menit_dari" name="menit_dari" class="form-select" style="width: 100px;"></select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label" for="jam_sampai">
                            Waktu Selesai
                            <span id="error-waktu-selesai" style="color: red; display: none; margin-left: 10px;" class="fw-normal">*Harus diisi</span>
                        </label>
                        <div class="d-flex gap-2"
                            data-jam="<?= htmlspecialchars($selectedJamSelesai) ?>"
                            data-menit="<?= htmlspecialchars($selectedMenitSelesai) ?>">
                            <select id="jam_sampai" name="jam_sampai" class="form-select" style="width: 100px;"></select>
                            <select id="menit_sampai" name="menit_sampai" class="form-select" style="width: 100px;"></select>
                            <button type="submit" class="btn btn-primary align-items-end ms-auto" name="submit">Cek Ketersediaan</button>
                        </div>
                    </div>
                </div>
                <div>
                    <span id="error-waktu" style="color: red; display: none;" class="fw-normal"></span>
                </div>
            </form>
        </div>
    </div>

    <div id="areaRuanganTersedia" style="<?= $showTable ? 'display:block;' : 'display:none;' ?>">
        <h5 class="card-title mb-3 fw-semibold">Daftar Ruangan yang Tersedia</h5>

        <div class="table-responsive">
            <table class="table table-hover align-middle table-bordered">
                <thead class="table-light">
                    <tr class="text-center">
                        <th>ID Ruangan</th>
                        <th>Nama Ruangan</th>
                        <th>Kondisi</th>
                        <th>Ketersediaan</th>
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
                                <td><?= htmlspecialchars($row['idRuangan'] ?? '') ?></td>
                                <td><?= htmlspecialchars($row['namaRuangan'] ?? '') ?></td>
                                <td class="text-center"><?= htmlspecialchars($row['kondisiRuangan'] ?? '') ?></td>
                                <td class="text-center"><?= htmlspecialchars($row['ketersediaan'] ?? '') ?></td>
                                <td class="td-aksi text-center align-middle">
                                    <a href="<?= BASE_URL ?>/CRUD/Peminjaman/tambahPeminjamanRuangan.php?idRuangan=<?= urlencode($row['idRuangan']) ?>" class="d-inline-block">
                                        <img src="<?= BASE_URL ?>/icon/tandaplus.svg" class="plus-tambah w-25" alt="Tambah Peminjaman Ruangan" style="display: inline-block; vertical-align: middle;">
                                    </a>
                                </td>
                            </tr>
                    <?php
                        }
                    }
                    if (!$hasData) {
                        echo '<tr><td colspan="5" class="text-center">Tidak ada ruangan yang tersedia</td></tr>';
                    }
                    ?>
                </tbody>
            </table>
        </div>

        <script>
            window.addEventListener('DOMContentLoaded', () => {
                // Isi ulang tanggal
                const tglHari = "<?= $tglHari ?>";
                const tglBulan = "<?= $tglBulan ?>";
                const tglTahun = "<?= $tglTahun ?>";

                const jamDari = "<?= $jamDari ?>";
                const menitDari = "<?= $menitDari ?>";
                const jamSampai = "<?= $jamSampai ?>";
                const menitSampai = "<?= $menitSampai ?>";

                // Inisialisasi select tanggal
                const hariSelect = document.getElementById('tglHari');
                const bulanSelect = document.getElementById('tglBulan');
                const tahunSelect = document.getElementById('tglTahun');

                for (let i = 1; i <= 31; i++) {
                    let option = new Option(i.toString().padStart(2, '0'), i.toString().padStart(2, '0'));
                    if (option.value === tglHari) option.selected = true;
                    hariSelect.add(option);
                }

                const bulanNama = ['01', '02', '03', '04', '05', '06', '07', '08', '09', '10', '11', '12'];
                for (let i = 0; i < 12; i++) {
                    let option = new Option(bulanNama[i], bulanNama[i]);
                    if (option.value === tglBulan) option.selected = true;
                    bulanSelect.add(option);
                }

                const currentYear = new Date().getFullYear();
                for (let i = currentYear; i <= currentYear + 2; i++) {
                    let option = new Option(i, i);
                    if (option.value === tglTahun) option.selected = true;
                    tahunSelect.add(option);
                }

                // Jam & menit
                const jam_dari = document.getElementById('jam_dari');
                const menit_dari = document.getElementById('menit_dari');
                const jam_sampai = document.getElementById('jam_sampai');
                const menit_sampai = document.getElementById('menit_sampai');

                for (let i = 0; i <= 23; i++) {
                    let jam = i.toString().padStart(2, '0');
                    jam_dari.add(new Option(jam, jam, false, jam === jamDari));
                    jam_sampai.add(new Option(jam, jam, false, jam === jamSampai));
                }

                for (let i = 0; i <= 59; i += 5) {
                    let menit = i.toString().padStart(2, '0');
                    menit_dari.add(new Option(menit, menit, false, menit === menitDari));
                    menit_sampai.add(new Option(menit, menit, false, menit === menitSampai));
                }
            });
        </script>

        <div>
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
include __DIR__ . '/../../templates/footer.php';
?>