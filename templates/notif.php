<?php

require_once __DIR__ . '/../function/init.php';


// Debugging - tampilkan data session
echo "<!-- Debug Session: ";
print_r($_SESSION);
echo " -->";

if (isset($_POST['notif_id']) && !empty($_POST['notif_id'])) {
    $notif_id = $_POST['notif_id'];
    $query_update = "UPDATE Notifikasi SET status = 'Sudah Dibaca' WHERE id = ?";
    $params_update = array($notif_id);
    $result = sqlsrv_query($conn, $query_update, $params_update);

    if ($result && sqlsrv_rows_affected($result) > 0) {
        if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
            // Jika request AJAX, kembalikan response JSON
            header('Content-Type: application/json');
            echo json_encode(['success' => true]);
            exit;
        }
    }
}

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

    sqlsrv_query($conn, $query_all_read, $params_all_read);
    header('Location: ' . BASE_URL . '/templates/notif.php');
    exit;
}

// Proses update status notifikasi
if (isset($_POST['baca']) && !empty($_POST['notif_id'])) {
    $notif_id = $_POST['notif_id'];
    $query_update = "UPDATE Notifikasi SET status = 'Sudah Dibaca' WHERE id = ?";
    $params_update = array($notif_id);
    sqlsrv_query($conn, $query_update, $params_update);
}

// Ambil data user dari session
$user_role = $_SESSION['user_role'] ?? '';
$nim = $_SESSION['nim'] ?? ''; // Khusus mahasiswa

// Validasi login
if (empty($user_role)) {
    die("<script>alert('Anda belum login!'); window.location='login.php';</script>");
}

// PAGINATION SETUP
$perPage = 7;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
if ($page < 1) $page = 1;

// Query notifikasi berdasarkan role
if ($user_role === 'PIC Aset') {
    $query_base = "SELECT * FROM Notifikasi WHERE untuk IN ('PIC Aset') AND status = 'Belum Dibaca'";
    $params = array();
    $query_count = "SELECT COUNT(*) as total FROM Notifikasi WHERE untuk IN ('PIC Aset') AND status = 'Belum Dibaca'";
} elseif ($user_role === 'Peminjam' && !empty($nim)) {
    $query_base = "SELECT * FROM Notifikasi WHERE untuk = ? AND status = 'Belum Dibaca'";
    $params = array($nim);
    $query_count = "SELECT COUNT(*) as total FROM Notifikasi WHERE untuk = ? AND status = 'Belum Dibaca'";
} else {
    $query_base = "SELECT * FROM Notifikasi WHERE untuk = ? AND status = 'Belum Dibaca'";
    $params = array($user_role);
    $query_count = "SELECT COUNT(*) as total FROM Notifikasi WHERE untuk = ? AND status = 'Belum Dibaca'";
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
$query = $query_base . " ORDER BY waktu DESC OFFSET ? ROWS FETCH NEXT ? ROWS ONLY";
$params_paged = array_merge($params, array($offset, $perPage));

// Debugging query
echo "<!-- Query: $query -->";
echo "<!-- Params: " . print_r($params_paged, true) . " -->";

$stmt = sqlsrv_query($conn, $query, $params_paged);

if ($stmt === false) {
    echo "<!-- Error: " . print_r(sqlsrv_errors(), true) . " -->";
    die("Terjadi kesalahan saat mengambil notifikasi");
}

include __DIR__ . '/header.php';
include __DIR__ . '/sidebar.php';

?>

<!-- Tampilan HTML -->
<main class="col bg-white px-3 px-md-4 py-3 position-relative">
    <h3 class="fw-semibold mb-3">Notifikasi</h3>
    <div class="mb-3">
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
    <div class="d-flex justify-content-end">
        <form id="formSetRead" action="notif.php" method="post" class="mb-3">
            <input type="hidden" name="tandai_semua" value="1">
            <button type="button" class="btn btn-sm btn-primary" id="setAllReadBtn">
                <i class="bi bi-check2-all"></i> Tandai Semua Sudah Dibaca
            </button>
        </form>
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
                ?>
                    <tr class="text-center">
                        <td><?= $no ?></td>
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
                        <td class="text-center status-cell"><?= htmlspecialchars($row['status']) ?></td>
                        <td class="text-center">
                            <?php if ($row['status'] == 'Belum Dibaca'): ?>
                                <form method="POST" onsubmit="return tandaiDibaca(this)">
                                    <input type="hidden" name="notif_id" value="<?= $row['id']; ?>">
                                    <button type="submit" name="baca" class="btn btn-sm btn-outline-success">
                                        <i class="bi bi-check2"></i>
                                    </button>
                                </form>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php $no++;
                endwhile; ?>
                <?php if (!$hasData): ?>
                    <tr>
                        <td colspan="4" class="text-center">Tidak ada notifikasi.</td>
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
?>