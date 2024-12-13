<?php
require_once 'includes/admin_header.php';

$database = new Database();
$db = $database->getConnection();

// Manejar cambios de estado
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['toggle_status'])) {
    $query = "UPDATE staff SET status = !status WHERE id = ?";
    $stmt = $db->prepare($query);
    $stmt->execute([$_POST['staff_id']]);
    header('Location: staff.php?msg=updated');
    exit();
}
?>

<div class="staff-manager">
    <div class="page-header">
        <h1>Gestión de Personal</h1>
        <a href="staff/add.php" class="btn btn-primary">+ Nuevo Empleado</a>
    </div>

    <!-- Tarjetas de resumen -->
    <div class="stats-grid">
        <?php
        // Total empleados
        $query = "SELECT COUNT(*) as total FROM staff";
        $total = $db->query($query)->fetch()['total'];

        // Activos hoy
        $query = "SELECT COUNT(*) as active FROM staff WHERE status = 1";
        $active = $db->query($query)->fetch()['active'];

        // Próximo turno
        $query = "SELECT COUNT(*) as next FROM staff WHERE shift = 'tarde' AND status = 1";
        $next_shift = $db->query($query)->fetch()['next'];
        ?>

        <div class="stat-card">
            <div class="stat-icon">👥</div>
            <div class="stat-info">
                <h3>Total Empleados</h3>
                <p class="stat-number"><?php echo $total; ?></p>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon">✅</div>
            <div class="stat-info">
                <h3>Activos Hoy</h3>
                <p class="stat-number"><?php echo $active; ?></p>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon">⏱️</div>
            <div class="stat-info">
                <h3>Próximo Turno</h3>
                <p class="stat-number"><?php echo $next_shift; ?> empleados</p>
            </div>
        </div>
    </div>

    <!-- Filtros -->
    <div class="filters">
        <input type="text" id="searchStaff" placeholder="Buscar empleado..." class="search-input">
        <select id="roleFilter" class="select-input">
            <option value="">Todos los roles</option>
            <option value="Chef">Chef</option>
            <option value="Mesero">Mesero</option>
            <option value="Cocinero">Cocinero</option>
            <option value="Cajero">Cajero</option>
        </select>
        <select id="shiftFilter" class="select-input">
            <option value="">Todos los turnos</option>
            <option value="mañana">Mañana</option>
            <option value="tarde">Tarde</option>
            <option value="noche">Noche</option>
        </select>
        <select id="statusFilter" class="select-input">
            <option value="">Todos los estados</option>
            <option value="1">Activo</option>
            <option value="0">Inactivo</option>
        </select>
    </div>

    <!-- Tabla de personal -->
    <div class="table-container">
        <table class="admin-table">
            <thead>
                <tr>
                    <th>Empleado</th>
                    <th>Rol</th>
                    <th>Turno</th>
                    <th>Estado</th>
                    <th>Contacto</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $query = "SELECT * FROM staff ORDER BY name";
                $stmt = $db->query($query);
                while ($employee = $stmt->fetch(PDO::FETCH_ASSOC)):
                ?>
                <tr>
                    <td>
                        <div class="employee-info">
                            <div class="employee-avatar">
                                <?php echo substr($employee['name'], 0, 1); ?>
                            </div>
                            <div class="employee-details">
                                <div class="employee-name"><?php echo htmlspecialchars($employee['name']); ?></div>
                                <div class="employee-id">ID: <?php echo $employee['id']; ?></div>
                            </div>
                        </div>
                    </td>
                    <td><?php echo htmlspecialchars($employee['role']); ?></td>
                    <td>
                        <span class="shift-badge shift-<?php echo $employee['shift']; ?>">
                            <?php echo ucfirst($employee['shift']); ?>
                        </span>
                    </td>
                    <td>
                        <span class="status-badge status-<?php echo $employee['status'] ? 'active' : 'inactive'; ?>">
                            <?php echo $employee['status'] ? 'Activo' : 'Inactivo'; ?>
                        </span>
                    </td>
                    <td>
                        <?php if (isset($employee['phone'])): ?>
                            <div class="contact-info">
                                <?php echo htmlspecialchars($employee['phone']); ?>
                            </div>
                        <?php endif; ?>
                    </td>
                    <td>
                        <div class="table-actions">
                            <a href="staff/edit.php?id=<?php echo $employee['id']; ?>" 
                               class="btn btn-small btn-edit">Editar</a>
                            <form method="POST" class="inline-form" 
                                  onsubmit="return confirm('¿Está seguro de cambiar el estado del empleado?');">
                                <input type="hidden" name="toggle_status" value="1">
                                <input type="hidden" name="staff_id" value="<?php echo $employee['id']; ?>">
                                <button type="submit" class="btn btn-small <?php echo $employee['status'] ? 'btn-warning' : 'btn-success'; ?>">
                                    <?php echo $employee['status'] ? 'Desactivar' : 'Activar'; ?>
                                </button>
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
    // Funcionalidad de búsqueda y filtros
    const searchInput = document.getElementById('searchStaff');
    const roleFilter = document.getElementById('roleFilter');
    const shiftFilter = document.getElementById('shiftFilter');
    const statusFilter = document.getElementById('statusFilter');
    const tableRows = document.querySelectorAll('tbody tr');

    function filterTable() {
        const searchTerm = searchInput.value.toLowerCase();
        const roleValue = roleFilter.value.toLowerCase();
        const shiftValue = shiftFilter.value.toLowerCase();
        const statusValue = statusFilter.value;

        tableRows.forEach(row => {
            const name = row.querySelector('.employee-name').textContent.toLowerCase();
            const role = row.cells[1].textContent.toLowerCase();
            const shift = row.querySelector('.shift-badge').textContent.toLowerCase();
            const status = row.querySelector('.status-badge').classList.contains('status-active') ? '1' : '0';

            const matchesSearch = name.includes(searchTerm);
            const matchesRole = !roleValue || role === roleValue;
            const matchesShift = !shiftValue || shift === shiftValue;
            const matchesStatus = !statusValue || status === statusValue;

            row.style.display = matchesSearch && matchesRole && matchesShift && matchesStatus ? '' : 'none';
        });
    }

    searchInput.addEventListener('input', filterTable);
    roleFilter.addEventListener('change', filterTable);
    shiftFilter.addEventListener('change', filterTable);
    statusFilter.addEventListener('change', filterTable);
});
</script>

<?php require_once 'includes/admin_footer.php'; ?>