<?php
error_reporting(E_ALL); ini_set('display_errors', 1);
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$host = "localhost";
$user = "root";
$pass = "root"; 
$dbname = "foodfusion_db";

$conn = new mysqli($host, $user, $pass, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}


if (!isset($_SESSION['user_id']) && isset($_COOKIE['user_login'])) {
    
    
    $cookie_user = base64_decode($_COOKIE['user_login']);
    
    
    $stmt = $conn->prepare("SELECT id, role, username, image FROM users WHERE username = ?");
    $stmt->bind_param("s", $cookie_user);
    $stmt->execute();
    $res = $stmt->get_result();
    
    if ($user_row = $res->fetch_assoc()) {
        
        $_SESSION['user_id'] = $user_row['id'];
        $_SESSION['role'] = $user_row['role'];
        $_SESSION['username'] = $user_row['username'];
        $_SESSION['image'] = $user_row['image'];
        
        
    } else {
    
        setcookie("user_login", "", time() - 3600, "/");
    }
}


?>