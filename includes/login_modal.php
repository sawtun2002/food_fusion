<div class="modal fade" id="loginModal" tabindex="-1" aria-labelledby="loginModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg auth-modal-custom">
            <div class="modal-body p-5">
                <div class="text-end mb-2">
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

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
                    
                    $current_page = basename($_SERVER['PHP_SELF']);
                    $query_string = !empty($_SERVER['QUERY_STRING']) ? '?' . $_SERVER['QUERY_STRING'] : '';
                    $redirect_url = $current_page . $query_string;
                ?>

                <ul class="nav nav-pills nav-fill mb-4" id="pills-tab-modal">
                    <li class="nav-item">
                        <button class="nav-link active rounded-pill fw-bold" data-bs-toggle="pill" data-bs-target="#modal-login-tab">
                            <i class="bi bi-person-check me-2"></i>Login
                        </button>
                    </li>
                    <li class="nav-item">
                        <button class="nav-link rounded-pill fw-bold" data-bs-toggle="pill" data-bs-target="#modal-reg-tab">
                            <i class="bi bi-person-plus me-2"></i>Register
                        </button>
                    </li>
                </ul>

                <div class="tab-content">
                    <div class="tab-pane fade show active" id="modal-login-tab">
                        <form action="auth_logic.php" method="POST" id="modalLoginForm">
                            <input type="hidden" name="redirect_to" value="<?php echo $redirect_url; ?>">
                            
                            <div class="form-floating mb-3">
                                <input type="text" name="username" class="form-control rounded-4 border-0 shadow-sm custom-input" id="modalLogUser" placeholder="Username or Email" required>
                                <label for="modalLogUser"><i class="bi bi-person me-2"></i>Username or Email</label>
                            </div>

                            <div class="form-floating mb-3">
                                <input type="password" name="password" class="form-control rounded-4 border-0 shadow-sm custom-input" id="modalLogPass" placeholder="Password" required>
                                <label for="modalLogPass"><i class="bi bi-shield-lock me-2"></i>Password</label>
                            </div>
                            
                            <div class="form-check mb-4 ms-2">
                                <input class="form-check-input" type="checkbox" name="remember" id="modalRem">
                                <label class="form-check-label text-muted small" for="modalRem">Remember me</label>
                            </div>

                            <button type="submit" name="login" id="modalLoginBtn" class="btn btn-auth-gradient w-100 shadow rounded-pill py-3 fw-bold text-white text-uppercase" <?php echo $is_locked ? 'disabled' : ''; ?>>
                                <span id="modalBtnText">Enter Kitchen <i class="bi bi-arrow-right-short ms-1"></i></span>
                            </button>
                        </form>
                    </div>

                    <div class="tab-pane fade" id="modal-reg-tab">
                        <form action="auth_logic.php" method="POST" id="registrationForm">
                            <input type="hidden" name="redirect_to" value="<?php echo $redirect_url; ?>">
                            
                            <div class="row g-2 mb-3">
                                <div class="col-md-6">
                                    <div class="form-floating">
                                        <input type="text" name="firstname" class="form-control rounded-4 border-0 shadow-sm custom-input" id="modalRegFName" placeholder="First Name" required>
                                        <label for="modalRegFName"><i class="bi bi-person me-2"></i>First Name</label>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-floating">
                                        <input type="text" name="lastname" class="form-control rounded-4 border-0 shadow-sm custom-input" id="modalRegLName" placeholder="Last Name" required>
                                        <label for="modalRegLName">Last Name</label>
                                    </div>
                                </div>
                            </div>

                            <div class="form-floating mb-3">
                                <input type="email" name="email" class="form-control rounded-4 border-0 shadow-sm custom-input" id="modalRegEmail" placeholder="Email" required>
                                <label for="modalRegEmail"><i class="bi bi-envelope me-2"></i>Email Address</label>
                            </div>

                            <div class="form-floating mb-3">
                                <input type="password" name="password" 
                                       class="form-control rounded-4 border-0 shadow-sm custom-input" 
                                       id="modalRegPass" placeholder="Password" 
                                       pattern="^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/"
                                       title="Must be at least 8 characters, with uppercase, lowercase, numbers, and symbols."
                                       required>
                                <label for="modalRegPass"><i class="bi bi-key me-2"></i>Create Password</label>
                            </div>

                            <div class="form-floating mb-1">
                                <input type="password" name="confirm_password" 
                                       class="form-control rounded-4 border-0 shadow-sm custom-input" 
                                       id="modalRegConfirmPass" placeholder="Confirm Password" required>
                                <label for="modalRegConfirmPass"><i class="bi bi-check2-circle me-2"></i>Confirm Password</label>
                            </div>
                            
                            <div class="px-2 mb-3">
                                <p id="passwordMatchText" style="font-size: 0.65rem; transition: 0.3s;"></p>
                            </div>

                            <div class="text-center mb-3">
                                <p class="text-muted mb-0" style="font-size: 0.7rem; line-height: 1.4;">
                                    By clicking Join Community, you agree to our 
                                    <a href="#" class="text-coral fw-bold text-decoration-none">Terms</a> and 
                                    <a href="#" class="text-coral fw-bold text-decoration-none">Privacy Policy</a>.
                                </p>
                            </div>

                            <button type="submit" name="register" id="regSubmitBtn" class="btn btn-auth-gradient w-100 shadow rounded-pill py-3 fw-bold text-white text-uppercase">
                                Join Community <i class="bi bi-plus-circle ms-1"></i>
                            </button>
                        </form>
                    </div>
                </div>

                <div class="text-center mt-4">
                    <p class="text-muted" style="font-size: 0.75rem;">
                        © 2026 FoodFusion. All Rights Reserved.
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .auth-modal-custom { background: rgba(255, 255, 255, 0.95) !important; backdrop-filter: blur(15px); border-radius: 40px !important; }
    .brand-logo { font-size: 2.2rem; font-weight: 800; color: #1a1a2e; letter-spacing: -1px; }
    .brand-logo span, .text-coral { color: #ff5733; }
    #pills-tab-modal { background: rgba(0,0,0,0.05); padding: 5px; border-radius: 30px; }
    #pills-tab-modal .nav-link { color: #1a1a2e; transition: all 0.4s ease; }
    #pills-tab-modal .nav-link.active { background-color: #ff5733 !important; color: white !important; box-shadow: 0 8px 15px rgba(255, 87, 51, 0.3); }
    .custom-input { background: white !important; transition: all 0.3s ease; }
    .custom-input:focus { transform: scale(1.01); box-shadow: 0 5px 15px rgba(0,0,0,0.05) !important; }
    .btn-auth-gradient { background: linear-gradient(45deg, #ff5733, #ffb347) !important; border: none !important; transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275) !important; }
    .btn-auth-gradient:hover:not(:disabled) { transform: translateY(-5px); box-shadow: 0 12px 20px rgba(255, 87, 51, 0.4) !important; }
    .btn-auth-gradient:disabled { background: #ccc !important; cursor: not-allowed; opacity: 0.7; }
    .alert { border-radius: 20px; border: none; }
</style>

<script>
    (function() {
        // Updated Timer Logic for Minutes and Seconds
        const lockTime = <?php echo $lock_duration; ?>;
        if (lockTime > 0) {
            let remaining = lockTime;
            const btn = document.getElementById('modalLoginBtn');
            const btnText = document.getElementById('modalBtnText');
            
            const timer = setInterval(() => {
                remaining--;
                
                // Minutes and Seconds calculation
                const mins = Math.floor(remaining / 60);
                const secs = remaining % 60;
                
                // Formatting time text
                let timeDisplay = (mins > 0) ? `${mins}m ${secs}s` : `${secs}s`;
                
                if (btnText) btnText.innerText = `Please wait ${timeDisplay}...`;
                
                if (remaining <= 0) {
                    clearInterval(timer);
                    if (btn) btn.disabled = false;
                    if (btnText) btnText.innerHTML = 'Enter Kitchen <i class="bi bi-arrow-right-short ms-1"></i>';
                }
            }, 1000);
        }

        const password = document.getElementById("modalRegPass");
        const confirmPassword = document.getElementById("modalRegConfirmPass");
        const matchText = document.getElementById("passwordMatchText");
        const submitBtn = document.getElementById("regSubmitBtn");

        function validatePassword() {
            if (password.value === "" || confirmPassword.value === "") {
                matchText.innerText = "";
                submitBtn.disabled = false;
                return;
            }
            if (password.value !== confirmPassword.value) {
                matchText.innerText = " Passwords do not match";
                matchText.style.color = "#dc3545";
                submitBtn.disabled = true;
            } else {
                matchText.innerText = "Passwords match";
                matchText.style.color = "#198754";
                submitBtn.disabled = false;
            }
        }

        password.onchange = validatePassword;
        confirmPassword.onkeyup = validatePassword;
    })();
</script>