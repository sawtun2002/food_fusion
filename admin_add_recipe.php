<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
include 'config.php';

// Admin မဟုတ်ရင် ဝင်ခွင့်မပြုပါ
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

$success_msg = "";
$error_msg = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = mysqli_real_escape_string($conn, $_POST['title']);
    $description = mysqli_real_escape_string($conn, $_POST['description']);
    $cuisine = mysqli_real_escape_string($conn, $_POST['cuisine']);
    $diet = mysqli_real_escape_string($conn, $_POST['diet']);
    $difficulty = mysqli_real_escape_string($conn, $_POST['difficulty']);
    $cooking_time = intval($_POST['cooking_time']);
    
    // Image Handling
    $image_url = "";
    if (isset($_FILES['recipe_image']) && $_FILES['recipe_image']['error'] == 0) {
        $target_dir = "uploads/";
        if (!file_exists($target_dir)) { mkdir($target_dir, 0777, true); }
        
        $file_ext = pathinfo($_FILES["recipe_image"]["name"], PATHINFO_EXTENSION);
        $file_name = time() . "_" . uniqid() . "." . $file_ext;
        $target_file = $target_dir . $file_name;

        if (move_uploaded_file($_FILES["recipe_image"]["tmp_name"], $target_file)) {
            $image_url = $target_file;
        }
    }

    $sql = "INSERT INTO recipes (title, description, cuisine_type, dietary_preference, difficulty, cooking_time, image_url) 
            VALUES (?, ?, ?, ?, ?, ?, ?)";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssssis", $title, $description, $cuisine, $diet, $difficulty, $cooking_time, $image_url);

    if ($stmt->execute()) {
        $success_msg = "Recipe အသစ်ကို အောင်မြင်စွာ ထည့်သွင်းပြီးပါပြီ။";
    } else {
        $error_msg = "Error: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add New Recipe - Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <style>
        body { background-color: #f8fafc; font-family: 'Segoe UI', sans-serif; }
        .form-card { background: white; border-radius: 20px; box-shadow: 0 10px 30px rgba(0,0,0,0.05); border: none; }
        .btn-submit { background: #ff5733; color: white; border-radius: 10px; padding: 12px; font-weight: 600; border: none; }
        .btn-submit:hover { background: #e64a19; }
    </style>
</head>
<body>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="d-flex align-items-center mb-4">
                <a href="recipe_collection.php" class="btn btn-light rounded-circle me-3"><i class="bi bi-arrow-left"></i></a>
                <h2 class="fw-bold mb-0">Add New Recipe</h2>
            </div>

            <?php if($success_msg): ?>
                <div class="alert alert-success border-0 shadow-sm mb-4"><?php echo $success_msg; ?></div>
            <?php endif; ?>
            
            <?php if($error_msg): ?>
                <div class="alert alert-danger border-0 shadow-sm mb-4"><?php echo $error_msg; ?></div>
            <?php endif; ?>

            <div class="card form-card p-4 p-md-5">
                <form action="" method="POST" enctype="multipart/form-data">
                    <div class="row g-4">
                        <div class="col-12">
                            <label class="form-label fw-bold">Recipe Title</label>
                            <input type="text" name="title" class="form-control" placeholder="e.g. Spicy Thai Basil Chicken" required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-bold">Cuisine Type</label>
                            <input type="text" name="cuisine" class="form-control" placeholder="e.g. Thai, Italian, Japanese" required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-bold">Dietary Preference</label>
                            <select name="diet" class="form-select">
                                <option value="Non-Vegetarian">Non-Vegetarian</option>
                                <option value="Vegetarian">Vegetarian</option>
                                <option value="Vegan">Vegan</option>
                                <option value="Gluten-Free">Gluten-Free</option>
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-bold">Difficulty Level</label>
                            <select name="difficulty" class="form-select">
                                <option value="Easy">Easy</option>
                                <option value="Intermediate">Intermediate</option>
                                <option value="Advanced">Advanced</option>
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-bold">Cooking Time (Minutes)</label>
                            <input type="number" name="cooking_time" class="form-control" placeholder="e.g. 30" required>
                        </div>

                        <div class="col-12">
                            <label class="form-label fw-bold">Description</label>
                            <textarea name="description" class="form-control" rows="4" placeholder="Write a short description about this recipe..." required></textarea>
                        </div>

                        <div class="col-12">
                            <label class="form-label fw-bold">Recipe Image</label>
                            <input type="file" name="recipe_image" class="form-control" accept="image/*" required>
                            <small class="text-muted">High-quality landscape images work best.</small>
                        </div>

                        <div class="col-12 mt-5">
                            <button type="submit" class="btn btn-submit w-100">Save Recipe</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

</body>
</html>