<?php
require_once 'includes/admin_header.php';

$db = getConnection();

// Manejar eliminación
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['delete_id'])) {
    $stmt = $db->prepare("DELETE FROM products WHERE id = ?");
    $stmt->bind_param("i", $_POST['delete_id']);
    if ($stmt->execute()) {
        header('Location: menu.php?msg=deleted');
        exit();
    }
}

// Obtener categorías para el filtro
$query = "SELECT * FROM categories";
$result = $db->query($query);
$categories = array();
while ($row = $result->fetch_assoc()) {
    $categories[] = $row;
}

// Obtener productos
$query = "SELECT p.*, c.name as category_name 
         FROM products p 
         LEFT JOIN categories c ON p.category_id = c.id 
         ORDER BY p.name";
$result = $db->query($query);

?>

<div class="menu-manager">
    <div class="page-header">
        <h1>Gestión de Menú</h1>
        <a href="menu/add.php" class="btn btn-primary">+ Agregar Platillo</a>
    </div>

    <!-- Filtros -->
    <div class="filters">
        <input type="text" id="searchMenu" placeholder="Buscar platillo..." class="search-input">
        <select id="categoryFilter" class="select-input">
            <option value="">Todas las categorías</option>
            <?php foreach ($categories as $category): ?>
                <option value="<?php echo $category['id']; ?>">
                    <?php echo htmlspecialchars($category['name']); ?>
                </option>
            <?php endforeach; ?>
        </select>
        <select id="statusFilter" class="select-input">
            <option value="">Todos los estados</option>
            <option value="1">Activo</option>
            <option value="0">Inactivo</option>
        </select>
    </div>

    <!-- Tabla de productos -->
    <div class="table-container">
        <table class="admin-table">
            <thead>
                <tr>
                    <th>Imagen</th>
                    <th>Nombre</th>
                    <th>Categoría</th>
                    <th>Precio</th>
                    <th>Estado</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($product = $result->fetch_assoc()): ?>
                <tr>
                    <td>
                        <img src="/restaurant_project/assets/img/no_image.png" 
                             class="product-thumbnail">
                    </td>
                    <td><?php echo htmlspecialchars($product['name']); ?></td>
                    <td><?php echo htmlspecialchars($product['category_name']); ?></td>
                    <td>$<?php echo number_format($product['price'], 2); ?></td>
                    <td>
                        <span class="status-badge status-<?php echo $product['status'] ? 'active' : 'inactive'; ?>">
                            <?php echo $product['status'] ? 'Activo' : 'Inactivo'; ?>
                        </span>
                    </td>
                    <td>
                        <div class="table-actions">
                            <a href="menu/edit.php?id=<?php echo $product['id']; ?>" 
                               class="btn btn-edit">Editar</a>
                            <form method="POST" class="delete-form" 
                                  onsubmit="return confirm('¿Está seguro de eliminar este platillo?');">
                                <input type="hidden" name="delete_id" value="<?php echo $product['id']; ?>">
                                <button type="submit" class="btn btn-delete">Eliminar</button>
                            </form>
                        </div>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Búsqueda en tiempo real
    const searchInput = document.getElementById('searchMenu');
    const categoryFilter = document.getElementById('categoryFilter');
    const statusFilter = document.getElementById('statusFilter');
    const tableRows = document.querySelectorAll('tbody tr');

    function filterTable() {
        const searchTerm = searchInput.value.toLowerCase();
        const categoryValue = categoryFilter.value;
        const statusValue = statusFilter.value;

        tableRows.forEach(row => {
            const name = row.children[1].textContent.toLowerCase();
            const category = row.children[2].textContent;
            const status = row.children[4].textContent;
            
            const matchesSearch = name.includes(searchTerm);
            const matchesCategory = !categoryValue || category === categoryFilter.options[categoryFilter.selectedIndex].text;
            const matchesStatus = !statusValue || status.includes(statusFilter.options[statusFilter.selectedIndex].text);

            row.style.display = matchesSearch && matchesCategory && matchesStatus ? '' : 'none';
        });
    }

    searchInput.addEventListener('input', filterTable);
    categoryFilter.addEventListener('change', filterTable);
    statusFilter.addEventListener('change', filterTable);
});
</script>
