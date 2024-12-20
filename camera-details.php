<?php
session_start();
require_once('includes/config.php');

if (!isset($_GET['id'])) {
    header("Location: index.php");
    exit();
}

$camera_id = $_GET['id'];
$result = mysqli_query($conn, "SELECT * FROM cameras WHERE id = $camera_id");
$camera = mysqli_fetch_assoc($result);

if (!$camera) {
    header("Location: index.php");
    exit();
}

$success_message = '';
$error_message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
    $start_date = $_POST['start_date'];
    $end_date = $_POST['end_date'];
    
    // Validate dates
    $start = new DateTime($start_date);
    $end = new DateTime($end_date);
    $today = new DateTime();
    
    if ($start < $today) {
        $error_message = "Start date cannot be in the past!";
    } elseif ($end <= $start) {
        $error_message = "End date must be after start date!";
    } else {
        // Check if camera is available for these dates
        $check_query = "SELECT * FROM reservations 
                       WHERE camera_id = $camera_id 
                       AND ((start_date BETWEEN '$start_date' AND '$end_date') 
                       OR (end_date BETWEEN '$start_date' AND '$end_date')
                       OR ('$start_date' BETWEEN start_date AND end_date))
                       AND status != 'cancelled'";
        
        $check_result = mysqli_query($conn, $check_query);
        
        if (mysqli_num_rows($check_result) > 0) {
            $error_message = "Camera is not available for the selected dates!";
        } else {
            // Calculate total days and price
            $interval = $start->diff($end);
            $total_days = $interval->days + 1;
            $total_price = $total_days * $camera['price_per_day'];
            
            // Create reservation
            $insert_query = "INSERT INTO reservations (user_id, camera_id, start_date, end_date, total_price, status, created_at) 
                           VALUES ($user_id, $camera_id, '$start_date', '$end_date', $total_price, 'pending', NOW())";
            
            if (mysqli_query($conn, $insert_query)) {
                $success_message = "Rental request submitted successfully! Check your reservations for status.";
            } else {
                $error_message = "Error submitting rental request. Please try again.";
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
    <title><?php echo htmlspecialchars($camera['title']); ?> - Aye's Rental.ph</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <?php include('includes/header.php'); ?>

    <main class="container">
        <?php if ($success_message): ?>
            <div class="success-message"><?php echo $success_message; ?></div>
        <?php endif; ?>
        
        <?php if ($error_message): ?>
            <div class="error-message"><?php echo $error_message; ?></div>
        <?php endif; ?>

        <div class="camera-details">
            <div class="camera-image">
                <img src="<?php echo htmlspecialchars($camera['image_url']); ?>" alt="<?php echo htmlspecialchars($camera['title']); ?>">
            </div>
            
            <div class="camera-info">
                <h1><?php echo htmlspecialchars($camera['title']); ?></h1>
                <p class="price">₱<?php echo number_format($camera['price_per_day'], 2); ?> per day</p>
                <p class="description"><?php echo nl2br(htmlspecialchars($camera['description'])); ?></p>
                
                <?php if (isset($_SESSION['user_id'])): ?>
                    <form method="POST" class="rental-form">
                        <div class="form-group">
                            <label for="start_date">Start Date</label>
                            <input type="date" id="start_date" name="start_date" required 
                                   min="<?php echo date('Y-m-d'); ?>">
                        </div>
                        
                        <div class="form-group">
                            <label for="end_date">End Date</label>
                            <input type="date" id="end_date" name="end_date" required 
                                   min="<?php echo date('Y-m-d'); ?>">
                        </div>
                        
                        <button type="submit" class="btn-primary">Rent Now</button>
                    </form>
                <?php else: ?>
                    <div class="login-prompt">
                        <p>Please <a href="login.php">login</a> to rent this camera.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </main>

    <?php include('includes/footer.php'); ?>

    <script>
        // Calculate and display total price when dates change
        const startDate = document.getElementById('start_date');
        const endDate = document.getElementById('end_date');
        const pricePerDay = <?php echo $camera['price_per_day']; ?>;

        function updateTotalPrice() {
            if (startDate.value && endDate.value) {
                const start = new Date(startDate.value);
                const end = new Date(endDate.value);
                const days = Math.ceil((end - start) / (1000 * 60 * 60 * 24)) + 1;
                
                if (days > 0) {
                    const total = days * pricePerDay;
                    document.querySelector('.price').innerHTML = 
                        `₱${pricePerDay.toFixed(2)} per day<br>Total for ${days} days: ₱${total.toFixed(2)}`;
                }
            }
        }

        startDate?.addEventListener('change', updateTotalPrice);
        endDate?.addEventListener('change', updateTotalPrice);
    </script>
</body>
</html>
