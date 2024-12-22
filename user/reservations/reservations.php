<?php
require_once '../../includes/header.php';

$db = getConnection();

// Obtener reservaciones del usuario
$query = "SELECT * FROM reservations WHERE user_id = ? ORDER BY date DESC, time DESC";
$stmt = $db->prepare($query);
$stmt->bind_param("i", $_SESSION['user_id']);
$stmt->execute();
$result = $stmt->get_result();
$reservations = $result->fetch_all(MYSQLI_ASSOC);
?>

<div class="container">
    <div class="user-section">
        <!-- Sidebar de navegación del usuario -->
        <div class="user-sidebar">
            <?php include '../../includes/user_sidebar.php'; ?>
        </div>

        <!-- Contenido principal -->
        <div class="user-content">
            <div class="content-header">
                <h1>Mis Reservaciones</h1>
                <a href="../do_reservation.php" class="btn btn-primary" style="text-decoration: none;">Nueva Reservación</a>
            </div>

            <!-- Reservaciones Activas -->
            <section class="reservations-section">
                <h2>Reservaciones Activas</h2>
                <div class="reservations-grid">
                    <?php 
                    $hasActive = false;
                    foreach ($reservations as $reservation):
                        if ($reservation['status'] == 'pending' || $reservation['status'] == 'confirmed'):
                            $hasActive = true;
                    ?>
                        <div class="reservation-card">
                            <div class="reservation-header">
                                <div class="reservation-id-group">
                                    <span class="reservation-id">Reserva#<?php echo $reservation['id']; ?></span>
                                    <span class="table-number">Mesa <?php echo $reservation['table_number']; ?></span>
                                </div>
                                <span class="status-badge status-<?php echo $reservation['status']; ?>">
                                    <?php echo ucfirst($reservation['status']); ?>
                                </span>
                            </div>
                            <div class="reservation-details">
                                <div class="detail-row">
                                    <div class="detail-item">
                                        <span class="icon">📅</span>
                                        <span class="text"><?php echo formatDate($reservation['date']); ?></span>
                                    </div>
                                    <div class="detail-item">
                                        <span class="icon">⏰</span>
                                        <span class="text"><?php echo formatTime($reservation['time']); ?></span>
                                    </div>
                                    <div class="detail-item">
                                        <span class="icon">👥</span>
                                        <span class="text"><?php echo $reservation['guests']; ?> personas</span>
                                    </div>
                                </div>
                                <?php if (isset($reservation['special_notes']) && !empty($reservation['special_notes'])): ?>
                                    <div class="reservation-notes">
                                        <small><?php echo htmlspecialchars($reservation['special_notes']); ?></small>
                                    </div>
                                <?php endif; ?>
                            </div>
                            <?php if ($reservation['status'] == 'pending'): ?>
                                <div class="reservation-actions">
                                    <a href="edit.php?id=<?php echo $reservation['id']; ?>" 
                                       class="action-btn edit-btn">
                                       <i class="fas fa-edit"></i> Modificar
                                    </a>
                                    <form method="POST" action="cancel.php" 
                                          onsubmit="return confirm('¿Está seguro de cancelar esta reservación?');">
                                        <input type="hidden" name="reservation_id" value="<?php echo $reservation['id']; ?>">
                                        <button type="submit" class="action-btn delete-btn">
                                            <i class="fas fa-times"></i> Cancelar
                                        </button>
                                    </form>
                                </div>
                            <?php endif; ?>
                        </div>
                    <?php 
                        endif;
                    endforeach;
                    
                    if (!$hasActive):
                    ?>
                        <div class="empty-state">
                            <p>No tienes reservaciones activas</p>
                            <a href="../do_reservation.php" class="btn btn-primary">Hacer una Reservación</a>
                        </div>
                    <?php endif; ?>
                </div>
            </section>

            <!-- Historial de Reservaciones -->
            <section class="reservations-section">
                <h2>Historial</h2>
                <div class="table-container">
                    <table class="reservations-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Fecha</th>
                                <th>Hora</th>
                                <th>Personas</th>
                                <th>Estado</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            foreach ($reservations as $reservation):
                                if ($reservation['status'] == 'completed' || $reservation['status'] == 'cancelled'):
                            ?>
                                <tr>
                                    <td>#<?php echo $reservation['id']; ?></td>
                                    <td><?php echo formatDate($reservation['date']); ?></td>
                                    <td><?php echo formatTime($reservation['time']); ?></td>
                                    <td><?php echo $reservation['guests']; ?> personas</td>
                                    <td>
                                        <span class="status-badge status-<?php echo $reservation['status']; ?>">
                                            <?php echo ucfirst($reservation['status']); ?>
                                        </span>
                                    </td>
                                </tr>
                            <?php 
                                endif;
                            endforeach; 
                            ?>
                        </tbody>
                    </table>
                </div>
            </section>
        </div>
    </div>
</div>

<?php require_once '../../includes/footer.php'; ?>