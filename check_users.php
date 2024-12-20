<?php
require_once('includes/config.php');

// Check if users table exists
$table_check = mysqli_query($conn, "SHOW TABLES LIKE 'users'");
if (mysqli_num_rows($table_check) == 0) {
    echo "Users table does not exist! Creating table...<br>";
    
    // Create users table
    $create_table = "CREATE TABLE users (
        id INT AUTO_INCREMENT PRIMARY KEY,
        username VARCHAR(50) UNIQUE NOT NULL,
        email VARCHAR(100) UNIQUE NOT NULL,
        password VARCHAR(255) NOT NULL,
        is_admin TINYINT(1) DEFAULT 0,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )";
    
    if (mysqli_query($conn, $create_table)) {
        echo "Users table created successfully!<br>";
    } else {
        echo "Error creating users table: " . mysqli_error($conn) . "<br>";
    }
}

// Show all users in the database
echo "<h2>Current Users in Database:</h2>";
$users = mysqli_query($conn, "SELECT id, username, email, is_admin, created_at FROM users");

if ($users && mysqli_num_rows($users) > 0) {
    echo "<table border='1' style='border-collapse: collapse; padding: 5px;'>";
    echo "<tr><th>ID</th><th>Username</th><th>Email</th><th>Is Admin</th><th>Created At</th></tr>";
    
    while ($row = mysqli_fetch_assoc($users)) {
        echo "<tr>";
        echo "<td>" . htmlspecialchars($row['id']) . "</td>";
        echo "<td>" . htmlspecialchars($row['username']) . "</td>";
        echo "<td>" . htmlspecialchars($row['email']) . "</td>";
        echo "<td>" . ($row['is_admin'] ? 'Yes' : 'No') . "</td>";
        echo "<td>" . htmlspecialchars($row['created_at']) . "</td>";
        echo "</tr>";
    }
    
    echo "</table>";
} else {
    echo "No users found in the database.<br>";
}

// Check for specific user
$username = 'pwetnamalagkit';
$check_user = mysqli_query($conn, "SELECT * FROM users WHERE username = '$username'");
if ($check_user && mysqli_num_rows($check_user) > 0) {
    echo "<br>User '$username' exists in the database!";
} else {
    echo "<br>User '$username' not found in the database!";
}
?>
