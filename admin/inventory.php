<?php
require_once 'includes/admin_header.php';

$db = getConnection();

// Agregar el manejo de eliminación al principio del archivo
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['delete_id'])) {
    $stmt = $db->prepare("DELETE FROM inventory WHERE id = ?");
    $stmt->bind_param("i", $_POST['delete_id']);
    if ($stmt->execute()) {
        header('Location: inventory.php?msg=deleted');
        exit();
    }
}

// Manejar actualizaciones de stock
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_stock'])) {
    $query = "UPDATE inventory SET current_stock = ? WHERE id = ?";
    $stmt = $db->prepare($query);
    $stmt->execute([$_POST['new_stock'], $_POST['item_id']]);
    header('Location: inventory.php?msg=updated');
    exit();
}

// Total de ingredientes
$query = "SELECT COUNT(*) as total FROM inventory";
$result = $db->query($query);
$total = $result->fetch_assoc()['total'];

// Items con stock bajo
$query = "SELECT COUNT(*) as low FROM inventory WHERE current_stock <= minimum_stock";
$result = $db->query($query);
$low_stock = $result->fetch_assoc()['low'];

// Items agotados - Corregido el nombre de la columna
$query = "SELECT COUNT(*) as out_of_stock FROM inventory WHERE current_stock = 0";
$result = $db->query($query);
$out_stock = $result->fetch_assoc()['out_of_stock'];
?>

<div class="inventory-manager">
    <div class="page-header">
        <h1>Gestión de Inventario</h1>
        <a href="inventory/add.php" class="btn btn-primary">+ Nuevo Ingrediente</a>
    </div>

    <!-- Tarjetas de resumen -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-icon">📦</div>
            <div class="stat-info">
                <h3>Total Ingredientes</h3>
                <p class="stat-number"><?php echo $total; ?></p>
            </div>
        </div>

        <div class="stat-card warning">
            <div class="stat-icon">⚠️</div>
            <div class="stat-info">
                <h3>Stock Bajo</h3>
                <p class="stat-number"><?php echo $low_stock; ?></p>
            </div>
        </div>

        <div class="stat-card danger">
            <div class="stat-icon">❗</div>
            <div class="stat-info">
                <h3>Agotados</h3>
                <p class="stat-number"><?php echo $out_stock; ?></p>
            </div>
        </div>
    </div>

    <!-- Filtros -->
    <div class="filters">
        <input type="text" id="searchInventory" placeholder="Buscar ingrediente..." class="search-input">
        <select id="categoryFilter" class="select-input">
            <option value="">Todas las categorías</option>
            <option value="Carnes">Carnes</option>
            <option value="Vegetales">Vegetales</option>
            <option value="Lácteos">Lácteos</option>
            <option value="Abarrotes">Abarrotes</option>
        </select>
        <select id="stockFilter" class="select-input">
            <option value="">Todos los estados</option>
            <option value="normal">Stock Normal</option>
            <option value="low">Stock Bajo</option>
            <option value="out">Agotado</option>
        </select>
    </div>

    <!-- Tabla de inventario -->
    <div class="table-container">
        <table class="admin-table">
            <thead>
                <tr>
                    <th>Ingrediente</th>
                    <th>Categoría</th>
                    <th>Stock Actual</th>
                    <th>Stock Mínimo</th>
                    <th>Unidad</th>
                    <th>Estado</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $query = "SELECT * FROM inventory ORDER BY name";
                $result = $db->query($query);
                while ($item = $result->fetch_assoc()):
                    $stock_status = $item['current_stock'] <= 0 ? 'out' : 
                                  ($item['current_stock'] <= $item['minimum_stock'] ? 'low' : 'normal');
                ?>
                <tr data-category="<?php echo $item['category']; ?>" data-stock="<?php echo $stock_status; ?>">
                    <td><?php echo htmlspecialchars($item['name']); ?></td>
                    <td><?php echo htmlspecialchars($item['category']); ?></td>
                    <td>
                        <span class="stock-number <?php echo $stock_status; ?>">
                            <?php echo $item['current_stock']; ?>
                        </span>
                    </td>
                    <td><?php echo $item['minimum_stock']; ?></td>
                    <td><?php echo htmlspecialchars($item['unit']); ?></td>
                    <td>
                        <span class="status-badge status-<?php echo $stock_status; ?>">
                            <?php
                            echo $stock_status == 'out' ? 'Agotado' : 
                                ($stock_status == 'low' ? 'Stock Bajo' : 'Normal');
                            ?>
                        </span>
                    </td>
                    <td>
                        <div class="table-actions">
                            <button class="btn btn-small btn-edit" 
                                    onclick="openUpdateStock(<?php echo $item['id']; ?>, 
                                    '<?php echo $item['name']; ?>', 
                                    <?php echo $item['current_stock']; ?>)">
                                Actualizar Stock
                            </button>
                            <a href="inventory/edit.php?id=<?php echo $item['id']; ?>" 
                               class="btn btn-small btn-edit">✏️</a>
                            <form method="POST" class="delete-form" 
                                  onsubmit="return confirm('¿Está seguro de eliminar este ingrediente?');">
                                <input type="hidden" name="delete_id" value="<?php echo $item['id']; ?>">
                                <button type="submit" class="btn btn-small btn-delete">🗑️</button>
                            </form>
                        </div>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Modal para actualizar stock -->
