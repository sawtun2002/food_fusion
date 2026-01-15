<?php
session_start();
$host = "localhost";
$user = "root";
$pass = "root"; // MAMP အတွက် root၊ XAMPP ဖြစ်လျှင် empty ထားပါ
$dbname = "foodfusion_db";

$conn = new mysqli($host, $user, $pass, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Cookie ရှိမရှိစစ်ပြီး Auto Login လုပ်ပေးခြင်း
if (!isset($_SESSION['user_id']) && isset($_COOKIE['user_login'])) {
    $cookie_user = $_COOKIE['user_login'];
    $stmt = $conn->prepare("SELECT id, role FROM users WHERE username = ?");
    $stmt->bind_param("s", $cookie_user);
    $stmt->execute();
    $res = $stmt->get_result();
    if ($user_row = $res->fetch_assoc()) {
        $_SESSION['user_id'] = $user_row['id'];
        $_SESSION['role'] = $user_row['role'];
    }
}
?>