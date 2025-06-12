<?php
include '../../templates/header.php';

$currentPage = basename($_SERVER['PHP_SELF']);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $tanggalPeminjaman = $_POST['tanggal_peminjaman'];
    $waktuPeminjaman = $_POST['waktu_peminjaman'];
}

include '../../templates/sidebar.php';
?>
<main class="col bg-white px-3 px-md-4 py-3 position-relative">
    <div class="mb-1">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="../dashboardPeminjam.php">Sistem Pengelolaan Lab</a></li>
                <li class="breadcrumb-item active" aria-current="page">Cek Ruangan </li>
            </ol>
        </nav>
    </div>

    <div class="container mt-4">
        <div class="row justify-content-center">
            <div class="col-md-8 col-lg-12 " style="margin-right: 20px;">
                <div class="card border border-dark">
                    <div class="card-header bg-white border-bottom border-dark">
                        <span class="fw-semibold">Peminjaman Ruangan</span>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="lihatRuangan.php">
                            <div class="mb-2">
                                <label for="tanggal_peminjaman" class="form-label">
                                    Pilih Tanggal Peminjaman <span id="error-tanggal" style="color: red; display: none; margin-left: 10px;">*Harus Diisi</span>
                                </label>
                                <input type="date" class="form-control" id="tanggal_peminjaman" name="tanggal_peminjaman">
                            </div>

                            <div class="row mb-2">
                                <div class="col-md-6">
                                    <label for="waktu_dari" class="form-label">
                                        Waktu Mulai <span id="error-waktu-dari" style="color: red; display: none; margin-left: 10px;">*Harus Diisi</span>
                                    </label>
                                    <input type="time" class="form-control" id="waktu_dari" name="waktu_dari">
                                </div>
                                <div class="col-md-6">
                                    <label for="waktu_sampai" class="form-label">
                                        Waktu Selesai <span id="error-waktu-sampai" style="color: red; display: none; margin-left: 10px;">*Harus Diisi</span>
                                    </label>
                                    <input type="time" class="form-control" id="waktu_sampai" name="waktu_sampai">
                                </div>
                            </div>

                            <div class="d-flex justify-content-end mt-4">
                                <button type="submit" class="btn btn-primary">Cek</button>
                            </div>
                        </form>
                        <!-- script untuk validasi kolom harus diisi input -->
                        <script>
                            document.querySelector('form').addEventListener('submit', function(event) {
                                var tanggal = document.getElementById('tanggal_peminjaman').value;
                                var waktuDari = document.getElementById('waktu_dari').value;
                                var waktuSampai = document.getElementById('waktu_sampai').value;

                                let errorTanggal = document.getElementById('error-tanggal');
                                let errorWaktuDari = document.getElementById('error-waktu-dari');
                                let errorWaktuSampai = document.getElementById('error-waktu-sampai');

                                let isValid = true;

                                if (tanggal.trim() === '') {
                                    errorTanggal.style.display = 'inline';
                                    isValid = false;
                                } else {
                                    errorTanggal.style.display = 'none';
                                }

                                if (waktuDari.trim() === '') {
                                    errorWaktuDari.style.display = 'inline';
                                    isValid = false;
                                } else {
                                    errorWaktuDari.style.display = 'none';
                                }

                                if (waktuSampai.trim() === '') {
                                    errorWaktuSampai.style.display = 'inline';
                                    isValid = false;
                                } else {
                                    errorWaktuSampai.style.display = 'none';
                                }

                                if (!isValid) {
                                    event.preventDefault(); // Mencegah submit jika ada input kosong
                                }
                            });
                        </script>

                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- End Tambah Barang -->
    </div>
</main>

<?php

include '../../templates/footer.php'

?>