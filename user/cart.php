<?php
require_once '../includes/header.php';

// Función para agregar un producto al carrito
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['product_id'])) {
    $productId = $_POST['product_id'];
    $productName = $_POST['product_name'];
    $productPrice = $_POST['product_price'];

    // Verificar si el carrito ya tiene productos
    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = [];
    }

    // Agregar o actualizar el producto en el carrito
    if (isset($_SESSION['cart'][$productId])) {
        // Si el producto ya existe, aumenta la cantidad
        $_SESSION['cart'][$productId]['quantity']++;
    } else {
        // Si es un producto nuevo, lo agrega al carrito
        $_SESSION['cart'][$productId] = [
            'name' => $productName,
            'price' => $productPrice,
            'quantity' => 1
        ];
    }

    // Redirigir de vuelta a la página anterior
    header('Location: ' . $_SERVER['HTTP_REFERER']);
    exit();
}
$subtotal = 0;
foreach ($_SESSION['cart'] as $product) {
    $subtotal += $product['price'] * $product['quantity'];
}
$tax = $subtotal * 0.10; // 10% de impuestos
$shipping = 5.00; // Envío fijo
$total = $subtotal + $tax + $shipping;
?>

<div class="order-container">
    <div class="order-details">
        <h1>Tu Pedido</h1>
        <?php if (!empty($_SESSION['cart'])): ?>
            <div class="order-list">
                <?php foreach ($_SESSION['cart'] as $productId => $product): ?>
                    <div class="order-item">
                        <p><?php echo htmlspecialchars($product['name']); ?></p>
                        <p>Cantidad: <?php echo $product['quantity']; ?></p>
                        <p>$<?php echo number_format($product['price'] * $product['quantity'], 2); ?></p>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <p>Tu carrito está vacío.</p>
        <?php endif; ?>
    </div>

    <div class="order-summary">
        <h2>Resumen</h2>
        <p>Subtotal: <span>$<?php echo number_format($subtotal, 2); ?></span></p>
        <p>Impuestos: <span>$<?php echo number_format($tax, 2); ?></span></p>
        <p>Envío: <span>$<?php echo number_format($shipping, 2); ?></span></p>
        <h3>Total: <span>$<?php echo number_format($total, 2); ?></span></h3>
        <button class="btn-primary">Proceder al Pago</button>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>
