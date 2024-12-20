<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once('includes/config.php');

echo "<h2>Resetting Users Table</h2>";

// First, delete any existing reservations
$drop_reservations = "DELETE FROM reservations";
if (mysqli_query($conn, $drop_reservations)) {
    echo "Existing reservations deleted successfully<br>";
} else {
    echo "Error deleting reservations: " . mysqli_error($conn) . "<br>";
}

// Now we can safely delete users
$delete_users = "DELETE FROM users";
if (mysqli_query($conn, $delete_users)) {
    echo "Existing users deleted successfully<br>";
} else {
    echo "Error deleting users: " . mysqli_error($conn) . "<br>";
    die();
}

// Reset auto-increment
$reset_autoincrement = "ALTER TABLE users AUTO_INCREMENT = 1";
if (mysqli_query($conn, $reset_autoincrement)) {
    echo "Auto-increment reset successfully<br>";
} else {
    echo "Error resetting auto-increment: " . mysqli_error($conn) . "<br>";
}

// Create your user account
$username = 'pwetnamalagkit';
$email = 'pwetnamalagkit@example.com';
$password = 'Asdfghjkl12_';

$insert_user = "INSERT INTO users (username, email, password, is_admin) 
                VALUES ('$username', '$email', '$password', 0)";

if (mysqli_query($conn, $insert_user)) {
    echo "<br>User account created successfully:<br>";
    echo "Username: $username<br>";
    echo "Password: $password<br>";
} else {
    echo "Error creating user account: " . mysqli_error($conn) . "<br>";
}

// Create admin account
$admin_username = 'admin';
$admin_email = 'admin@example.com';
$admin_password = 'admin123';

$insert_admin = "INSERT INTO users (username, email, password, is_admin) 
                 VALUES ('$admin_username', '$admin_email', '$admin_password', 1)";

if (mysqli_query($conn, $insert_admin)) {
    echo "<br>Admin account created successfully:<br>";
    echo "Username: $admin_username<br>";
    echo "Password: $admin_password<br>";
} else {
    echo "Error creating admin account: " . mysqli_error($conn) . "<br>";
}

// Show all users in database
echo "<h3>Current Users in Database:</h3>";
$result = mysqli_query($conn, "SELECT * FROM users");
if ($result && mysqli_num_rows($result) > 0) {
    echo "<table border='1' style='border-collapse: collapse; padding: 5px;'>";
    echo "<tr><th>ID</th><th>Username</th><th>Email</th><th>Password</th><th>Is Admin</th><th>Created At</th></tr>";
    
    while ($row = mysqli_fetch_assoc($result)) {
        echo "<tr>";
        echo "<td>" . $row['id'] . "</td>";
        echo "<td>" . $row['username'] . "</td>";
        echo "<td>" . $row['email'] . "</td>";
        echo "<td>" . $row['password'] . "</td>";
        echo "<td>" . ($row['is_admin'] ? 'Yes' : 'No') . "</td>";
        echo "<td>" . $row['created_at'] . "</td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "No users found in database<br>";
}
?>
