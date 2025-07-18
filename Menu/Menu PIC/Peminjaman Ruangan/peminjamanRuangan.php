<?php
require_once __DIR__ . '/../../../function/init.php';
authorize_role(['PIC Aset']);

$perPage = 8;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
if ($page < 1) $page = 1;

// Hitung total data
$countQuery = "SELECT COUNT(*) AS total 
               FROM Peminjaman_Ruangan pr
               LEFT JOIN Mahasiswa m ON pr.nim = m.nim
               LEFT JOIN Karyawan k ON pr.npk = k.npk";
$countResult = sqlsrv_query($conn, $countQuery);
$countRow = sqlsrv_fetch_array($countResult, SQLSRV_FETCH_ASSOC);
$totalData = $countRow['total'];
$totalPages = ceil($totalData / $perPage);

// Ambil data sesuai halaman dengan JOIN ke tabel Status_Peminjaman dan nama Mahasiswa/Karyawan
$offset = ($page - 1) * $perPage;
$query = "SELECT pr.*, r.namaRuangan, sp.statusPeminjaman, 
                 COALESCE(m.nama, k.nama) AS namaPeminjam
          FROM Peminjaman_Ruangan pr 
          JOIN Ruangan r ON pr.idRuangan = r.idRuangan 
          LEFT JOIN Status_Peminjaman sp ON pr.idPeminjamanRuangan = sp.idPeminjamanRuangan
          LEFT JOIN Mahasiswa m ON pr.nim = m.nim
          LEFT JOIN Karyawan k ON pr.npk = k.npk
          ORDER BY pr.idPeminjamanRuangan 
          OFFSET $offset ROWS FETCH NEXT $perPage ROWS ONLY";
$result = sqlsrv_query($conn, $query);
if ($result === false) {
  echo "Error executing query: <br>";
  die(print_r(sqlsrv_errors(), true));
}

include '../../../templates/header.php';
include '../../../templates/sidebar.php';
?>

