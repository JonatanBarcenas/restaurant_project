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
   
    
    $query = "SELECT id, name, password FROM users WHERE email = ?";
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
        <h2>Iniciar Sesión</h2>
        
        <?php if ($error): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>
        
        <form method="POST" action="">
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" required>
            </div>
            
            <div class="form-group">
                <label for="password">Contraseña</label>
                <input type="password" id="password" name="password" required>
            </div>
            
            <button type="submit" class="btn btn-primary">Ingresar</button>
        </form>
        
        <div class="auth-links">
            <p>¿No tienes cuenta? <a href="register.php">Regístrate</a></p>
        </div>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>