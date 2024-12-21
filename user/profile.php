<?php
require_once '../includes/header.php';

$db = getConnection();

// Obtener datos del usuario
$query = "SELECT * FROM users WHERE id = ?";
$stmt = $db->prepare($query);
$stmt->bind_param("i", $_SESSION['user_id']);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

// Manejar actualización de perfil
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    try {
        $query = "UPDATE users SET name = ?, phone = ? WHERE id = ?";
        $stmt = $db->prepare($query);
        $stmt->execute([
            $_POST['name'],
            $_POST['phone'],
            $_SESSION['user_id']
        ]);

        // Si hay cambio de contraseña
        if (!empty($_POST['new_password'])) {
            if (password_verify($_POST['current_password'], $user['password'])) {
                $new_password_hash = password_hash($_POST['new_password'], PASSWORD_DEFAULT);
                $query = "UPDATE users SET password = ? WHERE id = ?";
                $stmt = $db->prepare($query);
                $stmt->execute([$new_password_hash, $_SESSION['user_id']]);
            } else {
                throw new Exception("La contraseña actual es incorrecta");
            }
        }

        $_SESSION['user_name'] = $_POST['name'];
        $success = "Perfil actualizado correctamente";
        
        // Actualizar datos del usuario
        $stmt = $db->prepare("SELECT * FROM users WHERE id = ?");
        $stmt->execute([$_SESSION['user_id']]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        $error = $e->getMessage();
    }
}
?>

<div class="container">
    <div class="user-section">
        <!-- Sidebar de navegación del usuario -->
        <div class="user-sidebar">
            <?php include '../includes/user_sidebar.php'; ?>
        </div>

        <!-- Contenido principal -->
        <div class="user-content">
            <div class="profile-container">
                <h1>Información Personal</h1>

                <?php if (isset($success)): ?>
                    <div class="alert alert-success"><?php echo $success; ?></div>
                <?php endif; ?>

                <?php if (isset($error)): ?>
                    <div class="alert alert-danger"><?php echo $error; ?></div>
                <?php endif; ?>

                <form method="POST" class="profile-form">
                    <!-- Información básica -->
                    <div class="form-section">
                        <h2>Datos Personales</h2>
                        
                        <div class="form-group">
                            <label for="name">Nombre Completo*</label>
                            <input type="text" id="name" name="name" 
                                   value="<?php echo htmlspecialchars($user['name']); ?>" required>
                        </div>

                        <div class="form-group">
                            <label for="email">Correo Electrónico</label>
                            <input type="email" id="email" value="<?php echo htmlspecialchars($user['email']); ?>" 
                                   disabled readonly>
                            <small class="form-text">El correo electrónico no se puede modificar</small>
                        </div>

                        <div class="form-group">
                            <label for="phone">Teléfono</label>
                            <input type="tel" id="phone" name="phone" 
                                   value="<?php echo htmlspecialchars($user['phone']); ?>"
                                   pattern="[0-9]{10}">
                        </div>
                    </div>

                    <!-- Cambio de contraseña -->
                    <div class="form-section">
                        <h2>Cambiar Contraseña</h2>
                        <div class="form-group">
                            <label for="current_password">Contraseña Actual</label>
                            <input type="password" id="current_password" name="current_password">
                        </div>

                        <div class="form-group">
                            <label for="new_password">Nueva Contraseña</label>
                            <input type="password" id="new_password" name="new_password" 
                                   minlength="8">
                        </div>

                        <div class="form-group">
                            <label for="confirm_password">Confirmar Nueva Contraseña</label>
                            <input type="password" id="confirm_password" name="confirm_password">
                        </div>
                    </div>

                    <!-- Preferencias -->
                    <div class="form-section">
                        <h2>Preferencias de Notificación</h2>
                        <div class="form-check">
                            <input type="checkbox" id="notify_offers" name="notify_offers" checked >
                            <label for="notify_offers">Recibir notificaciones de ofertas</label>
                        </div>

                        <div class="form-check">
                            <input type="checkbox" id="notify_reservations" name="notify_reservations" checked>
                            <label for="notify_reservations">Recordatorios de reservaciones</label>
                        </div>
                    </div>

                    <div class="form-buttons">
                        <button type="submit" class="btn btn-primary">Guardar Cambios</button>
                        <button type="reset" class="btn btn-secondary">Cancelar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
document.querySelector('form').addEventListener('submit', function(e) {
    const newPassword = document.getElementById('new_password').value;
    const confirmPassword = document.getElementById('confirm_password').value;
    
    if (newPassword && newPassword !== confirmPassword) {
        e.preventDefault();
        alert('Las contraseñas no coinciden');
    }
});
</script>

<?php require_once '../includes/footer.php'; ?>