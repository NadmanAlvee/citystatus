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
<title>CITYSTATUS | Admin Dashboard</title>
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

  .container { max-width: 900px; margin: 20px auto; padding: 0 15px; }

  /* Header Styling */
  header {
      position: fixed;
      top: 0;
      left: 0;
      right: 0;
      height: 65px;
      background: #ffffff;
      border-bottom: 1px solid #e6e9ee;
      display: flex;
      align-items: center;
      justify-content: space-between;
      padding: 0 5%;
      z-index: 1000;
  }
  .header-left a { font-weight: 800; font-size: 20px; color: #0078d4; text-decoration: none; }
  .header-right { display:flex; gap:15px; }
  .header-right a { text-decoration:none; color:#666; font-size:14px; font-weight:500; }
  .header-right a.logout { color:#d93025; }

  /* User Table Styling */
  table { width: 100%; border-collapse: collapse; margin-top: 20px; }
  th, td { padding: 12px; text-align: left; border-bottom: 1px solid var(--border); }
  th { background: var(--card); color: var(--text); }
  tr:hover { background: #f1f3f4; cursor: pointer; }

  /* Modal Styling */
  .modal { display: none; position: fixed; z-index: 1001; left:0; top:0; width:100%; height:100%; overflow:auto; background-color: rgba(0,0,0,0.4); padding-top:60px; }
  .modal-content { background-color:#fff; margin:5% auto; padding:20px; border:1px solid var(--border); border-radius:8px; width:80%; max-width:400px; }
  .close { color:#aaa; float:right; font-size:28px; font-weight:bold; cursor:pointer; }
  .close:hover { color:black; }

  /* Alerts */
  .alert { padding:15px; margin-bottom:20px; border:1px solid transparent; border-radius:4px; }
  .alert-success { color:#155724; background-color:#d4edda; border-color:#c3e6cb; }
  .alert-error { color:#721c24; background-color:#f8d7da; border-color:#f5c6cb; }

  input, select, button { margin-top: 10px; width: 100%; padding:8px; border:1px solid var(--border); border-radius:6px; }
  button { background: var(--primary); color: #fff; cursor: pointer; }
</style>
</head>
<body>

<header>
    <div class="header-left">
        <a href="/citystatus/index">CITYSTATUS</a>
    </div>
    <div class="header-right">
        <a href="/citystatus/user-dashboard">Profile</a>
        <a href="/citystatus/logout" class="logout">Logout</a>
    </div>
</header>

<div style="height: 85px;"></div>

<div class="container">
    <h1>Admin Dashboard</h1>

    <table id="userTable">
        <thead>
            <tr>
                <th>User ID</th>
                <th>Name</th>
                <th>Email</th>
                <th>District</th>
                <th>Role</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <!-- User rows populated via JS -->
        </tbody>
    </table>
</div>

<!-- Edit User Modal -->
<div id="editUserModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeEditModal()">&times;</span>
        <h2>Edit User</h2>
        <form id="editUserForm">
            <input type="hidden" id="edit-user-id">
            <label for="edit-name">Name</label>
            <input type="text" id="edit-name" required>
            
            <label for="edit-district">District</label>
            <input type="text" id="edit-district" required>
            
            <label for="edit-password">New Password</label>
            <input type="password" id="edit-password" placeholder="Leave blank to keep current">

            <button type="submit">Update User</button>
        </form>
    </div>
</div>

<script>
const userTable = document.getElementById('userTable').getElementsByTagName('tbody')[0];

// Load users
function loadUsers() {
    fetch('/citystatus/api/user/getUsers')
        .then(res => res.json())
        .then(users => {
            userTable.innerHTML = '';
            users.forEach(user => {
                const row = userTable.insertRow();
                row.innerHTML = `
                    <td>${user.user_id}</td>
                    <td>${user.name}</td>
                    <td>${user.email}</td>
                    <td>${user.district || ''}</td>
                    <td>${user.user_type}</td>
                    <td><button onclick="openEditModal(${user.user_id})">Edit</button></td>
                `;
            });
        })
        .catch(err => console.error('Failed to load users', err));
}

// Open edit modal
function openEditModal(userId) {
    fetch('/citystatus/api/user/getUsers')
        .then(res => res.json())
        .then(users => {
            const user = users.find(u => u.user_id == userId);
            if (!user) return alert('User not found');

            document.getElementById('edit-user-id').value = user.user_id;
            document.getElementById('edit-name').value = user.name;
            document.getElementById('edit-district').value = user.district || '';
            document.getElementById('edit-password').value = '';

            document.getElementById('editUserModal').style.display = 'block';
        });
}

// Update user
document.getElementById('editUserForm').onsubmit = function(e) {
    e.preventDefault();

    const userId = document.getElementById('edit-user-id').value;
    const name = document.getElementById('edit-name').value.trim();
    const district = document.getElementById('edit-district').value.trim();
    const password = document.getElementById('edit-password').value;

    if (!name || !district) return alert('Name and District are required');

    const payload = { user_id: userId, name, district };
    if (password) payload.password = password; // only update password if entered

    fetch('/citystatus/api/user/update', {
        method: 'POST',
        headers: {'Content-Type': 'application/json'},
        body: JSON.stringify(payload)
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            alert('User updated successfully');
            closeEditModal();
            loadUsers();
        } else {
            alert('Error: ' + (data.error || 'Failed to update user'));
        }
    })
    .catch(err => { console.error(err); alert('Update failed'); });
};

// Close modal
function closeEditModal() { document.getElementById('editUserModal').style.display = 'none'; }

// Initial load
loadUsers();
</script>

</body>
</html>
