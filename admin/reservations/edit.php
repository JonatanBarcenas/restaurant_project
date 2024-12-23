<?php
require_once '../includes/admin_header.php';

$db = getConnection();
$error = '';
$success = '';

// Obtener reservación
if (isset($_GET['id'])) {
    $query = "SELECT * FROM reservations WHERE id = ?";
    $stmt = $db->prepare($query);
    $stmt->bind_param("i", $_GET['id']);
    $stmt->execute();
    $result = $stmt->get_result();
    $reserva = $result->fetch_assoc();
    
    if (!$reserva) {
        header('Location: ../reservations.php');
        exit();
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    try {
        $query = "UPDATE reservations SET 
                    date = ?, 
                    time = ?, 
                    guests = ?,
                    table_number = ?,
                    special_notes = ?,
                    status = ?
                 WHERE id = ?";
                 
        $stmt = $db->prepare($query);
        $special_notes = empty($_POST['special_notes']) ? null : $_POST['special_notes'];
        $table_number = empty($_POST['table_number']) ? null : $_POST['table_number'];
        
        $stmt->bind_param(
            "ssisssi",
            $_POST['date'],
            $_POST['time'],
            $_POST['guests'],
            $table_number,
            $special_notes,
            $_POST['status'],
            $_GET['id']
        );
        
        if ($stmt->execute()) {
            header('Location: ../reservations.php?msg=updated');
            exit();
        } else {
            throw new Exception("Error al actualizar la reservación");
        }
    } catch (Exception $e) {
        $error = $e->getMessage();
    }
}

$horarios = ['12:00', '13:00', '14:00', '15:00', '16:00', '17:00', '18:00', '19:00', '20:00'];
?>

<div class="page-header">
    <h1>Editar Reservación</h1>
    <a href="../reservations.php" class="btn btn-secondary">← Volver</a>
</div>

<div class="form-container">
    <form method="POST" class="reservation-form">
        <div class="form-group">
            <label for="date">Fecha*</label>
            <input type="date" id="date" name="date" required 
                   value="<?php echo $reserva['date'] ?? date('Y-m-d'); ?>">
        </div>

        <div class="form-group">
            <label for="time">Hora*</label>
            <select id="time" name="time" required>
                <?php foreach ($horarios as $hora): ?>
                    <option value="<?php echo $hora; ?>" 
                            <?php echo $hora === $reserva['time'] ? 'selected' : ''; ?>>
                        <?php echo $hora; ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="form-group">
            <label for="guests">Número de Personas*</label>
            <input type="number" id="guests" name="guests" 
                   min="1" max="20" required 
                   value="<?php echo $reserva['guests'] ?? '1'; ?>">
        </div>

        <div class="form-group">
            <label for="table_number">Número de Mesa</label>
            <input type="number" id="table_number" name="table_number" 
                   min="1" max="20" 
                   value="<?php echo $reserva['table_number'] ?? ''; ?>">
        </div>

        <div class="form-group">
            <label for="status">Estado*</label>
            <select id="status" name="status" required>
                <option value="pending" <?php echo $reserva['status'] === 'pending' ? 'selected' : ''; ?>>Pendiente</option>
                <option value="confirmed" <?php echo $reserva['status'] === 'confirmed' ? 'selected' : ''; ?>>Confirmada</option>
                <option value="cancelled" <?php echo $reserva['status'] === 'cancelled' ? 'selected' : ''; ?>>Cancelada</option>
                <option value="success" <?php echo $reserva['status'] === 'success' ? 'selected' : ''; ?>>Completada</option>
            </select>
        </div>

        <div class="form-group">
            <label for="special_notes">Notas Especiales</label>
            <textarea id="special_notes" name="special_notes" rows="4"><?php echo htmlspecialchars($reserva['special_notes'] ?? ''); ?></textarea>
        </div>

        <div class="form-buttons">
            <button type="submit" class="btn btn-primary">Guardar Cambios</button>
            <a href="../reservations.php" class="btn btn-secondary">Cancelar</a>
        </div>
    </form>
</div>

<?php require_once '../includes/admin_footer.php'; ?>
