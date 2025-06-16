<?php
include '../../templates/header.php';

$showModal = false;
$idPeminjamanRuangan = $_GET['id'] ?? '';

include '../../templates/sidebar.php';
?>

            <!-- Content Area -->
            <main class="col bg-white px-4 py-3 position-relative">
                <div class="mb-3">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="dashboardPIC.php">Sistem Pengelolaan Lab</a></li>
                            <li class="breadcrumb-item"><a href="peminjamanRuangan.php">Peminjaman Ruangan</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Pengembalian Ruangan </li>
                        </ol>
                    </nav>
                </div>


                <!-- Pengembalian Barang -->
                <div class="container mt-4">
                    <?php if (isset($error)) : ?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <?php echo $error; ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    <?php endif; ?>

                    <div class="row justify-content-center">
                        <div class="col-md-8 col-lg-12" style="margin-right: 20px;">
                            <div class="card border border-dark">
                                <div class="card-header bg-white border-bottom border-dark">
                                    <span class="fw-semibold">Pengembalian Peminjaman Barang</span>
                                </div>
                                <div class="card-body">
                                    <form method="POST">
                                        <div class="mb-2 row">
                                            <div class="col md-6">
                                                <label for="idPeminjamanRuangan" class="form-label">ID Peminjaman Barang</label>
                                                <input type="text" class="form-control" id="idPeminjamanRuangan" name="idPeminjamanRuangan" value="<?= isset($idPeminjamanRuangan) ? htmlspecialchars(  $idPeminjamanRuangan) : '' ?>" disabled>
                                            </div>
                                            <div class="col-md-6">
                                                <label for="txtKondisi" class="form-label">Kondisi Ruangan
                                                    <span id="kondisiError" class="text-danger small mt-1" style="font: size 0.95em;display:none;">*Harus Dipilih</span>
                                                </label>
                                                <select class="form-select" id="txtKondisi" name="kondisiRuangan">
                                                    <option selected>Pilih Kondisi Ruangan</option>
                                                    <option value="Baik" <?= (isset($data['kondisiRuangan']) && $data['kondisiRuangan'] == 'Baik') ? 'selected' : '' ?>>Baik</option>
                                                    <option value="Rusak" <?= (isset($data['kondisiRuangan']) && $data['kondisiRuangan'] == 'Rusak') ? 'selected' : '' ?>>Rusak</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="mb-2">
                                            <label for="catatanPengembalianBarang" class="form-label">Catatan Pengembalian
                                                <span id="catatanError" class="text-danger small mt-1" style="font: size 0.95em;display:none;">*Harus Diisi</span>
                                            </label>
                                            <textarea type="text" class="form-control" id="catatanPengembalianBarang" name="catatanPengembalianBarang" rows="3" style="resize: none;"><?= isset($data['catatanPengembalianBarang']) ? htmlspecialchars($data['catatanPengembalianBarang']) : '' ?></textarea>
                                        </div>
                                        <div class="mb-2">
                                            <label for="dokumentasiSebelum">Dokumentasi sebelum pemakaian <br></label>
                                            <a href="">Unduh bukti dokumentasi</a>
                                        </div>
                                        <div class="mb-2">
                                            <label for="dokumentasiSebelum">Dokumentasi sesudah pemakaian <br></label>
                                            <a href="">Unduh bukti dokumentasi</a>
                                        </div>
                                        <div class="d-flex justify-content-between mt-4">
                                            <a href="peminjamanRuangan.php" class="btn btn-secondary">Kembali</a>
                                            <button type="submit" class="btn btn-primary">Kirim</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
            </main>


        <script>    
            // Fungsi stepper untuk tombol +/-
            function changeStok(val) {
                // Targetkan ID yang benar: 'jumlahPengembalian'
                let stokInput = document.getElementById('jumlahPengembalian');
                let maxStok = parseInt(document.getElementById('jumlahBrg').value) || 0;
                let current = parseInt(stokInput.value) || 0;
                let next = current + val;

                if (next < 0) next = 0;
                if (next > maxStok) next = maxStok; // Batasi agar tidak lebih dari jumlah pinjaman
                stokInput.value = next;
            }
        </script>

        <script>
            // Fungsi validasi form sebelum submit
            document.querySelector('form').addEventListener('submit', function(e) {
                let valid = true;

                // Validasi Jumlah Pengembalian
                // Targetkan ID yang benar: 'jumlahPengembalian'
                const jumlahInput = document.getElementById('jumlahPengembalian');
                const jumlahError = document.getElementById('jumlahError');
                const jumlahPinjam = parseInt(document.getElementById('jumlahBrg').value) || 0;

                if (parseInt(jumlahInput.value) <= 0) {
                    jumlahError.textContent = '*Jumlah harus lebih dari 0.';
                    jumlahError.style.display = 'block';
                    valid = false;
                } else if (parseInt(jumlahInput.value) > jumlahPinjam) {
                    jumlahError.textContent = '*Jumlah melebihi yang dipinjam.';
                    jumlahError.style.display = 'block';
                    valid = false;
                } else {
                    jumlahError.style.display = 'none';
                }

                // Validasi Kondisi Barang
                const kondisiSelect = document.getElementById('txtKondisi');
                const kondisiError = document.getElementById('kondisiError');
                if (kondisiSelect.value === 'Pilih Kondisi Barang') {
                    kondisiError.style.display = 'block';
                    valid = false;
                } else {
                    kondisiError.style.display = 'none';
                }

                // Validasi Catatan Pengembalian
                // Targetkan ID yang benar: 'catatanPengembalianBarang'
                const catatanInput = document.getElementById('catatanPengembalianBarang');
                const catatanError = document.getElementById('catatanError');
                if (catatanInput.value.trim() === '') {
                    catatanError.style.display = 'block';
                    valid = false;
                } else {
                    catatanError.style.display = 'none';
                }

                if (!valid) {
                    e.preventDefault(); // Hentikan pengiriman form jika tidak valid
                }
            });
        </script>

<?php include '../../templates/footer.php'; ?>