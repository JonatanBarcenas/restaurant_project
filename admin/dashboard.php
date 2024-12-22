<?php
require_once 'includes/admin_header.php';


$db = getConnection();

// Obtener estadísticas
$stats = [
    'daily_orders' => 0,
    'active_today' => 0,
    'next_shift' => 0,
    'daily_revenue' => 0
];

// Pedidos del día
$query = "SELECT COUNT(*) as count, SUM(total) as revenue 
          FROM orders 
          WHERE DATE(created_at) = CURDATE()";
$result = $db->query($query);
$orderStats = $result->fetch_assoc();
$stats['daily_orders'] = $orderStats['count'] ?? 0;
$stats['daily_revenue'] = $orderStats['revenue'] ?? 0;

// Personal activo hoy
$query = "SELECT COUNT(*) as count FROM staff WHERE status = 1";
$result = $db->query($query);
$activeStaff = $result->fetch_assoc();
$stats['active_today'] = $activeStaff['count'] ?? 0;

// Personal próximo turno
$query = "SELECT COUNT(*) as count FROM staff WHERE shift = 'tarde' AND status = 1";
$result = $db->query($query);
$nextShift = $result->fetch_assoc();
$stats['next_shift'] = $nextShift['count'] ?? 0;
?>

<div class="dashboard-container">
    <!-- Tarjetas de resumen -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-icon orders-icon">📦</div>
            <div class="stat-info">
                <h3>Pedidos Hoy</h3>
                <p class="stat-number"><?php echo $stats['daily_orders']; ?></p>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon staff-icon">👥</div>
            <div class="stat-info">
                <h3>Personal Activo</h3>
                <p class="stat-number"><?php echo $stats['active_today']; ?></p>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon revenue-icon">💰</div>
            <div class="stat-info">
                <h3>Ingresos del Día</h3>
                <p class="stat-number">$<?php echo number_format($stats['daily_revenue'], 2); ?></p>
            </div>
        </div>
    </div>

    <!-- Pedidos Recientes -->
    <div class="recent-orders">
        <h2>Pedidos Recientes</h2>
        <div class="table-container">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Cliente</th>
                        <th>Total</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $query = "SELECT o.*, u.name as client_name 
                             FROM orders o 
                             JOIN users u ON o.user_id = u.id 
                             ORDER BY o.created_at DESC 
                             LIMIT 10";
                    $result = $db->query($query);
                    while ($order = $result->fetch_assoc()):
                    ?>
                    <tr>
                        <td>#<?php echo $order['id']; ?></td>
                        <td><?php echo htmlspecialchars($order['client_name']); ?></td>
                        <td>$<?php echo number_format($order['total'], 2); ?></td>
                        <td>
                            <span class="status-badge status-<?php echo $order['status']; ?>">
                                <?php echo ucfirst($order['status']); ?>
                            </span>
                        </td>
                        <td>
                            <div class="table-actions">
                                <a href="orders/view.php?id=<?php echo $order['id']; ?>" class="btn btn-small">Ver</a>
                                <button class="btn btn-small btn-status" data-order-id="<?php echo $order['id']; ?>">
                                    Cambiar Estado
                                </button>
                            </div>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>

    
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

