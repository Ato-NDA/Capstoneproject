<?php 
require_once(__DIR__ . '/auth_helper.php');
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Aye's Rental.ph - Camera Rental</title>
    <link rel="stylesheet" href="/camera-rental-website/assets/css/style.css">
    <?php if(isAdmin()): ?>
    <link rel="stylesheet" href="/camera-rental-website/admin/css/admin.css">
    <?php endif; ?>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .search-bar {
            display: flex;
            align-items: center;
            gap: 10px;
            margin: 0 20px;
        }
        .search-bar form {
            display: flex;
            align-items: center;
            width: 100%;
            max-width: 500px;
            position: relative;
        }
        .search-bar input[type="text"] {
            width: 100%;
            padding: 8px 40px 8px 12px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 14px;
        }
        .search-bar button {
            position: absolute;
            right: 5px;
            top: 50%;
            transform: translateY(-50%);
            background: transparent;
            border: none;
            color: #007bff;
            cursor: pointer;
            padding: 5px;
            font-size: 14px;
        }
        .search-bar button:hover {
            color: #0056b3;
        }
    </style>
</head>
<body>
    <header>
        <nav class="main-nav">
            <div class="logo">
                <a href="/camera-rental-website/" class="logo-link">
                    <h1>Aye's Rental.ph</h1>
                </a>
            </div>
            <div class="search-bar">
                <form action="/camera-rental-website/search.php" method="GET">
                    <input type="text" name="query" placeholder="Search for cameras..." 
                           value="<?php echo isset($_GET['query']) ? htmlspecialchars($_GET['query']) : ''; ?>">
                    <button type="submit"><i class="fas fa-search"></i></button>
                </form>
            </div>
            <ul class="nav-links">
                <?php if (isset($_SESSION['user_id'])): ?>
                    <?php if (isAdmin()): ?>
                        <li><a href="/camera-rental-website/admin/dashboard.php">
                            <i class="fas fa-tachometer-alt"></i> Dashboard
                        </a></li>
                        <li><a href="/camera-rental-website/admin/users.php">
                            <i class="fas fa-users"></i> Users
                        </a></li>
                        <li><a href="/camera-rental-website/admin/cameras.php">
                            <i class="fas fa-camera"></i> Cameras
                        </a></li>
                    <?php endif; ?>
                    <li><a href="/camera-rental-website/reservations.php" <?php echo basename($_SERVER['PHP_SELF']) == 'reservations.php' ? 'class="active"' : ''; ?>>Reservations</a></li>
                    <li><a href="/camera-rental-website/account.php" <?php echo basename($_SERVER['PHP_SELF']) == 'account.php' ? 'class="active"' : ''; ?>>My Account</a></li>
                    <li><a href="/camera-rental-website/logout.php" class="btn-logout">Logout</a></li>
                <?php else: ?>
                    <li>
                        <a href="/camera-rental-website/login.php" class="btn-login">
                            <i class="fas fa-sign-in-alt"></i> Login
                        </a>
                    </li>
                <?php endif; ?>
            </ul>
        </nav>
    </header>
