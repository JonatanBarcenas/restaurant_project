<?php
function initSession() {
    $sessionPath = 'C:/Apache24/tmp';
    if (!file_exists($sessionPath)) {
        mkdir($sessionPath, 0777, true);
    }
    
    
    if (session_status() === PHP_SESSION_NONE) {
        session_save_path($sessionPath);
        session_start();
    }
}

function isLoggedIn() {
    initSession();
    $currentFile = basename($_SERVER['PHP_SELF']);
    $loginPage = 'login.php';

    if (!isset($_SESSION['user_id']) && $currentFile !== $loginPage) {
        header("Location: /restaurant_project/auth/login.php");
        exit();
    }
}

function getUserName() {
    return isset($_SESSION['name']) ? $_SESSION['name'] : 'Usuario';
}

function isAdmin() {
    return isset($_SESSION['user_id']) && $_SESSION['user_role'] === 'admin';
}

function redirect($url) {
    header("Location: $url");
    exit();
}

function sanitize($input) {
    return htmlspecialchars(strip_tags($input));
}

function formatPrice($price) {
    return '$' . number_format($price, 2);
}

function formatDate($date) {
    return date('d/m/Y', strtotime($date));
}

function formatTime($time) {
    return date('H:i', strtotime($time));
}
?>