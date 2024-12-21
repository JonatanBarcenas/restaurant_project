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

    <link rel="stylesheet" href="/restaurant_project/assets/css/specialties.css">
    <link rel="stylesheet" href="/restaurant_project/assets/css/cart.css">
    <link rel="stylesheet" href="/restaurant_project/assets/css/auth.css">
    <link rel="stylesheet" href="/restaurant_project/assets/css/footer.css">
    <link rel="stylesheet" href="/restaurant_project/assets/css/navbar.css">
    <link rel="stylesheet" href="/restaurant_project/assets/css/index.css">
</head>
<body>
    <header>
        <nav>
            <div class="logo">
                <h1>Sabores Auténticos</h1>
            </div>
            <div class="menu">
                <a href="/restaurant_project/index.php">Inicio</a>
                <a href="/restaurant_project/menu.php">Menú</a>
                <a href="/restaurant_project/user/reservations.php">Reservar</a>
                <?php if(isset($_SESSION['user_id'])): ?>
                    <div class="user-menu">
                        <button class="user-button">
                            <?php echo $_SESSION['user_name']; ?> ▼
                        </button>
                        <div class="dropdown-menu">
                            <a href="/restaurant_project/user/profile.php">Mi Perfil</a>
                            <a href="/restaurant_project/user/orders.php">Mis Pedidos</a>
                            <a href="/restaurant_project/user/reservations.php">Mis Reservas</a>
                            <a href="/restaurant_project/user/addresses.php">Direcciones</a>
                            <a href="/restaurant_project/user/payments.php">Métodos de Pago</a>
                            <a href="/restaurant_project/auth/logout.php">Cerrar Sesión</a>
                        </div>
                    </div>
                <?php else: ?>
                    <a href="/restaurant_project/auth/login.php">Ingresar</a>
                <?php endif; ?>
                <a href="/restaurant_project/user/cart.php" class="cart-button">
                    Carrito <span class="cart-count"></span>
                </a>
            </div>
        </nav>
    </header>
</body>
</html>