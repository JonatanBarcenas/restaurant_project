<?php
require_once 'includes/header.php';

$db = getConnection();

// Obtener categorías de productos
$query = "SELECT * FROM categories";
$result = $db->query($query);
$categories = $result->fetch_all(MYSQLI_ASSOC);

// Obtener productos destacados
$query = "SELECT p.*, c.name as category_name 
          FROM products p 
          JOIN categories c ON p.category_id = c.id 
          WHERE p.status = 1 
          ORDER BY RAND() 
          LIMIT 4";
$result = $db->query($query);
$featured_products = $result->fetch_all(MYSQLI_ASSOC);
?>

<div class="hero-section">
    <div class="hero-content">
        <p>Bienvenidos a</p>
        <h1>Sabores Auténticos</h1>
    </div>
</div>

<!-- Categorías -->
<section class="categories-section">
    <div class="container">
        <h2>Nuestras Categorías</h2>
        <div class="categories-grid">
            <?php foreach ($categories as $category): ?>
                <div class="category-card">
                      <img src="/restaurant_project/assets/img/no_image.png" 
                             alt="<?php echo htmlspecialchars($category['name']); ?>"
                             class="category-image"> 
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

<?php require_once 'includes/footer.php'; ?>


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

