<?php 
include 'config.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

$msg = "";
// Food Add Logic (ဒီနေရာမှာပဲ ဆက်ထားနိုင်သလို ဖိုင်ခွဲချင်လည်း ရပါတယ်)
if (isset($_POST['add_food'])) {
    $name = htmlspecialchars($_POST['name']); 
    $price = $_POST['price'];
    $cat = htmlspecialchars($_POST['category']);
    $img_name = time() . "_" . $_FILES['image']['name']; 
    $target = "uploads/" . basename($img_name);
    $stmt = $conn->prepare("INSERT INTO foods (name, price, category, image) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("sdss", $name, $price, $cat, $img_name);
    if ($stmt->execute()) {
        move_uploaded_file($_FILES['image']['tmp_name'], $target);
        $msg = "<div class='alert alert-success alert-dismissible fade show'>အောင်မြင်စွာ ထည့်သွင်းပြီးပါပြီ။ <button type='button' class='btn-close' data-bs-dismiss='alert'></button></div>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <style>
        :root { --sidebar-width: 260px; }
        body { background-color: #f8f9fa; overflow-x: hidden; }
        #sidebar { width: var(--sidebar-width); height: 100vh; position: fixed; top: 0; left: 0; background: #212529; z-index: 1000; transition: all 0.3s; }
        #main-content { margin-left: var(--sidebar-width); transition: all 0.3s; min-height: 100vh; }
        .nav-link { color: #ced4da; cursor: pointer; border-radius: 8px; margin: 5px 10px; }
        .nav-link:hover, .nav-link.active { background: #343a40; color: #ffc107; }
        .management-section { display: none; }
        .management-section.active { display: block; }
        @media (max-width: 991.98px) { #sidebar { left: calc(-1 * var(--sidebar-width)); } #sidebar.show { left: 0; } #main-content { margin-left: 0; } .overlay { display: none; position: fixed; width: 100vw; height: 100vh; background: rgba(0,0,0,0.5); z-index: 999; } .overlay.show { display: block; } }
    </style>
</head>
<body>

<div class="overlay" id="overlay" onclick="toggleSidebar()"></div>

<?php include 'includes/sidebar.php'; ?>

<div id="main-content">
    <nav class="navbar navbar-white bg-white shadow-sm sticky-top px-3 py-3">
        <button class="btn btn-dark d-lg-none me-2" onclick="toggleSidebar()"><i class="bi bi-list"></i></button>
        <span class="fw-bold text-dark" id="current-title">Food Management</span>
    </nav>

    <div class="container-fluid p-4">
        <?php echo $msg; ?>

        <div id="food-mgmt" class="management-section active">
            <?php include 'includes/food_section.php'; ?>
        </div>

        <div id="user-mgmt" class="management-section">
            <?php include 'includes/user_section.php'; ?>
        </div>
    </div>
</div>

<?php include 'includes/logout_modal.php'; ?>

<script>
    function toggleSidebar() { document.getElementById('sidebar').classList.toggle('show'); document.getElementById('overlay').classList.toggle('show'); }
    function showSection(sectionId) {
        document.querySelectorAll('.management-section').forEach(s => s.classList.remove('active'));
        document.querySelectorAll('.nav-link').forEach(l => l.classList.remove('active'));
        document.getElementById(sectionId).classList.add('active');
        event.currentTarget.classList.add('active');
        document.getElementById('current-title').innerText = (sectionId === 'food-mgmt') ? 'Food Management' : 'User Management';
        if (window.innerWidth < 992) toggleSidebar();
    }
</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>