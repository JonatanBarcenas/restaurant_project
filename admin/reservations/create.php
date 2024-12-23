<?php
require_once '../includes/admin_header.php';

$db = getConnection();

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    try {
        $query = "INSERT INTO reservations (date, time, guests, special_notes, table_number, status) 
                  VALUES (?, ?, ?, ?, ?, 'pending')";
        
        $stmt = $db->prepare($query);
        $special_notes = empty($_POST['special_notes']) ? null : $_POST['special_notes'];
        $table_number = empty($_POST['table_number']) ? null : $_POST['table_number'];
        
        $stmt->bind_param(
            "ssiss",
            $_POST['date'],
            $_POST['time'],
            $_POST['guests'],
            $special_notes,
            $table_number
        );
        
        if ($stmt->execute()) {
            header('Location: ../reservations.php?msg=created');
            exit();
        } else {
            throw new Exception("Error al crear la reservación");
        }
    } catch (Exception $e) {
        $error = $e->getMessage();
    }
}

$horarios = ['12:00', '13:00', '14:00', '15:00', '16:00', '17:00', '18:00', '19:00', '20:00'];
?>

<div class="page-header">
    <h1>Nueva Reservación</h1>
    <a href="../reservations.php" class="btn btn-secondary">← Volver</a>
</div>

<?php if ($error): ?>
    <div class="alert alert-danger"><?php echo $error; ?></div>
<?php endif; ?>

<div class="form-container">
    <form method="POST" class="reservation-form">
        <div class="form-group">
            <label for="date">Fecha*</label>
            <input type="date" id="date" name="date" required 
                   min="<?php echo date('Y-m-d'); ?>" 
                   value="<?php echo date('Y-m-d'); ?>">
        </div>

        <div class="form-group">
            <label for="time">Hora*</label>
            <select id="time" name="time" required>
                <?php foreach ($horarios as $hora): ?>
                    <option value="<?php echo $hora; ?>"><?php echo $hora; ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="form-group">
            <label for="guests">Número de Personas*</label>
            <input type="number" id="guests" name="guests" 
                   min="1" max="20" required>
        </div>

        <div class="form-group">
            <label for="table_number">Número de Mesa</label>
            <input type="number" id="table_number" name="table_number" 
                   min="1" max="20">
        </div>

        <div class="form-group">
            <label for="special_notes">Notas Especiales</label>
            <textarea id="special_notes" name="special_notes" 
                      rows="4"></textarea>
        </div>

        <div class="form-buttons">
            <button type="submit" class="btn btn-primary">Crear Reservación</button>
            <a href="../reservations.php" class="btn btn-secondary">Cancelar</a>
        </div>
    </form>
</div>

