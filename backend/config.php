<?php

$host = 'localhost';
$user = 'root';
$password = '';
$database = 'gabo_user_db';

$conn = mysqli_connect($host, $user, $password, $database);

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

mysqli_set_charset($conn, 'utf8mb4');

$conn->query("CREATE TABLE IF NOT EXISTS users (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(120) NOT NULL,
    student_number VARCHAR(40) NOT NULL UNIQUE,
    email VARCHAR(160) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)");

$conn->query("CREATE TABLE IF NOT EXISTS parking_slots (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    slot_code VARCHAR(3) NOT NULL UNIQUE,
    status ENUM('available', 'occupied') NOT NULL DEFAULT 'available',
    user_id INT UNSIGNED NULL,
    occupied_at DATETIME NULL,
    CONSTRAINT fk_slot_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
)");

$conn->query("CREATE TABLE IF NOT EXISTS parking_logs (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    slot_id INT UNSIGNED NOT NULL,
    user_id INT UNSIGNED NOT NULL,
    action ENUM('occupied', 'released') NOT NULL,
    logged_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_log_slot FOREIGN KEY (slot_id) REFERENCES parking_slots(id) ON DELETE CASCADE,
    CONSTRAINT fk_log_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
)");

$slotStatement = $conn->prepare("INSERT IGNORE INTO parking_slots (slot_code) VALUES (?)");
foreach (['A', 'B', 'C', 'D'] as $row) {
    for ($number = 1; $number <= 4; $number++) {
        $slotCode = $row . str_pad((string) $number, 2, '0', STR_PAD_LEFT);
        $slotStatement->bind_param('s', $slotCode);
        $slotStatement->execute();
    }
}
$slotStatement->close();

?>