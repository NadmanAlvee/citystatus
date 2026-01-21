<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8" />
<meta name="viewport" content="width=device-width,initial-scale=1" />
<title>Admin Manager | CityStatus</title>
<style>
    :root { --primary: #2b6cb0; --bg: #f3f6fb; --white: #ffffff; --text: #2d3748; --border: #e2e8f0; --danger: #e53e3e; }
    body { font-family: 'Segoe UI', sans-serif; margin: 0; background: var(--bg); color: var(--text); }
    .header { background: var(--primary); color: var(--white); padding: 1rem 2rem; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
    .container { display: flex; gap: 24px; padding: 24px; max-width: 1200px; margin: 0 auto; }
    
    .sidebar { width: 220px; }
    .tab { display: block; padding: 12px 16px; margin-bottom: 8px; cursor: pointer; border-radius: 8px; background: var(--white); border: 1px solid var(--border); transition: 0.2s; }
    .tab.active { background: var(--primary); color: var(--white); border-color: var(--primary); font-weight: bold; }
    
    .content { flex: 1; background: var(--white); padding: 24px; border-radius: 12px; box-shadow: 0 4px 6px rgba(0,0,0,0.05); }
    .item { border: 1px solid var(--border); padding: 16px; margin-bottom: 12px; border-radius: 8px; display: flex; justify-content: space-between; align-items: center; }
    
    .btn { padding: 8px 14px; background: var(--primary); color: var(--white); border-radius: 6px; border: none; cursor: pointer; }
    .btn-danger { background: var(--danger); }
    
    .form-group { background: #f8fafc; padding: 16px; border-radius: 8px; border: 1px solid var(--border); margin-top: 20px; }
    .form-row { display: flex; gap: 10px; margin-top: 10px; }
    input { flex: 1; padding: 10px; border: 1px solid var(--border); border-radius: 6px; }
    .small { font-size: 13px; color: #718096; }
</style>
</head>
<body>

<!-- header -->
<?php include 'views/header.php'; ?>

<div class="container">
    <div class="sidebar">
        <div id="tabPosts" class="tab active" onclick="show('posts')">Manage Posts</div>
        <div id="tabUsers" class="tab" onclick="show('users')">Manage Users</div>
        <div id="tabAreas" class="tab" onclick="show('areas')">Manage Areas</div>
    </div>

    <div class="content">
        <div id="view-posts">
            <h3>Community Posts</h3>
            <div id="postsList"></div>
        </div>

        <div id="view-users" style="display:none">
            <h3>User Directory</h3>
            <div id="usersList"></div>
        </div>

        <div id="view-areas" style="display:none">
            <h3>Regional Settings</h3>
            <div id="areasList"></div>
            <div class="form-group">
                <strong>Add New Division/City</strong>
                <div class="form-row">
                    <input id="division" type="text" placeholder="Division">
                    <input id="city" type="text" placeholder="City">
                    <button class="btn" onclick="addArea()">Add Location</button>
                </div>
                <div id="areaMsg" class="small"></div>
            </div>
        </div>
    </div>
</div>

<script>
const USER_API = '/citystatus/api/user/';
const POST_API = '/citystatus/api/post/';

function show(name) {
    ['posts', 'users', 'areas'].forEach(v => {
        document.getElementById('view-' + v).style.display = (v === name) ? 'block' : 'none';
        document.getElementById('tab' + capitalize(v)).classList.toggle('active', v === name);
    });
    if(name === 'posts') loadPosts();
    if(name === 'users') loadUsers();
    if(name === 'areas') loadAreas();
}

function capitalize(s) { return s.charAt(0).toUpperCase() + s.slice(1); }

/* Unified XHR Helper */
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

/* User Functions */
function loadUsers() {
    const list = document.getElementById('usersList');
    list.innerHTML = 'Loading...';
    ajax('GET', USER_API + 'getUsers', null, function(data) {
        list.innerHTML = data.map(u => `
            <div class="item">
                <div>
                    <b>${escapeHtml(u.name)}</b><br>
                    <span class="small">${escapeHtml(u.email)} | ${u.user_type}</span>
                </div>
                <button class="btn btn-danger" onclick="deleteUser(${u.user_id})">Delete</button>
            </div>
        `).join('') || 'No users found.';
    });
}

function deleteUser(id) {
    if (!confirm('Delete user #' + id + '?')) return;
    ajax('POST', USER_API + 'deleteUser', {user_id: id}, function(res) {
        if (res.success) loadUsers();
    });
}

/* Post Functions */
function loadPosts() {
    const list = document.getElementById('postsList');
    list.innerHTML = 'Loading...';
    ajax('GET', POST_API + 'getPosts', null, function(data) {
        list.innerHTML = data.map(p => `
            <div class="item">
                <div style="flex:1">
                    <b>${escapeHtml(p.name || 'Anonymous')}</b>
                    <p style="margin:5px 0">${escapeHtml(p.text)}</p>
                    <span class="small">${p.division} / ${p.city}</span>
                </div>
                <button class="btn btn-danger" onclick="deletePost(${p.post_id})">Delete</button>
            </div>
        `).join('') || 'No posts found.';
    });
}

function deletePost(id) {
    if (!confirm('Delete post #' + id + '?')) return;
    ajax('POST', POST_API + 'deletePost', {post_id: id}, function(res) {
        if (res.success) loadPosts();
    });
}

/* Area Functions */
function loadAreas() {
    const list = document.getElementById('areasList');
    list.innerHTML = 'Loading...';
    ajax('GET', POST_API + 'getAreas', null, function(data) {
        list.innerHTML = data.map(a => `
            <div class="item">
                <span><strong>${escapeHtml(a.division)}</strong> — ${escapeHtml(a.city || 'Whole Division')}</span>
            </div>
        `).join('') || 'No areas found.';
    });
}

function addArea() {
    const division = document.getElementById('division').value.trim();
    const city = document.getElementById('city').value.trim();
    if (!division) return alert('Division is required');

    ajax('POST', POST_API + 'addArea', {division: division, city: city}, function(res) {
        if (res.success) {
            document.getElementById('division').value = '';
            document.getElementById('city').value = '';
            loadAreas();
        }
    });
}

function escapeHtml(str) {
    if (!str) return '';
    const map = { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' };
    return String(str).replace(/[&<>"']/g, s => map[s]);
}

loadPosts();
</script>
</body>
</html>