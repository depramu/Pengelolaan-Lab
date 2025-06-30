<?php
require_once __DIR__ . '/../../config.php';
require_once __DIR__ . '/../../koneksi.php';
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if (isset($_POST['submit'])) {
    // Pastikan tanggal tidak kosong sebelum redirect
    $_SESSION['tglPeminjamanBrg'] = $_POST['tglPeminjamanBrg'] ?? '';
    header('Location: lihatBarang.php');
    exit();
}

include __DIR__ . '/../../templates/header.php';
include __DIR__ . '/../../templates/sidebar.php';
$tglPeminjamanBrg = $_SESSION['tglPeminjamanBrg'] ?? '';
$query = "SELECT idBarang, namaBarang, lokasiBarang, stokBarang FROM Barang WHERE stokBarang > 0";
$stmt = sqlsrv_query($conn, $query);
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

    <div class="container mt-4">
        <div class="row justify-content-center">
            <div class="col-md-8 col-lg-12" style="margin-right: 20px;">
                <div class="card border border-dark">
                    <div class="card-header bg-white border-bottom border-dark">
<<<<<<< HEAD
                        <span class="fw-bold">Cek Barang</span>
=======
<<<<<<< HEAD
                        <span class="fw-semibold">Cek Barang</span>
=======
                        <span class="fw-bold">Cek Barang</span>
>>>>>>> 214511133ce34a90da4a05e4292cdc131042ab7d
>>>>>>> 8bf5b781af0e54bd3ece0577b973d3723f6a6238
                    </div>
                    <div class="card-body">
                        <form method="POST" action="">
                            <div class="mb-2">
<<<<<<< HEAD
                                <label class="form-label fw-semibold">
                                    Pilih Tanggal Peminjaman <span id="error-message" class="text-danger small mt-1 fw-normal" style="font-size: 0.95em; display:none;">*Harus Diisi</span>
=======
<<<<<<< HEAD
                                <label class="form-label">
=======
                                <label class="form-label fw-semibold">
>>>>>>> 214511133ce34a90da4a05e4292cdc131042ab7d
                                    Pilih Tanggal Peminjaman
                                    <span id="error-message" style="color: red; display: none; margin-left: 10px;">*Harus Diisi</span>
>>>>>>> 8bf5b781af0e54bd3ece0577b973d3723f6a6238
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
    </div>
</main>

<script>
    function isLeapYear(year) {
        return (year % 4 === 0 && year % 100 !== 0) || (year % 400 === 0);
    }

    function updateDays() {
        let bulan = parseInt(document.getElementById('tglBulan').value);
        let tahun = parseInt(document.getElementById('tglTahun').value);
        let hariSelect = document.getElementById('tglHari');
        let prevHari = hariSelect.value;
        let days = 31;
        if ([4, 6, 9, 11].includes(bulan)) days = 30;
        else if (bulan === 2) days = isLeapYear(tahun) ? 29 : 28;

        hariSelect.innerHTML = '';
        for (let i = 1; i <= days; i++) {
            hariSelect.innerHTML += `<option value="${i.toString().padStart(2, '0')}">${i}</option>`;
        }
        // Kembalikan ke hari sebelumnya jika masih valid, jika tidak pilih hari terakhir
        if (prevHari && parseInt(prevHari) <= days) {
            hariSelect.value = prevHari.padStart(2, '0');
        } else {
            hariSelect.value = days.toString().padStart(2, '0');
        }
    }

    function fillSelects() {
        let tahunSelect = document.getElementById('tglTahun');
        let bulanSelect = document.getElementById('tglBulan');
        let hariSelect = document.getElementById('tglHari');
        let now = new Date();
        for (let y = now.getFullYear(); y <= now.getFullYear() + 5; y++) {
            tahunSelect.innerHTML += `<option value="${y}">${y}</option>`;
        }
        for (let m = 1; m <= 12; m++) {
            bulanSelect.innerHTML += `<option value="${m}">${m.toString().padStart(2, '0')}</option>`;
        }
        bulanSelect.value = now.getMonth() + 1;
        tahunSelect.value = now.getFullYear();
        updateDays();
        // Set hari ke hari ini
        hariSelect.value = now.getDate().toString().padStart(2, '0');
    }

    document.addEventListener('DOMContentLoaded', function() {
        fillSelects();
        document.getElementById('tglBulan').addEventListener('change', updateDays);
        document.getElementById('tglTahun').addEventListener('change', updateDays);

        document.querySelector('form').addEventListener('submit', function(event) {
            // validasi tanggal
            let hari = document.getElementById('tglHari').value;
            let bulan = document.getElementById('tglBulan').value;
            let tahun = document.getElementById('tglTahun').value;
            let errorTanggal = document.getElementById('error-message');
            let isValid = hari && bulan && tahun;
            let pesan = '';
            // Validasi tanggal tidak boleh di masa lalu
            if (isValid) {
                let inputDate = new Date(`${tahun}-${bulan.padStart(2, '0')}-${hari.padStart(2, '0')}`);
                let today = new Date();
                today.setHours(0, 0, 0, 0);
                if (inputDate < today) {
                    isValid = false;
                    pesan = 'Input tanggal sudah lewat';
                }
            }
            if (!isValid) {
                errorTanggal.textContent = pesan ? `*${pesan}` : '*Harus Diisi';
                errorTanggal.style.display = 'inline';
                event.preventDefault();
            } else {
                errorTanggal.style.display = 'none';
                document.getElementById('tglPeminjamanBrg').value = `${hari.padStart(2, '0')}-${bulan.padStart(2, '0')}-${tahun}`;
            }
        });
    });
</script>

<?php
include __DIR__ . '/../../templates/footer.php';
?>