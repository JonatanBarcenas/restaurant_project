<?php
require_once '../includes/header.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: /restaurant_project/auth/login.php');
    exit();
}

$db = getConnection();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    try {
        $query = "INSERT INTO reservations (user_id, date, time, guests, special_notes, table_number, status) 
                  VALUES (?, ?, ?, ?, ?, ?, 'pending')";
        $stmt = $db->prepare($query);
        $stmt->bind_param("issssi", 
            $_SESSION['user_id'],
            $_POST['date'],
            $_POST['time'],
            $_POST['guests'],
            $_POST['special_notes'],
            $_POST['table_number']
        );
        
        if ($stmt->execute()) {
            $success = "Reservación realizada con éxito";
        }
    } catch (Exception $e) {
        $error = "Error al procesar la reservación: " . $e->getMessage();
    }
}
?>

<div class="containerR">
    <?php if (isset($success)): ?>
        <div class="alert alert-success"><?php echo $success; ?></div>
    <?php endif; ?>
    
    <?php if (isset($error)): ?>
        <div class="alert alert-danger"><?php echo $error; ?></div>
    <?php endif; ?>

    <div class="form-sectionR">
        <h2>Reserva tu Mesa</h2>
        <form method="POST" id="reservationForm">
            <input type="hidden" id="table_number" name="table_number" value="">
            
            <label for="date">Fecha</label>
            <input type="date" id="date" name="date" required min="<?php echo date('Y-m-d'); ?>">

            <label for="time">Hora</label>
            <select id="time" name="time" required>
                <option value="">Seleccione una hora</option>
                <?php
                    $start = strtotime('12:00');
                    $end = strtotime('22:00');
                    for ($i = $start; $i <= $end; $i += 1800) {
                        echo '<option value="' . date('H:i', $i) . '">' . date('h:i A', $i) . '</option>';
                    }
                ?>
            </select>

            <label for="guests">Número de Personas</label>
            <input type="number" id="guests" name="guests" min="1" max="10" required>

            <label for="occasion">Ocasión Especial</label>
            <input type="text" id="occasion" name="occasion" placeholder="Cumpleaños, Aniversario, etc.">

            <button type="submit" id="submitReservation" disabled>Reservar Ahora</button>
        </form>
    </div>

    <div class="map-section">
        <h2>Selecciona una Mesa</h2>
        <div class="tables-container">
            <?php for($i = 1; $i <= 10; $i++): ?>
                <div class="table available" data-table="<?php echo $i; ?>"><?php echo $i; ?></div>
            <?php endfor; ?>
        </div>
        <div class="legend">
            <div><span class="available-color"></span> Disponible</div>
            <div><span class="occupied-color"></span> Ocupada</div>
            <div><span class="selected-color"></span> Seleccionada</div>
        </div>
    </div>
</div>

<script src="/restaurant_project/assets/js/reservations.js"></script>
<?php require_once '../includes/footer.php'; ?>