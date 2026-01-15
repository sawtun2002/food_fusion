<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
include 'config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$msg = "";
$msg_type = "";

// လက်ရှိ User Data ကို ဆွဲယူခြင်း
$query = $conn->prepare("SELECT username, email, image, password FROM users WHERE id = ?");
$query->bind_param("i", $user_id);
$query->execute();
$user_data = $query->get_result()->fetch_assoc();

if (isset($_POST['update_profile'])) {
    $new_user = trim($_POST['username']);
    $new_email = trim($_POST['email']);
    $current_pass_input = $_POST['current_password']; 
    $new_pass = $_POST['new_password'];
    $confirm_pass = $_POST['confirm_password'];
    $new_image = $user_data['image'];

    // ၁။ ပုံအသစ်တင်ခြင်း
    if (!empty($_FILES['image']['name'])) {
        $file_name = time() . '_' . $_FILES['image']['name'];
        if (move_uploaded_file($_FILES['image']['tmp_name'], "uploads/" . $file_name)) {
            if (!empty($user_data['image']) && file_exists("uploads/" . $user_data['image'])) {
                unlink("uploads/" . $user_data['image']);
            }
            $new_image = $file_name;
        }
    }

    // ၂။ Password ပြောင်းလဲခြင်း ရှိ/မရှိ စစ်ဆေးခြင်း
    if (!empty($new_pass)) {
        if (password_verify($current_pass_input, $user_data['password'])) {
            if ($new_pass === $confirm_pass) {
                $hashed_pass = password_hash($new_pass, PASSWORD_DEFAULT);
                $stmt = $conn->prepare("UPDATE users SET username=?, email=?, image=?, password=? WHERE id=?");
                $stmt->bind_param("ssssi", $new_user, $new_email, $new_image, $hashed_pass, $user_id);
            } else {
                $msg = "Password အသစ် နှစ်ခု မတူညီပါ။";
                $msg_type = "danger";
            }
        } else {
            $msg = "Password ပြောင်းလဲရန် လက်ရှိ Password မှန်ကန်စွာ ရိုက်ထည့်ပါ";
            $msg_type = "danger";
        }
    } else {
        $stmt = $conn->prepare("UPDATE users SET username=?, email=?, image=? WHERE id=?");
        $stmt->bind_param("sssi", $new_user, $new_email, $new_image, $user_id);
    }

    if ($msg == "" && isset($stmt) && $stmt->execute()) {
        $_SESSION['username'] = $new_user;
        $_SESSION['image'] = $new_image;
        $msg = "Profile updated successfully!";
        $msg_type = "success";
        
        $user_data['username'] = $new_user;
        $user_data['email'] = $new_email;
        $user_data['image'] = $new_image;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Settings | Food Fusion</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <style>
        :root { --coral: #ff5733; --honey: #ffb347; --cream: #fffdfa; }
        body { background-color: var(--cream); font-family: 'Segoe UI', sans-serif; color: #444; }
        
        /* Community Cookbook Style Buttons */
        .btn-fancy { 
            background: var(--coral); 
            color: white; 
            border-radius: 30px; 
            border: none; 
            padding: 12px 30px; 
            font-weight: 600; 
            transition: 0.3s; 
        }
        .btn-fancy:hover { background: #e44d2d; transform: translateY(-2px); color: white; }
        
        /* Settings Card */
        .settings-card { 
            border-radius: 25px; 
            border: none; 
            background: white; 
            box-shadow: 0 10px 30px rgba(0,0,0,0.05); 
        }

        /* Profile Ring Style from Cookbook */
        .profile-ring { 
            padding: 4px; 
            background: linear-gradient(45deg, var(--coral), var(--honey)); 
            border-radius: 50%; 
            display: inline-flex; 
            position: relative;
        }
        .profile-ring img {
            width: 120px;
            height: 120px;
            object-fit: cover;
            border-radius: 50%;
            border: 4px solid white;
        }
        
        .form-control { border-radius: 12px; border: 1px solid #eee; padding: 10px 15px; }
        .form-control:focus { box-shadow: 0 0 0 0.25rem rgba(255, 87, 51, 0.1); border-color: var(--coral); }
        
        .section-title { font-size: 0.85rem; font-weight: 700; color: #aaa; text-transform: uppercase; letter-spacing: 1px; }
        .forgot-link { font-size: 0.8rem; color: var(--coral); text-decoration: none; font-weight: 600; }
        .forgot-link:hover { text-decoration: underline; }
    </style>
</head>
<body>

<?php include 'includes/navbar.php'; ?>

<div class="container mt-5 pt-4 mb-5">
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-5">
            <div class="card settings-card p-4 p-md-5">
                <div class="text-center mb-4">
                    <h4 class="fw-bold mb-1">Account Settings</h4>
                    <p class="text-muted small">Manage your profile and security</p>
                </div>

                <?php if($msg != ""): ?>
                    <div class="alert alert-<?php echo $msg_type; ?> alert-dismissible fade show small border-0 shadow-sm" role="alert">
                        <i class="bi <?php echo ($msg_type == 'success') ? 'bi-check-circle' : 'bi-exclamation-triangle'; ?> me-2"></i>
                        <?php echo $msg; ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <form action="" method="POST" enctype="multipart/form-data">
                    <div class="text-center mb-4">
                        <div class="profile-ring mb-3">
                            <img src="<?php echo (!empty($user_data['image'])) ? 'uploads/'.$user_data['image'] : 'https://cdn-icons-png.flaticon.com/512/149/149071.png'; ?>" 
                                 id="imgPreview">
                        </div>
                        <div>
                            <label for="imageInput" class="btn btn-sm btn-outline-secondary rounded-pill px-3">
                                <i class="bi bi-camera me-1"></i> Change Photo
                            </label>
                            <input type="file" name="image" id="imageInput" class="d-none" onchange="previewImage(this)">
                        </div>
                    </div>

                    <p class="section-title mb-3"><i class="bi bi-person me-2"></i>Profile Details</p>
                    <div class="mb-3">
                        <label class="small fw-bold mb-1">Username</label>
                        <input type="text" name="username" class="form-control" value="<?php echo htmlspecialchars($user_data['username']); ?>" required>
                    </div>
                    <div class="mb-4">
                        <label class="small fw-bold mb-1">Gmail Address</label>
                        <input type="email" name="email" class="form-control" value="<?php echo htmlspecialchars($user_data['email'] ?? ''); ?>" required>
                    </div>

                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <p class="section-title mb-0 text-danger"><i class="bi bi-shield-lock me-2"></i>Security</p>
                        <a href="forgot_password.php" class="forgot-link">Forgot Password?</a>
                    </div>
                    
                    <div class="mb-3">
                        <label class="small fw-bold mb-1 text-muted">Current Password</label>
                        <input type="password" name="current_password" class="form-control bg-light" placeholder="Leave blank if not changing password">
                    </div>
                    <div class="mb-3">
                        <label class="small fw-bold mb-1">New Password</label>
                        <input type="password" name="new_password" class="form-control" placeholder="Enter new password">
                    </div>
                    <div class="mb-4">
                        <label class="small fw-bold mb-1">Confirm New Password</label>
                        <input type="password" name="confirm_password" class="form-control" placeholder="Repeat new password">
                    </div>

                    <button type="submit" name="update_profile" class="btn btn-fancy w-100 py-3 shadow-sm mt-2">
                        Save All Changes
                    </button>
                </form>

                <div class="text-center mt-4">
                    <a href="communityCookbook.php" class="text-decoration-none text-muted small">
                        <i class="bi bi-arrow-left"></i> Back to Community
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function previewImage(input) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            reader.onload = function(e) { document.getElementById('imgPreview').src = e.target.result; }
            reader.readAsDataURL(input.files[0]);
        }
    }
</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<?php include 'includes/logout_modal.php'; ?>
</body>
</html>