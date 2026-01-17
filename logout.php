<?php 
// ၁။ Session စတင်ခြင်း
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// ၂။ Remember Me Cookie ရှိခဲ့လျှင် ဖျက်ထုတ်ခြင်း
// အရေးကြီးသည်- setcookie ကို session_destroy မတိုင်မီ လုပ်ဆောင်ခြင်းက ပိုမိုစိတ်ချရပါသည်
if (isset($_COOKIE['user_login'])) {
    // Cookie ကို expire ဖြစ်အောင် အချိန်ကို အတိတ်သို့ ပေးပို့ပြီး ဖျက်သည်
    setcookie("user_login", "", time() - 3600, "/");
    
    // Global variable ထဲမှပါ ဖျက်ထုတ်ခြင်း (လက်ရှိ script run နေစဉ်အတွက်)
    unset($_COOKIE['user_login']);
}

// ၃။ Session variable အားလုံးကို ရှင်းထုတ်ပြီး Session ကို အပြီးတိုင် ဖျက်ဆီးခြင်း
session_unset(); 
if (session_id() != "" || isset($_COOKIE[session_name()])) {
    setcookie(session_name(), '', time() - 2592000, '/');
}
session_destroy();

// ၄။ Logout လုပ်ပြီးပါက Index (Homepage) သို့ ပြန်ညွှန်ခြင်း
header("Location: index.php");
exit();
?>