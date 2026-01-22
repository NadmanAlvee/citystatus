<?php
session_start();
$isLoggedIn = isset($_SESSION['user_id']);
$isAdmin = (isset($_SESSION['is_admin']) && $_SESSION['is_admin'] === true);
if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] !== true) {
    header("Location: /citystatus/index");
    exit();
}
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
            --primary-hover: #005a9e;
            --muted: #666;
            --border: #e2e8f0;
            --text: #222;
            --danger: #d93025;
        }

        body { margin: 0; font-family: 'Segoe UI', system-ui, sans-serif; background: var(--bg); color: var(--text); }
        * { box-sizing: border-box; }

        .container { max-width: 1000px; margin: 20px auto; padding: 0 15px; }

        header {
            position: fixed; top: 0; left: 0; right: 0; height: 65px;
            background: #ffffff; border-bottom: 1px solid var(--border);
            display: flex; align-items: center; justify-content: space-between;
            padding: 0 5%; z-index: 1000;
        }

        .header-left a { font-weight: 800; font-size: 20px; color: var(--primary); text-decoration: none; }
        .header-right { display: flex; gap: 15px; }
        .header-right a { text-decoration: none; color: var(--muted); font-size: 14px; font-weight: 500; }
        .header-right a.logout { color: var(--danger); }

        /* Table Design */
        .table-container { background: var(--card); border-radius: 8px; border: 1px solid var(--border); overflow: hidden; margin-top: 20px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 14px; text-align: left; border-bottom: 1px solid var(--border); }
        th { background: #fafafa; font-weight: 600; font-size: 13px; color: var(--muted); text-transform: uppercase; }
        tr:last-child td { border-bottom: none; }
        tr:hover { background: #f8fafc; }

        .btn-edit { 
            background: var(--primary); color: white; border: none; padding: 6px 12px; 
            border-radius: 4px; cursor: pointer; font-size: 13px; 
        }
        .btn-edit:hover { background: var(--primary-hover); }

        /* Modal */
        .modal { display: none; position: fixed; z-index: 1001; left: 0; top: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); backdrop-filter: blur(2px); }
        .modal-content { background: #fff; margin: 5% auto; padding: 25px; border-radius: 12px; width: 90%; max-width: 450px; box-shadow: 0 20px 25px -5px rgba(0,0,0,0.1); }
        .close { color: #aaa; float: right; font-size: 24px; font-weight: bold; cursor: pointer; }

        label { display: block; margin-top: 15px; font-size: 13px; font-weight: 600; color: var(--muted); }
        input, select { margin-top: 5px; width: 100%; padding: 10px; border: 1px solid var(--border); border-radius: 6px; font-size: 14px; }
        input:disabled { background: #f1f3f4; cursor: not-allowed; }
        
        .submit-btn { background: var(--primary); color: #fff; border: none; margin-top: 20px; padding: 12px; width: 100%; border-radius: 6px; font-weight: 600; cursor: pointer; }
    </style>
</head>
<body>

<?php include 'views/header.php'; ?>

<div style="height: 85px;"></div>

<div class="container">
    <h1>User Management</h1>
    <div class="table-container">
        <table id="userTable">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>District</th>
                    <th>Role</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                </tbody>
        </table>
    </div>
</div>

<div id="editUserModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeEditModal()">&times;</span>
        <h2 style="margin-top:0">Edit User</h2>
        <form id="editUserForm">
            <input type="hidden" id="edit-user-id">
            
            <label>Name</label>
            <input type="text" id="edit-name" required>
            
            <label>District</label>
            <input type="text" id="edit-district">

            <label>Email (Cannot Change)</label>
            <input type="text" id="edit-email" disabled>

            <label>Role</label>
            <select id="edit-role">
                <option value="member">Member</option>
                <option value="admin">Admin</option>
            </select>
            
            <label>New Password</label>
            <input type="password" id="edit-password" placeholder="Leave blank to keep current">

            <button type="submit" class="submit-btn">Save Changes</button>
        </form>
    </div>
</div>

<script>
const userTableBody = document.querySelector('#userTable tbody');

async function loadUsers() {
    try {
        const res = await fetch('/citystatus/api/user/getUsers');
        const users = await res.json();
        userTableBody.innerHTML = '';
        
        users.forEach(user => {
            const row = document.createElement('tr');
            row.innerHTML = `
                <td>${user.user_id}</td>
                <td>${user.name}</td>
                <td>${user.email}</td>
                <td>${user.district || '—'}</td>
                <td><span class="badge">${user.user_type}</span></td>
                <td><button class="btn-edit" onclick="openEditModal(${user.user_id})">Edit</button></td>
            `;
            userTableBody.appendChild(row);
        });
    } catch (err) {
        console.error('Failed to load users', err);
    }
}

async function openEditModal(userId) {
    const res = await fetch('/citystatus/api/user/getUsers');
    const users = await res.json();
    const user = users.find(u => u.user_id == userId);
    
    if (!user) return alert('User not found');

    document.getElementById('edit-user-id').value = user.user_id;
    document.getElementById('edit-name').value = user.name;
    document.getElementById('edit-email').value = user.email;
    document.getElementById('edit-district').value = user.district || '';
    document.getElementById('edit-role').value = user.user_type;
    document.getElementById('edit-password').value = '';

    document.getElementById('editUserModal').style.display = 'block';
}

document.getElementById('editUserForm').onsubmit = async function(e) {
    e.preventDefault();

    // FIXED: Build the object by adding properties, not overwriting the whole variable
    const payload = {
        user_id: document.getElementById('edit-user-id').value,
        name: document.getElementById('edit-name').value.trim(),
        district: document.getElementById('edit-district').value.trim(),
        user_type: document.getElementById('edit-role').value
    };

    const password = document.getElementById('edit-password').value;
    if (password) {
        payload.password = password;
    }

    try {
        const res = await fetch('/citystatus/api/user/update', {
            method: 'POST',
            headers: {'Content-Type': 'application/json'},
            body: JSON.stringify(payload)
        });
        
        const data = await res.json();
        if (data.success) {
            alert('User updated successfully');
            closeEditModal();
            loadUsers();
        } else {
            alert('Error: ' + (data.error || 'Update failed'));
        }
    } catch (err) {
        alert('Server connection error');
    }
};

function closeEditModal() { 
    document.getElementById('editUserModal').style.display = 'none'; 
}

// Close modal if user clicks outside of it
window.onclick = function(event) {
    const modal = document.getElementById('editUserModal');
    if (event.target == modal) closeEditModal();
}

loadUsers();
</script>

</body>
</html>