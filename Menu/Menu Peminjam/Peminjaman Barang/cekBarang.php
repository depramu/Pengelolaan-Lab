<?php
require_once __DIR__ . '/../../../function/init.php'; // Pastikan path ke init.php sudah benar

authorize_role(['Peminjam']);

$showTable = false; // Defaultnya, tabel tidak ditampilkan

if (isset($_POST['submit'])) {
    // Validasi di sisi server (PENTING!)
    $tglPeminjaman = $_POST['tglPeminjamanBrg'] ?? '';
    if (empty($tglPeminjaman)) {
        // Atur pesan error jika perlu, atau abaikan saja
    } else {
        // Jika tanggal valid, simpan ke session dan set flag untuk tampilkan tabel
        $_SESSION['tglPeminjamanBrg'] = $tglPeminjaman;
        $showTable = true;
    }
}

// Logika pagination dan query data tetap sama
$perPage = 1;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
if ($page < 1) $page = 1;

// Hitung total data
$countQuery = "SELECT COUNT(*) AS total FROM Barang WHERE stokBarang > 0";
$countResult = sqlsrv_query($conn, $countQuery);
$countRow = sqlsrv_fetch_array($countResult, SQLSRV_FETCH_ASSOC);
$totalData = $countRow['total'];
$totalPages = ceil($totalData / $perPage);

// Ambil data sesuai halaman
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
                <li class="breadcrumb-item"><a href="<?= BASE_URL ?>/Menu Peminjam/dashboardPeminjam.php">Sistem Pengelolaan Lab</a></li>
                <li class="breadcrumb-item active" aria-current="page">Cek Barang</li>
            </ol>
        </nav>
    </div>

    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <h5 class="card-title mb-3 fw-bold">Cek Ketersediaan Barang</h5>
            <form method="POST" id="formCekKetersediaanBarang" action="">
                <div class="mb-2">
                    <label class="form-label fw-semibold">
                        Pilih Tanggal Peminjaman
                        <span id="error-message" class="text-danger small mt-1 fw-normal" style="font-size: 0.95em; display:none;">*Harus Diisi & tidak boleh tanggal lampau</span>
                    </label>
                    <div class="d-flex gap-2">
                        <select id="tglHari" class="form-select" style="width: 80px;"></select>
                        <select id="tglBulan" class="form-select" style="width: 100px;"></select>
                        <select id="tglTahun" class="form-select" style="width: 100px;"></select>
                    </div>
                    <input type="hidden" id="tglPeminjamanBrg" name="tglPeminjamanBrg">
                </div>
                <div class="d-flex justify-content-end mt-3">
                    <button type="submit" class="btn btn-primary" name="submit">Cek Ketersediaan</button>
                </div>
            </form>
        </div>
    </div>
    
    <div id="areaBarangTersedia" style="<?= $showTable ? 'display:block;' : 'display:none;' ?>">
        <h5 class="card-title mb-3 fw-bold">Daftar Barang yang Tersedia</h5>
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
            }
            ?>
        </div>
    </div>
</main>

<script>
    // Fungsi untuk mengisi tanggal, bulan, tahun
    function populateDateSelectors() {
        const hariSelect = document.getElementById('tglHari');
        const bulanSelect = document.getElementById('tglBulan');
        const tahunSelect = document.getElementById('tglTahun');
        const now = new Date();

        // Isi tahun
        for (let y = now.getFullYear(); y <= now.getFullYear() + 5; y++) {
            tahunSelect.innerHTML += `<option value="${y}">${y}</option>`;
        }
        // Isi bulan
        const bulanNama = ["Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember"];
        for (let m = 0; m < 12; m++) {
            bulanSelect.innerHTML += `<option value="${m + 1}">${bulanNama[m]}</option>`;
        }
        
        const updateDays = () => {
            const bulan = parseInt(bulanSelect.value);
            const tahun = parseInt(tahunSelect.value);
            const daysInMonth = new Date(tahun, bulan, 0).getDate();
            
            hariSelect.innerHTML = '';
            for (let i = 1; i <= daysInMonth; i++) {
                hariSelect.innerHTML += `<option value="${i}">${i}</option>`;
            }
        };

        // Set listener dan nilai awal
        bulanSelect.addEventListener('change', updateDays);
        tahunSelect.addEventListener('change', updateDays);
        
        // Set tanggal hari ini sebagai default
        bulanSelect.value = now.getMonth() + 1;
        tahunSelect.value = now.getFullYear();
        updateDays(); // Panggil sekali untuk mengisi hari
        hariSelect.value = now.getDate();
    }

    document.addEventListener('DOMContentLoaded', function() {
        populateDateSelectors();

        document.getElementById('formCekKetersediaanBarang').addEventListener('submit', function(e) {
            const hari = document.getElementById('tglHari').value;
            const bulan = document.getElementById('tglBulan').value;
            const tahun = document.getElementById('tglTahun').value;
            const errorSpan = document.getElementById('error-message');

            let isValid = true;

            // Validasi tanggal
            if (!hari || !bulan || !tahun) {
                isValid = false;
            } else {
                const selectedDate = new Date(`${tahun}-${bulan}-${hari}`);
                const today = new Date();
                today.setHours(0, 0, 0, 0); // Reset waktu ke awal hari
                if (selectedDate < today) {
                    isValid = false;
                }
            }

            if (isValid) {
                // Jika valid, gabungkan nilainya ke input hidden
                document.getElementById('tglPeminjamanBrg').value = `${hari.padStart(2, '0')}-${bulan.padStart(2, '0')}-${tahun}`;
                errorSpan.style.display = 'none';
            } else {
                // Jika tidak valid, batalkan pengiriman form dan tampilkan error
                e.preventDefault();
                errorSpan.style.display = 'inline';
            }
        });
    });
</script>

<?php
include __DIR__ . '/../../../templates/footer.php';
?>