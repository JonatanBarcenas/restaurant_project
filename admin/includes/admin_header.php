<?php

require_once $_SERVER['DOCUMENT_ROOT'] . '/restaurant_project/config/database.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/restaurant_project/includes/functions.php';
initSession();

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel Administrativo - Sabores Auténticos</title>
    <link rel="stylesheet" href="/restaurant_project/assets/css/admin.css">
    <link rel="stylesheet" href="/restaurant_project/assets/css/admin_reservations.css">
</head>
<body class="admin-body">
    <div class="admin-container">
        <!-- Sidebar -->
        <aside class="admin-sidebar">
            <div class="sidebar-header">
                <h2>Panel Administrativo</h2>
            </div>
            <nav class="sidebar-nav">
                <a href="<?php echo '/restaurant_project/admin/dashboard.php'; ?>" 
                   class="nav-item <?php echo isCurrentPage('dashboard.php') ? 'active' : ''; ?>">
                    Dashboard
                </a>
                <a href="<?php echo '/restaurant_project/admin/menu.php'; ?>" 
                   class="nav-item <?php echo isCurrentPage('menu.php') ? 'active' : ''; ?>">
                    Gestión de Menú
                </a>
                <a href="<?php echo '/restaurant_project/admin/inventory.php'; ?>" 
                   class="nav-item <?php echo isCurrentPage('inventory.php') ? 'active' : ''; ?>">
                    Inventario
                </a>
                <a href="<?php echo '/restaurant_project/admin/reservations.php'; ?>" 
                   class="nav-item <?php echo isCurrentPage('reservations.php') ? 'active' : ''; ?>">
                    Reservaciones
                </a>
                <a href="<?php echo '/restaurant_project/admin/staff.php'; ?>" 
                   class="nav-item <?php echo isCurrentPage('staff.php') ? 'active' : ''; ?>">
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
                        <span>ADMIN</span>
                        <a href="/restaurant_project/auth/logout.php">Cerrar Sesión</a>
                    </div>
                </div>
            </header>

            <div class="admin-content">