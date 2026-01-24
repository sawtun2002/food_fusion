<?php 
if (session_status() === PHP_SESSION_NONE) { session_start(); }
include 'config.php';

// Admin Check
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

$msg = "";

// delete Logic
if (isset($_GET['delete_user'])) {
    $u_id = intval($_GET['delete_user']);
    if($u_id != $_SESSION['user_id']) {
        $stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
        $stmt->bind_param("i", $u_id);
        if($stmt->execute()) {
            $msg = "<div class='alert alert-warning border-0 shadow-sm rounded-4'>User ကို ဖျက်သိမ်းပြီးပါပြီ။</div>";
        }
    }
}

// User Ban/Unban Logic
if (isset($_GET['toggle_ban'])) {
    $u_id = intval($_GET['toggle_ban']);
    $current_status = $_GET['current_status'];
    $new_status = ($current_status == 'active') ? 'banned' : 'active';

    if($u_id != $_SESSION['user_id']) {
        $stmt = $conn->prepare("UPDATE users SET status = ? WHERE id = ?");
        $stmt->bind_param("si", $new_status, $u_id);
        if($stmt->execute()) {
            $msg = "<div class='alert alert-info border-0 shadow-sm rounded-4 text-dark'>User status has been updated to ".ucfirst($new_status)."!</div>";
        }
    }
}

// Mark as Read Logic
if (isset($_GET['mark_read'])) {
    $m_id = intval($_GET['mark_read']);
    $conn->query("UPDATE contact_messages SET status = 'read' WHERE id = $m_id");
}

