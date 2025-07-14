<?php
require_once __DIR__ . '/../../../function/init.php'; // Penyesuaian: gunakan init.php untuk inisialisasi dan otorisasi
authorize_role('PIC Aset');

// Pagination setup
$currentPage = basename($_SERVER['PHP_SELF']); // Determine the current page
$perPage = 8;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
if ($page < 1) $page = 1;

// Hitung total data
$countQuery = "SELECT COUNT(*) AS total 
              FROM Peminjaman_Barang pb
              LEFT JOIN Status_Peminjaman sp ON pb.idPeminjamanBrg = sp.idPeminjamanBrg
              LEFT JOIN Mahasiswa m ON pb.nim = m.nim
              LEFT JOIN Karyawan k ON pb.npk = k.npk";
$countResult = sqlsrv_query($conn, $countQuery);
$countRow = sqlsrv_fetch_array($countResult, SQLSRV_FETCH_ASSOC);
$totalData = $countRow['total'];
$totalPages = ceil($totalData / $perPage);

// Ambil data sesuai halaman
$offset = ($page - 1) * $perPage;
$query = "SELECT pb.idPeminjamanBrg, pb.idBarang, pb.jumlahBrg, 
                 pb.tglPeminjamanBrg, sp.statusPeminjaman, b.namaBarang,
                 COALESCE(m.nama, k.nama) AS namaPeminjam
          FROM Peminjaman_Barang pb
          JOIN Barang b ON pb.idBarang = b.idBarang 
          LEFT JOIN Status_Peminjaman sp ON pb.idPeminjamanBrg = sp.idPeminjamanBrg
          LEFT JOIN Mahasiswa m ON pb.nim = m.nim
          LEFT JOIN Karyawan k ON pb.npk = k.npk
          ORDER BY pb.tglPeminjamanBrg
          OFFSET $offset ROWS FETCH NEXT $perPage ROWS ONLY";
$result = sqlsrv_query($conn, $query);

include '../../../templates/header.php';
include '../../../templates/sidebar.php';
?>

<main class="col bg-white px-3 px-md-4 py-3 position-relative">
    <h3 class="fw-semibold mb-3">Peminjaman Barang</h3>
    <div class="mb-4">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?= BASE_URL ?>/Menu/Menu PIC/dashboardPIC.php">Sistem Pengelolaan Lab</a></li>
                <li class="breadcrumb-item active" aria-current="page">Peminjaman Barang</li>
            </ol>
        </nav>
    </div>

    <div class="table-responsive">
        <table class="table table-hover align-middle table-bordered">
            <thead class="table-light">
                <tr class="text-center">
                    <th>No</th>
                    <th>Nama Barang</th>
                    <th>Nama Peminjam</th>
                    <th>Tanggal Peminjaman</th>
                    <th>Jumlah Peminjaman</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $no = $offset + 1;
                $hasData = false;
                while ($row = sqlsrv_fetch_array($result, SQLSRV_FETCH_ASSOC)) {
                    $hasData = true;
                    $statusPeminjaman = $row['statusPeminjaman'] ?? '';
                    $idPeminjaman = htmlspecialchars($row['idPeminjamanBrg'] ?? '');
                    $terlambat = false;
                        if (
                            $statusPeminjaman === 'Sedang Dipinjam' &&
                            isset($row['tglPeminjamanBrg']) &&
                            $row['tglPeminjamanBrg'] instanceof DateTime
                        ) {
                            $deadline = clone $row['tglPeminjamanBrg'];
                            $deadline->setTime(23, 59, 59);
                            $now = new DateTime('now', new DateTimeZone('Asia/Jakarta'));
                            if ($now > $deadline) {
                                $terlambat = true;
                            }
                        }

                    if ($statusPeminjaman == 'Menunggu Persetujuan') {
                        $iconSrc = BASE_URL . '/icon/jamAbu.svg';
                        $altText = 'Menunggu Persetujuan oleh PIC';
                        $linkDetail = BASE_URL . '/Menu/Menu PIC/Peminjaman Barang/pengajuanBarang.php?id=' . $idPeminjaman;
                    } elseif ($statusPeminjaman == 'Sedang Dipinjam') {
                        $iconSrc = BASE_URL . '/icon/jamKuning.svg';
                        $altText = 'Sedang Dipinjam';
                        $linkDetail = BASE_URL . '/Menu/Menu PIC/Peminjaman Barang/pengembalianBarang.php?id=' . $idPeminjaman;
                    } elseif ($statusPeminjaman == 'Sebagian Dikembalikan') {
                        $iconSrc = BASE_URL . '/icon/jamHijau.svg';
                        $altText = 'Sebagian Dikembalikan';
                        $linkDetail = BASE_URL . '/Menu/Menu PIC/Peminjaman Barang/pengembalianBarang.php?id=' . $idPeminjaman;
                    } elseif ($statusPeminjaman == 'Ditolak') {
                        $iconSrc = BASE_URL . '/icon/silang.svg';
                        $altText = 'Ditolak';
                        $linkDetail = BASE_URL . '/Menu/Menu PIC/Peminjaman Barang/detailPeminjamanBarang.php?id=' . $idPeminjaman;
                    } elseif ($statusPeminjaman == 'Telah Dikembalikan') {
                        $iconSrc = BASE_URL . '/icon/centang.svg';
                        $altText = 'Peminjaman Selesai';
                        $linkDetail = BASE_URL . '/Menu/Menu PIC/Peminjaman Barang/detailPeminjamanBarang.php?id=' . $idPeminjaman;
                    } else {
                        $iconSrc = BASE_URL . '/icon/jamKuning.svg';
                        $altText = 'Status Tidak Diketahui';
                        $linkDetail = '#';
                    }
                ?>
                    <tr class="<?= $terlambat ? 'table-danger' : '' ?> text-center">
                        <td><?= $no ?></td>
                        <td class="text-start"><?= htmlspecialchars($row['namaBarang'] ?? '') ?></td>
                        <td class="text-start"><?= htmlspecialchars($row['namaPeminjam'] ?? '') ?></td>
                        <td><?= ($row['tglPeminjamanBrg'] instanceof DateTime ? $row['tglPeminjamanBrg']->format('d M Y') : htmlspecialchars($row['tglPeminjamanBrg'] ?? '')) ?></td>
                        <td><?= htmlspecialchars($row['jumlahBrg'] ?? '') ?></td>
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

                if (!$hasData) {
                    echo '<tr><td colspan="7" class="text-center">Tidak ada data peminjaman</td></tr>';
                }
                ?>
            </tbody>
        </table>
    </div>
    <table class="legend-status">
            <tr>
                <td><p><img src="<?= BASE_URL?>/icon/centang.svg" class="legend-icon"> : Telah Dikembalikan</p></td>
                <td><p><img src="<?= BASE_URL?>/icon/silang.svg" class="legend-icon"> : Ditolak</p></td>
                <td><p><img src="<?= BASE_URL?>/icon/jamhijau.svg" class="legend-icon"> : Sebagian Dikembalikan</p></td>
                <td><p><img src="<?= BASE_URL?>/icon/jamkuning.svg" class="legend-icon"> : Sedang Dipinjam</p></td>
                <td><p><img src="<?= BASE_URL?>/icon/jamAbu.svg" class="legend-icon"> : Menunggu Persetujuan</p></td>
            </tr>
        </table>
    <?php
    generatePagination($page, $totalPages);
    ?>
</main>

<?php include '../../../templates/footer.php'; ?>