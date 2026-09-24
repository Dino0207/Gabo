<?php
session_start();
require_once '../backend/config.php';

if (($_SESSION['role'] ?? '') !== 'user') {
    header('Location: index.php');
    exit();
}

$userId = (int) $_SESSION['user_id'];
$message = $_SESSION['parking_message'] ?? '';
unset($_SESSION['parking_message']);

$slots = $conn->query(
    "SELECT parking_slots.*, users.name, users.student_number
     FROM parking_slots
     LEFT JOIN users ON users.id = parking_slots.user_id
     ORDER BY slot_code"
)->fetch_all(MYSQLI_ASSOC);

$mySlot = $conn->query(
    "SELECT slot_code
     FROM parking_slots
     WHERE user_id = $userId AND status = 'occupied'
     LIMIT 1"
)->fetch_assoc();

$logsStatement = $conn->prepare(
    "SELECT parking_logs.*, parking_slots.slot_code
     FROM parking_logs
     JOIN parking_slots ON parking_slots.id = parking_logs.slot_id
     WHERE parking_logs.user_id = ?
     ORDER BY logged_at DESC
     LIMIT 10"
);
$logsStatement->bind_param('i', $userId);
$logsStatement->execute();
$logs = $logsStatement->get_result()->fetch_all(MYSQLI_ASSOC);

