<?php
require_once '../includes/config.php'; // Include database configuration

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Collect and validate input data
    $id = intval($_POST['id']);
    $name = trim($_POST['name']);
    $description = trim($_POST['description']);
    $brand = trim($_POST['brand']);
    $rental_price = floatval($_POST['rental_price']);
    $stock = intval($_POST['stock']);
    $errors = [];

    if (empty($name) || $rental_price <= 0 || $stock < 0) {
        $errors[] = "Please fill all required fields with valid data.";
    }

    // Handle image uploads
    $uploaded_images = [];
    $upload_dir = '../uploads/equipment/';

    foreach ($_FILES['images']['tmp_name'] as $key => $tmp_name) {
        $file_name = basename($_FILES['images']['name'][$key]);
        $file_size = $_FILES['images']['size'][$key];
        $file_type = mime_content_type($tmp_name);

        if ($file_size > 5000000) {
            $errors[] = "File size must be less than 5MB.";
        }

        if (!in_array($file_type, ['image/jpeg', 'image/png', 'image/gif'])) {
            $errors[] = "Invalid image type. Only JPG, PNG, and GIF are allowed.";
        }

        if (empty($errors)) {
            $target_file = $upload_dir . $file_name;
            if (move_uploaded_file($tmp_name, $target_file)) {
                $uploaded_images[] = $target_file;
            } else {
                $errors[] = "Failed to upload image: $file_name";
            }
        }
    }

    // If no errors, update data in the database
    if (empty($errors)) {
        $images_json = json_encode($uploaded_images);
        $stmt = $conn->prepare("UPDATE equipment SET name=?, description=?, brand=?, rental_price=?, stock=?, images=? WHERE id=?");
        $stmt->bind_param('sssdiss', $name, $description, $brand, $rental_price, $stock, $images_json, $id);

        if ($stmt->execute()) {
            echo "Equipment updated successfully!";
        } else {
            echo "Error: " . $stmt->error;
        }
    } else {
        foreach ($errors as $error) {
            echo "<p>Error: $error</p>";
        }
    }
}
?>
