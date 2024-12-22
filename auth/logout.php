<?php
session_start();
$_SESSION = array(); 
session_unset();     
session_destroy();   

if (isset($_COOKIE[session_name()])) {
    setcookie(session_name(), '', time()-42000, '/');
}

header("Location: /restaurant_project/auth/login.php");
exit();
?>