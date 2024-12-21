<?php
require_once '../includes/header.php';
// Obtener el id_product_type desde la URL
$id_product_type = isset($_GET['product_type']) ? intval($_GET['product_type']) : null;
$category_id = isset($_GET['category']) ? intval($_GET['category']) : null;

$db = getConnection();

// Consulta base para obtener productos
$query = "SELECT p.*, t.name as type_name, c.name as category_name 
          FROM products p 
          JOIN dish_type t ON p.product_type = t.id 
          JOIN categories c ON p.category_id = c.id 
          WHERE p.status = 1";

// Agregar filtros
if ($id_product_type) {
    $query .= " AND p.product_type = $id_product_type";
}
if ($category_id) {
    $query .= " AND p.category_id = $category_id";
}

$result = $db->query($query);
$products = $result->fetch_all(MYSQLI_ASSOC);

// Obtener categorías para los filtros
$query = "SELECT * FROM categories";
$result = $db->query($query);
$categories = $result->fetch_all(MYSQLI_ASSOC);

$categories = [
    1 => ['id' => 1, 'name' => 'Carnes'],
    2 => ['id' => 2, 'name' => 'Mariscos'],
    6 => ['id' => 6, 'name' => 'Pastas'],
];

// Mantener el array de tipos de productos
$product_types = [
    1 => ['title' => 'Entradas', 'subtitle' => 'Deliciosas entradas para abrir el apetito'],
    2 => ['title' => 'Platos Principales', 'subtitle' => 'Descubre nuestra selección de especialidades de la casa'],
    3 => ['title' => 'Postres', 'subtitle' => 'Endulza tu paladar con nuestros deliciosos postres'],
    4 => ['title' => 'Bebidas', 'subtitle' => 'Refrescantes bebidas para acompañar tu comida']
];

// Si no se encuentra el id_product_type, se muestra "Todos"
$current_type = $product_types[$id_product_type] ?? ['title' => 'Todos los Platos', 'subtitle' => 'Descubre nuestra selección de especialidades de la casa'];
?>

<div class="page-header">
    <h1><?php echo $current_type['title']; ?></h1>
    <p><?php echo $current_type['subtitle']; ?></p>
</div>

<?php if ($id_product_type == 2): ?>
    <div class="filter-buttons">
    <?php foreach ($categories as $category): ?>
        <a href="?product_type=<?php echo $id_product_type; ?>&category=<?php echo $category['id']; ?>" 
           class="filter-btn <?php echo $category_id == $category['id'] ? 'active' : ''; ?>">
            <?php echo htmlspecialchars($category['name']); ?>
        </a>
    <?php endforeach; ?>
    <a href="?product_type=<?php echo $id_product_type; ?>" 
       class="filter-btn <?php echo is_null($category_id) ? 'active' : ''; ?>">
        Todos
    </a>
    </div>
<?php endif; ?>

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
                    <?php if (!isset($_SESSION['user_id'])): ?>
                        <button class="btn btn-primary" onclick="window.location.href='/restaurant_project/auth/login.php'">
                            Ordenar
                        </button>
                    <?php else: ?>
                        <form method="POST" action="/restaurant_project/user/cart.php">
                            <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                            <input type="hidden" name="product_name" value="<?php echo htmlspecialchars($product['name']); ?>">
                            <input type="hidden" name="product_price" value="<?php echo $product['price']; ?>">
                            <button type="submit" class="btn btn-primary">Ordenar</button>
                        </form>
                    <?php endif; ?>
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
