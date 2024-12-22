<?php
require_once '../includes/header.php';

if (!isset($_SESSION['user_id']) || empty($_SESSION['cart'])) {
    header('Location: cart.php');
    exit;
}

$db = getConnection();

try {
    $db->autocommit(FALSE);

    // Obtener el método de pago predeterminado
    $query = "SELECT id FROM payment_methods WHERE user_id = ? AND is_default = 1";
    $stmt = $db->prepare($query);
    $stmt->bind_param("i", $_SESSION['user_id']);
    $stmt->execute();
    $result = $stmt->get_result();
    $payment_method = $result->fetch_assoc();

    if (!$payment_method) {
        throw new Exception('No se encontró un método de pago predeterminado');
    }

    // Calcular el total
    $subtotal = 0;
    foreach ($_SESSION['cart'] as $product) {
        $subtotal += $product['price'] * $product['quantity'];
    }
    $tax = $subtotal * 0.10;
    $shipping = 5.00;
    $total = $subtotal + $tax + $shipping;

    // Crear la orden
    $query = "INSERT INTO orders (user_id, payment_method_id, total, status, created_at) 
             VALUES (?, ?, ?, 'pending', NOW())";
    $stmt = $db->prepare($query);
    $stmt->bind_param("iid", $_SESSION['user_id'], $payment_method['id'], $total);
    
    if (!$stmt->execute()) {
        throw new Exception('Error al crear la orden');
    }
    
    $order_id = $db->insert_id;

    // Insertar los detalles de la orden
    $query = "INSERT INTO order_details (order_id, product_id, quantity, price) 
             VALUES (?, ?, ?, ?)";
    $stmt = $db->prepare($query);

    foreach ($_SESSION['cart'] as $product_id => $product) {
        $stmt->bind_param("iiid", 
            $order_id, 
            $product_id, 
            $product['quantity'], 
            $product['price']
        );
        if (!$stmt->execute()) {
            throw new Exception('Error al guardar los detalles del pedido');
        }
    }

    $db->commit();
    unset($_SESSION['cart']); // Limpiar el carrito
    
    $_SESSION['success'] = "¡Pedido realizado con éxito!";
    header('Location: orders/index.php');
    exit;

} catch (Exception $e) {
    $db->rollback();
    $_SESSION['error'] = "Error al procesar el pedido: " . $e->getMessage();
    header('Location: cart.php');
    exit;
} finally {
    $db->autocommit(TRUE);
}
?>
