<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
include 'config.php';

// ၁။ Login ရှိမရှိ စစ်ဆေးခြင်း (Redirect လုပ်ထားသည်ကို ဖယ်ရှားလိုက်ပါသည်)
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

// Download Logic (Login မဝင်ထားလဲ Download လုပ်ခွင့်ပေးထားသည်)
if (isset($_GET['download'])) {
    $file = basename($_GET['download']);
    $path = "uploads/recipes/" . $file;
    if (file_exists($path)) {
        header('Content-Type: application/pdf');
        header('Content-Disposition: attachment; filename="'.$file.'"');
        readfile($path);
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Culinary Resources | Food Fusion</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <style>
        :root { --coral: #ff5733; --honey: #ffb347; --navy: #1a1a2e; --cream: #fffdfa; --glass: rgba(255, 255, 255, 0.8); }
        .resource-page-wrapper {
            background-color: #fffdfa;
            font-family: 'Poppins', sans-serif;
            color: #444;
            overflow-x: hidden;
            /* Navbar အောက်ရောက်မသွားစေရန် Padding ထပ်ထည့်ပေးထားပါသည် */
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
    </style>
</head>
<body>

<?php include 'includes/navbar.php'; ?>

<div class="resource-page-wrapper">

    <div class="hero-section text-center">
        <div class="container">
            <h1 class="display-5 fw-bold text-white">Culinary Masterclass</h1>
            <p class="text-white-50 lead">ဟင်းချက်အတတ်ပညာနှင့် မီးဖိုချောင်သုံး လျှို့ဝှက်ချက်များ</p>
        </div>
    </div>

    <div class="container my-5">
        <div class="mb-5">
            <h3 class="section-title">Cooking Techniques & Tutorials</h3>
            <div class="row g-4">
                <div class="col-lg-7">
                    <div class="video-card">
                        <div class="video-container">
                            <iframe src="https://www.youtube.com/embed/G-Fg7l7G1zw" allowfullscreen></iframe>
                        </div>
                    </div>
                    <div class="mt-3 px-2">
                        <h4 class="fw-bold">Essential Knife Skills for Every Chef</h4>
                        <p class="text-muted">အခြေခံ ဓားကိုင်နည်းမှစ၍ ပါးပါးလှီးနည်း၊ အတုံးလိုက်တုံးနည်းနှင့် ကျွမ်းကျင်စွာ အသုံးပြုနည်း သင်ခန်းစာ။</p>
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
                <div class="col-md-4">
                    <div class="card download-card p-4 h-100">
                        <span class="hack-badge mb-3 text-uppercase">Time Saver</span>
                        <h5 class="fw-bold">Peeling Garlic in Seconds</h5>
                        <p class="small text-muted mb-0">ကြက်သွန်ဖြူ အခွံမြန်မြန်နွှာနည်းနှင့် မီးဖိုချောင် အလုပ်ရှုပ်သက်သာစေမည့် နည်းလမ်းများ။</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card download-card p-4 h-100">
                        <span class="hack-badge mb-3 text-uppercase">Freshness</span>
                        <h5 class="fw-bold">Keeping Herbs Fresh</h5>
                        <p class="small text-muted mb-0">ဟင်းခတ်အမွှေးအကြိုင်များကို တစ်ပတ်ထက်မက လတ်လတ်ဆတ်ဆတ် သိမ်းဆည်းနည်း လျှို့ဝှက်ချက်။</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card download-card p-4 h-100">
                        <span class="hack-badge mb-3 text-uppercase">Emergency</span>
                        <h5 class="fw-bold">Fixing Over-salted Soup</h5>
                        <p class="small text-muted mb-0">ဟင်းရည် ငန်သွားပါက အရသာ ပြန်လည်ထိန်းညှိနိုင်မည့် အရေးပေါ် မီးဖိုချောင်သုံး နည်းလမ်း။</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="mb-5">
            <h3 class="section-title">Printable Recipe Cards</h3>
            <div class="row g-4">
                <?php
                $recipes = [
                    ["Summer Salad", "https://images.unsplash.com/photo-1546069901-ba9599a7e63c?auto=format&fit=crop&w=400&q=80", "summer_salad.pdf"],
                    ["Creamy Tomato", "https://images.unsplash.com/photo-1512621776951-a57141f2eefd?auto=format&fit=crop&w=400&q=80", "tomato_soup.pdf"],
                    ["Thin Crust Pizza", "https://images.unsplash.com/photo-1565299624946-b28f40a0ae38?auto=format&fit=crop&w=400&q=80", "pizza.pdf"],
                    ["Avocado Toast", "https://images.unsplash.com/photo-1482049016688-2d3e1b311543?auto=format&fit=crop&w=400&q=80", "avocado_toast.pdf"]
                ];
                foreach($recipes as $r): ?>
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
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</div> 

<?php include 'includes/footer.php'; ?>

<?php 
// Login ဝင်ထားမှသာ Logout Modal ကို ထည့်ပေးရန်
if (isset($_SESSION['user_id'])) {
    include 'includes/logout_modal.php'; 
}
?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>