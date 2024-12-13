<?php
require_once '../../includes/header.php';

if (!isLoggedIn()) {
    redirect('/auth/login.php');
}

$database = new Database();
$db = $database->getConnection();

// Obtener pedidos del usuario
$query = "SELECT o.*, 
          COUNT(od.id) as total_items,
          GROUP_CONCAT(CONCAT(od.quantity, 'x ', p.name) SEPARATOR ', ') as items
          FROM orders o 
          LEFT JOIN order_details od ON o.id = od.order_id
          LEFT JOIN products p ON od.product_id = p.id
          WHERE o.user_id = ?
          GROUP BY o.id
          ORDER BY o.created_at DESC";

$stmt = $db->prepare($query);
$stmt->execute([$_SESSION['user_id']]);
$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="container">
    <div class="user-section">
        <!-- Sidebar -->
        <div class="user-sidebar">
            <?php include '../../includes/user_sidebar.php'; ?>
        </div>

        <!-- Contenido principal -->
        <div class="user-content">
            <h1>Historial de Pedidos</h1>

            <!-- Pedidos en proceso -->
            <section class="orders-section">
                <h2>Pedidos Activos</h2>
                <div class="orders-grid">
                    <?php
                    $hasActiveOrders = false;
                    foreach ($orders as $order):
                        if ($order['status'] != 'completed' && $order['status'] != 'cancelled'):
                            $hasActiveOrders = true;
                    ?>
                        <div class="order-card">
                            <div class="order-header">
                                <span class="order-number">Pedido #<?php echo $order['id']; ?></span>
                                <span class="status-badge status-<?php echo $order['status']; ?>">
                                    <?php
                                    $status_text = [
                                        'pending' => 'Pendiente',
                                        'processing' => 'En Proceso',
                                        'completed' => 'Completado',
                                        'cancelled' => 'Cancelado'
                                    ];
                                    echo $status_text[$order['status']];
                                    ?>
                                </span>
                            </div>
                            <div class="order-details">
                                <div class="detail-row">
                                    <span class="icon">📅</span>
                                    <span><?php echo formatDate($order['created_at']); ?></span>
                                </div>
                                <div class="detail-row">
                                    <span class="icon">📦</span>
                                    <span><?php echo $order['total_items']; ?> productos</span>
                                </div>
                                <div class="detail-row">
                                    <span class="icon">💰</span>
                                    <span><?php echo formatPrice($order['total']); ?></span>
                                </div>
                            </div>
                            <div class="order-items">
                                <p><?php echo htmlspecialchars($order['items']); ?></p>
                            </div>
                            <div class="order-actions">
                                <a href="details.php?id=<?php echo $order['id']; ?>" 
                                   class="btn btn-primary btn-small">Ver Detalles</a>
                                <?php if ($order['status'] == 'pending'): ?>
                                    <form method="POST" action="cancel.php" 
                                          onsubmit="return confirm('¿Está seguro de cancelar este pedido?');">
                                        <input type="hidden" name="order_id" value="<?php echo $order['id']; ?>">
                                        <button type="submit" class="btn btn-danger btn-small">Cancelar</button>
                                    </form>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php
                        endif;
                    endforeach;
                    
                    if (!$hasActiveOrders):
                    ?>
                        <div class="empty-state">
                            <p>No tienes pedidos activos</p>
                            <a href="/menu.php" class="btn btn-primary">Ver Menú</a>
                        </div>
                    <?php endif; ?>
                </div>
            </section>

            <!-- Historial de pedidos -->
            <section class="orders-section">
                <h2>Pedidos Anteriores</h2>
                <div class="table-container">
                    <table class="orders-table">
                        <thead>
                            <tr>
                                <th>Pedido #</th>
                                <th>Fecha</th>
                                <th>Productos</th>
                                <th>Total</th>
                                <th>Estado</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            foreach ($orders as $order):
                                if ($order['status'] == 'completed' || $order['status'] == 'cancelled'):
                            ?>
                                <tr>
                                    <td><?php echo $order['id']; ?></td>
                                    <td><?php echo formatDate($order['created_at']); ?></td>
                                    <td class="order-items-cell">
                                        <div class="truncate-text">
                                            <?php echo htmlspecialchars($order['items']); ?>
                                        </div>
                                    </td>
                                    <td><?php echo formatPrice($order['total']); ?></td>
                                    <td>
                                        <span class="status-badge status-<?php echo $order['status']; ?>">
                                            <?php echo $status_text[$order['status']]; ?>
                                        </span>
                                    </td>
                                    <td>
                                        <a href="details.php?id=<?php echo $order['id']; ?>" 
                                           class="btn btn-small">Ver Detalles</a>
                                        <?php if ($order['status'] == 'completed'): ?>
                                            <button class="btn btn-small btn-success" 
                                                    onclick="reorderItems(<?php echo $order['id']; ?>)">
                                                Pedir de nuevo
                                            </button>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php
                                endif;
                            endforeach;
                            ?>
                        </tbody>
                    </table>
                </div>
            </section>
        </div>
    </div>
</div>

<script>
function reorderItems(orderId) {
    if (confirm('¿Desea realizar el mismo pedido nuevamente?')) {
        // Implementar lógica para agregar items al carrito
        fetch(`/api/reorder.php?order_id=${orderId}`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    window.location.href = '/cart.php';
                } else {
                    alert('Error al procesar el pedido');
                }
            });
    }
}
</script>

<?php require_once '../../includes/footer.php'; ?>