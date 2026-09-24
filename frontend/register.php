<?php
session_start();
require_once '../backend/config.php';
$error = $_SESSION['register_err'] ?? '';
unset($_SESSION['register_err']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create account | SmartPark</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div id="loginScreen">
        <div class="login-card">
            <div class="login-logo">🚗</div>
            <h2>Create your account</h2>
            <div class="login-subtitle">Register to reserve and log your parking slot</div>

            <?php if ($error): ?>
                <p class="error"><?= htmlspecialchars($error) ?></p>
            <?php endif; ?>

            <form action="../backend/register_action.php" method="post">
                <div class="login-field">
                    <label class="login-label" for="name">Full name</label>
                    <input id="name" name="name" type="text" autocomplete="name" required>
                </div>

                <div class="login-field">
                    <label class="login-label" for="student_number">Student number</label>
                    <input id="student_number" name="student_number" type="text" required>
                </div>

                <div class="login-field">
                    <label class="login-label" for="email">Email</label>
                    <input id="email" name="email" type="email" autocomplete="email" required>
                </div>

                <div class="login-field">
                    <label class="login-label" for="password">Password</label>
                    <input id="password" name="password" type="password" autocomplete="new-password" required>
                </div>

                <div class="login-field">
                    <label class="login-label" for="confirm_password">Confirm password</label>
                    <input id="confirm_password" name="confirm_password" type="password" autocomplete="new-password" required>
                </div>

                <button type="submit" class="login-btn">Register</button>
            </form>

            <div class="auth-switch">
                Already registered? <a href="index.php">Sign in</a>
            </div>
        </div>
    </div>
</body>
</html>
