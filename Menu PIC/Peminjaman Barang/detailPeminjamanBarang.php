<?php
include '../../templates/header.php';

// Get peminjaman detail
if (isset($_GET['id'])) {
    $idPeminjamanBrg = $_GET['id'];
    $query = "SELECT p.*, m.namaMhs, m.email, m.noHp, b.namaBarang, b.kondisi 
              FROM Peminjaman p 
              JOIN Mahasiswa m ON p.nim = m.nim 
              JOIN Barang b ON p.idBarang = b.idBarang 
              WHERE p.idPeminjaman = ?";
    $params = [$idPeminjamanBrg];
    $stmt = sqlsrv_query($conn, $query, $params);
}

include '../../templates/sidebar.php';
?>
<!-- Main Content -->
<main class="col bg-white px-3 px-md-4 py-3">
    <div class="mb-5">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?= BASE_URL ?>/Menu PIC/dashboardPIC.php">Sistem Pengelolaan Lab</a></li>
                <li class="breadcrumb-item"><a href="<?= BASE_URL ?>/Menu PIC/Peminjaman Barang/peminjamanBarang.php">Peminjaman Barang</a></li>
                <li class="breadcrumb-item active">Detail Peminjaman</li>
            </ol>
        </nav>
    </div>

    <!-- Content here -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="mb-0">Detail Peminjaman Barang</h4>
        <a href="index.php" class="btn btn-secondary">
            <i class="bi bi-arrow-left"></i> Kembali
        </a>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <h5 class="card-title mb-4">Informasi Peminjaman</h5>
                    <table class="table">
                        <tr>
                            <th>ID Peminjaman</th>
                            <td><?php echo htmlspecialchars($idPeminjamanBrg['idPeminjamanBrg']); ?></td>
                        </tr>
                        <tr>
                            <th>Tanggal Peminjaman</th>
                            <td><?php echo $peminjaman['tanggalPeminjaman']->format('d/m/Y'); ?></td>
                        </tr>
                        <tr>
                            <th>Tanggal Kembali</th>
                            <td><?php echo $peminjaman['tanggalKembali']->format('d/m/Y'); ?></td>
                        </tr>
                        <tr>
                            <th>Status</th>
                            <td>
                                <?php
                                switch ($peminjaman['status']) {
                                    case 'pending':
                                        echo '<span class="badge bg-warning">Menunggu</span>';
                                        break;
                                    case 'approved':
                                        echo '<span class="badge bg-success">Disetujui</span>';
                                        break;
                                    case 'rejected':
                                        echo '<span class="badge bg-danger">Ditolak</span>';
                                        break;
                                    case 'returned':
                                        echo '<span class="badge bg-info">Dikembalikan</span>';
                                        break;
                                }
                                ?>
                            </td>
                        </tr>
                    </table>
                </div>
                <div class="col-md-6">
                    <h5 class="card-title mb-4">Informasi Peminjam</h5>
                    <table class="table">
                        <tr>
                            <th>Nama</th>
                            <td><?php echo htmlspecialchars($peminjaman['namaMhs']); ?></td>
                        </tr>
                        <tr>
                            <th>Email</th>
                            <td><?php echo htmlspecialchars($peminjaman['email']); ?></td>
                        </tr>
                        <tr>
                            <th>No. HP</th>
                            <td><?php echo htmlspecialchars($peminjaman['noHp']); ?></td>
                        </tr>
                    </table>
                </div>
            </div>
            <div class="row mt-4">
                <div class="col-12">
                    <h5 class="card-title mb-4">Informasi Barang</h5>
                    <table class="table">
                        <tr>
                            <th>Nama Barang</th>
                            <td><?php echo htmlspecialchars($peminjaman['namaBarang']); ?></td>
                        </tr>
                        <tr>
                            <th>Kondisi</th>
                            <td><?php echo htmlspecialchars($peminjaman['kondisi']); ?></td>
                        </tr>
                        <tr>
                            <th>Keterangan</th>
                            <td><?php echo htmlspecialchars($peminjaman['keterangan']); ?></td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>
</main>


<?php include '../../templates/footer.php'; ?>