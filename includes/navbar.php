<nav class="navbar navbar-expand-lg navbar-light sticky-top shadow-sm py-2" style="background-color: #fffdfa; border-bottom: 2px solid #fce4ec;">
    <div class="container-fluid px-lg-5"> 
        <a class="navbar-brand d-flex align-items-center fw-bold fs-3" href="index.php" style="letter-spacing: -0.5px; min-width: 220px;">
            <div class="fusion-logo-badge me-3">
                <div class="fusion-ring"></div>
                <div class="fusion-icons">
                    <i class="bi bi-fire"></i>
                </div>
            </div>
            <div class="brand-name">
                <span style="color: #ff5733; font-weight: 800;">Food</span><span style="color: #ffb347; font-weight: 400;">Fusion</span>
            </div>
        </a>

        <button class="navbar-toggler border-0 shadow-none" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav mx-auto align-items-center text-nowrap">
                <li class="nav-item mx-lg-1">
                    <a class="nav-link fw-semibold fancy-nav-link" href="index.php">Homepage</a>
                </li>
                <li class="nav-item mx-lg-1">
                    <a class="nav-link fw-semibold fancy-nav-link" href="about_us.php">About us</a>
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
            </ul>

            <div class="d-flex align-items-center justify-content-lg-end" style="min-width: 200px;">
                <?php if (!isset($_SESSION['user_id'])): ?>
                    <a class="nav-link fw-semibold join-us-link" href="#" data-bs-toggle="modal" data-bs-target="#loginModal">Join-Us</a>
                <?php endif; ?>

                <?php if (isset($_SESSION['user_id'])): ?>
                <div class="nav-item dropdown mt-lg-0 mt-3">
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
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</nav>

<style>
    :root { --coral: #ff5733; --honey: #ffb347; }

    /* Fusion Logo Badge Styles */
    .fusion-logo-badge {
        position: relative;
        width: 40px;
        height: 40px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .fusion-ring {
        position: absolute;
        width: 100%;
        height: 100%;
        border: 3px solid var(--honey);
        border-top-color: var(--coral);
        border-radius: 50%;
        animation: spin-slow 4s linear infinite;
    }

    .fusion-icons {
        color: var(--coral);
        font-size: 1.5rem;
        z-index: 2;
    }

    @keyframes spin-slow {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }

    .brand-name { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }

    /* Rest of the styling remains consistent with your original UI */
    .fancy-nav-link { color: #4a4a4a !important; transition: all 0.3s ease; padding: 8px 12px !important; font-size: 0.88rem; white-space: nowrap; }
    .fancy-nav-link:hover { color: var(--coral) !important; background-color: #fff5f2; border-radius: 10px; transform: translateY(-1px); }
    .join-us-link { background: linear-gradient(45deg, var(--coral), var(--honey)); color: white !important; border-radius: 30px; border: none; transition: all 0.3s ease; font-size: 0.88rem; padding: 8px 22px !important; white-space: nowrap; display: inline-block; }
    .join-us-link:hover { opacity: 0.9; transform: translateY(-1px); color: white !important; box-shadow: 0 4px 12px rgba(255, 87, 51, 0.2); }
    .fancy-profile-ring { padding: 3px; background: linear-gradient(45deg, var(--coral), var(--honey)); border-radius: 50%; display: flex; transition: all 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275); }
    .fancy-profile-ring:hover { transform: scale(1.1) rotate(5deg); box-shadow: 0 5px 15px rgba(255, 87, 51, 0.3); }
    
    @keyframes slideIn { from { opacity: 0; transform: translateY(12px); } to { opacity: 1; transform: translateY(0); } }
    .slideIn { animation: slideIn 0.3s ease-out forwards; }
    .dropdown-toggle::after { display: none; }
</style>