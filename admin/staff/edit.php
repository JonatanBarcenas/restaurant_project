<?php
require_once '../includes/admin_header.php';

$db = getConnection();
$error = '';
$success = '';

// Obtener empleado
if (isset($_GET['id'])) {
    $query = "SELECT * FROM staff WHERE id = ?";
    $stmt = $db->prepare($query);
    $stmt->bind_param("i", $_GET['id']);
    $stmt->execute();
    $result = $stmt->get_result();
    $employee = $result->fetch_assoc();
    
    if (!$employee) {
        header('Location: ../staff.php');
        exit();
    }
}

// Procesar formulario
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    try {
        $query = "UPDATE staff SET 
                    name = ?,
                    role = ?,
                    shift = ?,
                    status = ?
                 WHERE id = ?";
                 
        $stmt = $db->prepare($query);
        
        // Preparar las variables
        $name = $_POST['name'];
        $role = $_POST['role'];
        $shift = $_POST['shift'];
        $status = isset($_POST['status']) ? 1 : 0;
        $id = $_GET['id'];
        
        // Vincular los parámetros
        $stmt->bind_param("sssii", $name, $role, $shift, $status, $id);
        
        if ($stmt->execute()) {
            $success = "Empleado actualizado correctamente";
        } else {
            throw new Exception("Error al actualizar el empleado");
        }
        
    } catch (Exception $e) {
        $error = $e->getMessage();
    }
}
?>

<div class="staff-manager">
    <div class="page-header">
        <h1>Editar Empleado</h1>
        <a href="../staff.php" class="btn btn-secondary">← Volver</a>
    </div>

    <?php if ($error): ?>
        <div class="alert alert-danger"><?php echo $error; ?></div>
    <?php endif; ?>
    
    <?php if ($success): ?>
        <div class="alert alert-success"><?php echo $success; ?></div>
    <?php endif; ?>

    <form method="POST" class="staff-form">
        <div class="form-group">
            <label for="name">Nombre*</label>
            <input type="text" id="name" name="name" required 
                   value="<?php echo htmlspecialchars($employee['name']); ?>">
        </div>

        <div class="form-group">
            <label for="role">Rol*</label>
            <select id="role" name="role" required>
                <option value="Chef" <?php echo $employee['role'] == 'Chef' ? 'selected' : ''; ?>>Chef</option>
                <option value="Mesero" <?php echo $employee['role'] == 'Mesero' ? 'selected' : ''; ?>>Mesero</option>
                <option value="Cocinero" <?php echo $employee['role'] == 'Cocinero' ? 'selected' : ''; ?>>Cocinero</option>
                <option value="Cajero" <?php echo $employee['role'] == 'Cajero' ? 'selected' : ''; ?>>Cajero</option>
            </select>
        </div>

        <div class="form-group">
            <label for="shift">Turno*</label>
            <select id="shift" name="shift" required>
                <option value="mañana" <?php echo $employee['shift'] == 'mañana' ? 'selected' : ''; ?>>Mañana</option>
                <option value="tarde" <?php echo $employee['shift'] == 'tarde' ? 'selected' : ''; ?>>Tarde</option>
            </select>
        </div>

    

        <div class="form-check">
            <input type="checkbox" id="status" name="status" 
                   <?php echo $employee['status'] ? 'checked' : ''; ?>>
            <label for="status">Activo</label>
        </div>

        <div class="form-buttons">
            <a href="../staff.php"><button type="submit" class="btn btn-primary">Guardar Cambios</button></a>
            
            <a href="../staff.php" class="btn btn-secondary">Cancelar</a>
        </div>
    </form>
</div>

