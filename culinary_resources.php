<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
include 'config.php';

// 1. Check Login Status
$current_username = "";
$current_role = "";
$current_user_img = "";

if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
    $user_query = $conn->prepare("SELECT username, role, image FROM users WHERE id = ?");
    $user_query->bind_param("i", $user_id);
    $user_query->execute();
    $result = $user_query->get_result();

    if ($row = $result->fetch_assoc()) {
        $current_username = $row['username'];
        $current_role = $row['role'];
        $current_user_img = $row['image']; 
        
        $_SESSION['username'] = $current_username;
        $_SESSION['role'] = $current_role;
        $_SESSION['image'] = $current_user_img; 
    }
}

 // Download Logic
if (isset($_GET['download'])) {
    $file = basename($_GET['download']);
    $path = "uploads/recipes/" . $file;
    
    if (file_exists($path) && is_file($path)) {
        header('Content-Description: File Transfer');
        header('Content-Type: application/pdf');
        header('Content-Disposition: attachment; filename="'.$file.'"');
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Length: ' . filesize($path));
        flush(); 
        readfile($path);
        exit;
    } else {
        echo "<script>alert('File not found or does not exist yet.'); window.location.href='culinary_resources.php';</script>";
        exit;
    }
}

// 3. Fetch Recipe Cards from Database
$recipe_query = "SELECT * FROM recipe_cards ORDER BY created_at DESC";
$recipe_result = $conn->query($recipe_query);
?>

