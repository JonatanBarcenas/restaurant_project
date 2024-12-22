<?php
require_once '../../includes/header.php';

if (!isset($_SESSION['user_id']) || !isset($_POST['address_id'])) {
    header('Location: index.php');
    exit;
}

$db = getConnection();
$address_id = (int)$_POST['address_id'];

// Verificar que la dirección pertenezca al usuario y no sea la principal
$query = "DELETE FROM addresses WHERE id = ? AND user_id = ? AND is_primary = 0";
$stmt = $db->prepare($query);
$stmt->bind_param("ii", $address_id, $_SESSION['user_id']);

if ($stmt->execute() && $stmt->affected_rows > 0) {
    $_SESSION['success'] = "Dirección eliminada correctamente";
} else {
    $_SESSION['error'] = "No se pudo eliminar la dirección";
}

header('Location: index.php');
exit;
