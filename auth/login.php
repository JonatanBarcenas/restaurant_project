<?php
require_once '../includes/header.php';
require_once '../includes/functions.php';

if (isset($_SESSION['user_id'])) {
    redirect('/');
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = sanitize($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    
    if (empty($email) || empty($password)) {
        $error = "Por favor complete todos los campos";
    } else {
        $cnn = getConnection();
       
        $query = "SELECT id, name, password, user_role FROM users WHERE email = ?";
        $stmt = $cnn->prepare($query);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();
        
        if ($user && $password === $user['password']) {  // Comparación directa en lugar de password_verify
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['name'];
            $_SESSION['user_role'] = $user['user_role'];
            
            // Redirigir según el rol
            if ($user['user_role'] === 'admin') {
                redirect('/restaurant_project/admin/dashboard.php');
            } else {
                redirect('/restaurant_project/index.php');
            }
        } else {
            $error = "Email o contraseña incorrectos";
        }
    }
}
?>

<div class="fondo auth-container">
        <div class="auth-box">
            <div class="tabs">
                <button class="tab active"><a style="text-decoration: none; color: #666; " href="../../restaurant_project/auth/login.php">Iniciar Sesión</a></button>
                <button class="tab"><a style="text-decoration: none; color: #666; " href="../../restaurant_project/auth/register.php">Registrarse</a></button>
            </div>

            <div class="form-content">
                <h2 class="form-title">Bienvenido de nuevo</h2>
                <p class="form-subtitle">Ingresa tus credenciales para continuar</p>

                <?php if ($error): ?>
                    <div class="alert alert-danger"><?php echo $error; ?></div>
                <?php endif; ?>

                <form method="POST" action="">
                    <div class="form-group">
                        <label for="email">Correo Electrónico</label>
                        <input type="email" id="email" name="email" placeholder="ejemplo@gmail.com" required>
                    </div>

                    <div class="form-group">
                        <label for="password">Contraseña</label>
                        <input type="password" id="password" name="password" placeholder="********" required>
                    </div>

                    <div class="remember-me">
                        <div class="remember-me-left">
                            <input type="checkbox" id="remember">
                            <label for="remember">Recuérdame</label>
                        </div>
                        <a href="#" class="forgot-password">¿Olvidaste tu contraseña?</a>
                    </div>

                    <button type="submit" class="btn-login">Ingresar</button>

                    <div class="social-login">
                        <button type="button" class="btn-social btn-google">
                            Continuar con Google
                        </button>
                        <button type="button" class="btn-social btn-facebook">
                            Continuar con Facebook
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>


<?php require_once '../includes/footer.php'; ?>