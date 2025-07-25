<?php
if (!isset($_SESSION['user_id'])) {
    header('Location: ../Login/login.php');
    exit;
}

$currentPage = basename($_SERVER['PHP_SELF']);

// Sesuaikan dashboard dan base url berdasarkan role
$user_role = $_SESSION['user_role'] ?? '';
switch ($user_role) {
    case 'PIC Aset':
        $base_url = BASE_URL . '/Menu/Menu PIC/';
        $dashboard_link = $base_url . 'dashboardPIC.php';
        break;
    case 'KA UPT':
        $base_url = BASE_URL . '/Menu/Menu Ka UPT/';
        $dashboard_link = $base_url . 'dashboardKAUPT.php';
        break;
    default:
        $base_url = BASE_URL . '/Menu/Menu Peminjam/';
        $dashboard_link = $base_url . 'dashboardPeminjam.php';
        break;
}

$notif_count = 0;
$user_role = $_SESSION['user_role'] ?? '';
$nim = $_SESSION['nim'] ?? ''; // Khusus mahasiswa
if ($user_role === 'PIC Aset') {
    $query = "SELECT COUNT(*) as total FROM Notifikasi WHERE untuk IN ('PIC Aset') AND status = 'Belum Dibaca'";
    $params = [];
} elseif ($user_role === 'Peminjam' && !empty($nim)) {
    $query = "SELECT COUNT(*) as total FROM Notifikasi WHERE untuk = ? AND status = 'Belum Dibaca'";
    $params = [$nim];
} else {
    $query = "SELECT COUNT(*) as total FROM Notifikasi WHERE untuk = ? AND status = 'Belum Dibaca'";
    $params = [$user_role];
}

$stmt = sqlsrv_query($conn, $query, $params);
if ($stmt && $row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
    $notif_count = $row['total'];
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Sistem Pengelolaan Laboratorium</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>/style.css">
</head>

<body class="bg-light">
    <div class="container-fluid min-vh-100 d-flex flex-column p-0">
        <header class="d-flex align-items-center justify-content-between px-3 px-md-5 py-2">
            <div class="d-flex align-items-center">
                <a href="<?= htmlspecialchars($dashboard_link) ?>">
                    <img src="<?= BASE_URL ?>/icon/logo0.png" class="sidebar-logo img-fluid" alt="Logo" />
                </a>
            </div>
            <div class="d-flex align-items-center">
                <div class="d-none d-md-block ps-3 ps-md-4" style="margin-right: 1vw;">
                    <div class="d-flex flex-column align-items-end">
                        <span class="fw-semibold fs-5">Halo Sobat,</span>
                    </div>
                    <span class="fw-normal small">
                        <?= htmlspecialchars($_SESSION['user_nama'] ?? 'Pengguna') ?>
                        <?= !empty($_SESSION['user_role']) ? " (" . htmlspecialchars($_SESSION['user_role']) . ")" : '' ?>
                    </span>
                </div>
                <a href="<?= BASE_URL ?>/templates/profil.php" class="me-2">
                    <img src="<?= BASE_URL ?>/icon/vector0.svg" class="profile-img img-fluid" alt="Profil">
                </a>
                <a href="<?= BASE_URL ?>/templates/notif.php" class="me-0 me-2 position-relative">
                    <img src="<?= BASE_URL ?>/icon/bell.png" class="profile-img img-fluid" alt="Notif">
                    <span id="notifBadge" class="position-absolute top-4 translate-middle badge rounded-pill bg-danger" style="<?= $notif_count > 0 ? '' : 'display: none;' ?>">
                        <?= $notif_count ?>
                        <span class="visually-hidden">unread notifications</span>
                    </span>
                </a>
                <button class="btn btn-primary d-lg-none ms-2" type="button" data-bs-toggle="offcanvas" data-bs-target="#offcanvasSidebar" aria-controls="offcanvasSidebar">
                    <i class="bi bi-list"></i>
                </button>
            </div>
        </header>