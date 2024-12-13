<?php
require_once '../../includes/header.php';

if (!isLoggedIn()) {
    redirect('/auth/login.php');
}

$database = new Database();
$db = $database->getConnection();

// Obtener direcciones del usuario
$query = "SELECT * FROM addresses WHERE user_id = ? ORDER BY is_primary DESC";
$stmt = $db->prepare($query);
$stmt->execute([$_SESSION['user_id']]);
$addresses = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="container">
    <div class="addresses-header">
        <h1>Direcciones de Entrega</h1>
        <a href="add.php" class="btn btn-primary">+ Agregar Dirección</a>
    </div>

    <?php if (empty($addresses)): ?>
        <div class="empty-state">
            <p>No tienes direcciones guardadas</p>
            <a href="add.php" class="btn btn-primary">Agregar tu primera dirección</a>
        </div>
    <?php else: ?>
        <div class="addresses-grid">
            <?php foreach ($addresses as $address): ?>
                <div class="address-card">
                    <div class="address-icon">
                        📍
                    </div>
                    <div class="address-details">
                        <h3><?php echo htmlspecialchars($address['alias']); ?></h3>
                        <p><?php echo htmlspecialchars($address['street']); ?></p>
                        <p><?php echo htmlspecialchars($address['colony']); ?></p>
                        <p><?php echo htmlspecialchars($address['city']); ?>, CP <?php echo htmlspecialchars($address['postal_code']); ?></p>
                        <?php if ($address['references_text']): ?>
                            <p class="references">Referencias: <?php echo htmlspecialchars($address['references_text']); ?></p>
                        <?php endif; ?>
                        <?php if ($address['is_primary']): ?>
                            <span class="primary-badge">Principal</span>
                        <?php endif; ?>
                    </div>
                    <div class="address-actions">
                        <a href="edit.php?id=<?php echo $address['id']; ?>" class="btn btn-edit">Editar</a>
                        <?php if (!$address['is_primary']): ?>
                            <form method="POST" action="delete.php" class="delete-form" onsubmit="return confirm('¿Estás seguro de eliminar esta dirección?');">
                                <input type="hidden" name="address_id" value="<?php echo $address['id']; ?>">
                                <button type="submit" class="btn btn-delete">Eliminar</button>
                            </form>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<?php require_once '../../includes/footer.php'; ?>