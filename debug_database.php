<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Connect directly to MySQL
$conn = mysqli_connect('localhost', 'root', '');
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
echo "Connected to MySQL server<br>";

// Show all databases
echo "<h3>Available Databases:</h3>";
$result = mysqli_query($conn, "SHOW DATABASES");
while ($row = mysqli_fetch_array($result)) {
    echo $row[0] . "<br>";
}

// Select camera_rental database
if (!mysqli_select_db($conn, 'camera_rental')) {
    die("Could not select database: " . mysqli_error($conn));
}
echo "<br>Selected camera_rental database<br>";

// Show all tables
echo "<h3>Tables in camera_rental:</h3>";
$result = mysqli_query($conn, "SHOW TABLES");
while ($row = mysqli_fetch_array($result)) {
    echo $row[0] . "<br>";
}

// Show users table structure
echo "<h3>Users Table Structure:</h3>";
$result = mysqli_query($conn, "DESCRIBE users");
echo "<table border='1'>";
echo "<tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th><th>Extra</th></tr>";
while ($row = mysqli_fetch_assoc($result)) {
    echo "<tr>";
    foreach ($row as $value) {
        echo "<td>" . $value . "</td>";
    }
    echo "</tr>";
}
echo "</table>";

// Try to insert test user directly
echo "<h3>Inserting Test User:</h3>";
$insert = "INSERT INTO users (username, email, password, is_admin) VALUES ('test', 'test@test.com', 'test123', 0)";
if (mysqli_query($conn, $insert)) {
    echo "Test user inserted successfully<br>";
} else {
    echo "Error inserting test user: " . mysqli_error($conn) . "<br>";
}

// Try to insert your user
echo "<h3>Inserting Your User:</h3>";
$insert = "INSERT INTO users (username, email, password, is_admin) VALUES ('pwetnamalagkit', 'pwetnamalagkit@example.com', 'Asdfghjkl12_', 0)";
if (mysqli_query($conn, $insert)) {
    echo "Your user inserted successfully<br>";
} else {
    echo "Error inserting your user: " . mysqli_error($conn) . "<br>";
}

// Show all users
echo "<h3>All Users in Database:</h3>";
$result = mysqli_query($conn, "SELECT * FROM users");
if ($result && mysqli_num_rows($result) > 0) {
    echo "<table border='1'>";
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
    echo "No users found<br>";
}

// Try to select your user specifically
echo "<h3>Looking for your user:</h3>";
$result = mysqli_query($conn, "SELECT * FROM users WHERE username = 'pwetnamalagkit'");
if ($result && mysqli_num_rows($result) > 0) {
    $user = mysqli_fetch_assoc($result);
    echo "Found user:<br>";
    echo "ID: " . $user['id'] . "<br>";
    echo "Username: " . $user['username'] . "<br>";
    echo "Password: " . $user['password'] . "<br>";
} else {
    echo "Your user not found in database<br>";
}

echo "<br><a href='login.php'>Try logging in</a>";
?>
