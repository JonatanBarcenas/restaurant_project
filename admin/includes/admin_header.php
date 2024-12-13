<?php
session_start();
require_once '../config/database.php';
require_once '../includes/functions.php';

// Verificar si el usuario es administrador
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    header('Location: /auth/login.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel Administrativo - Sabores Auténticos</title>
    <link rel="stylesheet" href="/assets/css/admin.css">
</head>
<body class="admin-body">
    <div class="admin-container">
        <!-- Sidebar -->
        <aside class="admin-sidebar">
            <div class="sidebar-header">
                <h2>Panel Administrativo</h2>
            </div>
            <nav class="sidebar-nav">
                <a href="/admin/dashboard.php" class="nav-item <?php echo isCurrentPage('dashboard.php') ? 'active' : ''; ?>">
                    Dashboard
                </a>
                <a href="/admin/menu.php" class="nav-item <?php echo isCurrentPage('menu.php') ? 'active' : ''; ?>">
                    Gestión de Menú
                </a>
                <a href="/admin/inventory.php" class="nav-item <?php echo isCurrentPage('inventory.php') ? 'active' : ''; ?>">
                    Inventario
                </a>
                <a href="/admin/reservations.php" class="nav-item <?php echo isCurrentPage('reservations.php') ? 'active' : ''; ?>">
                    Reservaciones
                </a>
                <a href="/admin/staff.php" class="nav-item <?php echo isCurrentPage('staff.php') ? 'active' : ''; ?>">
                    Personal
                </a>
            </nav>
        </aside>

        <!-- Contenido principal -->
        <main class="admin-main">
            <header class="admin-header">
                <div class="admin-header-left">
                    <!-- Espacio para breadcrumbs o título -->
                </div>
                <div class="admin-header-right">
                    <div class="admin-user">
                        <span><?php echo $_SESSION['user_name']; ?></span>
                        <a href="/auth/logout.php">Cerrar Sesión</a>
                    </div>
                </div>
            </header>

            <div class="admin-content">