$availableCount = count(array_filter($slots, fn($slot) => $slot['status'] === 'available'));
$nameParts = preg_split('/\s+/', trim($_SESSION['user_name']));
$profileInitials = strtoupper(
    substr($nameParts[0], 0, 1) . substr($nameParts[count($nameParts) - 1], 0, 1)
);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Parking | SmartPark</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="app">
        <aside class="sidebar">
            <div class="brand">
                <div class="brand-icon">🚗</div>
                <span>SmartPark</span>
            </div>

            <div class="nav-title">My parking</div>
            <nav class="nav">
                <a class="active" href="#">
                    <span>📊</span>
                    <span>Dashboard</span>
                </a>
                <a href="#parking">
                    <span>🅿️</span>
                    <span>Live Slots</span>
                </a>
                <a href="#my-log">
                    <span>📝</span>
                    <span>My Activity</span>
                </a>
            </nav>

            <div class="sidebar-bottom">
                Smart Parking Attendance<br>
                <b>v1.0 Demo</b>
            </div>
        </aside>

        <main>
            <div class="topbar">
                <div>
                    <h1>My Parking Dashboard</h1>
                    <div class="subtitle">Choose an available slot to log your arrival.</div>
                </div>

                <div class="top-actions">
                    <div class="status">● System Online</div>
                    <div class="profile-menu">
                        <button class="avatar profile-toggle" type="button" aria-expanded="false">
                            <?= htmlspecialchars($profileInitials) ?>
                        </button>
                        <div class="profile-dropdown">
                            <strong><?= htmlspecialchars($_SESSION['user_name']) ?></strong>
                            <span><?= htmlspecialchars($_SESSION['student_number']) ?></span>
                            <a href="../backend/logout.php">Log out</a>
                        </div>
                    </div>
                </div>
            </div>

            <?php if ($message): ?>
                <div class="notice"><?= htmlspecialchars($message) ?></div>
            <?php endif; ?>

            <section class="stats">
                <div class="stat">
                    <div class="stat-top">
                        <span class="stat-label">My active slot</span>
                        <div class="stat-icon">🅿️</div>
                    </div>
                    <div class="stat-value stat-value-small">
                        <?= $mySlot ? htmlspecialchars($mySlot['slot_code']) : 'None' ?>
                    </div>
                </div>

                <div class="stat">
                    <div class="stat-top">
                        <span class="stat-label">Available slots</span>
                        <div class="stat-icon">✓</div>
                    </div>
                    <div class="stat-value"><?= $availableCount ?></div>
                </div>

                <div class="stat">
                    <div class="stat-top">
                        <span class="stat-label">My activity logs</span>
                        <div class="stat-icon">↗</div>
                    </div>
                    <div class="stat-value"><?= count($logs) ?></div>
                </div>

                <div class="stat">
                    <div class="stat-top">
                        <span class="stat-label">Account</span>
                        <div class="stat-icon">👤</div>
                    </div>
                    <div class="stat-value stat-value-small">Active</div>
                </div>
            </section>

            <div class="content-grid">
                <section id="parking">
                    <div class="card">
                        <div class="card-header">
                            <div>
                                <h2>Live Parking Slots</h2>
                                <div class="small">Select an available slot to log your arrival.</div>
                            </div>
                        </div>

                        <div class="parking-grid">
                            <?php foreach ($slots as $slot): ?>
                                <?php if ($slot['status'] === 'occupied'): ?>
                                    <div class="slot occupied">
                                        <div class="slot-name"><?= htmlspecialchars($slot['slot_code']) ?></div>
                                        <div class="slot-state">Occupied</div>
                                        <div class="slot-car">
                                            <?= htmlspecialchars($slot['name']) ?><br>
                                            <?= htmlspecialchars($slot['student_number']) ?>
                                        </div>
                                    </div>
                                <?php elseif (!$mySlot): ?>
                                    <form action="../backend/parking_action.php" method="post" class="slot slot-submit free">
                                        <input type="hidden" name="slot_id" value="<?= (int) $slot['id'] ?>">
                                        <button
                                            type="submit"
                                            name="action"
                                            value="occupy"
                                            class="slot-card-button"
                                            aria-label="Occupy <?= htmlspecialchars($slot['slot_code']) ?>"
                                        >
                                            <span class="slot-name"><?= htmlspecialchars($slot['slot_code']) ?></span>
                                            <span class="slot-state">Available</span>
                                        </button>
                                    </form>
                                <?php else: ?>
                                    <div class="slot free">
                                        <div class="slot-name"><?= htmlspecialchars($slot['slot_code']) ?></div>
                                        <div class="slot-state">Available</div>
                                    </div>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </div>

                        <div class="legend">
                            <span><i class="dot free"></i>Available</span>
                            <span><i class="dot occupied"></i>Occupied</span>
                        </div>
                    </div>

                    <div class="card" id="my-log">
                        <div class="card-header">
                            <div>
                                <h2>My Activity</h2>
                                <div class="small">Your recent parking records</div>
                            </div>
                        </div>

                        <div class="attendance-list">
                            <?php foreach ($logs as $log): ?>
                                <div class="attendance-row">
                                    <div>
                                        <div class="person"><?= ucfirst($log['action']) ?> · <?= htmlspecialchars($log['slot_code']) ?></div>
                                        <div class="plate"><?= htmlspecialchars($log['logged_at']) ?></div>
                                    </div>
                                    <span class="badge <?= $log['action'] === 'occupied' ? 'in' : 'out' ?>"><?= ucfirst($log['action']) ?></span>
                                </div>
                            <?php endforeach; ?>

                            <?php if (!$logs): ?>
                                <div class="small">You have no parking activity yet.</div>
                            <?php endif; ?>
                        </div>
                    </div>
                </section>

                <aside>
                    <div class="card">
                        <div class="card-header">
                            <h2>Account</h2>
                        </div>

                        <div class="account-details">
                            <strong><?= htmlspecialchars($_SESSION['user_name']) ?></strong>
                            <span><?= htmlspecialchars($_SESSION['student_number']) ?></span>
                            <a href="../backend/logout.php" class="button-link">Log out</a>
                        </div>
                    </div>

                    <?php if ($mySlot): ?>
                        <div class="card">
                            <div class="card-header">
                                <h2>Leave parking</h2>
                            </div>

                            <p class="small">Release your current slot when you leave.</p>
                            <form action="../backend/parking_action.php" method="post">
                                <button type="submit" name="action" value="release" class="danger full-button">
                                    Release <?= htmlspecialchars($mySlot['slot_code']) ?>
                                </button>
                            </form>
                        </div>
                    <?php endif; ?>
                </aside>
            </div>
        </main>
    </div>

    <script src="script.js"></script>
</body>
</html>
