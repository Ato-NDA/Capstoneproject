<?php
session_start();
require_once '../includes/config.php';

// Check if admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit;
}

// Get statistics
$stats = [
    'cameras' => mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as count FROM cameras"))['count'],
    'users' => mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as count FROM users"))['count'],
    'rentals' => mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as count FROM reservations"))['count']
];

// Get recent cameras
$recent_cameras = mysqli_query($conn, "SELECT * FROM cameras ORDER BY id DESC LIMIT 5");

// Get recent users
$recent_users = mysqli_query($conn, "SELECT * FROM users ORDER BY id DESC LIMIT 5");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Aye's Rental</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="css/admin.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <div class="admin-container">
        <!-- Sidebar -->
        <aside class="admin-sidebar">
            <div class="sidebar-header">
                <h2><i class="fas fa-camera"></i> Aye's Rental</h2>
            </div>
            <nav class="sidebar-nav">
                <a href="index.php" class="nav-item active">
                    <i class="fas fa-dashboard"></i> Dashboard
                </a>
                <a href="cameras.php" class="nav-item">
                    <i class="fas fa-camera"></i> Camera Management
                </a>
                <a href="users.php" class="nav-item">
                    <i class="fas fa-users"></i> User Management
                </a>
                <a href="rentals.php" class="nav-item">
                    <i class="fas fa-calendar"></i> Rental Management
                </a>
                <a href="logout.php" class="nav-item logout">
                    <i class="fas fa-sign-out-alt"></i> Logout
                </a>
            </nav>
        </aside>

        <!-- Main Content -->
        <main class="admin-main">
            <header class="main-header">
                <h1>Welcome, <?php echo htmlspecialchars($_SESSION['admin_username']); ?>!</h1>
                <div class="header-actions">
                    <a href="add_camera.php" class="btn-add">
                        <i class="fas fa-plus"></i> Add New Camera
                    </a>
                </div>
            </header>

            <div class="dashboard-stats">
                <div class="stat-card">
                    <i class="fas fa-camera"></i>
                    <h3>Total Cameras</h3>
                    <p><?php echo $stats['cameras']; ?></p>
                </div>
                <div class="stat-card">
                    <i class="fas fa-users"></i>
                    <h3>Total Users</h3>
                    <p><?php echo $stats['users']; ?></p>
                </div>
                <div class="stat-card">
                    <i class="fas fa-calendar-check"></i>
                    <h3>Total Rentals</h3>
                    <p><?php echo $stats['rentals']; ?></p>
                </div>
            </div>

            <div class="dashboard-grid">
                <!-- Recent Cameras -->
                <div class="dashboard-card">
                    <div class="card-header">
                        <h3>Recent Cameras</h3>
                        <a href="cameras.php" class="view-all">View All</a>
                    </div>
                    <div class="card-content">
                        <table>
                            <thead>
                                <tr>
                                    <th>Model</th>
                                    <th>Brand</th>
                                    <th>Price/Day</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($camera = mysqli_fetch_assoc($recent_cameras)): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($camera['model']); ?></td>
                                    <td><?php echo htmlspecialchars($camera['brand']); ?></td>
                                    <td>$<?php echo number_format($camera['price_per_day'], 2); ?></td>
                                    <td>
                                        <span class="status <?php echo strtolower($camera['status']); ?>">
                                            <?php echo $camera['status']; ?>
                                        </span>
                                    </td>
                                </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Recent Users -->
                <div class="dashboard-card">
                    <div class="card-header">
                        <h3>Recent Users</h3>
                        <a href="users.php" class="view-all">View All</a>
                    </div>
                    <div class="card-content">
                        <table>
                            <thead>
                                <tr>
                                    <th>Username</th>
                                    <th>Email</th>
                                    <th>Join Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($user = mysqli_fetch_assoc($recent_users)): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($user['username']); ?></td>
                                    <td><?php echo htmlspecialchars($user['email']); ?></td>
                                    <td><?php echo date('Y-m-d', strtotime($user['created_at'])); ?></td>
                                </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </main>
    </div>
</body>
</html>
