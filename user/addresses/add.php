<?php
require_once '../../includes/header.php';

if (!isLoggedIn()) {
    redirect('/auth/login.php');
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    try {
        $database = new Database();
        $db = $database->getConnection();
        
        // Iniciar transacción
        $db->beginTransaction();
        
        // Si es la primera dirección o se marca como principal
        if (isset($_POST['is_primary'])) {
            // Actualizar todas las direcciones como no principales
            $query = "UPDATE addresses SET is_primary = 0 WHERE user_id = ?";
            $stmt = $db->prepare($query);
            $stmt->execute([$_SESSION['user_id']]);
        }
        
        // Insertar nueva dirección
        $query = "INSERT INTO addresses (user_id, alias, street, colony, city, postal_code, references_text, is_primary) 
                 VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $db->prepare($query);
        $stmt->execute([
            $_SESSION['user_id'],
            sanitize($_POST['alias']),
            sanitize($_POST['street']),
            sanitize($_POST['colony']),
            sanitize($_POST['city']),
            sanitize($_POST['postal_code']),
            sanitize($_POST['references']),
            isset($_POST['is_primary']) ? 1 : 0
        ]);
        
        $db->commit();
        redirect('index.php');
        
    } catch (Exception $e) {
        $db->rollBack();
        $error = "Error al guardar la dirección. Por favor intente nuevamente.";
    }
}
?>

<div class="container">
    <div class="form-container">
        <h1>Agregar Nueva Dirección</h1>
        
        <?php if ($error): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>

        <form method="POST" action="" class="address-form">
            <div class="form-group">
                <label for="alias">Alias de la dirección*</label>
                <input type="text" id="alias" name="alias" required 
                       placeholder="Ej: Casa, Trabajo, Casa de mis padres">
            </div>

            <div class="form-group">
                <label for="street">Calle y número*</label>
                <input type="text" id="street" name="street" required 
                       placeholder="Ej: Av. Principal 123">
            </div>

            <div class="form-group">
                <label for="colony">Colonia*</label>
                <input type="text" id="colony" name="colony" required 
                       placeholder="Ej: Colonia Centro">
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="city">Ciudad*</label>
                    <input type="text" id="city" name="city" required>
                </div>

                <div class="form-group">
                    <label for="postal_code">Código Postal*</label>
                    <input type="text" id="postal_code" name="postal_code" required 
                           pattern="[0-9]{5}" title="El código postal debe tener 5 dígitos">
                </div>
            </div>

            <div class="form-group">
                <label for="references">Referencias</label>
                <textarea id="references" name="references" 
                          placeholder="Ej: Entre calle A y calle B, casa blanca"></textarea>
            </div>

            <div class="form-check">
                <input type="checkbox" id="is_primary" name="is_primary">
                <label for="is_primary">Establecer como dirección principal</label>
            </div>

            <div class="form-buttons">
                <button type="submit" class="btn btn-primary">Guardar Dirección</button>
                <a href="index.php" class="btn btn-secondary">Cancelar</a>
            </div>
        </form>
    </div>
</div>

<?php require_once '../../includes/footer.php'; ?>