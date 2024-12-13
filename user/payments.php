<?php
require_once '../includes/header.php';

if (!isLoggedIn()) {
    redirect('/auth/login.php');
}

$database = new Database();
$db = $database->getConnection();

// Obtener métodos de pago del usuario
$query = "SELECT * FROM payment_methods WHERE user_id = ? ORDER BY is_default DESC";
$stmt = $db->prepare($query);
$stmt->execute([$_SESSION['user_id']]);
$payment_methods = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="container">
    <div class="user-section">
        <!-- Sidebar -->
        <div class="user-sidebar">
            <?php include '../includes/user_sidebar.php'; ?>
        </div>

        <!-- Contenido principal -->
        <div class="user-content">
            <div class="content-header">
                <h1>Métodos de Pago</h1>
                <a href="payments/add.php" class="btn btn-primary">+ Agregar Método de Pago</a>
            </div>

            <?php if (isset($_GET['msg'])): ?>
                <div class="alert alert-success">
                    <?php
                    $messages = [
                        'added' => 'Método de pago agregado exitosamente',
                        'deleted' => 'Método de pago eliminado',
                        'updated' => 'Método de pago actualizado'
                    ];
                    echo $messages[$_GET['msg']] ?? '';
                    ?>
                </div>
            <?php endif; ?>

            <div class="payments-grid">
                <?php if (empty($payment_methods)): ?>
                    <div class="empty-state">
                        <p>No tienes métodos de pago guardados</p>
                        <a href="payments/add.php" class="btn btn-primary">Agregar método de pago</a>
                    </div>
                <?php else: ?>
                    <?php foreach ($payment_methods as $method): ?>
                        <div class="payment-card">
                            <div class="card-type-icon">
                                <?php
                                $card_icon = [
                                    'visa' => '💳',
                                    'mastercard' => '💳',
                                    'amex' => '💳'
                                ][$method['card_type']] ?? '💳';
                                echo $card_icon;
                                ?>
                            </div>
                            <div class="card-details">
                                <div class="card-number">
                                    •••• •••• •••• <?php echo substr($method['card_number'], -4); ?>
                                </div>
                                <div class="card-info">
                                    <span>Vence: <?php echo $method['expiry_month'].'/'.$method['expiry_year']; ?></span>
                                </div>
                                <?php if ($method['is_default']): ?>
                                    <span class="default-badge">Principal</span>
                                <?php endif; ?>
                            </div>
                            <div class="card-actions">
                                <?php if (!$method['is_default']): ?>
                                    <form method="POST" action="payments/set_default.php" class="inline-form">
                                        <input type="hidden" name="payment_id" value="<?php echo $method['id']; ?>">
                                        <button type="submit" class="btn btn-small">
                                            Establecer como principal
                                        </button>
                                    </form>

                                    <form method="POST" action="payments/delete.php" 
                                          class="inline-form" 
                                          onsubmit="return confirm('¿Está seguro de eliminar este método de pago?');">
                                        <input type="hidden" name="payment_id" value="<?php echo $method['id']; ?>">
                                        <button type="submit" class="btn btn-small btn-danger">
                                            Eliminar
                                        </button>
                                    </form>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>