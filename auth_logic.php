<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
include 'config.php';

// Registration Logic (
if (isset($_POST['register'])) {
    $fname = trim($_POST['firstname']);
    $lname = trim($_POST['lastname']);
    $email = trim($_POST['email']); 
    $pass = $_POST['password'];

    $password_regex = "/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/";

    if (!preg_match($password_regex, $pass)) {
        $_SESSION['msg'] = "Password must be at least 8 characters long, including uppercase, lowercase, numbers, and a special character.";
        $_SESSION['msg_type'] = "danger";
        $_SESSION['show_login_modal'] = true;
        header("Location: " . $_SERVER['HTTP_REFERER']); 
        exit();
    }

    $username = $fname . " " . $lname; 
    
    $check = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $check->bind_param("s", $email);
    $check->execute();
    
    if ($check->get_result()->num_rows > 0) {
        $_SESSION['msg'] = "This email is already registered.";
        $_SESSION['msg_type'] = "danger";
        $_SESSION['show_login_modal'] = true; 
    } else {
        $hashed = password_hash($pass, PASSWORD_DEFAULT);
        
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
    // Check Login Lock
    if (isset($_SESSION['login_lock']) && time() < $_SESSION['login_lock']) {
        $remaining_seconds = $_SESSION['login_lock'] - time();
        
        
        $mins = floor($remaining_seconds / 60);
        $secs = $remaining_seconds % 60;
        
        
        $time_text = ($mins > 0) ? "$mins minutes and $secs seconds" : "$secs seconds";
        
        $_SESSION['msg'] = "Login temporarily locked. Please wait $time_text.";
        $_SESSION['msg_type'] = "warning";
        $_SESSION['show_login_modal'] = true; 
        header("Location: " . $_SERVER['HTTP_REFERER']);
        exit();
    }

    $user_input = trim($_POST['username']); 
    $pass = $_POST['password'];

    $stmt = $conn->prepare("SELECT id, password, role, image, username FROM users WHERE username = ? OR email = ? LIMIT 1");
    $stmt->bind_param("ss", $user_input, $user_input);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($row = $result->fetch_assoc()) {
        if (password_verify($pass, $row['password'])) {
            unset($_SESSION['login_attempts']);
            unset($_SESSION['login_lock']);
            unset($_SESSION['show_login_modal']); 

            $_SESSION['user_id'] = $row['id'];
            $_SESSION['role'] = $row['role'];
            $_SESSION['username'] = $row['username']; 
            $_SESSION['image'] = $row['image'];

            if (isset($_POST['remember'])) {
                $cookie_val = base64_encode($row['username']);
                setcookie("user_login", $cookie_val, time() + (86400 * 30), "/");
            } else {
                if (isset($_COOKIE["user_login"])) {
                    setcookie("user_login", "", time() - 3600, "/");
                }
            }

            $redirect = isset($_POST['redirect_to']) ? $_POST['redirect_to'] : 'index.php';
            header("Location: " . $redirect);
            exit();
        } else {
            $_SESSION['msg'] = "Incorrect password.";
            $_SESSION['login_attempts'] = ($_SESSION['login_attempts'] ?? 0) + 1;
        }
    } else {
        $_SESSION['msg'] = "No account found with this username or email.";
        $_SESSION['login_attempts'] = ($_SESSION['login_attempts'] ?? 0) + 1;
    }

    // Lock account logic
    if (($_SESSION['login_attempts'] ?? 0) >= 3) {
        $_SESSION['login_lock'] = time() + 180; // 3 minutes lock
        $_SESSION['login_attempts'] = 0;
        $_SESSION['msg'] = "Too many failed attempts. Locked for 3 minutes.";
    }
    
    $_SESSION['msg_type'] = "danger";
    $_SESSION['show_login_modal'] = true; 
    
    header("Location: " . $_SERVER['HTTP_REFERER']); 
    exit();
}
?>