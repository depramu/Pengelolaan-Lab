<?php
require_once __DIR__ . '/../../function/init.php';
authorize_role(['Peminjam']);

$showTable = false;

if (isset($_POST['submit'])) {
    $tglInput = $_POST['tglPeminjamanRuangan'] ?? '';
    $mulai = $_POST['waktuMulai'] ?? '';
    $selesai = $_POST['waktuSelesai'] ?? '';

    // FIX: Pakai format Y-m-d agar cocok dengan value dari input
    $tglObj = DateTime::createFromFormat('Y-m-d', $tglInput);
    $isValid = true;
    $error = '';

    if (!$tglObj) {
        $isValid = false;
        $error = 'Format tanggal tidak valid.';
    }

    if (empty($mulai) || empty($selesai)) {
        $isValid = false;
        $error = 'Waktu mulai dan selesai harus diisi.';
    } elseif ($mulai >= $selesai) {
        $isValid = false;
        $error = 'Waktu mulai harus lebih awal dari waktu selesai.';
    }

    if ($isValid) {
        // Gabungkan tanggal dan waktu mulai untuk validasi waktu sekarang
        $inputMulaiDateTime = DateTime::createFromFormat('Y-m-d H:i', $tglInput . ' ' . $mulai);
        $now = new DateTime();

        if ($inputMulaiDateTime && $inputMulaiDateTime < $now) {
            $isValid = false;
            $error = 'Waktu mulai tidak boleh kurang dari waktu sekarang.';
        }
    }

    if ($isValid) {
        // Validasi jam minimal dan maksimal
        if ($mulai < "07:00" || $mulai > "21:00") {
            $isValid = false;
            $error = 'Waktu mulai minimal 07:00 dan maksimal 21:00.';
        }
        if ($selesai < "07:00" || $selesai > "21:00") {
            $isValid = false;
            $error = 'Waktu selesai minimal 07:00 dan maksimal 21:00.';
        }
    }

    if ($isValid) {
        $_SESSION['tglPeminjamanRuangan'] = $tglInput; // sudah dalam format Y-m-d
        $_SESSION['waktuMulai'] = $mulai;
        $_SESSION['waktuSelesai'] = $selesai;
    } else {
        unset($_SESSION['tglPeminjamanRuangan'], $_SESSION['waktuMulai'], $_SESSION['waktuSelesai']);
        $showTable = false;
    }
}

if (!empty($_SESSION['tglPeminjamanRuangan']) && !empty($_SESSION['waktuMulai']) && !empty($_SESSION['waktuSelesai'])) {
    $showTable = true;
}

$perPage = 2;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
if ($page < 1) $page = 1;

$countQuery = "SELECT COUNT(*) AS total FROM Ruangan WHERE ketersediaan = 'Tersedia'";
$countResult = sqlsrv_query($conn, $countQuery);
$countRow = sqlsrv_fetch_array($countResult, SQLSRV_FETCH_ASSOC);
$totalData = $countRow['total'] ?? 0;
$totalPages = ceil($totalData / $perPage);

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

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

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

           <form method="POST" id="formCekKetersediaanRuangan" action="">
    <div class="row mb-3 align-items-end">
        <div class="col-md-4">
            <label class="form-label" for="tglPeminjamanRuangan">
                Pilih Tanggal Peminjaman
                <span id="error-message" style="color: red; display: none; margin-left: 10px;" class="fw-normal">*Harus diisi</span>
            </label>
            <input type="text" id="tglPeminjamanRuangan" name="tglPeminjamanRuangan"
                class="form-control"
                placeholder="dd-month-yyyy"
                value="<?= $_SESSION['tglPeminjamanRuangan'] ?? '' ?>">
            <!-- Tambahkan ruang kosong yang sama untuk menjaga keseimbangan -->
            <div style="min-height: 20px; margin-top: 5px;"></div>
        </div>
        <div class="col-md-4">
            <label class="form-label" for="waktuMulai">
                Waktu Mulai
                <span id="error-waktu-mulai" style="color: red; display: none; margin-left: 10px;" class="fw-normal">*Harus diisi</span>
            </label>
            <input type="text" id="waktuMulai" name="waktuMulai"
                class="form-control"
                placeholder="HH:MM"
                value="<?= $_SESSION['waktuMulai'] ?? '' ?>">
            <!-- Berikan tinggi tetap untuk area error -->
            <div style="min-height: 20px; margin-top: 5px;">
                <span id="error-waktu" style="color: red; display: none;" class="fw-normal"></span>
            </div>
        </div>
        <div class="col-md-4">
            <label class="form-label" for="waktuSelesai">
                Waktu Selesai
                <span id="error-waktu-selesai" style="color: red; display: none; margin-left: 10px;" class="fw-normal">*Harus diisi</span>
            </label>
            <input type="text" id="waktuSelesai" name="waktuSelesai"
                class="form-control"
                placeholder="HH:MM"
                value="<?= $_SESSION['waktuSelesai'] ?? '' ?>">
            <!-- Berikan ruang kosong yang sama untuk menjaga keseimbangan -->
            <div style="min-height: 20px; margin-top: 5px;"></div>
        </div>
    </div>
    
    <div class="row mt-4">
        <div class="col-md-12 d-flex gap-2">
            <button type="submit" class="btn btn-primary" name="submit">Cek Ketersediaan</button>
            <a href="#" id="lihatJadwalBtn" class="btn btn-primary">Lihat Jadwal Ruangan</a>
            <span id="error-jadwal" class="text-danger fw-normal ms-2"></span>
        </div>
    </div>
