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
let allPosts = [];
let areasData = [];

// Custom AJAX
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

// Load areas dynamically
function loadAreas() {
    ajax('GET', POST_API + 'getAreas', null, (areas) => {
        if (!Array.isArray(areas)) return;
        areasData = areas;

        // Post division
        const divisionSelect = document.getElementById('postDivision');
        const divisions = [...new Set(areas.map(a => a.division))];

        divisionSelect.innerHTML = `<option value="">Select Division</option>` +
            divisions.map(d => `<option value="${d}">${d}</option>`).join('');

        // Handle division change using your improved logic
        divisionSelect.onchange = function() {
            const selectedDivision = this.value;
            const currentCityEl = document.getElementById('postCity');

            const cities = areasData
                .filter(a => a.division === selectedDivision && a.city)
                .map(a => a.city);

            let nextEl;
            if (cities.length === 0) {
                nextEl = document.createElement('input');
                nextEl.type = 'text';
                nextEl.placeholder = 'City (Optional)';
            } else {
                nextEl = document.createElement('select');
                nextEl.innerHTML = `<option value="">Select City (Optional)</option>` +
                    cities.map(c => `<option value="${c}">${c}</option>`).join('');
            }

            nextEl.id = 'postCity';
            currentCityEl.replaceWith(nextEl);
        };

        // Search dropdown
        const searchSelect = document.getElementById('searchDivision');
        searchSelect.innerHTML = `<option value="All">All Regions</option>` +
            divisions.map(d => `<option value="${d}">${d}</option>`).join('');
    });
}

// Load posts
function loadPosts() {
    const loader = document.getElementById('loading');
    ajax('GET', POST_API + 'getPosts', null, (posts) => {
        allPosts = posts;
        filterAndRender();
        loader.style.display = 'none';
    });
}

// Filter + render posts
function filterAndRender() {
    const selectedDivision = document.getElementById('searchDivision').value;
    const feed = document.getElementById('post-feed');
    const filteredPosts = (selectedDivision === "All") ? allPosts : allPosts.filter(p => p.division === selectedDivision);

    if (filteredPosts.length === 0) {
        feed.innerHTML = `<div style="text-align:center; padding: 40px; color:#666;">
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

// Handle upvote/downvote/report
function handleAction(postId, type) {
    ajax('POST', POST_API + 'UpvoteOrDownvote', { post_id: postId, type }, (data) => {
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

// Post form submit
const postForm = document.getElementById('postForm');
if(postForm) {
    postForm.onsubmit = function(e) {
        e.preventDefault();
        const data = {
            text: document.getElementById('postText').value,
            division: document.getElementById('postDivision').value,
            city: document.getElementById('postCity').value
        };
        ajax('POST', POST_API + 'addPost', data, () => {
            postForm.reset();
            loadPosts();
        });
    };
}

// Escape HTML
function escapeHtml(str) {
    if(!str) return '';
    const div = document.createElement('div');
    div.textContent = str;
    return div.innerHTML;
}

// Logout
function handleLogout() {
    ajax('POST', '/citystatus/api/user/logout', null, () => {
        window.location.href = '/citystatus/index';
    });
}

// Initial load
loadAreas();
loadPosts();
document.getElementById('searchDivision').addEventListener('change', filterAndRender);
</script>
</body>
</html>
