<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once('includes/config.php');

echo "<h2>Database Check</h2>";

// Check database connection
if (!$conn) {
    die("Database connection failed: " . mysqli_connect_error());
}
echo "Database connected successfully<br>";

// Check if we can select the database
if (!mysqli_select_db($conn, 'camera_rental')) {
    die("Could not select database: " . mysqli_error($conn));
}
echo "Database selected successfully<br>";

// Get the user details
$username = 'pwetnamalagkit';
$password = 'Asdfghjkl12_';

// Check if user exists
$query = "SELECT * FROM users WHERE username = '$username'";
echo "Executing query: " . htmlspecialchars($query) . "<br>";

$result = mysqli_query($conn, $query);
if (!$result) {
    die("Query failed: " . mysqli_error($conn));
}

if (mysqli_num_rows($result) > 0) {
    $user = mysqli_fetch_assoc($result);
    echo "<br>User found in database:<br>";
    echo "ID: " . $user['id'] . "<br>";
    echo "Username: " . $user['username'] . "<br>";
    echo "Email: " . $user['email'] . "<br>";
    echo "Password in DB: " . $user['password'] . "<br>";
    echo "Password trying to login with: " . $password . "<br>";
    echo "Password match: " . ($user['password'] === $password ? "YES" : "NO") . "<br>";
} else {
    echo "<br>No user found with username: " . htmlspecialchars($username) . "<br>";
}

// Show all users in database
echo "<h3>All Users in Database:</h3>";
$all_users = mysqli_query($conn, "SELECT * FROM users");
if ($all_users && mysqli_num_rows($all_users) > 0) {
    echo "<table border='1' style='border-collapse: collapse; padding: 5px;'>";
    echo "<tr><th>ID</th><th>Username</th><th>Email</th><th>Password</th><th>Is Admin</th><th>Created At</th></tr>";
    
    while ($row = mysqli_fetch_assoc($all_users)) {
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

// Try to create the user if they don't exist
if (mysqli_num_rows($result) === 0) {
    echo "<br>Attempting to create user...<br>";
    
    $insert_query = "INSERT INTO users (username, email, password, is_admin) 
                     VALUES ('$username', 'pwetnamalagkit@example.com', '$password', 0)";
    
    if (mysqli_query($conn, $insert_query)) {
        echo "User created successfully!<br>";
    } else {
        echo "Error creating user: " . mysqli_error($conn) . "<br>";
    }
}
?>
