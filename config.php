<?php
// Session ကို တစ်ခါပဲ ပွင့်စေရန် စစ်ဆေးခြင်း
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$host = "localhost";
$user = "root";
$pass = "root"; // MAMP အတွက် root၊ XAMPP ဖြစ်လျှင် empty ထားပါ
$dbname = "foodfusion_db";

$conn = new mysqli($host, $user, $pass, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// --- Cookie ရှိမရှိစစ်ပြီး Auto Login လုပ်ပေးခြင်း (Updated for Security & Consistency) ---
if (!isset($_SESSION['user_id']) && isset($_COOKIE['user_login'])) {
    
    // Auth_logic တွင် encode လုပ်ခဲ့သော username ကို ပြန်လည် decode လုပ်သည်
    $cookie_user = base64_decode($_COOKIE['user_login']);
    
    // လိုအပ်သော username နှင့် image ပါ တစ်ခါတည်း ဆွဲထုတ်သည်
    $stmt = $conn->prepare("SELECT id, role, username, image FROM users WHERE username = ?");
    $stmt->bind_param("s", $cookie_user);
    $stmt->execute();
    $res = $stmt->get_result();
    
    if ($user_row = $res->fetch_assoc()) {
        // Session များကို ပြန်လည်တည်ဆောက်ခြင်း
        $_SESSION['user_id'] = $user_row['id'];
        $_SESSION['role'] = $user_row['role'];
        $_SESSION['username'] = $user_row['username'];
        $_SESSION['image'] = $user_row['image'];
        
        // စနစ်၏ လုံခြုံရေးအရ အသုံးပြုသူ၏ browser ထဲတွင် login တကယ်ရှိနေကြောင်း အတည်ပြုပြီးဖြစ်သည်
    } else {
        // အကယ်၍ user မရှိတော့လျှင် (သို့မဟုတ်) မှားယွင်းနေလျှင် cookie ကို ဖျက်ပစ်သည်
        setcookie("user_login", "", time() - 3600, "/");
    }
}

// ဤနေရာတွင် redirect logic များ မထည့်ထားပါ (Index ကို လွတ်လပ်စွာ ပေးဝင်ရန်အတွက်ဖြစ်သည်)
?>