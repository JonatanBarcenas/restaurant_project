<?php
require_once '../includes/admin_header.php';

$db = getConnection();
$error = '';
$success = '';

// Obtener el producto
if (isset($_GET['id'])) {
    $query = "SELECT * FROM products WHERE id = ?";
    $stmt = $db->prepare($query);
    $stmt->bind_param("i", $_GET['id']);
    $stmt->execute();
    $result = $stmt->get_result();
    $product = $result->fetch_assoc();
    
    if (!$product) {
        header('Location: ../menu.php');
        exit();
    }
}

// Obtener categorías
$query = "SELECT * FROM categories";
$result = $db->query($query);
$categories = array();
while ($row = $result->fetch_assoc()) {
    $categories[] = $row;
}

// Procesar formulario de edición
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    try {
        $name = sanitize($_POST['name']);
        $description = sanitize($_POST['description']);
        $price = floatval($_POST['price']);
        $category_id = intval($_POST['category_id']);
        $status = isset($_POST['status']) ? 1 : 0;
        
        $query = "UPDATE products SET 
                    name = ?, 
                    description = ?, 
                    price = ?, 
                    category_id = ?, 
                    status = ?
                 WHERE id = ?";
                 
        $stmt = $db->prepare($query);
        $stmt->bind_param("ssdiis", $name, $description, $price, $category_id, $status, $_GET['id']);
        
        if ($stmt->execute()) {
            $success = "Producto actualizado correctamente";
        } else {
            throw new Exception("Error al actualizar el producto");
        }
        
    } catch (Exception $e) {
        $error = $e->getMessage();
    }
}
?>

<div class="menu-manager">
    <div class="page-header">
        <h1>Editar Producto</h1>
        <a href="../menu.php" class="btn btn-secondary">← Volver</a>
    </div>

    <?php if ($error): ?>
        <div class="alert alert-danger"><?php echo $error; ?></div>
    <?php endif; ?>
    
    <?php if ($success): ?>
        <div class="alert alert-success"><?php echo $success; ?></div>
    <?php endif; ?>

    <form method="POST" class="product-form">
        <div class="form-group">
            <label for="category_id">Categoría*</label>
            <select id="category_id" name="category_id" required>
                <?php foreach ($categories as $category): ?>
                    <option value="<?php echo $category['id']; ?>" 
                            <?php echo $category['id'] == $product['category_id'] ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($category['name']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="form-group">
            <label for="name">Nombre*</label>
            <input type="text" id="name" name="name" required 
                   value="<?php echo htmlspecialchars($product['name']); ?>">
        </div>

        <div class="form-group">
            <label for="description">Descripción</label>
            <textarea id="description" name="description"><?php echo htmlspecialchars($product['description']); ?></textarea>
        </div>

        <div class="form-group">
            <label for="price">Precio*</label>
            <input type="number" id="price" name="price" step="0.01" required 
                   value="<?php echo $product['price']; ?>">
        </div>

        <div class="form-check">
            <input type="checkbox" id="status" name="status" 
                   <?php echo $product['status'] ? 'checked' : ''; ?>>
            <label for="status">Activo</label>
        </div>

        <div class="form-buttons">
            <button type="submit" class="btn btn-primary">Guardar Cambios</button>
            <a href="../menu.php" class="btn btn-secondary">Cancelar</a>
        </div>
    </form>
</div>

