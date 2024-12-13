<?php
require_once '../../includes/header.php';

if (!isLoggedIn()) {
    redirect('/auth/login.php');
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    try {
        $database = new Database();
        $db = $database->getConnection();
        
        // Validar fecha de expiración
        $expiry_month = $_POST['expiry_month'];
        $expiry_year = $_POST['expiry_year'];
        
        if (!checkdate($expiry_month, 1, $expiry_year)) {
            throw new Exception('Fecha de expiración inválida');
        }

        // Inicio de transacción
        $db->beginTransaction();

        // Si es el primer método de pago o se marca como predeterminado
        if (isset($_POST['is_default'])) {
            $query = "UPDATE payment_methods SET is_default = 0 WHERE user_id = ?";
            $stmt = $db->prepare($query);
            $stmt->execute([$_SESSION['user_id']]);
        }

        // Insertar nuevo método de pago
        $query = "INSERT INTO payment_methods (
                    user_id, card_type, card_number, cardholder_name,
                    expiry_month, expiry_year, is_default
                ) VALUES (?, ?, ?, ?, ?, ?, ?)";
        
        $stmt = $db->prepare($query);
        $stmt->execute([
            $_SESSION['user_id'],
            $_POST['card_type'],
            $_POST['card_number'],
            $_POST['cardholder_name'],
            $expiry_month,
            $expiry_year,
            isset($_POST['is_default']) ? 1 : 0
        ]);

        $db->commit();
        redirect('../payments.php?msg=added');

    } catch (Exception $e) {
        $db->rollBack();
        $error = $e->getMessage();
    }
}
?>

<div class="container">
    <div class="form-container">
        <div class="form-header">
            <h1>Agregar Método de Pago</h1>
            <a href="../payments.php" class="btn btn-link">← Volver</a>
        </div>

        <?php if (isset($error)): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>

        <form method="POST" class="payment-form" id="paymentForm">
            <div class="form-group">
                <label for="card_type">Tipo de Tarjeta*</label>
                <select id="card_type" name="card_type" required>
                    <option value="">Seleccione tipo de tarjeta</option>
                    <option value="visa">Visa</option>
                    <option value="mastercard">Mastercard</option>
                    <option value="amex">American Express</option>
                </select>
            </div>

            <div class="form-group">
                <label for="card_number">Número de Tarjeta*</label>
                <input type="text" id="card_number" name="card_number" 
                       pattern="[0-9]{16}" maxlength="16" required
                       placeholder="1234 5678 9012 3456">
            </div>

            <div class="form-group">
                <label for="cardholder_name">Nombre del Titular*</label>
                <input type="text" id="cardholder_name" name="cardholder_name" required>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label>Fecha de Expiración*</label>
                    <div class="expiry-inputs">
                        <select name="expiry_month" required>
                            <option value="">Mes</option>
                            <?php for ($i = 1; $i <= 12; $i++): ?>
                                <option value="<?php echo str_pad($i, 2, '0', STR_PAD_LEFT); ?>">
                                    <?php echo str_pad($i, 2, '0', STR_PAD_LEFT); ?>
                                </option>
                            <?php endfor; ?>
                        </select>
                        <select name="expiry_year" required>
                            <option value="">Año</option>
                            <?php 
                            $current_year = date('Y');
                            for ($i = $current_year; $i <= $current_year + 10; $i++):
                            ?>
                                <option value="<?php echo $i; ?>"><?php echo $i; ?></option>
                            <?php endfor; ?>
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <label for="cvv">CVV*</label>
                    <input type="text" id="cvv" pattern="[0-9]{3,4}" maxlength="4" required>
                    <small class="form-text">Este número no será almacenado</small>
                </div>
            </div>

            <div class="form-check">
                <input type="checkbox" id="is_default" name="is_default">
                <label for="is_default">Establecer como método de pago principal</label>
            </div>

            <div class="form-buttons">
                <button type="submit" class="btn btn-primary">Guardar Tarjeta</button>
                <a href="../payments.php" class="btn btn-secondary">Cancelar</a>
            </div>
        </form>
    </div>
</div>

<script>
// Validación del formulario y formato de tarjeta
document.getElementById('paymentForm').addEventListener('submit', function(e) {
    const cardNumber = document.getElementById('card_number').value.replace(/\s/g, '');
    const cvv = document.getElementById('cvv').value;
    
    if (!/^\d{16}$/.test(cardNumber)) {
        e.preventDefault();
        alert('Número de tarjeta inválido');
        return;
    }

    if (!/^\d{3,4}$/.test(cvv)) {
        e.preventDefault();
        alert('CVV inválido');
        return;
    }
});

// Formato de número de tarjeta mientras se escribe
document.getElementById('card_number').addEventListener('input', function(e) {
    let value = e.target.value.replace(/\D/g, '');
    if (value.length > 16) value = value.slice(0, 16);
    e.target.value = value;
});

// Formato de CVV
document.getElementById('cvv').addEventListener('input', function(e) {
    let value = e.target.value.replace(/\D/g, '');
    if (value.length > 4) value = value.slice(0, 4);
    e.target.value = value;
});
</script>

<?php require_once '../../includes/footer.php'; ?>