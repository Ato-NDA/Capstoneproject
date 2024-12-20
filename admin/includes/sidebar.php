<?php
// Check if user is logged in and is admin
if (!isset($_SESSION['user_id']) || !isset($_SESSION['is_admin']) || $_SESSION['is_admin'] !== 1) {
    header("Location: ../login.php");
    exit();
}

// Get current page name
$current_page = basename($_SERVER['PHP_SELF'], '.php');
?>

<div class="admin-sidebar">
    <div class="sidebar-header">
        <h2>Admin Panel</h2>
    </div>
    
    <nav class="admin-nav">
        <ul>
            <li class="<?php echo $current_page === 'dashboard' ? 'active' : ''; ?>">
                <a href="dashboard.php">
                    <i class="fas fa-tachometer-alt"></i>
                    <span>Dashboard</span>
                </a>
            </li>
            <li class="<?php echo $current_page === 'cameras' ? 'active' : ''; ?>">
                <a href="cameras.php">
                    <i class="fas fa-camera"></i>
                    <span>Cameras</span>
                </a>
            </li>
            <li class="<?php echo $current_page === 'rentals' ? 'active' : ''; ?>">
                <a href="rentals.php">
                    <i class="fas fa-clipboard-list"></i>
                    <span>Rentals</span>
                </a>
            </li>
            <li class="<?php echo $current_page === 'users' ? 'active' : ''; ?>">
                <a href="users.php">
                    <i class="fas fa-users"></i>
                    <span>Users</span>
                </a>
            </li>
            <li class="<?php echo $current_page === 'settings' ? 'active' : ''; ?>">
                <a href="settings.php">
                    <i class="fas fa-cog"></i>
                    <span>Settings</span>
                </a>
            </li>
            <li>
                <a href="logout.php">
                    <i class="fas fa-sign-out-alt"></i>
                    <span>Logout</span>
                </a>
            </li>
        </ul>
    </nav>
</div>

<style>
.admin-sidebar {
    width: 250px;
    background: #2c3e50;
    color: #ecf0f1;
    height: 100vh;
    position: fixed;
    left: 0;
    top: 0;
    padding: 20px 0;
}

.sidebar-header {
    padding: 0 20px 20px;
    border-bottom: 1px solid #34495e;
}

.sidebar-header h2 {
    margin: 0;
    font-size: 1.5rem;
    color: #ecf0f1;
}

.admin-nav ul {
    list-style: none;
    padding: 0;
    margin: 0;
}

.admin-nav li {
    margin: 5px 0;
}

.admin-nav a {
    display: flex;
    align-items: center;
    padding: 12px 20px;
    color: #bdc3c7;
    text-decoration: none;
    transition: all 0.3s ease;
}

.admin-nav a:hover {
    background: #34495e;
    color: #ecf0f1;
}

.admin-nav li.active a {
    background: #3498db;
    color: #fff;
}

.admin-nav i {
    width: 20px;
    margin-right: 10px;
    text-align: center;
}

.admin-nav span {
    font-size: 0.95rem;
}
</style>
