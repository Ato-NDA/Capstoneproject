<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h2>Database Connection Test</h2>";

// Database credentials
$db_server = 'localhost';
$db_username = 'root';
$db_password = '';
$db_name = 'camera_rental';

// Create connection
$conn = mysqli_connect($db_server, $db_username, $db_password);

// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
echo "Connected to MySQL server successfully!<br>";

// Create database if not exists
$sql = "CREATE DATABASE IF NOT EXISTS $db_name";
if (mysqli_query($conn, $sql)) {
    echo "Database created/selected successfully!<br>";
} else {
    echo "Error creating database: " . mysqli_error($conn) . "<br>";
}

// Select the database
if (mysqli_select_db($conn, $db_name)) {
    echo "Database '$db_name' selected successfully!<br>";
} else {
    die("Error selecting database: " . mysqli_error($conn));
}

// Create users table
$create_users_table = "CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    is_admin TINYINT(1) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";

if (mysqli_query($conn, $create_users_table)) {
    echo "Users table created successfully!<br>";
} else {
    echo "Error creating users table: " . mysqli_error($conn) . "<br>";
}

// Insert test user
$username = 'pwetnamalagkit';
$email = 'pwetnamalagkit@example.com';
$password = 'Asdfghjkl12_';

// First, delete existing user if exists
mysqli_query($conn, "DELETE FROM users WHERE username = '$username'");

// Insert new user
$insert_user = "INSERT INTO users (username, email, password, is_admin) VALUES ('$username', '$email', '$password', 0)";
if (mysqli_query($conn, $insert_user)) {
    echo "Test user created successfully!<br>";
} else {
    echo "Error creating test user: " . mysqli_error($conn) . "<br>";
}

// Show all users
echo "<h3>Current Users in Database:</h3>";
$result = mysqli_query($conn, "SELECT * FROM users");
if ($result) {
    if (mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            echo "ID: " . $row['id'] . "<br>";
            echo "Username: " . $row['username'] . "<br>";
            echo "Email: " . $row['email'] . "<br>";
            echo "Is Admin: " . $row['is_admin'] . "<br>";
            echo "Created At: " . $row['created_at'] . "<br><br>";
        }
    } else {
        echo "No users found in the database.<br>";
    }
} else {
    echo "Error querying users: " . mysqli_error($conn) . "<br>";
}
?>
