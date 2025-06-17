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

function renderSidebarMenu($role, $isPeminjam, $currentPage)
{
    // ... (array $submenuFiles biarkan sama) ...
    $submenuFiles = [
        'aset' => ['manajemenBarang.php', 'manajemenRuangan.php'],
        'akun' => ['manajemenAkunMhs.php', 'manajemenAkunKry.php'],
        'pinjam' => ['peminjamanBarang.php', 'peminjamanRuangan.php'],
        'peminjaman' => ['cekBarang.php', 'cekRuangan.php'],
        'riwayat' => ['riwayatBarang.php', 'riwayatRuangan.php']
    ];

    ob_start();
    if ($role === 'PIC Aset') :
        $isAsetActive = in_array($currentPage, $submenuFiles['aset']);
        $isAkunActive = in_array($currentPage, $submenuFiles['akun']);
        $isPinjamActive = in_array($currentPage, $submenuFiles['pinjam']);
?>
        <li class="nav-item mb-2">
            <a href="<?= BASE_URL ?>/Menu PIC/dashboardPIC.php" class="nav-link <?= ($currentPage == 'dashboardPIC.php') ? 'active' : ''; ?>"><img src="<?= BASE_URL ?>/icon/dashboard0.svg" class="sidebar-icon">Dashboard</a>
        </li>
        <li class="nav-item mb-2">
            <a class="nav-link d-flex justify-content-between align-items-center <?= $isAsetActive ? 'active' : '' ?>" data-bs-toggle="collapse" href="#asetSubmenu" role="button" aria-expanded="false" aria-controls="asetSubmenu">
                <span><img src="<?= BASE_URL ?>/icon/layers0.png" class="sidebar-icon">Manajemen Aset</span>
                <i class="bi bi-chevron-down transition-chevron ps-3"></i>
            </a>
            <div class="collapse ps-4" id="asetSubmenu">
                <a href="<?= BASE_URL ?>/Menu PIC/manajemenBarang.php" class="nav-link <?= ($currentPage == 'manajemenBarang.php') ? 'active' : '' ?>">Barang</a>
                <a href="<?= BASE_URL ?>/Menu PIC/manajemenRuangan.php" class="nav-link <?= ($currentPage == 'manajemenRuangan.php') ? 'active' : '' ?>">Ruangan</a>
            </div>
        </li>
        <li class="nav-item mb-2">
            <a class="nav-link d-flex justify-content-between align-items-center <?= $isAkunActive ? 'active' : '' ?>" data-bs-toggle="collapse" href="#akunSubmenu" role="button" aria-expanded="false" aria-controls="akunSubmenu">
                <span><img src="<?= BASE_URL ?>/icon/iconamoon-profile-fill0.svg" class="sidebar-icon">Manajemen Akun</span>
                <i class="bi bi-chevron-down transition-chevron ps-3"></i>
            </a>
            <div class="collapse ps-4" id="akunSubmenu">
                <a href="<?= BASE_URL ?>/Menu PIC/manajemenAkunMhs.php" class="nav-link <?= ($currentPage == 'manajemenAkunMhs.php') ? 'active' : '' ?>">Mahasiswa</a>
                <a href="<?= BASE_URL ?>/Menu PIC/manajemenAkunKry.php" class="nav-link <?= ($currentPage == 'manajemenAkunKry.php') ? 'active' : '' ?>">Karyawan</a>
            </div>
        </li>
        <li class="nav-item mb-2">
            <a class="nav-link d-flex justify-content-between align-items-center <?= $isPinjamActive ? 'active' : '' ?>" data-bs-toggle="collapse" href="#pinjamSubmenu" role="button" aria-expanded="false" aria-controls="pinjamSubmenu">
                <span><img src="<?= BASE_URL ?>/icon/ic-twotone-sync-alt0.svg" class="sidebar-icon">Peminjaman</span>
                <i class="bi bi-chevron-down transition-chevron ps-3"></i>
            </a>
            <div class="collapse ps-4" id="pinjamSubmenu">
                <a href="<?= BASE_URL ?>/Menu PIC/Peminjaman Barang/peminjamanBarang.php" class="nav-link <?= ($currentPage == 'peminjamanBarang.php') ? 'active' : '' ?>">Barang</a>
                <a href="<?= BASE_URL ?>/Menu PIC/Peminjaman Ruangan/peminjamanRuangan.php" class="nav-link <?= ($currentPage == 'peminjamanRuangan.php') ? 'active' : '' ?>">Ruangan</a>
            </div>
        </li>
        <li class="nav-item mb-2">
            <a href="<?= BASE_URL ?>/Menu PIC/laporan.php" class="nav-link <?= ($currentPage == 'laporan.php') ? 'active' : '' ?>"><img src="<?= BASE_URL ?>/icon/graph-report0.png" class="sidebar-icon sidebar-icon-report">Laporan</a>
        </li>
    <?php
    elseif ($role === 'KA UPT') :
        $isPinjamActive = in_array($currentPage, $submenuFiles['pinjam']);
    ?>
        <li class="nav-item mb-2">
            <a href="<?= BASE_URL ?>/Menu Ka UPT/dashboardKaUPT.php" class="nav-link <?= ($currentPage == 'dashboardKaUPT.php') ? 'active' : ''; ?>"><img src="<?= BASE_URL ?>/icon/dashboard0.svg" class="sidebar-icon">Dashboard</a>
        </li>
        <li class="nav-item mb-2">
            <a href="<?= BASE_URL ?>/Menu Ka UPT/laporan.php" class="nav-link <?= ($currentPage == 'laporan.php') ? 'active' : '' ?>"><img src="<?= BASE_URL ?>/icon/graph-report0.png" class="sidebar-icon sidebar-icon-report">Laporan</a>
        </li>
    <?php
    elseif ($isPeminjam) :
        $isPeminjamanActive = in_array($currentPage, $submenuFiles['peminjaman']);
        $isRiwayatActive = in_array($currentPage, $submenuFiles['riwayat']);
    ?>
        <li class="nav-item mb-2">
            <a href="<?= BASE_URL ?>/Menu Peminjam/dashboardPeminjam.php" class="nav-link <?= ($currentPage == 'dashboardPeminjam.php') ? 'active' : ''; ?>"><img src="<?= BASE_URL ?>/icon/dashboard0.svg" class="sidebar-icon">Dashboard</a>
        </li>
        <li class="nav-item mb-2">
            <a class="nav-link d-flex justify-content-between align-items-center <?= $isPeminjamanActive ? 'active' : '' ?>" data-bs-toggle="collapse" href="#peminjamanSubmenu" role="button" aria-expanded="false" aria-controls="peminjamanSubmenu">
                <span><img src="<?= BASE_URL ?>/icon/peminjaman.svg" class="sidebar-icon">Peminjaman</span>
                <i class="bi bi-chevron-down transition-chevron ps-3"></i>
            </a>
            <div class="collapse ps-4" id="peminjamanSubmenu">
                <a href="<?= BASE_URL ?>/Menu Peminjam/Peminjaman Barang/cekBarang.php" class="nav-link <?= ($currentPage == 'cekBarang.php') ? 'active' : '' ?>">Barang</a>
                <a href="<?= BASE_URL ?>/Menu Peminjam/Peminjaman Ruangan/cekRuangan.php" class="nav-link <?= ($currentPage == 'cekRuangan.php') ? 'active' : '' ?>">Ruangan</a>
            </div>
        </li>
        <li class="nav-item mb-2">
            <a class="nav-link d-flex justify-content-between align-items-center <?= $isRiwayatActive ? 'active' : '' ?>" data-bs-toggle="collapse" href="#riwayatSubmenu" role="button" aria-expanded="false" aria-controls="riwayatSubmenu">
                <span><img src="<?= BASE_URL ?>/icon/riwayat.svg" class="sidebar-icon" style="width: 28px; height: 28px; object-fit: contain;">Riwayat</span>
                <i class="bi bi-chevron-down transition-chevron ps-3"></i>
            </a>
            <div class="collapse ps-4" id="riwayatSubmenu">
                <a href="<?= BASE_URL ?>/Menu Peminjam/Riwayat Barang/riwayatBarang.php" class="nav-link <?= ($currentPage == 'riwayatBarang.php') ? 'active' : '' ?>">Barang</a>
                <a href="<?= BASE_URL ?>/Menu Peminjam/Riwayat Ruangan/riwayatRuangan.php" class="nav-link <?= ($currentPage == 'riwayatRuangan.php') ? 'active' : '' ?>">Ruangan</a>
            </div>
        </li>
    <?php
    endif;
    ?>
    <li class="nav-item mt-auto">
        <a href="#" class="nav-link logout" data-bs-toggle="modal" data-bs-target="#logoutModal"><img src="<?= BASE_URL ?>/icon/exit.png" class="sidebar-icon">Log Out</a>
    </li>
<?php
    return ob_get_clean();
}
?>

