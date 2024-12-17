<?php
require_once 'includes/header.php';

$database = new Database();
$db = $database->getConnection();

// Obtener categorías de productos
$query = "SELECT * FROM categories";
$stmt = $db->query($query);
$categories = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Obtener productos destacados
$query = "SELECT p.*, c.name as category_name 
          FROM products p 
          JOIN categories c ON p.category_id = c.id 
          WHERE p.status = 1 
          ORDER BY RAND() 
          LIMIT 4";
$stmt = $db->query($query);
$featured_products = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="hero-section">
    <div class="hero-content">
        <h1>Sabores Auténticos</h1>
        <p>Descubre la mejor experiencia gastronómica</p>
        <div class="hero-buttons">
            <a href="menu.php" class="btn btn-primary">Ver Menú</a>
            <a href="reservations.php" class="btn btn-secondary">Hacer Reservación</a>
        </div>
    </div>
</div>

<!-- Categorías -->
<section class="categories-section">
    <div class="container">
        <h2>Nuestras Categorías</h2>
        <div class="categories-grid">
            <?php foreach ($categories as $category): ?>
                <div class="category-card">
                    <?php if ($category['image']): ?>
                        <img src="<?php echo htmlspecialchars($category['image']); ?>" 
                             alt="<?php echo htmlspecialchars($category['name']); ?>"
                             class="category-image">
                    <?php endif; ?>
                    <div class="category-info">
                        <h3><?php echo htmlspecialchars($category['name']); ?></h3>
                        <?php if ($category['description']): ?>
                            <p><?php echo htmlspecialchars($category['description']); ?></p>
                        <?php endif; ?>
                        <a href="menu.php?category=<?php echo $category['id']; ?>" 
                           class="btn btn-outline">Ver Platillos</a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- Productos Destacados -->
<section class="featured-section">
    <div class="container">
        <h2>Platillos Destacados</h2>
        <div class="products-grid">
            <?php foreach ($featured_products as $product): ?>
                <div class="product-card">
                    <?php if ($product['image']): ?>
                        <img src="<?php echo htmlspecialchars($product['image']); ?>" 
                             alt="<?php echo htmlspecialchars($product['name']); ?>"
                             class="product-image">
                    <?php endif; ?>
                    <div class="product-info">
                        <h3><?php echo htmlspecialchars($product['name']); ?></h3>
                        <p class="product-category"><?php echo htmlspecialchars($product['category_name']); ?></p>
                        <p class="product-description">
                            <?php echo htmlspecialchars($product['description']); ?>
                        </p>
                        <div class="product-footer">
                            <span class="price"><?php echo formatPrice($product['price']); ?></span>
                            <button class="btn btn-primary add-to-cart" 
                                    data-product-id="<?php echo $product['id']; ?>">
                                Agregar al Carrito
                            </button>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- Sección de Información -->
<section class="info-section">
    <div class="container">
        <div class="info-grid">
            <div class="info-card">
                <div class="info-icon">🕒</div>
                <h3>Horario</h3>
                <p>Lunes a Domingo</p>
                <p>12:00 PM - 10:00 PM</p>
            </div>
            <div class="info-card">
                <div class="info-icon">📍</div>
                <h3>Ubicación</h3>
                <p>Calle Principal #123</p>
                <p>Ciudad de México</p>
            </div>
            <div class="info-card">
                <div class="info-icon">📞</div>
                <h3>Contacto</h3>
                <p>Tel: (555) 123-4567</p>
                <p>info@saboresautenticos.com</p>
            </div>
        </div>
    </div>
</section>

<!-- Scripts -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Manejar agregar al carrito
    const addToCartButtons = document.querySelectorAll('.add-to-cart');
    addToCartButtons.forEach(button => {
        button.addEventListener('click', function() {
            const productId = this.dataset.productId;
            addToCart(productId);
        });
    });

    function addToCart(productId) {
        fetch('/api/cart.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                product_id: productId,
                quantity: 1
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                updateCartCount(data.cartCount);
                showNotification('Producto agregado al carrito');
            } else {
                showNotification('Error al agregar el producto', 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showNotification('Error al agregar el producto', 'error');
        });
    }

    function updateCartCount(count) {
        const cartCount = document.querySelector('.cart-count');
        if (cartCount) {
            cartCount.textContent = count;
        }
    }

    function showNotification(message, type = 'success') {
        const notification = document.createElement('div');
        notification.className = `notification ${type}`;
        notification.textContent = message;
        document.body.appendChild(notification);

        setTimeout(() => {
            notification.remove();
        }, 3000);
    }
});
</script>

<?php require_once 'includes/footer.php'; ?>