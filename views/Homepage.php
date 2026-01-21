<?php
session_start();
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

        .create-post { background: var(--card); padding: 20px; border: 1px solid var(--border); border-radius: 12px; box-shadow: 0 2px 10px rgba(0,0,0,0.05); margin-bottom: 25px; }
        .create-post textarea { width: 100%; border: none; resize: none; min-height: 80px; font-size: 16px; outline: none; font-family: inherit; margin-bottom: 10px; }
        .post-actions { display: flex; justify-content: flex-end; border-top: 1px solid var(--border); padding-top: 12px; }
        
        .post-selectors { display: flex; gap: 10px; margin-bottom: 10px; }
        .post-selectors select { padding: 5px; border: 1px solid var(--border); border-radius: 5px; font-size: 13px; color: var(--muted); }

        .btn-post { background: var(--primary); color: white; border: none; padding: 8px 24px; border-radius: 20px; font-weight: 600; cursor: pointer; }

        #post-feed { display: flex; flex-direction: column; gap: 15px; }
        .post-card { background: var(--card); padding: 20px; border-radius: 12px; border: 1px solid var(--border); box-shadow: 0 2px 5px rgba(0,0,0,0.02); }
        .post-card .author { font-weight: bold; font-size: 14px; margin-bottom: 5px; display: block; }
        .post-card p { margin: 10px 0; color: #333; line-height: 1.5; white-space: pre-wrap; }
        .post-meta { display: flex; justify-content: space-between; font-size: 12px; color: var(--muted); border-top: 1px solid #f0f0f0; padding-top: 10px; }
        
        #loading { text-align: center; padding: 20px; color: var(--muted); font-size: 14px; }
    </style>
</head>
<body>

    <?php include 'views/header.php'; ?>

    
    <div class="container">
        <?php if(isset($_SESSION['user_id'])): ?>
          <div class="create-post">
              <form id="postForm">
                  <textarea id="postText" placeholder="What's happening in your city?" required></textarea>
                  
                  <div class="post-selectors">
                      <select id="postDivision" required>
                          <option value="">Select Division</option>
                          <option value="Dhaka">Dhaka</option>
                          <option value="Chittagong">Chittagong</option>
                      </select>
                      <input type="text" id="postCity" placeholder="City (Optional)" style="font-size:13px; padding:5px; border:1px solid var(--border); border-radius:5px;">
                  </div>

                  <div class="post-actions">
                      <button type="submit" class="btn-post">Post</button>
                  </div>
              </form>
          </div>
        <?php endif; ?>

        <div id="post-feed">
            </div>

        <div id="loading">Gathering latest updates...</div>
    </div>

    <script>
        const POST_API = '/citystatus/api/post/';

        function loadPosts() {
            const feed = document.getElementById('post-feed');
            const loader = document.getElementById('loading');
            
            const xhr = new XMLHttpRequest();
            xhr.open('GET', POST_API + 'getPosts', true);
            
            xhr.onload = function() {
                if (xhr.status === 200) {
                    const posts = JSON.parse(xhr.responseText);
                    renderPosts(posts);
                    loader.style.display = 'none';
                }
            };
            xhr.send();
        }

        function renderPosts(posts) {
            const feed = document.getElementById('post-feed');
            if (posts.length === 0) {
                feed.innerHTML = '<p style="text-align:center; color:#666;">No posts yet. Be the first to update!</p>';
                return;
            }

            feed.innerHTML = posts.map(p => `
                <div class="post-card">
                    <span class="author">${escapeHtml(p.name || 'Anonymous User')}</span>
                    <p>${escapeHtml(p.text)}</p>
                    <div class="post-meta">
                        <span>📍 ${escapeHtml(p.division)} ${p.city ? '• '+escapeHtml(p.city) : ''}</span>
                        <span>${p.created_at}</span>
                    </div>
                </div>
            `).join('');
        }

        document.getElementById('postForm').onsubmit = function(e) {
            e.preventDefault();
            
            const data = {
                text: document.getElementById('postText').value,
                division: document.getElementById('postDivision').value,
                city: document.getElementById('postCity').value
            };

            const xhr = new XMLHttpRequest();
            xhr.open('POST', '/citystatus/api/post/addPost', true);
            xhr.setRequestHeader('Content-Type', 'application/json');
            
            xhr.onload = function() {
                if (xhr.status === 200) {
                    document.getElementById('postForm').reset();
                    loadPosts();
                } else {
                    alert('Error posting update. Are you logged in?');
                }
            };
            xhr.send(JSON.stringify(data));
        };

        function escapeHtml(str) {
            if(!str) return '';
            const map = {'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"' Joyce":'&#39;'};
            return String(str).replace(/[&<>"']/g, s => map[s]);
        }

        loadPosts();
    </script>
</body>
</html>
