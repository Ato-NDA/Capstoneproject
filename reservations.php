<?php
session_start();
require_once('includes/config.php');

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$message = '';

// Handle reservation cancellation
if (isset($_POST['cancel_reservation'])) {
    $reservation_id = mysqli_real_escape_string($conn, $_POST['reservation_id']);
    
    // Use prepared statement for security
    $query = "UPDATE reservations SET status = 'cancelled' WHERE id = ? AND user_id = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "ii", $reservation_id, $user_id);
    
    if (mysqli_stmt_execute($stmt)) {
        $message = '<div class="success-message">Reservation cancelled successfully.</div>';
    } else {
        $message = '<div class="error-message">Error cancelling reservation.</div>';
    }
}

// Handle reservation removal
if (isset($_POST['remove_reservation'])) {
    $reservation_id = mysqli_real_escape_string($conn, $_POST['reservation_id']);
    
    // Only allow removal of cancelled or completed reservations
    $query = "DELETE FROM reservations 
              WHERE id = ? AND user_id = ? 
              AND status IN ('cancelled', 'completed')";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "ii", $reservation_id, $user_id);
    
    if (mysqli_stmt_execute($stmt)) {
        $message = '<div class="success-message">Reservation removed successfully.</div>';
    } else {
        $message = '<div class="error-message">Error removing reservation.</div>';
    }
}

// Fetch user's reservations with camera details
$query = "SELECT r.*, c.title as camera_title, c.image_url, c.price_per_day 
          FROM reservations r 
          JOIN cameras c ON r.camera_id = c.id 
          WHERE r.user_id = ? 
          ORDER BY 
            CASE r.status 
                WHEN 'pending' THEN 1 
                WHEN 'confirmed' THEN 2 
                WHEN 'completed' THEN 3 
                WHEN 'cancelled' THEN 4 
            END,
            r.created_at DESC";
