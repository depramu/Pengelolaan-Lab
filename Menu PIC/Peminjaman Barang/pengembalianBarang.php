<?php
include '../../templates/header.php';

$showModal = false;
$idPeminjamanBrg = $_GET['id'] ?? '';

// Hentikan jika tidak ada ID
if (empty($idPeminjamanBrg)) {
    die("Akses tidak valid. ID Peminjaman tidak ditemukan.");
}

// Ambil data awal peminjaman
$data = [];
$jumlahBrg = 0;
$idBarang = null;

// Menggunakan prepared statement yang lebih aman untuk GET
$query_get = "SELECT pb.jumlahBrg, pb.idBarang, b.namaBarang
              FROM Peminjaman_Barang pb
              JOIN Barang b ON pb.idBarang = b.idBarang
              WHERE pb.idPeminjamanBrg = ?";
$params_get = [$idPeminjamanBrg];
$stmt_get = sqlsrv_query($conn, $query_get, $params_get);


if ($stmt_get && ($data = sqlsrv_fetch_array($stmt_get, SQLSRV_FETCH_ASSOC))) {
    $idBarang = $data['idBarang'];
    $jumlahBrg = $data['jumlahBrg'];
    $namaBarang = $data['namaBarang'];
} else {
    $idBarang = '';
    $jumlahBrg = 0;
    $namaBarang = '';
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Ambil data dari form
    $jumlahPengembalian = (int)$_POST['jumlahPengembalian'];
    $catatan = $_POST['catatanPengembalianBarang'];
    $kondisiBrg = $_POST['kondisiBrg'];

    // Validasi Sederhana di Sisi Server (jumlahBrg dari data awal)
    if ($jumlahPengembalian <= 0 || $jumlahPengembalian > $jumlahBrg || empty($kondisiBrg) || $kondisiBrg == 'Pilih Kondisi Barang') {
        $error = "Data tidak valid. Pastikan jumlah pengembalian benar (tidak melebihi jumlah pinjam) dan kondisi barang telah dipilih.";
    } else {
        // --- MULAI TRANSAKSI DATABASE (PENTING!) ---
        sqlsrv_begin_transaction($conn);

        // LANGKAH 1: Masukkan data ke tabel pengembalian_barang
        $query_insert_pengembalian = "INSERT INTO pengembalian_barang 
                                            (idPeminjamanBrg, jumlahPengembalian, kondisiBrg, catatanPengembalianBarang) 
                                          VALUES (?, ?, ?, ?)";
        $params_insert_pengembalian = [$idPeminjamanBrg, $jumlahPengembalian, $kondisiBrg, $catatan];
        $stmt_insert_pengembalian = sqlsrv_query($conn, $query_insert_pengembalian, $params_insert_pengembalian);

        // LANGKAH 2: Update jumlahBrg (jumlah yang dipinjam) di tabel Peminjaman_Barang
        $sisaPinjaman = $jumlahBrg - $jumlahPengembalian;
        $statusPeminjaman = ($sisaPinjaman == 0) ? 'Telah Dikembalikan' : 'Sebagian Dikembalikan';

        $query_update_peminjaman = "UPDATE Peminjaman_Barang 
                                        SET jumlahBrg = ?, 
                                            statusPeminjaman = ?
                                        WHERE idPeminjamanBrg = ?";
        $params_update_peminjaman = [$sisaPinjaman, $statusPeminjaman, $idPeminjamanBrg];
        $stmt_update_peminjaman = sqlsrv_query($conn, $query_update_peminjaman, $params_update_peminjaman);

        // LANGKAH 3: Update stok di tabel master Barang (stok bertambah sesuai jumlah pengembalian)
        $query_update_stok = "UPDATE Barang SET stokBarang = stokBarang + ? WHERE idBarang = ?";
        $params_update_stok = [$jumlahPengembalian, $idBarang];
        $stmt_update_stok = sqlsrv_query($conn, $query_update_stok, $params_update_stok);

        // LANGKAH 4: Cek apakah SEMUA (3) query berhasil
        if ($stmt_insert_pengembalian && $stmt_update_peminjaman && $stmt_update_stok) {
            sqlsrv_commit($conn); // Jika semua berhasil, simpan perubahan
            $showModal = true;
        } else {
            sqlsrv_rollback($conn); // Jika ada yang gagal, batalkan semua perubahan
            $error = "Gagal memproses pengembalian barang. Silakan coba lagi.";
            // die(print_r(sqlsrv_errors(), true)); 
        }
        // --- AKHIR TRANSAKSI DATABASE ---
    }
}
include '../../templates/sidebar.php';
?>

