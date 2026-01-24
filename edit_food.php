<?php 
include 'config.php';


if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}


if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $stmt = $conn->prepare("SELECT * FROM foods WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $food = $stmt->get_result()->fetch_assoc();

    if (!$food) {
        die("Food item not found!");
    }
}


if (isset($_POST['update_food'])) {
    $id = $_POST['id'];
    $name = htmlspecialchars($_POST['name']);
    $price = $_POST['price'];
    $cat = htmlspecialchars($_POST['category']);
    $old_image = $_POST['old_image'];

    
    if (!empty($_FILES['image']['name'])) {
        $img_name = time() . "_" . $_FILES['image']['name'];
        move_uploaded_file($_FILES['image']['tmp_name'], "uploads/" . $img_name);
    } else {
        $img_name = $old_image; 
    }

    $update = $conn->prepare("UPDATE foods SET name=?, price=?, category=?, image=? WHERE id=?");
    $update->bind_param("sdssi", $name, $price, $cat, $img_name, $id);

    if ($update->execute()) {
        header("Location: dashboard.php");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Food - Food Fusion</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow border-0">
                <div class="card-body p-4">
                    <h4 class="mb-4 text-primary">Edit Food Item</h4>
                    <form method="POST" enctype="multipart/form-data">
                        <input type="hidden" name="id" value="<?php echo $food['id']; ?>">
                        <input type="hidden" name="old_image" value="<?php echo $food['image']; ?>">

                        <div class="mb-3">
                            <label class="form-label fw-bold">Food Name</label>
                            <input type="text" name="name" class="form-control" value="<?php echo $food['name']; ?>" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Price ($)</label>
                            <input type="number" step="0.01" name="price" class="form-control" value="<?php echo $food['price']; ?>" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Category</label>
                            <select name="category" class="form-select">
                                <option value="Fast Food" <?php if($food['category'] == 'Fast Food') echo 'selected'; ?>>Fast Food</option>
                                <option value="Main Course" <?php if($food['category'] == 'Main Course') echo 'selected'; ?>>Main Course</option>
                                <option value="Dessert" <?php if($food['category'] == 'Dessert') echo 'selected'; ?>>Dessert</option>
                                <option value="Drinks" <?php if($food['category'] == 'Drinks') echo 'selected'; ?>>Drinks</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Current Image</label><br>
                            <img src="uploads/<?php echo $food['image']; ?>" width="100" class="rounded mb-2 shadow-sm">
                            <input type="file" name="image" class="form-control" accept="image/*">
                            <small class="text-muted">Leave empty if you don't change။</small>
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" name="update_food" class="btn btn-primary px-4">Save Changes</button>
                            <a href="dashboard.php" class="btn btn-secondary px-4">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>