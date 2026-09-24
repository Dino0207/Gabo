<?php
session_start();
require_once '../backend/config.php';

if (($_SESSION['role'] ?? '') !== 'admin') {
    header('Location: index.php');
    exit();
}

$slots = $conn->query(
    "SELECT parking_slots.*, users.name, users.student_number
     FROM parking_slots
     LEFT JOIN users ON users.id = parking_slots.user_id
     ORDER BY slot_code"
)->fetch_all(MYSQLI_ASSOC);

$logs = $conn->query(
    "SELECT parking_logs.*, parking_slots.slot_code, users.name, users.student_number
     FROM parking_logs
     JOIN parking_slots ON parking_slots.id = parking_logs.slot_id
     JOIN users ON users.id = parking_logs.user_id
     ORDER BY logged_at DESC
     LIMIT 20"
)->fetch_all(MYSQLI_ASSOC);

$occupiedCount = count(array_filter($slots, fn($slot) => $slot['status'] === 'occupied'));
$availableCount = count($slots) - $occupiedCount;
$message = $_SESSION['admin_message'] ?? '';
unset($_SESSION['admin_message']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SmartPark Dashboard</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="app">
        <aside class="sidebar">
            <div class="brand">
                <div class="brand-icon">🚗</div>
                <span>SmartPark</span>
            </div>

            <div class="nav-title">Management</div>
            <nav class="nav">
                <a class="active" href="#">
                    <span>📊</span>
                    <span>Dashboard</span>
                </a>
                <a href="#parking">
                    <span>🅿️</span>
                    <span>Parking Slots</span>
                </a>
                <a href="#attendance">
                    <span>📝</span>
                    <span>Attendance</span>
                </a>
                <a href="#reports">
                    <span>📈</span>
                    <span>Reports</span>
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
                    <h1>Parking Dashboard</h1>
                    <div class="subtitle">Today's attendance and parking overview</div>
                </div>

                <div class="top-actions">
                    <div class="status">● System Online</div>
                    <div class="profile-menu">
                        <button class="avatar profile-toggle" type="button" aria-expanded="false">AD</button>
                        <div class="profile-dropdown">
                            <strong><?= htmlspecialchars($_SESSION['username']) ?></strong>
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
                        <span class="stat-label">Vehicles Inside</span>
                        <div class="stat-icon">🚘</div>
                    </div>
                    <div class="stat-value"><?= $occupiedCount ?></div>
                </div>

                <div class="stat">
                    <div class="stat-top">
                        <span class="stat-label">Available Slots</span>
                        <div class="stat-icon">🅿️</div>
                    </div>
                    <div class="stat-value"><?= $availableCount ?></div>
                </div>

                <div class="stat">
                    <div class="stat-top">
                        <span class="stat-label">Recent Logs</span>
                        <div class="stat-icon">↗</div>
                    </div>
                    <div class="stat-value"><?= count($logs) ?></div>
                </div>

                <div class="stat">
                    <div class="stat-top">
                        <span class="stat-label">Occupancy Rate</span>
                        <div class="stat-icon">✓</div>
                    </div>
                    <div class="stat-value"><?= count($slots) ? round(($occupiedCount / count($slots)) * 100) : 0 ?>%</div>
                </div>
            </section>

            <div class="content-grid">
                <section id="parking">
                    <div class="card">
                        <div class="card-header">
                            <div>
                                <h2>Live Parking Slots</h2>
                                <div class="small">Live occupancy for all A01-D04 parking slots.</div>
                            </div>
                        </div>

                        <div class="parking-grid">
                            <?php foreach ($slots as $slot): ?>
                                <div class="slot <?= $slot['status'] === 'occupied' ? 'occupied' : 'free' ?>">
                                    <div class="slot-name"><?= htmlspecialchars($slot['slot_code']) ?></div>
                                    <div class="slot-state"><?= ucfirst($slot['status']) ?></div>
                                    <?php if ($slot['status'] === 'occupied'): ?>
                                        <div class="slot-car">
                                            <?= htmlspecialchars($slot['name']) ?><br>
                                            <?= htmlspecialchars($slot['student_number']) ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            <?php endforeach; ?>
                        </div>

                        <div class="legend">
                            <span><i class="dot free"></i>Available</span>
                            <span><i class="dot occupied"></i>Occupied</span>
                        </div>
                    </div>

                    <div class="card" id="attendance">
                        <div class="card-header">
                            <div>
                                <h2>Parking Log</h2>
                                <div class="small">User slot occupation and release records</div>
                            </div>

                            <form action="../backend/clear_logs.php" method="post" onsubmit="return confirm('Clear all parking logs for every user?');">
                                <button type="submit" class="danger">Clear all logs</button>
                            </form>
                        </div>

                        <div class="attendance-list">
                            <?php foreach ($logs as $log): ?>
                                <div class="attendance-row">
                                    <div>
                                        <div class="person"><?= htmlspecialchars($log['name']) ?></div>
                                        <div class="plate"><?= htmlspecialchars($log['student_number']) ?> · <?= htmlspecialchars($log['slot_code']) ?></div>
                                    </div>
                                    <span class="badge <?= $log['action'] === 'occupied' ? 'in' : 'out' ?>"><?= ucfirst($log['action']) ?></span>
                                    <span class="small"><?= htmlspecialchars($log['logged_at']) ?></span>
                                </div>
                            <?php endforeach; ?>

                            <?php if (!$logs): ?>
                                <div class="small">No parking activity yet.</div>
                            <?php endif; ?>
                        </div>
                    </div>
                </section>

                <aside>
                    <div class="card">
                        <div class="card-header">
                            <h2>Parking Summary</h2>
                        </div>

                        <div style="display:grid;gap:13px">
                            <div style="display:flex;justify-content:space-between">
                                <span class="small">Total Slots</span>
                                <b><?= count($slots) ?></b>
                            </div>
                            <div style="display:flex;justify-content:space-between">
                                <span class="small">Occupied</span>
                                <b><?= $occupiedCount ?></b>
                            </div>
                            <div style="display:flex;justify-content:space-between">
                                <span class="small">Available</span>
                                <b><?= $availableCount ?></b>
                            </div>
                        </div>
                    </div>

                    <div class="card" id="reports">
                        <div class="card-header">
                            <h2>Today's Activity</h2>
                        </div>

                        <div style="font-size:13px;color:var(--muted);line-height:1.8">
                            <div>🟢 Normal parking activity</div>
                            <div>🔵 User attendance logging enabled</div>
                        </div>
                    </div>
                </aside>
            </div>
        </main>
    </div>

    <script src="script.js"></script>
</body>
</html>
