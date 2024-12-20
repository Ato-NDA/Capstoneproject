<?php
require_once('check_admin.php');

// Handle rental status updates
if (isset($_POST['update_status'])) {
    $rental_id = $_POST['rental_id'];
    $new_status = $_POST['new_status'];
    mysqli_query($conn, "UPDATE reservations SET status = '$new_status' WHERE id = $rental_id");
}

// Fetch all rentals with user and camera details
$rentals = mysqli_query($conn, "
    SELECT r.*, u.username, c.title as camera_title 
    FROM reservations r 
    JOIN users u ON r.user_id = u.id 
    JOIN cameras c ON r.camera_id = c.id 
    ORDER BY r.created_at DESC
");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rental Management - Aye's Rental.ph</title>
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
                <a href="dashboard.php" class="admin-nav-item">
                    <i class="fas fa-tachometer-alt"></i> Dashboard
                </a>
                <a href="users.php" class="admin-nav-item">
                    <i class="fas fa-users"></i> User Management
                </a>
                <a href="cameras.php" class="admin-nav-item">
                    <i class="fas fa-camera"></i> Camera Management
                </a>
                <a href="rentals.php" class="admin-nav-item active">
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
            <h1>Rental Management</h1>
            
            <div class="dashboard-card">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>User</th>
                            <th>Camera</th>
                            <th>Start Date</th>
                            <th>End Date</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while($rental = mysqli_fetch_assoc($rentals)): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($rental['username']); ?></td>
                            <td><?php echo htmlspecialchars($rental['camera_title']); ?></td>
                            <td><?php echo date('M d, Y', strtotime($rental['start_date'])); ?></td>
                            <td><?php echo date('M d, Y', strtotime($rental['end_date'])); ?></td>
                            <td>
                                <span class="status-<?php echo strtolower($rental['status']); ?>">
                                    <?php echo $rental['status']; ?>
                                </span>
                            </td>
                            <td>
                                <form method="POST" style="display: inline;">
                                    <input type="hidden" name="rental_id" value="<?php echo $rental['id']; ?>">
                                    <select name="new_status" onchange="this.form.submit()">
                                        <option value="">Update Status</option>
                                        <option value="pending">Pending</option>
                                        <option value="confirmed">Confirmed</option>
                                        <option value="completed">Completed</option>
                                        <option value="cancelled">Cancelled</option>
                                    </select>
                                    <input type="hidden" name="update_status" value="1">
                                </form>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>
</html>
