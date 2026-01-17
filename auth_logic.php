<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
include 'config.php';

// Registration Logic
if (isset($_POST['register'])) {
    $fname = trim($_POST['firstname']);
    $lname = trim($_POST['lastname']);
    $email = trim($_POST['email']); 
    $pass = $_POST['password'];

    // 1. Password Strength Validation (International Standard)
    // Must be at least 8 characters, include uppercase, lowercase, numbers, and symbols.
    $password_regex = "/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/";

    if (!preg_match($password_regex, $pass)) {
        $_SESSION['msg'] = "Password must be at least 8 characters long, including uppercase, lowercase, numbers, and a special character.";
        $_SESSION['msg_type'] = "danger";
        $_SESSION['show_login_modal'] = true;
        header("Location: " . $_SERVER['HTTP_REFERER']); 
        exit();
    }

    // 2. Combine First Name and Last Name as Username
    $username = $fname . " " . $lname; 
    
    // 3. Check if Email is unique
    $check = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $check->bind_param("s", $email);
    $check->execute();
    
    if ($check->get_result()->num_rows > 0) {
        $_SESSION['msg'] = "This email is already registered.";
        $_SESSION['msg_type'] = "danger";
        $_SESSION['show_login_modal'] = true; 
    } else {
        $hashed = password_hash($pass, PASSWORD_DEFAULT);
        
        // 4. Save to Database
        $ins = $conn->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
        $ins->bind_param("sss", $username, $email, $hashed);
        
        if ($ins->execute()) {
            $_SESSION['msg'] = "Account created successfully! Please login.";
            $_SESSION['msg_type'] = "success";
            $_SESSION['show_login_modal'] = true; 
        }
    }
    header("Location: " . $_SERVER['HTTP_REFERER']); 
    exit();
}

// Login Logic
if (isset($_POST['login'])) {
    // Brute-force Prevention: Check Login Lock
    if (isset($_SESSION['login_lock']) && time() < $_SESSION['login_lock']) {
        $remaining = $_SESSION['login_lock'] - time();
        $_SESSION['msg'] = "Login temporarily locked. Please wait $remaining seconds.";
        $_SESSION['msg_type'] = "warning";
        $_SESSION['show_login_modal'] = true; 
        header("Location: " . $_SERVER['HTTP_REFERER']);
        exit();
    }

    $user_input = trim($_POST['username']); 
    $pass = $_POST['password'];

    // Allow login via Full Name (Username) or Email
    $stmt = $conn->prepare("SELECT id, password, role, image, username FROM users WHERE username = ? OR email = ?");
    $stmt->bind_param("ss", $user_input, $user_input);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($row = $result->fetch_assoc()) {
        if (password_verify($pass, $row['password'])) {
            // Success: Clear login attempts and lock sessions
            unset($_SESSION['login_attempts']);
            unset($_SESSION['login_lock']);
            unset($_SESSION['show_login_modal']); 

            $_SESSION['user_id'] = $row['id'];
            $_SESSION['role'] = $row['role'];
            $_SESSION['username'] = $row['username']; 
            $_SESSION['image'] = $row['image'];

            // Remember Me Logic
            if (isset($_POST['remember'])) {
                $cookie_val = base64_encode($row['username']);
                setcookie("user_login", $cookie_val, time() + (86400 * 30), "/");
            } else {
                if (isset($_COOKIE["user_login"])) {
                    setcookie("user_login", "", time() - 3600, "/");
                }
            }

            header("Location: index.php");
            exit();
        } else {
            $_SESSION['msg'] = "Incorrect password.";
            $_SESSION['login_attempts'] = ($_SESSION['login_attempts'] ?? 0) + 1;
        }
    } else {
        $_SESSION['msg'] = "Account not found.";
        $_SESSION['login_attempts'] = ($_SESSION['login_attempts'] ?? 0) + 1;
    }

    // Lock account if failed attempts reach 3 or more
    if (($_SESSION['login_attempts'] ?? 0) >= 3) {
        $_SESSION['login_lock'] = time() + 30; // Lock for 30 seconds
        $_SESSION['login_attempts'] = 0;
        $_SESSION['msg'] = "Too many failed attempts. Locked for 30 seconds.";
    }
    
    $_SESSION['msg_type'] = "danger";
    $_SESSION['show_login_modal'] = true; 
    
    header("Location: " . $_SERVER['HTTP_REFERER']); 
    exit();
}
?>