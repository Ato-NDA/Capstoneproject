<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once('includes/config.php');

echo "<h2>Database Structure Information</h2>";

// Get all tables
$tables_query = "SHOW TABLES FROM camera_rental";
$tables_result = mysqli_query($conn, $tables_query);

if (!$tables_result) {
    die("Error getting tables: " . mysqli_error($conn));
}

while ($table = mysqli_fetch_row($tables_result)) {
    $table_name = $table[0];
    echo "<h3>Table: $table_name</h3>";
    
    // Get create table statement
    $create_query = "SHOW CREATE TABLE $table_name";
    $create_result = mysqli_query($conn, $create_query);
    
    if ($create_result) {
        $row = mysqli_fetch_assoc($create_result);
        if (isset($row['Create Table'])) {
            echo "<pre>" . htmlspecialchars($row['Create Table']) . "</pre>";
        }
    }
    
    // Get foreign keys referencing users table
    $fk_query = "
        SELECT 
            TABLE_NAME,
            COLUMN_NAME,
            CONSTRAINT_NAME,
            REFERENCED_TABLE_NAME,
            REFERENCED_COLUMN_NAME
        FROM
            information_schema.KEY_COLUMN_USAGE
        WHERE
            REFERENCED_TABLE_SCHEMA = 'camera_rental'
            AND REFERENCED_TABLE_NAME = 'users'
            AND TABLE_NAME = '$table_name'";
            
    $fk_result = mysqli_query($conn, $fk_query);
    
    if ($fk_result && mysqli_num_rows($fk_result) > 0) {
        echo "<p>Foreign Keys referencing users table:</p>";
        while ($fk = mysqli_fetch_assoc($fk_result)) {
            echo "- Table {$fk['TABLE_NAME']} column {$fk['COLUMN_NAME']} references users({$fk['REFERENCED_COLUMN_NAME']}) via constraint {$fk['CONSTRAINT_NAME']}<br>";
        }
    }
}
?>
