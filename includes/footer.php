<?php
// includes/footer.php
?>

<footer class="footer">
    <div class="container">
        <div class="footer-grid">
            <!-- Información del Restaurante -->
            <div class="footer-section">
                <h3>Sabores Auténticos</h3>
                <p>Descubre la mejor experiencia gastronómica con platillos auténticos y sabores inolvidables.</p>
                <div class="contact-info">
                    <p>📍 Calle Principal #123, Ciudad de México</p>
                    <p>📞 (555) 123-4567</p>
                    <p>✉️ info@saboresautenticos.com</p>
                </div>
            </div>

            <!-- Enlaces Rápidos -->
            <div class="footer-section">
                <h3>Enlaces Rápidos</h3>
                <ul class="footer-links">
                    <li><a href="/menu.php">Menú</a></li>
                    <li><a href="/reservations.php">Reservaciones</a></li>
                    <?php if (isLoggedIn()): ?>
                        <li><a href="/user/orders">Mis Pedidos</a></li>
                        <li><a href="/user/reservations.php">Mis Reservaciones</a></li>
                        <li><a href="/user/profile.php">Mi Perfil</a></li>
                    <?php else: ?>
                        <li><a href="/auth/login.php">Iniciar Sesión</a></li>
                        <li><a href="/auth/register.php">Registrarse</a></li>
                    <?php endif; ?>
                </ul>
            </div>

            <!-- Horarios -->
            <div class="footer-section">
                <h3>Horario</h3>
                <div class="schedule">
                    <p><strong>Lunes a Jueves</strong><br>12:00 PM - 10:00 PM</p>
                    <p><strong>Viernes y Sábado</strong><br>12:00 PM - 11:00 PM</p>
                    <p><strong>Domingo</strong><br>12:00 PM - 9:00 PM</p>
                </div>
            </div>

            <!-- Newsletter -->
            <div class="footer-section">
                <h3>Mantente Conectado</h3>
                <p>Suscríbete para recibir nuestras ofertas y novedades.</p>
                <form class="newsletter-form" method="POST" action="/subscribe.php">
                    <input type="email" name="email" placeholder="Tu correo electrónico" required>
                    <button type="submit" class="btn btn-primary">Suscribirse</button>
                </form>
            </div>
        </div>

        <!-- Redes Sociales -->
        <div class="social-links">
            <a href="#" target="_blank" class="social-link">Facebook</a>
            <a href="#" target="_blank" class="social-link">Instagram</a>
            <a href="#" target="_blank" class="social-link">Twitter</a>
        </div>

        <!-- Copyright -->
        <div class="footer-bottom">
            <p>&copy; <?php echo date('Y'); ?> Sabores Auténticos. Todos los derechos reservados.</p>
            <div class="footer-legal">
                <a href="/privacy.php">Política de Privacidad</a>
                <a href="/terms.php">Términos y Condiciones</a>
            </div>
        </div>
    </div>
</footer>

<!-- JavaScript principal -->
<script src="/assets/js/main.js"></script>

<?php if (isset($extraScripts)): ?>
    <?php foreach ($extraScripts as $script): ?>
        <script src="<?php echo $script; ?>"></script>
    <?php endforeach; ?>
<?php endif; ?>

</body>
</html>