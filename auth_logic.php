<?php
include 'config.php';

$message = "";

// Registration Logic
if (isset($_POST['register'])) {
    $user = trim($_POST['username']);
    $email = trim($_POST['email']); 
    $pass = $_POST['password'];
    
    // Check if Username or Email already exists
    $check = $conn->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
    $check->bind_param("ss", $user, $email);
    $check->execute();
    if ($check->get_result()->num_rows > 0) {
        $_SESSION['msg'] = "Username or Email already exists.";
        $_SESSION['msg_type'] = "danger";
    } else {
        $hashed = password_hash($pass, PASSWORD_DEFAULT);
        // Insert with Email column
        $ins = $conn->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
        $ins->bind_param("sss", $user, $email, $hashed);
        if ($ins->execute()) {
            $_SESSION['msg'] = "Account created successfully! Please login.";
            $_SESSION['msg_type'] = "success";
        }
    }
    header("Location: login.php");
    exit();
}

// Login Logic (with 3-attempt lock feature)
if (isset($_POST['login'])) {
    // Check if login is currently locked
    if (isset($_SESSION['login_lock']) && time() < $_SESSION['login_lock']) {
        $remaining = $_SESSION['login_lock'] - time();
        $_SESSION['msg'] = "Login temporarily locked. Please wait $remaining seconds.";
        $_SESSION['msg_type'] = "warning";
        header("Location: login.php");
        exit();
    }

    $user = trim($_POST['username']);
    $pass = $_POST['password'];

    $stmt = $conn->prepare("SELECT id, password, role FROM users WHERE username = ?");
    $stmt->bind_param("s", $user);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($row = $result->fetch_assoc()) {
        if (password_verify($pass, $row['password'])) {
            // Reset attempts on successful login
            unset($_SESSION['login_attempts']);
            unset($_SESSION['login_lock']);

            $_SESSION['user_id'] = $row['id'];
            $_SESSION['role'] = $row['role'];

            if (isset($_POST['remember'])) {
                setcookie("user_login", $user, time() + (86400 * 30), "/");
            }

            header("Location: index.php");
            exit();
        } else {
            $_SESSION['msg'] = "Incorrect password.";
            $_SESSION['login_attempts'] = ($_SESSION['login_attempts'] ?? 0) + 1;
        }
    } else {
        $_SESSION['msg'] = "User not found.";
        $_SESSION['login_attempts'] = ($_SESSION['login_attempts'] ?? 0) + 1;
    }

    // Lock if attempts reach 3
    if (($_SESSION['login_attempts'] ?? 0) >= 3) {
        $_SESSION['login_lock'] = time() + 10; // 10 seconds lock
        $_SESSION['login_attempts'] = 0; // Reset counter
        $_SESSION['msg'] = "Too many failed attempts. Locked for 10 seconds.";
    }
    
    $_SESSION['msg_type'] = "danger";
    header("Location: login.php");
    exit();
}
?>