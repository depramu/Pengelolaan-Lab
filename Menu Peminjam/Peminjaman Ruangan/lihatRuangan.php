<?php
include '../../templates/header.php';
$query = "SELECT idRuangan, namaRuangan, kondisiRuangan, ketersediaan FROM Ruangan WHERE ketersediaan = 'Tersedia'";
$result = sqlsrv_query($conn, $query);

// Fix current page detection to support subfolders
$currentPage = basename($_SERVER['SCRIPT_NAME']); // This will be 'lihatRuangan.php'

if (isset($row['ketersediaan']) && $row['ketersediaan'] === 'Tersedia') {
    $_SESSION['ketersediaan'] = $row['ketersediaan'];
}

include '../../templates/sidebar.php'

?>
<main class="col bg-white px-3 px-md-4 py-3 position-relative">
    <div class="mb-2">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="../dashboardPeminjam.php">Sistem Pengelolaan Lab</a></li>
                <li class="breadcrumb-item"><a href="cekRuangan.php">Cek Ruangan</a></li>
                <li class="breadcrumb-item active" aria-current="page">Lihat Ruangan</li>
            </ol>
        </nav>
    </div>

    <div class="table-responsive">
        <table class="table table-hover align-middle table-bordered">
            <thead class="table-light">
                <tr>
                    <th>ID Ruangan</th>
                    <th>Nama Ruangan</th>
                    <th>Kondisi</th>
                    <th>Ketersediaan</th>
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
                        <td><?= $row['idRuangan'] ?></td>
                        <td><?= $row['namaRuangan'] ?></td>
                        <td><?= $row['kondisiRuangan'] ?></td>
                        <td><?= $row['ketersediaan'] ?></td>
                        <td class="td-aksi text-center">
                            <a href="../../CRUD/Peminjaman/tambahPeminjamanRuangan.php?idRuangan=<?= $row['idRuangan'] ?>"> <img src="../../icon/tandaplus.svg" class="plus-tambah w-25" alt="plus button"></a>

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
include '../../templates/footer.php'
?>