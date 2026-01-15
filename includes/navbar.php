<nav class="navbar navbar-expand-lg navbar-light sticky-top shadow-sm py-2" style="background-color: #fffdfa; border-bottom: 2px solid #fce4ec;">
    <div class="container">
        <a class="navbar-brand fw-bold fs-3" href="index.php" style="letter-spacing: -1px;">
            <span style="color: #ff5733;">FOOD</span> <span style="color: #ffb347;">FUSION</span>
        </a>

        <button class="navbar-toggler border-0 shadow-none" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto align-items-center">
                <li class="nav-item mx-lg-1">
                    <a class="nav-link fw-semibold fancy-nav-link" href="index.php">Homepage</a>
                </li>
                <li class="nav-item mx-lg-1">
                    <a class="nav-link fw-semibold fancy-nav-link" href="recipe_collection.php">Recipe Collection</a>
                </li>
                <li class="nav-item mx-lg-1">
                    <a class="nav-link fw-semibold fancy-nav-link" href="communityCookbook.php">Community Cookbook</a>
                </li>
                <li class="nav-item mx-lg-1">
                    <a class="nav-link fw-semibold fancy-nav-link" href="contact_us.php">Contact Us</a>
                </li>
                <li class="nav-item mx-lg-1">
                    <a class="nav-link fw-semibold fancy-nav-link" href="culinary_resources.php">Culinary resources</a>
                </li>
                <li class="nav-item mx-lg-1">
                    <a class="nav-link fw-semibold fancy-nav-link" href="educational_resources.php">Educational Resources</a>
                </li>

                <?php if (!isset($_SESSION['user_id'])): ?>
                <li class="nav-item mx-lg-1">
                    <a class="nav-link fw-semibold join-us-link" href="#" data-bs-toggle="modal" data-bs-target="#loginModal">Join-Us</a>
                </li>
                <?php endif; ?>

                <?php if (isset($_SESSION['user_id'])): ?>
                <li class="nav-item dropdown ms-lg-4 mt-lg-0 mt-3">
                    <a class="nav-link dropdown-toggle d-flex align-items-center p-0" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <div class="fancy-profile-ring shadow-sm">
                            <img src="<?php echo !empty($current_user_img) ? 'uploads/'.$current_user_img : 'https://cdn-icons-png.flaticon.com/512/149/149071.png'; ?>"
                                 alt="Profile" width="45" height="45" class="rounded-circle object-fit-cover">
                        </div>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end shadow-lg border-0 mt-3 p-2 animate slideIn" style="border-radius: 20px; background-color: #ffffff; min-width: 220px;">
                        <li><h6 class="dropdown-header text-muted small">Account Profile</h6></li>
                        <li><span class="dropdown-item-text fw-bold fs-5" style="color: #ff5733;"><?php echo htmlspecialchars($current_username); ?></span></li>
                        <li><hr class="dropdown-divider"></li>

                        <?php if($current_role === 'admin'): ?>
                            <li>
                                <a class="dropdown-item py-2 rounded-pill mb-1 fw-bold admin-dropdown-link" href="dashboard.php">
                                    <i class="bi bi-speedometer2 me-2"></i> Admin Panel
                                </a>
                            </li>
                        <?php else: ?>
                            <li>
                                <a class="dropdown-item py-2 rounded-pill mb-1 user-dropdown-link" href="user_setting.php">
                                    <i class="bi bi-person-gear me-2"></i> User Settings
                                </a>
                            </li>
                        <?php endif; ?>

                        <li>
                            <button class="dropdown-item py-2 rounded-pill text-danger fw-bold hover-danger" data-bs-toggle="modal" data-bs-target="#logoutModal">
                                <i class="bi bi-box-arrow-right me-2"></i> Logout
                            </button>
                        </li>
                    </ul>
                </li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>

