<?php 
if (session_status() === PHP_SESSION_NONE) { session_start(); }
include 'config.php';
error_reporting(E_ALL);
ini_set('display_errors', 1);

ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$user_query = $conn->prepare("SELECT username, role, image FROM users WHERE id = ?");
$user_query->bind_param("i", $user_id);
$user_query->execute();
$result = $user_query->get_result();

if ($row = $result->fetch_assoc()) {
    $current_username = $row['username'];
    $current_role = $row['role'];
    $current_user_img = $row['image'];
    $_SESSION['username'] = $current_username;
    $_SESSION['role'] = $current_role;
    $_SESSION['image'] = $current_user_img;
} else {
    session_destroy();
    header("Location: login.php");
    exit();
}

// --- Post တင်သည့် Logic (ပြင်ဆင်ပြီး) ---
if (isset($_POST['submit_post'])) {
    $title = $conn->real_escape_string($_POST['title']);
    $desc = $conn->real_escape_string($_POST['description']);
    $ingredients = $conn->real_escape_string($_POST['ingredients']); // အသစ်ထည့်သွင်းခြင်း
    $instructions = $conn->real_escape_string($_POST['instructions']); // အသစ်ထည့်သွင်းခြင်း
    
    $image_name = "";
    if (!empty($_FILES['image']['name'])) {
        $image_name = time() . '_' . $_FILES['image']['name'];
        move_uploaded_file($_FILES['image']['tmp_name'], "uploads/" . $image_name);
    }
    
    // Database Column များနှင့်အညီ Insert လုပ်ခြင်း (Syntax error ကိုပါ ပြင်ဆင်ထားသည်)
    $sql = "INSERT INTO community_recipes (user_id, title, description, image, ingredients, instructions) 
            VALUES ('$user_id', '$title', '$desc', '$image_name', '$ingredients', '$instructions')";
    
    if ($conn->query($sql)) {
        header("Location: communityCookbook.php");
        exit();
    }
}

// --- Post ဖျက်သည့် Logic ---
if (isset($_GET['delete_post'])) {
    $p_id = (int)$_GET['delete_post'];
    $conn->query("DELETE FROM community_recipes WHERE id=$p_id AND user_id=$user_id");
    header("Location: communityCookbook.php");
    exit();
}

// --- Post ပြင်သည့် Logic ---
if (isset($_POST['update_post'])) {
    $p_id = (int)$_POST['post_id'];
    $title = $conn->real_escape_string($_POST['title']);
    $desc = $conn->real_escape_string($_POST['description']);
    // Update မှာပါ ingredients/instructions ပါဝင်လိုပါက ဤနေရာတွင် ထပ်တိုးနိုင်ပါသည်
    $conn->query("UPDATE community_recipes SET title='$title', description='$desc' WHERE id=$p_id AND user_id=$user_id");
    header("Location: communityCookbook.php");
    exit();
}

// --- မူရင်း Comment & Like Logic များ ---
if (isset($_GET['delete_comment'])) {
    $c_id = (int)$_GET['delete_comment'];
    $conn->query("DELETE FROM recipe_comments WHERE id=$c_id AND user_id=$user_id");
    if(isset($_SERVER['HTTP_X_REQUESTED_WITH'])) exit; 
    header("Location: communityCookbook.php"); exit();
}

