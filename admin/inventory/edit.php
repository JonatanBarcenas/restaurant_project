<?php
require_once '../includes/admin_header.php';

$db = getConnection();
$error = '';
$success = '';

// Obtener el ingrediente
if (isset($_GET['id'])) {
    $query = "SELECT * FROM inventory WHERE id = ?";
    $stmt = $db->prepare($query);
    $stmt->bind_param("i", $_GET['id']);
    $stmt->execute();
    $result = $stmt->get_result();
    $item = $result->fetch_assoc();
    
    if (!$item) {
        header('Location: ../inventory.php');
        exit();
    }
}

// Procesar formulario de edición
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    try {
        $query = "UPDATE inventory SET 
                    name = ?, 
                    category = ?, 
                    current_stock = ?,
                    minimum_stock = ?,
                    unit = ?
                 WHERE id = ?";
                 
        $stmt = $db->prepare($query);
        $stmt->bind_param("ssddsi", 
            $_POST['name'],
            $_POST['category'],
            $_POST['current_stock'],
            $_POST['minimum_stock'],
            $_POST['unit'],
            $_GET['id']
        );
        
        if ($stmt->execute()) {
            $success = "Ingrediente actualizado correctamente";
        } else {
            throw new Exception("Error al actualizar el ingrediente");
        }
        
    } catch (Exception $e) {
        $error = $e->getMessage();
    }
}
?>

<div class="inventory-manager">
    <div class="page-header">
        <h1>Editar Ingrediente</h1>
        <a href="../inventory.php" class="btn btn-secondary">← Volver</a>
    </div>

    <?php if ($error): ?>
        <div class="alert alert-danger"><?php echo $error; ?></div>
    <?php endif; ?>
    
    <?php if ($success): ?>
        <div class="alert alert-success"><?php echo $success; ?></div>
    <?php endif; ?>

    <form method="POST" class="product-form">
        <div class="form-group">
            <label for="name">Nombre del Ingrediente*</label>
            <input type="text" id="name" name="name" required 
                   value="<?php echo htmlspecialchars($item['name']); ?>">
        </div>

        <div class="form-group">
            <label for="category">Categoría*</label>
            <select id="category" name="category" required>
                <option value="Carnes" <?php echo $item['category'] == 'Carnes' ? 'selected' : ''; ?>>Carnes</option>
                <option value="Vegetales" <?php echo $item['category'] == 'Vegetales' ? 'selected' : ''; ?>>Vegetales</option>
                <option value="Lácteos" <?php echo $item['category'] == 'Lácteos' ? 'selected' : ''; ?>>Lácteos</option>
                <option value="Abarrotes" <?php echo $item['category'] == 'Abarrotes' ? 'selected' : ''; ?>>Abarrotes</option>
            </select>
        </div>

        <div class="form-group">
            <label for="current_stock">Stock Actual*</label>
            <input type="number" id="current_stock" name="current_stock" step="0.01" required
                   value="<?php echo $item['current_stock']; ?>">
        </div>

        <div class="form-group">
            <label for="minimum_stock">Stock Mínimo*</label>
            <input type="number" id="minimum_stock" name="minimum_stock" step="0.01" required
                   value="<?php echo $item['minimum_stock']; ?>">
        </div>

        <div class="form-group">
            <label for="unit">Unidad de Medida*</label>
            <select id="unit" name="unit" required>
                <option value="kg" <?php echo $item['unit'] == 'kg' ? 'selected' : ''; ?>>Kilogramos (kg)</option>
                <option value="g" <?php echo $item['unit'] == 'g' ? 'selected' : ''; ?>>Gramos (g)</option>
                <option value="l" <?php echo $item['unit'] == 'l' ? 'selected' : ''; ?>>Litros (l)</option>
                <option value="ml" <?php echo $item['unit'] == 'ml' ? 'selected' : ''; ?>>Mililitros (ml)</option>
                <option value="unidad" <?php echo $item['unit'] == 'unidad' ? 'selected' : ''; ?>>Unidades</option>
            </select>
        </div>

        <div class="form-buttons">
            <button type="submit" class="btn btn-primary">Guardar Cambios</button>
            <a href="../inventory.php" class="btn btn-secondary">Cancelar</a>
        </div>
    </form>
</div>