$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Reservations - Aye's Rental.ph</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .success-message {
            background: #e3fcef;
            color: #00875a;
            padding: 1rem;
            border-radius: 4px;
            margin-bottom: 1rem;
        }
        
        .error-message {
            background: #ffebe6;
            color: #de350b;
            padding: 1rem;
            border-radius: 4px;
            margin-bottom: 1rem;
        }

        .reservations-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 2rem;
            padding: 2rem 0;
        }

        .reservation-card {
            position: relative;
            border: 1px solid #eee;
            border-radius: 8px;
            padding: 1.5rem;
            background: white;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            display: flex;
            flex-direction: column;
        }

        .reservation-image {
            position: relative;
            width: 100%;
            padding-top: 75%; /* 4:3 Aspect Ratio */
            margin-bottom: 1rem;
            background: #f5f5f5;
            border-radius: 4px;
            overflow: hidden;
        }

        .reservation-image img {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            object-fit: contain;
            padding: 0.5rem;
        }

        .no-image {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #999;
            font-size: 1.1em;
        }

        .status {
            display: inline-block;
            padding: 0.5rem 1rem;
            border-radius: 15px;
            font-size: 0.9em;
            font-weight: 500;
            margin: 0.5rem 0;
        }

        .status-pending { background: #fff0b3; color: #974f0c; }
        .status-confirmed { background: #e3fcef; color: #00875a; }
        .status-completed { background: #deebff; color: #0747a6; }
        .status-cancelled { background: #ffebe6; color: #de350b; }

        .btn-cancel, .btn-remove {
            padding: 0.5rem 1rem;
            border-radius: 4px;
            border: none;
            cursor: pointer;
            font-size: 0.9em;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            width: 100%;
        }

        .btn-cancel {
            background: #ffebe6;
            color: #de350b;
        }

        .btn-remove {
            background: #eee;
            color: #666;
        }

        .btn-cancel:hover { background: #ffbdad; }
        .btn-remove:hover { background: #ddd; }

        .action-buttons {
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
            margin-top: auto;
            padding-top: 1rem;
        }

        .reservation-details {
            flex-grow: 1;
        }

        .reservation-details p {
            margin: 0.5rem 0;
            color: #666;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .reservation-details i {
            width: 16px;
            color: #999;
        }

        .reservation-title {
            font-size: 1.2em;
            font-weight: 600;
            color: #2c3e50;
            margin-bottom: 1rem;
        }

        /* Footer Styles */
        .main-footer {
            background: #2c3e50;
            color: #fff;
            padding: 3rem 0 0;
            margin-top: 3rem;
        }

        .footer-content {
            max-width: 1200px;
            margin: 0 auto;
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 2rem;
            padding: 0 1rem;
        }

        .footer-section h3 {
            color: #fff;
            margin-bottom: 1.5rem;
            font-size: 1.2em;
        }

        .footer-section p {
            color: #ecf0f1;
            line-height: 1.6;
        }

        .footer-section ul {
            list-style: none;
            padding: 0;
        }

        .footer-section ul li {
            margin-bottom: 0.8rem;
        }

        .footer-section ul li a {
            color: #ecf0f1;
            text-decoration: none;
            transition: color 0.3s ease;
        }

        .footer-section ul li a:hover {
            color: #3498db;
        }

        .social-links {
            display: flex;
            gap: 1rem;
        }

        .social-link {
            color: #fff;
            font-size: 1.5em;
            transition: color 0.3s ease;
        }

        .social-link:hover {
            color: #3498db;
        }

        .footer-bottom {
            background: #243342;
            text-align: center;
            padding: 1.5rem;
            margin-top: 3rem;
        }

        .footer-bottom p {
            margin: 0;
            color: #ecf0f1;
        }

        @media (max-width: 768px) {
            .footer-content {
                grid-template-columns: 1fr;
                text-align: center;
            }

            .social-links {
                justify-content: center;
            }

            .reservations-grid {
                grid-template-columns: 1fr;
            }
        }

        .container {
            min-height: calc(100vh - 400px); /* Adjust based on footer height */
        }
    </style>
</head>
<body>
    <?php include('includes/header.php'); ?>

    <main class="container">
        <h1>My Reservations</h1>

        <?php echo $message; ?>

        <?php if (mysqli_num_rows($result) > 0): ?>
            <div class="reservations-grid">
                <?php while ($reservation = mysqli_fetch_assoc($result)): ?>
                    <div class="reservation-card">
                        <div class="reservation-image">
                            <?php if (!empty($reservation['image_url'])): ?>
                                <img src="<?php echo htmlspecialchars($reservation['image_url']); ?>" 
                                     alt="<?php echo htmlspecialchars($reservation['camera_title']); ?>">
                            <?php else: ?>
                                <div class="no-image">
                                    <i class="fas fa-camera fa-2x"></i>
                                </div>
                            <?php endif; ?>
                        </div>
                        <h3 class="reservation-title"><?php echo htmlspecialchars($reservation['camera_title']); ?></h3>
                        
                        <div class="reservation-details">
                            <p>
                                <i class="fas fa-calendar"></i>
                                <span><?php echo date('M d, Y', strtotime($reservation['start_date'])); ?> - 
                                <?php echo date('M d, Y', strtotime($reservation['end_date'])); ?></span>
                            </p>
                            <p>
                                <i class="fas fa-money-bill"></i>
                                <span>Total: ₱<?php echo number_format($reservation['total_price'], 2); ?></span>
                            </p>
                            <p>
                                <i class="fas fa-clock"></i>
                                <span>Reserved on: <?php echo date('M d, Y h:i A', strtotime($reservation['created_at'])); ?></span>
                            </p>
                            <p class="status status-<?php echo strtolower($reservation['status']); ?>">
                                <i class="fas fa-info-circle"></i>
                                <span>Status: <?php echo ucfirst($reservation['status']); ?></span>
                            </p>
                        </div>

                        <div class="action-buttons">
                            <?php if ($reservation['status'] === 'pending'): ?>
                                <form method="POST">
                                    <input type="hidden" name="reservation_id" value="<?php echo $reservation['id']; ?>">
                                    <button type="submit" name="cancel_reservation" class="btn-cancel"
                                            onclick="return confirm('Are you sure you want to cancel this reservation?')">
                                        <i class="fas fa-times"></i> Cancel Reservation
                                    </button>
                                </form>
                            <?php endif; ?>

                            <?php if (in_array($reservation['status'], ['cancelled', 'completed'])): ?>
                                <form method="POST">
                                    <input type="hidden" name="reservation_id" value="<?php echo $reservation['id']; ?>">
                                    <button type="submit" name="remove_reservation" class="btn-remove"
                                            onclick="return confirm('Are you sure you want to remove this reservation from your history? This cannot be undone.')">
                                        <i class="fas fa-trash"></i> Remove from History
                                    </button>
                                </form>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>
        <?php else: ?>
            <div class="no-reservations">
                <i class="fas fa-camera"></i>
                <p>You haven't made any reservations yet.</p>
                <a href="index.php" class="btn-primary">Browse Cameras</a>
            </div>
        <?php endif; ?>
    </main>

    <?php include('includes/footer.php'); ?>
</body>
</html>
