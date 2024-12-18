<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/functions.php';
initSession();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sabores Auténticos</title>
    <link rel="stylesheet" href="/assets/css/auth.css">
   
</head>
<body>
    <header>
        <nav>
            <div class="logo">
                <h1>Sabores Auténticos</h1>
            </div>
            <div class="menu">
                <a href="/">Inicio</a>
                <a href="/menu.php">Menú</a>
                <a href="/reservations.php">Reservar</a>
                <?php if(isLoggedIn()): ?>
                    <div class="user-menu">
                        <button class="user-button">
                            <?php echo $_SESSION['user_name']; ?> ▼
                        </button>
                        <div class="dropdown-menu">
                            <a href="/user/profile.php">Mi Perfil</a>
                            <a href="/user/orders/">Mis Pedidos</a>
                            <a href="/user/reservations.php">Mis Reservas</a>
                            <a href="/user/addresses/">Direcciones</a>
                            <a href="/user/payments.php">Métodos de Pago</a>
                            <a href="/auth/logout.php">Cerrar Sesión</a>
                        </div>
                    </div>
                <?php else: ?>
                    <a href="/auth/login.php">Ingresar</a>
                <?php endif; ?>
                <a href="/cart.php" class="cart-button">
                    Carrito <span class="cart-count">0</span>
                </a>
            </div>
        </nav>
    </header>