<?php
// Database configuration
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'camera_rental');

// Website configuration
define('SITE_URL', 'http://localhost/camera-rental-website');
define('ADMIN_URL', SITE_URL . '/admin');

// Upload paths
define('UPLOAD_DIR', __DIR__ . '/uploads');
define('UPLOAD_URL', SITE_URL . '/uploads');

// Session configuration
define('SESSION_NAME', 'camera_rental_session');
define('SESSION_LIFETIME', 86400); // 24 hours

// Error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);
?>
