<?php
require_once 'config.php';

// Sample camera data
$cameras = [
    [
        'title' => 'Canon EOS R5',
        'description' => 'Professional full-frame mirrorless camera with 8K video capability and advanced autofocus.',
        'category' => 'mirrorless',
        'price' => 120.00,
        'image_url' => 'assets/images/canon-r5.jpg',
        'status' => 'available'
    ],
    [
        'title' => 'Sony A7 III',
        'description' => 'Full-frame mirrorless camera with excellent low-light performance and 4K video.',
        'category' => 'mirrorless',
        'price' => 100.00,
        'image_url' => 'assets/images/sony-a7iii.jpg',
        'status' => 'available'
    ],
    [
        'title' => 'Nikon D850',
        'description' => 'High-resolution DSLR with exceptional image quality and professional features.',
        'category' => 'dslr',
        'price' => 90.00,
        'image_url' => 'assets/images/nikon-d850.jpg',
        'status' => 'available'
    ],
    [
        'title' => 'Blackmagic Pocket 6K',
        'description' => 'Professional cinema camera with 6K resolution and advanced RAW recording.',
        'category' => 'video',
        'price' => 150.00,
        'image_url' => 'assets/images/blackmagic-6k.jpg',
        'status' => 'available'
    ]
];

// Insert sample cameras
foreach ($cameras as $camera) {
    $query = "INSERT INTO cameras (title, description, category, price, image_url, status) 
              VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "sssdss", 
        $camera['title'],
        $camera['description'],
        $camera['category'],
        $camera['price'],
        $camera['image_url'],
        $camera['status']
    );
    mysqli_stmt_execute($stmt);
}

echo "Database seeded successfully!\n";
?>
