<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
include 'config.php';

// ၁။ User Data ကို Database မှ ဆွဲထုတ်ခြင်း (Login ဝင်ထားမှသာ)
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

// Download Logic
if (isset($_GET['download'])) {
    $file = basename($_GET['download']);
    $path = "uploads/resources/" . $file;
    if (file_exists($path)) {
        header('Content-Type: application/octet-stream');
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
    <title>Educational Resources | Food Fusion</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <style>
        :root { --coral: #ff5733; --honey: #ffb347; --navy: #1a1a2e; --cream: #fffdfa; --glass: rgba(255, 255, 255, 0.8); }
        /* Scoped CSS - Page content အတွက်သာ သက်ရောက်စေရန် wrapper သုံးထားသည် */
        .edu-page-container {
            background-color: #fffdfa;
            font-family: 'Poppins', sans-serif;
            color: #333;
        }

        .edu-hero { 
            background: linear-gradient(135deg, #1a4d2e 0%, #2d6a4f 100%); 
            color: white; padding: 70px 0; 
            border-bottom: 5px solid #ff5733;
        }
        
        .edu-card {
            border: none; border-radius: 20px; background: white;
            transition: all 0.3s ease; box-shadow: 0 4px 20px rgba(0,0,0,0.06);
            height: 100%;
        }
        .edu-card:hover { transform: translateY(-8px); box-shadow: 0 15px 30px rgba(0,0,0,0.12); }
        
        .video-box { border-radius: 18px; overflow: hidden; background: #000; box-shadow: 0 8px 25px rgba(0,0,0,0.15); }
        .video-aspect { position: relative; padding-bottom: 56.25%; height: 0; }
        .video-aspect iframe { position: absolute; top: 0; left: 0; width: 100%; height: 100%; border: 0; }
        
        .title-border { 
            font-weight: 800; color: #1a4d2e; 
            border-left: 6px solid #ff5733; padding-left: 15px; margin-bottom: 40px;
        }
        
        .btn-download-pro { 
            background: #ff5733; color: white; border-radius: 50px; 
            font-weight: 600; border: none; padding: 10px 25px; transition: 0.3s;
        }
        .btn-download-pro:hover { background: #e44d2d; color: white; transform: scale(1.05); }

        .topic-icon {
            width: 60px; height: 60px; display: flex; align-items: center; justify-content: center;
            border-radius: 15px; background: #fff5f2; color: #ff5733; margin-bottom: 15px;
        }
    </style>
</head>
<body>

<?php include 'includes/navbar.php'; ?>

<div class="edu-page-container">

    <div class="edu-hero text-center">
        <div class="container">
            <h1 class="display-5 fw-bold text-white">Food & Energy Education</h1>
            <p class="text-white-50 lead">ပြန်လည်ပြည့်ဖြိုးမြဲစွမ်းအင်နှင့် စဉ်ဆက်မပြတ် အစားအသောက်စနစ်</p>
        </div>
    </div>

    <div class="container my-5 pb-5">
        
        <div class="mb-5">
            <h3 class="title-border">Educational Videos</h3>
            <div class="row g-4">
                <div class="col-md-6">
                    <div class="edu-card p-3">
                        <div class="video-box mb-3">
                            <div class="video-aspect">
                                <iframe src="https://www.youtube.com/embed/v4u6S-Yj3I0" allowfullscreen></iframe>
                            </div>
                        </div>
                        <h5 class="fw-bold">Solar Cooking Technology</h5>
                        <p class="text-muted small">နေရောင်ခြည်စွမ်းအင်သုံး မီးဖိုများဖြင့် သဘာဝပတ်ဝန်းကျင်ကို ကာကွယ်ပါ။</p>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="edu-card p-3">
                        <div class="video-box mb-3">
                            <div class="video-aspect">
                                <iframe src="https://www.youtube.com/embed/7YpT_LqGid4" allowfullscreen></iframe>
                            </div>
                        </div>
                        <h5 class="fw-bold">Biogas: Waste to Energy</h5>
                        <p class="text-muted small">စွန့်ပစ်ပစ္စည်းများမှ ဇီဝဓာတ်ငွေ့ ထုတ်လုပ်ပုံ လေ့လာရန်။</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="mb-5 py-3">
            <h3 class="title-border">Green Energy Topics</h3>
            <div class="row g-4">
                <div class="col-md-4">
                    <div class="edu-card p-4">
                        <div class="topic-icon"><i class="bi bi-sun-fill fs-3"></i></div>
                        <h5 class="fw-bold">Solar Drying</h5>
                        <p class="small text-muted">အသီးအနှံများကို နေရောင်ခြည်ဖြင့် ကြာရှည်သိမ်းဆည်းနည်း။</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="edu-card p-4 text-white" style="background: #1a4d2e;">
                        <div class="topic-icon" style="background: rgba(255,255,255,0.1); color: white;"><i class="bi bi-recycle fs-3"></i></div>
                        <h5 class="fw-bold">Composting</h5>
                        <p class="small opacity-75">အစားအသောက် အကြွင်းအကျန်များကို သဘာဝမြေဩဇာလုပ်ခြင်း။</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="edu-card p-4">
                        <div class="topic-icon"><i class="bi bi-droplet-fill fs-3"></i></div>
                        <h5 class="fw-bold">Hydroponics</h5>
                        <p class="small text-muted">ရေကို အကျိုးရှိရှိသုံးပြီး စွမ်းအင်သက်သာစွာ စိုက်ပျိုးနည်း။</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-4">
            <div class="col-lg-8">
                <h3 class="title-border">Guides & Manuals</h3>
                <div class="edu-card overflow-hidden">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th class="ps-4 py-3">File Name</th>
                                    <th>Format</th>
                                    <th class="text-end pe-4">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td class="ps-4 fw-bold">Solar Cooker DIY Guide.pdf</td>
                                    <td><span class="badge bg-danger-subtle text-danger px-3">PDF</span></td>
                                    <td class="text-end pe-4">
                                        <a href="?download=Solar_Cooker_DIY_Guide.pdf" class="btn btn-download-pro btn-sm">Download</a>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="ps-4 fw-bold">Nutrition & Energy Savings.docx</td>
                                    <td><span class="badge bg-primary-subtle text-primary px-3">DOCX</span></td>
                                    <td class="text-end pe-4">
                                        <a href="?download=Nutrition_Energy_Savings.docx" class="btn btn-download-pro btn-sm">Download</a>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="col-lg-4">
                <h3 class="title-border">Infographic</h3>
                <div class="edu-card p-2 text-center">
                    <img src="https://img.freepik.com/free-vector/green-energy-infographic_23-2148530342.jpg" class="img-fluid rounded-4 mb-2" alt="Energy Cycle">
                    <p class="small fw-bold text-uppercase mt-2">Sustainable Food Cycle</p>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
<?php include 'includes/logout_modal.php'; ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>