<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Connect to MySQL server
$conn = mysqli_connect('localhost', 'root', '');
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
echo "Connected to MySQL server successfully<br>";

// Drop the database if it exists
$drop_db = "DROP DATABASE IF EXISTS camera_rental";
if (mysqli_query($conn, $drop_db)) {
    echo "Existing database dropped successfully<br>";
} else {
    echo "Error dropping database: " . mysqli_error($conn) . "<br>";
}

// Create fresh database
$create_db = "CREATE DATABASE camera_rental";
if (mysqli_query($conn, $create_db)) {
    echo "Database created successfully<br>";
} else {
    die("Error creating database: " . mysqli_error($conn));
}

// Select the database
if (!mysqli_select_db($conn, 'camera_rental')) {
    die("Could not select database: " . mysqli_error($conn));
}
echo "Database selected successfully<br>";

// Create users table
$create_users = "CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL,
    email VARCHAR(100) NOT NULL,
    password VARCHAR(255) NOT NULL,
    is_admin TINYINT(1) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY unique_username (username),
    UNIQUE KEY unique_email (email)
)";

if (mysqli_query($conn, $create_users)) {
    echo "Users table created successfully<br>";
} else {
    die("Error creating users table: " . mysqli_error($conn));
}

// Create cameras table
$create_cameras = "CREATE TABLE cameras (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    description TEXT,
    image_url VARCHAR(255),
    price_per_day DECIMAL(10,2) NOT NULL,
    category VARCHAR(50),
    status ENUM('available', 'rented', 'maintenance') DEFAULT 'available',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";

if (mysqli_query($conn, $create_cameras)) {
    echo "Cameras table created successfully<br>";
} else {
    die("Error creating cameras table: " . mysqli_error($conn));
}

// Create reservations table
$create_reservations = "CREATE TABLE reservations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    camera_id INT NOT NULL,
    start_date DATE NOT NULL,
    end_date DATE NOT NULL,
    total_price DECIMAL(10,2) NOT NULL,
    status ENUM('pending', 'confirmed', 'completed', 'cancelled') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id),
    FOREIGN KEY (camera_id) REFERENCES cameras(id)
)";

if (mysqli_query($conn, $create_reservations)) {
    echo "Reservations table created successfully<br>";
} else {
    die("Error creating reservations table: " . mysqli_error($conn));
}

// Create test user account
$username = 'pwetnamalagkit';
$email = 'pwetnamalagkit@example.com';
$password = 'Asdfghjkl12_';

$create_user = "INSERT INTO users (username, email, password, is_admin) 
                VALUES ('$username', '$email', '$password', 0)";

if (mysqli_query($conn, $create_user)) {
    echo "<br>Test user created successfully:<br>";
    echo "Username: $username<br>";
    echo "Password: $password<br>";
} else {
    echo "Error creating test user: " . mysqli_error($conn) . "<br>";
}

// Create admin account
$create_admin = "INSERT INTO users (username, email, password, is_admin) 
                 VALUES ('admin', 'admin@example.com', 'admin123', 1)";

if (mysqli_query($conn, $create_admin)) {
    echo "<br>Admin account created successfully:<br>";
    echo "Username: admin<br>";
    echo "Password: admin123<br>";
} else {
    echo "Error creating admin account: " . mysqli_error($conn) . "<br>";
}

// Show all users
echo "<h3>All Users in Database:</h3>";
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

echo "<br><a href='login.php'>Go to Login Page</a>";
?>
