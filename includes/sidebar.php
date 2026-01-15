<div id="sidebar" class="shadow">
    <div class="p-4 text-center">
        <h4 class="text-warning fw-bold mb-0">FOOD FUSION</h4>
        <small class="text-muted">Admin Panel</small>
    </div>
    <hr class="text-secondary mx-3">
    <div class="nav flex-column">
        <a onclick="showSection('food-mgmt')" id="link-food" class="nav-link active p-3">
            <i class="bi bi-egg-fried me-2"></i> Food Menu
        </a>
        <a onclick="showSection('user-mgmt')" id="link-user" class="nav-link p-3">
            <i class="bi bi-people me-2"></i> Users Management
        </a>
        <hr class="text-secondary mx-3">
        <a href="index.php" class="nav-link p-3 text-info">
            <i class="bi bi-house-door me-2"></i> View Website
        </a>
        <button class="nav-link p-3 text-danger border-0  text-start w-100" data-bs-toggle="modal" data-bs-target="#logoutModal">
            <i class="bi bi-box-arrow-right me-2"></i> Logout
        </button>
    </div>
</div>