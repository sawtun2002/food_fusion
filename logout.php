<?php 

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}


if (isset($_COOKIE['user_login'])) {
    
    setcookie("user_login", "", time() - 3600, "/");
    
    
    unset($_COOKIE['user_login']);
}


session_unset(); 
if (session_id() != "" || isset($_COOKIE[session_name()])) {
    setcookie(session_name(), '', time() - 2592000, '/');
}
session_destroy();


header("Location: index.php");
exit();
?>