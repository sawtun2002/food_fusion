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

// ၁။ ပြင်ဆင်မည့် Recipe Data ကို Database မှ အရင်ဆွဲထုတ်ခြင်း
if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $query = "SELECT * FROM recipes WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $recipe = $result->fetch_assoc();

    if (!$recipe) {
        die("Recipe not found!");
    }
}

// ၂။ Update Button နှိပ်လိုက်သည့်အခါ လုပ်ဆောင်မည့် Logic
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $recipe_id = intval($_POST['recipe_id']);
    $title = mysqli_real_escape_string($conn, $_POST['title']);
    $description = mysqli_real_escape_string($conn, $_POST['description']);
    $cuisine = mysqli_real_escape_string($conn, $_POST['cuisine']);
    $diet = mysqli_real_escape_string($conn, $_POST['diet']);
    $difficulty = mysqli_real_escape_string($conn, $_POST['difficulty']);
    $cooking_time = intval($_POST['cooking_time']);
    
    // ပုံအသစ်မတင်ရင် ပုံဟောင်းကိုပဲ ဆက်သုံးမည်
    $image_url = $_POST['old_image']; 

    if (isset($_FILES['recipe_image']) && $_FILES['recipe_image']['error'] == 0) {
        $target_dir = "uploads/";
        $file_ext = pathinfo($_FILES["recipe_image"]["name"], PATHINFO_EXTENSION);
        $file_name = time() . "_" . uniqid() . "." . $file_ext;
        $target_file = $target_dir . $file_name;

        if (move_uploaded_file($_FILES["recipe_image"]["tmp_name"], $target_file)) {
            $image_url = $target_file;
        }
    }

    $sql = "UPDATE recipes SET title=?, description=?, cuisine_type=?, dietary_preference=?, difficulty=?, cooking_time=?, image_url=? WHERE id=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssssisi", $title, $description, $cuisine, $diet, $difficulty, $cooking_time, $image_url, $recipe_id);

    if ($stmt->execute()) {
        $success_msg = "Recipe ကို အောင်မြင်စွာ ပြင်ဆင်ပြီးပါပြီ။";
        // ပြင်ဆင်ပြီး Data ကို ပြန်ပြရန်
        $recipe['title'] = $title;
        $recipe['description'] = $description;
        $recipe['cuisine_type'] = $cuisine;
        $recipe['dietary_preference'] = $diet;
        $recipe['difficulty'] = $difficulty;
        $recipe['cooking_time'] = $cooking_time;
        $recipe['image_url'] = $image_url;
    } else {
        $error_msg = "Error: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Recipe - Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <style>
        body { background-color: #f8fafc; font-family: 'Segoe UI', sans-serif; }
        .form-card { background: white; border-radius: 20px; box-shadow: 0 10px 30px rgba(0,0,0,0.05); border: none; }
        .btn-update { background: #4f46e5; color: white; border-radius: 10px; padding: 12px; font-weight: 600; border: none; }
        .btn-update:hover { background: #4338ca; }
    </style>
</head>
<body>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="d-flex align-items-center mb-4">
                <a href="recipe_collection.php" class="btn btn-light rounded-circle me-3"><i class="bi bi-arrow-left"></i></a>
                <h2 class="fw-bold mb-0">Edit Recipe</h2>
            </div>

            <?php if($success_msg): ?>
                <div class="alert alert-success border-0 shadow-sm mb-4"><?php echo $success_msg; ?></div>
            <?php endif; ?>

            <div class="card form-card p-4 p-md-5">
                <form action="" method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="recipe_id" value="<?php echo $recipe['id']; ?>">
                    <input type="hidden" name="old_image" value="<?php echo $recipe['image_url']; ?>">

                    <div class="row g-4">
                        <div class="col-12">
                            <label class="form-label fw-bold">Recipe Title</label>
                            <input type="text" name="title" class="form-control" value="<?php echo htmlspecialchars($recipe['title']); ?>" required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-bold">Cuisine Type</label>
                            <input type="text" name="cuisine" class="form-control" value="<?php echo htmlspecialchars($recipe['cuisine_type'] ?? ''); ?>" required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-bold">Dietary Preference</label>
                            <select name="diet" class="form-select">
                                <option value="Non-Vegetarian" <?php echo ($recipe['dietary_preference'] == 'Non-Vegetarian') ? 'selected' : ''; ?>>Non-Vegetarian</option>
                                <option value="Vegetarian" <?php echo ($recipe['dietary_preference'] == 'Vegetarian') ? 'selected' : ''; ?>>Vegetarian</option>
                                <option value="Vegan" <?php echo ($recipe['dietary_preference'] == 'Vegan') ? 'selected' : ''; ?>>Vegan</option>
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-bold">Difficulty</label>
                            <select name="difficulty" class="form-select">
                                <option value="Easy" <?php echo ($recipe['difficulty'] == 'Easy') ? 'selected' : ''; ?>>Easy</option>
                                <option value="Intermediate" <?php echo ($recipe['difficulty'] == 'Intermediate') ? 'selected' : ''; ?>>Intermediate</option>
                                <option value="Advanced" <?php echo ($recipe['difficulty'] == 'Advanced') ? 'selected' : ''; ?>>Advanced</option>
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-bold">Cooking Time (Mins)</label>
                            <input type="number" name="cooking_time" class="form-control" value="<?php echo $recipe['cooking_time']; ?>" required>
                        </div>

                        <div class="col-12">
                            <label class="form-label fw-bold">Description</label>
                            <textarea name="description" class="form-control" rows="4" required><?php echo htmlspecialchars($recipe['description']); ?></textarea>
                        </div>

                        <div class="col-12">
                            <label class="form-label fw-bold">Recipe Image</label>
                            <div class="mb-2">
                                <img src="<?php echo $recipe['image_url']; ?>" width="150" class="rounded shadow-sm">
                                <small class="text-muted d-block mt-1">လက်ရှိပုံ (ပုံအသစ်မတင်ပါက ဤပုံကိုသာ ဆက်သုံးမည်)</small>
                            </div>
                            <input type="file" name="recipe_image" class="form-control" accept="image/*">
                        </div>

                        <div class="col-12 mt-5">
                            <button type="submit" class="btn btn-update w-100">Update Changes</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

</body>
</html>