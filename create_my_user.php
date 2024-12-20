<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once('includes/config.php');

echo "<h2>Creating User Account</h2>";

// First, check if user already exists
$check_user = mysqli_query($conn, "SELECT * FROM users WHERE username = 'pwetnamalagkit' OR email = 'pwetnamalagkit@example.com'");
if ($check_user && mysqli_num_rows($check_user) > 0) {
    // Delete existing user
    mysqli_query($conn, "DELETE FROM users WHERE username = 'pwetnamalagkit' OR email = 'pwetnamalagkit@example.com'");
    echo "Removed existing user account<br>";
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
    
    // Verify the user was created
    $verify = mysqli_query($conn, "SELECT * FROM users WHERE username = '$username'");
    if ($verify && mysqli_num_rows($verify) > 0) {
        $user = mysqli_fetch_assoc($verify);
        echo "<br>User verified in database:<br>";
        echo "ID: " . $user['id'] . "<br>";
        echo "Username: " . $user['username'] . "<br>";
        echo "Email: " . $user['email'] . "<br>";
        echo "Password: " . $user['password'] . "<br>";
        
        echo "<br><a href='login.php'>Click here to log in</a>";
    }
} else {
    echo "Error creating user account: " . mysqli_error($conn) . "<br>";
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
?>
