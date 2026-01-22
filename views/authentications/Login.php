<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login</title>
    <style>
        :root { --bg: #f4f7fb; --card: #ffffff; --primary: #0078d4; --muted: #666; }
        * { box-sizing: border-box; }
        body { margin: 0; font-family: Inter, Segoe UI, Arial, sans-serif; background: var(--bg); display: flex; align-items: center; justify-content: center; min-height: 100vh; }
        .card { width: 360px; background: var(--card); padding: 24px; border-radius: 10px; box-shadow: 0 8px 30px rgba(16, 24, 40, 0.08); }
        h2 { margin: 0 0 18px; font-weight: 600; color: #222; text-align: center; }
        .input-group { margin-bottom: 12px; }
        label { display: block; margin-bottom: 6px; font-size: 13px; color: #444; }
        input[type="email"], input[type="password"] { width: 100%; padding: 10px 12px; border: 1px solid #979797; border-radius: 8px; font-size: 14px; background: #fff; }
        .actions { display: flex; justify-content: space-between; align-items: center; margin: 10px 0 16px; }
        .remember { display: flex; align-items: center; gap: 8px; font-size: 13px; color: var(--muted); }
        .forgot { font-size: 13px; color: var(--primary); text-decoration: none; }
        button { width: 100%; padding: 11px; background: var(--primary); color: #fff; border: 0; border-radius: 8px; font-size: 15px; cursor: pointer; }
        button:disabled { background: #ccc; cursor: not-allowed; }
        .meta { margin-top: 14px; text-align: center; font-size: 13px; color: var(--muted); }
        .meta a { color: var(--primary); text-decoration: none; }
        #message { display: none; padding: 10px; border-radius: 8px; margin-bottom: 12px; font-size: 13px; text-align: center; }
        .error-msg { background: #fff2f2; color: #8a0000; border: 1px solid #ffcccc; }
    </style>
</head>
<body>
    <div class="card">
        <h2>Sign In</h2>
        <div id="message"></div>

        <form id="loginForm">
            <div class="input-group">
                <label for="email">Email address</label>
                <input type="email" id="email" required autocomplete="email">
            </div>

            <div class="input-group">
                <label for="password">Password</label>
                <input type="password" id="password" required autocomplete="current-password">
            </div>

            <div class="actions">
                <a class="forgot" href="forgotpassword">Forgot password?</a>
            </div>

            <button type="submit" id="loginBtn">Log In</button>

            <div class="meta">Don't have an account? <a href="signup">Create one</a></div>
        </form>
    </div>

    <script>
        document.getElementById('loginForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const msgBox = document.getElementById('message');
            const btn = document.getElementById('loginBtn');
            const email = document.getElementById('email').value;
            const password = document.getElementById('password').value;

            msgBox.style.display = 'none';
            btn.disabled = true;
            btn.innerText = "Signing in...";

            const xhr = new XMLHttpRequest();
            xhr.open('POST', 'api/user/login', true);
            xhr.setRequestHeader('Content-Type', 'application/json');

            xhr.onload = function() {
                btn.disabled = false;
                btn.innerText = "Log In";
                
                try {
                    const res = JSON.parse(xhr.responseText);
                    if (xhr.status === 200 && res.success) {
                        window.location.href = 'index';
                    } else {
                        msgBox.style.display = 'block';
                        msgBox.className = 'error-msg';
                        msgBox.innerText = res.error || "Invalid email or password.";
                    }
                } catch(e) {
                    msgBox.style.display = 'block';
                    msgBox.className = 'error-msg';
                    msgBox.innerText = "Server error. Please check logs.";
                }
            };

            xhr.send(JSON.stringify({
                email: email,
                password: password
            }));
        });
    </script>
</body>
</html>