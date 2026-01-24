<?php
include 'config.php';

$is_logged_in = isset($_SESSION['user_id']); 
$current_username = "Guest";
$current_role = "user";
$current_user_img = ""; 

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
    <?php 

    include 'includes/link_and_title.php'; ?>
    
    <style>
        :root { 
            --coral: #ff5733; 
            --navy: #0f172a; 
            --glass: rgba(255, 255, 255, 0.03);
            --text-muted: #64748b;
        }

        body { 
            background-color: #ffffff; 
            
            color: var(--navy);
            overflow-x: hidden;
        }

        h1, h2, .playfair { font-family: 'Playfair Display', serif; }

        /* Smooth Floating Animation */
        @keyframes float {
            0% { transform: translateY(0px); }
            50% { transform: translateY(-20px); }
            100% { transform: translateY(0px); }
        }

        /* Hero Section with Parallax */
        .hero-visual {
            height: 90vh;
            background: linear-gradient(to right, rgba(15, 23, 42, 0.9), rgba(15, 23, 42, 0.2)), 
                        url('https://images.unsplash.com/photo-1559339352-11d035aa65de?auto=format&fit=crop&w=1600&q=80');
            background-attachment: fixed;
            background-size: cover;
            display: flex;
            align-items: center;
            clip-path: polygon(0 0, 100% 0, 100% 90%, 0% 100%);
        }

        /* Philosophy Section - Minimalist & Clean */
        .philosophy-grid { padding: 120px 0; }
        .overlap-img {
            position: relative;
            border-radius: 40px;
            z-index: 1;
            box-shadow: 0 50px 100px -20px rgba(0,0,0,0.25);
        }

        /* Value Cards with Glassmorphism */
        .value-card {
            border: 1px solid #f1f5f9;
            border-radius: 32px;
            padding: 45px;
            transition: all 0.5s cubic-bezier(0.23, 1, 0.32, 1);
            background: #ffffff;
        }
        .value-card:hover {
            border-color: var(--coral);
            box-shadow: 0 40px 80px -15px rgba(255, 87, 51, 0.15);
            transform: translateY(-15px);
        }

        /* Team Image Styling */
        .member-wrapper {
            position: relative;
            margin-bottom: 30px;
            overflow: hidden;
            border-radius: 30px;
        }
        .member-img {
            transition: transform 0.6s ease;
            filter: grayscale(100%);
        }
        .member-wrapper:hover .member-img {
            transform: scale(1.1);
            filter: grayscale(0%);
        }

        .stat-circle {
            border: 1px dashed var(--coral);
            width: 150px; height: 150px;
            border-radius: 50%;
            display: flex; flex-direction: column;
            align-items: center; justify-content: center;
            margin: 0 auto;
        }
    </style>
</head>
<body>

<?php include 'includes/navbar.php'; ?>

<section class="hero-visual">
    <div class="container">
        <div class="row">
            <div class="col-lg-7 text-white" data-aos="fade-up">
                <span class="text-coral fw-bold text-uppercase mb-3 d-block" style="letter-spacing: 3px;">Since 2024</span>
                <h1 class="display-1 fw-bold mb-4">Beyond <br><i class="playfair text-coral">Gastronomy.</i></h1>
                <p class="fs-5 opacity-75 mb-5 lh-lg">FoodFusion isn't just a platform; it's a global movement to unite humanity through the art of contemporary culinary fusion.</p>
                <div class="stat-row d-flex gap-5">
                    <div><h3 class="fw-bold mb-0">50+</h3><small class="opacity-50">Global Cuisines</small></div>
                    <div class="vr"></div>
                    <div><h3 class="fw-bold mb-0">12k</h3><small class="opacity-50">Active Foodies</small></div>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="philosophy-grid">
    <div class="container">
        <div class="row align-items-center g-5">
            <div class="col-lg-6">
                <div class="position-relative">
                    <img src="https://images.unsplash.com/photo-1551218808-94e220e084d2?auto=format&fit=crop&w=800&q=80" class="img-fluid overlap-img" alt="Philosophy">
                    <div class="bg-coral p-4 rounded-4 shadow-lg position-absolute d-none d-md-block" style="bottom: -30px; right: -30px; width: 250px;">
                        <p class="text-white mb-0 italic">"Innovation is the main ingredient of every dish we serve."</p>
                    </div>
                </div>
            </div>
            <div class="col-lg-6 ps-lg-5">
                <h6 class="text-coral fw-bold text-uppercase mb-3">Culinary Philosophy</h6>
                <h2 class="display-4 fw-bold mb-4">A Dialogue Between <br>Tradition & Future.</h2>
                <p class="text-muted lh-lg mb-4">We believe that culinary traditions shouldn't be static museums. Instead, they should be a living language. Our team of Michelin-experienced chefs work tirelessly to dissect classical recipes and reconstruct them using avant-garde techniques.</p>
                <ul class="list-unstyled">
                    <li class="mb-3"><i class="bi bi-check2-circle text-coral me-2"></i> Respect for original ingredient DNA</li>
                    <li class="mb-3"><i class="bi bi-check2-circle text-coral me-2"></i> Scientific approach to flavor pairing</li>
                    <li><i class="bi bi-check2-circle text-coral me-2"></i> Sustainable sourcing from ethical farms</li>
                </ul>
            </div>
        </div>
    </div>
