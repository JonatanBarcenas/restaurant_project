// includes/header.php
<?php
session_start();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sabores Auténticos</title>
    <link rel="stylesheet" href="/assets/css/style.css">
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
                <?php if(isset($_SESSION['user_id'])): ?>
                    <div class="user-menu">
                        <button class="user-button">
                            <?php echo $_SESSION['user_name']; ?> ▼
                        </button>
                        <div class="dropdown-menu">
                            <a href="/user/profile.php">Mi Perfil</a>
                            <a href="/user/orders.php">Mis Pedidos</a>
                            <a href="/user/reservations.php">Mis Reservas</a>
                            <a href="/user/addresses.php">Direcciones</a>
                            <a href="/logout.php">Cerrar Sesión</a>
                        </div>
                    </div>
                <?php else: ?>
                    <a href="/login.php">Ingresar</a>
                <?php endif; ?>
                <a href="/cart.php" class="cart-button">
                    Carrito <span class="cart-count">0</span>
                </a>
            </div>
        </nav>
    </header>