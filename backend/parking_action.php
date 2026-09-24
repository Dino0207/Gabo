<?php
session_start();
require_once 'config.php';

if (($_SESSION['role'] ?? '') !== 'user') {
    header('Location: ../frontend/index.php');
    exit();
}

$userId = (int) $_SESSION['user_id'];
$action = $_POST['action'] ?? '';
$conn->begin_transaction();

if ($action === 'occupy') {
    $slotId = (int) ($_POST['slot_id'] ?? 0);
    $stmt = $conn->prepare("UPDATE parking_slots SET status = 'occupied', user_id = ?, occupied_at = NOW() WHERE id = ? AND status = 'available' AND NOT EXISTS (SELECT 1 FROM (SELECT id FROM parking_slots WHERE user_id = ? AND status = 'occupied') AS active_slot)");
    $stmt->bind_param('iii', $userId, $slotId, $userId);
    $stmt->execute();
    if ($stmt->affected_rows === 1) {
        $log = $conn->prepare("INSERT INTO parking_logs (slot_id, user_id, action) VALUES (?, ?, 'occupied')");
        $log->bind_param('ii', $slotId, $userId);
        $log->execute();
        $_SESSION['parking_message'] = 'Your name and student number are now shown on the selected slot.';
    } else {
        $_SESSION['parking_message'] = 'That slot is no longer available, or you already occupy a slot.';
    }
} elseif ($action === 'release') {
    $slot = $conn->prepare("SELECT id FROM parking_slots WHERE user_id = ? AND status = 'occupied' LIMIT 1");
    $slot->bind_param('i', $userId);
    $slot->execute();
    $slotData = $slot->get_result()->fetch_assoc();
    if ($slotData) {
        $slotId = (int) $slotData['id'];
        $update = $conn->prepare("UPDATE parking_slots SET status = 'available', user_id = NULL, occupied_at = NULL WHERE id = ?");
        $update->bind_param('i', $slotId);
        $update->execute();
        $log = $conn->prepare("INSERT INTO parking_logs (slot_id, user_id, action) VALUES (?, ?, 'released')");
        $log->bind_param('ii', $slotId, $userId);
        $log->execute();
        $_SESSION['parking_message'] = 'Your parking slot has been released.';
    }
}

$conn->commit();
header('Location: ../frontend/main.php');
exit();