<div id="updateStockModal" class="modal">
    <div class="modal-content">
        <h2>Actualizar Stock</h2>
        <form id="updateStockForm" method="POST">
            <input type="hidden" name="update_stock" value="1">
            <input type="hidden" name="item_id" id="modalItemId">
            
            <div class="form-group">
                <label for="modalItemName">Ingrediente:</label>
                <input type="text" id="modalItemName" readonly>
            </div>
            
            <div class="form-group">
                <label for="modalNewStock">Nuevo Stock:</label>
                <input type="number" id="modalNewStock" name="new_stock" step="0.01" required>
            </div>
            
            <div class="form-buttons">
                <button type="submit" class="btn btn-primary">Guardar</button>
                <button type="button" class="btn btn-secondary" onclick="closeModal()">Cancelar</button>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Funcionalidad de búsqueda y filtros
    const searchInput = document.getElementById('searchInventory');
    const categoryFilter = document.getElementById('categoryFilter');
    const stockFilter = document.getElementById('stockFilter');
    const tableRows = document.querySelectorAll('tbody tr');

    function filterTable() {
        const searchTerm = searchInput.value.toLowerCase();
        const categoryValue = categoryFilter.value.toLowerCase();
        const stockValue = stockFilter.value;

        tableRows.forEach(row => {
            const text = row.textContent.toLowerCase();
            const category = row.dataset.category.toLowerCase();
            const stockStatus = row.dataset.stock;
            
            const matchesSearch = text.includes(searchTerm);
            const matchesCategory = !categoryValue || category === categoryValue;
            const matchesStock = !stockValue || stockStatus === stockValue;

            row.style.display = matchesSearch && matchesCategory && matchesStock ? '' : 'none';
        });
    }

    searchInput.addEventListener('input', filterTable);
    categoryFilter.addEventListener('change', filterTable);
    stockFilter.addEventListener('change', filterTable);
});

// Funcionalidad del modal
function openUpdateStock(id, name, currentStock) {
    document.getElementById('modalItemId').value = id;
    document.getElementById('modalItemName').value = name;
    document.getElementById('modalNewStock').value = currentStock;
    document.getElementById('updateStockModal').style.display = 'block';
}

function closeModal() {
    document.getElementById('updateStockModal').style.display = 'none';
}

// Cerrar modal al hacer clic fuera
window.onclick = function(event) {
    const modal = document.getElementById('updateStockModal');
    if (event.target === modal) {
        modal.style.display = 'none';
    }
}
</script>
