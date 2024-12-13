<?php
require_once '../includes/admin_header.php';

$database = new Database();
$db = $database->getConnection();

// Obtener categorías
$query = "SELECT * FROM categories";
$stmt = $db->query($query);
$categories = $stmt->fetchAll(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    try {
        // Manejar la carga de imagen
        $image_path = '';
        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $upload_dir = '../../assets/img/products/';
            $file_extension = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
            $file_name = uniqid() . '.' . $file_extension;
            $image_path = '/assets/img/products/' . $file_name;

            if (!move_uploaded_file($_FILES['image']['tmp_name'], $upload_dir . $file_name)) {
                throw new Exception('Error al subir la imagen');
            }
        }

        // Insertar producto
        $query = "INSERT INTO products (category_id, name, description, price, image, status) 
                 VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $db->prepare($query);
        $stmt->execute([
            $_POST['category_id'],
            $_POST['name'],
            $_POST['description'],
            $_POST['price'],
            $image_path,
            isset($_POST['status']) ? 1 : 0
        ]);

        header('Location: ../menu.php?msg=added');
        exit();

    } catch (Exception $e) {
        $error = $e->getMessage();
    }
}
?>

<div class="form-container">
    <div class="page-header">
        <h1>Agregar Nuevo Platillo</h1>
        <a href="../menu.php" class="btn btn-secondary">← Volver</a>
    </div>

    <?php if (isset($error)): ?>
        <div class="alert alert-danger"><?php echo $error; ?></div>
    <?php endif; ?>

    <form method="POST" enctype="multipart/form-data" class="product-form">
        <div class="form-group">
            <label for="category">Categoría*</label>
            <select id="category" name="category_id" required>
                <option value="">Seleccione una categoría</option>
                <?php foreach ($categories as $category): ?>
                    <option value="<?php echo $category['id']; ?>">
                        <?php echo htmlspecialchars($category['name']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="form-group">
            <label for="name">Nombre del Platillo*</label>
            <input type="text" id="name" name="name" required>
        </div>

        <div class="form-group">
            <label for="description">Descripción</label>
            <textarea id="description" name="description" rows="4"></textarea>
        </div>

        <div class="form-group">
            <label for="price">Precio*</label>
            <input type="number" id="price" name="price" step="0.01" required>
        </div>

        <div class="form-group">
            <label for="image">Imagen</label>
            <input type="file" id="image" name="image" accept="image/*">
            <small>Tamaño recomendado: 800x600 píxeles</small>
        </div>

        <div class="form-check">
            <input type="checkbox" id="status" name="status" checked>
            <label for="status">Activo</label>
        </div>

        <div class="form-buttons">
            <button type="submit" class="btn btn-primary">Guardar Platillo</button>
            <a href="../menu.php" class="btn btn-secondary">Cancelar</a>
        </div>
    </form>
</div>

<?php require_once '../includes/admin_footer.php'; ?>