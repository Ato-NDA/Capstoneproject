<?php
require_once 'config.php';

// Get featured and available cameras
$query = "SELECT * FROM cameras WHERE featured = 1 AND status = 'available' ORDER BY id DESC";
$result = mysqli_query($conn, $query);

// Create debug log
$log_file = "../debug_cameras.log";
$log_content = "Query executed at " . date('Y-m-d H:i:s') . "\n";
$log_content .= "SQL Query: " . $query . "\n";
$log_content .= "Number of results: " . mysqli_num_rows($result) . "\n\n";
file_put_contents($log_file, $log_content, FILE_APPEND);

// If no featured cameras found, show some default ones
if (mysqli_num_rows($result) == 0) {
    file_put_contents($log_file, "No featured cameras found, using defaults\n", FILE_APPEND);
    $featured_cameras = [
        [
            'id' => 1,
            'title' => 'Canon EOS 4000D DSLR',
            'image_url' => 'assets/images/Canon-EOS-4000D-DSLR-Camera-EF-S-18-55-mm-f-3-5-5-6-III-Lens-Intl-Model_87e41624-5053-4b1b-b177-14b31e8ea8e2_1.f52dbc3ff9348a7535ac8ccb49b2dc08.webp',
            'price_per_day' => 950,
            'category' => 'DSLR'
        ],
        [
            'id' => 2,
            'title' => 'Sony DSC-W830 Compact Digital Camera',
            'image_url' => 'assets/images/sony-dsc-w830-digital-camera-black-digit-sony-dsc-w830b_3.jpg',
            'price_per_day' => 500,
            'category' => 'Digital'
        ],
        [
            'id' => 3,
            'title' => 'Fujifilm X-A3 Mirrorless Camera',
            'image_url' => 'assets/images/ZPR-FUJI-X-A3-BEAUTY.jpg',
            'price_per_day' => 850,
            'category' => 'Mirrorless'
        ],
        [
            'id' => 4,
            'title' => 'Professional DSLR Camera Kit',
            'image_url' => 'assets/images/OIP.jpg',
            'price_per_day' => 800,
            'category' => 'DSLR'
        ]
    ];
} else {
    $featured_cameras = [];
    while ($camera = mysqli_fetch_assoc($result)) {
        $featured_cameras[] = $camera;
    }
}
?>

<div class="camera-grid">
    <?php foreach ($featured_cameras as $camera): ?>
        <div class="camera-card">
            <div class="camera-image-container">
                <img src="<?php echo htmlspecialchars($camera['image_url']); ?>" 
                     alt="<?php echo htmlspecialchars($camera['title']); ?>"
                     loading="lazy">
            </div>
            <div class="camera-details">
                <h3 class="camera-title"><?php echo htmlspecialchars($camera['title']); ?></h3>
                <div class="camera-category"><?php echo ucfirst(htmlspecialchars($camera['category'])); ?></div>
                <div class="camera-price">₱<?php echo number_format($camera['price_per_day'], 2); ?>/day</div>
                <a href="camera-details.php?id=<?php echo $camera['id']; ?>" class="rent-button">Rent Now</a>
            </div>
        </div>
    <?php endforeach; ?>
</div>
