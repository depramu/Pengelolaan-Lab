<?php
include '../../templates/header.php';

// Inisialisasi variabel result sebagai null
$result = null;

// Cek role dari session
if (isset($_SESSION['user_role'])) {

    // Jika role adalah Mahasiswa dan session 'nim' ada
    if ($_SESSION['user_role'] == 'Mahasiswa' && isset($_SESSION['nim'])) {
        $nim_value = $_SESSION['nim'];
        $query = "SELECT idPeminjamanRuangan, idRuangan, tglPeminjamanRuangan, waktuMulai, waktuSelesai, statusPeminjaman FROM Peminjaman_Ruangan WHERE nim = ? ORDER BY tglPeminjamanRuangan DESC, waktuMulai DESC";
        $params = [$nim_value];

        $result = sqlsrv_query($conn, $query, $params);

        // Jika role adalah Karyawan dan session 'npk' ada
    } elseif ($_SESSION['user_role'] == 'Karyawan' && isset($_SESSION['npk'])) {
        $npk_value = $_SESSION['npk'];
        $query = "SELECT idPeminjamanRuangan, idRuangan, tglPeminjamanRuangan, waktuMulai, waktuSelesai, statusPeminjaman FROM Peminjaman_Ruangan WHERE npk = ? ORDER BY tglPeminjamanRuangan DESC, waktuMulai DESC";
        $params = [$npk_value];

        $result = sqlsrv_query($conn, $query, $params);
    }
}

$currentPage = basename($_SERVER['PHP_SELF']);
include '../../templates/sidebar.php';
?>
    <main class="col bg-white px-3 px-md-4 py-3 position-relative">
        <div class="mb-4">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="<?= BASE_URL ?>/Menu Peminjam/dashboardPeminjam.php">Sistem Pengelolaan Lab</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Riwayat Peminjaman Ruangan</li>
                </ol>
            </nav>
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle table-bordered">
                <thead class="table-light">
                <tr class="text-center">
                    <th>ID Peminjaman</th>
                    <th>ID Ruangan</th>
                    <th>Tanggal Peminjaman</th>
                    <th>Waktu Peminjaman </th>
                    <th>Aksi</th>
                </tr>
                </thead>
                <tbody>
                <?php
                if ($result === false) {
                    echo "<tr><td colspan='6' class='text-center text-danger'>Gagal mengambil data dari database " . print_r(sqlsrv_errors(), true) . "</td></tr>";
                } elseif (sqlsrv_has_rows($result) === false) {
                    echo "<tr><td colspan='6' class='text-center'>Tidak ada data peminjaman ruangan.</td></tr>";
                } else {
                    while ($row = sqlsrv_fetch_array($result, SQLSRV_FETCH_ASSOC)) {
                        $statusPeminjaman = $row['statusPeminjaman'] ?? '';
                        $idPeminjaman = htmlspecialchars($row['idPeminjamanRuangan'] ?? '');

                        $linkDetail = "formDetailRiwayatRuangan.php?idPeminjamanRuangan=" . $idPeminjaman;

                        if ($statusPeminjaman == 'Telah Dikembalikan') {
                            $iconSrc = BASE_URL . '/icon/centang.svg';
                            $altText = 'Peminjaman Selesai';
                        } elseif ($statusPeminjaman == 'Sedang Dipinjam') {
                            $iconSrc = BASE_URL . '/icon/jamHijau.svg';
                            $altText = 'Sedang Dipinjam';
                        } elseif ($statusPeminjaman == 'Menunggu Pengecekan') {
                            $iconSrc = BASE_URL . '/icon/jamHijau.svg';
                            $altText = 'Menunggu Pengecekan oleh PIC';
                        } elseif ($statusPeminjaman == 'Menunggu Persetujuan') {
                            $iconSrc = BASE_URL . '/icon/jamKuning.svg';
                            $altText = 'Menunggu Persetujuan oleh PIC';
                        } elseif ($statusPeminjaman == 'Ditolak') {
                            $iconSrc = BASE_URL . '/icon/silang.svg';
                            $altText = 'Ditolak';
                        }
                        ?>
                        <tr class="text-center">
                            <td><?= htmlspecialchars($row['idPeminjamanRuangan'] ?? '') ?></td>
                            <td><?= htmlspecialchars($row['idRuangan'] ?? '') ?></td>
                            <td><?= ($row['tglPeminjamanRuangan'] instanceof DateTime ? $row['tglPeminjamanRuangan']->format('d-m-Y') : htmlspecialchars($row['tglPeminjamanRuangan'] ?? '')) ?></td>
                            <td><?= ($row['waktuMulai'] instanceof DateTime ? $row['waktuMulai']->format('H:i') : htmlspecialchars($row['waktuMulai'] ?? '')) ?>
                                -
                                <?= ($row['waktuSelesai'] instanceof DateTime ? $row['waktuSelesai']->format('H:i') : htmlspecialchars($row['waktuSelesai'] ?? '')) ?></td>
                            <td class="td-aksi">
                                <a href="<?= $linkDetail ?>">
                                    <img src="<?= $iconSrc ?>" alt="<?= $altText ?>" class="aksi-icon" title="<?= $altText ?>">
                                </a>
                                <a href="<?= $linkDetail ?>">
                                    <img src="<?= BASE_URL ?>/icon/detail.svg" alt="Lihat Detail" class="aksi-icon">
                                </a>
                            </td>
                        </tr>
                        <?php
                    }
                }
                ?>
                </tbody>
            </table>
        </div>
    </main>
<?php

include '../../templates/footer.php';
?>