<?php
// Simple admin manager view - open at /citystatus/admin-manager
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8" />
<meta name="viewport" content="width=device-width,initial-scale=1" />
<title>Admin Manager</title>
<style>
/* simple CSS */
body{font-family:Arial,Helvetica,sans-serif;margin:0;background:#f3f6fb}
.header{background:#2b6cb0;color:#fff;padding:12px 16px}
.container{display:flex;gap:16px;padding:16px}
.sidebar{width:200px;background:#fff;padding:12px;border-radius:6px;box-shadow:0 1px 3px rgba(0,0,0,.08)}
.content{flex:1;background:#fff;padding:12px;border-radius:6px;box-shadow:0 1px 3px rgba(0,0,0,.08)}
.tab{display:block;padding:8px;margin-bottom:6px;cursor:pointer;border-radius:4px;background:#eef6ff}
.tab.active{background:#2b6cb0;color:#fff}
.item{border:1px solid #e6eef8;padding:8px;margin:8px 0;border-radius:6px;background:#fff}
.btn{display:inline-block;padding:6px 10px;background:#2b6cb0;color:#fff;border-radius:4px;text-decoration:none;border:none;cursor:pointer}
.form-row{display:flex;gap:8px;align-items:center;margin:8px 0}
input[type="text"]{flex:1;padding:6px;border:1px solid #ccc;border-radius:4px}
.small{font-size:13px;color:#666}
</style>
</head>
<body>
<div class="header"><strong>Admin Manager</strong></div>
<div class="container">
  <div class="sidebar">
    <div id="tabPosts" class="tab active" onclick="show('posts')">Manage Posts</div>
    <div id="tabUsers" class="tab" onclick="show('users')">Manage Users</div>
    <div id="tabAreas" class="tab" onclick="show('areas')">Cities / Divisions</div>
    <div style="margin-top:12px"><a class="small" href="/citystatus/views/Dashboards/AdminDashboard.php">Open old AdminDashboard</a></div>
  </div>

  <div class="content">
    <div id="view-posts">
      <h3>Posts</h3>
      <div id="postsList">Loading posts...</div>
    </div>

    <div id="view-users" style="display:none">
      <h3>Users</h3>
      <div id="usersList">Loading users...</div>
    </div>

    <div id="view-areas" style="display:none">
      <h3>Cities / Divisions</h3>
      <div id="areasList">Loading areas...</div>
      <h4 style="margin-top:12px">Add Area</h4>
      <div class="form-row">
        <input id="division" type="text" placeholder="Division">
        <input id="city" type="text" placeholder="City (optional)">
        <button class="btn" onclick="addArea()">Add</button>
      </div>
      <div id="areaMsg" class="small"></div>
    </div>
  </div>
</div>

<script>
const API = '/citystatus/api/manage.php';

function show(name){
  document.getElementById('view-posts').style.display = name==='posts' ? '' : 'none';
  document.getElementById('view-users').style.display = name==='users' ? '' : 'none';
  document.getElementById('view-areas').style.display = name==='areas' ? '' : 'none';
  document.querySelectorAll('.tab').forEach(t=>t.classList.remove('active'));
  document.getElementById('tab'+capitalize(name)).classList.add('active');
  if(name==='posts') loadPosts();
  if(name==='users') loadUsers();
  if(name==='areas') loadAreas();
}

function capitalize(s){ return s.charAt(0).toUpperCase()+s.slice(1); }

/* AJAX helpers */
function ajax(method, url, data, cb){
  const xhr = new XMLHttpRequest();
  xhr.open(method, url, true);
  xhr.setRequestHeader('Accept','application/json');
  if(method==='POST') xhr.setRequestHeader('Content-Type','application/json');
  xhr.onload = ()=> cb(xhr.status, xhr.responseText);
  xhr.onerror = ()=> cb(0, null);
  xhr.send(data ? JSON.stringify(data) : null);
}

/* Posts */
function loadPosts(){
  document.getElementById('postsList').innerText = 'Loading posts...';
  ajax('GET', API+'?action=getPosts', null, (s,r)=>{
    if(s!==200){ document.getElementById('postsList').innerText = 'Error loading posts'; return; }
    const posts = JSON.parse(r);
    if(posts.length===0){ document.getElementById('postsList').innerHTML = '<div class="small">No posts</div>'; return; }
    let html = '';
    posts.forEach(p=>{
      html += `<div class="item">
        <div><strong>${escapeHtml(p.name||'User')}</strong> <span class="small">(${p.email||''})</span></div>
        <div style="margin:6px 0">${escapeHtml(p.text || '')}</div>
        <div class="small">Division: ${escapeHtml(p.division||'')} | City: ${escapeHtml(p.city||'')}</div>
        <div style="margin-top:6px"><button class="btn" onclick="deletePost(${p.post_id})">Delete</button></div>
      </div>`;
    });
    document.getElementById('postsList').innerHTML = html;
  });
}

function deletePost(id){
  if(!confirm('Delete post #'+id+'?')) return;
  ajax('POST', API+'?action=deletePost', {post_id:id}, (s,r)=>{
    if(s===200) loadPosts(); else alert('Failed to delete');
  });
}

/* Users */
function loadUsers(){
  document.getElementById('usersList').innerText = 'Loading users...';
  ajax('GET', API+'?action=getUsers', null, (s,r)=>{
    if(s!==200){ document.getElementById('usersList').innerText = 'Error'; return; }
    const users = JSON.parse(r);
    if(users.length===0){ document.getElementById('usersList').innerHTML = '<div class="small">No users</div>'; return; }
    let html = '';
    users.forEach(u=>{
      html += `<div class="item">
        <div><strong>${escapeHtml(u.name)}</strong> <span class="small">(${escapeHtml(u.email)})</span></div>
        <div class="small">Type: ${escapeHtml(u.user_type||'')}</div>
        <div style="margin-top:6px"><button class="btn" onclick="deleteUser(${u.user_id})">Delete</button></div>
      </div>`;
    });
    document.getElementById('usersList').innerHTML = html;
  });
}

function deleteUser(id){
  if(!confirm('Delete user #'+id+'?')) return;
  ajax('POST', API+'?action=deleteUser', {user_id:id}, (s,r)=>{
    if(s===200) loadUsers(); else alert('Failed to delete');
  });
}

/* Areas */
function loadAreas(){
  document.getElementById('areasList').innerText = 'Loading areas...';
  ajax('GET', API+'?action=getAreas', null, (s,r)=>{
    if(s!==200){ document.getElementById('areasList').innerText = 'Error'; return; }
    const areas = JSON.parse(r);
    if(areas.length===0){ document.getElementById('areasList').innerHTML = '<div class="small">No areas</div>'; return; }
    let html = '';
    areas.forEach(a=>{
      html += `<div class="item">${escapeHtml(a.division)} ${a.city ? ' / '+escapeHtml(a.city) : ''}</div>`;
    });
    document.getElementById('areasList').innerHTML = html;
  });
}

function addArea(){
  const division = document.getElementById('division').value.trim();
  const city = document.getElementById('city').value.trim();
  if(!division){ document.getElementById('areaMsg').innerText = 'Division required'; return; }
  document.getElementById('areaMsg').innerText = '';
  ajax('POST', API+'?action=addArea', {division, city}, (s,r)=>{
    if(s===200){ document.getElementById('division').value=''; document.getElementById('city').value=''; loadAreas(); }
    else document.getElementById('areaMsg').innerText = 'Failed to add';
  });
}

/* small helper to avoid XSS in innerHTML */
function escapeHtml(str){
  if(!str) return '';
  return String(str).replace(/[&<>"']/g, s => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[s]));
}

/* initial load */
loadPosts();
</script>
</body>
</html>
