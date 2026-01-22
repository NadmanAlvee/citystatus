<?php
session_start();
$isLoggedIn = isset($_SESSION['user_id']);
$isAdmin = (isset($_SESSION['is_admin']) && $_SESSION['is_admin'] === true);
if (!isset($_SESSION['user_id'])) {
    header("Location: login");
    exit;
}

require_once 'models/User.php';
require_once 'models/Post.php';
require_once 'lib/DBConfig.php';

$database = new Database();
$db = $database->getConnection();

$userModel = new User($db); 
$userData = $userModel->getUserById($_SESSION['user_id']);

$postModel = new Post($db);
$userPosts = $postModel->getUserPosts($_SESSION['user_id']);
?>

<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>User Dashboard | CityStatus</title>
    <style>
        :root { --bg: #f4f7fb; --card: #ffffff; --primary: #0078d4; --muted: #666; --border: #e2e8f0; --text: #222; }
        * { box-sizing: border-box; }
        body { margin:0; font-family: 'Inter', system-ui, sans-serif; background:var(--bg); color: var(--text); }
        
        .btn { padding: 8px 14px; font-weight: 600; background: var(--primary); color: white; border-radius: 6px; border: none; cursor: pointer; transition: opacity 0.2s; }
        .btn:hover { opacity: 0.9; }
        .btn-danger { background: #d93025; margin-top: 10px; }
        
        nav { width: 260px; background: var(--card); border-right: 1px solid var(--border); padding: 20px; display: flex; flex-direction: column; gap: 8px; min-height: 100vh; position: fixed; }
        nav h1 { font-size: 1.2rem; margin-bottom: 20px; color: var(--primary); padding-left: 12px; }
        
        .nav-btn { padding: 12px; border: none; background: none; text-align: left; font-size: 15px; cursor: pointer; border-radius: 8px; color: var(--muted); transition: 0.2s; text-decoration: none; }
        .nav-btn:hover { background: #f0f4f8; color: var(--primary); }
        .nav-btn.active { background: #e0effa; color: var(--primary); font-weight: 600; }
        
        main { margin-left: 260px; padding: 40px; width: calc(100% - 260px); }
        .section { display: none; max-width: 800px; margin: 0 auto; }
        .section.active { display: block; animation: fadeIn 0.3s ease; }
        
        .card { background: var(--card); padding: 30px; border-radius: 12px; box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05); border: 1px solid var(--border); }
        .input-group { margin-bottom: 15px; }
        label { display: block; margin-bottom: 8px; font-size: 13px; font-weight: 600; color: #444; }
        input { width: 100%; padding: 11px; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 14px; transition: border-color 0.2s; }
        input:focus { outline: none; border-color: var(--primary); }
        input:read-only { background: #f8fafc; color: #64748b; cursor: not-allowed; }
        
        .post-card { background: var(--card); padding: 20px; border-radius: 10px; border: 1px solid var(--border); margin-bottom: 20px; }
        .post-card p { color: #334155; line-height: 1.6; margin-bottom: 15px; font-size: 15px; }
        .post-meta { display: flex; gap: 20px; font-size: 12px; color: var(--muted); border-top: 1px solid #f1f5f9; padding-top: 15px; align-items: center; }
        
        @keyframes fadeIn { from { opacity: 0; transform: translateY(5px); } to { opacity: 1; transform: translateY(0); } }
    </style>
</head>
<body>
    
    <?php include 'views/header.php'; ?>

    <div style="display: flex; margin-top: 65px;">
        <nav>
            <h1>Dashboard</h1>
            <button class="nav-btn active" onclick="showSection('settings', this)">User Settings</button>
            <button class="nav-btn" onclick="showSection('posts', this)">My Posts (<?php echo count($userPosts); ?>)</button>
            <hr style="width:100%; border:0; border-top:1px solid var(--border); margin: 10px 0;">
            <a href="logout" class="nav-btn" style="color: #d93025;">Logout</a>
        </nav>

        <main>
            <div id="settings" class="section active">
                <h2 style="margin-top:0">Account Settings</h2>
                <div class="card">
                    <form id="updateProfileForm">
                        <div class="input-group">
                            <label>Full Name</label>
                            <input type="text" id="update-name" name="name" value="<?php echo htmlspecialchars($userData['name']); ?>" required>
                        </div>
                        <div class="input-group">
                            <label>Email Address</label>
                            <input type="email" value="<?php echo htmlspecialchars($userData['email']); ?>" readonly>
                        </div>
                        <div class="input-group">
                            <label>District</label>
                            <input type="text" id="update-district" name="district" value="<?php echo htmlspecialchars($userData['district']); ?>">
                        </div>
                        
                        <hr style="margin: 25px 0; border: 0; border-top: 1px solid var(--border);">
                        
                        <div class="input-group">
                            <label for="newPass">Change Password (Optional)</label>
                            <input type="password" id="newPass" placeholder="Enter new password">
                        </div>
                        <div class="input-group">
                            <label for="confirmNewPass">Confirm New Password</label>
                            <input type="password" id="confirmNewPass" placeholder="Repeat new password">
                        </div>
                        
                        <button type="submit" class="btn" style="width:100%; padding: 12px; margin-top: 10px;">Update Profile</button>
                    </form>
                </div>
            </div>

            <div id="posts" class="section">
                <h2 style="margin-top:0">Your Activity</h2>
                <div class="post-list">
                    <?php if (empty($userPosts)): ?>
                        <p style="color: var(--muted); text-align: center; padding: 40px;">You haven't posted anything yet.</p>
                    <?php else: ?>
                        <?php foreach($userPosts as $post): ?>
                            <div class="post-card">
                                <p><?php echo htmlspecialchars($post['text']); ?></p>
                                <div class="post-meta">
                                    <span>📍 <?php echo htmlspecialchars($post['city'] . ', ' . $post['division']); ?></span>
                                    <span>▲ <?php echo $post['upvote']; ?></span>
                                    <span>▼ <?php echo $post['downvote']; ?></span>
                                    <span>📅 <?php echo date('M d, Y', strtotime($post['created_at'])); ?></span>
                                </div>
                                <button class="btn btn-danger" onclick="deletePost(<?php echo $post['post_id']; ?>)">Delete Post</button>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </main>
    </div>

    <script>
        // HANDLE PROFILE UPDATE (Works like Forgot Password)
        const updateForm = document.getElementById('updateProfileForm');

        updateForm.addEventListener('submit', async function(e) {
            e.preventDefault();

            const name = document.getElementById('update-name').value.trim();
            const district = document.getElementById('update-district').value.trim();
            const newPassword = document.getElementById('newPass').value;
            const confirmPassword = document.getElementById('confirmNewPass').value;

            // Password matching validation
            if (newPassword !== "" || confirmPassword !== "") {
                if (newPassword !== confirmPassword) {
                    alert("Passwords do not match!");
                    return;
                }
            }

            const payload = {
                user_id: <?php echo $_SESSION['user_id']; ?>,
                name: name,
                district: district
            };

            if (newPassword) {
                payload.password = newPassword;
            }

            try {
                const response = await fetch('/citystatus/api/user/update', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(payload)
                });

                const result = await response.json();

                if (result.success) {
                    alert('Profile updated successfully!');
                    location.reload(); // Refresh to show new data
                } else {
                    alert('Error: ' + (result.error || 'Update failed'));
                }
            } catch (err) {
                alert('An error occurred. Check console for details.');
                console.error(err);
            }
        });

        // HANDLE POST DELETION (Fetch Version)
        async function deletePost(id) {
            if (!confirm('Are you sure you want to delete this post?')) return;

            try {
                const response = await fetch('/citystatus/api/post/deletePost', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ post_id: id })
                });

                const result = await response.json();
                if (result.success) {
                    location.reload();
                } else {
                    alert('Delete failed');
                }
            } catch (err) {
                console.error(err);
            }
        }

        // NAVIGATION LOGIC
        function showSection(sectionId, btn) {
            document.querySelectorAll('.section').forEach(s => s.classList.remove('active'));
            document.querySelectorAll('.nav-btn').forEach(b => b.classList.remove('active'));
            document.getElementById(sectionId).classList.add('active');
            btn.classList.add('active');
        }
    </script>
</body>
</html>