<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
include 'config.php';

// 1. Fetch User Data (Maintain Login Logic)
$is_logged_in = isset($_SESSION['user_id']);
if ($is_logged_in) {
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

// 2. Secured Download Logic
if (isset($_GET['download'])) {
    $file = basename($_GET['download']);
    $path = "uploads/resources/" . $file;
    
    if (file_exists($path)) {
        if (ob_get_level()) { ob_end_clean(); }
        
        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="'.$file.'"');
        header('Content-Transfer-Encoding: binary');
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Length: ' . filesize($path));
        
        readfile($path);
        exit;
    } else {
        echo "<script>alert('Error: File not found at $path. Please check your uploads/resources/ folder.'); window.location.href='educational_resources.php';</script>";
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FoodFusion</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="icon" type="image/svg+xml" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16' fill='%23ff5733'><path d='M8 16c3.314 0 6-2 6-5.5 0-1.5-.5-4-2.5-6 .25 1.5-1.25 2-1.25 2C11 4 9 .5 6 0c.357 2 .5 4-2 6-1.25 1-2 2.729-2 4.5C2 14 4.686 16 8 16Zm0-1c-1.657 0-3-1-3-2.75 0-.75.25-2 1.25-3C6.125 10 7 10.5 7 10.5c-.375-1.25.5-3.25 2-3.5-.179 1-.25 2 1 3 .625.5 1 1.364 1 2.25C11 14 9.657 15 8 15Z'/></svg>">
    
    <script type="module" src="https://unpkg.com/@splinetool/viewer@1.9.54/build/spline-viewer.js"></script>

    <style>
        :root { 
            --coral: #ff5733; --honey: #ffb347; --navy: #1a1a2e; 
            --forest: #1a4d2e; --cream: #fffdfa; 
        }
        body { background-color: var(--cream); color: #333; overflow-x: hidden;  }
        
        .edu-hero { 
            background: linear-gradient(135deg, var(--forest) 0%, #2d6a4f 100%); 
            color: white; padding: 60px 0; border-bottom: 6px solid var(--coral); position: relative; 
            min-height: 450px; display: flex; align-items: center;
        }
        
        .edu-card { 
            border: none; border-radius: 24px; background: white; 
            transition: all 0.4s cubic-bezier(0.165, 0.84, 0.44, 1); 
            box-shadow: 0 10px 30px rgba(0,0,0,0.05); height: 100%;
            display: block; text-decoration: none; color: inherit;
        }
        .edu-card:hover { transform: translateY(-10px); box-shadow: 0 20px 40px rgba(0,0,0,0.1); color: inherit; }
        
        .video-box { border-radius: 20px; overflow: hidden; background: #000; box-shadow: 0 8px 25px rgba(0,0,0,0.15); }
        .video-aspect { position: relative; padding-bottom: 56.25%; height: 0; }
        .video-aspect iframe { position: absolute; top: 0; left: 0; width: 100%; height: 100%; border: 0; }
        
        .title-border { font-weight: 700; color: var(--forest); border-left: 6px solid var(--coral); padding-left: 15px; margin-bottom: 40px; }
        .btn-download-pro { background: var(--coral); color: white; border-radius: 50px; font-weight: 600; padding: 12px 30px; transition: 0.3s; border: none; text-decoration: none; display: inline-block; }
        .btn-download-pro:hover { background: #e44d2d; transform: scale(1.05); color: white; }

        .topic-icon { width: 64px; height: 64px; display: flex; align-items: center; justify-content: center; border-radius: 18px; background: #fff5f2; color: var(--coral); margin-bottom: 20px; }
        .text-coral { color: var(--coral); }

        .carousel-fixed-height img {
            height: 280px; 
            object-fit: cover; 
            width: 100%;
        }
        .carousel-item { background: white; }
        
        .spline-container {
            width: 100%;
            height: 400px;
            position: relative;
        }

        .calc-input { border-radius: 12px; border: 2px solid #eee; padding: 12px; margin-bottom: 15px; }
        .calc-input:focus { border-color: var(--coral); box-shadow: none; }
        .accordion-button:not(.collapsed) { background-color: #fff5f2; color: var(--coral); }
        .farming-badge { position: absolute; top: 20px; right: 20px; border-radius: 10px; }

        .scrollable-table-body {
            display: block;
            max-height: 450px; 
            overflow-y: auto;
            width: 100%;
        }
        .scrollable-table-body tr {
            display: table;
            width: 100%;
            table-layout: fixed; 
        }
        thead.fixed-header {
            display: table;
            width: 100%;
            table-layout: fixed;
        }
        .scrollable-table-body::-webkit-scrollbar { width: 6px; }
        .scrollable-table-body::-webkit-scrollbar-thumb { background: var(--coral); border-radius: 10px; }
        .scrollable-table-body::-webkit-scrollbar-track { background: #f8f9fa; }
    </style>
</head>
<body>

<?php include 'includes/navbar.php'; ?>

<div class="edu-page-container">
    <div class="edu-hero">
        <div class="container">
            <div class="row align-items-center text-center text-md-start">
                <div class="col-md-7">
                    <h1 class="display-4 fw-bold text-white mb-3">Food & Energy Hub</h1>
                    <p class="text-white-50 lead mb-4" style="max-width: 600px;">Empowering the community with knowledge for a sustainable and greener future.</p>
                    <a href="#resources" class="btn btn-download-pro shadow">Explore Resources</a>
                </div>
                <div class="col-md-5 mt-5 mt-md-0 d-flex justify-content-center">
                    <div class="spline-container">
                        <spline-viewer url="https://prod.spline.design/6Wq1Q7YGe9vI8ZqY/scene.splinecode" hint="false"></spline-viewer>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="container my-5 pb-5" id="resources">
        
        <div class="mb-5 py-4">
            <h3 class="title-border">Biogas Energy Insights</h3>
            <div class="row g-4">
                <div class="col-lg-7">
                    <div class="edu-card p-4">
                        <div class="row align-items-center">
                            <div class="col-md-6">
                                <h5>Waste-to-Energy Estimator</h5>
                                <p class="text-muted small">Calculate how much biogas you can produce from your daily organic waste.</p>
                                <div class="mb-3">
                                    <label class="form-label fw-bold">Daily Organic Waste (kg)</label>
                                    <input type="number" id="wasteInput" class="form-control calc-input" placeholder="e.g. 5">
                                </div>
                                <button onclick="calculateEnergy()" class="btn btn-download-pro w-100">Calculate Potential</button>
                            </div>
                            <div class="col-md-6 text-center border-start mt-4 mt-md-0">
                                <div id="calcResult" style="display: none;">
                                    <h2 class="text-coral fw-bold mb-0"><span id="gasOutput">0</span> m³</h2>
                                    <p class="text-muted">Potential Biogas/Day</p>
                                    <hr>
                                    <p class="mb-1"><i class="bi bi-fire me-2 text-warning"></i>Equivalent to <strong><span id="cookingTime">0</span> hours</strong> of cooking</p>
                                </div>
                                <div id="calcPlaceholder">
                                    <i class="bi bi-calculator display-1 text-light"></i>
                                    <p class="text-muted">Enter waste amount to see results</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-5">
                    <div class="edu-card p-4 bg-light shadow-none border">
                        <h5 class="fw-bold mb-3"><i class="bi bi-diagram-3-fill me-2 text-coral"></i>The Biogas Process</h5>
                        
                        <div class="mt-3 small">
                            <ul class="list-unstyled">
                                <li class="mb-2"><i class="bi bi-1-circle-fill text-coral me-2"></i><strong>Input:</strong> Organic waste (food, manure).</li>
                                <li class="mb-2"><i class="bi bi-2-circle-fill text-coral me-2"></i><strong>Digestion:</strong> Bacteria break down waste without oxygen.</li>
                                <li class="mb-2"><i class="bi bi-3-circle-fill text-coral me-2"></i><strong>Output:</strong> Methane gas (energy) and organic fertilizer.</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="mb-5 mt-4">
            <h3 class="title-border">Educational Video Series</h3>
            <div class="row g-4">
                <div class="col-md-6">
                    <div class="edu-card p-4">
                        <div class="video-box mb-3">
                            <div class="video-aspect">
                                <iframe src="https://www.youtube.com/embed/Ofn7jqPDTeY" 
                                        title="Solar Cooking Technology"
                                        allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" 
                                        allowfullscreen></iframe>
                            </div>
                        </div>
                        <h5 class="fw-bold">Solar Cooking Technology</h5>
                        <p class="text-muted small">Learn clean, fuel-free cooking.</p>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="edu-card p-4">
                        <div class="video-box mb-3">
                            <div class="video-aspect">
                                <iframe src="https://www.youtube.com/embed/alljc5elqqw" 
                                        title="Biogas Technology"
                                        allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" 
                                        allowfullscreen></iframe>
                            </div>
                        </div>
                        <h5 class="fw-bold">Biogas: Turning Waste to Wealth</h5>
                        <p class="text-muted small">Converting organic waste into energy.</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="mb-5 py-4">
            <h3 class="title-border">Seasonal Farming Guide (Myanmar)</h3>
            <div class="row g-4">
                <div class="col-md-4">
                    <div class="edu-card p-4 position-relative">
                        <span class="badge bg-primary farming-badge">Rainy Season</span>
                        <div class="topic-icon bg-primary-subtle text-primary"><i class="bi bi-cloud-rain-heavy-fill fs-3"></i></div>
                        <h5 class="fw-bold">Monsoon Crops</h5>
                        <ul class="small text-muted ps-3">
                            <li>Rice (Paddy)</li>
                            <li>Corn (Maize)</li>
                            <li>Green Gram</li>
                        </ul>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="edu-card p-4 position-relative">
                        <span class="badge bg-warning text-dark farming-badge">Summer Season</span>
                        <div class="topic-icon bg-warning-subtle text-warning"><i class="bi bi-brightness-high-fill fs-3"></i></div>
                        <h5 class="fw-bold">Dry Season Crops</h5>
                        <ul class="small text-muted ps-3">
                            <li>Sesame</li>
                            <li>Peanuts</li>
                            <li>Sunflowers</li>
                        </ul>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="edu-card p-4 position-relative">
                        <span class="badge bg-info text-dark farming-badge">Winter Season</span>
                        <div class="topic-icon bg-info-subtle text-info"><i class="bi bi-snow2 fs-3"></i></div>
                        <h5 class="fw-bold">Cool Season Crops</h5>
                        <ul class="small text-muted ps-3">
                            <li>Wheat</li>
                            <li>Chickpeas</li>
                            <li>Potatoes</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <div class="mb-5 py-4">
            <h3 class="title-border">Sustainable Living Topics</h3>
            <div class="row g-4">
                <div class="col-md-4">
                    <a href="https://en.wikipedia.org/wiki/Solar_food_processing" target="_blank" class="edu-card p-4">
                        <div class="topic-icon"><i class="bi bi-sun-fill fs-3"></i></div>
                        <h5 class="fw-bold">Solar Drying</h5>
                        <p class="small text-muted">Preserving crops using solar thermal energy.</p>
                    </a>
                </div>
                <div class="col-md-4">
                    <a href="https://en.wikipedia.org/wiki/Compost" target="_blank" class="edu-card p-4 text-white" style="background: var(--forest);">
                        <div class="topic-icon" style="background: rgba(255,255,255,0.1); color: white;"><i class="bi bi-recycle fs-3"></i></div>
                        <h5 class="fw-bold">Organic Composting</h5>
                        <p class="small opacity-75">Transforming scraps into nutrient-rich soil.</p>
                    </a>
                </div>
                <div class="col-md-4">
                    <a href="https://en.wikipedia.org/wiki/Hydroponics" target="_blank" class="edu-card p-4">
                        <div class="topic-icon"><i class="bi bi-droplet-fill fs-3"></i></div>
                        <h5 class="fw-bold">Modern Hydroponics</h5>
                        <p class="small text-muted">Efficient farming with minimal energy.</p>
                    </a>
                </div>
            </div>
        </div>

        <div class="row g-4 mb-5">
            <div class="col-lg-8">
                <h3 class="title-border">Technical Guides & Manuals</h3>
                <div class="edu-card overflow-hidden">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="bg-light fixed-header">
                                <tr>
                                    <th class="ps-4">Resource Title</th>
                                    <th>Format</th>
                                    <th class="text-end pe-4">Download</th>
                                </tr>
                            </thead>
                            <tbody class="scrollable-table-body">
                                <?php
                                $res_query = $conn->query("SELECT * FROM educational_resources ORDER BY id DESC");
                                if($res_query && $res_query->num_rows > 0):
                                    while($res = $res_query->fetch_assoc()):
                                        $file_ext = strtoupper(pathinfo($res['file_name'], PATHINFO_EXTENSION));
                                        $icon = ($file_ext == 'PDF') ? 'bi-file-earmark-pdf text-danger' : 'bi-file-earmark-word text-primary';
                                ?>
                                <tr>
                                    <td class="ps-4 py-3">
                                        <div class="d-flex align-items-center">
                                            <i class="bi <?php echo $icon; ?> fs-4 me-3"></i>
                                            <div>
                                                <span class="d-block fw-bold"><?php echo htmlspecialchars($res['title']); ?></span>
                                                <small class="text-muted"><?php echo htmlspecialchars($res['description']); ?></small>
                                            </div>
                                        </div>
                                    </td>
                                    <td><span class="badge bg-light text-dark border"><?php echo $file_ext; ?></span></td>
                                    <td class="text-end pe-4">
                                        <a href="?download=<?php echo urlencode($res['file_name']); ?>" class="btn btn-download-pro btn-sm py-1 px-3">Get File</a>
                                    </td>
                                </tr>
                                <?php endwhile; else: ?>
                                <tr><td colspan="3" class="text-center py-4 text-muted">No resources found in database.</td></tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-4">
                <h3 class="title-border">Visual Learning</h3>
                <div class="edu-card overflow-hidden">
                    <div id="visualLearningCarousel" class="carousel slide" data-bs-ride="carousel">
                        <div class="carousel-indicators">
                            <button type="button" data-bs-target="#visualLearningCarousel" data-bs-slide-to="0" class="active"></button>
                            <button type="button" data-bs-target="#visualLearningCarousel" data-bs-slide-to="1"></button>
                            <button type="button" data-bs-target="#visualLearningCarousel" data-bs-slide-to="2"></button>
                        </div>

                        <div class="carousel-inner carousel-fixed-height">
                            <div class="carousel-item active" data-bs-interval="3000">
                                <img src="https://images.unsplash.com/photo-1509391366360-fe5bb6583e2c?auto=format&fit=crop&q=80&w=500" class="d-block w-100" alt="Solar Oven">
                                <div class="p-3 text-center">
                                    <h6 class="fw-bold text-coral">Solar Oven Design</h6>
                                    <p class="small mb-0 text-muted">How heat is trapped using reflectors.</p>
                                </div>
                            </div>
                            <div class="carousel-item" data-bs-interval="3000">
                                <img src="https://images.unsplash.com/photo-1532601224476-15c79f2f7a51?auto=format&fit=crop&q=80&w=500" class="d-block w-100" alt="Biogas System">
                                <div class="p-3 text-center">
                                    <h6 class="fw-bold text-coral">The Biogas Cycle</h6>
                                    <p class="small mb-0 text-muted">Turning organic waste into energy.</p>
                                </div>
                            </div>
                            <div class="carousel-item" data-bs-interval="3000">
                                <img src="https://images.unsplash.com/photo-1558449028-b53a39d100fc?auto=format&fit=crop&q=80&w=500" class="d-block w-100" alt="Hydroponics">
                                <div class="p-3 text-center">
                                    <h6 class="fw-bold text-coral">Hydroponics Guide</h6>
                                    <p class="small mb-0 text-muted">Growing food with 90% less water.</p>
                                </div>
                            </div>
                        </div>

                        <button class="carousel-control-prev" type="button" data-bs-target="#visualLearningCarousel" data-bs-slide="prev">
                            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                        </button>
                        <button class="carousel-control-next" type="button" data-bs-target="#visualLearningCarousel" data-bs-slide="next">
                            <span class="carousel-control-next-icon" aria-hidden="true"></span>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <div class="mb-5 py-4">
            <h3 class="title-border">Common Questions (FAQ)</h3>
            <div class="accordion border-0 shadow-sm rounded-4 overflow-hidden" id="faqAccordion">
                <div class="accordion-item border-0 border-bottom">
                    <h2 class="accordion-header">
                        <button class="accordion-button fw-bold" type="button" data-bs-toggle="collapse" data-bs-target="#faq1">
                            How much waste is needed for a household biogas digester?
                        </button>
                    </h2>
                    <div id="faq1" class="accordion-collapse collapse show" data-bs-parent="#faqAccordion">
                        <div class="accordion-body text-muted">
                            For a small family, about 5-10kg of daily organic waste (kitchen scraps + livestock waste) can produce enough gas for 2-3 hours of cooking.
                        </div>
                    </div>
                </div>
                <div class="accordion-item border-0 border-bottom">
                    <h2 class="accordion-header">
                        <button class="accordion-button collapsed fw-bold" type="button" data-bs-toggle="collapse" data-bs-target="#faq2">
                            Can I use solar drying for all types of crops?
                        </button>
                    </h2>
                    <div id="faq2" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                        <div class="accordion-body text-muted">
                            Most fruits, vegetables, and grains can be solar dried. However, the drying time varies. High-water content items like tomatoes require better ventilation.
                        </div>
                    </div>
                </div>
                <div class="accordion-item border-0 border-bottom">
                    <h2 class="accordion-header">
                        <button class="accordion-button collapsed fw-bold" type="button" data-bs-toggle="collapse" data-bs-target="#faq3">
                            Is the leftover waste from a biogas digester useful?
                        </button>
                    </h2>
                    <div id="faq3" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                        <div class="accordion-body text-muted">
                            Yes! The byproduct (slurry) is an excellent, nutrient-rich organic fertilizer that is even better than raw manure for your crops.
                        </div>
                    </div>
                </div>
                <div class="accordion-item border-0 border-bottom">
                    <h2 class="accordion-header">
                        <button class="accordion-button collapsed fw-bold" type="button" data-bs-toggle="collapse" data-bs-target="#faq4">
                            What is the best time for organic composting?
                        </button>
                    </h2>
                    <div id="faq4" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                        <div class="accordion-body text-muted">
                            Composting can be done year-round, but it works fastest in warmer months because heat helps the bacteria break down the waste more quickly.
                        </div>
                    </div>
                </div>
                <div class="accordion-item border-0 border-bottom">
                    <h2 class="accordion-header">
                        <button class="accordion-button collapsed fw-bold" type="button" data-bs-toggle="collapse" data-bs-target="#faq5">
                            How do I prevent pests in my seasonal garden?
                        </button>
                    </h2>
                    <div id="faq5" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                        <div class="accordion-body text-muted">
                            Use natural methods like crop rotation, intercropping (planting different crops together), and organic neem oil sprays to keep pests away without chemicals.
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

<?php include 'includes/footer.php'; ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script>
    function calculateEnergy() {
        const waste = document.getElementById('wasteInput').value;
        if(waste > 0) {
            document.getElementById('calcPlaceholder').style.display = 'none';
            document.getElementById('calcResult').style.display = 'block';
            
            const output = (waste * 0.05).toFixed(2);
            const time = (output * 2).toFixed(1);
            
            document.getElementById('gasOutput').innerText = output;
            document.getElementById('cookingTime').innerText = time;
        } else {
            alert("Please enter a valid amount of waste.");
        }
    }
</script>

</body>
</html>