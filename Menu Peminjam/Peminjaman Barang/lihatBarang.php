<?php
include '../../templates/header.php';

$tglPeminjamanBrg = isset($_POST['tglPeminjamanBrg']) ? $_POST['tglPeminjamanBrg'] : null;
$query = "SELECT idBarang, namaBarang, lokasiBarang, stokBarang FROM Barang WHERE stokBarang > 0";
if ($tglPeminjamanBrg) {
    $query .= " AND tglPeminjamanBrg = '$tglPeminjamanBrg'";
}
$result = sqlsrv_query($conn, $query);

$currentPage = basename($_SERVER['PHP_SELF']); // Determine the current page
$peminjamanPages = ['cekBarang.php', 'cekRuangan.php', 'tambahPeminjamanBrg.php', 'tambahPeminjamanRuangan.php', 'lihatBarang.php', 'lihatRuangan.php'];
$isPeminjamanActive = in_array($currentPage, $peminjamanPages);

include '../../templates/sidebar.php';


?>
<!-- Content Area -->
<main class="col bg-white px-3 px-md-4 py-3 position-relative">
    <div class="mb-4">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="dashboardPeminjam.php">Sistem Pengelolaan Lab</a></li>
                <li class="breadcrumb-item"><a href="cekBarang.php">Cek Barang</a></li>
                <li class="breadcrumb-item active" aria-current="page">Lihat Barang</li>
            </ol>
        </nav>
    </div>

    <div class="table-responsive">
        <table class="table table-hover align-middle table-bordered">
            <thead class="table-light">
                <tr>
                    <th>ID Barang</th>
                    <th>Nama Barang</th>
                    <th>Stok Barang</th>
                    <th>Lokasi Barang</th>
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
                        <td><?= $row['idBarang'] ?></td>
                        <td><?= $row['namaBarang'] ?></td>
                        <td><?= $row['stokBarang'] ?></td>
                        <td><?= $row['lokasiBarang'] ?></td>
                        <td class="text-center">
                            <a href="../../CRUD/Peminjaman/tambahPeminjamanBrg.php?idBarang=<?= $row['idBarang'] ?>"
                                onclick="event.preventDefault(); window.location.href=this.href+'<?= isset($_SESSION['tglPeminjamanBrg']) ? ('&tglPeminjamanBrg=' . urlencode($_SESSION['tglPeminjamanBrg'])) : '' ?>';">
                                <img src="../../icon/tandaplus.svg" class="plus-tambah w-25" alt="plus button">
                            </a>
                        </td>

                    </tr>
                <?php
                }
                if (!$hasData) {
                    echo '<tr><td colspan="5" class="text-center">Tidak ada barang yang tersedia</td></tr>';
                }
                ?>

            </tbody>
        </table>
    </div>
</main>


<?php

include '../../templates/footer.php';

?>