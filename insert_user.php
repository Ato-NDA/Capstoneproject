<?php
require_once('includes/config.php');

// User details
$username = 'pwetnamalagkit';
$email = 'pwetnamalagkit@example.com';
$password = 'Asdfghjkl12_';
$created_at = date('Y-m-d H:i:s');

// First, make sure we're connected to the right database
if (!mysqli_select_db($conn, 'camera_rental')) {
    die("Could not select database: " . mysqli_error($conn));
}

// Insert the user
$query = "INSERT INTO users (username, email, password, created_at, is_admin) 
          VALUES ('$username', '$email', '$password', '$created_at', 0)";

if (mysqli_query($conn, $query)) {
    echo "User account created successfully!<br>";
    echo "Username: $username<br>";
    echo "Password: $password<br>";
    
    // Verify the user was created
    $check = mysqli_query($conn, "SELECT * FROM users WHERE username = '$username'");
    if ($check && mysqli_num_rows($check) > 0) {
        $user = mysqli_fetch_assoc($check);
        echo "<br>User verified in database:<br>";
        echo "ID: " . $user['id'] . "<br>";
        echo "Username: " . $user['username'] . "<br>";
        echo "Email: " . $user['email'] . "<br>";
    }
} else {
    echo "Error creating user account: " . mysqli_error($conn) . "<br>";
}

// Show all users in the database
echo "<h3>All Users in Database:</h3>";
$result = mysqli_query($conn, "SELECT * FROM users");
if ($result && mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
        echo "ID: " . $row['id'] . "<br>";
        echo "Username: " . $row['username'] . "<br>";
        echo "Email: " . $row['email'] . "<br>";
        echo "Is Admin: " . ($row['is_admin'] ? 'Yes' : 'No') . "<br>";
        echo "Created At: " . $row['created_at'] . "<br><br>";
    }
} else {
    echo "No users found in the database.<br>";
}
?>