<style>
    /* Sidebar icon size for consistency */
    .sidebar-icon {
        width: 24px;
        height: 24px;
        object-fit: contain;
        margin-right: 8px;
        vertical-align: middle;
    }

    /* Active state for parent menu (submenu open) */
    .nav-link.active,
    .offcanvas .nav-link.active {
        background: #2563eb !important;
        color: #fff !important;
    }

    /* Active state for submenu item */
    .collapse .nav-link.active,
    .offcanvas .collapse .nav-link.active {
        background: #1d4ed8 !important;
        color: #fff !important;
    }

    /* Offcanvas sidebar custom style for small screen */
    @media (max-width: 991.98px) {
        .offcanvas-start {
            background: linear-gradient(135deg, rgb(18, 99, 180) 60%, #e0e7ef 100%);
            border-right: 1.5px solid #d1d5db;
            min-width: 220px;
            max-width: 85vw;
            box-shadow: 2px 0 16px 0 rgba(0, 0, 0, 0.07);
        }

        .offcanvas-header {
            background: #f1f5f9;
            border-bottom: 1px solid #e5e7eb;
            padding-top: 1.2rem;
            padding-bottom: 1.2rem;
        }

        .offcanvas-title {
            font-weight: 600;
            color: #1e293b;
            letter-spacing: 0.5px;
        }

        .offcanvas-body {
            padding: 0.5rem 0.5rem 1rem 0.5rem !important;
        }

        .offcanvas .nav-link {
            border-radius: 8px;
            margin-bottom: 0.3rem;
            font-size: 1.08rem;
            color: rgb(255, 255, 255);
            transition: background 0.15s, color 0.15s;
            padding: 0.65rem 1rem;
            display: flex;
            align-items: center;
            gap: 0.7rem;
        }

        .offcanvas .nav-link.active,
        .offcanvas .nav-link:active,
        .offcanvas .nav-link:focus {
            background: #2563eb;
            color: #fff !important;
        }

        .offcanvas .collapse .nav-link.active {
            background: #1d4ed8 !important;
            color: #fff !important;
        }

        .offcanvas .nav-link:hover {
            background: rgb(7, 120, 249);
            color: rgb(221, 221, 221);
        }

        .offcanvas .nav-item.mt-auto {
            margin-top: 2.5rem !important;
        }

        .offcanvas .collapse .nav-link {
            padding-left: 2.2rem;
            font-size: 0.98rem;
        }

        .offcanvas .bi-chevron-down {
            font-size: 1.1rem;
            color: rgb(255, 255, 255);
            transition: transform 0.2s;
        }

        .offcanvas .nav-link[aria-expanded="true"] .bi-chevron-down {
            transform: rotate(180deg);
        }

    }
</style>

<div class="row flex-grow-1 g-0">
    <!-- Sidebar untuk layar besar (large screen) -->
    <nav class="col-auto sidebar d-none d-lg-flex flex-column p-3 ms-lg-4">
        <ul class="nav nav-pills flex-column mb-auto">
            <?= renderSidebarMenu($role, $isPeminjam, $currentPage); ?>
        </ul>
    </nav>

    <!-- Sidebar untuk layar kecil (small screen) dengan tampilan lebih menarik -->
    <div class="offcanvas offcanvas-start d-lg-none" tabindex="-1" id="offcanvasSidebar" aria-labelledby="offcanvasSidebarLabel">
        <div class="offcanvas-header">
            <div class="d-flex align-items-center gap-2">
                <h5 class="offcanvas-title mb-0 ms-2" id="offcanvasSidebarLabel">Sistem Pengelolaan Lab</h5>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        </div>
        <div class="offcanvas-body p-0">
            <ul class="nav nav-pills flex-column mb-auto">
                <?= renderSidebarMenu($role, $isPeminjam, $currentPage); ?>
            </ul>
        </div>
    </div>