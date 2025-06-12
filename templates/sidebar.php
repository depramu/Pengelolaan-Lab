<div class="row flex-grow-1 g-0">
    <nav class="col-auto sidebar d-none d-lg-flex flex-column p-3 ms-lg-4">
        <ul class="nav nav-pills flex-column mb-auto">
            <li class="nav-item mb-2">
                <a href="<?= BASE_URL ?>/Menu Peminjam/dashboardPeminjam.php" class="nav-link ..."><img src="<?= BASE_URL ?>/icon/dashboard0.svg">Dashboard</a>
            </li>
            <li class="nav-item mb-2">
                <a class="nav-link d-flex justify-content-between align-items-center" data-bs-toggle="collapse" href="#peminjamanSubmenu" role="button" aria-expanded="false" aria-controls="peminjamanSubmenu">
                    <span><img src="<?= BASE_URL ?>/icon/peminjaman.svg">Peminjaman</span>
                    <i class="bi bi-chevron-down transition-chevron ps-3"></i>
                </a>
                <div class="collapse ps-4" id="peminjamanSubmenu">
                    <a href="<?= BASE_URL ?>/Menu Peminjam/Peminjaman Barang/cekBarang.php" class="nav-link ...">Barang</a>
                    <a href="<?= BASE_URL ?>/Menu Peminjam/Peminjaman Ruangan/cekRuangan.php" class="nav-link ...">Ruangan</a>
                </div>
            </li>
            <li class="nav-item mb-2">
                <a class="nav-link d-flex justify-content-between align-items-center" data-bs-toggle="collapse" href="#riwayatSubmenu" role="button" aria-expanded="false" aria-controls="riwayatSubmenu">
                    <span><img src="<?= BASE_URL ?>/icon/riwayat.svg" style="width: 28px; height: 28px; object-fit: contain;">Riwayat</span>
                    <i class="bi bi-chevron-down transition-chevron ps-3"></i>
                </a>
                <div class="collapse ps-4" id="riwayatSubmenu">
                    <a href="<?= BASE_URL ?>/Menu Peminjam/Riwayat Barang/riwayatBarang.php" class="nav-link <?php echo ($currentPage == 'riwayatBarang.php') ? 'active-submenu' : ''; ?>">Barang</a>
                    <a href="<?= BASE_URL ?>/Menu Peminjam/Riwayat Ruangan/riwayatRuangan.php" class="nav-link">Ruangan</a>
                </div>
            </li>
            <li class="nav-item mt-0">
                <a href="#" class="nav-link logout" data-bs-toggle="modal" data-bs-target="#logoutModal"><img src="<?= BASE_URL ?>/icon/exit.png">Log Out</a>
            </li>
        </ul>

    </nav>
    <div class="offcanvas offcanvas-start d-lg-none" tabindex="-1" id="offcanvasSidebar" aria-labelledby="offcanvasSidebarLabel">
        <div class="offcanvas-header">
            <h5 class="offcanvas-title" id="offcanvasSidebarLabel">Menu</h5>
            <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        </div>
        <div class="offcanvas-body p-0">
            <nav class="sidebar flex-column p-4 h-100">
                <ul class="nav nav-pills flex-column mb-auto">
                    <li class="nav-item mb-2">
                        <a href="<?= BASE_URL ?>/Menu Peminjam/dashboardPeminjam.php" class="nav-link <?php echo ($currentPage == 'dashboardPeminjam.php') ? 'active' : ''; ?>"><img src="<?= BASE_URL ?>/icon/dashboard0.svg">Dashboard</a>
                    </li>
                    <li class="nav-item mb-2">
                        <a class="nav-link d-flex justify-content-between align-items-center" data-bs-toggle="collapse" href="#peminjamanSubmenuMobile" role="button" aria-expanded="false" aria-controls="peminjamanSubmenuMobile">
                            <span><img src="<?= BASE_URL ?>/icon/peminjaman.svg">Peminjaman</span>
                            <i class="bi bi-chevron-down transition-chevron ps-3"></i>
                        </a>
                        <div class="collapse ps-4" id="peminjamanSubmenuMobile">
                            <a href="<?= BASE_URL ?>/Menu Peminjam/Peminjaman Barang/cekBarang.php" class="nav-link">Barang</a>
                            <a href="<?= BASE_URL ?>/Menu Peminjam/Peminjaman Ruangan/cekRuangan.php" class="nav-link">Ruangan</a>
                        </div>
                    </li>
                    <li class="nav-item mb-2">
                        <a class="nav-link d-flex justify-content-between align-items-center" data-bs-toggle="collapse" href="#riwayatSubmenuMobile" role="button" aria-expanded="false" aria-controls="riwayatSubmenuMobile">
                            <span><img src="<?= BASE_URL ?>/icon/riwayat.svg">Riwayat</span>
                            <i class="bi bi-chevron-down transition-chevron ps-3"></i>
                        </a>
                        <div class="collapse ps-4" id="riwayatSubmenuMobile">
                            <a href="<?= BASE_URL ?>/Menu Peminjam/Riwayat Barang/riwayatBarang.php" class="nav-link">Barang</a>
                            <a href="<?= BASE_URL ?>/Menu Peminjam/Riwayat Ruangan/riwayatRuangan.php" class="nav-link">Ruangan</a>
                        </div>
                    </li>
                    <li class="nav-item mt-0">
                        <a href="#" class="nav-link logout" data-bs-toggle="modal" data-bs-target="#logoutModal"><img src="<?= BASE_URL ?>/icon/exit.png">Log Out</a>
                    </li>
                </ul>
            </nav>
        </div>
    </div>