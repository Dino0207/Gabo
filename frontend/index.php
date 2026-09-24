<?php
session_start();

$error = $_SESSION['login_err'] ?? '';
unset($_SESSION['login_err']);

function showErr($error) {
    return !empty($error) ? "<p class='error'>" . htmlspecialchars($error, ENT_QUOTES, 'UTF-8') . "</p>" : '';
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SmartPark Attendance</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div id="loginScreen">
        <div class="login-card">
            <div class="login-logo">🚗</div>
            <h2>SmartPark Attendance</h2>
            <div class="login-subtitle">Sign in to access the parking dashboard</div>

            <form action="../backend/login.php" method="post">
                <div class="login-field">
                    <?= showErr($error); ?>
                    <label class="login-label" for="loginUsername">Email or student number</label>
                    <input
                        id="loginUsername"
                        type="text"
                        name="username"
                        placeholder="Enter email or student number"
                        autocomplete="username"
                        required
                    >
                    <span class="login-icon">👤</span>
                </div>

                <div class="login-field">
                    <label class="login-label" for="loginPassword">Password</label>
                    <input
                        id="loginPassword"
                        type="password"
                        name="password"
                        placeholder="Enter password"
                        autocomplete="current-password"
                        required
                    >
                    <span
                        class="login-icon"
                        id="togglePassword"
                        title="Show password"
                        style="cursor:pointer"
                    >👁</span>
                </div>

                <div class="login-options">
                    <label>
                        <input type="checkbox" id="rememberMe">
                        Remember me
                    </label>
                    <a href="#" onclick="showForgotPassword(event)">Forgot password?</a>
                </div>

                <button type="submit" class="login-btn" name="login">Login</button>
            </form>

            <div class="auth-switch">
                New to SmartPark? <a href="register.php">Create an account</a>
            </div>

            <div class="login-footer">Smart Parking Attendance • v1.0 Demo</div>
        </div>
    </div>

    <script src="script.js"></script>
</body>
</html>
