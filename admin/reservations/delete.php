<?php
require_once '../includes/admin_header.php';

if (isset($_GET['id'])) {
    $db = getConnection();
    
    $query = "DELETE FROM reservations WHERE id = ?";
    $stmt = $db->prepare($query);
    $stmt->bind_param("i", $_GET['id']);
    
    if ($stmt->execute()) {
        header('Location: ../reservations.php?msg=deleted');
    } else {
        header('Location: ../reservations.php?error=1');
    }
    exit();
}

header('Location: ../reservations.php');
exit();
