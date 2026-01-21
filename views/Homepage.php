<?php
session_start();
$isLoggedIn = isset($_SESSION['user_id']);
$isAdmin = (isset($_SESSION['is_admin']) && $_SESSION['is_admin'] === true);
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>CITYSTATUS | Home</title>
    <style>
        :root{--bg:#f4f7fb;--card:#ffffff;--primary:#0078d4;--muted:#666;--border:#e2e8f0;--text:#222}
        body { margin:0; font-family: 'Segoe UI', Arial, sans-serif; background:var(--bg); color:var(--text); }
        * { box-sizing:border-box }
        
        .container { max-width: 650px; margin: 20px auto; padding: 0 15px; }

        /* Create Post Box */
        .create-post { background: var(--card); padding: 20px; border: 1px solid var(--border); border-radius: 12px; box-shadow: 0 2px 10px rgba(0,0,0,0.05); margin-bottom: 25px; }
        .create-post textarea { width: 100%; border: none; resize: none; min-height: 80px; font-size: 16px; outline: none; font-family: inherit; margin-bottom: 10px; }
        .post-actions { display: flex; justify-content: flex-end; border-top: 1px solid var(--border); padding-top: 12px; }
        .post-selectors { display: flex; gap: 10px; margin-bottom: 10px; }
        .post-selectors select, .post-selectors input { padding: 8px; border: 1px solid var(--border); border-radius: 6px; font-size: 13px; }

        .btn-post { background: var(--primary); color: white; border: none; padding: 8px 24px; border-radius: 20px; font-weight: 600; cursor: pointer; }

        /* Post Cards */
        #post-feed { display: flex; flex-direction: column; gap: 15px; }
        .post-card { background: var(--card); padding: 20px; border-radius: 12px; border: 1px solid var(--border); box-shadow: 0 2px 5px rgba(0,0,0,0.02); }
        .post-card .author { font-weight: bold; font-size: 14px; margin-bottom: 5px; display: block; color: var(--primary); }
        .post-card p { margin: 10px 0; color: #333; line-height: 1.5; white-space: pre-wrap; }

        /* Interaction Buttons */
        .post-interactions { display: flex; gap: 10px; margin: 15px 0; padding-top: 10px; border-top: 1px solid #f8f9fa; }
        .btn-vote, .btn-report { 
            display: flex; align-items: center; gap: 6px; background: #f8f9fa; border: 1px solid #eef0f2; 
            padding: 6px 14px; border-radius: 20px; cursor: pointer; font-size: 13px; transition: all 0.2s; 
        }
        .btn-up:hover { background: #e7f3ff; color: #0078d4; border-color: #0078d4; }
        .btn-down:hover { background: #f5f5f5; color: #333; }
        .btn-report:hover { background: #fff0f0; color: #d93025; border-color: #d93025; }

        .post-meta { display: flex; justify-content: space-between; font-size: 12px; color: var(--muted); margin-top: 10px; }
        #loading { text-align: center; padding: 20px; color: var(--muted); }
    </style>
</head>
<body>

    <?php include 'views/header.php'; ?>

    <div class="container">
        <?php if($isLoggedIn): ?>
          <div class="create-post">
              <form id="postForm">
                  <textarea id="postText" placeholder="What's happening in your city?" required></textarea>
                  <div class="post-selectors">
                      <select id="postDivision" required>
                          <option value="">Select Division</option>
                          <option value="Dhaka">Dhaka</option>
                          <option value="Chittagong">Chittagong</option>
                      </select>
                      <input type="text" id="postCity" placeholder="City (Optional)">
                  </div>
                  <div class="post-actions">
                      <button type="submit" class="btn-post">Post Update</button>
                  </div>
              </form>
          </div>
        <?php endif; ?>

        <div id="post-feed"></div>
        <div id="loading">Gathering latest updates...</div>
    </div>

    <script>
        const POST_API = '/citystatus/api/post/';

        function loadPosts() {
            const feed = document.getElementById('post-feed');
            const loader = document.getElementById('loading');
            
            fetch(POST_API + 'getPosts')
                .then(res => res.json())
                .then(posts => {
                    renderPosts(posts);
                    loader.style.display = 'none';
                })
                .catch(err => console.error("Error loading posts:", err));
        }

        function renderPosts(posts) {
            const feed = document.getElementById('post-feed');
            if (posts.length === 0) {
                feed.innerHTML = '<p style="text-align:center; color:#666;">No updates found for this area.</p>';
                return;
            }

            feed.innerHTML = posts.map(p => `
                <div class="post-card">
                    <span class="author">@${escapeHtml(p.name || 'anonymous')}</span>
                    <p>${escapeHtml(p.text)}</p>
                    
                    <div class="post-interactions">
                        <button onclick="handleAction(${p.post_id}, 'up')" class="btn-vote btn-up">
                            👍 <span id="up-count-${p.post_id}">${p.upvote || 0}</span>
                        </button>
                        <button onclick="handleAction(${p.post_id}, 'down')" class="btn-vote btn-down">
                            👎 <span id="down-count-${p.post_id}">${p.downvote || 0}</span>
                        </button>
                        <button onclick="handleAction(${p.post_id}, 'report')" class="btn-report">
                            🚩 Report
                        </button>
                    </div>

                    <div class="post-meta">
                        <span>📍 ${escapeHtml(p.division)} ${p.city ? '• '+escapeHtml(p.city) : ''}</span>
                        <span>${p.created_at}</span>
                    </div>
                </div>
            `).join('');
        }

        function handleAction(postId, type) {
            fetch(POST_API + 'UpvoteOrDownvote', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ post_id: postId, type: type })
            })
            .then(res => res.json())
            .then(data => {
                if(data.success) {
                    if(type === 'report') alert('Post reported.');
                    else document.getElementById(`${type}-count-${postId}`).innerText = data.new_count;
                } else {
                    alert(data.error || 'Failed to process request.');
                }
            });
        }

        // Only attach form listener if user is logged in
        const postForm = document.getElementById('postForm');
        if (postForm) {
            postForm.onsubmit = function(e) {
                e.preventDefault();
                const data = {
                    text: document.getElementById('postText').value,
                    division: document.getElementById('postDivision').value,
                    city: document.getElementById('postCity').value
                };

                fetch(POST_API + 'addPost', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(data)
                }).then(() => {
                    postForm.reset();
                    loadPosts();
                });
            };
        }

        function escapeHtml(str) {
            if(!str) return '';
            const div = document.createElement('div');
            div.textContent = str;
            return div.innerHTML;
        }

        loadPosts();
    </script>
</body>
</html>