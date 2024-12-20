<?php
session_start();
require_once('../includes/config.php');

// Check if user is logged in and is admin
if (!isset($_SESSION['user_id']) || !isset($_SESSION['is_admin']) || $_SESSION['is_admin'] !== 1) {
    header("Location: ../login.php");
    exit();
}

// Handle camera deletion
if (isset($_POST['delete_camera'])) {
    $camera_id = mysqli_real_escape_string($conn, $_POST['camera_id']);
    $query = "DELETE FROM cameras WHERE id = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "i", $camera_id);
    
    if (mysqli_stmt_execute($stmt)) {
        $_SESSION['success'] = "Camera deleted successfully";
    } else {
        $_SESSION['error'] = "Error deleting camera";
    }
    header("Location: cameras.php");
    exit();
}

// Handle status update
if (isset($_POST['update_status'])) {
    $camera_id = mysqli_real_escape_string($conn, $_POST['camera_id']);
    $status = mysqli_real_escape_string($conn, $_POST['status']);
    
    $query = "UPDATE cameras SET status = ? WHERE id = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "si", $status, $camera_id);
    
    if (mysqli_stmt_execute($stmt)) {
        $_SESSION['success'] = "Status updated successfully";
    } else {
        $_SESSION['error'] = "Error updating status";
    }
    header("Location: cameras.php");
    exit();
}

// Handle featured status update
if (isset($_POST['toggle_featured'])) {
    $camera_id = mysqli_real_escape_string($conn, $_POST['camera_id']);
    $featured = isset($_POST['featured']) ? 1 : 0;
    
    $query = "UPDATE cameras SET featured = ? WHERE id = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "ii", $featured, $camera_id);
    
    if (mysqli_stmt_execute($stmt)) {
        $_SESSION['success'] = "Featured status updated successfully";
    } else {
        $_SESSION['error'] = "Error updating featured status";
    }
    header("Location: cameras.php");
    exit();
}

// Pagination setup
$results_per_page = 10;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $results_per_page;

// Get total number of cameras
$total_query = "SELECT COUNT(*) as total FROM cameras";
$total_result = mysqli_query($conn, $total_query);
$total_cameras = mysqli_fetch_assoc($total_result)['total'];
$total_pages = ceil($total_cameras / $results_per_page);

// Fetch cameras with pagination and rental status
$query = "SELECT c.*, 
          (SELECT COUNT(*) FROM reservations r WHERE r.camera_id = c.id AND r.status = 'confirmed') as active_rentals,
          (SELECT GROUP_CONCAT(r.start_date, ' to ', r.end_date) 
           FROM reservations r 
           WHERE r.camera_id = c.id AND r.status = 'confirmed'
           GROUP BY r.camera_id) as rental_dates
          FROM cameras c 
          ORDER BY c.id DESC 
          LIMIT ? OFFSET ?";
          
$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, "ii", $results_per_page, $offset);
mysqli_stmt_execute($stmt);
$cameras = mysqli_stmt_get_result($stmt);

