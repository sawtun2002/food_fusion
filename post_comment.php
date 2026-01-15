<?php
session_start();
include 'config.php';

if (isset($_POST['comment']) && isset($_SESSION['user_id'])) {
    $u_id = $_SESSION['user_id'];
    $r_id = $_POST['recipe_id'];
    $p_id = $_POST['parent_id'] ?? 0; // 0 ဆိုလျှင် Main Comment ဖြစ်သည်
    
    $bad_words = ['f***', 's***']; 
    $comment = str_ireplace($bad_words, "***", htmlspecialchars($_POST['comment']));

    $stmt = $conn->prepare("INSERT INTO recipe_comments (user_id, recipe_id, comment, parent_id) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("iisi", $u_id, $r_id, $comment, $p_id);
    $stmt->execute();
}
header("Location: communityCookbook.php");
?>