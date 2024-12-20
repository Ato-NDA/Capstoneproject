<?php
require_once 'includes/config.php';
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php?redirect=rent.php' . (isset($_GET['id']) ? '?id=' . $_GET['id'] : ''));
    exit;
}

// Get camera details
if (!isset($_GET['id'])) {
    header('Location: index.php');
    exit;
}

$camera_id = intval($_GET['id']);
$query = "SELECT * FROM cameras WHERE id = ? AND status = 'available'";
$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, "i", $camera_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if (mysqli_num_rows($result) == 0) {
    header('Location: index.php');
    exit;
}

$camera = mysqli_fetch_assoc($result);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rent Camera - <?php echo htmlspecialchars($camera['title']); ?></title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
</head>
<body>
    <?php include 'includes/header.php'; ?>

    <main>
        <div class="rental-container">
            <div class="camera-details-large">
                <div class="camera-image-large">
                    <img src="<?php echo htmlspecialchars($camera['image_url']); ?>" 
                         alt="<?php echo htmlspecialchars($camera['title']); ?>">
                </div>
                <div class="camera-info-large">
                    <h1><?php echo htmlspecialchars($camera['title']); ?></h1>
                    <p class="category"><?php echo ucfirst(htmlspecialchars($camera['category'])); ?></p>
                    <p class="price">₱<?php echo number_format($camera['price_per_day'], 2); ?> per day</p>
                    <p class="description"><?php echo htmlspecialchars($camera['description'] ?? ''); ?></p>
                    
                    <form action="process_rental.php" method="POST" class="rental-form">
                        <input type="hidden" name="camera_id" value="<?php echo $camera['id']; ?>">
                        <div class="form-group">
                            <label for="rental_date">Rental Date:</label>
                            <input type="date" id="rental_date" name="rental_date" required 
                                   min="<?php echo date('Y-m-d'); ?>">
                        </div>
                        <div class="form-group">
                            <label for="rental_days">Number of Days:</label>
                            <input type="number" id="rental_days" name="rental_days" required 
                                   min="1" max="30" value="1">
                        </div>
                        <button type="submit" class="submit-button">Proceed to Rent</button>
                    </form>
                </div>
            </div>
        </div>
    </main>

    <?php include 'includes/footer.php'; ?>

    <script>
        // Calculate total price when rental days change
        document.getElementById('rental_days').addEventListener('change', function() {
            const days = this.value;
            const pricePerDay = <?php echo $camera['price_per_day']; ?>;
            const total = days * pricePerDay;
            document.querySelector('.price').textContent = 
                `₱${(pricePerDay).toLocaleString('en-US', {minimumFractionDigits: 2})} per day\n` +
                `Total: ₱${total.toLocaleString('en-US', {minimumFractionDigits: 2})}`;
        });
    </script>
</body>
</html>
