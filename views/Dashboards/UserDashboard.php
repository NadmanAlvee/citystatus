<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login");
    exit;
}
$isLoggedIn = isset($_SESSION['user_id']);
$isAdmin = (isset($_SESSION['is_admin']) && $_SESSION['is_admin'] === true);

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
    <title>User Dashboard</title>
    <style>
        :root { --bg: #f4f7fb; --card: #ffffff; --primary: #0078d4; --muted: #666; --border: #979797; --text: #222; }
        * { box-sizing: border-box; }
        .btn { padding: 8px 14px;font-weight: 500; background: var(--primary); color: white; border-radius: 6px; border: none; cursor: pointer; }
        .btn-danger { background: red; }
        nav { width: 250px; background: var(--card); border-right: 1px solid var(--border); padding: 20px; display: flex; flex-direction: column; gap: 10px; min-height: 100vh; }
        nav h1 { font-size: 1.2rem; margin-bottom: 20px; color: var(--primary); text-align: center; }
        .nav-btn { padding: 12px; border: none; background: none; text-align: left; font-size: 15px; cursor: pointer; border-radius: 8px; color: var(--muted); transition: 0.2s; text-decoration: none; }
        .nav-btn:hover { background: #f0f4f8; color: var(--primary); }
        .nav-btn.active { background: var(--primary); color: #fff; }
        main { flex: 1; padding: 40px; overflow-y: auto; }
        .section { display: none; max-width: 800px; margin: 0 auto; }
        .section.active { display: block; }
        .card { background: var(--card); padding: 24px; border-radius: 10px; box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05); }
        .input-group { margin-bottom: 15px; }
        label { display: block; margin-bottom: 6px; font-size: 13px; color: #444; }
        input { width: 100%; padding: 10px; border: 1px solid var(--border); border-radius: 8px; font-size: 14px; }
        button.save-btn { background: var(--primary); border: 0; padding: 10px 20px; border-radius: 8px; color: white; cursor: pointer; }
        .post-card { background: var(--card); padding: 20px; border-radius: 10px; border: 1px solid var(--border); margin-bottom: 20px; }
        .post-card p { color: #444; line-height: 1.5; margin-bottom: 15px; }
        .post-meta { display: flex; gap: 20px; font-size: 13px; color: var(--muted); border-top: 1px solid var(--border); padding-top: 12px; }
    </style>
</head>
<body style="margin:0; font-family: Inter, sans-serif; background:var(--bg);">
    
    <?php include 'views/header.php'; ?>

    <div style="display: flex;">
        <nav>
            <h1>Dashboard</h1>
            <button class="nav-btn active" onclick="showSection('settings', this)">User Settings</button>
            <button class="nav-btn" onclick="showSection('posts', this)">My Posts (<?php echo count($userPosts); ?>)</button>
            <hr style="width:100%; border:0; border-top:1px solid var(--border); margin: 10px 0;">
            <a href="logout" class="nav-btn" style="color: #d93025;">Logout</a>
        </nav>

        <main>
            <div id="settings" class="section active">
                <h2>Account Settings</h2>
                <div class="card">
                    <form action="api/user/update" method="POST">
                        <div class="input-group">
                            <label>Full Name</label>
                            <input type="text" name="name" value="<?php echo htmlspecialchars($userData['name']); ?>">
                        </div>
                        <div class="input-group">
                            <label>Email Address</label>
                            <input type="email" name="email" value="<?php echo htmlspecialchars($userData['email']); ?>" readonly style="background: #f9f9f9;">
                        </div>
                        <div class="input-group">
                            <label>District</label>
                            <input type="text" name="district" value="<?php echo htmlspecialchars($userData['district']); ?>">
                        </div>
                        <hr style="margin: 20px 0; border: 0; border-top: 1px solid var(--border);">
                        <div class="input-group">
                            <label>New Password (leave blank to keep current)</label>
                            <input type="password" name="new_password">
                        </div>
                        <button type="submit" class="save-btn">Update Profile</button>
                    </form>
                </div>
            </div>

            <div id="posts" class="section">
                <h2>Your Activity</h2>
                <div class="post-list">
                    <?php if (empty($userPosts)): ?>
                        <p style="color: var(--muted);">You haven't posted anything yet.</p>
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
                                <button class="btn btn-danger" onclick="deletePost(<?php echo $post['post_id']; ?>)">Delete</button>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </main>
    </div>

    <script>
        function ajax(method, url, data, callback) {
            const xhr = new XMLHttpRequest();
            xhr.open(method, url, true);
            xhr.setRequestHeader('Accept', 'application/json');
            if (method === 'POST') xhr.setRequestHeader('Content-Type', 'application/json');
            
            xhr.onload = function() {
                if (xhr.status >= 200 && xhr.status < 300) {
                    callback(JSON.parse(xhr.responseText));
                } else {
                    console.error('Request failed:', xhr.responseText);
                }
            };
            xhr.send(data ? JSON.stringify(data) : null);
        }
        function deletePost(id) {
            if (!confirm('Delete post ?')) return;
            ajax('POST', '/citystatus/api/post/deletePost', {post_id: id}, function(res) {
                if (res.success) location.reload();
            });
        }
        function showSection(sectionId, btn) {
            document.querySelectorAll('.section').forEach(s => s.classList.remove('active'));
            document.querySelectorAll('.nav-btn').forEach(b => b.classList.remove('active'));
            document.getElementById(sectionId).classList.add('active');
            btn.classList.add('active');
        }
    </script>
</body>
</html>
