<?php
require_once '../includes/header.php';

if (isLoggedIn()) {
    redirect('/');
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = sanitize($_POST['name']);
    $email = sanitize($_POST['email']);
    $phone = sanitize($_POST['phone']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    
    if ($password !== $confirm_password) {
        $error = "Las contraseñas no coinciden";
    } else {
        $db = getConnection();
        
        // Verificar si el email ya existe
        $query = "SELECT id FROM users WHERE email = ?";
        $stmt = $db->prepare($query);
        $stmt->execute([$email]);
        
        if ($stmt->fetch()) {
            $error = "Este email ya está registrado";
        } else {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            
            $query = "INSERT INTO users (name, email, phone, password) VALUES (?, ?, ?, ?)";
            $stmt = $db->prepare($query);
            
            if ($stmt->execute([$name, $email, $phone, $hashed_password])) {
                $_SESSION['user_id'] = $db->insert_id;  // Cambiar lastInsertId() por insert_id
                $_SESSION['user_name'] = $name;
                $_SESSION['user_role'] = 'user';
                
                redirect('/');
            } else {
                $error = "Error al registrar el usuario";
            }
        }
    }
}
?>

<div class="fondo auth-container">
    <div class="auth-box">
            <div class="tabs">
                <button class="tab"><a style="text-decoration: none; color: #666; " href="../../restaurant_project/auth/login.php">Iniciar Sesión</a></button>
                <button class="tab active"><a style="text-decoration: none; color: #666; " href="../../restaurant_project/auth/register.php">Registrarse</a></button>
            </div>
            <div class="form-content">
        <h2 class="form-title">Crear una cuenta</h2>
        
        <?php if ($error): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>
        
        <form method="POST" action="">
            <div class="form-group">
                <label for="name">Nombre Completo</label>
                <input type="text" id="name" name="name" placeholder="Jonatan Barcenas" required>
            </div>
            
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" placeholder="ejemplo@gmail.com" required>
            </div>
            
            <div class="form-group">
                <label for="phone">Teléfono</label>
                <input type="tel" id="phone" name="phone" required>
            </div>
            
            <div class="form-group">
                <label for="password">Contraseña</label>
                <input type="password" id="password" name="password" placeholder="**********" required>
            </div>
            
            <div class="form-group">
                <label for="confirm_password">Confirmar Contraseña</label>
                <input type="password" id="confirm_password" name="confirm_password" placeholder="**********" required>
            </div>
            
            <button type="submit" class="btn-login">Crear Cuenta</button>
        </form>
        
        <div class="auth-links">
            <p>¿Ya tienes cuenta? <a href="login.php">Inicia sesión</a></p>
        </div>
        </div>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>