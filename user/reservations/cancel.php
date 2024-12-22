<?php
require_once '../../includes/header.php';

if (!isset($_SESSION['user_id']) || !isset($_POST['reservation_id'])) {
    header('Location: reservations.php');
    exit;
}

$db = getConnection();
$reservation_id = (int)$_POST['reservation_id'];

$query = "UPDATE reservations 
          SET status = 'cancelled' 
          WHERE id = ? AND user_id = ? AND status = 'pending'";

$stmt = $db->prepare($query);
$stmt->bind_param("ii", $reservation_id, $_SESSION['user_id']);

if ($stmt->execute()) {
    $_SESSION['message'] = "La reservación ha sido cancelada.";
} else {
    $_SESSION['error'] = "No se pudo cancelar la reservación.";
}

header('Location: reservations.php');
exit;
