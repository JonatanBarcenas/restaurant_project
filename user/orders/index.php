<?php
require_once '../../includes/header.php';

$db = getConnection();

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
$stmt->bind_param("i", $_SESSION['user_id']);
$stmt->execute();
$result = $stmt->get_result();
$orders = $result->fetch_all(MYSQLI_ASSOC);
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
            
            <section class="orders-section">
                <h2>Pedidos Anteriores</h2>
                <div class="orders-grid">
                    <?php
                    $hasOrders = false;
                    foreach ($orders as $order):
                        $hasOrders = true;
                    ?>
                        <div class="order-card">
                            <div class="order-header">
                                <div class="order-id-group">
                                    <span class="order-id">Pedido #<?php echo $order['id']; ?></span>
                                </div>
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
                                    <div class="detail-item">
                                        <span class="icon">📅</span>
                                        <span class="text"><?php echo formatDate($order['created_at']); ?></span>
                                    </div>
                                    <div class="detail-item">
                                        <span class="icon">📦</span>
                                        <span class="text"><?php echo $order['total_items']; ?> productos</span>
                                    </div>
                                    <div class="detail-item">
                                        <span class="icon">💰</span>
                                        <span class="text"><?php echo formatPrice($order['total']); ?></span>
                                    </div>
                                </div>
                                <div class="order-items">
                                    <p><?php echo htmlspecialchars($order['items']); ?></p>
                                </div>
                            </div>
                            <div class="order-actions">
                                <a href="details.php?id=<?php echo $order['id']; ?>" 
                                   class="action-btn edit-btn">
                                   <i class="fas fa-eye"></i> Ver Detalles
                                </a>
                                <?php if ($order['status'] == 'completed'): ?>
                                    <button class="action-btn success-btn" 
                                            onclick="reorderItems(<?php echo $order['id']; ?>)">
                                        <i class="fas fa-redo"></i> Pedir de nuevo
                                    </button>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php 
                    endforeach;
                    
                    if (!$hasOrders):
                    ?>
                        <div class="empty-state">
                            <p>No tienes pedidos anteriores</p>
                            <a href="../../index.php" class="btn btn-primary" style="text-decoration: none;">Ver Menú</a>
                        </div>
                    <?php endif; ?>
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