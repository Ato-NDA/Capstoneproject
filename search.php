<?php
session_start();
require_once('includes/config.php');

$search_query = isset($_GET['query']) ? trim($_GET['query']) : '';
$message = '';
$cameras = [];

if (!empty($search_query)) {
    // Prepare the search query with wildcards for partial matches
    $search_term = '%' . mysqli_real_escape_string($conn, $search_query) . '%';
    
    // Query to search cameras by title, category, or description
    $query = "SELECT c.*, 
              CASE 
                WHEN c.status = 'maintenance' THEN 'Under Maintenance'
                WHEN c.status = 'rented' THEN 'Currently Rented'
                WHEN EXISTS (
                    SELECT 1 FROM reservations r 
                    WHERE r.camera_id = c.id 
                    AND r.status = 'confirmed'
                    AND (
                        CURRENT_DATE BETWEEN r.start_date AND r.end_date
                        OR r.start_date > CURRENT_DATE
                    )
                ) THEN 'Reserved'
                ELSE 'Available'
              END as availability_status
              FROM cameras c
              WHERE (c.title LIKE ? OR c.category LIKE ? OR c.description LIKE ?)
              ORDER BY 
                CASE 
                    WHEN c.status = 'available' THEN 1
                    WHEN c.status = 'rented' THEN 2
                    WHEN c.status = 'maintenance' THEN 3
                END,
                c.title ASC";
                
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "sss", $search_term, $search_term, $search_term);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
    if (mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            $cameras[] = $row;
        }
    } else {
        $message = "No cameras found matching your search.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Search Results - Aye's Rental.ph</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .search-results {
            padding: 2rem 0;
        }

        .search-header {
            margin-bottom: 2rem;
        }

        .search-header h1 {
            font-size: 1.8em;
            color: #2c3e50;
            margin-bottom: 0.5rem;
        }

        .search-header p {
            color: #666;
        }

        .cameras-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 2rem;
        }

        .camera-card {
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            overflow: hidden;
            transition: transform 0.3s ease;
            display: flex;
            flex-direction: column;
        }

        .camera-card:hover {
            transform: translateY(-5px);
        }

        .camera-image {
            position: relative;
            width: 100%;
            padding-top: 75%; /* 4:3 Aspect Ratio */
            background: #f5f5f5;
            overflow: hidden;
        }

        .camera-image img {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            object-fit: contain;
            padding: 0.5rem;
        }

        .camera-info {
            padding: 1.5rem;
            flex-grow: 1;
            display: flex;
            flex-direction: column;
        }

        .camera-title {
            font-size: 1.2em;
            font-weight: 600;
            color: #2c3e50;
            margin-bottom: 0.5rem;
        }

        .camera-category {
            color: #666;
            font-size: 0.9em;
            margin-bottom: 1rem;
        }

        .camera-price {
            font-weight: 500;
            color: #2c3e50;
            margin-bottom: 1rem;
        }

        .camera-status {
            display: inline-block;
            padding: 0.5rem 1rem;
            border-radius: 15px;
            font-size: 0.9em;
            font-weight: 500;
            margin-bottom: 1rem;
        }

        .status-Available { background: #e3fcef; color: #00875a; }
        .status-Reserved { background: #fff0b3; color: #974f0c; }
        .status-Rented { background: #deebff; color: #0747a6; }
        .status-Maintenance { background: #ffebe6; color: #de350b; }

        .btn-rent {
            display: inline-block;
            padding: 0.75rem 1.5rem;
            background: #3498db;
            color: white;
            text-decoration: none;
            border-radius: 4px;
            text-align: center;
            transition: background-color 0.3s ease;
            margin-top: auto;
        }

        .btn-rent:hover {
            background: #2980b9;
        }

        .btn-rent.disabled {
            background: #ccc;
            cursor: not-allowed;
        }

        .no-results {
            text-align: center;
            padding: 3rem;
            background: #f8f9fa;
            border-radius: 8px;
        }

        .no-results i {
            font-size: 3em;
            color: #dee2e6;
            margin-bottom: 1rem;
        }

        .no-results p {
            color: #666;
            margin-bottom: 1rem;
        }

        .search-query {
            color: #3498db;
            font-weight: 500;
        }
    </style>
</head>
<body>
    <?php include('includes/header.php'); ?>

    <main class="container">
        <div class="search-results">
            <div class="search-header">
                <?php if (!empty($search_query)): ?>
                    <h1>Search Results</h1>
                    <p>Showing results for: <span class="search-query">"<?php echo htmlspecialchars($search_query); ?>"</span></p>
                <?php else: ?>
                    <h1>All Cameras</h1>
                <?php endif; ?>
            </div>

            <?php if (!empty($message)): ?>
                <div class="no-results">
                    <i class="fas fa-search"></i>
                    <p><?php echo $message; ?></p>
                    <a href="index.php" class="btn-rent">Browse All Cameras</a>
                </div>
            <?php elseif (!empty($cameras)): ?>
                <div class="cameras-grid">
                    <?php foreach ($cameras as $camera): ?>
                        <div class="camera-card">
                            <div class="camera-image">
                                <?php if (!empty($camera['image_url'])): ?>
                                    <img src="<?php echo htmlspecialchars($camera['image_url']); ?>" 
                                         alt="<?php echo htmlspecialchars($camera['title']); ?>">
                                <?php else: ?>
                                    <div class="no-image">
                                        <i class="fas fa-camera fa-2x"></i>
                                    </div>
                                <?php endif; ?>
                            </div>
                            <div class="camera-info">
                                <h3 class="camera-title"><?php echo htmlspecialchars($camera['title']); ?></h3>
                                <p class="camera-category">
                                    <i class="fas fa-tag"></i>
                                    <?php echo ucfirst(htmlspecialchars($camera['category'])); ?>
                                </p>
                                <p class="camera-price">
                                    <i class="fas fa-money-bill"></i>
                                    ₱<?php echo number_format($camera['price_per_day'], 2); ?> per day
                                </p>
                                <p class="camera-status status-<?php echo $camera['availability_status']; ?>">
                                    <i class="fas fa-info-circle"></i>
                                    <?php echo $camera['availability_status']; ?>
                                </p>
                                <?php if ($camera['availability_status'] === 'Available'): ?>
                                    <a href="rent.php?id=<?php echo $camera['id']; ?>" class="btn-rent">
                                        Rent Now
                                    </a>
                                <?php else: ?>
                                    <button class="btn-rent disabled" disabled>
                                        Not Available
                                    </button>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </main>

    <?php include('includes/footer.php'); ?>
</body>
</html>
