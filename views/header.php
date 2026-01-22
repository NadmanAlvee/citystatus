<header style="position: fixed; top: 0; left: 0; right: 0; height: 65px; background: #ffffff; border-bottom: 1px solid #e6e9ee; display: flex; align-items: center; justify-content: space-between; padding: 0 5%; z-index: 1000; font-family: Inter, Segoe UI, Arial, sans-serif;">
    
    <div class="header-left" style="display: flex; align-items: center; gap: 20px; flex: 1;">
        <a href="/citystatus/index" class="logo" style="font-weight: 800; font-size: 20px; color: #0078d4; text-decoration: none; letter-spacing: -0.5px;">
            CITYSTATUS
        </a>
    </div>

    <div class="header-right" style="display: flex; align-items: center; gap: 15px;">
        <a href="<?php echo $isLoggedIn ? '/citystatus/user-dashboard' : '/citystatus/login'; ?>" class="nav-link" style="text-decoration: none; color: #666; font-size: 14px; font-weight: 500;">
            User Profile
        </a>

        <?php if ($isAdmin): ?>
        <a href="/citystatus/admin-dashboard" class="nav-link" style="text-decoration: none; color: #666; font-size: 14px; font-weight: 500;">
            Admin Dashboard
        </a>
        <?php endif; ?>

        <?php if ($isLoggedIn): ?>
            <button onclick="handleLogout()" class="btn-logout" 
                style="padding: 7px 15px; color: #d93025; border: none; border-radius: 6px; text-decoration: none; font-size: 14px; font-weight: 500; cursor: pointer; background: #fff2f2;"
                onmouseover="this.style.background='#ffc8c8';" onmouseout="this.style.background='#fff2f2';">
                Logout
            </button>
        <?php else: ?>
            <a href="/citystatus/login" class="btn-logout" 
                style="padding: 7px 15px; background: #eef6ff; color: #0078d4; border-radius: 6px; text-decoration: none; font-size: 14px; font-weight: 500;">
                Login
            </a>
        <?php endif; ?>
    </div>
</header>
<div style="height: 65px; width: 100%; visibility: hidden; pointer-events: none;"></div>

<script>
  function handleLogout() {
    const xhr = new XMLHttpRequest();
    xhr.open('POST', '/citystatus/api/user/logout', true);
    xhr.setRequestHeader('Content-Type', 'application/json');
    xhr.onload = function() {
        if (xhr.status === 200) {
          window.location.href = '/citystatus/index'; 
        } else {
          console.error('Logout failed');
        }
      };
    xhr.send();
  }
</script>