<!DOCTYPE html>
<html lang="en">
    <?php include 'includes/link_and_title.php'; ?>
    
    <style>
        :root { --coral: #ff5733; --honey: #ffb347; --navy: #1a1a2e; --cream: #fffdfa; --glass: rgba(255, 255, 255, 0.8); }
        .resource-page-wrapper {
            background-color: #fffdfa;
            font-family: 'Poppins', sans-serif;
            color: #444;
            overflow-x: hidden;
            padding-top: 20px;
        }
        
        .hero-section { 
            background: #1a1a2e; 
            color: white; 
            padding: 60px 0; 
            border-bottom: 5px solid #ff5733;
        }
        
        .download-card {
            border: none; 
            border-radius: 15px; 
            background: white;
            transition: all 0.3s ease; 
            box-shadow: 0 4px 15px rgba(0,0,0,0.05);
        }
        .download-card:hover { 
            transform: translateY(-5px); 
            box-shadow: 0 12px 25px rgba(0,0,0,0.1); 
        }
        
        .video-card { 
            border-radius: 20px; 
            overflow: hidden; 
            background: #000; 
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
        }
        .video-container { position: relative; padding-bottom: 56.25%; height: 0; }
        .video-container iframe { position: absolute; top: 0; left: 0; width: 100%; height: 100%; border: 0; }
        
        .section-title { 
            font-weight: 800; 
            color: #1a1a2e; 
            position: relative; 
            display: inline-block; 
            margin-bottom: 40px; 
        }
        .section-title::after { 
            content: ''; 
            width: 60%; 
            height: 4px; 
            background: #ff5733; 
            position: absolute; 
            bottom: -10px; 
            left: 0; 
            border-radius: 2px;
        }
        
        .btn-coral { 
            background: #ff5733; 
            color: white; 
            border-radius: 10px; 
            font-weight: 600; 
            border: none;
            padding: 10px 20px;
            transition: 0.3s;
        }
        .btn-coral:hover { background: #e44d2d; color: white; transform: scale(1.05); }
        
        .hack-badge { 
            background: #fff0ed; 
            color: #ff5733; 
            font-size: 0.7rem; 
            font-weight: 700; 
            padding: 5px 12px; 
            border-radius: 6px;
            display: inline-block;
        }

        .recipe-img {
            height: 200px;
            object-fit: cover;
        }

        /* New Styles for Converter */
        .converter-input { border: 2px solid #eee; border-radius: 10px; padding: 10px; }
        .converter-input:focus { border-color: var(--coral); outline: none; }
        .temp-table { font-size: 0.9rem; border-radius: 10px; overflow: hidden; }
        .temp-table thead { background: var(--navy); color: white; }
    </style>
</head>
<body>

<?php include 'includes/navbar.php'; ?>

<div class="resource-page-wrapper">

    <div class="hero-section text-center">
        <div class="container">
            <h1 class="display-5 fw-bold text-white">Culinary Masterclass</h1>
            <p class="text-white-50 lead">Explore Cooking Techniques and Professional Kitchen Secrets</p>
        </div>
    </div>

    <div class="container my-5">

        <div class="mb-5 py-4">
            <div class="row g-4">
                <div class="col-lg-6">
                    <h3 class="section-title">Measurement Converter</h3>
                    <div class="card download-card p-4">
                        <p class="text-muted small mb-4">Quickly convert kitchen units for international recipes.</p>
                        <div class="row g-3">
                            <div class="col-6">
                                <label class="small fw-bold">Cups</label>
                                <input type="number" id="inputCups" class="form-control converter-input" placeholder="Cups" oninput="convertUnits('cup')">
                            </div>
                            <div class="col-6">
                                <label class="small fw-bold">Milliliters (ml)</label>
                                <input type="number" id="inputMl" class="form-control converter-input" placeholder="ml" oninput="convertUnits('ml')">
                            </div>
                            <div class="col-6">
                                <label class="small fw-bold">Tablespoons (tbsp)</label>
                                <input type="number" id="inputTbsp" class="form-control converter-input" placeholder="tbsp" oninput="convertUnits('tbsp')">
                            </div>
                            <div class="col-6">
                                <label class="small fw-bold">Teaspoons (tsp)</label>
                                <input type="number" id="inputTsp" class="form-control converter-input" placeholder="tsp" oninput="convertUnits('tsp')">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6">
                    <h3 class="section-title">Cooking Temperatures</h3>
                    <div class="table-responsive shadow-sm">
                        <table class="table table-hover temp-table bg-white mb-0">
                            <thead>
                                <tr><th>Meat Type</th><th>Doneness</th><th>Temp (°C)</th></tr>
                            </thead>
                            <tbody>
                                <tr><td>Beef / Lamb</td><td>Medium Rare</td><td>54 - 57°C</td></tr>
                                <tr><td>Beef / Lamb</td><td>Medium</td><td>60 - 63°C</td></tr>
                                <tr><td>Pork / Veal</td><td>Well Done</td><td>71°C</td></tr>
                                <tr><td>Poultry</td><td>Safe to Eat</td><td>74°C</td></tr>
                                <tr><td>Fish</td><td>Flaky</td><td>63°C</td></tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="mb-5">
            <h3 class="section-title">Cooking Techniques & Tutorials</h3>
            <div class="row g-4">
                <div class="col-lg-7">
                    <div id="tutorialCarousel" class="carousel slide" data-bs-ride="false">
                        <div class="carousel-inner video-card">
                            <div class="carousel-item active">
                                <div class="video-container">
                                    <iframe src="https://www.youtube.com/embed/G-Fg7l7G1zw" allowfullscreen></iframe>
                                </div>
                                <div class="bg-dark text-white p-3">
                                    <h5 class="mb-0">Essential Knife Skills</h5>
                                </div>
                            </div>
                            <div class="carousel-item">
                                <div class="video-container">
                                    <iframe src="https://www.youtube.com/embed/LPE6B_j8Vsk" allowfullscreen></iframe>
                                </div>
                                <div class="bg-dark text-white p-3">
                                    <h5 class="mb-0">Mastering Sautéing</h5>
                                </div>
                            </div>
                            <div class="carousel-item">
                                <div class="video-container">
                                    <iframe src="https://www.youtube.com/embed/fA9E99JpAn8" allowfullscreen></iframe>
                                </div>
                                <div class="bg-dark text-white p-3">
                                    <h5 class="mb-0">The Perfect Sear</h5>
                                </div>
                            </div>
                        </div>
                        <button class="carousel-control-prev" type="button" data-bs-target="#tutorialCarousel" data-bs-slide="prev">
                            <span class="carousel-control-prev-icon"></span>
                        </button>
                        <button class="carousel-control-next" type="button" data-bs-target="#tutorialCarousel" data-bs-slide="next">
                            <span class="carousel-control-next-icon"></span>
                        </button>
                    </div>
                </div>
                <div class="col-lg-5">
                    <div class="d-flex flex-column gap-3">
                        <?php
                        $tutorials = [
                            ["Sauté Vegetables", "05:20 mins"],
                            ["Perfect Searing", "08:45 mins"],
                            ["Mastering Mother Sauces", "12:10 mins"]
                        ];
                        foreach($tutorials as $t): ?>
                        <div class="card download-card p-3 d-flex flex-row align-items-center gap-3">
                            <div class="bg-light rounded-circle p-3 text-danger">
                                <i class="bi bi-play-fill fs-4"></i>
                            </div>
                            <div>
                                <h6 class="mb-0 fw-bold"><?php echo $t[0]; ?></h6>
                                <small class="text-muted">Duration: <?php echo $t[1]; ?></small>
                            </div>
                            <i class="bi bi-chevron-right ms-auto text-muted"></i>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>

        <div class="mb-5 py-4">
            <h3 class="section-title">Pro Kitchen Hacks</h3>
            <div class="row g-4">
                <a href="https://cookingwithmagali.com/how-to-peel-garlic-fast/" class="col-md-4 text-decoration-none">
                    <div class="card download-card p-4 h-100 border-start border-5 border-coral">
                        <span class="hack-badge mb-3 text-uppercase">Time Saver</span>
                        <h5 class="fw-bold">Peeling Garlic in Seconds</h5>
                        <p class="small text-muted mb-0">Learn how to peel garlic effortlessly and other time-saving methods for a busy kitchen.</p>
                    </div>
                </a>
                <a href="https://www.foodnetwork.com/how-to/packages/food-network-essentials/how-to-store-fresh-herbs" class="col-md-4 text-decoration-none">
                    <div class="card download-card p-4 h-100 border-start border-5 border-warning">
                        <span class="hack-badge mb-3 text-uppercase" style="background:#fff9e6; color:#ffb347;">Freshness</span>
                        <h5 class="fw-bold">Keeping Herbs Fresh</h5>
                        <p class="small text-muted mb-0">Discover the secrets to keeping your aromatic herbs fresh and vibrant for over a week.</p>
                    </div>
                </a>
                <a href="https://www.realsimple.com/how-to-fix-an-oversalted-soup-11866284" class="col-md-4 text-decoration-none">
                    <div class="card download-card p-4 h-100 border-start border-5 border-info">
                        <span class="hack-badge mb-3 text-uppercase" style="background:#e6f7ff; color:#1890ff;">Emergency</span>
                        <h5 class="fw-bold">Fixing Over-salted Soup</h5>
                        <p class="small text-muted mb-0">An emergency kitchen guide on how to balance flavors if your soup becomes too salty.</p>
                    </div>
                </a>
            </div>
        </div>

        <div class="mb-5">
            <h3 class="section-title">Printable Recipe Cards</h3>
            <div class="row g-4">
                <?php 
                if ($recipe_result && $recipe_result->num_rows > 0): 
                    while($row = $recipe_result->fetch_assoc()): ?>
                    <div class="col-md-3">
                        <div class="card download-card overflow-hidden h-100">
                            <img src="<?php echo htmlspecialchars($row['image_url']); ?>" class="card-img-top recipe-img" alt="Recipe">
                            <div class="card-body text-center p-3">
                                <h6 class="fw-bold mb-3"><?php echo htmlspecialchars($row['title']); ?></h6>
                                <a href="?download=<?php echo urlencode($row['file_name']); ?>" class="btn btn-coral btn-sm w-100">
                                    <i class="bi bi-download me-2"></i>Download PDF
                                </a>
                            </div>
                        </div>
                    </div>
                    <?php endwhile; 
                else: 
                    // Enhanced Static Data with More Recipes
                    $static_recipes = [
                        ["Summer Salad", "https://images.unsplash.com/photo-1546069901-ba9599a7e63c?w=400", "summer_salad.pdf"],
                        ["Creamy Tomato", "https://images.unsplash.com/photo-1512621776951-a57141f2eefd?w=400", "tomato_soup.pdf"],
                        ["Classic Pasta", "https://images.unsplash.com/photo-1473093226795-af9932fe5856?w=400", "pasta.pdf"],
                        ["Steak Master", "https://images.unsplash.com/photo-1546241072-48010ad2862c?w=400", "steak.pdf"],
                        ["Grilled Chicken", "https://images.unsplash.com/photo-1532550907401-a500c9a57435?w=400", "chicken.pdf"],
                        ["Chocolate Cake", "https://images.unsplash.com/photo-1578985545062-69928b1d9587?w=400", "cake.pdf"],
                        ["Sushi Rolls", "https://images.unsplash.com/photo-1579871494447-9811cf80d66c?w=400", "sushi.pdf"],
                        ["Roasted Veggies", "https://images.unsplash.com/photo-1512621776951-a57141f2eefd?w=400", "veggies.pdf"]
                    ];
                    foreach($static_recipes as $r): ?>
                    <div class="col-md-3">
                        <div class="card download-card overflow-hidden h-100">
                            <img src="<?php echo $r[1]; ?>" class="card-img-top recipe-img" alt="<?php echo $r[0]; ?>">
                            <div class="card-body text-center p-3">
                                <h6 class="fw-bold mb-3"><?php echo $r[0]; ?></h6>
                                <a href="?download=<?php echo $r[2]; ?>" class="btn btn-coral btn-sm w-100">
                                    <i class="bi bi-download me-2"></i>Download PDF
                                </a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; endif; ?>
            </div>
        </div>

        <div class="mb-5 py-4 bg-light rounded-4 p-4 border shadow-sm">
             <div class="row align-items-center">
                 <div class="col-md-2 text-center d-none d-md-block">
                     <i class="bi bi-shield-check display-1 text-success"></i>
                 </div>
                 <div class="col-md-10">
                     <h4 class="fw-bold"><i class="bi bi-shield-check me-2 text-success"></i>Kitchen Safety First</h4>
                     <p class="mb-0 text-muted">Remember: Always keep pot handles turned inward, never put water on a grease fire, and keep your knives sharpened to prevent slipping. Professional chefs prioritize safety to ensure creativity never stops.</p>
                 </div>
             </div>
        </div>

    </div>
</div> 

<?php include 'includes/footer.php'; ?>
<?php if (isset($_SESSION['user_id'])) { include 'includes/logout_modal.php'; } ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script>
    function convertUnits(type) {
        const cups = document.getElementById('inputCups');
        const ml = document.getElementById('inputMl');
        const tbsp = document.getElementById('inputTbsp');
        const tsp = document.getElementById('inputTsp');

        if(type === 'cup') {
            ml.value = (cups.value * 236.588).toFixed(2);
            tbsp.value = (cups.value * 16).toFixed(2);
            tsp.value = (cups.value * 48).toFixed(2);
        } else if(type === 'ml') {
            cups.value = (ml.value / 236.588).toFixed(2);
            tbsp.value = (ml.value / 14.787).toFixed(2);
            tsp.value = (ml.value / 4.929).toFixed(2);
        } else if(type === 'tbsp') {
            cups.value = (tbsp.value / 16).toFixed(2);
            ml.value = (tbsp.value * 14.787).toFixed(2);
            tsp.value = (tbsp.value * 3).toFixed(2);
        } else if(type === 'tsp') {
            cups.value = (tsp.value / 48).toFixed(2);
            ml.value = (tsp.value * 4.929).toFixed(2);
            tbsp.value = (tsp.value / 3).toFixed(2);
        }
    }
</script>
</body>
</html>