<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
include 'config.php';

// Login Check
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// --- Filter Logic ---
$cuisine = isset($_GET['cuisine']) ? $_GET['cuisine'] : 'All Cuisines';
$diet = isset($_GET['diet']) ? $_GET['diet'] : 'All Diets';
$difficulty = isset($_GET['difficulty']) ? $_GET['difficulty'] : 'Any Difficulty';

// --- Pagination Logic Setup ---
$limit = 9; 
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

// Build Base SQL for filtering
$where_clauses = ["1=1"];
$params = [];
$types = "";

if ($cuisine != 'All Cuisines') {
    $where_clauses[] = "cuisine_type = ?";
    $params[] = $cuisine;
    $types .= "s";
}
if ($diet != 'All Diets') {
    $where_clauses[] = "dietary_preference = ?";
    $params[] = $diet;
    $types .= "s";
}
if ($difficulty != 'Any Difficulty') {
    $where_clauses[] = "difficulty = ?";
    $params[] = $difficulty;
    $types .= "s";
}

$where_sql = implode(" AND ", $where_clauses);

// 1. Count total records
$count_sql = "SELECT COUNT(*) FROM recipes WHERE $where_sql";
$count_stmt = $conn->prepare($count_sql);
if (!empty($params)) {
    $count_stmt->bind_param($types, ...$params);
}
$count_stmt->execute();
$total_results = $count_stmt->get_result()->fetch_row()[0];
$total_pages = ceil($total_results / $limit);

// 2. Fetch records
$sql = "SELECT * FROM recipes WHERE $where_sql ORDER BY id DESC LIMIT ? OFFSET ?";
$stmt = $conn->prepare($sql);
$final_params = $params;
$final_params[] = $limit;
$final_params[] = $offset;
$final_types = $types . "ii";
$stmt->bind_param($final_types, ...$final_params);
$stmt->execute();
$result = $stmt->get_result();