<div class="modal fade" id="loginModal" tabindex="-1" aria-labelledby="loginModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 40px; background: rgba(255, 255, 255, 0.95); backdrop-filter: blur(15px);">
            <div class="modal-body p-5">
                <div class="text-end">
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="text-center mb-4">
                    <h1 class="brand-logo" style="font-size: 2.2rem; font-weight: 800; color: #1a1a2e;"><i class="bi bi-fire" style="color: #ff5733;"></i> Food<span style="color: #ff5733;">Fusion</span></h1>
                    <p class="text-muted small fw-bold">EXPERIENCE INTERNATIONAL FLAVORS</p>
                </div>
                <ul class="nav nav-pills nav-fill mb-4" id="pills-tab" style="background: rgba(0,0,0,0.05); padding: 5px; border-radius: 30px;">
                    <li class="nav-item">
                        <button class="nav-link active rounded-pill fw-bold" data-bs-toggle="pill" data-bs-target="#modal-login-tab">Login</button>
                    </li>
                    <li class="nav-item">
                        <button class="nav-link rounded-pill fw-bold" data-bs-toggle="pill" data-bs-target="#modal-reg-tab">Register</button>
                    </li>
                </ul>
                <div class="tab-content">
                    <div class="tab-pane fade show active" id="modal-login-tab">
                        <form action="auth_logic.php" method="POST">
                            <div class="form-floating mb-3">
                                <input type="text" name="username" class="form-control rounded-4 border-0 shadow-sm" placeholder="Username" required>
                                <label><i class="bi bi-person me-2"></i>Username</label>
                            </div>
                            <div class="form-floating mb-3">
                                <input type="password" name="password" class="form-control rounded-4 border-0 shadow-sm" placeholder="Password" required>
                                <label><i class="bi bi-shield-lock me-2"></i>Password</label>
                            </div>
                            <button type="submit" name="login" class="btn w-100 shadow rounded-pill py-3 fw-bold text-white text-uppercase" style="background: linear-gradient(45deg, #ff5733, #ffb347); border: none;">Enter Kitchen</button>
                        </form>
                    </div>
                    <div class="tab-pane fade" id="modal-reg-tab">
                        <form action="auth_logic.php" method="POST">
                            <div class="form-floating mb-3">
                                <input type="text" name="username" class="form-control rounded-4 border-0 shadow-sm" placeholder="Full Name" required>
                                <label><i class="bi bi-person me-2"></i>Full Name</label>
                            </div>
                            <div class="form-floating mb-3">
                                <input type="email" name="email" class="form-control rounded-4 border-0 shadow-sm" placeholder="Email" required>
                                <label><i class="bi bi-envelope me-2"></i>Email</label>
                            </div>
                            <div class="form-floating mb-4">
                                <input type="password" name="password" class="form-control rounded-4 border-0 shadow-sm" placeholder="Password" required>
                                <label><i class="bi bi-key me-2"></i>Create Password</label>
                            </div>
                            <button type="submit" name="register" class="btn w-100 shadow rounded-pill py-3 fw-bold text-white text-uppercase" style="background: linear-gradient(45deg, #ff5733, #ffb347); border: none;">Join Community</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    :root { --coral: #ff5733; --honey: #ffb347; }
    
    .fancy-nav-link { color: #4a4a4a !important; transition: all 0.3s ease; padding: 8px 15px !important; font-size: 0.95rem; }
    .fancy-nav-link:hover { color: var(--coral) !important; background-color: #fff5f2; border-radius: 10px; transform: translateY(-1px); }

    .join-us-link { background: linear-gradient(45deg, var(--coral), var(--honey)); color: white !important; border-radius: 30px; border: none; transition: all 0.3s ease; font-size: 0.95rem; padding: 8px 15px !important; }
    .join-us-link:hover { opacity: 0.9; transform: translateY(-1px); color: white !important; }

    .fancy-profile-ring { padding: 3px; background: linear-gradient(45deg, var(--coral), var(--honey)); border-radius: 50%; display: flex; transition: all 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275); }
    .fancy-profile-ring:hover { transform: scale(1.1) rotate(5deg); box-shadow: 0 5px 15px rgba(255, 87, 51, 0.3); }

    #loginModal .nav-pills .nav-link { color: #1a1a2e; }
    #loginModal .nav-pills .nav-link.active { background-color: var(--coral) !important; color: white !important; }
    
    .admin-dropdown-link { color: var(--coral) !important; background-color: #fff5f2; }
    .admin-dropdown-link:hover { background-color: var(--coral) !important; color: white !important; }
    .user-dropdown-link:hover { background-color: #fff9f0 !important; color: var(--honey) !important; }
    .hover-danger:hover { background-color: #fff5f5 !important; }

    @keyframes slideIn { from { opacity: 0; transform: translateY(12px); } to { opacity: 1; transform: translateY(0); } }
    .slideIn { animation: slideIn 0.3s ease-out forwards; }
    .dropdown-toggle::after { display: none; }
</style>