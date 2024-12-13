<?php
require_once '../../includes/header.php';

if (!isLoggedIn()) {
    redirect('/auth/login.php');
}

if (!isset($_GET['id'])) {
    redirect('index.php');
}

$database = new Database();
$db = $database->getConnection();

// Obtener detalles del pedido
$query = "SELECT o.*, a.street, a.colony, a.city, a.postal_code, a.references_text 
          FROM orders o 
          LEFT JOIN addresses a ON o.address_id = a.id 
          WHERE o.id = ? AND o.user_id = ?";
$stmt = $db->prepare($query);
$stmt->execute([$_GET['id'], $_SESSION['user_id']]);
$order = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$order) {
    redirect('index.php');
}

// Obtener items del pedido
$query = "SELECT od.*, p.name as product_name, p.image 
          FROM order_details od 
          JOIN products p ON od.product_id = p.id 
          WHERE od.order_id = ?";
$stmt = $db->prepare($query);
$stmt->execute([$_GET['id']]);
$items = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="container">
    <div class="order-details">
        <div class="order-details-header">
            <div class="back-button">
                <a href="index.php" class="btn btn-link">← Volver a mis pedidos</a>
            </div>
            <h1>Detalles del Pedido #<?php echo $order['id']; ?></h1>
            <span class="status-badge status-<?php echo $order['status']; ?>">
                <?php echo ucfirst($order['status']); ?>
            </span>
        </div>

        <div class="order-details-grid">
            <!-- Información del pedido -->
            <div class="details-card">
                <h2>Información del Pedido</h2>
                <div class="details-content">
                    <div class="detail-row">
                        <span class="label">Fecha:</span>
                        <span class="value"><?php echo formatDate($order['created_at']); ?></span>
                    </div>
                    <div class="detail-row">
                        <span class="label">Estado:</span>
                        <span class="value status-<?php echo $order['status']; ?>">
                            <?php echo ucfirst($order['status']); ?>
                        </span>
                    </div>
                    <div class="detail-row">
                        <span class="label">Dirección de entrega:</span>
                        <span class="value">
                            <?php echo htmlspecialchars($order['street']); ?><br>
                            <?php echo htmlspecialchars($order['colony']); ?><br>
                            <?php echo htmlspecialchars($order['city']); ?>, 
                            CP <?php echo htmlspecialchars($order['postal_code']); ?>
                            <?php if ($order['references_text']): ?>
                                <br>Referencias: <?php echo htmlspecialchars($order['references_text']); ?>
                            <?php endif; ?>
                        </span>
                    </div>
                </div>
            </div>

            <!-- Items del pedido -->
            <div class="details-card">
                <h2>Productos</h2>
                <div class="order-items">
                    <?php foreach ($items as $item): ?>
                        <div class="order-item">
                            <?php if ($item['image']): ?>
                                <img src="<?php echo htmlspecialchars($item['image']); ?>" 
                                     alt="<?php echo htmlspecialchars($item['product_name']); ?>"
                                     class="item-image">
                            <?php endif; ?>
                            <div class="item-details">
                                <h3><?php echo htmlspecialchars($item['product_name']); ?></h3>
                                <p class="item-quantity">Cantidad: <?php echo $item['quantity']; ?></p>
                                <p class="item-price">
                                    <?php echo formatPrice($item['price']); ?> c/u
                                </p>
                                <?php if ($item['notes']): ?>
                                    <p class="item-notes">
                                        Nota: <?php echo htmlspecialchars($item['notes']); ?>
                                    </p>
                                <?php endif; ?>
                            </div>
                            <div class="item-total">
                                <?php echo formatPrice($item['price'] * $item['quantity']); ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Resumen de costos -->
            <div class="details-card order-summary">
                <h2>Resumen</h2>
                <div class="summary-content">
                    <div class="summary-row">
                        <span>Subtotal:</span>
                        <span><?php echo formatPrice($order['subtotal']); ?></span>
                    </div>
                    <div class="summary-row">
                        <span>Costo de envío:</span>
                        <span><?php echo formatPrice($order['shipping']); ?></span>
                    </div>
                    <div class="summary-row">
                        <span>IVA:</span>
                        <span><?php echo formatPrice($order['tax']); ?></span>
                    </div>
                    <div class="summary-row total">
                        <span>Total:</span>
                        <span><?php echo formatPrice($order['total']); ?></span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once '../../includes/footer.php'; ?>