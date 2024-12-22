<div class="sidebar-menu">
    <div class="user-info">
        <div class="user-avatar">
            <i class="fas fa-user-circle"></i>
        </div>
        <span class="user-name"><?php echo htmlspecialchars($_SESSION['user_name']); ?></span>
    </div>
    
    <nav class="sidebar-nav">
        <!-- Cuenta -->
        <div class="nav-section">
            <h3>Mi Cuenta</h3>
            <a href="/restaurant_project/user/profile.php" class="<?php echo strpos($_SERVER['REQUEST_URI'], 'profile.php') ? 'active' : ''; ?>">
                <i class="fas fa-user"></i> Mi Perfil
            </a>
            <a href="/restaurant_project/user/addresses/index.php" class="<?php echo strpos($_SERVER['REQUEST_URI'], 'addresses.php') ? 'active' : ''; ?>">
                <i class="fas fa-map-marker-alt"></i> Mis Direcciones
            </a>
            <a href="/restaurant_project/user/payments.php" class="<?php echo strpos($_SERVER['REQUEST_URI'], 'payments.php') ? 'active' : ''; ?>">
                <i class="fas fa-credit-card"></i> Métodos de Pago
            </a>

        </div>

        <!-- Pedidos -->
        <div class="nav-section">
            <h3>Mis Pedidos</h3>
            <a href="/restaurant_project/user/orders/index.php" class="<?php echo strpos($_SERVER['REQUEST_URI'], 'orders.php') ? 'active' : ''; ?>">
                <i class="fas fa-shopping-bag"></i> Historial de Pedidos
            </a>
            <a href="/restaurant_project/user/reservations/reservations.php" class="<?php echo strpos($_SERVER['REQUEST_URI'], 'reservations.php') ? 'active' : ''; ?>">
                <i class="fas fa-calendar-alt"></i> Mis Reservas
            </a>
        </div>
    </nav>

    <!-- Botón de cerrar sesión -->
    <div class="logout-section">
        <a href="../auth/logout.php" class="logout-button">
            <i class="fas fa-sign-out-alt"></i> Cerrar Sesión
        </a>
    </div>
</div>