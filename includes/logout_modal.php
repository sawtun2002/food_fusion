<div class="modal fade" id="loginModal" tabindex="-1" aria-labelledby="loginModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-primary text-white border-0">
                <h5 class="modal-title" id="loginModalLabel">Login to Food Fusion</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body py-4">
                <?php if (isset($_SESSION['msg'])): ?>
                    <div class="alert alert-<?php echo $_SESSION['msg_type']; ?> alert-dismissible fade show" role="alert">
                        <?php 
                            echo $_SESSION['msg']; 
                            unset($_SESSION['msg']); 
                        ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>

                <form action="auth_logic.php" method="POST">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Username</label>
                        <input type="text" name="username" class="form-control" placeholder="Enter username" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Password</label>
                        <input type="password" name="password" class="form-control" placeholder="Enter password" required>
                    </div>
                    <button type="submit" name="login" class="btn btn-primary w-100 fw-bold">Login</button>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="logoutModal" tabindex="-1" aria-labelledby="logoutModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-danger text-white border-0">
                <h5 class="modal-title" id="logoutModalLabel">Confirm Logout</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center py-4">
                <h5 class="fw-bold">Are you sure to logout?</h5>
                <p class="text-muted mb-0">Do you want to logout</p>
            </div>
            <div class="modal-footer border-0 bg-light justify-content-center">
                <button type="button" class="btn btn-secondary px-4" data-bs-dismiss="modal">No</button>
                <a href="logout.php?action=logout" class="btn btn-danger px-4">Yes</a>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    <?php if (isset($_SESSION['show_login_modal']) && $_SESSION['show_login_modal']): ?>
        var loginModalElement = document.getElementById('loginModal');
        if (loginModalElement) {
            var myModal = new bootstrap.Modal(loginModalElement);
            myModal.show();
        }
        <?php unset($_SESSION['show_login_modal']); ?>
    <?php endif; ?>
});
</script>