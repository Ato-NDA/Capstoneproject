<?php
require_once __DIR__ . '/../includes/config.php';

// Create admin_users table
$create_table_sql = "CREATE TABLE IF NOT EXISTS admin_users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    email VARCHAR(100) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";

if (mysqli_query($conn, $create_table_sql)) {
    echo "Admin users table created successfully\n";
} else {
    echo "Error creating table: " . mysqli_error($conn) . "\n";
}

// Check if default admin exists
$check_admin = "SELECT id FROM admin_users WHERE username = 'admin'";
$result = mysqli_query($conn, $check_admin);

if (mysqli_num_rows($result) == 0) {
    // Create default admin user
    $username = 'admin';
    $password = 'admin123'; // You should change this password after first login
    $email = 'admin@example.com';
    
    $insert_admin = "INSERT INTO admin_users (username, password, email) 
                     VALUES (?, ?, ?)";
    
    $stmt = mysqli_prepare($conn, $insert_admin);
    mysqli_stmt_bind_param($stmt, "sss", $username, $password, $email);
    
    if (mysqli_stmt_execute($stmt)) {
        echo "Default admin user created successfully\n";
        echo "Username: admin\n";
        echo "Password: admin123\n";
        echo "Please change these credentials after first login!\n";
    } else {
        echo "Error creating default admin: " . mysqli_error($conn) . "\n";
    }
}

mysqli_close($conn);
?>
