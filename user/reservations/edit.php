<?php
require_once '../../includes/header.php';


if (!isset($_SESSION['user_id'])) {
    header('Location: ../../login.php');
    exit;
}

$db = getConnection();
$reservation_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Verificar que la reservación pertenezca al usuario
$query = "SELECT * FROM reservations WHERE id = ? AND user_id = ? AND status = 'pending'";
$stmt = $db->prepare($query);
$stmt->bind_param("ii", $reservation_id, $_SESSION['user_id']);
$stmt->execute();
$result = $stmt->get_result();
$reservation = $result->fetch_assoc();

if (!$reservation) {
    header('Location: reservations.php');
    exit;
}

// Procesar el formulario de actualización
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $date = $_POST['date'];
    $time = $_POST['time'];
    $guests = (int)$_POST['guests'];
    $special_notes = $_POST['special_notes'];

    $update_query = "UPDATE reservations SET 
                    date = ?, 
                    time = ?, 
                    guests = ?, 
                    special_notes = ? 
                    WHERE id = ? AND user_id = ?";
    
    $update_stmt = $db->prepare($update_query);
    $update_stmt->bind_param("ssisii", $date, $time, $guests, $special_notes, $reservation_id, $_SESSION['user_id']);
    
    if ($update_stmt->execute()) {
        header('Location: reservations.php');
        exit;
    }
}
?>

<div class="container">
    <h2>Modificar Reservación</h2>
    <form method="POST" class="reservation-form">
        <div class="form-group">
            <label for="date">Fecha</label>
            <input type="date" id="date" name="date" value="<?php echo $reservation['date']; ?>" required>
        </div>

        <div class="form-group">
            <label for="time">Hora</label>
            <input type="time" id="time" name="time" value="<?php echo $reservation['time']; ?>" required>
        </div>

        <div class="form-group">
            <label for="guests">Número de personas</label>
            <input type="number" id="guests" name="guests" value="<?php echo $reservation['guests']; ?>" min="1" max="10" required>
        </div>

        <div class="form-group">
            <label for="special_notes">Notas especiales</label>
            <textarea id="special_notes" name="special_notes"><?php echo isset($reservation['special_notes']) ? htmlspecialchars($reservation['special_notes']) : ''; ?></textarea>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn btn-primary">Guardar cambios</button>
            <a href="reservations.php" class="btn btn-secondary">Cancelar</a>
        </div>
    </form>
</div>

<?php require_once '../../includes/footer.php'; ?>