<main class="col bg-white px-4 py-3 position-relative">
    <h3 class="fw-semibold mb-3">Pengembalian Barang</h3>
    <div class="mb-3">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="dashboardPIC.php">Sistem Pengelolaan Lab</a></li>
                <li class="breadcrumb-item"><a href="peminjamanBarang.php">Peminjaman Barang</a></li>
                <li class="breadcrumb-item active" aria-current="page">Pengembalian Barang</li>
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
                        <span class="fw-semibold">Pengembalian Barang</span>
                    </div>

                    <div class="card-body">
                        <form method="POST">
                            <div class='mb-2 row'>
                                <div class="col-md-6">
                                    <label for="idPeminjamanBrg" class="form-label fw-bold">ID Peminjaman Barang</label>
                                    <input type="hidden" class="form-control" id="idPeminjamanBrg" name="idPeminjamanBrg" value="<?= isset($idPeminjamanBrg) ? htmlspecialchars($idPeminjamanBrg) : '' ?>">
                                    <span class="form-control d-block bg-light" id="idPeminjaman"><?= $idPeminjamanBrg ?></span>
                                </div>
                                <div class="col-md-6">
                                    <label for="namaBarang" class="form-label fw-bold">Nama Barang</label>
                                    <input type="hidden" class="form-control" id="namaBarang" name="namaBarang" value="<?= isset($data['namaBarang']) ? htmlspecialchars($data['namaBarang']) : '' ?>">
                                    <span class="form-control d-block bg-light" id="namaBarang"><?= htmlspecialchars($namaBarang) ?></span>
                                </div>
                            </div>
                            <div class="mb-2 row">
                                <div class="col-md-3">
                                    <label for="jumlahBrg" class="form-label fw-bold">Jumlah Peminjaman</label>
                                    <input type="hidden" class="form-control" id="jumlahBrg" name="jumlahBrg" value="<?= $jumlahBrg ?>">
                                    <span class="form-control d-block bg-light" id="tampilJumlah"><?= $jumlahBrg ?></span>
                                </div>
                                <div class="col-md-4">
                                    <label for="jumlahPengembalian" class="form-label w-100 text-center fw-bold">Jumlah Pengembalian
                                        <span id="jumlahError" class="text-danger small mt-1" style="font: size 0.95em;display:none;">*Harus Diisi</span>
                                    </label>
                                    <div class="input-group mx-auto" style="max-width: 140px;">
                                        <button class="btn btn-outline-secondary" type="button" onclick="changeStok(-1)">-</button>
                                        <input class="form-control text-center" id="jumlahPengembalian" name="jumlahPengembalian" value="0" min="0" required style="max-width: 70px;">
                                        <button class="btn btn-outline-secondary" type="button" onclick="changeStok(1)">+</button>
                                    </div>
                                </div>
                                <div class="col-md-5">
                                    <label for="txtKondisi" class="form-label fw-bold">Kondisi Barang
                                        <span id="kondisiError" class="text-danger small mt-1" style="font: size 0.95em;display:none;">*Harus Dipilih</span>
                                    </label>
                                    <select class="form-select" id="txtKondisi" name="kondisiBrg">
                                        <option selected>Pilih Kondisi Barang</option>
                                        <option value="Baik" <?= (isset($data['kondisiBrg']) && $data['kondisiBrg'] == 'Baik') ? 'selected' : '' ?>>Baik</option>
                                        <option value="Rusak" <?= (isset($data['kondisiBrg']) && $data['kondisiBrg'] == 'Rusak') ? 'selected' : '' ?>>Rusak</option>
                                    </select>
                                </div>
                            </div>
                            <div class="mb-2">
                                <label for="catatanPengembalianBarang" class="form-label fw-bold">Catatan Pengembalian
                                    <span id="catatanError" class="text-danger small mt-1" style="font: size 0.95em;display:none;">*Harus Diisi</span>
                                </label>
                                <textarea type="text" class="form-control" id="catatanPengembalianBarang" name="catatanPengembalianBarang" rows="3" style="resize: none;"><?= isset($data['catatanPengembalianBarang']) ? htmlspecialchars($data['catatanPengembalianBarang']) : '' ?></textarea>
                            </div>
                            <div class="d-flex justify-content-between mt-4">
                                <a href="peminjamanBarang.php" class="btn btn-secondary">Kembali</a>
                                <button type="submit" class="btn btn-primary">Kirim</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal Berhasil -->
        <div class="modal fade" id="successModal" tabindex="-1">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="confirmModalLabel">Berhasil</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <p>Peminjaman berhasil Dikembalikan.</p>
                    </div>
                    <div class="modal-footer">
                        <a href="peminjamanBarang.php" class="btn btn-primary">OK</a>
                    </div>
                </div>
            </div>
        </div>

    </div>
    <!-- End Edit Barang -->
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

<script>
    let jumlah = parseInt(document.getElementById('jumlahBrg').value) || 0;

    function updateJumlah() {
        // Update tampilan ke user
        document.getElementById('tampilJumlah').textContent = jumlah;

        // Update input hidden untuk dikirim ke server
        document.getElementById('jumlahBrg').value = jumlah;
    }
</script>

<script>
    let id = parseInt(document.getElementById('idPeminjamanBrg').value) || 0;

    function updateId() {
        // Update tampilan ke user
        document.getElementById('idPeminjaman').textContent = id;

        // Update input hidden untuk dikirim ke server
        document.getElementById('idPeminjamanBrg').value = id;
    }
</script>

<script>
    let namaBarang = document.getElementById('namaBarang').value || '';

    function updateNamaBarang() {
        // Update tampilan ke user
        document.getElementById('namaBarang').textContent = namaBarang;

        // Update input hidden untuk dikirim ke server
        document.getElementById('namaBarang').value = namaBarang;
    }
</script>

<?php if ($showModal) : ?>
    <script>
        let modal = new bootstrap.Modal(document.getElementById('successModal'));
        modal.show();
    </script>
<?php endif; ?>


<?php include '../../templates/footer.php'; ?>