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
    <title>CITYSTATUS | Home Page</title>
    <style>
        :root {
            --bg: #f4f7fb;
            --card: #ffffff;
            --primary: #0078d4;
            --muted: #666;
            --border: #e2e8f0;
            --text: #222;
        }
        body { margin:0; font-family: 'Segoe UI', Arial, sans-serif; background:var(--bg); color:var(--text); }
        * { box-sizing:border-box }
        
        .container { max-width: 650px; margin: 20px auto; padding: 0 15px; }

        /* Header Search Styling */
        .search-container {
            display: flex;
            align-items: center;
            background: #f1f3f4;
            padding: 4px 12px;
            border-radius: 20px;
            transition: all 0.2s ease;
            border: 1px solid transparent;
        }
        .search-container:focus-within {
            background: #fff;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(0, 120, 212, 0.1);
        }
        .search-container select {
            background: transparent;
            border: none;
            outline: none;
            font-size: 14px;
            color: var(--text);
            padding: 4px;
            cursor: pointer;
            font-weight: 500;
        }

        /* Create Post Box */
        .create-post { background: var(--card); padding: 20px; border: 1px solid var(--border); border-radius: 12px; box-shadow: 0 2px 10px rgba(0,0,0,0.05); margin-bottom: 25px; }
        .create-post textarea { width: 100%; border: none; resize: none; min-height: 80px; font-size: 16px; outline: none; font-family: inherit; margin-bottom: 10px; }
        .post-actions { display: flex; justify-content: flex-end; border-top: 1px solid var(--border); padding-top: 12px; }
        
        .post-selectors { display: flex; gap: 10px; margin-bottom: 10px; }
        .post-selectors select, .post-selectors input { padding: 8px; border: 1px solid var(--border); border-radius: 6px; font-size: 13px; outline: none; }
        .post-selectors select:focus, .post-selectors input:focus { border-color: var(--primary); }

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
        #loading { text-align: center; padding: 40px; color: var(--muted); }
    </style>
</head>
<body>

    <header style="position: fixed; top: 0; left: 0; right: 0; height: 65px; background: #ffffff; border-bottom: 1px solid #e6e9ee; display: flex; align-items: center; justify-content: space-between; padding: 0 5%; z-index: 1000;">
        <div class="header-left" style="display: flex; align-items: center; gap: 20px; flex: 1;">
            <a href="/citystatus/index" style="font-weight: 800; font-size: 20px; color: #0078d4; text-decoration: none; letter-spacing: -0.5px;">
                CITYSTATUS
            </a>
            
            <div class="search-container">
                <span style="font-size: 14px; margin-right: 5px;">🔍</span>
                <select id="searchDivision">
                    <option value="All">All Regions</option>
                    <option value="Dhaka">Dhaka</option>
                    <option value="Chittagong">Chittagong</option>
                    <!-- <option value="Rajshahi">Rajshahi</option>
                    <option value="Sylhet">Sylhet</option>
                    <option value="Khulna">Khulna</option>
                    <option value="Barisal">Barisal</option>
                    <option value="Rangpur">Rangpur</option>
                    <option value="Mymensingh">Mymensingh</option> -->
                </select>
            </div>
        </div>

        <div class="header-right" style="display: flex; align-items: center; gap: 15px;">
            <a href="<?php echo $isLoggedIn ? '/citystatus/user-dashboard' : '/citystatus/login'; ?>" style="text-decoration: none; color: #666; font-size: 14px; font-weight: 500;">
                Profile
            </a>

            <?php if ($isAdmin): ?>
            <a href="/citystatus/admin-dashboard" style="text-decoration: none; color: #666; font-size: 14px; font-weight: 500;">
                Admin
            </a>
            <?php endif; ?>

            <?php if ($isLoggedIn): ?>
                <button onclick="handleLogout()" style="padding: 7px 15px; color: #d93025; border: none; border-radius: 6px; font-size: 14px; font-weight: 500; cursor: pointer; background: #fff2f2;">
                    Logout
                </button>
            <?php else: ?>
                <a href="/citystatus/login" style="padding: 7px 15px; background: #eef6ff; color: #0078d4; border-radius: 6px; text-decoration: none; font-size: 14px; font-weight: 500;">
                    Login
                </a>
            <?php endif; ?>
        </div>
    </header>
    
    <div style="height: 85px;"></div>

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
                          <!-- <option value="Rajshahi">Rajshahi</option>
                          <option value="Sylhet">Sylhet</option>
                          <option value="Khulna">Khulna</option>
                          <option value="Barisal">Barisal</option>
                          <option value="Rangpur">Rangpur</option>
                          <option value="Mymensingh">Mymensingh</option> -->
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
        let allPosts = []; // Global variable to store fetched posts

        // Initial Load
        function loadPosts() {
            const loader = document.getElementById('loading');
            
            fetch(POST_API + 'getPosts')
                .then(res => res.json())
                .then(posts => {
                    allPosts = posts; // Save to memory
                    filterAndRender(); // Display based on current filter
                    loader.style.display = 'none';
                })
                .catch(err => {
                    console.error("Error loading posts:", err);
                    loader.innerText = "Error loading feed.";
                });
        }

        // Logic to filter and show posts
        function filterAndRender() {
            const selectedDivision = document.getElementById('searchDivision').value;
            const feed = document.getElementById('post-feed');
            
            // Filter the array
            const filteredPosts = (selectedDivision === "All") 
                ? allPosts 
                : allPosts.filter(p => p.division === selectedDivision);

            // Render
            if (filteredPosts.length === 0) {
                feed.innerHTML = `
                    <div style="text-align:center; padding: 40px; color:#666;">
                        <p style="font-size: 24px;">📍</p>
                        <p>No updates found for ${selectedDivision}.</p>
                    </div>`;
                return;
            }

            feed.innerHTML = filteredPosts.map(p => `
                <div class="post-card">
                    <span class="author">@${escapeHtml(p.name || 'anonymous')}</span>
                    <p>${escapeHtml(p.text)}</p>
                    
                    <div class="post-interactions">
                        <button onclick="handleAction(${p.post_id}, 'up')" class="btn-vote btn-up">
                            👍 <span id="up-count-${p.post_id}">${p.upvote}</span>
                        </button>
                        <button onclick="handleAction(${p.post_id}, 'down')" class="btn-vote btn-down">
                            👎 <span id="down-count-${p.post_id}">${p.downvote}</span>
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

        // Listen for filter changes
        document.getElementById('searchDivision').addEventListener('change', filterAndRender);

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
                    else {
                        const countEl = document.getElementById(`${type}-count-${postId}`);
                        if(countEl) countEl.innerText = data.new_count;
                    }
                } else {
                    alert(data.error || 'Failed to process request.');
                }
            });
        }

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
                    loadPosts(); // Reload all and re-render
                });
            };
        }

        function escapeHtml(str) {
            if(!str) return '';
            const div = document.createElement('div');
            div.textContent = str;
            return div.innerHTML;
        }

        function handleLogout() {
            fetch('/citystatus/api/user/logout', { method: 'POST' })
            .then(() => window.location.href = '/citystatus/index');
        }

        loadPosts();
    </script>
</body>
</html>