if (isset($_GET['like_id'])) {
    $r_id = (int)$_GET['like_id'];
    $check_like = $conn->query("SELECT id FROM recipe_likes WHERE user_id=$user_id AND recipe_id=$r_id");
    if ($check_like->num_rows > 0) {
        $conn->query("DELETE FROM recipe_likes WHERE user_id=$user_id AND recipe_id=$r_id");
    } else {
        $conn->query("INSERT INTO recipe_likes (user_id, recipe_id) VALUES ($user_id, $r_id)");
    }
    if(isset($_SERVER['HTTP_X_REQUESTED_WITH'])) exit;
    header("Location: communityCookbook.php"); exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Community Cookbook | Food Fusion</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    
    <style>
        :root { --coral: #ff5733; --honey: #ffb347; --cream: #fffdfa; }
        body { background-color: var(--cream); font-family: 'Segoe UI', sans-serif; color: #444; }
        .btn-fancy { background: var(--coral); color: white; border-radius: 30px; border: none; padding: 12px 30px; font-weight: 600; transition: 0.3s; }
        .btn-fancy:hover { background: #e44d2d; transform: translateY(-2px); }
        .recipe-card { border-radius: 25px; border: none; background: white; margin-bottom: 35px; overflow: hidden; box-shadow: 0 10px 30px rgba(0,0,0,0.05); }
        .post-box { border-radius: 20px; background: white; border: none; box-shadow: 0 5px 15px rgba(0,0,0,0.05); cursor: pointer; transition: 0.3s; }
        .post-box:hover { background: #fdfdfd; }
        .recipe-img { height: 400px; object-fit: cover; width: 100%; border-radius: 20px; }
        .profile-ring { padding: 3px; background: linear-gradient(45deg, var(--coral), var(--honey)); border-radius: 50%; display: inline-flex; }
        .heart-icon { font-size: 1.4rem; transition: 0.3s; color: #ccc; }
        .heart-icon.active { color: var(--coral); }
        .comment-box { background: #f8f9fa; border-radius: 15px; padding: 10px 15px; margin-bottom: 8px; }
        .reply-box { margin-left: 40px; background: #fffcf5; border-left: 3px solid var(--honey); border-radius: 12px; padding: 8px 12px; margin-top: 5px; }
        .comment-input { border-radius: 30px; border: 1px solid #eee; padding: 8px 18px; background: #fbfbfb; }
        .time-ago { font-size: 0.75rem; color: #888; }
        .modal-content { border-radius: 25px; border: none; }
        .post-options .dropdown-toggle::after { display: none; }
        .post-options .btn { color: #888; border-radius: 50%; width: 35px; height: 35px; display: flex; align-items: center; justify-content: center; }
        .post-options .btn:hover { background: #eee; }
    </style>
</head>
<body>

<?php include 'includes/navbar.php'; ?>

<div class="container pb-5 mt-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card post-box p-3 mb-5 d-flex flex-row align-items-center gap-3" data-bs-toggle="modal" data-bs-target="#postModal">
                <div class="profile-ring">
                    <img src="<?php echo (!empty($current_user_img)) ? 'uploads/'.$current_user_img : 'https://cdn-icons-png.flaticon.com/512/149/149071.png'; ?>" width="40" height="40" class="rounded-circle">
                </div>
                <div class="bg-light flex-grow-1 py-2 px-4 rounded-pill text-muted">
                    What's on your mind, <?php echo htmlspecialchars($current_username); ?>?
                </div>
                <button class="btn btn-fancy">Post</button>
            </div>

            <?php 
            $res = $conn->query("SELECT cr.*, u.username, u.image as u_img FROM community_recipes cr JOIN users u ON cr.user_id = u.id ORDER BY cr.id DESC");
            while($row = $res->fetch_assoc()):
                $rid = $row['id'];
                $comment_total = $conn->query("SELECT id FROM recipe_comments WHERE recipe_id=$rid")->num_rows;
                $likes_count = $conn->query("SELECT id FROM recipe_likes WHERE recipe_id=$rid")->num_rows;
                $is_liked = ($conn->query("SELECT id FROM recipe_likes WHERE user_id=$user_id AND recipe_id=$rid")->num_rows > 0);
                $display_u_img = (!empty($row['u_img']) && file_exists("uploads/".$row['u_img'])) ? "uploads/".$row['u_img'] : "https://cdn-icons-png.flaticon.com/512/149/149071.png";
            ?>
            <div class="card recipe-card p-4" id="recipe-<?php echo $rid; ?>">
                <div class="d-flex align-items-center justify-content-between mb-4">
                    <div class="d-flex align-items-center">
                        <div class="profile-ring me-3"><img src="<?php echo $display_u_img; ?>" width="45" height="45" class="rounded-circle" style="object-fit: cover;"></div>
                        <div>
                            <h6 class="mb-0 fw-bold"><?php echo htmlspecialchars($row['username']); ?></h6>
                            <small class="time-ago" data-time="<?php echo $row['created_at']; ?>"></small>
                        </div>
                    </div>

                    <?php if($row['user_id'] == $user_id): ?>
                    <div class="dropdown post-options">
                        <button class="btn btn-link dropdown-toggle" type="button" data-bs-toggle="dropdown">
                            <i class="bi bi-three-dots"></i>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end shadow border-0" style="border-radius: 12px;">
                            <li><a class="dropdown-item py-2" href="#" onclick="openEditModal(<?php echo $rid; ?>, '<?php echo addslashes($row['title']); ?>', '<?php echo addslashes($row['description']); ?>')"><i class="bi bi-pencil me-2"></i> Edit Post</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item py-2 text-danger" href="?delete_post=<?php echo $rid; ?>" onclick="return confirm('Are you sure to delete this post?')"><i class="bi bi-trash me-2"></i> Delete Post</a></li>
                        </ul>
                    </div>
                    <?php endif; ?>
                </div>

                <h3 class="fw-bold mb-3"><?php echo htmlspecialchars($row['title']); ?></h3>
                <p class="text-muted mb-4"><?php echo nl2br(htmlspecialchars($row['description'])); ?></p>
                
                <?php if(!empty($row['ingredients'])): ?>
                    <div class="mb-3">
                        <h6 class="fw-bold text-coral"><i class="bi bi-egg-fried me-2"></i>Ingredients</h6>
                        <p class="small text-muted"><?php echo nl2br(htmlspecialchars($row['ingredients'])); ?></p>
                    </div>
                <?php endif; ?>

                <?php if(!empty($row['image'])): ?>
                    <img src="uploads/<?php echo $row['image']; ?>" class="recipe-img mb-4 shadow-sm">
                <?php endif; ?>

                <div class="d-flex gap-4 align-items-center mb-2">
                    <a href="?like_id=<?php echo $rid; ?>" class="text-decoration-none d-flex align-items-center ajax-like">
                        <i class="bi <?php echo $is_liked ? 'bi-heart-fill active' : 'bi-heart'; ?> heart-icon me-2"></i>
                        <span class="fw-bold like-count <?php echo $is_liked ? 'text-danger' : 'text-muted'; ?>"><?php echo $likes_count; ?> Likes</span>
                    </a>
                    <div class="comment-trigger text-muted fw-bold d-flex align-items-center" data-bs-toggle="collapse" data-bs-target="#commentCollapse<?php echo $rid; ?>" style="cursor:pointer">
                        <i class="bi bi-chat-left-text me-2"></i> <span class="c-total"><?php echo $comment_total; ?>&nbsp; </span>Comments 
                    </div>
                </div>

                <div class="collapse mt-3" id="commentCollapse<?php echo $rid; ?>">
                    <hr>
                    <div class="comments-list mb-3" id="list-<?php echo $rid; ?>">
                        <?php 
                        $coms = $conn->query("SELECT rc.*, u.username FROM recipe_comments rc JOIN users u ON rc.user_id = u.id WHERE rc.recipe_id=$rid AND rc.parent_id=0 ORDER BY rc.id ASC");
                        while($c = $coms->fetch_assoc()):
                            $cid = $c['id'];
                        ?>
                            <div class="comment-box" id="comment-<?php echo $cid; ?>">
                                <strong class="text-dark small"><?php echo htmlspecialchars($c['username']); ?></strong>
                                <span class="time-ago ms-2" data-time="<?php echo $c['created_at']; ?>"></span>
                                <p class="mb-1 mt-1 small"><?php echo htmlspecialchars($c['comment']); ?></p>
                                <div class="d-flex">
                                    <span class="reply-btn small me-3 text-primary" style="cursor:pointer" onclick="toggleReplyForm(<?php echo $cid; ?>)">Reply</span>
                                    <?php if($c['user_id'] == $user_id): ?>
                                        <a href="?delete_comment=<?php echo $cid; ?>" class="delete-btn small ajax-delete text-danger text-decoration-none">Delete</a>
                                    <?php endif; ?>
                                </div>
                                
                                <form action="post_comment.php" method="POST" id="replyForm<?php echo $cid; ?>" class="mt-2 d-none ajax-comment-form" data-recipe="<?php echo $rid; ?>">
                                    <input type="hidden" name="recipe_id" value="<?php echo $rid; ?>">
                                    <input type="hidden" name="parent_id" value="<?php echo $cid; ?>">
                                    <div class="d-flex gap-2">
                                        <input type="text" name="comment" class="form-control comment-input" placeholder="Reply..." required>
                                        <button type="submit" class="btn btn-sm btn-fancy px-3">Send</button>
                                    </div>
                                </form>

                                <?php 
                                $replies = $conn->query("SELECT rc.*, u.username FROM recipe_comments rc JOIN users u ON rc.user_id = u.id WHERE rc.parent_id=$cid ORDER BY rc.id ASC");
                                while($r = $replies->fetch_assoc()):
                                ?>
                                    <div class="reply-box">
                                        <strong class="text-dark x-small"><?php echo htmlspecialchars($r['username']); ?></strong>
                                        <span class="time-ago ms-2" data-time="<?php echo $r['created_at']; ?>"></span>
                                        <p class="mb-0 small"><?php echo htmlspecialchars($r['comment']); ?></p>
                                    </div>
                                <?php endwhile; ?>
                            </div>
                        <?php endwhile; ?>
                    </div>
                    
                    <form action="post_comment.php" method="POST" class="d-flex gap-2 ajax-comment-form" data-recipe="<?php echo $rid; ?>">
                        <input type="hidden" name="recipe_id" value="<?php echo $rid; ?>">
                        <input type="hidden" name="parent_id" value="0">
                        <input type="text" name="comment" class="form-control comment-input" placeholder="Write a comment..." required>
                        <button type="submit" class="btn btn-fancy py-2 px-4">Post</button>
                    </form>
                </div>
            </div>
            <?php endwhile; ?>
        </div>
    </div>
</div>

<div class="modal fade" id="postModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content p-3">
            <div class="modal-header border-0">
                <h5 class="fw-bold">Create New Post</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="" method="POST" enctype="multipart/form-data">
                <div class="modal-body">
                    <label class="fw-bold small text-muted mb-1">Title</label>
                    <input type="text" name="title" class="form-control mb-3" placeholder="Recipe Title" required style="border-radius: 12px;">

                    <label class="fw-bold small text-muted mb-1">Description</label>
                    <textarea name="description" class="form-control mb-3" rows="3" placeholder="Share your recipe story..." required style="border-radius: 12px;"></textarea>
                    
                    <label class="fw-bold small text-muted mb-1">Ingredients</label>
                    <textarea name="ingredients" class="form-control mb-3" rows="3" placeholder="List ingredients..." required style="border-radius: 12px;"></textarea>
                    
                    <label class="fw-bold small text-muted mb-1">Instructions</label>
                    <textarea name="instructions" class="form-control mb-3" rows="4" placeholder="Step by step instructions..." required style="border-radius: 12px;"></textarea>

                    <label class="form-label fw-bold small text-muted">Upload Photo (Optional)</label>
                    <input type="file" name="image" class="form-control" style="border-radius: 12px;">
                </div>
                <div class="modal-footer border-0">
                    <button type="submit" name="submit_post" class="btn btn-fancy w-100 py-3">Publish Post</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="editPostModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content p-3">
            <div class="modal-header border-0">
                <h5 class="fw-bold">Edit Post</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="" method="POST">
                <div class="modal-body">
                    <input type="hidden" name="post_id" id="edit_post_id">
                    <input type="text" name="title" id="edit_title" class="form-control mb-3" placeholder="Recipe Title" required style="border-radius: 12px;">
                    <textarea name="description" id="edit_desc" class="form-control mb-3" rows="4" placeholder="Share your recipe details..." required style="border-radius: 12px;"></textarea>
                    <small class="text-muted">Note: ပုံကို Edit လုပ်ခွင့်မပေးထားပါ (မူရင်းကုတ်မထိခိုက်စေရန်)</small>
                </div>
                <div class="modal-footer border-0">
                    <button type="submit" name="update_post" class="btn btn-fancy w-100 py-3">Update Post</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
const currentLoggedInUser = "<?php echo htmlspecialchars($current_username); ?>";

function openEditModal(id, title, desc) {
    document.getElementById('edit_post_id').value = id;
    document.getElementById('edit_title').value = title;
    document.getElementById('edit_desc').value = desc;
    var myModal = new bootstrap.Modal(document.getElementById('editPostModal'));
    myModal.show();
}

function updateTimeLabels() {
    const elements = document.querySelectorAll('.time-ago');
    elements.forEach(el => {
        const timeStr = el.getAttribute('data-time');
        if (!timeStr) return;
        const date = new Date(timeStr.replace(' ', 'T'));
        const now = new Date();
        const diffInSeconds = Math.floor((now - date) / 1000);
        let label = "";
        if (diffInSeconds < 60) label = (diffInSeconds < 5) ? "Just now" : diffInSeconds + "s ago";
        else if (diffInSeconds < 3600) label = Math.floor(diffInSeconds / 60) + "m ago";
        else if (diffInSeconds < 86400) label = Math.floor(diffInSeconds / 3600) + "h ago";
        else label = Math.floor(diffInSeconds / 86400) + "d ago";
        el.innerText = label;
    });
}
setInterval(updateTimeLabels, 1000);
updateTimeLabels();

function toggleReplyForm(cid) { document.getElementById('replyForm' + cid).classList.toggle('d-none'); }

document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.ajax-like').forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            const heart = this.querySelector('.heart-icon');
            const countSpan = this.querySelector('.like-count');
            fetch(this.href, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
            .then(() => {
                let count = parseInt(countSpan.innerText);
                if(heart.classList.contains('bi-heart')) {
                    heart.classList.replace('bi-heart', 'bi-heart-fill'); heart.classList.add('active');
                    countSpan.classList.replace('text-muted', 'text-danger'); countSpan.innerText = (count + 1) + " Likes";
                } else {
                    heart.classList.replace('bi-heart-fill', 'bi-heart'); heart.classList.remove('active');
                    countSpan.classList.replace('text-danger', 'text-muted'); countSpan.innerText = (count - 1) + " Likes";
                }
            });
        });
    });

    document.querySelectorAll('.ajax-comment-form').forEach(form => {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            const recipeId = this.getAttribute('data-recipe');
            const commentInput = this.querySelector('input[name="comment"]');
            const listDiv = document.getElementById('list-' + recipeId);
            const countSpan = document.querySelector('#recipe-' + recipeId + ' .c-total');
            fetch(this.action, { method: 'POST', body: formData, headers: { 'X-Requested-With': 'XMLHttpRequest' } })
            .then(res => {
                if(res.ok) {
                    const now = new Date();
                    const nowISO = now.getFullYear() + "-" + String(now.getMonth() + 1).padStart(2, '0') + "-" + String(now.getDate()).padStart(2, '0') + " " + String(now.getHours()).padStart(2, '0') + ":" + String(now.getMinutes()).padStart(2, '0') + ":" + String(now.getSeconds()).padStart(2, '0');
                    const newComment = `
                        <div class="comment-box border-start border-primary border-4">
                            <strong class="text-dark small">${currentLoggedInUser}</strong>
                            <span class="time-ago ms-2" data-time="${nowISO}">Just now</span>
                            <p class="mb-1 mt-1 small">${commentInput.value}</p>
                        </div>`;
                    listDiv.insertAdjacentHTML('beforeend', newComment);
                    countSpan.innerText = parseInt(countSpan.innerText) + 1;
                    commentInput.value = '';
                    if(this.id.startsWith('replyForm')) this.classList.add('d-none');
                    updateTimeLabels();
                }
            });
        });
    });

    document.querySelectorAll('.ajax-delete').forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            if(!confirm('ဖျက်မှာ သေချာလား?')) return;
            const commentDiv = this.closest('.comment-box, .reply-box');
            const recipeCard = this.closest('.recipe-card');
            const countSpan = recipeCard.querySelector('.c-total');
            fetch(this.href, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
            .then(() => { 
                commentDiv.remove(); 
                countSpan.innerText = parseInt(countSpan.innerText) - 1;
            });
        });
    });
});
</script>
<?php include 'includes/footer.php'; ?>
<?php include 'includes/logout_modal.php'; ?>
</body>
</html>