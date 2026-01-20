<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>CITYSTATUS | Home</title>
    <style>
        :root{--bg:#f4f7fb;--card:#ffffff;--primary:#0078d4;--muted:#666;--border:#979797;--text:#222}
        *{box-sizing:border-box}
        
        /* Main Content */
        .container { max-width: 650px; margin: 20px auto; padding: 0 15px; }

        /* Create Post Form */
        .create-post { background: var(--card); padding: 20px;border: 1px solid var(--border); border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.05); margin-bottom: 25px; }
        .create-post input[type="text"] { width: 100%; border: none; font-size: 18px; font-weight: 600; margin-bottom: 10px; outline: none; }
        .create-post textarea { width: 100%; border: none; resize: none; min-height: 80px; font-size: 15px; outline: none; font-family: inherit; }
        .post-actions { display: flex; justify-content: flex-end; border-top: 1px solid var(--border); padding-top: 10px; margin-top: 10px; }
        .btn-post { background: var(--primary); color: white; border: none; padding: 8px 24px; border-radius: 20px; font-weight: 600; cursor: pointer; }

        /* Post Feed */
        #post-feed { display: flex; flex-direction: column; gap: 15px; }
        .post-card { background: var(--card); padding: 20px; border-radius: 10px; border: 1px solid var(--border); }
        .post-card h3 { margin: 0 0 10px; font-size: 18px; }
        .post-card p { margin: 0 0 15px; color: #444; line-height: 1.5; }
        .post-meta { display: flex; gap: 20px; font-size: 13px; color: var(--muted); }
        
        /* Loading Spinner */
        #loading { text-align: center; padding: 20px; color: var(--muted); display: none; }
    </style>
</head>
<body>
    <!-- header -->
    <?php include 'views/header.php'; ?>
    <section style="margin:0;font-family:Inter,Segoe UI,Arial,sans-serif;background:var(--bg);color:var(--text);">
      <div class="container">
          <div class="create-post">
              <form action="api/posts/create.php" method="POST">
                  <input type="text" name="heading" placeholder="Post Title" required>
                  <textarea name="body" placeholder="What's happening in your city?" required></textarea>
                  <div class="post-actions">
                      <button type="submit" class="btn-post">Post</button>
                  </div>
              </form>
          </div>

          <div id="post-feed">
              <div class="post-card">
                  <h3>Welcome to CityStatus!</h3>
                  <p>This is the first post. Start sharing updates about your district to help others stay informed.</p>
                  <div class="post-meta"><span>▲ 12</span> <span>▼ 0</span></div>
              </div>
          </div>

          <div id="loading">Loading more posts...</div>
      </div>
    </section>
    <script>
        let page = 1;
        let isLoading = false;

        // Function to load posts via AJAX
        function loadMorePosts() {
            if (isLoading) return;
            isLoading = true;
            document.getElementById('loading').style.display = 'block';

            // Simulate AJAX request
            setTimeout(() => {
                const feed = document.getElementById('post-feed');
                for (let i = 0; i < 3; i++) {
                    const post = document.createElement('div');
                    post.className = 'post-card';
                    post.innerHTML = `
                        <h3>Infinite Post Title ${page}-${i}</h3>
                        <p>This post was loaded automatically as you scrolled. It contains details about city maintenance and updates.</p>
                        <div class="post-meta"><span>▲ ${Math.floor(Math.random()*100)}</span> <span>▼ ${Math.floor(Math.random()*10)}</span></div>
                    `;
                    feed.appendChild(post);
                }
                isLoading = false;
                document.getElementById('loading').style.display = 'none';
                page++;
            }, 1000); // Simulated delay
        }

        // Infinite Scroll Logic
        window.onscroll = function() {
            if ((window.innerHeight + window.scrollY) >= document.body.offsetHeight - 100) {
                loadMorePosts();
            }
        };

        // Initial Load
        loadMorePosts();
    </script>
</body>
</html>