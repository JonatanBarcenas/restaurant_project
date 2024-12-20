<?php
require_once '../includes/header.php';
require_once '../includes/functions.php';

if (isset($_SESSION['user_id'])) {
    redirect('/');
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = sanitize($_POST['email']);
    $password = $_POST['password'];
    
    $cnn = getConnection();
   
    $query = "SELECT id, name, password, role FROM users WHERE email = ?";
    $stmt = $cnn->prepare($query);
    $stmt->execute([$email]);
    
    if ($user = $stmt->fetch(PDO::FETCH_ASSOC)) {
        if (password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['name'];
            $_SESSION['user_role'] = $user['role'];
            
            redirect('/');
        }
    }
    $error = "Email o contraseña incorrectos";
}
?>


<div class="auth-container">
        <div class="auth-box">
            <div class="tabs">
                <button class="tab active">Iniciar Sesión</button>
                <button class="tab">Registrarse</button>
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
                        <input type="email" id="email" placeholder="ejemplo@email.com">
                    </div>

                    <div class="form-group">
                        <label for="password">Contraseña</label>
                        <input type="password" id="password">
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