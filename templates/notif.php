<?php

require_once __DIR__ . '/../function/init.php';

include __DIR__ . '/header.php';
include __DIR__ . '/sidebar.php';

// Debugging - tampilkan data session
echo "<!-- Debug Session: ";
print_r($_SESSION);
echo " -->";

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

// Query notifikasi berdasarkan role
if ($user_role === 'PIC Aset') {
    $query = "SELECT * FROM Notifikasi WHERE untuk IN ('PIC Aset') AND status = 'Belum Dibaca' ORDER BY waktu DESC";
    $params = array();
} elseif ($user_role === 'Peminjam' && !empty($nim)) {
    $query = "SELECT * FROM Notifikasi WHERE untuk = ? AND status = 'Belum Dibaca' ORDER BY waktu DESC";
    $params = array($nim);
} else {
    $query = "SELECT * FROM Notifikasi WHERE untuk = ? AND status = 'Belum Dibaca' ORDER BY waktu DESC";
    $params = array($user_role);
}

// Debugging query
echo "<!-- Query: $query -->";
echo "<!-- Params: " . print_r($params, true) . " -->";

$stmt = sqlsrv_query($conn, $query, $params);

if ($stmt === false) {
    echo "<!-- Error: " . print_r(sqlsrv_errors(), true) . " -->";
    die("Terjadi kesalahan saat mengambil notifikasi");
}

// Hitung jumlah notifikasi
$notif_count = 0;
if (sqlsrv_has_rows($stmt)) {
    $query_count = "SELECT COUNT(*) as total FROM ($query) AS temp";
    $stmt_count = sqlsrv_query($conn, $query_count, $params);
    if ($stmt_count && $row = sqlsrv_fetch_array($stmt_count, SQLSRV_FETCH_ASSOC)) {
        $notif_count = $row['total'];
    }
}
?>

<!-- Tampilan HTML -->
<main class="col bg-white px-3 px-md-4 py-3 position-relative">
    <h3 class="fw-semibold mb-3">Notifikasi</h3>
    <div class="mb-5">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?= BASE_URL ?>/Menu PIC/dashboardPIC.php">Sistem Pengelolaan Lab</a></li>
                <li class="breadcrumb-item active" aria-current="page">Notifikasi</li>
            </ol>
        </nav>
    </div>
    <div class="container">
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Waktu</th>
                        <th>Pesan</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)): ?>
                        <tr>
                            <td>
                                <?php
                                if ($row['waktu'] instanceof DateTime) {
                                    echo $row['waktu']->format('d-m-y');
                                } else {
                                    echo htmlspecialchars($row['waktu']);
                                }
                                ?>
                            </td>
                            <td><?= htmlspecialchars($row['pesan']) ?></td>
                            <td><?= htmlspecialchars($row['status']) ?></td>
                            <td>
                                <?php if ($row['status'] == 'Belum Dibaca'): ?>
                                    <form method="POST" style="display:inline;">
                                        <input type="hidden" name="notif_id" value="<?= $row['id']; ?>">
                                        <button type="submit" name="baca" style="background:none; border:none; cursor:pointer;">
                                            <i class="bi bi-check2"></i>
                                        </button>
                                    </form>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</main>


<?php
include 'footer.php';
?>