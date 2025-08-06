<?php
require_once __DIR__ . '/../function/init.php';

// Debugging - tampilkan data session
ob_start();

$searchTerm = isset($_GET['search']) ? $_GET['search'] : '';
$filterStatus = isset($_GET['status']) ? $_GET['status'] : '';

// Ambil data user dari session
$user_role = $_SESSION['user_role'] ?? '';
$nim = $_SESSION['nim'] ?? ''; // Khusus mahasiswa

// Handle marking single notification as read
if (isset($_POST['notif_id']) && !empty($_POST['notif_id'])) {
    $notif_id = $_POST['notif_id'];
    $query_update = "UPDATE Notifikasi SET status = 'Sudah Dibaca' WHERE id = ?";
    $params_update = array($notif_id);
    $result = sqlsrv_query($conn, $query_update, $params_update);
    
    // if ($result) {
    //     $_SESSION['notif_success'] = "Notifikasi telah ditandai sebagai sudah dibaca.";
    // } else {
    //     $_SESSION['notif_error'] = "Gagal menandai notifikasi.";
    // }
    header("Location: notif.php");
    exit;
}

// Handle marking all notifications as read
if (isset($_POST['tandai_semua'])) {
    if ($user_role === 'PIC Aset') {
        $query_all_read = "UPDATE Notifikasi SET status = 'Sudah Dibaca' WHERE untuk IN ('PIC Aset') AND status = 'Belum Dibaca'";
        $params_all_read = array();
    } elseif ($user_role === 'Peminjam' && !empty($nim)) {
        $query_all_read = "UPDATE Notifikasi SET status = 'Sudah Dibaca' WHERE untuk = ? AND status = 'Belum Dibaca'";
        $params_all_read = array($nim);
    } else {
        $query_all_read = "UPDATE Notifikasi SET status = 'Sudah Dibaca' WHERE untuk = ? AND status = 'Belum Dibaca'";
        $params_all_read = array($user_role);
    }

    $result = sqlsrv_query($conn, $query_all_read, $params_all_read);
    
    // if ($result) {
    //     $_SESSION['notif_success'] = "Semua notifikasi telah ditandai sebagai sudah dibaca.";
    // } else {
    //     $_SESSION['notif_error'] = "Gagal menandai semua notifikasi.";
    // }
    header("Location: notif.php");
    exit;
}

// Validasi login
if (empty($user_role)) {
    header("Location: login.php");
    exit;
}

// SETELAH semua logika yang mungkin redirect, baru include header
include __DIR__ . '/header.php';
include __DIR__ . '/sidebar.php';

// Tampilkan debugging session
echo "<!-- Debug Session: ";
print_r($_SESSION);
echo " -->";

// PAGINATION SETUP
$perPage = 7;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
if ($page < 1) $page = 1;

// Query notifikasi berdasarkan role (tampilkan semua, baik sudah maupun belum dibaca)
if ($user_role === 'PIC Aset') {
    $query_base = "SELECT * FROM Notifikasi WHERE untuk IN ('PIC Aset')";
    $query_count = "SELECT COUNT(*) as total FROM Notifikasi WHERE untuk IN ('PIC Aset')";
    $params = array();
} elseif ($user_role === 'Peminjam' && !empty($nim)) {
    $query_base = "SELECT * FROM Notifikasi WHERE untuk = ?";
    $query_count = "SELECT COUNT(*) as total FROM Notifikasi WHERE untuk = ?";
    $params = array($nim);
} else {
    $query_base = "SELECT * FROM Notifikasi WHERE untuk = ?";
    $query_count = "SELECT COUNT(*) as total FROM Notifikasi WHERE untuk = ?";
    $params = array($user_role);
}

// Tambahkan filter status notifikasi jika ada
if (!empty($filterStatus)) {
    $query_base .= " AND status = ?";
    $query_count .= " AND status = ?";
    $params[] = $filterStatus;
}

// Tambahkan pencarian jika ada
if (!empty($searchTerm)) {
    $query_base .= " AND pesan LIKE ?";
    $query_count .= " AND pesan LIKE ?";
    $params[] = "%" . $searchTerm . "%";
}

// Hitung total data untuk pagination
$stmt_count = sqlsrv_query($conn, $query_count, $params);
$totalData = 0;
if ($stmt_count && $row = sqlsrv_fetch_array($stmt_count, SQLSRV_FETCH_ASSOC)) {
    $totalData = $row['total'];
}
$totalPages = max(1, ceil($totalData / $perPage));
$offset = ($page - 1) * $perPage;

// Query data notifikasi dengan limit dan offset
$query = $query_base . " ORDER BY CASE WHEN status = 'Belum Dibaca' THEN 0 ELSE 1 END, waktu DESC OFFSET ? ROWS FETCH NEXT ? ROWS ONLY";
$params_paged = array_merge($params, array($offset, $perPage));

// Debugging query
echo "<!-- Query: $query -->";
echo "<!-- Params: " . print_r($params_paged, true) . " -->";

