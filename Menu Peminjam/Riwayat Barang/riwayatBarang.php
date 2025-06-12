<?php
include '../../templates/header.php';

if ($_SESSION['user_role'] == 'Mahasiswa') {
    $countQuery = "SELECT COUNT(*) AS total FROM Peminjaman_Barang WHERE nim = '$_SESSION[nim]'";
} else {
    $countQuery = "SELECT COUNT(*) AS total FROM Peminjaman_Barang WHERE npk = '$_SESSION[npk]'";
}
$countResult = sqlsrv_query($conn, $countQuery);
$countRow = sqlsrv_fetch_array($countResult, SQLSRV_FETCH_ASSOC);
$totalData = $countRow['total'];

if ($_SESSION['user_role'] == 'Mahasiswa') {
    $query = "SELECT idPeminjamanBrg, idBarang, jumlahBrg, tglPeminjamanBrg, statusPeminjaman FROM Peminjaman_Barang WHERE nim = '$_SESSION[nim]' ORDER BY idPeminjamanBrg";
} else {
    $query = "SELECT idPeminjamanBrg, idBarang, jumlahBrg, tglPeminjamanBrg, statusPeminjaman FROM Peminjaman_Barang WHERE npk = '$_SESSION[npk]' ORDER BY idPeminjamanBrg";
}
$result = sqlsrv_query($conn, $query);

$currentPage = basename($_SERVER['PHP_SELF']);

include '../../templates/sidebar.php';

?>
<main class="col bg-white px-3 px-md-4 py-3 position-relative">
    <div class="mb-4">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?= BASE_URL ?>/Menu Peminjam/dashboardPeminjam.php">Sistem Pengelolaan Lab</a></li>
                <li class="breadcrumb-item active" aria-current="page">Riwayat Peminjaman Barang</li>
            </ol>
        </nav>
    </div>
    <!-- Table Peminjaman Barang -->
    <div class="table-responsive">
        <table class="table table-hover align-middle table-bordered">
            <thead class="table-light">
                <tr>
                    <th>ID Peminjaman</th>
                    <th>ID Barang</th>
                    <th>Tanggal Peminjaman</th>
                    <th>Jumlah Peminjaman </th>
                    <th class="text-center">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $hasData = false;
                while ($row = sqlsrv_fetch_array($result, SQLSRV_FETCH_ASSOC)) {
                    $hasData = true;
                ?>
                    <tr>
                        <td><?= htmlspecialchars($row['idPeminjamanBrg']) ?></td>
                        <td><?= htmlspecialchars($row['idBarang']) ?></td>
                        <td>
                            <?= ($row['tglPeminjamanBrg'] instanceof DateTimeInterface) ? $row['tglPeminjamanBrg']->format('D, d M Y') : 'N/A'; ?>
                        </td>
                        <td><?= htmlspecialchars($row['jumlahBrg']) ?></td>
                        <td class="text-center">
                            <?php
                            $statusFromDB = $row['statusPeminjaman'] ?? 'Menunggu Persetujuan';

                            $iconSource = 'bi-hourglass-split text-info';
                            $statusText = 'Status Tidak Diketahui';

                            switch ($statusFromDB) {
                                case 'Menunggu Persetujuan':
                                    $iconSource = '../../icon/jamkuning.svg';
                                    $statusText = 'Menunggu Persetujuan';
                                    break;
                                case 'Sedang Dipinjam':
                                    $iconSource = '../../icon/jamhijau.svg';
                                    $statusText = 'Sedang Dipinjam';
                                    break;
                                case 'Ditolak':
                                    $iconSource = '../../icon/silang.svg';
                                    $statusText = 'Ditolak';
                                    break;
                                case 'Telah Dikembalikan':
                                    $iconSource = '../../icon/centang.svg';
                                    $statusText = 'Telah Dikembalikan';
                                    break;
                            }
                            ?>

                            <span title="<?= htmlspecialchars($statusText); ?>" style="cursor: help; vertical-align: middle;">
                                <?php
                                // Cek apakah $iconSource berisi ekstensi file gambar
                                if (str_contains($iconSource, '.svg') || str_contains($iconSource, '.png')) {
                                    // JIKA YA: Tampilkan sebagai gambar <img>
                                    echo '<img src="' . htmlspecialchars($iconSource) . '" 
                                                alt="' . htmlspecialchars($statusText) . '" 
                                                style="width: 30px; height: 30px;" 
                                                class="me-2 mb-2">';
                                } else {
                                    // JIKA TIDAK: Tampilkan sebagai font icon <i> (cara lama)
                                    echo '<i class="bi ' . htmlspecialchars($iconSource) . ' me-3" 
                     style="font-size: 1.2rem;"></i>';
                                }
                                ?>
                            </span>
                            <?php if ($statusFromDB == 'Menunggu Persetujuan') { ?>
                                <a href="#=<?= htmlspecialchars($row['idPeminjamanBrg']); ?>" class="text-secondary" title="Lihat Detail" style="vertical-align: middle;">
                                    <i><img src="../../icon/detail.svg" alt="Detail" style="width: 25px; height: 25px; margin-bottom: 7px;"></i>
                                </a>
                            <?php } else if ($statusFromDB == 'Sedang Dipinjam') { ?>
                                <a href="detailPeminjamanBarang.php?id=<?= htmlspecialchars($row['idPeminjamanBrg']); ?>" class="text-secondary" title="Lihat Detail" style="vertical-align: middle;">
                                    <i><img src="../../icon/detail.svg" alt="Detail" style="width: 25px; height: 25px; margin-bottom: 7px;"></i>
                                </a>
                            <?php } else if ($statusFromDB == 'Ditolak') { ?>
                                <a href="detailPenolakanBrg.php?id=<?= htmlspecialchars($row['idPeminjamanBrg']); ?>" class="text-secondary" title="Lihat Detail" style="vertical-align: middle;">
                                    <i><img src="../../icon/detail.svg" alt="Detail" style="width: 25px; height: 25px; margin-bottom: 7px;"></i>
                                </a>
                            <?php } else if ($statusFromDB == 'Telah Dikembalikan') { ?>
                                <a href="detailPeminjamanBarang.php?id=<?= htmlspecialchars($row['idPeminjamanBrg']); ?>" class="text-secondary" title="Lihat Detail" style="vertical-align: middle;">
                                    <i><img src="../../icon/detail.svg" alt="Detail" style="width: 25px; height: 25px; margin-bottom: 7px;"></i>
                                </a>
                            <?php } ?>
                        </td>
                    </tr>
                <?php }

                if (!$hasData) {
                    echo '<tr><td colspan="5" class="text-center">Tidak ada data peminjaman</td></tr>';
                }
                ?>
            </tbody>
        </table>
    </div>
</main>

<?php

include '../../templates/footer.php';
?>