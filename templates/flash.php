<?php
// templates/flash.php
// Sticky flash notification shown at top-center when ?notif=... is present in URL

if (isset($_GET['notif'])) {
    $message = '';
    switch ($_GET['notif']) {
        case 'approve_success':
            $message = 'Peminjaman berhasil disetujui.';
            break;
        case 'reject_success':
            $message = 'Peminjaman berhasil ditolak.';
            break;
        default:
            // Fallback: show the raw value
            $message = htmlspecialchars($_GET['notif']);
    }

    echo '<div class="alert alert-success alert-dismissible fade show position-fixed top-0 start-50 translate-middle-x mt-3" role="alert" style="z-index: 2000; max-width: 90vw;">'
        . htmlspecialchars($message) .
        '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>' .
        '</div>';
}
