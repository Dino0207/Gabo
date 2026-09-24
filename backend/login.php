<?php
session_start();
require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    $stmt = $conn->prepare("SELECT * FROM admin WHERE username = ? AND password = ?");
    if (!$stmt) {
        $_SESSION['login_err'] = 'Login is temporarily unavailable. Please try again.';
        header('Location: ../frontend/index.php');
        exit();
    }
    $stmt->bind_param("ss", $username, $password);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        session_regenerate_id(true);
        $_SESSION['username'] = $username;
        $_SESSION['role'] = 'admin';
        header('Location: ../frontend/dashboard.php');
        exit();
    } else {
        $stmt->close();
        $stmt = $conn->prepare("SELECT id, name, student_number, email, password FROM users WHERE email = ? OR student_number = ?");
        $stmt->bind_param("ss", $username, $username);
        $stmt->execute();
        $user = $stmt->get_result()->fetch_assoc();

        if ($user && password_verify($password, $user['password'])) {
            session_regenerate_id(true);
            $_SESSION['user_id'] = (int) $user['id'];
            $_SESSION['user_name'] = $user['name'];
            $_SESSION['student_number'] = $user['student_number'];
            $_SESSION['role'] = 'user';
            header('Location: ../frontend/main.php');
            exit();
        }

        $_SESSION['login_err'] = "Invalid email, student number, or password.";
        header('Location: ../frontend/index.php');
        exit();
    }

    $stmt->close();
}
?>