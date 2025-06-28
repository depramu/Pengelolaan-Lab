<?php
/**
 * Notification helper
 * Provides add_notif_once(string $message) to push a notification only if
 * an identical unread one is not already present in $_SESSION['notifikasi'].
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function add_notif_once(string $message): void
{
    if (!isset($_SESSION['notifikasi'])) {
        $_SESSION['notifikasi'] = [];
    }
    // Check duplicates among UNREAD notifications
    foreach ($_SESSION['notifikasi'] as $notif) {
        if ($notif['pesan'] === $message && $notif['status'] === 'Belum Dibaca') {
            return; // already recorded
        }
    }

    $_SESSION['notifikasi'][] = [
        'id'     => uniqid(),
        'waktu'  => date('d-m-Y H:i'),
        'pesan'  => $message,
        'status' => 'Belum Dibaca'
    ];
}
