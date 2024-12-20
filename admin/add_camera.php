<?php
session_start();
require_once('../includes/config.php');

// Check if user is logged in and is admin
if (!isset($_SESSION['user_id']) || !isset($_SESSION['is_admin']) || $_SESSION['is_admin'] !== 1) {
    header("Location: ../login.php");
    exit();
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get form data
    $title = trim(mysqli_real_escape_string($conn, $_POST['title']));
    $description = trim(mysqli_real_escape_string($conn, $_POST['description']));
    $price = filter_var($_POST['price'], FILTER_VALIDATE_FLOAT);
    $category = trim(mysqli_real_escape_string($conn, $_POST['category']));
    $status = trim(mysqli_real_escape_string($conn, $_POST['status']));
    $featured = isset($_POST['featured']) ? 1 : 0;

    // Validate input
    if (empty($title)) {
        $error = "Camera title is required";
    } elseif (empty($description)) {
        $error = "Camera description is required";
    } elseif ($price === false || $price <= 0) {
        $error = "Please enter a valid price";
    } elseif (empty($category)) {
        $error = "Category is required";
    } elseif (!in_array($status, ['available', 'unavailable', 'maintenance'])) {
        $error = "Invalid status selected";
    } else {
        // Handle image upload
        $image_url = '';
        if (isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
            $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
            $filename = $_FILES['image']['name'];
            $filesize = $_FILES['image']['size'];
            $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));

            // Additional image validation
            if (!in_array($ext, $allowed)) {
                $error = "Please upload an image file (jpg, jpeg, png, gif, webp)";
            } elseif ($filesize > 5242880) { // 5MB max
                $error = "Image file size must be less than 5MB";
            } else {
                // Validate image dimensions
                list($width, $height) = getimagesize($_FILES['image']['tmp_name']);
                if ($width > 4096 || $height > 4096) {
                    $error = "Image dimensions must not exceed 4096x4096 pixels";
                } else {
                    // Create upload directory if it doesn't exist
                    $upload_dir = '../uploads/cameras/';
                    if (!file_exists($upload_dir)) {
                        if (!mkdir($upload_dir, 0777, true)) {
                            $error = "Failed to create upload directory";
                        }
                    }

                    if (empty($error)) {
                        // Generate unique filename
                        $new_filename = uniqid('camera_') . '.' . $ext;
                        $destination = $upload_dir . $new_filename;

                        if (!move_uploaded_file($_FILES['image']['tmp_name'], $destination)) {
                            $error = "Error uploading image";
                        } else {
                            $image_url = 'uploads/cameras/' . $new_filename;
                        }
                    }
                }
            }
        }

        if (empty($error)) {
            // Begin transaction
            mysqli_begin_transaction($conn);
            try {
                // Insert camera into database
                $query = "INSERT INTO cameras (title, description, price_per_day, category, status, image_url, featured) 
                         VALUES (?, ?, ?, ?, ?, ?, ?)";
                $stmt = mysqli_prepare($conn, $query);
                mysqli_stmt_bind_param($stmt, "ssdsssi", $title, $description, $price, $category, $status, $image_url, $featured);

                if (!mysqli_stmt_execute($stmt)) {
                    throw new Exception("Error adding camera: " . mysqli_error($conn));
                }

                mysqli_commit($conn);
                $_SESSION['success'] = "Camera added successfully!";
                header("Location: cameras.php");
                exit();
            } catch (Exception $e) {
                mysqli_rollback($conn);
                $error = $e->getMessage();
                
                // Delete uploaded image if database insert failed
                if (!empty($image_url) && file_exists("../$image_url")) {
                    unlink("../$image_url");
                }
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Camera - Aye's Rental.ph</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .form-container {
            max-width: 800px;
            margin: 0 auto;
            padding: 2rem;
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 500;
            color: #333;
        }

        .form-group input[type="text"],
        .form-group input[type="number"],
        .form-group select,
        .form-group textarea {
            width: 100%;
            padding: 0.75rem;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 1rem;
            font-family: inherit;
        }

        .form-group textarea {
            height: 150px;
            resize: vertical;
        }

        .image-preview {
            width: 200px;
            height: 200px;
            border: 2px dashed #ddd;
            border-radius: 4px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-top: 0.5rem;
            overflow: hidden;
        }

        .image-preview img {
            max-width: 100%;
            max-height: 100%;
            object-fit: contain;
        }

        .error-message {
            background: #ffebe6;
            color: #de350b;
            padding: 1rem;
            border-radius: 4px;
            margin-bottom: 1rem;
        }

        .success-message {
            background: #e3fcef;
            color: #00875a;
            padding: 1rem;
            border-radius: 4px;
            margin-bottom: 1rem;
        }
    </style>
</head>
<body>
    <div class="admin-container">
        <!-- Sidebar -->
        <div class="admin-sidebar">
            <h2>Admin Panel</h2>
            <nav class="admin-nav">
                <a href="../index.php" class="admin-nav-item">
                    <i class="fas fa-home"></i> Back to Homepage
                </a>
                <a href="dashboard.php" class="admin-nav-item">
                    <i class="fas fa-tachometer-alt"></i> Dashboard
                </a>
                <a href="users.php" class="admin-nav-item">
                    <i class="fas fa-users"></i> User Management
                </a>
                <a href="cameras.php" class="admin-nav-item active">
                    <i class="fas fa-camera"></i> Camera Management
                </a>
                <a href="rentals.php" class="admin-nav-item">
                    <i class="fas fa-receipt"></i> Rental Management
                </a>
                <a href="settings.php" class="admin-nav-item">
                    <i class="fas fa-cog"></i> Settings
                </a>
                <a href="../logout.php" class="admin-nav-item">
                    <i class="fas fa-sign-out-alt"></i> Logout
                </a>
            </nav>
        </div>

        <!-- Main Content -->
        <div class="admin-content">
            <div class="content-header">
                <h1>Add New Camera</h1>
                <a href="cameras.php" class="btn-secondary">
                    <i class="fas fa-arrow-left"></i> Back to Cameras
                </a>
            </div>

            <?php if ($error): ?>
                <div class="error-message"><?php echo $error; ?></div>
            <?php endif; ?>

            <?php if ($success): ?>
                <div class="success-message"><?php echo $success; ?></div>
            <?php endif; ?>

            <div class="form-container">
                <form method="POST" enctype="multipart/form-data">
                    <div class="form-group">
                        <label for="title">Camera Title*</label>
                        <input type="text" id="title" name="title" required 
                               value="<?php echo isset($_POST['title']) ? htmlspecialchars($_POST['title']) : ''; ?>">
                    </div>

                    <div class="form-group">
                        <label for="description">Description*</label>
                        <textarea id="description" name="description" required><?php echo isset($_POST['description']) ? htmlspecialchars($_POST['description']) : ''; ?></textarea>
                    </div>

                    <div class="form-group">
                        <label for="price">Price per Day (₱)*</label>
                        <input type="number" id="price" name="price" step="0.01" min="0" required 
                               value="<?php echo isset($_POST['price']) ? htmlspecialchars($_POST['price']) : ''; ?>">
                    </div>

                    <div class="form-group">
                        <label for="category">Category*</label>
                        <select id="category" name="category" required>
                            <option value="">Select Category</option>
                            <option value="dslr" <?php echo (isset($_POST['category']) && $_POST['category'] == 'dslr') ? 'selected' : ''; ?>>DSLR</option>
                            <option value="mirrorless" <?php echo (isset($_POST['category']) && $_POST['category'] == 'mirrorless') ? 'selected' : ''; ?>>Mirrorless</option>
                            <option value="video" <?php echo (isset($_POST['category']) && $_POST['category'] == 'video') ? 'selected' : ''; ?>>Video Camera</option>
                            <option value="action" <?php echo (isset($_POST['category']) && $_POST['category'] == 'action') ? 'selected' : ''; ?>>Action Camera</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="status">Status*</label>
                        <select id="status" name="status" required>
                            <option value="available" <?php echo (isset($_POST['status']) && $_POST['status'] == 'available') ? 'selected' : ''; ?>>Available</option>
                            <option value="maintenance" <?php echo (isset($_POST['status']) && $_POST['status'] == 'maintenance') ? 'selected' : ''; ?>>Maintenance</option>
                        </select>
                    </div>

                    <div class="form-group checkbox-group">
                        <input type="checkbox" id="featured" name="featured">
                        <label for="featured">Feature this camera on homepage</label>
                    </div>

                    <div class="form-group">
                        <label for="image">Camera Image</label>
                        <input type="file" id="image" name="image" accept="image/*" onchange="previewImage(this)">
                        <div class="image-preview" id="imagePreview">
                            <span>No image selected</span>
                        </div>
                    </div>

                    <div class="form-group">
                        <button type="submit" class="btn-primary">
                            <i class="fas fa-plus"></i> Add Camera
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function previewImage(input) {
            const preview = document.getElementById('imagePreview');
            preview.innerHTML = '';

            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const img = document.createElement('img');
                    img.src = e.target.result;
                    preview.appendChild(img);
                }
                reader.readAsDataURL(input.files[0]);
            } else {
                preview.innerHTML = '<span>No image selected</span>';
            }
        }
    </script>
</body>
</html>
