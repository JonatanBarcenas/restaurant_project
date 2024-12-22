<?php
require_once '../../includes/header.php';


$error = '';
$address = null;

// Obtener dirección
if (isset($_GET['id'])) {
    
    $db = getConnection();
    
    $query = "SELECT * FROM addresses WHERE id = ? AND user_id = ?";
    $stmt = $db->prepare($query);
    $stmt->execute([$_GET['id'], $_SESSION['user_id']]);
    $address = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$address) {
        redirect('index.php');
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    try {
        $database = new Database();
        $db = $database->getConnection();
        
        // Iniciar transacción
        $db->beginTransaction();
        
        // Si se marca como principal
        if (isset($_POST['is_primary']) && !$address['is_primary']) {
            $query = "UPDATE addresses SET is_primary = 0 WHERE user_id = ?";
            $stmt = $db->prepare($query);
            $stmt->execute([$_SESSION['user_id']]);
        }
        
        // Actualizar dirección
        $query = "UPDATE addresses SET 
                    alias = ?, 
                    street = ?, 
                    colony = ?, 
                    city = ?, 
                    postal_code = ?, 
                    references_text = ?, 
                    is_primary = ?
                 WHERE id = ? AND user_id = ?";
        
        $stmt = $db->prepare($query);
        $stmt->execute([
            sanitize($_POST['alias']),
            sanitize($_POST['street']),
            sanitize($_POST['colony']),
            sanitize($_POST['city']),
            sanitize($_POST['postal_code']),
            sanitize($_POST['references']),
            isset($_POST['is_primary']) ? 1 : 0,
            $address['id'],
            $_SESSION['user_id']
        ]);
        
        $db->commit();
        redirect('index.php');
        
    } catch (Exception $e) {
        $db->rollBack();
        $error = "Error al actualizar la dirección. Por favor intente nuevamente.";
    }
}
?>

<div class="container">
    <div class="form-container">
        <h1>Editar Dirección</h1>
        
        <?php if ($error): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>

        <form method="POST" action="" class="address-form">
            <div class="form-group">
                <label for="alias">Alias de la dirección*</label>
                <input type="text" id="alias" name="alias" required 
                       value="<?php echo htmlspecialchars($address['alias']); ?>">
            </div>

            <div class="form-group">
                <label for="street">Calle y número*</label>
                <input type="text" id="street" name="street" required 
                       value="<?php echo htmlspecialchars($address['street']); ?>">
            </div>

            <!-- Resto de los campos similar al formulario de agregar -->
            
            <div class="form-check">
                <input type="checkbox" id="is_primary" name="is_primary"
                       <?php echo $address['is_primary'] ? 'checked disabled' : ''; ?>>
                <label for="is_primary">Establecer como dirección principal</label>
            </div>

            <div class="form-buttons">
                <button type="submit" class="btn btn-primary">Guardar Cambios</button>
                <a href="index.php" class="btn btn-secondary">Cancelar</a>
            </div>
        </form>
    </div>
</div>

<?php require_once '../../includes/footer.php'; ?>