<?php
require_once('check_admin.php');

$success_message = '';
$error_message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['update_password'])) {
        $current_password = $_POST['current_password'];
        $new_password = $_POST['new_password'];
        $confirm_password = $_POST['confirm_password'];

        // Get current user's password
        $user_id = $_SESSION['user_id'];
        $result = mysqli_query($conn, "SELECT password FROM users WHERE id = $user_id");
        $user = mysqli_fetch_assoc($result);

        if ($current_password === $user['password']) {
            if ($new_password === $confirm_password) {
                mysqli_query($conn, "UPDATE users SET password = '$new_password' WHERE id = $user_id");
                $success_message = "Password updated successfully!";
            } else {
                $error_message = "New passwords do not match!";
            }
        } else {
            $error_message = "Current password is incorrect!";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Settings - Aye's Rental.ph</title>
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
                <a href="rentals.php" class="admin-nav-item">
                    <i class="fas fa-receipt"></i> Rental Management
                </a>
                <a href="settings.php" class="admin-nav-item active">
                    <i class="fas fa-cog"></i> Settings
                </a>
                <a href="../logout.php" class="admin-nav-item">
                    <i class="fas fa-sign-out-alt"></i> Logout
                </a>
            </nav>
        </div>

        <!-- Main Content -->
        <div class="admin-content">
            <h1>Settings</h1>
            
            <?php if ($success_message): ?>
                <div class="success-message"><?php echo $success_message; ?></div>
            <?php endif; ?>
            
            <?php if ($error_message): ?>
                <div class="error-message"><?php echo $error_message; ?></div>
            <?php endif; ?>

            <div class="dashboard-card">
                <h2>Change Password</h2>
                <form method="POST" class="settings-form">
                    <div class="form-group">
                        <label for="current_password">Current Password</label>
                        <input type="password" id="current_password" name="current_password" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="new_password">New Password</label>
                        <input type="password" id="new_password" name="new_password" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="confirm_password">Confirm New Password</label>
                        <input type="password" id="confirm_password" name="confirm_password" required>
                    </div>
                    
                    <button type="submit" name="update_password">Update Password</button>
                </form>
            </div>
        </div>
    </div>
</body>
</html>
