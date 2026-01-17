<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
include 'config.php';

// --- Login ဝင်ထားမှသာ Profile Data ကို Database မှ ယူမည် ---
$current_username = "";
$current_user_img = "";

if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
    $user_query = $conn->prepare("SELECT username, role, image FROM users WHERE id = ?");
    $user_query->bind_param("i", $user_id);
    $user_query->execute();
    $user_result = $user_query->get_result();

    if ($u_row = $user_result->fetch_assoc()) {
        $current_username = $u_row['username'];
        $current_role = $u_row['role'];
        $current_user_img = $u_row['image'];
        
        $_SESSION['username'] = $current_username;
        $_SESSION['role'] = $current_role;
        $_SESSION['image'] = $current_user_img;
    }
}

// --- Search & Filter Logic ---
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$cuisine = isset($_GET['cuisine']) ? $_GET['cuisine'] : 'All Cuisines';
$diet = isset($_GET['diet']) ? $_GET['diet'] : 'All Diets';
$difficulty = isset($_GET['difficulty']) ? $_GET['difficulty'] : 'Any Difficulty';

// --- Pagination Logic Setup ---
$limit = 9; 
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

$where_clauses = ["1=1"];
$params = [];
$types = "";

if (!empty($search)) {
    $where_clauses[] = "(title LIKE ? OR description LIKE ?)";
    $search_param = "%$search%";
    $params[] = $search_param;
    $params[] = $search_param;
    $types .= "ss";
}

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
        :root { --coral: #ff5733; --honey: #ffb347; --cream: #fffdfa; --dark-blue: #1a2a3a; }
        body { background-color: var(--cream); font-family: 'Segoe UI', sans-serif; }
        
        /* Original UI Retained */
        .hero-banner {
            background: linear-gradient(rgba(0,0,0,0.6), rgba(0,0,0,0.6)), url('https://images.unsplash.com/photo-1490818387583-1baba5e638af?auto=format&fit=crop&w=1350&q=80');
            background-size: cover; background-position: center; color: white; padding: 100px 0; border-radius: 0 0 40px 40px;
        }

        .filter-section { 
            background: white; border-radius: 20px; 
            box-shadow: 0 15px 35px rgba(0,0,0,0.1); 
            margin-top: -60px; padding: 30px; border: 1px solid rgba(0,0,0,0.05);
        }
        
        .modern-search-container {
            display: flex; background: #f8f9fa; border-radius: 15px; padding: 8px; transition: 0.3s; border: 2px solid transparent; margin-bottom: 20px;
        }

        .modern-search-container:focus-within {
            background: #fff; border-color: var(--coral); box-shadow: 0 5px 15px rgba(255, 87, 51, 0.15);
        }

        .search-input-group { display: flex; align-items: center; flex-grow: 1; padding-left: 15px; }
        .search-input-group i { color: #888; font-size: 1.2rem; }
        .search-input-group input { border: none; background: transparent; padding: 12px 15px; width: 100%; outline: none; font-size: 1.1rem; }

        .btn-search-premium { background: var(--dark-blue); color: white; border-radius: 10px; padding: 10px 30px; font-weight: 600; border: none; transition: 0.3s; }
        .btn-search-premium:hover { background: #000; transform: scale(1.02); }

        /* Recipe Card Styles */
        .recipe-card { border: none; border-radius: 20px; transition: 0.3s; background: white; overflow: hidden; height: 100%; box-shadow: 0 5px 15px rgba(0,0,0,0.05); position: relative;}
        .recipe-card:hover { transform: translateY(-10px); box-shadow: 0 15px 30px rgba(0,0,0,0.1); }
        
        /* Container for image to handle fallback background */
        .img-container { height: 200px; width: 100%; background: #e9ecef; display: flex; align-items: center; justify-content: center; overflow: hidden; }
        .recipe-img { height: 100%; width: 100%; object-fit: cover; display: block; }
        
        .badge-difficulty { position: absolute; top: 15px; right: 15px; border-radius: 30px; padding: 5px 15px; font-size: 0.75rem; font-weight: 600; z-index: 10; }
        .difficulty-Easy { background: #d1e7dd; color: #0f5132; }
        .difficulty-Intermediate { background: #fff3cd; color: #856404; }
        .difficulty-Advanced { background: #f8d7da; color: #842029; }
        .text-coral { color: var(--coral); }
        .btn-view { background: var(--coral); color: white; border-radius: 30px; padding: 8px 25px; font-weight: 600; font-size: 0.85rem; }

        /* Pagination New Design (Matching Page) */
        .pagination .page-link { color: var(--dark-blue); border-radius: 10px; margin: 0 3px; border: none; background: #fff; font-weight: 600; transition: 0.2s; }
        .pagination .page-item.active .page-link { background-color: var(--coral) !important; color: white !important; box-shadow: 0 4px 10px rgba(255, 87, 51, 0.3); }
        .pagination .page-link:hover:not(.active) { background-color: #fcece9; color: var(--coral); }
    </style>
</head>
<body>

<?php include 'includes/navbar.php'; ?>

<div class="hero-banner text-center">
    <div class="container">
        <h1 class="display-4 fw-bold mb-3">World Flavors Collection</h1>
        <p class="lead opacity-75">Discover authentic recipes and culinary secrets from around the globe.</p>
    </div>
</div>

<div class="container mb-5">
    <div class="filter-section mb-5">
        <form method="GET" action="">
            <div class="modern-search-container">
                <div class="search-input-group">
                    <i class="bi bi-search"></i>
                    <input type="text" name="search" placeholder="Search by recipe name, ingredients or country..." value="<?php echo htmlspecialchars($search); ?>">
                </div>
                <button class="btn btn-search-premium d-none d-md-block" type="submit">Search</button>
            </div>
            
            <div class="row g-3">
                <div class="col-md-3">
                    <label class="small fw-bold text-muted mb-2"><i class="bi bi-geo-alt"></i> Cuisine</label>
                    <select name="cuisine" class="form-select border-0 bg-light" onchange="this.form.submit()" style="height: 45px; border-radius: 10px;">
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
                    <label class="small fw-bold text-muted mb-2"><i class="bi bi-egg-fried"></i> Dietary</label>
                    <select name="diet" class="form-select border-0 bg-light" onchange="this.form.submit()" style="height: 45px; border-radius: 10px;">
                        <option>All Diets</option>
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
                    <label class="small fw-bold text-muted mb-2"><i class="bi bi-bar-chart"></i> Difficulty</label>
                    <select name="difficulty" class="form-select border-0 bg-light" onchange="this.form.submit()" style="height: 45px; border-radius: 10px;">
                        <option <?php if($difficulty == 'Any Difficulty') echo 'selected'; ?>>Any Difficulty</option>
                        <option <?php if($difficulty == 'Easy') echo 'selected'; ?>>Easy</option>
                        <option <?php if($difficulty == 'Intermediate') echo 'selected'; ?>>Intermediate</option>
                        <option <?php if($difficulty == 'Advanced') echo 'selected'; ?>>Advanced</option>
                    </select>
                </div>
                <div class="col-md-3 d-flex align-items-end">
                    <a href="recipe_collection.php" class="btn btn-outline-danger w-100 fw-bold" style="height: 45px; border-radius: 10px;">
                        <i class="bi bi-arrow-clockwise"></i> Clear Filters
                    </a>
                </div>
            </div>
            <button class="btn btn-search-premium w-100 mt-3 d-md-none" type="submit">Search Now</button>
        </form>
    </div>

    <div class="d-flex justify-content-between align-items-center mb-4 px-2">
        <div>
            <h3 class="fw-bold mb-0"><?php echo !empty($search) ? "Results for '".htmlspecialchars($search)."'" : "Discover Recipes"; ?></h3>
            <p class="text-muted small mb-0"><?php echo $total_results; ?> items available</p>
        </div>
        <span class="badge bg-white text-dark border p-2 px-3 rounded-pill shadow-sm">Page <?php echo $page; ?> of <?php echo $total_pages; ?></span>
    </div>

    <div class="row g-4">
        <?php if ($result->num_rows > 0): ?>
            <?php while($row = $result->fetch_assoc()): ?>
                <div class="col-md-6 col-lg-4">
                    <div class="card recipe-card">
                        <span class="badge-difficulty difficulty-<?php echo $row['difficulty']; ?>">
                            <?php echo $row['difficulty']; ?>
                        </span>
                        
                        <div class="img-container">
                            <?php if(!empty($row['image_url'])): ?>
                                <img src="<?php echo htmlspecialchars($row['image_url']); ?>" 
                                     class="recipe-img" 
                                     alt="recipe" 
                                     onerror="this.style.display='none'; this.parentElement.innerHTML='<i class=\'bi bi-image text-muted display-6\'></i>';">
                            <?php else: ?>
                                <i class="bi bi-image text-muted display-6"></i>
                            <?php endif; ?>
                        </div>
                        
                        <div class="card-body p-4">
                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-coral small fw-bold"><?php echo htmlspecialchars($row['cuisine_type']); ?></span>
                                <span class="text-muted small"><i class="bi bi-clock"></i> <?php echo $row['cooking_time']; ?> mins</span>
                            </div>
                            <h5 class="fw-bold text-truncate"><?php echo htmlspecialchars($row['title']); ?></h5>
                            <p class="small text-muted mb-4" style="height: 40px; overflow: hidden;">
                                <?php echo htmlspecialchars(substr($row['description'], 0, 85)) . '...'; ?>
                            </p>
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="badge bg-light text-dark fw-normal border"><?php echo htmlspecialchars($row['dietary_preference']); ?></span>
                                <button class="btn btn-view" onclick='showRecipe(<?php echo json_encode($row); ?>)'>View Details</button>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <div class="col-12 text-center py-5">
                <i class="bi bi-search-heart display-1 text-muted opacity-50"></i>
                <h4 class="fw-bold mt-4">We couldn't find a match</h4>
                <p class="text-muted">Try adjusting your keywords or filters.</p>
            </div>
        <?php endif; ?>
    </div>

    <?php if ($total_pages > 1): ?>
    <div class="d-flex justify-content-center mt-5">
        <nav>
            <ul class="pagination">
                <li class="page-item <?php echo ($page <= 1) ? 'disabled' : ''; ?>">
                    <a class="page-link shadow-sm" href="<?php echo getPageUrl($page - 1); ?>"><i class="bi bi-chevron-left"></i></a>
                </li>
                <?php for($i = 1; $i <= $total_pages; $i++): ?>
                    <?php if ($i == 1 || $i == $total_pages || ($i >= $page - 1 && $i <= $page + 1)): ?>
                        <li class="page-item <?php echo ($page == $i) ? 'active' : ''; ?>">
                            <a class="page-link shadow-sm" href="<?php echo getPageUrl($i); ?>"><?php echo $i; ?></a>
                        </li>
                    <?php endif; ?>
                <?php endfor; ?>
                <li class="page-item <?php echo ($page >= $total_pages) ? 'disabled' : ''; ?>">
                    <a class="page-link shadow-sm" href="<?php echo getPageUrl($page + 1); ?>"><i class="bi bi-chevron-right"></i></a>
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