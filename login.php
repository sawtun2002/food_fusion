<?php include 'config.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Food Fusion - Login & Register</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    
    <style>
        :root { 
            --coral: #ff5733; 
            --honey: #ffb347; 
            --navy: #1a1a2e; 
            --cream: #fffdfa; 
            --glass: rgba(255, 255, 255, 0.9); 
        }

        body { 
            background: linear-gradient(rgba(26, 26, 46, 0.8), rgba(26, 26, 46, 0.8)), 
                        url('https://img.freepik.com/free-photo/top-view-circular-food-frame_23-2148723455.jpg');
            background-size: cover;
            background-attachment: fixed;
            font-family: 'Poppins', sans-serif;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--navy);
        }

        .auth-card {
            background: var(--glass);
            backdrop-filter: blur(15px);
            border-radius: 40px;
            border: 1px solid rgba(255,255,255,0.3);
            box-shadow: 0 25px 50px rgba(0,0,0,0.3);
            width: 100%;
            max-width: 480px;
            padding: 50px 40px;
            transition: transform 0.3s ease;
        }

        .brand-logo {
            font-size: 2.2rem;
            font-weight: 800;
            color: var(--navy);
            letter-spacing: -1px;
        }

        .brand-logo span { color: var(--coral); }

        .nav-pills {
            background: rgba(0,0,0,0.05);
            padding: 5px;
            border-radius: 30px;
            margin-bottom: 35px;
        }
        .nav-pills .nav-link {
            border-radius: 25px;
            color: var(--navy);
            font-weight: 600;
            transition: all 0.4s ease;
        }
        .nav-pills .nav-link.active {
            background-color: var(--coral);
            box-shadow: 0 8px 15px rgba(255, 87, 51, 0.3);
        }

        .form-floating > .form-control {
            border-radius: 15px;
            border: 2px solid transparent;
            background: white;
            transition: all 0.3s ease;
        }
        .form-floating > .form-control:focus {
            border-color: var(--honey);
            box-shadow: none;
            transform: scale(1.02);
        }

        .btn-auth {
            background: linear-gradient(45deg, var(--coral), var(--honey));
            border: none;
            border-radius: 30px;
            padding: 14px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: white;
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        }
        .btn-auth:hover {
            transform: translateY(-5px);
            box-shadow: 0 12px 20px rgba(255, 87, 51, 0.4);
            color: white;
        }
        .btn-auth:disabled {
            background: #ccc;
            transform: none;
            box-shadow: none;
            cursor: not-allowed;
        }

        .alert {
            border-radius: 20px;
            font-weight: 500;
            border: none;
        }
    </style>
</head>
<body>

<div class="container d-flex justify-content-center">
    <div class="auth-card" data-aos="zoom-in" data-aos-duration="1000">
        
        <div class="text-center mb-4">
            <h1 class="brand-logo"><i class="bi bi-fire text-coral"></i> Food<span>Fusion</span></h1>
            <p class="text-muted small fw-bold">EXPERIENCE INTERNATIONAL FLAVORS</p>
        </div>

        <?php if(isset($_SESSION['msg'])): ?>
            <div class="alert alert-<?php echo $_SESSION['msg_type']; ?> alert-dismissible fade show shadow-sm mb-4" role="alert">
                <i class="bi bi-info-circle-fill me-2"></i> <?php echo $_SESSION['msg']; unset($_SESSION['msg']); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <?php 
            $is_locked = (isset($_SESSION['login_lock']) && time() < $_SESSION['login_lock']);
            $lock_duration = $is_locked ? ($_SESSION['login_lock'] - time()) : 0;
        ?>

        <ul class="nav nav-pills nav-fill" id="pills-tab">
            <li class="nav-item">
                <button class="nav-link active" data-bs-toggle="pill" data-bs-target="#login-tab">
                    <i class="bi bi-person-check me-2"></i>Login
                </button>
            </li>
            <li class="nav-item">
                <button class="nav-link" data-bs-toggle="pill" data-bs-target="#reg-tab">
                    <i class="bi bi-person-plus me-2"></i>Register
                </button>
            </li>
        </ul>

        <div class="tab-content">
            <div class="tab-pane fade show active" id="login-tab">
                <form action="auth_logic.php" method="POST" id="loginForm">
                    <div class="form-floating mb-3">
                        <input type="text" name="username" class="form-control shadow-sm" id="logUser" placeholder="Username" required>
                        <label for="logUser"><i class="bi bi-person me-2"></i>Username</label>
                    </div>
                    <div class="form-floating mb-3">
                        <input type="password" name="password" class="form-control shadow-sm" id="logPass" placeholder="Password" required>
                        <label for="logPass"><i class="bi bi-shield-lock me-2"></i>Password</label>
                    </div>
                    <div class="form-check mb-4 ms-2">
                        <input class="form-check-input" type="checkbox" name="remember" id="rem">
                        <label class="form-check-label text-muted small" for="rem">Remember my session</label>
                    </div>
                    <button type="submit" name="login" id="loginBtn" class="btn btn-auth w-100 shadow" <?php echo $is_locked ? 'disabled' : ''; ?>>
                        <span id="btnText">Enter Kitchen <i class="bi bi-arrow-right-short ms-1"></i></span>
                    </button>
                </form>
            </div>

            <div class="tab-pane fade" id="reg-tab">
                <form action="auth_logic.php" method="POST">
                    <div class="form-floating mb-3">
                        <input type="text" name="username" class="form-control shadow-sm" id="regUser" placeholder="Username" required>
                        <label for="regUser"><i class="bi bi-person me-2"></i>Full Name</label>
                    </div>
                    <div class="form-floating mb-3">
                        <input type="email" name="email" class="form-control shadow-sm" id="regEmail" placeholder="Email" required>
                        <label for="regEmail"><i class="bi bi-envelope me-2"></i>Email Address</label>
                    </div>
                    <div class="form-floating mb-4">
                        <input type="password" name="password" class="form-control shadow-sm" id="regPass" placeholder="Password" required>
                        <label for="regPass"><i class="bi bi-key me-2"></i>Create Password</label>
                    </div>
                    <button type="submit" name="register" class="btn btn-auth w-100 shadow">
                        Join Community <i class="bi bi-plus-circle ms-1"></i>
                    </button>
                </form>
            </div>
        </div>

        <div class="text-center mt-5">
            <p class="text-muted extra-small" style="font-size: 0.75rem;">
                © 2026 FoodFusion Culinary Group. <br>
                All flavors protected.
            </p>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
<script>
    AOS.init();

    // Lock Timer Logic
    const lockTime = <?php echo $lock_duration; ?>;
    if (lockTime > 0) {
        let remaining = lockTime;
        const btn = document.getElementById('loginBtn');
        const btnText = document.getElementById('btnText');
        
        const timer = setInterval(() => {
            remaining--;
            btnText.innerText = `Please wait ${remaining}s...`;
            if (remaining <= 0) {
                clearInterval(timer);
                btn.disabled = false;
                btnText.innerHTML = 'Enter Kitchen <i class="bi bi-arrow-right-short ms-1"></i>';
            }
        }, 1000);
    }
</script>
</body>
</html>