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

<?php include 'includes/login_modal.php'  ?>

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