<?php
// Ambil peran pengguna dari session untuk menentukan menu mana yang akan ditampilkan.
$role = $_SESSION['user_role'] ?? '';

// Untuk peminjam: Mahasiswa atau Karyawan tanpa role khusus (PIC Aset/Ka UPT)
$isPeminjam = false;
if ($role === 'Mahasiswa') {
    $isPeminjam = true;
} elseif ($role === 'Karyawan') {
    // Cek apakah user Karyawan TIDAK punya jenisRole (atau kosong/null)
    // Asumsi: $_SESSION['jenisRole'] hanya di-set jika Karyawan punya role khusus
    if (
        !isset($_SESSION['jenisRole']) ||
        $_SESSION['jenisRole'] === '' ||
        is_null($_SESSION['jenisRole'])
    ) {
        $isPeminjam = true;
    }
}
?>


<div class="row flex-grow-1 g-0">
    <nav class="col-auto sidebar d-none d-lg-flex flex-column p-3 ms-lg-4">
        <ul class="nav nav-pills flex-column mb-auto">

            <?php
            if ($role === 'PIC Aset') :
            ?>
                <li class="nav-item mb-2">
                    <a href="<?= BASE_URL ?>/Menu PIC/dashboardPIC.php" class="nav-link <?= ($currentPage == 'dashboardPIC.php') ? 'active' : ''; ?>"><img src="<?= BASE_URL ?>/icon/dashboard0.svg">Dashboard</a>
                </li>
                <li class="nav-item mb-2">
                    <a class="nav-link d-flex justify-content-between align-items-center" data-bs-toggle="collapse" href="#asetSubmenu" role="button" aria-expanded="false" aria-controls="asetSubmenu">
                        <span><img src="<?= BASE_URL ?>/icon/layers0.png">Manajemen Aset</span>
                        <i class="bi bi-chevron-down transition-chevron ps-3"></i>
                    </a>
                    <div class="collapse ps-4" id="asetSubmenu">
                        <a href="<?= BASE_URL ?>/Menu PIC/manajemenBarang.php" class="nav-link">Barang</a>
                        <a href="<?= BASE_URL ?>/Menu PIC/manajemenRuangan.php" class="nav-link">Ruangan</a>
                    </div>
                </li>
                <li class="nav-item mb-2">
                    <a class="nav-link d-flex justify-content-between align-items-center" data-bs-toggle="collapse" href="#akunSubmenu" role="button" aria-expanded="false" aria-controls="akunSubmenu">
                        <span><img src="<?= BASE_URL ?>/icon/iconamoon-profile-fill0.svg">Manajemen Akun</span>
                        <i class="bi bi-chevron-down transition-chevron ps-3"></i>
                    </a>
                    <div class="collapse ps-4" id="akunSubmenu">
                        <a href="<?= BASE_URL ?>/Menu PIC/manajemenAkunMhs.php" class="nav-link">Mahasiswa</a>
                        <a href="<?= BASE_URL ?>/Menu PIC/manajemenAkunKry.php" class="nav-link">Karyawan</a>
                    </div>
                </li>
                <li class="nav-item mb-2">
                    <a class="nav-link d-flex justify-content-between align-items-center" data-bs-toggle="collapse" href="#pinjamSubmenu" role="button" aria-expanded="false" aria-controls="pinjamSubmenu">
                        <span><img src="<?= BASE_URL ?>/icon/ic-twotone-sync-alt0.svg">Peminjaman</span>
                        <i class="bi bi-chevron-down transition-chevron ps-3"></i>
                    </a>
                    <div class="collapse ps-4" id="pinjamSubmenu">
                        <a href="<?= BASE_URL ?>/Menu PIC/Peminjaman Barang/peminjamanBarang.php" class="nav-link">Barang</a>
                        <a href="<?= BASE_URL ?>/Menu PIC/Peminjaman Ruangan/peminjamanRuangan.php" class="nav-link">Ruangan</a>
                    </div>
                </li>
                <li class="nav-item mb-2">
                    <a href="<?= BASE_URL ?>/Menu PIC/laporan.php" class="nav-link"><img src="<?= BASE_URL ?>/icon/graph-report0.png" class="sidebar-icon-report">Laporan</a>
                </li>

            <?php
            elseif ($isPeminjam) :
            ?>
                <li class="nav-item mb-2">
                    <a href="<?= BASE_URL ?>/Menu Peminjam/dashboardPeminjam.php" class="nav-link <?= ($currentPage == 'dashboardPeminjam.php') ? 'active' : ''; ?>"><img src="<?= BASE_URL ?>/icon/dashboard0.svg">Dashboard</a>
                </li>
                <li class="nav-item mb-2">
                    <a class="nav-link d-flex justify-content-between align-items-center" data-bs-toggle="collapse" href="#peminjamanSubmenu" role="button" aria-expanded="false" aria-controls="peminjamanSubmenu">
                        <span><img src="<?= BASE_URL ?>/icon/peminjaman.svg">Peminjaman</span>
                        <i class="bi bi-chevron-down transition-chevron ps-3"></i>
                    </a>
                    <div class="collapse ps-4" id="peminjamanSubmenu">
                        <a href="<?= BASE_URL ?>/Menu Peminjam/Peminjaman Barang/cekBarang.php" class="nav-link">Barang</a>
                        <a href="<?= BASE_URL ?>/Menu Peminjam/Peminjaman Ruangan/cekRuangan.php" class="nav-link">Ruangan</a>
                    </div>
                </li>
                <li class="nav-item mb-2">
                    <a class="nav-link d-flex justify-content-between align-items-center" data-bs-toggle="collapse" href="#riwayatSubmenu" role="button" aria-expanded="false" aria-controls="riwayatSubmenu">
                        <span><img src="<?= BASE_URL ?>/icon/riwayat.svg" style="width: 28px; height: 28px; object-fit: contain;">Riwayat</span>
                        <i class="bi bi-chevron-down transition-chevron ps-3"></i>
                    </a>
                    <div class="collapse ps-4" id="riwayatSubmenu">
                        <a href="<?= BASE_URL ?>/Menu Peminjam/Riwayat Barang/riwayatBarang.php" class="nav-link">Barang</a>
                        <a href="<?= BASE_URL ?>/Menu Peminjam/Riwayat Ruangan/riwayatRuangan.php" class="nav-link">Ruangan</a>
                    </div>
                </li>
            <?php
            // Anda bisa menambahkan 'elseif ($role === 'Ka UPT'):' di sini nanti
            endif;
            ?>

            <li class="nav-item mt-auto"> <a href="#" class="nav-link logout" data-bs-toggle="modal" data-bs-target="#logoutModal"><img src="<?= BASE_URL ?>/icon/exit.png">Log Out</a>
            </li>
        </ul>
    </nav>
    <div class="offcanvas offcanvas-start d-lg-none" tabindex="-1" id="offcanvasSidebar" aria-labelledby="offcanvasSidebarLabel">
    </div>