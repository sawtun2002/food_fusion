<?php 
include 'config.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

// User Delete Logic 
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
    $stmt->bind_param("i", $id);
    if ($stmt->execute()) {
        header("Location: dashboard.php?msg=deleted");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage User - Food Fusion</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <style>
        :root { --coral: #ff7f50; --navy: #1a1a2e; }
        body { background-color: #f4f7fe; font-family: 'Poppins', sans-serif; }
        .admin-card { background: white; border-radius: 20px; padding: 30px; box-shadow: 0 10px 30px rgba(0,0,0,0.03); margin-top: 50px; }
        .btn-coral { background: var(--coral); color: white; border-radius: 12px; transition: 0.3s; }
        .btn-coral:hover { background: #ff6a3d; color: white; transform: translateY(-2px); }
    </style>
</head>
<body>

<div class="container">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="admin-card">
                <div class="d-flex align-items-center mb-4">
                    <a href="dashboard.php" class="btn btn-light rounded-circle me-3"><i class="bi bi-arrow-left"></i></a>
                    <h4 class="fw-bold mb-0">Manage User Details</h4>
                </div>

                <hr class="opacity-25 mb-4">

                <p class="text-muted">သင်ပြင်ဆင်လိုသော User ၏ အချက်အလက်များကို ဖြည့်စွက်ပါ။</p>
                
                <form action="" method="POST">
                    <div class="mb-3">
                        <label class="form-label">User Name</label>
                        <input type="text" class="form-control rounded-3" value="Existing Name">
                    </div>
                    <button type="submit" class="btn btn-coral px-4 py-2">Update User Profile</button>
                </form>

            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>