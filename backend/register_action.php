<?php
session_start();
require_once 'config.php';

$name = trim($_POST['name'] ?? '');
$studentNumber = trim($_POST['student_number'] ?? '');
$email = trim($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';
$confirmPassword = $_POST['confirm_password'] ?? '';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $_SESSION['register_err'] = 'Invalid registration request.';
    header('Location: ../frontend/register.php');
    exit();
}

if (!$name || !$studentNumber || !filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($password) < 6 || $password !== $confirmPassword) {
    $_SESSION['register_err'] = 'Enter valid details and make sure both passwords match (minimum 6 characters).';
    header('Location: ../frontend/register.php');
    exit();
}

$stmt = $conn->prepare('INSERT INTO users (name, student_number, email, password) VALUES (?, ?, ?, ?)');
if (!$stmt) {
    $_SESSION['register_err'] = 'Registration is temporarily unavailable. Please try again.';
    header('Location: ../frontend/register.php');
    exit();
}

$hash = password_hash($password, PASSWORD_DEFAULT);
$stmt->bind_param('ssss', $name, $studentNumber, $email, $hash);
$inserted = false;
$insertErrorCode = 0;
try {
    $inserted = $stmt->execute();
    $insertErrorCode = $stmt->errno;
} catch (mysqli_sql_exception $exception) {
    $insertErrorCode = $exception->getCode();
}

if (!$inserted) {
    if ($insertErrorCode === 1062) {
        $stmt->close();
        $duplicateStmt = $conn->prepare('SELECT email, student_number FROM users WHERE email = ? OR student_number = ?');
        $emailDuplicate = false;
        $studentNumberDuplicate = false;

        if ($duplicateStmt) {
            $duplicateStmt->bind_param('ss', $email, $studentNumber);
            if ($duplicateStmt->execute()) {
                $duplicateStmt->bind_result($duplicateEmail, $duplicateStudentNumber);
                while ($duplicateStmt->fetch()) {
                    $emailDuplicate = $emailDuplicate || $duplicateEmail === $email;
                    $studentNumberDuplicate = $studentNumberDuplicate || $duplicateStudentNumber === $studentNumber;
                }
            }
            $duplicateStmt->close();
        }

        if ($emailDuplicate && $studentNumberDuplicate) {
            $_SESSION['register_err'] = 'That email and student number are already registered.';
        } elseif ($emailDuplicate) {
            $_SESSION['register_err'] = 'That email is already registered.';
        } elseif ($studentNumberDuplicate) {
            $_SESSION['register_err'] = 'That student number is already registered.';
        } else {
            $_SESSION['register_err'] = 'That email or student number is already registered.';
        }
    } else {
        $_SESSION['register_err'] = 'Registration failed. Please try again.';
    }
    header('Location: ../frontend/register.php');
    exit();
}

$stmt->close();
header('Location: ../frontend/index.php');
exit();