</form>
            
        </div>
        
    </div>

    <div id="areaRuanganTersedia" style="<?= $showTable ? 'display:block;' : 'display:none;' ?>">
        <h5 class="card-title mb-3 fw-semibold">Daftar Ruangan yang Tersedia</h5>
        <div class="table-responsive" style="max-height: 70vh; overflow-y: auto;">
            <table class="table table-hover align-middle table-bordered">
                <thead class="table-light">
                    <tr class="text-center">
                        <th>No</th>
                        <th>Nama Ruangan</th>
                        <th>Kondisi</th>
                        <th>Ketersediaan</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $hasData = false;
                    $nomorUrut = ($page - 1) * $perPage + 1;
                    if ($result && sqlsrv_has_rows($result)) {
                        $hasData = true;
                        while ($row = sqlsrv_fetch_array($result, SQLSRV_FETCH_ASSOC)) {
                    ?>
                            <tr class="text-center">
                                <td><?= $nomorUrut++ ?></td>
                                <td><?= htmlspecialchars($row['namaRuangan'] ?? '') ?></td>
                                <td class="text-center"><?= htmlspecialchars($row['kondisiRuangan'] ?? '') ?></td>
                                <td class="text-center"><?= htmlspecialchars($row['ketersediaan'] ?? '') ?></td>
                                <td class="td-aksi text-center align-middle">
                                    <a href="<?= BASE_URL ?>/CRUD/Peminjaman/tambahPeminjamanRuangan.php?idRuangan=<?= urlencode($row['idRuangan']) ?>&nomorUrut=<?= urlencode($nomorUrut - 1) ?>" class="d-inline-block">
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
    </div>

    <!-- Pagination selalu ditampilkan ketika tabel ditampilkan -->
    <div class="mt-3" style="<?= $showTable ? 'display:block;' : 'display:none;' ?>">
        <?php
        // Manual pagination - selalu tampil
        $displayTotalPages = max($totalPages, 1);
        ?>
        <nav aria-label="Page navigation">
            <ul class="pagination justify-content-end">
                <!-- Previous button -->
                <li class="page-item <?= $page <= 1 ? 'disabled' : '' ?>">
                    <a class="page-link" href="<?= $page > 1 ? '?page=' . ($page - 1) : '#' ?>" aria-label="Previous">
                        <span aria-hidden="true">&laquo;</span>
                    </a>

                </li>

                <!-- Page numbers -->
                <?php for ($i = 1; $i <= $displayTotalPages; $i++): ?>
                    <li class="page-item <?= $i == $page ? 'active' : '' ?>">
                        <a class="page-link" href="?page=<?= $i ?>"><?= $i ?></a>
                    </li>
                <?php endfor; ?>

                <!-- Next button -->
                <li class="page-item <?= $page >= $displayTotalPages ? 'disabled' : '' ?>">
                    <a class="page-link" href="<?= $page < $displayTotalPages ? '?page=' . ($page + 1) : '#' ?>" aria-label="Next">
                        <span aria-hidden="true">&raquo;</span>
                    </a>
                </li>
            </ul>
        </nav>
    </div>
</main>

<script>
    // Inisialisasi flatpickr untuk input tanggal dan waktu
    flatpickr("#tglPeminjamanRuangan", {
        dateFormat: "Y-m-d", //format yang dikirim ke server
        altInput: true, //tampilan alternatif ke user
        altFormat: "d F Y", //yang ditampilkan ke user
        minDate: "today"
    });

    flatpickr("#waktuMulai", {
        enableTime: true,
        noCalendar: true,
        dateFormat: "H:i",
        time_24hr: true
    });

    flatpickr("#waktuSelesai", {
        enableTime: true,
        noCalendar: true,
        dateFormat: "H:i",
        time_24hr: true
    });

    //validasi form saat submit
    document.getElementById("formCekKetersediaanRuangan").addEventListener("submit", function(e) {
        const tanggal = document.getElementById("tglPeminjamanRuangan").value.trim();
        const waktuMulai = document.getElementById("waktuMulai").value.trim();
        const waktuSelesai = document.getElementById("waktuSelesai").value.trim();

        let isValid = true;

        document.getElementById("error-message").style.display = "none";
        document.getElementById("error-waktu").style.display = "none";
        document.getElementById("error-waktu-mulai").style.display = "none";
        document.getElementById("error-waktu-selesai").style.display = "none";

        if (!tanggal) {
            document.getElementById("error-message").textContent = "*Harus diisi";
            document.getElementById("error-message").style.display = "inline";
            isValid = false;
        }
        if (!waktuMulai) {
            document.getElementById("error-waktu-mulai").style.display = "inline";
            isValid = false;
        }
        if (!waktuSelesai) {
            document.getElementById("error-waktu-selesai").style.display = "inline";
            isValid = false;
        }

        if (waktuMulai && waktuSelesai && waktuMulai >= waktuSelesai) {
            document.getElementById("error-waktu").textContent = "*Waktu mulai harus lebih awal dari selesai";
            document.getElementById("error-waktu").style.display = "inline";
            isValid = false;
        }

        // Tambahkan validasi waktu mulai < sekarang jika tanggal == hari ini
        if (tanggal && waktuMulai) {
            const now = new Date();
            const inputDateTime = new Date(tanggal + 'T' + waktuMulai);
            if (inputDateTime < now) {
                document.getElementById("error-waktu").textContent = "*Waktu mulai kurang dari sekarang";
                document.getElementById("error-waktu").style.display = "inline";
                isValid = false;
            }
        }

        if (waktuMulai && (waktuMulai < "07:00" || waktuMulai > "21:00")) {
            document.getElementById("error-waktu-mulai").textContent = "*Waktu mulai minimal 07:00";
            document.getElementById("error-waktu-mulai").style.display = "inline";
            isValid = false;
        }
        if (waktuSelesai && (waktuSelesai < "07:00" || waktuSelesai > "21:00")) {
            document.getElementById("error-waktu-selesai").textContent = "*Waktu selesai maksimal 21:00";
            document.getElementById("error-waktu-selesai").style.display = "inline";
            isValid = false;
        }

        if (!isValid) {
            e.preventDefault();
        }
    });

    document.getElementById("lihatJadwalBtn").addEventListener("click", function(e) {
        const tanggal = document.getElementById("tglPeminjamanRuangan").value.trim();
        const waktuMulai = document.getElementById("waktuMulai").value.trim();
        const waktuSelesai = document.getElementById("waktuSelesai").value.trim();
        let isValid = true;
        let errorMsg = "";

        if (!tanggal) isValid = false;
        if (!waktuMulai || waktuMulai < "07:00" || waktuMulai > "21:00") isValid = false;
        if (!waktuSelesai || waktuSelesai < "07:00" || waktuSelesai > "21:00") isValid = false;

        if (!isValid) {
            errorMsg = "*waktu mulai (min 07:00), dan waktu selesai (maks 21:00) harus diisi";
            document.getElementById("error-jadwal").textContent = errorMsg;
            e.preventDefault();
            return false;
        } else {
            document.getElementById("error-jadwal").textContent = '';
            window.location.href = "jadwalKetersediaanRuangan.php?tanggal=" + encodeURIComponent(tanggal);
            e.preventDefault();
        }
    });
</script>

<?php include __DIR__ . '/../../templates/footer.php';?>
