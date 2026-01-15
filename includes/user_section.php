<div class="card border-0 shadow-sm">
    <div class="card-body">
        <h5 class="fw-bold mb-4">Users List (Control Panel)</h5>
        <div class="table-responsive">
            <table class="table align-middle table-hover">
                <thead class="table-dark"><tr><th>Username</th><th>Email</th><th>Status</th><th>Action</th></tr></thead>
                <tbody>
                    <?php
                    $u_result = $conn->query("SELECT * FROM users WHERE role != 'admin' ORDER BY id DESC");
                    while($u_row = $u_result->fetch_assoc()):
                    ?>
                    <tr>
                        <td><?php echo htmlspecialchars($u_row['username']); ?></td>
                        <td><?php echo htmlspecialchars($u_row['email']); ?></td>
                        <td><span class="badge <?php echo ($u_row['status'] === 'active') ? 'bg-success' : 'bg-danger'; ?>"><?php echo strtoupper($u_row['status']); ?></span></td>
                        <td>
                            <div class="d-flex gap-2">
                                <?php if($u_row['status'] === 'active'): ?>
                                    <a href="manage_user.php?action=ban&id=<?php echo $u_row['id']; ?>" class="btn btn-sm btn-danger">Ban</a>
                                <?php else: ?>
                                    <a href="manage_user.php?action=unban&id=<?php echo $u_row['id']; ?>" class="btn btn-sm btn-success">Unban</a>
                                <?php endif; ?>
                                <a href="manage_user.php?action=delete&id=<?php echo $u_row['id']; ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('ဖျက်မှာလား?')"><i class="bi bi-trash"></i></a>
                            </div>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>