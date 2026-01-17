<?php
// 1. Load Session and Config
include 'config.php';

// 2. Set Default values (To prevent errors for Guests)
$is_logged_in = isset($_SESSION['user_id']); 
$current_username = "Guest";
$current_role = "user";
$current_user_img = ""; 

// 3. Fetch User Information from Database only if logged in
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
    } else {
        session_destroy();
        $is_logged_in = false;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Food Fusion - Discover International Flavors</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <style>
        :root { --coral: #ff5733; --honey: #ffb347; --navy: #1a1a2e; --cream: #fffdfa; --glass: rgba(255, 255, 255, 0.8); }
        body { background-color: var(--cream); font-family: 'Poppins', 'Segoe UI', sans-serif; overflow-x: hidden; color: #333; }
        
        .hero-section {
            background: linear-gradient(rgba(255,253,250,0.8), rgba(255,253,250,0.8)), url('https://img.freepik.com/free-photo/top-view-circular-food-frame_23-2148723455.jpg');
            background-size: cover; background-attachment: fixed; min-height: 550px;
        }

        .trend-card, .fancy-food-card, .stat-box {
            border: none; border-radius: 24px; transition: all 0.4s cubic-bezier(0.165, 0.84, 0.44, 1);
            background: white; border: 1px solid rgba(0,0,0,0.03);
        }
        .trend-card:hover, .fancy-food-card:hover { transform: translateY(-12px); box-shadow: 0 20px 40px rgba(0,0,0,0.08); }

        .stat-box { padding: 40px 20px; border-bottom: 6px solid var(--honey); }
        .stat-number { font-size: 2.5rem; font-weight: 800; color: var(--coral); display: block; }

        .chef-section { background: var(--navy); color: white; border-radius: 40px; padding: 50px; position: relative; overflow: hidden; }
        .chef-section::before { content: '"'; position: absolute; font-size: 200px; color: rgba(255,255,255,0.05); top: -50px; left: 20px; }

        .event-carousel { border-radius: 40px; overflow: hidden; box-shadow: 0 25px 50px rgba(0,0,0,0.15); }
        .carousel-item img { height: 450px; object-fit: cover; filter: brightness(0.5) contrast(1.1); }
        
        .btn-fancy-cart {
            background: linear-gradient(45deg, var(--coral), var(--honey));
            color: white; border-radius: 30px; font-weight: 700; padding: 12px; border: none;
        }
        
        .newsletter-section {
            background: var(--navy); border-radius: 50px; padding: 80px 40px; color: white;
            background: linear-gradient(135deg, var(--navy) 0%, #252545 100%);
        }
        .btn-coral { background-color: var(--coral); color: white; }
        .btn-honey { background-color: var(--honey); color: white; }
    </style>
</head>
<body>

<?php include 'includes/navbar.php'; ?>

<section class="hero-section d-flex align-items-center">
    <div class="container text-center py-5">
        <span class="badge text-dark px-3 py-2 rounded-pill shadow-sm mb-3 fw-bold">WORLDWIDE CUISINES</span>
        <h1 class="display-2 fw-bold text-dark mb-4" style="letter-spacing: -3px;">A Symphony of <span style="color: var(--coral);">Flavors</span></h1>
        <div class="row justify-content-center">
            <div class="col-md-7">
                <p class="lead fw-medium text-secondary mb-5 lh-lg">"FoodFusion's mission is to blend diverse flavors from around the world into one place, bringing joy to the community through healthy and fresh culinary experiences."</p>
                <div style="width: 100px; height: 5px; background: var(--honey); margin: 0 auto; border-radius: 10px;"></div>
            </div>
        </div>
    </div>
</section>

<div class="container mb-5 mt-n5 position-relative" style="z-index: 5;">
    <div class="row g-4 text-center">
        <div class="col-md-3 col-6"><div class="stat-box shadow-lg"><span class="stat-number">500+</span><p class="text-muted mb-0 small fw-bold">Daily Recipes</p></div></div>
        <div class="col-md-3 col-6"><div class="stat-box shadow-lg"><span class="stat-number">10k+</span><p class="text-muted mb-0 small fw-bold">Happy Foodies</p></div></div>
        <div class="col-md-3 col-6"><div class="stat-box shadow-lg"><span class="stat-number">15+</span><p class="text-muted mb-0 small fw-bold">Expert Chefs</p></div></div>
        <div class="col-md-3 col-6"><div class="stat-box shadow-lg"><span class="stat-number">4.9</span><p class="text-muted mb-0 small fw-bold">Average Rating</p></div></div>
    </div>
</div>

<div class="container my-5">
    <div class="chef-section shadow-sm">
        <div class="row align-items-center">
            <div class="col-lg-2 text-center text-lg-start">
                <i class="bi bi-patch-check-fill text-honey" style="font-size: 4rem;"></i>
            </div>
            <div class="col-lg-10">
                <h4 class="fw-bold">Chef's Secret of the Week</h4>
                <p class="mb-0 text-white-50 lh-lg">"To make meat more tender when cooking, adding a small amount of honey or lemon juice not only deepens the flavor but also significantly improves the texture."</p>
            </div>
        </div>
    </div>
</div>

<div class="container my-5 pt-4">
    <div class="d-flex justify-content-between align-items-end mb-4">
        <div>
            <h3 class="fw-bold mb-0">Culinary Experiences</h3>
            <p class="text-muted mb-0">Join our exclusive live sessions and events.</p>
        </div>
        <div class="d-none d-md-block">
            <button class="btn btn-outline-dark rounded-pill px-4" <?php if(!$is_logged_in) echo 'data-bs-toggle="modal" data-bs-target="#loginModal"'; ?>>View All Events</button>
        </div>
    </div>
    <div id="eventCarousel" class="carousel slide event-carousel" data-bs-ride="carousel">
        <div class="carousel-inner">
            <div class="carousel-item active">
                <img src="https://images.unsplash.com/photo-1556910103-1c02745aae4d?auto=format&fit=crop&w=1200&q=80" class="d-block w-100">
                <div class="carousel-caption">
                    <span class="badge bg-danger mb-3 px-3 py-2 rounded-pill">LIVE WORKSHOP</span>
                    <h2 class="display-5 fw-bold">Fusion Pasta Masterclass</h2>
                    <p class="lead">Learn the secrets of Italian-Thai fusion pasta. <br><strong>Date: Jan 25, 2026</strong></p>
                    <button class="btn btn-coral px-5 rounded-pill fw-bold shadow" <?php echo $is_logged_in ? '' : 'data-bs-toggle="modal" data-bs-target="#loginModal"'; ?>>Secure Your Spot</button>
                </div>
            </div>
            <div class="carousel-item">
                <img src="https://images.unsplash.com/photo-1507048331197-7d4ac70811cf?auto=format&fit=crop&w=1200&q=80" class="d-block w-100">
                <div class="carousel-caption">
                    <span class="badge bg-warning text-dark mb-3 px-3 py-2 rounded-pill">WEBINAR</span>
                    <h2 class="display-5 fw-bold">Sustainable Cooking Trends</h2>
                    <p class="lead">Expert talk on zero-waste kitchen techniques. <br><strong>Date: Feb 05, 2026</strong></p>
                    <button class="btn btn-honey px-5 rounded-pill fw-bold shadow text-white" <?php echo $is_logged_in ? '' : 'data-bs-toggle="modal" data-bs-target="#loginModal"'; ?>>Join Webinar</button>
                </div>
            </div>
        </div>
        <button class="carousel-control-prev" type="button" data-bs-target="#eventCarousel" data-bs-slide="prev"><span class="carousel-control-prev-icon"></span></button>
        <button class="carousel-control-next" type="button" data-bs-target="#eventCarousel" data-bs-slide="next"><span class="carousel-control-next-icon"></span></button>
    </div>
</div>

<div class="container my-5 py-4">
    <div class="row g-5">
        <div class="col-lg-4">
            <h4 class="fw-bold mb-4">Market Trends 2026</h4>
            <div class="card trend-card p-4 mb-3">
                <div class="d-flex gap-4 align-items-center">
                    <div class="bg-light p-3 rounded-4"><i class="bi bi-graph-up-arrow text-success fs-3"></i></div>
                    <div><h6 class="mb-0 fw-bold">Plant-Based Fusion</h6><small class="text-muted">Growing by 40% globally</small></div>
                </div>
            </div>
            <div class="card trend-card p-4">
                <div class="d-flex gap-4 align-items-center">
                    <div class="bg-light p-3 rounded-4"><i class="bi bi-fire text-danger fs-3"></i></div>
                    <div><h6 class="mb-0 fw-bold">Smoky Aromas</h6><small class="text-muted">New wood-fired tech</small></div>
                </div>
            </div>
        </div>
        <div class="col-lg-8">
            <h4 class="fw-bold mb-4">Latest From The Kitchen</h4>
            <div class="bg-white p-4 rounded-4 shadow-sm border-0 trend-card d-flex align-items-center" style="height: calc(100% - 48px);">
                <div class="row g-4 align-items-center">
                    <div class="col-md-5"><img src="https://images.unsplash.com/photo-1504674900247-0877df9cc836?auto=format&fit=crop&w=600&q=80" class="img-fluid rounded-4 shadow-sm"></div>
                    <div class="col-md-7">
                        <span class="text-coral fw-bold small text-uppercase">Exclusive • Jan 2026</span>
                        <h3 class="fw-bold mt-2">FoodFusion Digital Expands</h3>
                        <p class="text-muted lh-base">We have launched high-tech digital branches to provide faster service and a seamless experience for our community.</p>
                        <a href="#" class="btn btn-link text-coral p-0 fw-bold text-decoration-none" <?php if(!$is_logged_in) echo 'data-bs-toggle="modal" data-bs-target="#loginModal"'; ?>>READ FULL STORY <i class="bi bi-arrow-right ms-2"></i></a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="container my-5 py-5">
    <h3 class="text-center fw-bold mb-5">Trusted by Thousands</h3>
    <div class="row g-4 pt-4">
        <div class="col-md-4">
            <div class="card testimonial-card shadow-sm text-center px-3 pb-3 border-0 rounded-4">
                <div class="card-body">
                    <img src="https://i.pravatar.cc/150?u=1" class="shadow-lg mb-3" style="width: 80px; height: 80px; border-radius: 50%; margin-top: -50px; object-fit: cover;">
                    <p class="text-muted italic mb-4">"The taste is truly unique. I absolutely loved the perfect blend of fusion flavors."</p>
                    <h6 class="fw-bold mb-1">Ma Thida</h6>
                    <div class="text-warning small"><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i></div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card testimonial-card shadow-sm text-center px-3 pb-3 border-0 rounded-4">
                <div class="card-body">
                    <img src="https://i.pravatar.cc/150?u=2" class="shadow-lg mb-3" style="width: 80px; height: 80px; border-radius: 50%; margin-top: -50px; object-fit: cover;">
                    <p class="text-muted italic mb-4">"The recipes are clear and easy to follow. I feel like a Master Chef right at my home!"</p>
                    <h6 class="fw-bold mb-1">Ko Aung Myo</h6>
                    <div class="text-warning small"><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i></div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card testimonial-card shadow-sm text-center px-3 pb-3 border-0 rounded-4">
                <div class="card-body">
                    <img src="https://i.pravatar.cc/150?u=3" class="shadow-lg mb-3" style="width: 80px; height: 80px; border-radius: 50%; margin-top: -50px; object-fit: cover;">
                    <p class="text-muted italic mb-4">"I really appreciate the focus on healthy living. It's perfectly suitable for the kids as well."</p>
                    <h6 class="fw-bold mb-1">Daw Hla</h6>
                    <div class="text-warning small"><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-half"></i></div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="container mb-5 pb-5">
    <div class="newsletter-section text-center shadow-lg">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <h2 class="display-5 fw-bold mb-4">Stay Inspired, Stay Delicious</h2>
                <p class="mb-5 text-white-50 fs-5">We will deliver new recipes and health tips directly to your inbox.</p>
                <form class="d-flex gap-3 p-2 bg-white rounded-pill shadow-sm mx-auto" style="max-width: 600px;">
                    <input type="email" class="form-control border-0 rounded-pill ps-4" placeholder="Your best email address">
                    <button type="<?php echo $is_logged_in ? 'submit' : 'button'; ?>" 
                            class="btn btn-fancy-cart px-5"
                            <?php if(!$is_logged_in) echo 'data-bs-toggle="modal" data-bs-target="#loginModal"'; ?>>
                        Join Now
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
<?php include 'includes/login_modal.php'; ?>
<?php include 'includes/logout_modal.php'; ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script>
    window.addEventListener('load', function() {
        <?php if(isset($_SESSION['msg'])): ?>
            var myModal = new bootstrap.Modal(document.getElementById('loginModal'));
            myModal.show();
        <?php endif; ?>
    });
</script>

</body>
</html>