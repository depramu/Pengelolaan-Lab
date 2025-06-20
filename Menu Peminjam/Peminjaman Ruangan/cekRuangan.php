<?php
require_once __DIR__ . '/../../config.php';
require_once __DIR__ . '/../../koneksi.php';
if (session_status() == PHP_SESSION_NONE) session_start();

if (isset($_POST['submit'])) {
    $_SESSION['tglPeminjamanRuangan'] = $_POST['tglPeminjamanRuangan'] ?? '';
    $_SESSION['waktuMulai'] = $_POST['jam_dari'] . ':' . $_POST['menit_dari'];
    $_SESSION['waktuSelesai'] = $_POST['jam_sampai'] . ':' . $_POST['menit_sampai'];
    header('Location: lihatRuangan.php');
    exit();
}

include __DIR__ . '/../../templates/header.php';
include __DIR__ . '/../../templates/sidebar.php';
?>

<main class="col bg-white px-3 px-md-4 py-3 position-relative">
    <h3 class="fw-semibold mb-3">Peminjaman Ruangan</h3>
    <div class="mb-1">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?= BASE_URL ?>/Menu Peminjam/dashboardPeminjam.php">Sistem Pengelolaan Lab</a></li>
                <li class="breadcrumb-item active" aria-current="page">Cek Ruangan</li>
            </ol>
        </nav>
    </div>

    <div class="container mt-4">
        <div class="row justify-content-center">
            <div class="col-md-8 col-lg-12" style="margin-right: 20px;">
                <div class="card border border-dark">
                    <div class="card-header bg-white border-bottom border-dark">
                        <span class="fw-semibold">Cek Ruangan</span>
                    </div>
                    <div class="card-body">
                        <form method="POST" id="form-peminjaman" action="">
                            <div class="mb-2">
                                <label class="form-label">
                                    Pilih Tanggal Peminjaman
                                    <span id="error-message" style="color: red; display: none; margin-left: 10px;">*Harus Diisi</span>
                                </label>
                                <div class="d-flex gap-2">
                                    <select id="tglHari" class="form-select" style="width: 80px;"></select>
                                    <select id="tglBulan" class="form-select" style="width: 100px;"></select>
                                    <select id="tglTahun" class="form-select" style="width: 100px;"></select>
                                </div>
                                <input type="hidden" id="tglPeminjamanRuangan" name="tglPeminjamanRuangan">
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label class="form-label">Waktu Mulai</label>
                                    <div class="d-flex gap-2">
                                        <select id="jam_dari" name="jam_dari" class="form-select" style="width: 100px;"></select>
                                        <select id="menit_dari" name="menit_dari" class="form-select" style="width: 100px;"></select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Waktu Selesai</label>
                                    <div class="d-flex gap-2">
                                        <select id="jam_sampai" name="jam_sampai" class="form-select" style="width: 100px;"></select>
                                        <select id="menit_sampai" name="menit_sampai" class="form-select" style="width: 100px;"></select>
                                    </div>
                                </div>
                            </div>

                            <div id="error-waktu" style="color: red; display: none;">*Waktu tidak valid</div>

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
        const bulan = parseInt(document.getElementById('tglBulan').value);
        const tahun = parseInt(document.getElementById('tglTahun').value);
        let days = 31;
        if ([4, 6, 9, 11].includes(bulan)) days = 30;
        else if (bulan === 2) days = isLeapYear(tahun) ? 29 : 28;

        const hariSelect = document.getElementById('tglHari');
        hariSelect.innerHTML = '';
        for (let i = 1; i <= days; i++) {
            hariSelect.innerHTML += `<option value="${i.toString().padStart(2, '0')}">${i}</option>`;
        }
    }

    function fillSelects() {
        const tahunSelect = document.getElementById('tglTahun');
        const bulanSelect = document.getElementById('tglBulan');
        const hariSelect = document.getElementById('tglHari');
        const now = new Date();

        for (let y = now.getFullYear(); y <= now.getFullYear() + 5; y++) {
            tahunSelect.innerHTML += `<option value="${y}">${y}</option>`;
        }
        for (let m = 1; m <= 12; m++) {
            bulanSelect.innerHTML += `<option value="${m}">${m.toString().padStart(2, '0')}</option>`;
        }

        bulanSelect.value = now.getMonth() + 1;
        tahunSelect.value = now.getFullYear();
        updateDays();
        hariSelect.value = now.getDate().toString().padStart(2, '0');
    }

    function isiWaktu(id, max) {
        const el = document.getElementById(id);
        for (let i = 0; i < max; i++) {
            const val = i.toString().padStart(2, '0');
            el.innerHTML += `<option value="${val}">${val}</option>`;
        }
    }

    document.addEventListener('DOMContentLoaded', function() {
        fillSelects();
        isiWaktu('jam_dari', 24);
        isiWaktu('jam_sampai', 24);
        isiWaktu('menit_dari', 60);
        isiWaktu('menit_sampai', 60);

        document.getElementById('tglBulan').addEventListener('change', updateDays);
        document.getElementById('tglTahun').addEventListener('change', updateDays);

        document.getElementById('form-peminjaman').addEventListener('submit', function(e) {
            const hari = document.getElementById('tglHari').value;
            const bulan = document.getElementById('tglBulan').value;
            const tahun = document.getElementById('tglTahun').value;
            const jamDari = document.getElementById('jam_dari').value;
            const menitDari = document.getElementById('menit_dari').value;
            const jamSampai = document.getElementById('jam_sampai').value;
            const menitSampai = document.getElementById('menit_sampai').value;

            const errorMsg = document.getElementById('error-message');
            const errorWaktu = document.getElementById('error-waktu');

            let isValid = hari && bulan && tahun;

            const inputDate = new Date(`${tahun}-${bulan.padStart(2, '0')}-${hari.padStart(2, '0')}`);
            const today = new Date();
            today.setHours(0, 0, 0, 0);

            if (!isValid || inputDate < today) {
                errorMsg.textContent = !isValid ? "*Harus Diisi" : "*Tanggal sudah lewat";
                errorMsg.style.display = 'inline';
                e.preventDefault();
                return;
            } else {
                errorMsg.style.display = 'none';
            }

            const startMinutes = parseInt(jamDari) * 60 + parseInt(menitDari);
            const endMinutes = parseInt(jamSampai) * 60 + parseInt(menitSampai);

            const selectedDate = new Date(`${tahun}-${bulan.padStart(2, '0')}-${hari.padStart(2, '0')}`);
            const now = new Date();
            const nowMinutes = now.getHours() * 60 + now.getMinutes();

            if (
                jamDari === "" || menitDari === "" ||
                jamSampai === "" || menitSampai === "" ||
                isNaN(parseInt(jamDari)) || isNaN(parseInt(menitDari)) ||
                isNaN(parseInt(jamSampai)) || isNaN(parseInt(menitSampai))
            ) {
                errorWaktu.textContent = '*Semua waktu harus diisi';
                errorWaktu.style.display = 'inline';
                e.preventDefault();
                return;
            } else if (endMinutes <= startMinutes) {
                errorWaktu.textContent = '*Waktu selesai harus lebih besar dari waktu mulai';
                errorWaktu.style.display = 'block';
                e.preventDefault();
                return;
            } else if (
                selectedDate.toDateString() === now.toDateString() &&
                startMinutes < nowMinutes
            ) {
                errorWaktu.textContent = '*Waktu mulai tidak boleh lebih kecil dari waktu sekarang';
                errorWaktu.style.display = 'block';
                e.preventDefault();
                return;
            } else {
                errorWaktu.style.display = 'none';
            }

            document.getElementById('tglPeminjamanRuangan').value = `${hari}-${bulan}-${tahun}`;
        });
    });
</script>

<?php
include '../../templates/footer.php';
?>