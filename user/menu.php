<?php
require_once '../includes/header.php';

$db = getConnection();

// Consulta base para obtener productos
$query = "SELECT p.*, t.name as type_name, c.name as category_name 
          FROM products p 
          JOIN dish_type t ON p.product_type = t.id 
          JOIN categories c ON p.category_id = c.id 
          WHERE p.status = 1";



$result = $db->query($query);
$products = $result->fetch_all(MYSQLI_ASSOC);


$current_type = ['title' => 'Menu', 'subtitle' => 'Descubre nuestra selección de especialidades de la casa'];
?>

<div class="page-header">
    <h1><?php echo $current_type['title']; ?></h1>
    <p><?php echo $current_type['subtitle']; ?></p>
</div>


<div class="products-grid">
    <?php foreach ($products as $product): ?>
        <div class="product-card">
            <img src="../assets/img/no_image.png" 
                 alt="<?php echo htmlspecialchars($product['name']); ?>"
                 class="product-image">
            <div class="product-info">
                <h3><?php echo htmlspecialchars($product['name']); ?></h3>
                <p class="product-description">
                    <?php echo htmlspecialchars($product['description']); ?>
                </p>
                <div class="product-footer">
                    <span class="price"><?php echo formatPrice($product['price']); ?></span>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
</div>

<div class="pagination">
    <button class="page-btn active">1</button>
    <button class="page-btn">2</button>
    <button class="page-btn">3</button>
</div>

<script>
function handleOrder(productId) {
    <?php if (!isset($_SESSION['user_id'])): ?>
        window.location.href = '/restaurant_project/auth/login.php';
    <?php else: ?>
        addToCart(productId);
    <?php endif; ?>
}

function addToCart(productId) {
    
}
</script>

<?php require_once '../includes/footer.php'; ?>
