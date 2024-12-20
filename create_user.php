<?php
require_once('includes/config.php');

// Check if users table exists
$table_check = mysqli_query($conn, "SHOW TABLES LIKE 'users'");
if (mysqli_num_rows($table_check) == 0) {
    echo "Creating users table...<br>";
    
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
        exit;
    }
}

// Create user account
$username = 'pwetnamalagkit';
$email = 'pwetnamalagkit@example.com';
$password = 'Asdfghjkl12_';
$created_at = date('Y-m-d H:i:s');

// Check if user already exists
$check_user = mysqli_query($conn, "SELECT id FROM users WHERE username = '$username'");
if (mysqli_num_rows($check_user) > 0) {
    echo "User already exists!<br>";
} else {
    // Insert new user
    $query = "INSERT INTO users (username, email, password, created_at, is_admin) 
              VALUES ('$username', '$email', '$password', '$created_at', 0)";
    
    if (mysqli_query($conn, $query)) {
        echo "User account created successfully!<br>";
        echo "Username: $username<br>";
        echo "Password: $password<br>";
    } else {
        echo "Error creating user account: " . mysqli_error($conn) . "<br>";
    }
}

// Show all users
echo "<h2>Current Users:</h2>";
$users = mysqli_query($conn, "SELECT id, username, email, is_admin, created_at FROM users");
if ($users && mysqli_num_rows($users) > 0) {
    while ($row = mysqli_fetch_assoc($users)) {
        echo "ID: " . $row['id'] . "<br>";
        echo "Username: " . $row['username'] . "<br>";
        echo "Email: " . $row['email'] . "<br>";
        echo "Is Admin: " . ($row['is_admin'] ? 'Yes' : 'No') . "<br>";
        echo "Created At: " . $row['created_at'] . "<br><br>";
    }
} else {
    echo "No users found in database.<br>";
}
?>
