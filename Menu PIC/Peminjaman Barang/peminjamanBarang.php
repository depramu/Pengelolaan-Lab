<?php
include '../../template/header.php';


// Pagination setup
$currentPage = basename($_SERVER['PHP_SELF']); // Determine the current page
$perPage = 7;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
if ($page < 1) $page = 1;

// Define page groups for active sidebar states
$manajemenAsetPages = ['manajemenBarang.php', 'manajemenRuangan.php', 'tambahBarang.php', 'editBarang.php', 'tambahRuangan.php', 'editRuangan.php'];
$isManajemenAsetActive = in_array($currentPage, $manajemenAsetPages);

$manajemenAkunPages = ['manajemenAkunMhs.php', 'tambahAkunMhs.php', 'editAkunMhs.php', 'manajemenAkunKry.php', 'tambahAkunKry.php', 'editAkunKry.php'];
$isManajemenAkunActive = in_array($currentPage, $manajemenAkunPages);

$peminjamanPages = ['peminjamanBarang.php', 'peminjamanRuangan.php', 'detailPeminjaman.php']; // Added detailPeminjaman.php here
$isPeminjamanActive = in_array($currentPage, $peminjamanPages);

// Hitung total data
$countQuery = "SELECT COUNT(*) AS total FROM Peminjaman_Barang";
$countResult = sqlsrv_query($conn, $countQuery);
$countRow = sqlsrv_fetch_array($countResult, SQLSRV_FETCH_ASSOC);
$totalData = $countRow['total'];
$totalPages = ceil($totalData / $perPage);

// Ambil data sesuai halaman
$offset = ($page - 1) * $perPage;
$query = "SELECT idPeminjamanBrg, idBarang, jumlahBrg, tglPeminjamanBrg, statusPeminjaman FROM Peminjaman_Barang ORDER BY idPeminjamanBrg OFFSET $offset ROWS FETCH NEXT $perPage ROWS ONLY";
$result = sqlsrv_query($conn, $query);

include '../../template/sidebar.php';
?>

      <!-- Content Area -->
      <main class="col bg-white px-4 py-3 position-relative">
        <div class="mb-4">
          <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
              <li class="breadcrumb-item"><a href="dashboardPIC.php">Sistem Pengelolaan Lab</a></li>
              <li class="breadcrumb-item active" aria-current="page">Peminjaman Barang</li>
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
                      <a href="pengajuanBarang.php?id=<?= htmlspecialchars($row['idPeminjamanBrg']); ?>" class="text-secondary" title="Lihat Detail" style="vertical-align: middle;">
                        <i><img src="../../icon/detail.svg" alt="Detail" style="width: 25px; height: 25px; margin-bottom: 7px;"></i>
                      </a>
                    <?php } else if ($statusFromDB == 'Sedang Dipinjam') { ?>
                      <a href="pengembalianBarang.php?id=<?= htmlspecialchars($row['idPeminjamanBrg']); ?>" class="text-secondary" title="Lihat Detail" style="vertical-align: middle;">
                        <i><img src="../../icon/detail.svg" alt="Detail" style="width: 25px; height: 25px; margin-bottom: 7px;"></i>
                      </a>
                    <?php } else if ($statusFromDB == 'Ditolak') { ?>
                      <a href="detailPenolakanBarang.php?id=<?= htmlspecialchars($row['idPeminjamanBrg']); ?>" class="text-secondary" title="Lihat Detail" style="vertical-align: middle;">
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

<?php include '../../template/footer.php'; ?>