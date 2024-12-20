<?php
session_start();
require_once('db.php');

function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

function isAdmin() {
    return isset($_SESSION['is_admin']) && $_SESSION['is_admin'] === true;
}

function requireLogin() {
    if (!isLoggedIn()) {
        header("Location: /camera-rental-website/login.php");
        exit();
    }
}

function requireAdmin() {
    if (!isAdmin()) {
        header("Location: /camera-rental-website/login.php");
        exit();
    }
}

function login($email, $password) {
    global $conn;
    
    $stmt = $conn->prepare("SELECT id, username, password, is_admin FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();
        if (password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['is_admin'] = $user['is_admin'] == 1;
            return true;
        }
    }
    return false;
}

function logout() {
    session_start();
    session_destroy();
    header("Location: /camera-rental-website/login.php");
    exit();
}