// Helper function to keep URL parameters
function getPageUrl($p) {
    $params = $_GET;
    $params['page'] = $p;
    return "?" . http_build_query($params);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Global Recipe Collection | Food Fusion</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <style>
        :root { --coral: #ff5733; --honey: #ffb347; --cream: #fffdfa; }
        body { background-color: var(--cream); font-family: 'Segoe UI', sans-serif; }
        .hero-banner {
            background: linear-gradient(rgba(0,0,0,0.6), rgba(0,0,0,0.6)), url('https://images.unsplash.com/photo-1490818387583-1baba5e638af?auto=format&fit=crop&w=1350&q=80');
            background-size: cover; background-position: center; color: white; padding: 80px 0; border-radius: 0 0 40px 40px;
        }
        .filter-section { background: white; border-radius: 20px; box-shadow: 0 10px 30px rgba(0,0,0,0.05); margin-top: -50px; padding: 25px; }
        .recipe-card { border: none; border-radius: 20px; transition: 0.3s; background: white; overflow: hidden; height: 100%; box-shadow: 0 5px 15px rgba(0,0,0,0.05); }
        .recipe-card:hover { transform: translateY(-10px); box-shadow: 0 15px 30px rgba(0,0,0,0.1); }
        .recipe-img { height: 200px; width: 100%; object-fit: cover; background-color: #f0f0f0; }
        .badge-difficulty { position: absolute; top: 15px; right: 15px; border-radius: 30px; padding: 5px 15px; font-size: 0.75rem; font-weight: 600; z-index: 10; }
        .difficulty-Easy { background: #d1e7dd; color: #0f5132; }
        .difficulty-Intermediate { background: #fff3cd; color: #856404; }
        .difficulty-Advanced { background: #f8d7da; color: #842029; }
        .text-coral { color: var(--coral); }
        .btn-view { background: var(--coral); color: white; border-radius: 30px; padding: 8px 25px; font-weight: 600; font-size: 0.85rem; }
        
        /* Pagination Styling - သေချာအောင် ပြန်စစ်ပေးထားပါတယ် */
        .pagination .page-link { color: var(--coral); border: none; margin: 0 5px; border-radius: 10px !important; font-weight: 600; box-shadow: 0 2px 5px rgba(0,0,0,0.05); }
        .pagination .page-item.active .page-link { background-color: var(--coral) !important; color: white !important; }
        .pagination .page-item.disabled .page-link { background-color: #eee; color: #bbb; }
    </style>
</head>
<body>

<?php include 'includes/navbar.php'; ?>

<div class="hero-banner text-center">
    <div class="container">
        <h1 class="display-4 fw-bold">World Flavors Collection</h1>
        <p class="lead">Exploring over 100+ authentic recipes from 30+ countries.</p>
    </div>
</div>

<div class="container mb-5">
    <div class="filter-section mb-5">
        <form class="row g-3" method="GET" action="">
            <div class="col-md-3">
                <label class="small fw-bold text-muted mb-2">Cuisine</label>
                <select name="cuisine" class="form-select shadow-sm" onchange="this.form.submit()">
                    <option>All Cuisines</option>
                    <?php
                    $c_query = "SELECT DISTINCT cuisine_type FROM recipes ORDER BY cuisine_type ASC";
                    $c_res = $conn->query($c_query);
                    while($c_row = $c_res->fetch_assoc()):
                        $sel = ($cuisine == $c_row['cuisine_type']) ? 'selected' : '';
                        echo "<option value='".htmlspecialchars($c_row['cuisine_type'])."' $sel>".htmlspecialchars($c_row['cuisine_type'])."</option>";
                    endwhile;
                    ?>
                </select>
            </div>
            <div class="col-md-3">
                <label class="small fw-bold text-muted mb-2">Dietary</label>
                <select name="diet" class="form-select shadow-sm" onchange="this.form.submit()">
                    
                    <?php
                    $d_query = "SELECT DISTINCT dietary_preference FROM recipes ORDER BY dietary_preference ASC";
                    $d_res = $conn->query($d_query);
                    while($d_row = $d_res->fetch_assoc()):
                        $sel = ($diet == $d_row['dietary_preference']) ? 'selected' : '';
                        echo "<option value='".htmlspecialchars($d_row['dietary_preference'])."' $sel>".htmlspecialchars($d_row['dietary_preference'])."</option>";
                    endwhile;
                    ?>
                </select>
            </div>
            <div class="col-md-3">
                <label class="small fw-bold text-muted mb-2">Difficulty</label>
                <select name="difficulty" class="form-select shadow-sm" onchange="this.form.submit()">
                    <option <?php if($difficulty == 'Any Difficulty') echo 'selected'; ?>>Any Difficulty</option>
                    <option <?php if($difficulty == 'Easy') echo 'selected'; ?>>Easy</option>
                    <option <?php if($difficulty == 'Intermediate') echo 'selected'; ?>>Intermediate</option>
                    <option <?php if($difficulty == 'Advanced') echo 'selected'; ?>>Advanced</option>
                </select>
            </div>
            <div class="col-md-3 d-flex align-items-end">
                <a href="recipe_collection.php" class="btn btn-outline-secondary w-100 mb-1" style="border-radius: 10px;">
                    <i class="bi bi-arrow-clockwise"></i> Reset Filters
                </a>
            </div>
        </form>
    </div>

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="fw-bold mb-0">Discover Recipes (<?php echo $total_results; ?>)</h3>
        <span class="text-muted small">Showing Page <strong><?php echo $page; ?></strong> of <strong><?php echo $total_pages; ?></strong></span>
    </div>

    <div class="row g-4">
        <?php if ($result->num_rows > 0): ?>
            <?php while($row = $result->fetch_assoc()): ?>
                <div class="col-md-6 col-lg-4">
                    <div class="card recipe-card">
                        <span class="badge-difficulty difficulty-<?php echo $row['difficulty']; ?>">
                            <?php echo $row['difficulty']; ?>
                        </span>
                        
                        <img src="<?php echo htmlspecialchars($row['image_url']); ?>" 
                             class="recipe-img" 
                             alt="<?php echo htmlspecialchars($row['title']); ?>" 
                             loading="lazy"
                             onerror="this.onerror=null; this.src='https://images.unsplash.com/photo-1495195129352-aec32977fffd?w=500';">
                        
                        <div class="card-body p-4">
                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-coral small fw-bold text-uppercase"><?php echo htmlspecialchars($row['cuisine_type']); ?></span>
                                <span class="text-muted small"><i class="bi bi-clock"></i> <?php echo $row['cooking_time']; ?> mins</span>
                            </div>
                            <h5 class="fw-bold text-truncate"><?php echo htmlspecialchars($row['title']); ?></h5>
                            <p class="small text-muted mb-4" style="height: 40px; overflow: hidden;">
                                <?php echo htmlspecialchars(substr($row['description'], 0, 85)) . '...'; ?>
                            </p>
                            
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="badge bg-light text-dark fw-normal border"><?php echo htmlspecialchars($row['dietary_preference']); ?></span>
                                <button class="btn btn-view" onclick='showRecipe(<?php echo json_encode($row); ?>)'>View Recipe</button>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <div class="col-12 text-center py-5">
                <i class="bi bi-search display-1 text-muted"></i>
                <h4 class="mt-3">No recipes found</h4>
            </div>
        <?php endif; ?>
    </div>

    <?php if ($total_pages > 1): ?>
    <div class="d-flex justify-content-center mt-5">
        <nav aria-label="Recipe Pagination">
            <ul class="pagination">
                <li class="page-item <?php echo ($page <= 1) ? 'disabled' : ''; ?>">
                    <a class="page-link shadow-sm" href="<?php echo getPageUrl($page - 1); ?>">
                        <i class="bi bi-chevron-left"></i>
                    </a>
                </li>

                <?php 
                for($i = 1; $i <= $total_pages; $i++): 
                    if ($i == 1 || $i == $total_pages || ($i >= $page - 1 && $i <= $page + 1)):
                ?>
                    <li class="page-item <?php echo ($page == $i) ? 'active' : ''; ?>">
                        <a class="page-link shadow-sm" href="<?php echo getPageUrl($i); ?>"><?php echo $i; ?></a>
                    </li>
                <?php 
                    elseif ($i == $page - 2 || $i == $page + 2):
                        echo '<li class="page-item disabled"><span class="page-link">...</span></li>';
                    endif;
                endfor; 
                ?>

                <li class="page-item <?php echo ($page >= $total_pages) ? 'disabled' : ''; ?>">
                    <a class="page-link shadow-sm" href="<?php echo getPageUrl($page + 1); ?>">
                        <i class="bi bi-chevron-right"></i>
                    </a>
                </li>
            </ul>
        </nav>
    </div>
    <?php endif; ?>
</div>
<?php include 'includes/logout_modal.php'; ?>
<?php include 'includes/recipe_view_modal.php'; ?>
<?php include 'includes/footer.php'; ?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>