<?php
session_start();
$isLoggedIn = isset($_SESSION['user_id']);
$isAdmin = (isset($_SESSION['is_admin']) && $_SESSION['is_admin'] === true);
if (!isset($_SESSION['user_id']) || !isset($_SESSION['is_admin']) || $_SESSION['is_admin'] !== true) {
    header("Location: /citystatus/login");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width,initial-scale=1" />
    <title>Admin Dashboard | CityStatus</title>
    <style>
        :root { 
            --bg: #f4f7fb; 
            --card: #ffffff; 
            --primary: #0078d4; 
            --primary-hover: #005a9e;
            --muted: #64748b; 
            --border: #e2e8f0; 
            --text: #1e293b; 
            --danger: #d93025; 
        }

        * { box-sizing: border-box; }
        body { margin: 0; font-family: 'Inter', system-ui, -apple-system, sans-serif; background: var(--bg); color: var(--text); }

        /* Sidebar & Layout */
        nav { 
            width: 260px; background: var(--card); border-right: 1px solid var(--border); 
            padding: 20px; display: flex; flex-direction: column; gap: 8px; 
            min-height: 100vh; position: fixed; top: 65px; 
        }
        nav h2 { font-size: 1rem; color: var(--muted); text-transform: uppercase; letter-spacing: 0.05em; padding-left: 12px; margin-bottom: 10px; }

        .nav-btn { 
            padding: 12px; border: none; background: none; text-align: left; 
            font-size: 15px; cursor: pointer; border-radius: 8px; color: var(--muted); 
            transition: 0.2s; text-decoration: none; font-weight: 500;
        }
        .nav-btn:hover { background: #f0f4f8; color: var(--primary); }
        .nav-btn.active { background: #e0effa; color: var(--primary); font-weight: 600; }

        main { margin-left: 260px; padding: 40px; width: calc(100% - 260px); margin-top: 65px; }
        
        /* Content Sections */
        .section { display: none; max-width: 900px; margin: 0 auto; animation: fadeIn 0.3s ease; }
        .section.active { display: block; }

        h3 { font-size: 1.5rem; margin-top: 0; margin-bottom: 24px; font-weight: 700; }

        /* Card / Item Styling */
        .card { background: var(--card); border-radius: 12px; border: 1px solid var(--border); box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05); overflow: hidden; }
        .item { 
            padding: 20px; border-bottom: 1px solid var(--border); 
            display: flex; justify-content: space-between; align-items: center; 
            background: var(--card); transition: background 0.2s;
        }
        .item:last-child { border-bottom: none; }
        .item:hover { background: #f8fafc; }

        /* Buttons & Inputs */
        .btn { padding: 8px 16px; background: var(--primary); color: white; border-radius: 6px; border: none; cursor: pointer; font-weight: 600; font-size: 13px; transition: opacity 0.2s; }
        .btn:hover { opacity: 0.9; }
        .btn-danger { background: var(--danger); }
        .btn-outline { background: transparent; border: 1px solid var(--border); color: var(--text); margin-right: 5px; }
        .btn-outline:hover { background: #f1f5f9; }

        .form-group { background: #f8fafc; padding: 24px; border-radius: 12px; border: 1px solid var(--border); margin-top: 30px; }
        .form-row { display: flex; gap: 12px; margin-top: 15px; }
        input { flex: 1; padding: 12px; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 14px; }
        input:focus { outline: none; border-color: var(--primary); box-shadow: 0 0 0 3px rgba(0, 120, 212, 0.1); }

        .small { font-size: 13px; color: var(--muted); margin-top: 4px; display: block; }
        .post-meta { display: flex; gap: 15px; font-size: 12px; color: var(--muted); font-weight: 500; }
        
        @keyframes fadeIn { from { opacity: 0; transform: translateY(5px); } to { opacity: 1; transform: translateY(0); } }
    </style>
</head>
<body>

<?php include 'views/header.php'; ?>

<div style="display: flex;">
    <nav>
        <h2>Admin Tools</h2>
        <button id="tabPosts" class="nav-btn active" onclick="show('posts')">Manage Posts</button>
        <button id="tabUsers" class="nav-btn" onclick="show('users')">User Directory</button>
        <button id="tabAreas" class="nav-btn" onclick="show('areas')">Regional Settings</button>
        <hr style="width:100%; border:0; border-top:1px solid var(--border); margin: 10px 0;">
        <a href="/citystatus/logout" class="nav-btn" style="color: var(--danger);">Logout</a>
    </nav>

    <main>
        <div id="view-posts" class="section active">
            <h3>Community Posts</h3>
            <div id="postsList" class="card"></div>
        </div>

        <div id="view-users" class="section">
            <h3>User Directory</h3>
            <div id="usersList" class="card"></div>
        </div>

        <div id="view-areas" class="section">
            <h3>Regional Settings</h3>
            <div id="areasList" class="card"></div>
            
            <div class="form-group">
                <strong style="font-size: 1.1rem;">Add New Division/City</strong>
                <p class="small">Add a new area to the registration and posting options.</p>
                <div class="form-row">
                    <input id="division" type="text" placeholder="e.g. Dhaka">
                    <input id="city" type="text" placeholder="e.g. Mirpur (Optional)">
                    <button class="btn" onclick="addArea()">Add Location</button>
                </div>
            </div>
        </div>
    </main>
</div>

<script>
// Use the modern Fetch API as requested to match the user page logic
const USER_API = '/citystatus/api/user/';
const POST_API = '/citystatus/api/post/';

function show(name) {
    document.querySelectorAll('.section').forEach(s => s.classList.remove('active'));
    document.querySelectorAll('.nav-btn').forEach(b => b.classList.remove('active'));
    
    document.getElementById('view-' + name).classList.add('active');
    document.getElementById('tab' + name.charAt(0).toUpperCase() + name.slice(1)).classList.add('active');
    
    if(name === 'posts') loadPosts();
    if(name === 'users') loadUsers();
    if(name === 'areas') loadAreas();
}

// Global fetch helper to keep code clean
async function apiCall(url, method = 'GET', data = null) {
    const options = {
        method,
        headers: { 'Accept': 'application/json' }
    };
    if (data) {
        options.headers['Content-Type'] = 'application/json';
        options.body = JSON.stringify(data);
    }
    const res = await fetch(url, options);
    return res.json();
}

async function loadUsers() {
    const list = document.getElementById('usersList');
    list.innerHTML = '<div style="padding:20px; color:var(--muted)">Loading directory...</div>';
    try {
        const users = await apiCall(USER_API + 'getUsers');
        list.innerHTML = users.map(u => `
            <div class="item">
                <div>
                    <b style="font-size:16px">${escapeHtml(u.name)}</b>
                    <span class="small">${escapeHtml(u.email)} • <span style="text-transform:capitalize">${u.user_type}</span></span>
                </div>
                <div>
                    <button class="btn btn-outline" onclick="window.location.href='/citystatus/manage-users?user_id=${u.user_id}'">Update</button>
                    <button class="btn btn-danger" onclick="deleteUser(${u.user_id})">Delete</button>
                </div>
            </div>
        `).join('') || '<div style="padding:20px">No users found.</div>';
    } catch (e) { list.innerHTML = 'Error loading users.'; }
}

async function deleteUser(id) {
    if(!confirm('Permanently delete user #' + id + '?')) return;
    const res = await apiCall(USER_API + 'deleteUser', 'POST', {user_id: id});
    if(res.success) loadUsers();
}

async function loadPosts() {
    const list = document.getElementById('postsList');
    list.innerHTML = '<div style="padding:20px; color:var(--muted)">Loading community activity...</div>';
    try {
        const posts = await apiCall(POST_API + 'getPosts');
        list.innerHTML = posts.map(p => `
            <div class="item">
                <div style="flex:1; padding-right:20px;">
                    <b>${escapeHtml(p.name || 'Anonymous')}</b>
                    <p style="margin:8px 0; line-height:1.5; color:#334155;">${escapeHtml(p.text)}</p>
                    <div class="post-meta">
                        <span>📍 ${escapeHtml(p.division)} / ${escapeHtml(p.city)}</span>
                        <span>▲ ${p.upvote}</span>
                        <span>▼ ${p.downvote}</span>
                        ${p.report_count > 0 ? `<span style="color:var(--danger)">🚩 ${p.report_count} Reports</span>` : ''}
                    </div>
                </div>
                <button class="btn btn-danger" onclick="deletePost(${p.post_id})">Delete</button>
            </div>
        `).join('') || '<div style="padding:20px">No posts found.</div>';
    } catch (e) { list.innerHTML = 'Error loading posts.'; }
}

async function deletePost(id) {
    if (!confirm('Delete this post?')) return;
    const res = await apiCall(POST_API + 'deletePost', 'POST', { post_id: id });
    if (res.success) loadPosts();
}

async function loadAreas() {
    const list = document.getElementById('areasList');
    list.innerHTML = '<div style="padding:20px; color:var(--muted)">Loading regional data...</div>';
    try {
        const areas = await apiCall(POST_API + 'getAreas');
        list.innerHTML = areas.map(a => `
            <div class="item">
                <span><strong style="color:var(--primary)">${escapeHtml(a.division)}</strong> — ${escapeHtml(a.city || 'General')}</span>
                <button class="btn btn-danger" onclick="deleteArea(${a.area_id})">Delete</button>
            </div>
        `).join('') || '<div style="padding:20px">No areas defined.</div>';
    } catch (e) { list.innerHTML = 'Error loading areas.'; }
}

async function addArea() {
    const division = document.getElementById('division').value.trim();
    const city = document.getElementById('city').value.trim();
    if (!division) return alert('Division is required');

    const res = await apiCall(POST_API + 'addArea', 'POST', { division, city });
    if (res.success) {
        document.getElementById('division').value = '';
        document.getElementById('city').value = '';
        loadAreas();
    }
}

async function deleteArea(areaId) {
    if (!confirm('Delete this area?')) return;
    const res = await apiCall(POST_API + 'deleteArea', 'POST', { area_id: areaId });
    if (res.success) loadAreas();
}

function escapeHtml(str) {
    if (!str) return '';
    const map = { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' };
    return String(str).replace(/[&<>"']/g, s => map[s]);
}

// Initial load
loadPosts();
</script>
</body>
</html>