$stmt = sqlsrv_query($conn, $query, $params_paged);

if ($stmt === false) {
    echo "<!-- Error: " . print_r(sqlsrv_errors(), true) . " -->";
    die("Terjadi kesalahan saat mengambil notifikasi");
}
?>

<!-- Tampilan HTML -->
<main class="col bg-white px-3 px-md-4 py-3 position-relative">
    <?php if (isset($_SESSION['notif_success'])): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?= $_SESSION['notif_success']; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        <?php unset($_SESSION['notif_success']); ?>
    <?php endif; ?>
    
    <?php if (isset($_SESSION['notif_error'])): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <?= $_SESSION['notif_error']; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        <?php unset($_SESSION['notif_error']); ?>
    <?php endif; ?>

    <h3 class="fw-semibold mb-3">Notifikasi</h3>

    <div class="mb-4">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <?php
                // Sesuaikan dashboard berdasarkan role
                $base_url = 'Menu/Menu Peminjam/';
                $dashboard_link = $base_url . 'dashboardPeminjam.php';
                if ($user_role === 'PIC Aset') {
                    $base_url = 'Menu/Menu PIC/';
                    $dashboard_link = $base_url . 'dashboardPIC.php';
                } elseif ($user_role === 'KA UPT') {
                    $base_url = 'Menu/Menu KA UPT/';
                    $dashboard_link = $base_url . 'dashboardKAUPT.php';
                }
                ?>
                <li class="breadcrumb-item"><a href="<?= BASE_URL ?>/<?= $dashboard_link ?>">Sistem Pengelolaan Lab</a></li>
                <li class="breadcrumb-item active" aria-current="page">Notifikasi</li>
            </ol>
        </nav>
    </div>

   <div class="d-flex justify-content-end mb-4">
    <div class="d-flex align-items-center gap-3">
        <!-- Filter Status Dropdown -->
        <div class="dropdown">
            <button class="btn btn-outline-secondary dropdown-toggle" type="button" id="dropdownMenuButton1" data-bs-toggle="dropdown" aria-expanded="false">
                <i class="bi bi-funnel"></i> Filter Status
            </button>
            <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton1">
                <li><a class="dropdown-item<?= empty($filterStatus) ? ' active' : '' ?>" href="?search=<?= htmlspecialchars($searchTerm) ?>">Semua Status</a></li>
                <li><hr class="dropdown-divider"></li>
                <li><a class="dropdown-item<?= $filterStatus === 'Sudah Dibaca' ? ' active' : '' ?>" href="?status=Sudah Dibaca&search=<?= htmlspecialchars($searchTerm) ?>">Sudah Dibaca</a></li>
                <li><a class="dropdown-item<?= $filterStatus === 'Belum Dibaca' ? ' active' : '' ?>" href="?status=Belum Dibaca&search=<?= htmlspecialchars($searchTerm) ?>">Belum Dibaca</a></li>
            </ul>
        </div>  
    </div>

    <div class="ms-3">
        <form id="formSetRead" action="notif.php" method="post">
            <input type="hidden" name="tandai_semua" value="1">
            <button type="submit" class="btn btn-primary">
                <i class="bi bi-check2-all me-1"></i> Tandai Semua Sudah Dibaca
            </button>
        </form>
    </div>
</div>

    <div class="table-responsive">
        <table id="notifikasiTable" class="table table-hover align-middle table-bordered">
            <thead class="table-light">
                <tr class="text-center">
                    <th>No</th>
                    <th>Pesan</th>
                    <th>Tanggal</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $no = $offset + 1;
                $hasData = false;
                while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)):
                    $hasData = true;
                    $statusClass = ($row['status'] == 'Sudah Dibaca') ? 'table-success' : '';
                ?>
                    <tr class="<?= $statusClass ?>">
                        <td class="text-center"><?= $no ?></td>
                        <td class="text-start"><?= htmlspecialchars($row['pesan']) ?></td>
                        <td class="text-center">
                            <?php
                            if ($row['waktu'] instanceof DateTime) {
                                echo $row['waktu']->format('d M Y');
                            } else {
                                echo htmlspecialchars($row['waktu']);
                            }
                            ?>
                        </td>
                        <td class="text-center"><?= htmlspecialchars($row['status']) ?></td>
                        <td class="text-center">
                            <?php if ($row['status'] == 'Belum Dibaca'): ?>
                                <form method="POST">
                                    <input type="hidden" name="notif_id" value="<?= $row['id']; ?>">
                                    <button type="submit" class="btn btn-sm btn-outline-success">
                                        <i class="bi bi-check2"></i>
                                    </button>
                                </form>
                                <?php else: ?>
                                <span class="text-success"><i class="bi bi-check2"></i></span>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php $no++; endwhile; ?>
                <?php if (!$hasData): ?>
                    <tr>
                        <td colspan="5" class="text-center">Tidak ada notifikasi.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    
    <!-- Pagination -->
    <?php
    generatePagination($page, $totalPages);
    ?>
</main>

<?php
include 'footer.php';
ob_end_flush();
?>