// Stats fetching
$user_count = $conn->query("SELECT id FROM users")->num_rows;
$msg_count = $conn->query("SELECT id FROM contact_messages WHERE status = 'unread'")->num_rows;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel - FoodFusion</title>
    <link rel="icon" type="image/svg+xml" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16' fill='%23ff5733'><path d='M8 16c3.314 0 6-2 6-5.5 0-1.5-.5-4-2.5-6 .25 1.5-1.25 2-1.25 2C11 4 9 .5 6 0c.357 2 .5 4-2 6-1.25 1-2 2.729-2 4.5C2 14 4.686 16 8 16Zm0-1c-1.657 0-3-1-3-2.75 0-.75.25-2 1.25-3C6.125 10 7 10.5 7 10.5c-.375-1.25.5-3.25 2-3.5-.179 1-.25 2 1 3 .625.5 1 1.364 1 2.25C11 14 9.657 15 8 15Z'/></svg>">
 
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <style>
        :root { --accent: #6366f1; --navy: #0f172a; --bg: #f8fafc; --danger: #ef4444; --success: #22c55e; }
        body { font-family: 'Inter', sans-serif; background: var(--bg); color: #334155; }
        
        #sidebar { width: 280px; height: 100vh; position: fixed; background: var(--navy); color: white; padding: 20px; transition: 0.3s; }
        .nav-link { color: #94a3b8; padding: 12px 20px; border-radius: 12px; margin-bottom: 5px; transition: 0.2s; cursor: pointer; }
        .nav-link:hover, .nav-link.active { background: rgba(255,255,255,0.1); color: white; }
        .nav-link.active { background: var(--accent); }

        #main-content { margin-left: 280px; padding: 40px; transition: 0.3s; }
        .glass-card { background: white; border-radius: 24px; padding: 30px; border: none; box-shadow: 0 4px 15px rgba(0,0,0,0.05); }
        
        .stat-card { background: white; border-radius: 20px; padding: 20px; border-left: 5px solid var(--accent); transition: 0.3s; }
        .stat-card:hover { transform: translateY(-5px); }

        /* Badge Styling - Updated for better visibility */
        .badge-active { background: #dcfce7; color: #166534; padding: 6px 14px; border-radius: 20px; font-size: 0.75rem; font-weight: 600; text-transform: uppercase; }
        .badge-banned { background: #fee2e2; color: #991b1b; padding: 6px 14px; border-radius: 20px; font-size: 0.75rem; font-weight: 600; text-transform: uppercase; }
        
        @media (max-width: 992px) {
            #sidebar { margin-left: -280px; }
            #main-content { margin-left: 0; }
            #sidebar.active { margin-left: 0; }
        }
    </style>
</head>
<body>

<div id="sidebar">
    <div class="mb-5 px-3 d-flex justify-content-between">
        <h4 class="fw-bold"><i class="bi bi-shield-lock-fill me-2 text-primary"></i>Admin Panel</h4>
    </div>
    <div class="nav flex-column">
        <div class="nav-link active" onclick="showSection('user-sec', this)"><i class="bi bi-people me-3"></i> User Management</div>
        <div class="nav-link" onclick="showSection('msg-sec', this)"><i class="bi bi-chat-left-dots me-3"></i> User Feedback</div>
        <hr class="text-secondary my-4">
        <a href="index.php" class="nav-link text-decoration-none"><i class="bi bi-house me-3"></i> Home Website</a>
        <a href="logout.php" class="nav-link text-danger text-decoration-none"><i class="bi bi-power me-3"></i> Logout</a>
    </div>
</div>

<div id="main-content">
    <div class="d-flex justify-content-between align-items-center mb-5">
        <h3 class="fw-bold" id="title-text">User Management</h3>
        <div class="d-flex gap-3">
            <div class="stat-card shadow-sm text-center">
                <small class="text-muted d-block fw-bold text-uppercase">Total Users</small>
                <span class="fw-bold fs-4"><?php echo $user_count; ?></span>
            </div>
            <div class="stat-card shadow-sm border-danger text-center">
                <small class="text-muted d-block fw-bold text-uppercase">Unread Feedback</small>
                <span class="fw-bold fs-4 text-danger"><?php echo $msg_count; ?></span>
            </div>
        </div>
    </div>

    <?php echo $msg; ?>

    <div id="user-sec" class="management-section active">
        <div class="glass-card">
            <h5 class="fw-bold mb-4">Registered Users List</h5>
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>User</th>
                            <th>Email</th>
                            <th>Status</th>
                            <th>Role</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $users = $conn->query("SELECT * FROM users ORDER BY created_at DESC");
                        while($u = $users->fetch_assoc()):
                            // Status check logic
                            $status = (isset($u['status']) && !empty($u['status'])) ? $u['status'] : 'active';
                        ?>
                        <tr>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <div class="bg-light rounded-circle p-2 text-center" style="width:35px">
                                        <i class="bi bi-person"></i>
                                    </div>
                                    <span class="fw-semibold"><?php echo $u['username']; ?></span>
                                </div>
                            </td>
                            <td><?php echo $u['email'] ?? 'N/A'; ?></td>
                            <td>
                                <?php if($status == 'active'): ?>
                                    <span class="badge-active">Active</span>
                                <?php else: ?>
                                    <span class="badge-banned">Banned</span>
                                <?php endif; ?>
                            </td>
                            <td><span class="badge <?php echo $u['role'] == 'admin' ? 'bg-primary' : 'bg-secondary'; ?>"><?php echo $u['role']; ?></span></td>
                            <td>
                                <?php if($u['id'] != $_SESSION['user_id']): ?>
                                    <a href="?toggle_ban=<?php echo $u['id']; ?>&current_status=<?php echo $status; ?>" 
                                       class="btn btn-sm <?php echo ($status == 'active') ? 'btn-outline-warning' : 'btn-outline-success'; ?> border-0" 
                                       title="<?php echo ($status == 'active') ? 'Ban User' : 'Unban User'; ?>">
                                         <i class="bi <?php echo ($status == 'active') ? 'bi-slash-circle' : 'bi-check-circle'; ?>"></i>
                                    </a>
                                    
                                    <a href="?delete_user=<?php echo $u['id']; ?>" class="btn btn-sm btn-outline-danger border-0" onclick="return confirm('Are you sure to delete?')">
                                        <i class="bi bi-trash"></i>
                                    </a>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div id="msg-sec" class="management-section d-none">
        <div class="glass-card">
            <h5 class="fw-bold mb-4">User Feedbacks & Messages</h5>
            <div class="list-group list-group-flush">
                <?php 
                $feedbacks = $conn->query("SELECT * FROM contact_messages ORDER BY created_at DESC");
                if($feedbacks->num_rows > 0):
                    while($f = $feedbacks->fetch_assoc()):
                ?>
                <div class="list-group-item border-0 mb-3 p-4 rounded-4 shadow-sm <?php echo $f['status'] == 'unread' ? 'bg-light border-start border-primary border-4' : ''; ?>">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <div>
                            <h6 class="fw-bold mb-0"><?php echo htmlspecialchars($f['name']); ?> <small class="text-muted ms-2 fw-normal"><?php echo $f['email']; ?></small></h6>
                            <small class="text-primary fw-semibold"><?php echo htmlspecialchars($f['subject']); ?></small>
                        </div>
                        <div class="text-end">
                            <small class="text-muted d-block"><?php echo date('d M, h:i A', strtotime($f['created_at'])); ?></small>
                            <?php if($f['status'] == 'unread'): ?>
                                <a href="?mark_read=<?php echo $f['id']; ?>" class="btn btn-sm btn-link text-decoration-none p-0 mt-1">Mark as Read</a>
                            <?php endif; ?>
                        </div>
                    </div>
                    <p class="mb-0 text-secondary mt-2"><?php echo nl2br(htmlspecialchars($f['message'])); ?></p>
                </div>
                <?php endwhile; else: ?>
                    <p class="text-center text-muted py-5">No feedback received yet.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    function showSection(id, btn) {
        document.querySelectorAll('.management-section').forEach(s => s.classList.add('d-none'));
        document.querySelectorAll('.nav-link').forEach(l => l.classList.remove('active'));
        
        document.getElementById(id).classList.remove('d-none');
        btn.classList.add('active');
        
        document.getElementById('title-text').innerText = (id === 'user-sec') ? 'User Management' : 'User Feedback';
    }
</script>

</body>
</html>