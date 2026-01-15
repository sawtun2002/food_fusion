// forgot_password.php ရဲ့ အဓိက logic
if (isset($_POST['reset_request'])) {
    $email = $_POST['email'];
    $check = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $check->bind_param("s", $email);
    $check->execute();
    
    if ($check->get_result()->num_rows > 0) {
        $token = bin2hex(random_bytes(32)); // လုံခြုံတဲ့ token ထုတ်ခြင်း
        $conn->query("UPDATE users SET reset_token='$token' WHERE email='$email'");
        
        $reset_link = "http://yourdomain.com/reset_password.php?token=" . $token;
        
        // Email ပို့ခြင်း (Localhost မှာ အလုပ်လုပ်ရန် PHPMailer သုံးဖို့လိုပါမည်)
        $subject = "Password Reset Request";
        $message = "Please click this link to reset your password: " . $reset_link;
        mail($email, $subject, $message); 
        
        echo "Reset link ကို သင့် Email ဆီ ပို့ပေးလိုက်ပါပြီ။";
    }
}