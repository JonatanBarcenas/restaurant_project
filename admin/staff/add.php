<?php
require_once '../includes/admin_header.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    try {
        $database = new Database();
        $db = $database->getConnection();
        
        $query = "INSERT INTO staff (name, role, shift, phone, status) VALUES (?, ?, ?, ?, ?)";
        $stmt = $db->prepare($query);
        $stmt->execute([
            $_POST['name'],
            $_POST['role'],
            $_POST['shift'],
            $_POST['phone'],
            isset($_POST['status']) ? 1 : 0
        ]);
        
        header('Location: ../staff.php?msg=added');
        exit();
    } catch (Exception $e) {
        $error = "Error al agregar empleado: " . $e->getMessage();
    }
}
?>

<div class="form-container">
    <div class="page-header">
        <h1>Agregar Nuevo Empleado</h1>
        <a href="../staff.php" class="btn btn-secondary">← Volver</a>
    </div>

    <?php if (isset($error)): ?>
        <div class="alert alert-danger"><?php echo $error; ?></div>
    <?php endif; ?>

    <form method="POST" class="staff-form">
        <div class="form-group">
            <label for="name">Nombre Completo*</label>
            <input type="text" id="name" name="name" required>
        </div>

        <div class="form-group">
            <label for="role">Rol*</label>
            <select id="role" name="role" required>
                <option value="">Seleccione un rol</option>
                <option value="Chef">Chef</option>
                <option value="Mesero">Mesero</option>
                <option value="Cocinero">Cocinero</option>
                <option value="Cajero">Cajero</option>
            </select>
        </div>

        <div class="form-group">
            <label for="shift">Turno*</label>
            <select id="shift" name="shift" required>
                <option value="">Seleccione un turno</option>
                <option value="mañana">Mañana</option>
                <option value="tarde">Tarde</option>
                <option value="noche">Noche</option>
            </select>
        </div>

        <div class="form-group">
            <label for="phone">Teléfono</label>
            <input type="tel" id="phone" name="phone" pattern="[0-9]{10}">
        </div>

        <div class="form-check">
            <input type="checkbox" id="status" name="status" checked>
            <label for="status">Activo</label>
        </div>

        <div class="form-buttons">
            <button type="submit" class="btn btn-primary">Guardar Empleado</button>
            <a href="../staff.php" class="btn btn-secondary">Cancelar</a>
        </div>
    </form>
</div>

<?php require_once '../includes/admin_footer.php'; ?>