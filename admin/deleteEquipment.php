<?php
require_once '../includes/config.php'; // Include database configuration

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = intval($_POST['id']);

    // Prepare and execute delete statement
    $stmt = $conn->prepare("DELETE FROM equipment WHERE id=?");
    $stmt->bind_param('i', $id);

    if ($stmt->execute()) {
        echo "Equipment deleted successfully!";
    } else {
        echo "Error: " . $stmt->error;
    }
}
?>
