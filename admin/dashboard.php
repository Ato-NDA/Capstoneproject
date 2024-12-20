<?php
session_start();
require_once('../includes/config.php');

// Check if user is logged in and is admin
if (!isset($_SESSION['user_id']) || !isset($_SESSION['is_admin']) || $_SESSION['is_admin'] !== 1) {
    header("Location: ../login.php");
    exit();
}

// Fetch metrics
$userCount = mysqli_query($conn, "SELECT COUNT(*) as count FROM users")->fetch_assoc()['count'];
$rentalCount = mysqli_query($conn, "SELECT COUNT(*) as count FROM reservations")->fetch_assoc()['count'];
$cameraCount = mysqli_query($conn, "SELECT COUNT(*) as count FROM cameras")->fetch_assoc()['count'];

// Fetch recent activities
$recentActivities = mysqli_query($conn, "
    SELECT r.*, u.username, c.title as camera_title 
    FROM reservations r 
    JOIN users u ON r.user_id = u.id 
    JOIN cameras c ON r.camera_id = c.id 
    ORDER BY r.created_at DESC 
    LIMIT 5
");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Aye's Rental.ph</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <div class="admin-container">
        <!-- Sidebar -->
        <div class="admin-sidebar">
            <h2>Admin Panel</h2>
            <nav class="admin-nav">
                <a href="../index.php" class="admin-nav-item">
                    <i class="fas fa-home"></i> Back to Homepage
                </a>
                <a href="dashboard.php" class="admin-nav-item active">
                    <i class="fas fa-tachometer-alt"></i> Dashboard
                </a>
                <a href="users.php" class="admin-nav-item">
                    <i class="fas fa-users"></i> User Management
                </a>
                <a href="cameras.php" class="admin-nav-item">
                    <i class="fas fa-camera"></i> Camera Management
                </a>
                <a href="rentals.php" class="admin-nav-item">
                    <i class="fas fa-receipt"></i> Rental Management
                </a>
                <a href="settings.php" class="admin-nav-item">
                    <i class="fas fa-cog"></i> Settings
                </a>
                <a href="../logout.php" class="admin-nav-item">
                    <i class="fas fa-sign-out-alt"></i> Logout
                </a>
            </nav>
        </div>

        <!-- Main Content -->
        <div class="admin-content">
            <h1>Dashboard</h1>
            
            <!-- Metrics -->
            <div class="dashboard-grid">
                <div class="dashboard-card">
                    <h3>Total Users</h3>
                    <p class="metric"><?php echo $userCount; ?></p>
                </div>
                <div class="dashboard-card">
                    <h3>Total Rentals</h3>
                    <p class="metric"><?php echo $rentalCount; ?></p>
                </div>
                <div class="dashboard-card">
                    <h3>Available Cameras</h3>
                    <p class="metric"><?php echo $cameraCount; ?></p>
                </div>
            </div>

            <!-- Recent Activities -->
            <h2>Recent Activities</h2>
            <div class="dashboard-card">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>User</th>
                            <th>Camera</th>
                            <th>Date</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while($activity = mysqli_fetch_assoc($recentActivities)): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($activity['username']); ?></td>
                            <td><?php echo htmlspecialchars($activity['camera_title']); ?></td>
                            <td><?php echo date('M d, Y', strtotime($activity['created_at'])); ?></td>
                            <td><span class="status-<?php echo strtolower($activity['status']); ?>"><?php echo $activity['status']; ?></span></td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>
</html>