<main class="col bg-white px-3 px-md-4 py-3 position-relative">
  <h3 class="fw-semibold mb-3">Peminjaman Ruangan</h3>
  <div class="mb-4">
    <nav aria-label="breadcrumb">
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="<?= BASE_URL ?>/Menu/Menu PIC/dashboardPIC.php">Sistem Pengelolaan Lab</a></li>
        <li class="breadcrumb-item active" aria-current="page">Peminjaman Ruangan</li>
      </ol>
    </nav>
  </div>

  <!-- Table Peminjaman Ruangan -->
  <div class="table-responsive">
    <table class="table table-hover align-middle table-bordered">
      <thead class="table-light">
        <tr class="text-center">
          <th>No</th>
          <th>Nama Ruangan</th>
          <th>Nama Peminjam</th>
          <th>Tanggal Peminjaman</th>
          <th>Waktu Mulai</th>
          <th>Waktu Selesai</th>
          <th>Aksi</th>
        </tr>
      </thead>
      <tbody>
        <?php
        $hasData = false;
        $no = $offset + 1;
        while ($row = sqlsrv_fetch_array($result, SQLSRV_FETCH_ASSOC)) {
          $hasData = true;
          $statusPeminjaman = $row['statusPeminjaman'] ?? '';
          $idPeminjaman = htmlspecialchars($row['idPeminjamanRuangan'] ?? '');

          $now = new DateTime();
                            $terlambat = false;

                            if (
                                $statusPeminjaman === 'Sedang Dipinjam' &&
                                ($row['tglPeminjamanRuangan'] instanceof DateTime) &&
                                ($row['waktuSelesai'] instanceof DateTime) &&
                                $statusPeminjaman !== 'Telah Dikembalikan'
                            ) {
                                $tgl = $row['tglPeminjamanRuangan']->format('Y-m-d');
                                $jam = $row['waktuSelesai']->format('H:i:s');
                                $waktuSelesaiFull = new DateTime("$tgl $jam");

                                $terlambat = $now > $waktuSelesaiFull;
                            }

          // Penyesuaian link dan ikon aksi sesuai status
          switch ($statusPeminjaman) {
            case 'Menunggu Persetujuan':
              $iconSrc = BASE_URL . '/icon/jamAbu.svg';
              $altText = 'Menunggu Persetujuan oleh PIC';
              $linkAksi = BASE_URL . '/Menu/Menu PIC/Peminjaman Ruangan/pengajuanRuangan.php?id=' . $idPeminjaman;
              $linkDetail = BASE_URL . '/Menu/Menu PIC/Peminjaman Ruangan/pengajuanRuangan.php?id=' . $idPeminjaman;
              break;
            case 'Menunggu Pengecekan':
              $iconSrc = BASE_URL . '/icon/jamhijau.svg';
              $altText = 'Menunggu Pengecekan oleh PIC';
              $linkAksi = BASE_URL . '/Menu/Menu PIC/Peminjaman Ruangan/pengembalianRuangan.php?id=' . $idPeminjaman;
              $linkDetail = BASE_URL . '/Menu/Menu PIC/Peminjaman Ruangan/pengembalianRuangan.php?id=' . $idPeminjaman;
              break;
            case 'Sedang Dipinjam':
              $iconSrc = BASE_URL . '/icon/jamkuning.svg';
              $altText = 'Sedang Dipinjam';
              $linkAksi = BASE_URL . '/Menu/Menu PIC/Peminjaman Ruangan/detailPeminjamanRuangan.php?id=' . $idPeminjaman;
              $linkDetail = BASE_URL . '/Menu/Menu PIC/Peminjaman Ruangan/detailPeminjamanRuangan.php?id=' . $idPeminjaman;
              break;
            case 'Ditolak':
              $iconSrc = BASE_URL . '/icon/silang.svg';
              $altText = 'Ditolak';
              $linkAksi = BASE_URL . '/Menu/Menu PIC/Peminjaman Ruangan/detailPeminjamanRuangan.php?id=' . $idPeminjaman;
              $linkDetail = BASE_URL . '/Menu/Menu PIC/Peminjaman Ruangan/detailPeminjamanRuangan.php?id=' . $idPeminjaman;
              break;
            case 'Telah Dikembalikan':
              $iconSrc = BASE_URL . '/icon/centang.svg';
              $altText = 'Telah Dikembalikan';
              $linkAksi = BASE_URL . '/Menu/Menu PIC/Peminjaman Ruangan/detailPeminjamanRuangan.php?id=' . $idPeminjaman;
              $linkDetail = BASE_URL . '/Menu/Menu PIC/Peminjaman Ruangan/detailPeminjamanRuangan.php?id=' . $idPeminjaman;
              break;
            default:
              $iconSrc = BASE_URL . '/icon/jamKuning.svg';
              $altText = 'Status Tidak Diketahui';
              $linkAksi = '#';
              $linkDetail = '#';
              break;
          }
        ?>
          <tr class="<?= $terlambat ? 'table-danger' : '' ?> text-center">
            <td><?= $no ?></td>
            <td class="text-start"><?= htmlspecialchars($row['namaRuangan']) ?></td>
            <td class="text-start"><?= htmlspecialchars($row['namaPeminjam']) ?></td>
            <td>
              <?= ($row['tglPeminjamanRuangan'] instanceof DateTime ? $row['tglPeminjamanRuangan']->format('d M Y') : htmlspecialchars($row['tglPeminjamanRuangan'] ?? '')) ?>
            </td>
            <td><?= ($row['waktuMulai'] instanceof DateTimeInterface) ? $row['waktuMulai']->format('H:i') : 'N/A'; ?></td>
            <td><?= ($row['waktuSelesai'] instanceof DateTimeInterface) ? $row['waktuSelesai']->format('H:i') : 'N/A'; ?></td>
            <td class="td-aksi">
              <a href="<?= $linkAksi ?>">
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
                <td><p><img src="<?= BASE_URL?>/icon/jamhijau.svg" class="legend-icon"> : Menunggu Pengecekan</p></td>
                <td><p><img src="<?= BASE_URL?>/icon/jamkuning.svg" class="legend-icon"> : Sedang Dipinjam</p></td>
                <td><p><img src="<?= BASE_URL?>/icon/jamAbu.svg" class="legend-icon"> : Menunggu Persetujuan</p></td>
            </tr>
        </table>
  <?php
  generatePagination($page, $totalPages);
  ?>
</main>

<?php include '../../../templates/footer.php'; ?>