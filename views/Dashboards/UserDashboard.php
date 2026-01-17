<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>User Dashboard</title>
    <style>
        :root{--bg:#f4f7fb;--card:#ffffff;--primary:#0078d4;--muted:#666;--border:#e6e9ee;--text:#222}
        *{box-sizing:border-box}
        body{margin:0;font-family:Inter,Segoe UI,Arial,sans-serif;background:var(--bg);color:var(--text);display:flex;min-height:100vh}
        
        /* Navigation Sidebar */
        nav {width: 250px; background: var(--card); border-right: 1px solid var(--border); padding: 20px; display: flex; flex-direction: column; gap: 10px;}
        nav h1 { font-size: 1.2rem; margin-bottom: 20px; color: var(--primary); text-align: center; }
        .nav-btn { padding: 12px; border: none; background: none; text-align: left; font-size: 15px; cursor: pointer; border-radius: 8px; color: var(--muted); transition: 0.2s; }
        .nav-btn:hover { background: #f0f4f8; color: var(--primary); }
        .nav-btn.active { background: var(--primary); color: #fff; }

        /* Main Content Area */
        main { flex: 1; padding: 40px; overflow-y: auto; max-height: 100vh; }
        .section { display: none; max-width: 800px; margin: 0 auto; }
        .section.active { display: block; }

        /* Settings Card */
        .card { background: var(--card); padding: 24px; border-radius: 10px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); }
        h2 { margin-top: 0; font-weight: 600; }
        .input-group { margin-bottom: 15px; }
        label { display: block; margin-bottom: 6px; font-size: 13px; color: #444; }
        input { width: 100%; padding: 10px; border: 1px solid var(--border); border-radius: 8px; font-size: 14px; }
        button.save-btn { background: var(--primary); color: #white; border: 0; padding: 10px 20px; border-radius: 8px; color: white; cursor: pointer; }

        /* Posts Section */
        .post-list { display: flex; flex-direction: column; gap: 20px; }
        .post-card { background: var(--card); padding: 20px; border-radius: 10px; border: 1px solid var(--border); }
        .post-card h3 { margin: 0 0 10px; font-size: 18px; color: var(--primary); }
        .post-card p { margin: 0 0 15px; color: #444; line-height: 1.5; }
        .post-meta { display: flex; gap: 20px; font-size: 13px; color: var(--muted); border-top: 1px solid var(--border); padding-top: 12px; }
        .meta-item { display: flex; align-items: center; gap: 5px; }

        @media (max-width: 768px) {
            body { flex-direction: column; }
            nav { width: 100%; border-right: none; border-bottom: 1px solid var(--border); }
        }
    </style>
</head>
<body>

    <nav>
        <h1>Dashboard</h1>
        <button class="nav-btn active" onclick="showSection('settings', this)">User Settings</button>
        <button class="nav-btn" onclick="showSection('posts', this)">My Posts</button>
        <hr style="width:100%; border:0; border-top:1px solid var(--border); margin: 10px 0;">
        <a href="api/logout.php" class="nav-btn" style="text-decoration:none; color: #d93025;">Logout</a>
    </nav>

    <main>
        <div id="settings" class="section active">
            <h2>Account Settings</h2>
            <div class="card">
                <form action="api/users/update.php" method="POST">
                    <div class="input-group">
                        <label>Full Name</label>
                        <input type="text" name="name" value="John Doe">
                    </div>
                    <div class="input-group">
                        <label>Email Address</label>
                        <input type="email" name="email" value="john@example.com">
                    </div>
                    <hr style="margin: 20px 0; border: 0; border-top: 1px solid var(--border);">
                    <div class="input-group">
                        <label>Current Password (to verify changes)</label>
                        <input type="password" name="old_password" required>
                    </div>
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
                <?php
                // Temporary array of posts
                $posts = [
                    [
                        "heading" => "Getting Started with Web Design",
                        "body" => "Today I learned how to use CSS variables to create a consistent design language across multiple pages...",
                        "upvotes" => 124,
                        "downvotes" => 3
                    ],
                    [
                        "heading" => "The Importance of Clean Code",
                        "body" => "Writing code is easy, but writing code that your future self can understand is the real challenge...",
                        "upvotes" => 89,
                        "downvotes" => 1
                    ],
                    [
                        "heading" => "PHP vs Node.js in 2024",
                        "body" => "Both have their strengths. PHP is excellent for rapid development and has a massive hosting ecosystem...",
                        "upvotes" => 56,
                        "downvotes" => 12
                    ]
                ];

                foreach($posts as $post): ?>
                    <div class="post-card">
                        <h3><?php echo htmlspecialchars($post['heading']); ?></h3>
                        <p><?php echo htmlspecialchars($post['body']); ?></p>
                        <div class="post-meta">
                            <span class="meta-item">▲ <?php echo $post['upvotes']; ?> Upvotes</span>
                            <span class="meta-item">▼ <?php echo $post['downvotes']; ?> Downvotes</span>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </main>

    <script>
        function showSection(sectionId, btn) {
            // Hide all sections
            document.querySelectorAll('.section').forEach(s => s.classList.remove('active'));
            // Remove active class from all buttons
            document.querySelectorAll('.nav-btn').forEach(b => b.classList.remove('active'));
            
            // Show selected section
            document.getElementById(sectionId).classList.add('active');
            // Add active class to clicked button
            btn.classList.add('active');
        }
    </script>
</body>
</html>