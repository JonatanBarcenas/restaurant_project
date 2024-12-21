<?php
require_once 'includes/header.php';

$db = getConnection();

// Obtener categorías de productos
$query = "SELECT * FROM categories";
$result = $db->query($query);
$categories = $result->fetch_all(MYSQLI_ASSOC);

// Obtener tipos de productos
$query = "SELECT * FROM dish_type";
$result = $db->query($query);
$dish_types = $result->fetch_all(MYSQLI_ASSOC);

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
        <h2>Nuestras Especialidades</h2>
        <div class="categories-grid">
            <?php foreach ($dish_types as $dish_type): ?>
                <div class="category-card">
                      <img src="/restaurant_project/assets/img/no_image.png" 
                             alt="<?php echo htmlspecialchars($dish_type['name']); ?>"
                             class="category-image"> 
                    <div class="category-info">
                        <h3><?php echo htmlspecialchars($dish_type['name']); ?></h3>
                        <a href="user/specialties.php?product_type=<?php echo $dish_type['id']; ?>" 
                           class="btn btn-outline">Ver Platillos</a>
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