</section>

<section class="py-5" style="background: #fcfcfc;">
    <div class="container py-5">
        <div class="text-center mb-5">
            <h2 class="display-5 fw-bold">Our Core Ethos</h2>
            <div class="mx-auto" style="width: 50px; height: 3px; background: var(--coral);"></div>
        </div>
        <div class="row g-4">
            <div class="col-md-4">
                <div class="value-card h-100">
                    <h1 class="display-4 fw-bold text-light mb-4">01</h1>
                    <h3>Integrity</h3>
                    <p class="text-muted">Transparency in every calorie and every source. We believe you deserve to know exactly what fuels your body.</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="value-card h-100">
                    <h1 class="display-4 fw-bold text-light mb-4">02</h1>
                    <h3>Diversity</h3>
                    <p class="text-muted">Our platform celebrates the spice of life. We bring the street food of Bangkok and the bistros of Paris together.</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="value-card h-100">
                    <h1 class="display-4 fw-bold text-light mb-4">03</h1>
                    <h3>Evolution</h3>
                    <p class="text-muted">Constant learning. We update our culinary databases weekly with the latest in nutritional science.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="py-5 mb-5">
    <div class="container py-5">
        <div class="row mb-5 align-items-end">
            <div class="col-md-6">
                <h2 class="display-5 fw-bold">The Visionaries</h2>
            </div>
            <div class="col-md-6 text-md-end">
                <p class="text-muted">A collective of artists, scientists, and gourmets.</p>
            </div>
        </div>
        
        <div class="row g-4">
            <div class="col-lg-3 col-md-6">
                <div class="member-wrapper">
                    <img src="https://images.unsplash.com/photo-1583394293214-28ded15ee548?auto=format&fit=crop&w=600&q=80" class="img-fluid member-img" alt="Chef">
                </div>
                <h5 class="fw-bold mb-1">Dr. Julian Vane</h5>
                <small class="text-coral text-uppercase fw-bold">Chief Taste Scientist</small>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="member-wrapper">
                    <img src="https://images.unsplash.com/photo-1595273670150-db0a3d37d482?auto=format&fit=crop&w=600&q=80" class="img-fluid member-img" alt="Chef">
                </div>
                <h5 class="fw-bold mb-1">Chef Elena Sato</h5>
                <small class="text-coral text-uppercase fw-bold">Fusion Lead</small>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="member-wrapper">
                    <img src="https://images.unsplash.com/photo-1519085360753-af0119f7cbe7?auto=format&fit=crop&w=600&q=80" class="img-fluid member-img" alt="Founder">
                </div>
                <h5 class="fw-bold mb-1">Marcus Thorne</h5>
                <small class="text-coral text-uppercase fw-bold">Founder & CEO</small>
            </div>
            <div class="col-lg-3 col-md-6 text-center">
                <div class="stat-circle mb-4">
                    <h3 class="fw-bold mb-0 text-coral">Join</h3>
                    <small>Our Team</small>
                </div>
                <p class="small text-muted">We are always looking for passionate creators.</p>
                <a href="#" class="btn btn-outline-dark btn-sm rounded-pill px-4">Apply Now</a>
            </div>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>
<?php include 'includes/login_modal.php'; ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>