if (!$cameras) {
    die("Error fetching cameras: " . mysqli_error($conn));
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Camera Management - Aye's Rental.ph</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .camera-thumbnail {
            width: 100px;
            height: 100px;
            object-fit: contain;
            background: #f5f5f5;
            padding: 5px;
            border-radius: 5px;
        }
        
        .status-select {
            padding: 5px 10px;
            border-radius: 5px;
            font-size: 0.9em;
            font-weight: 500;
        }
        .available { background: #e3fcef; color: #00875a; }
        .unavailable { background: #ffebe6; color: #de350b; }
        
        .featured-checkbox {
            margin: 0 5px;
        }
        
        .actions {
            display: flex;
            align-items: center;
        }
        
        .btn-edit {
            padding: 5px 10px;
            border-radius: 5px;
            color: white;
            text-decoration: none;
            background: #0052cc;
            margin: 0 2px;
            cursor: pointer;
        }
        
        .btn-delete {
            padding: 5px 10px;
            border-radius: 5px;
            color: white;
            text-decoration: none;
            background: #de350b;
            border: none;
            cursor: pointer;
        }
        
        .alert {
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 20px;
        }
        .alert-success {
            background: #e3fcef;
            color: #00875a;
        }
        .alert-error {
            background: #ffebe6;
            color: #de350b;
        }
        
        .pagination {
            display: flex;
            justify-content: center;
            margin-top: 20px;
        }
        .pagination a {
            padding: 10px;
            border-radius: 5px;
            margin: 0 5px;
            text-decoration: none;
            color: #0052cc;
        }
        .pagination a.active {
            background: #0052cc;
            color: white;
        }
        
        .admin-container {
            display: flex;
            min-height: 100vh;
        }
        
        .admin-content {
            flex: 1;
            padding: 20px;
            margin-left: 250px;
        }
    </style>
</head>
<body>
    <div class="admin-container">
        <?php include('./includes/sidebar.php'); ?>
        
        <div class="admin-content">
            <div class="content-header">
                <h1>Camera Management</h1>
                <a href="add_camera.php" class="btn-primary">
                    <i class="fas fa-plus"></i> Add New Camera
                </a>
            </div>

            <?php if (isset($_SESSION['success'])): ?>
                <div class="alert alert-success">
                    <?php 
                        echo htmlspecialchars($_SESSION['success']); 
                        unset($_SESSION['success']);
                    ?>
                </div>
            <?php endif; ?>
            
            <?php if (isset($_SESSION['error'])): ?>
                <div class="alert alert-danger">
                    <?php 
                        echo htmlspecialchars($_SESSION['error']); 
                        unset($_SESSION['error']);
                    ?>
                </div>
            <?php endif; ?>
            
            <div class="admin-table-container">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Image</th>
                            <th>Title</th>
                            <th>Category</th>
                            <th>Price/Day</th>
                            <th>Status</th>
                            <th>Featured</th>
                            <th>Rental Status</th>
                            <th>Rental Dates</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($camera = mysqli_fetch_assoc($cameras)): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($camera['id']); ?></td>
                                <td>
                                    <?php if ($camera['image_url']): ?>
                                        <img src="../<?php echo htmlspecialchars($camera['image_url']); ?>" alt="<?php echo htmlspecialchars($camera['title']); ?>" class="camera-thumbnail">
                                    <?php else: ?>
                                        <span>No image</span>
                                    <?php endif; ?>
                                </td>
                                <td><?php echo htmlspecialchars($camera['title']); ?></td>
                                <td><?php echo htmlspecialchars($camera['category']); ?></td>
                                <td>₱<?php echo number_format($camera['price_per_day'], 2); ?></td>
                                <td>
                                    <form method="POST" class="inline-form">
                                        <input type="hidden" name="camera_id" value="<?php echo $camera['id']; ?>">
                                        <select name="status" class="status-select <?php echo $camera['status']; ?>" onchange="this.form.submit()" <?php echo $camera['active_rentals'] > 0 ? 'disabled' : ''; ?>>
                                            <option value="available" <?php echo $camera['status'] === 'available' ? 'selected' : ''; ?>>Available</option>
                                            <option value="unavailable" <?php echo $camera['status'] === 'unavailable' ? 'selected' : ''; ?>>Unavailable</option>
                                            <option value="maintenance" <?php echo $camera['status'] === 'maintenance' ? 'selected' : ''; ?>>Maintenance</option>
                                        </select>
                                        <input type="hidden" name="update_status" value="1">
                                    </form>
                                </td>
                                <td>
                                    <form method="POST" class="inline-form">
                                        <input type="hidden" name="camera_id" value="<?php echo $camera['id']; ?>">
                                        <input type="hidden" name="toggle_featured" value="1">
                                        <input type="checkbox" name="featured" <?php echo $camera['featured'] ? 'checked' : ''; ?> onchange="this.form.submit()">
                                    </form>
                                </td>
                                <td>
                                    <?php if ($camera['active_rentals'] > 0): ?>
                                        <span class="badge badge-warning">Currently Rented</span>
                                    <?php else: ?>
                                        <span class="badge badge-success">Available</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php echo $camera['rental_dates'] ? htmlspecialchars($camera['rental_dates']) : 'No active rentals'; ?>
                                </td>
                                <td class="actions">
                                    <a href="edit_camera.php?id=<?php echo $camera['id']; ?>" class="btn-secondary btn-sm">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form method="POST" class="inline-form" onsubmit="return confirm('Are you sure you want to delete this camera?');">
                                        <input type="hidden" name="camera_id" value="<?php echo $camera['id']; ?>">
                                        <button type="submit" name="delete_camera" class="btn-danger btn-sm" <?php echo $camera['active_rentals'] > 0 ? 'disabled' : ''; ?>>
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
                
                <!-- Pagination -->
                <?php if ($total_pages > 1): ?>
                <div class="pagination">
                    <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                        <a href="?page=<?php echo $i; ?>" class="<?php echo $page === $i ? 'active' : ''; ?>">
                            <?php echo $i; ?>
                        </a>
                    <?php endfor; ?>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</body>
</html>
