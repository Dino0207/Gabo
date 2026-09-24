<?php
session_start();
require_once 'config.php';

if (($_SESSION['role'] ?? '') !== 'admin') {
    header('Location: ../frontend/index.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($conn->query('DELETE FROM parking_logs')) {
        $_SESSION['admin_message'] = 'Parking logs for all users have been cleared.';
    } else {
        $_SESSION['admin_message'] = 'The parking logs could not be cleared.';
    }
}

header('Location: ../frontend/dashboard.php#attendance');
exit();