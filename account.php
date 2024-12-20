<?php
session_start();
require_once('includes/config.php');

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$success_message = '';
$error_message = '';

// Fetch current user data
$result = mysqli_query($conn, "SELECT * FROM users WHERE id = $user_id");
$user = mysqli_fetch_assoc($result);

// Handle profile update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['update_profile'])) {
        $username = mysqli_real_escape_string($conn, $_POST['username']);
        $email = mysqli_real_escape_string($conn, $_POST['email']);
        $current_password = $_POST['current_password'];
        $new_password = $_POST['new_password'];
        $confirm_password = $_POST['confirm_password'];

        // Check if username is already taken by another user
        $check_username = mysqli_query($conn, "SELECT id FROM users WHERE username = '$username' AND id != $user_id");
        if (mysqli_num_rows($check_username) > 0) {
            $error_message = "Username is already taken!";
        }
        // Check if email is already taken by another user
        else if ($email !== $user['email']) {
            $check_email = mysqli_query($conn, "SELECT id FROM users WHERE email = '$email' AND id != $user_id");
            if (mysqli_num_rows($check_email) > 0) {
                $error_message = "Email is already registered!";
            }
        }
        // If changing password, verify current password
        else if (!empty($new_password) && $current_password !== $user['password']) {
            $error_message = "Current password is incorrect!";
        }
        // If new password is provided, verify it matches confirmation
        else if (!empty($new_password) && $new_password !== $confirm_password) {
            $error_message = "New passwords do not match!";
        }
        else {
            // Update profile
            $update_query = "UPDATE users SET username = '$username', email = '$email'";
            if (!empty($new_password)) {
                $update_query .= ", password = '$new_password'";
            }
            $update_query .= " WHERE id = $user_id";

            if (mysqli_query($conn, $update_query)) {
                $success_message = "Profile updated successfully!";
                // Refresh user data
                $result = mysqli_query($conn, "SELECT * FROM users WHERE id = $user_id");
                $user = mysqli_fetch_assoc($result);
            } else {
                $error_message = "Error updating profile. Please try again.";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Account - Aye's Rental.ph</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <?php include('includes/header.php'); ?>

    <main class="container">
        <div class="account-container">
            <h1>My Account</h1>

            <?php if ($success_message): ?>
                <div class="success-message"><?php echo $success_message; ?></div>
            <?php endif; ?>
            
            <?php if ($error_message): ?>
                <div class="error-message"><?php echo $error_message; ?></div>
            <?php endif; ?>

            <div class="profile-card">
                <div class="profile-header">
                    <i class="fas fa-user-circle"></i>
                    <h2><?php echo htmlspecialchars($user['username']); ?></h2>
                    <p class="member-since">Member since <?php echo date('F Y', strtotime($user['created_at'])); ?></p>
                </div>

                <form method="POST" class="profile-form">
                    <div class="form-group">
                        <label for="username">Username</label>
                        <input type="text" id="username" name="username" value="<?php echo htmlspecialchars($user['username']); ?>" required>
                    </div>

                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>
                    </div>

                    <div class="password-section">
                        <h3>Change Password</h3>
                        <div class="form-group">
                            <label for="current_password">Current Password</label>
                            <input type="password" id="current_password" name="current_password">
                        </div>

                        <div class="form-group">
                            <label for="new_password">New Password</label>
                            <input type="password" id="new_password" name="new_password">
                        </div>

                        <div class="form-group">
                            <label for="confirm_password">Confirm New Password</label>
                            <input type="password" id="confirm_password" name="confirm_password">
                        </div>
                    </div>

                    <div class="form-actions">
                        <button type="submit" name="update_profile" class="btn-primary">Update Profile</button>
                    </div>
                </form>
            </div>
        </div>
    </main>

    <?php include('includes/footer.php'); ?>
</body>
</html>