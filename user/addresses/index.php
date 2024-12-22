<?php
require_once '../../includes/header.php';

$db = getConnection();

// Obtener direcciones del usuario
$query = "SELECT * FROM addresses WHERE user_id = ? ORDER BY is_primary DESC";
$stmt = $db->prepare($query);
$stmt->bind_param("i", $_SESSION['user_id']);
$stmt->execute();
$result = $stmt->get_result();
$addresses = $result->fetch_all(MYSQLI_ASSOC);
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
                <h1>Direcciones de Entrega</h1>
                <a href="add.php" class="btn btn-primary" style="text-decoration: none;">+ Agregar Dirección</a>
            </div>

            <?php if (empty($addresses)): ?>
                <div class="empty-state">
                    <p>No tienes direcciones guardadas</p>
                   
                </div>
            <?php else: ?>
                <div class="addresses-grid">
                    <?php foreach ($addresses as $address): ?>
                        <div class="address-card">
                            <div class="address-header">
                                <div class="address-title">
                                    <span class="address-icon"><i class="fas fa-map-marker-alt"></i></span>
                                    <h3><?php echo htmlspecialchars($address['alias']); ?></h3>
                                </div>
                                <?php if ($address['is_primary']): ?>
                                    <span class="primary-badge">Principal</span>
                                <?php endif; ?>
                            </div>
                            <div class="address-details">
                                <p><?php echo htmlspecialchars($address['street']); ?></p>
                                <p><?php echo htmlspecialchars($address['colony']); ?></p>
                                <p><?php echo htmlspecialchars($address['city']); ?>, CP <?php echo htmlspecialchars($address['postal_code']); ?></p>
                                <?php if ($address['references_text']): ?>
                                    <p class="references"><?php echo htmlspecialchars($address['references_text']); ?></p>
                                <?php endif; ?>
                            </div>
                            <div class="address-actions">
                                <a href="edit.php?id=<?php echo $address['id']; ?>" class="action-btn edit-btn">
                                    <i class="fas fa-edit"></i> Editar
                                </a>
                               <?php if (!$address['is_primary']): ?>
                                    <form method="POST" action="delete.php" class="delete-form" 
                                          onsubmit="return confirm('¿Está seguro de eliminar esta dirección?');">
                                        <input type="hidden" name="address_id" value="<?php echo $address['id']; ?>">
                                        <button type="submit" class="action-btn delete-btn">
                                            <i class="fas fa-trash"></i> Eliminar
                                        </button>
                                    </form>
                                <?php endif; ?>
                               
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php require_once '../../includes/footer.php'; ?>