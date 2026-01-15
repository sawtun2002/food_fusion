<div class="row g-4">
    <div class="col-xl-4 col-lg-5">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <h5 class="fw-bold mb-4">Add New Item</h5>
                <form method="POST" enctype="multipart/form-data">
                    <div class="mb-3"><label class="small fw-bold">Food Name</label><input type="text" name="name" class="form-control" required></div>
                    <div class="mb-3"><label class="small fw-bold">Price ($)</label><input type="number" step="0.01" name="price" class="form-control" required></div>
                    <div class="mb-3"><label class="small fw-bold">Category</label>
                        <select name="category" class="form-select">
                            <option value="Fast Food">Fast Food</option>
                            <option value="Main Course">Main Course</option>
                        </select>
                    </div>
                    <div class="mb-4"><label class="small fw-bold">Image</label><input type="file" name="image" class="form-control" required></div>
                    <button type="submit" name="add_food" class="btn btn-warning w-100 fw-bold">Add to Menu</button>
                </form>
            </div>
        </div>
    </div>
    <div class="col-xl-8 col-lg-7">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <h5 class="fw-bold mb-4">Current Menu</h5>
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light"><tr><th>Image</th><th>Name</th><th>Price</th><th>Action</th></tr></thead>
                        <tbody>
                            <?php
                            $result = $conn->query("SELECT * FROM foods ORDER BY id DESC");
                            while($row = $result->fetch_assoc()):
                            ?>
                            <tr>
                                <td><img src="uploads/<?php echo $row['image']; ?>" width="50" height="40" class="rounded object-fit-cover shadow-sm"></td>
                                <td class="fw-bold"><?php echo htmlspecialchars($row['name']); ?></td>
                                <td class="text-success fw-bold">$<?php echo number_format($row['price'], 2); ?></td>
                                <td>
                                    <div class="d-flex gap-1">
                                        <a href="edit_food.php?id=<?php echo $row['id']; ?>" class="btn btn-sm btn-outline-primary"><i class="bi bi-pencil"></i></a>
                                        <a href="delete.php?id=<?php echo $row['id']; ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('ဖျက်မှာလား?')"><i class="bi bi-trash"></i></a>
                                    </div>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>