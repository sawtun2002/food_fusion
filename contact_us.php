<?php 
if (session_status() === PHP_SESSION_NONE) { session_start(); }
include 'config.php';

// ၁။ Login ရှိမရှိ စစ်ဆေးခြင်း
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
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Us - Food Fusion</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <style>
    

        /* --- Contact Page မူရင်း Body Content Styles သီးသန့် --- */
        .contact-header {
            background: linear-gradient(rgba(26, 26, 46, 0.8), rgba(26, 26, 46, 0.8)), url('https://images.unsplash.com/photo-1495474472287-4d71bcdd2085?auto=format&fit=crop&w=1200&q=80');
            background-size: cover; background-position: center;
            padding: 100px 0; color: white; text-align: center;
        }
        
        .contact-card { border: none; border-radius: 20px; transition: 0.3s; background: white; padding: 30px; height: 100%; box-shadow: 0 10px 30px rgba(0,0,0,0.05); }
        .icon-box { width: 60px; height: 60px; border-radius: 15px; display: flex; align-items: center; justify-content: center; font-size: 24px; margin-bottom: 20px; }
        .text-coral { color: var(--coral); }
        
        .form-control { border-radius: 12px; padding: 12px 20px; border: 1px solid #eee; background: #f8f9fa; }
        .btn-send { background: var(--coral); color: white; border-radius: 30px; padding: 12px 40px; font-weight: 600; border: none; }
        
        .social-link { width: 45px; height: 45px; border-radius: 50%; display: inline-flex; align-items: center; justify-content: center; margin-right: 10px; color: white; text-decoration: none; }
        .viber { background: #7360f2; } .telegram { background: #0088cc; } .phone { background: #28a745; }
        
        .map-container { border-radius: 30px; overflow: hidden; height: 450px; box-shadow: 0 10px 30px rgba(0,0,0,0.1); }
        
        .newsletter-section {
            background: var(--navy);
            color: white;
            padding: 60px;
            border-radius: 40px;
            margin-top: 50px;
        }
        .btn-fancy-cart { background: var(--coral); color: white; border-radius: 30px; border: none; font-weight: 600; }
    </style>
</head>
<body>

<?php include 'includes/navbar.php'; ?>

<div class="mt-4"></div>

<section class="contact-header">
    <div class="container">
        <h1 class="display-4 fw-bold">Get In Touch</h1>
        <p class="lead opacity-75">ဟင်းချက်နည်းများ၊ ဝန်ဆောင်မှုများနှင့် ပတ်သက်၍ သိလိုသည်များကို မေးမြန်းနိုင်ပါသည်။</p>
    </div>
</section>

<div class="container my-5">
    <div class="row g-4 mb-5">
        <div class="col-md-4">
            <div class="contact-card">
                <div class="icon-box bg-light text-coral"><i class="bi bi-geo-alt-fill"></i></div>
                <h5 class="fw-bold">Our Location</h5>
                <p class="text-muted small">No. 123, Pyay Road, Kamayut Township, Yangon, Myanmar.</p>
            </div>
        </div>
        <div class="col-md-4">
            <div class="contact-card">
                <div class="icon-box bg-light text-primary"><i class="bi bi-chat-dots-fill"></i></div>
                <h5 class="fw-bold">Social Connect</h5>
                <div class="mt-3">
                    <a href="#" class="social-link viber"><i class="bi bi-messenger"></i></a>
                    <a href="#" class="social-link telegram"><i class="bi bi-telegram"></i></a>
                    <a href="#" class="social-link phone"><i class="bi bi-telephone-fill"></i></a>
                </div>
                <p class="text-muted small mt-3">Available 9:00 AM - 6:00 PM</p>
            </div>
        </div>
        <div class="col-md-4">
            <div class="contact-card">
                <div class="icon-box bg-light text-success"><i class="bi bi-envelope-check-fill"></i></div>
                <h5 class="fw-bold">Email Us</h5>
                <p class="text-muted small">support@foodfusion.com<br>info@foodfusion.com</p>
            </div>
        </div>
    </div>

    <div class="row g-5 align-items-stretch">
        <div class="col-lg-6">
            <div class="bg-white p-5 rounded-4 shadow-sm h-100">
                <h3 class="fw-bold mb-4">Send us a Message</h3>
                <form action="process_contact.php" method="POST">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Your Name</label>
                            <input type="text" name="name" class="form-control" placeholder="John Doe" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Email Address</label>
                            <input type="email" name="email" class="form-control" placeholder="name@example.com" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label small fw-bold">Subject</label>
                            <select name="subject" class="form-select form-control">
                                <option>General Inquiry</option>
                                <option>Recipe Feedback</option>
                                <option>Business Partnership</option>
                                <option>Technical Issue</option>
                            </select>
                        </div>
                        <div class="col-12">
                            <label class="form-label small fw-bold">Your Message</label>
                            <textarea name="message" class="form-control" rows="5" placeholder="How can we help you?" required></textarea>
                        </div>
                        <div class="col-12 text-end">
                            <button type="submit" class="btn btn-send">Send Message <i class="bi bi-send ms-2"></i></button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="map-container">
                <iframe
                    src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3819.463283626381!2d96.1284568!3d16.8281143!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x30c194be0376e73f%3A0x8677f48509c2e0f8!2sPyay%20Rd%2C%20Yangon!5e0!3m2!1sen!2smm!4v1700000000000"
                    width="100%" height="100%" style="border:0;" allowfullscreen="" loading="lazy">
                </iframe>
            </div>
        </div>
    </div>
</div>

<div class="container mb-5 pb-5">
    <div class="newsletter-section text-center shadow-lg">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <h2 class="display-5 fw-bold mb-4">Stay Inspired, Stay Delicious</h2>
                <p class="mb-5 text-white-50 fs-5">ဟင်းချက်နည်းအသစ်များနှင့် ကျန်းမာရေးဗဟုသုတများကို သင့် Inbox ဆီသို့ အရောက်ပို့ပေးပါမည်။</p>
                <form class="d-flex gap-3 p-2 bg-white rounded-pill shadow-sm mx-auto" style="max-width: 600px;">
                    <input type="email" class="form-control border-0 rounded-pill ps-4" placeholder="Your email address">
                    <button type="submit" class="btn btn-fancy-cart px-5">Join Now</button>
                </form>
            </div>
        </div>
    </div>
</div>

<?php 
if (isset($_SESSION['user_id'])) {
    include 'includes/logout_modal.php'; 
}
?>
<?php include 'includes/footer.php'; ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>