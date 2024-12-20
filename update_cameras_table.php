<?php
require_once 'includes/config.php';

// Add featured column if it doesn't exist
$check_column = "SELECT COLUMN_NAME 
                 FROM INFORMATION_SCHEMA.COLUMNS 
                 WHERE TABLE_SCHEMA = 'camera_rental' 
                 AND TABLE_NAME = 'cameras' 
                 AND COLUMN_NAME = 'featured'";

$result = mysqli_query($conn, $check_column);

if (mysqli_num_rows($result) == 0) {
    $alter_table = "ALTER TABLE cameras ADD COLUMN featured TINYINT(1) DEFAULT 0";
    if (mysqli_query($conn, $alter_table)) {
        echo "Added featured column successfully\n";
    } else {
        echo "Error adding featured column: " . mysqli_error($conn) . "\n";
    }
} else {
    echo "Featured column already exists\n";
}
?>
