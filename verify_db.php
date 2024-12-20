<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once('includes/config.php');

echo "<h2>Database Verification</h2>";

// Check database connection
if (!$conn) {
    die("Database connection failed: " . mysqli_connect_error());
}
echo "Database connected successfully<br>";

// Make sure we're using the right database
if (!mysqli_select_db($conn, 'camera_rental')) {
    die("Could not select database: " . mysqli_error($conn));
}
echo "Using database: camera_rental<br>";

// Check users table structure
echo "<h3>Users Table Structure:</h3>";
$structure = mysqli_query($conn, "DESCRIBE users");
if ($structure) {
    echo "<table border='1'><tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th><th>Extra</th></tr>";
    while ($row = mysqli_fetch_assoc($structure)) {
        echo "<tr>";
        foreach ($row as $value) {
            echo "<td>" . htmlspecialchars($value) . "</td>";
        }
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "Error getting table structure: " . mysqli_error($conn) . "<br>";
}

// Show all users
echo "<h3>All Users in Database:</h3>";
$users = mysqli_query($conn, "SELECT * FROM users");
if ($users) {
    if (mysqli_num_rows($users) > 0) {
        echo "<table border='1'>";
        echo "<tr><th>ID</th><th>Username</th><th>Email</th><th>Password</th><th>Is Admin</th><th>Created At</th></tr>";
        while ($user = mysqli_fetch_assoc($users)) {
            echo "<tr>";
            echo "<td>" . htmlspecialchars($user['id']) . "</td>";
            echo "<td>" . htmlspecialchars($user['username']) . "</td>";
            echo "<td>" . htmlspecialchars($user['email']) . "</td>";
            echo "<td>" . htmlspecialchars($user['password']) . "</td>";
            echo "<td>" . ($user['is_admin'] ? 'Yes' : 'No') . "</td>";
            echo "<td>" . htmlspecialchars($user['created_at']) . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "No users found in database<br>";
    }
} else {
    echo "Error querying users: " . mysqli_error($conn) . "<br>";
}

// Try to insert a test user directly
$test_user = "INSERT INTO users (username, email, password, is_admin) 
              VALUES ('test_user', 'test@example.com', 'test123', 0)";
              
if (mysqli_query($conn, $test_user)) {
    echo "<br>Test user created successfully<br>";
} else {
    echo "<br>Error creating test user: " . mysqli_error($conn) . "<br>";
}

// Show the test user
echo "<h3>Verifying Test User:</h3>";
$verify = mysqli_query($conn, "SELECT * FROM users WHERE username = 'test_user'");
if ($verify && mysqli_num_rows($verify) > 0) {
    $user = mysqli_fetch_assoc($verify);
    echo "Found test user:<br>";
    echo "Username: " . htmlspecialchars($user['username']) . "<br>";
    echo "Password: " . htmlspecialchars($user['password']) . "<br>";
} else {
    echo "Test user not found in database<br>";
}
?>
