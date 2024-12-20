<?php
session_start();
require_once('../includes/config.php');

// Check if user is logged in and is admin
if (!isset($_SESSION['user_id']) || !isset($_SESSION['is_admin']) || $_SESSION['is_admin'] !== 1) {
    header("Location: ../login.php");
    exit();
}
?>
