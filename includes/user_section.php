<div class="card border-0 shadow-sm rounded-4 overflow-hidden">
    <div class="card-header bg-white py-3 border-0">
        <div class="d-flex justify-content-between align-items-center">
            <h5 class="fw-bold mb-0 text-navy">
                <i class="bi bi-people-fill text-coral me-2"></i>User Accounts Control
            </h5>
            <span class="badge bg-light text-dark rounded-pill fw-normal shadow-sm px-3">
                Total Users: <?php echo $conn->query("SELECT id FROM users WHERE role != 'admin'")->num_rows; ?>
            </span>
        </div>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table align-middle table-hover mb-0">
                <thead class="bg-light">
                    <tr>
                        <th class="ps-4 border-0 text-uppercase small fw-bold text-muted">User Profile</th>
                        <th class="border-0 text-uppercase small fw-bold text-muted">Email Address</th>
                        <th class="border-0 text-uppercase small fw-bold text-muted">Account Status</th>
                        <th class="pe-4 border-0 text-uppercase small fw-bold text-muted text-end">Management</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $u_result = $conn->query("SELECT * FROM users WHERE role != 'admin' ORDER BY id DESC");
                    while($u_row = $u_result->fetch_assoc()):
                        $status_class = ($u_row['status'] === 'active') ? 'bg-success-subtle text-success border-success-subtle' : 'bg-danger-subtle text-danger border-danger-subtle';
                    ?>
                    <tr>
                        <td class="ps-4">
                            <div class="d-flex align-items-center">
                                <div class="bg-light rounded-circle d-flex align-items-center justify-content-center text-navy fw-bold me-3 shadow-sm" style="width: 40px; height: 40px; font-size: 0.9rem;">
                                    <?php echo strtoupper(substr($u_row['username'], 0, 1)); ?>
                                </div>
                                <div>
                                    <h6 class="mb-0 fw-semibold"><?php echo htmlspecialchars($u_row['username']); ?></h6>
                                    <small class="text-muted text-capitalize"><?php echo $u_row['role']; ?></small>
                                </div>
                            </div>
                        </td>
                        <td>
                            <span class="text-muted small"><i class="bi bi-envelope me-1"></i><?php echo htmlspecialchars($u_row['email']); ?></span>
                        </td>
                        <td>
                            <span class="badge rounded-pill border px-3 py-2 fw-medium <?php echo $status_class; ?>" style="font-size: 0.75rem;">
                                <i class="bi bi-circle-fill me-1" style="font-size: 0.5rem;"></i>
                                <?php echo strtoupper($u_row['status']); ?>
                            </span>
                        </td>
                        <td class="pe-4 text-end">
                            <div class="d-flex gap-2 justify-content-end">
                                <?php if($u_row['status'] === 'active'): ?>
                                    <a href="dashboard.php?action=ban&id=<?php echo $u_row['id']; ?>&tab=user-mgmt" 
                                       class="btn btn-sm btn-light text-danger rounded-pill px-3 fw-medium shadow-sm border" 
                                       title="Ban User">
                                        <i class="bi bi-slash-circle me-1"></i> Ban
                                    </a>
                                <?php else: ?>
                                    <a href="dashboard.php?action=unban&id=<?php echo $u_row['id']; ?>&tab=user-mgmt" 
                                       class="btn btn-sm btn-light text-success rounded-pill px-3 fw-medium shadow-sm border" 
                                       title="Unban User">
                                        <i class="bi bi-check-circle me-1"></i> Unban
                                    </a>
                                <?php endif; ?>

                                <a href="dashboard.php?delete_user=<?php echo $u_row['id']; ?>&tab=user-mgmt" 
                                   class="btn btn-sm btn-outline-danger rounded-circle p-2 d-flex align-items-center justify-content-center shadow-sm" 
                                   style="width: 32px; height: 32px;"
                                   onclick="return confirm('ဤ User ကို အပြီးဖျက်မှာ သေချာပါသလား?')">
                                    <i class="bi bi-trash"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>