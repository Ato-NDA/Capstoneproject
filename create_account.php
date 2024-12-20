<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once('includes/config.php');

echo "<h2>Creating User Account</h2>";

// Check database connection
if (!$conn) {
    die("Database connection failed: " . mysqli_connect_error());
}
echo "Database connected successfully<br>";

// Select database
if (!mysqli_select_db($conn, 'camera_rental')) {
    die("Could not select database: " . mysqli_error($conn));
}
echo "Database selected successfully<br>";

// User details
$username = 'pwetnamalagkit';
$email = 'pwetnamalagkit@example.com';
$password = 'Asdfghjkl12_';

// First, check if user exists
$check = mysqli_query($conn, "SELECT * FROM users WHERE username = '$username' OR email = '$email'");
if ($check && mysqli_num_rows($check) > 0) {
    echo "User already exists. Removing existing user...<br>";
    mysqli_query($conn, "DELETE FROM users WHERE username = '$username' OR email = '$email'");
}

// Create user account
$query = "INSERT INTO users (username, email, password, is_admin) VALUES (?, ?, ?, 0)";
$stmt = mysqli_prepare($conn, $query);

if ($stmt) {
    mysqli_stmt_bind_param($stmt, "sss", $username, $email, $password);
    
    if (mysqli_stmt_execute($stmt)) {
        echo "<br>User account created successfully!<br>";
        echo "Username: " . htmlspecialchars($username) . "<br>";
        echo "Password: " . htmlspecialchars($password) . "<br>";
        
        // Verify the user was created
        $verify = mysqli_query($conn, "SELECT * FROM users WHERE username = '$username'");
        if ($verify && mysqli_num_rows($verify) > 0) {
            $user = mysqli_fetch_assoc($verify);
            echo "<br>User verified in database:<br>";
            echo "ID: " . $user['id'] . "<br>";
            echo "Username: " . $user['username'] . "<br>";
            echo "Email: " . $user['email'] . "<br>";
            echo "Password: " . $user['password'] . "<br>";
        }
    } else {
        echo "Error creating user: " . mysqli_stmt_error($stmt) . "<br>";
    }
    mysqli_stmt_close($stmt);
} else {
    echo "Error preparing statement: " . mysqli_error($conn) . "<br>";
}

// Show all users in database
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

echo "<br><a href='login.php'>Click here to login</a>";
?>
