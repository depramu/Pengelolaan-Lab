<?php
include '../../templates/header.php';
$query = "SELECT idPeminjamanRuangan, idRuangan, tglPeminjamanRuangan, waktuMulai, waktuSelesai, statusPeminjaman FROM Peminjaman_Ruangan";
$result = sqlsrv_query($conn, $query);
if ($result === false) {
  echo "Error executing query: <br>";
  die(print_r(sqlsrv_errors(), true));
}
include '../../templates/sidebar.php';
?>
      <!-- Content Area -->
      <main class="col bg-white px-4 py-3 position-relative">
        <div class="mb-4">
          <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
              <li class="breadcrumb-item"><a href="dashboardPIC.php">Sistem Pengelolaan Lab</a></li>
              <li class="breadcrumb-item active" aria-current="page">Peminjaman Ruangan</li>
            </ol>
          </nav>
        </div>

        <!-- Table Peminjaman Barang -->
        <div class="table-responsive">
          <table class="table table-hover align-middle table-bordered">
            <thead class="table-light">
              <tr>
                <th>ID Peminjaman</th>
                <th>ID Ruangan</th>
                <th>Tanggal Peminjaman</th>
                <th>Waktu Mulai</th>
                <th>Waktu Selesai</th>
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
                  <td><?= htmlspecialchars($row['idPeminjamanRuangan']) ?></td>
                  <td><?= htmlspecialchars($row['idRuangan']) ?></td>
                  <td>
                    <?= ($row['tglPeminjamanRuangan'] instanceof DateTimeInterface) ? $row['tglPeminjamanRuangan']->format('D, d M Y') : 'N/A'; ?>
                  </td>
                  <td><?= ($row['waktuMulai'] instanceof DateTimeInterface) ? $row['waktuMulai']->format('H:i') : 'N/A'; ?></td>
                  <td><?= ($row['waktuSelesai'] instanceof DateTimeInterface) ? $row['waktuSelesai']->format('H:i') : 'N/A'; ?></td>
                  <td class="text-center">
                    <?php
                    $statusFromDB = $row['statusPeminjaman'] ?? 'Menunggu Persetujuan';

                    $iconSource = 'bi-hourglass-split';
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
                      if (str_contains($iconSource, '.svg') || str_contains($iconSource, '.png')) {
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
                      <a href="pengajuanRuangan.php?id=<?= htmlspecialchars($row['idPeminjamanRuangan']); ?>" class="text-secondary" title="Lihat Detail" style="vertical-align: middle;">
                        <i><img src="../../icon/detail.svg" alt="Detail" style="width: 25px; height: 25px; margin-bottom: 7px;"></i>
                      </a>
                    <?php } else if ($statusFromDB == 'Sedang Dipinjam') { ?>
                      <a href="pengembalianRuangan.php?id=<?= htmlspecialchars($row['idPeminjamanRuangan']); ?>" class="text-secondary" title="Lihat Detail" style="vertical-align: middle;">
                        <i><img src="../../icon/detail.svg" alt="Detail" style="width: 25px; height: 25px; margin-bottom: 7px;"></i>
                      </a>
                    <?php } else if ($statusFromDB == 'Ditolak') { ?>
                      <a href="detailPenolakanRuangan.php?id=<?= htmlspecialchars($row['idPeminjamanRuangan']); ?>" class="text-secondary" title="Lihat Detail" style="vertical-align: middle;">
                        <i><img src="../../icon/detail.svg" alt="Detail" style="width: 25px; height: 25px; margin-bottom: 7px;"></i>
                      </a>
                    <?php } else if ($statusFromDB == 'Telah Dikembalikan') { ?>
                      <a href="DetailPeminjamanRuangan.php?id=<?= htmlspecialchars($row['idPeminjamanRuangan']); ?>" class="text-secondary" title="Lihat Detail" style="vertical-align: middle;">
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

<?php include '../../templates/footer.php'; ?>