<?php
require_once 'includes/admin_header.php';

$db = getConnection();

// Obtener estadísticas
$query = "SELECT COUNT(*) as total FROM reservations WHERE DATE(date) = CURDATE()";
$result = $db->query($query);
$reservas_hoy = $result->fetch_assoc()['total'];

// Total de mesas disponibles (asumiendo 20 mesas en total)
$query = "SELECT COUNT(*) as ocupadas FROM reservations 
          WHERE DATE(date) = CURDATE() 
          AND status NOT IN ('cancelled')";
$result = $db->query($query);
$mesas_ocupadas = $result->fetch_assoc()['ocupadas'];
$mesas_disponibles = 20 - $mesas_ocupadas; // Ajusta este número según tu restaurante

// Próximas reservaciones
$hora_actual = date('H:i:s');
$query = "SELECT COUNT(*) as total FROM reservations 
          WHERE DATE(date) = CURDATE() 
          AND time > ? 
          AND status = 'confirmed'
          ORDER BY time ASC LIMIT 1";
$stmt = $db->prepare($query);
$stmt->bind_param('s', $hora_actual);
$stmt->execute();
$result = $stmt->get_result();
$proxima_hora = $result->fetch_assoc()['total'];

?>

<div class="dashboardA">
    <div class="dashboard-headerA">
        <div class="summary-cardsA">
            <div class="summary-cardA">
                <h3>Reservas Hoy</h3>
                <p><?php echo $reservas_hoy; ?></p>
            </div>
            <div class="summary-cardA available">
                <h3>Mesas Disponibles</h3>
                <p><?php echo $mesas_disponibles; ?></p>
            </div>
            <div class="summary-cardA next-hour">
                <h3>Próxima Hora</h3>
                <p><?php echo $proxima_hora; ?></p>
            </div>
        </div>
        <div class="controls-rowA">
            <a href="reservations/create.php" class="btn-new-reservation">+ Nueva Reserva</a>
            <input type="date" class="date-pickerA" value="<?php echo date('Y-m-d'); ?>" 
                   onchange="filterReservations(this.value)">
        </div>
    </div>

    <div class="dashboard-bodyA">
        <div class="scheduleA">
            <h4>Horarios</h4>
            <?php
            $horarios = ['12:00', '13:00', '14:00', '15:00', '16:00', '17:00', '18:00', '19:00', '20:00'];
            foreach ($horarios as $hora) {
                $query = "SELECT COUNT(*) as total FROM reservations 
                         WHERE DATE(date) = CURDATE() 
                         AND TIME(time) = ?
                         AND status NOT IN ('cancelled')";
                $stmt = $db->prepare($query);
                $hora_completa = $hora . ':00';
                $stmt->bind_param('s', $hora_completa);
                $stmt->execute();
                $result = $stmt->get_result();
                $reservas = $result->fetch_assoc()['total'];
                
                $clase = $reservas > 0 ? 'reserved' : 'available';
                echo "<button class='schedule-timeA $clase'>$hora</button>";
            }
            ?>
        </div>

        <div class="reservationsA">
            

            <h4>Reservas Actuales</h4>
            <table class="reservations-tableA">
                <tr>
                    <th>Mesa</th>
                    <th>Cliente</th>
                    <th>Fecha</th>
                    <th>Hora</th>
                    <th>Personas</th>
                  
                    <th>Estado</th>
                    <th>Acciones</th>
                </tr>
                <?php
                // Modificar la consulta para obtener todas las reservaciones
                $query = "SELECT r.*, u.name as client_name 
                         FROM reservations r
                         LEFT JOIN users u ON r.user_id = u.id
                         ORDER BY r.date DESC, r.time ASC";
                $result = $db->query($query);

                if ($result && $result->num_rows > 0) {
                    while ($reserva = $result->fetch_assoc()):
                ?>
                <tr>
                    <td>Mesa <?php echo $reserva['table_number'] ?: 'Sin asignar'; ?></td>
                    <td><?php echo htmlspecialchars($reserva['client_name'] ?: 'Cliente no registrado'); ?></td>
                    <td><?php echo date('d/m/Y', strtotime($reserva['date'])); ?></td>
                    <td><?php echo date('H:i', strtotime($reserva['time'])); ?></td>
                    <td><?php echo $reserva['guests']; ?> personas</td>

                    <td>
                        <span class="status-badge status-<?php echo $reserva['status']; ?>">
                            <?php 
                            $estados = [
                                'pending' => 'Pendiente',
                                'confirmed' => 'Confirmada',
                                'cancelled' => 'Cancelada',
                                'success' => 'Completada'
                            ];
                            echo $estados[$reserva['status']] ?? ucfirst($reserva['status']); 
                            ?>
                        </span>
                    </td>
                    <td>
                        <div class="table-actions">
                            <button class="btn btn-small btn-edit" 
                                    onclick="editReservation(<?php echo $reserva['id']; ?>)">
                                ✏️
                            </button>
                            <button class="btn btn-small btn-delete" 
                                    onclick="deleteReservation(<?php echo $reserva['id']; ?>)">
                                🗑️
                            </button>
                        </div>
                    </td>
                </tr>
                <?php 
                    endwhile;
                } else {
                    echo "<tr><td colspan='8' style='text-align: center;'>No hay reservaciones registradas</td></tr>";
                }
                ?>
            </table>
        </div>
    </div>
</div>

<script>
function filterReservations(date) {
    // Implementar filtrado por fecha
    window.location.href = `reservations.php?date=${date}`;
}

function editReservation(id) {
    // Implementar edición
    window.location.href = `reservations/edit.php?id=${id}`;
}

function deleteReservation(id) {
    if (confirm('¿Está seguro de eliminar esta reservación?')) {
        // Implementar eliminación
        window.location.href = `reservations/delete.php?id=${id}`;
    }
}
</script>

