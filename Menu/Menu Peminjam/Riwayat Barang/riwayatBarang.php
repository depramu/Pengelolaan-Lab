<?php
require_once __DIR__ . '/../../../function/init.php';
authorize_role(['Peminjam']);

// Pagination setup
$perPage = 9;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
if ($page < 1) $page = 1;

// Cek role dari session
if (isset($_SESSION['user_role'])) {
    // Jika role adalah Mahasiswa dan session 'nim' ada
    if ($_SESSION['user_role'] == 'Peminjam' && isset($_SESSION['nim'])) {
        $peminjam_field = 'nim';
        $peminjam_value = $_SESSION['nim'];
        // Jika role adalah Karyawan dan session 'npk' ada
    } elseif ($_SESSION['user_role'] == 'Peminjam' && isset($_SESSION['npk'])) {
        $peminjam_field = 'npk';
        $peminjam_value = $_SESSION['npk'];
    } else {
        $peminjam_field = null;
        $peminjam_value = null;
    }

    if (!empty($peminjam_field) && !empty($peminjam_value)) {
        // Hitung total data untuk Peminjam (Mahasiswa/Karyawan)
        $countQuery = "SELECT COUNT(*) AS total FROM Peminjaman_Barang WHERE $peminjam_field = ?";
        $countParams = [$peminjam_value];
        $countResult = sqlsrv_query($conn, $countQuery, $countParams);
        $countRow = sqlsrv_fetch_array($countResult, SQLSRV_FETCH_ASSOC);
        $totalData = $countRow['total'];
        $totalPages = ceil($totalData / $perPage);

        // Ambil data sesuai halaman
        $offset = ($page - 1) * $perPage;
        $query = "SELECT pb.idPeminjamanBrg, pb.idBarang, pb.tglPeminjamanBrg, pb.jumlahBrg, sp.statusPeminjaman, b.namaBarang 
                  FROM Peminjaman_Barang pb 
                  JOIN Barang b ON pb.idBarang = b.idBarang 
                  LEFT JOIN Status_Peminjaman sp ON pb.idPeminjamanBrg = sp.idPeminjamanBrg
                  WHERE pb.$peminjam_field = ? 
                  ORDER BY pb.tglPeminjamanBrg DESC
                  OFFSET ? ROWS FETCH NEXT ? ROWS ONLY";
        $params = [$peminjam_value, $offset, $perPage];
        $result = sqlsrv_query($conn, $query, $params);
    }
}

include __DIR__ . '/../../../templates/header.php';
include __DIR__ . '/../../../templates/sidebar.php';
?>
<main class="col bg-white px-3 px-md-4 py-3 position-relative">
    <h3 class="fw-semibold mb-3">Riwayat Peminjaman Barang</h3>
    <div class="mb-4">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?= BASE_URL ?>/Menu/Menu Peminjam/dashboardPeminjam.php">Sistem Pengelolaan Lab</a></li>
                <li class="breadcrumb-item active" aria-current="page">Riwayat Peminjaman Barang</li>
            </ol>
        </nav>
    </div>
    <div class="table-responsive">
        <table class="table table-hover align-middle table-bordered">
            <thead class="table-light">
                <tr class="text-center">
                    <th>No</th>
                    <th>Nama Barang</th>
                    <th>Tanggal Peminjaman</th>
                    <th>Jumlah</th>
                    <th>Status Peminjaman</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $no = $offset + 1;
                if ($result === false) {
                    echo "<tr><td colspan='7' class='text-center text-danger'>Gagal mengambil data dari database " . print_r(sqlsrv_errors(), true) . "</td></tr>";
                } elseif (sqlsrv_has_rows($result) === false) {
                    echo "<tr><td colspan='7' class='text-center'>Tidak ada data peminjaman barang.</td></tr>";
                } else {
                    while ($row = sqlsrv_fetch_array($result, SQLSRV_FETCH_ASSOC)) {
                        $statusPeminjaman = $row['statusPeminjaman'] ?? '';
                        $idPeminjaman = htmlspecialchars($row['idPeminjamanBrg'] ?? '');

                        $linkDetail = "formDetailRiwayatBrg.php?idPeminjamanBrg=" . $idPeminjaman;

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
                            <td><?= $no ?></td>
                            <td class="text-start"><?= htmlspecialchars($row['namaBarang'] ?? '') ?></td>
                            <td><?= ($row['tglPeminjamanBrg'] instanceof DateTime ? $row['tglPeminjamanBrg']->format('d M Y') : htmlspecialchars($row['tglPeminjamanBrg'] ?? '')) ?></td>
                            <td><?= htmlspecialchars($row['jumlahBrg'] ?? '') ?></td>
                            <td class="text-start"><?= htmlspecialchars($row['statusPeminjaman'] ?? '') ?></td>
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
                    $no++;
                    }
                }
                ?>
            </tbody>
        </table>
    </div>
    <?php
    generatePagination($page, $totalPages);
    ?>
</main>
<?php

include __DIR__ . '/../../../templates/footer.php';
?>