<?php
// handle POST login using model->checkLogin()
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_once __DIR__ . '/../../model/users.php';
    $email = trim($_POST['email'] ?? '');
    $password = trim($_POST['password'] ?? '');

    $authMessage = '';
    if ($email === '' || $password === '') {
        $authMessage = 'email and password required';
    } else {
        $user = new User();
        if ($user->checkLogin($email, $password)) {
            $authMessage = 'logged in';
        } else {
            $authMessage = 'invalid credentials';
        }
    }
}
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login</title>
    <style>
        :root {
            --bg: #f4f7fb;
            --card: #ffffff;
            --primary: #0078d4;
            --muted: #666;
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            font-family: Inter, Segoe UI, Arial, sans-serif;
            background: var(--bg);
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
        }

        .card {
            width: 360px;
            background: var(--card);
            padding: 24px;
            border-radius: 10px;
            box-shadow: 0 8px 30px rgba(16, 24, 40, 0.08);
        }

        h2 {
            margin: 0 0 18px;
            font-weight: 600;
            color: #222;
            text-align: center;
        }

        .input-group {
            margin-bottom: 12px;
        }

        label {
            display: block;
            margin-bottom: 6px;
            font-size: 13px;
            color: #444;
        }

        input[type="email"],
        input[type="password"],
        input[type="text"] {
            width: 100%;
            padding: 10px 12px;
            border: 1px solid #979797;
            border-radius: 8px;
            font-size: 14px;
            background: #fff;
        }

        .actions {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin: 10px 0 16px;
        }

        .remember {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 13px;
            color: var(--muted);
        }

        .forgot {
            font-size: 13px;
            color: var(--primary);
            text-decoration: none;
        }

        .forgot:hover {
            text-decoration: underline;
        }

        button {
            width: 100%;
            padding: 11px;
            background: var(--primary);
            color: #fff;
            border: 0;
            border-radius: 8px;
            font-size: 15px;
            cursor: pointer;
        }

        button:active {
            transform: translateY(1px);
        }

        .meta {
            margin-top: 14px;
            text-align: center;
            font-size: 13px;
            color: var(--muted);
        }

        .meta a {
            color: var(--primary);
            text-decoration: none;
        }

        .error {
            background: #fff2f2;
            color: #8a0000;
            padding: 8px;
            border-radius: 8px;
            margin-bottom: 12px;
            font-size: 13px;
        }
    </style>
</head>
<body>
    <div class="card">
        <h2>Sign In</h2>

        <?php if (!empty($authMessage)): ?>
            <div class="error"><?php echo htmlspecialchars($authMessage); ?></div>
        <?php endif; ?>

        <?php if(!empty($_GET['error'] ?? '')): ?>
            <div class="error"><?php echo htmlspecialchars($_GET['error']); ?></div>
        <?php endif; ?>

        <form action="" method="POST">
            <div class="input-group">
                <label for="email">Email address</label>
                <input type="email" id="email" name="email" required autocomplete="email">
            </div>

            <div class="input-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required autocomplete="current-password">
            </div>

            <div class="actions">
                <label class="remember"><input type="checkbox" name="remember"> Remember me</label>
                <a class="forgot" href="forgotpassword">Forgot password?</a>
            </div>

            <button type="submit" name="login_submit">Log In</button>

            <div class="meta">Don't have an account? <a href="signup">Create one</a></div>
        </form>
    </div>
</body>
</html>
<?php
?>