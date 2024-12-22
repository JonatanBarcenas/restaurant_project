<?php
require_once '../includes/admin_header.php';

$db = getConnection();
$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    try {
        $query = "INSERT INTO inventory (name, category, current_stock, minimum_stock, unit) 
                  VALUES (?, ?, ?, ?, ?)";
        $stmt = $db->prepare($query);
        $stmt->bind_param("ssdds", 
            $_POST['name'],
            $_POST['category'],
            $_POST['current_stock'],
            $_POST['minimum_stock'],
            $_POST['unit']
        );
        
        if ($stmt->execute()) {
            header('Location: ../inventory.php?msg=added');
            exit();
        } else {
            throw new Exception("Error al agregar el ingrediente");
        }
    } catch (Exception $e) {
        $error = $e->getMessage();
    }
}
?>

<div class="inventory-manager">
    <div class="page-header">
        <h1>Agregar Nuevo Ingrediente</h1>
        <a href="../inventory.php" class="btn btn-secondary">← Volver</a>
    </div>

    <?php if ($error): ?>
        <div class="alert alert-danger"><?php echo $error; ?></div>
    <?php endif; ?>

    <form method="POST" class="product-form">
        <div class="form-group">
            <label for="name">Nombre del Ingrediente*</label>
            <input type="text" id="name" name="name" required>
        </div>

        <div class="form-group">
            <label for="category">Categoría*</label>
            <select id="category" name="category" required>
                <option value="">Seleccione una categoría</option>
                <option value="Carnes">Carnes</option>
                <option value="Vegetales">Vegetales</option>
                <option value="Lácteos">Lácteos</option>
                <option value="Abarrotes">Abarrotes</option>
            </select>
        </div>

        <div class="form-group">
            <label for="current_stock">Stock Actual*</label>
            <input type="number" id="current_stock" name="current_stock" step="0.01" required>
        </div>

        <div class="form-group">
            <label for="minimum_stock">Stock Mínimo*</label>
            <input type="number" id="minimum_stock" name="minimum_stock" step="0.01" required>
        </div>

        <div class="form-group">
            <label for="unit">Unidad de Medida*</label>
            <select id="unit" name="unit" required>
                <option value="">Seleccione una unidad</option>
                <option value="kg">Kilogramos (kg)</option>
                <option value="g">Gramos (g)</option>
                <option value="l">Litros (l)</option>
                <option value="ml">Mililitros (ml)</option>
                <option value="unidad">Unidades</option>
            </select>
        </div>

        <div class="form-buttons">
            <button type="submit" class="btn btn-primary">Guardar Ingrediente</button>
            <a href="../inventory.php" class="btn btn-secondary">Cancelar</a>
        </div>
    </form>
</div>

