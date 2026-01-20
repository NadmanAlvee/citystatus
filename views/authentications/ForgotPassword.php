<?php
/**
 * Forgot Password Handler - CityStatus
 * * Instructions: 
 * 1. Update $dsn, $dbUser, and $dbPass with your credentials.
 * 2. Ensure your 'users' table has 'reset_token' (VARCHAR) and 'reset_expires' (DATETIME).
 */

session_start();

// --- Configuration ---
$dsn      = 'mysql:host=localhost;dbname=your_db;charset=utf8mb4';
$dbUser   = 'db_user';
$dbPass   = 'db_pass';
$baseUrl  = 'http://localhost:80/citystatus';

// --- Initialization ---
$pdo      = null;
$msg      = '';
$msg_type = '';

try {
    $pdo = new PDO($dsn, $dbUser, $dbPass, [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ]);
} catch (Exception $e) {
    // Silently fail to "Preview Mode" for safety
    $pdo = null;
}

// --- Logic ---
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = filter_var($_POST['email'] ?? '', FILTER_VALIDATE_EMAIL);

    if (!$email) {
        $msg      = 'Please enter a valid email address.';
        $msg_type = 'error';
    } else {
        if ($pdo === null) {
            $msg      = 'System in Preview Mode: Reset link generated but not sent.';
            $msg_type = 'success';
        } else {
            // Check if user exists
            $stmt = $pdo->prepare('SELECT id FROM users WHERE email = ? LIMIT 1');
            $stmt->execute([$email]);
            $user = $stmt->fetch();

            if ($user) {
                $token   = bin2hex(random_bytes(32));
                $expires = date('Y-m-d H:i:s', time() + 3600);

                $update = $pdo->prepare('UPDATE users SET reset_token = ?, reset_expires = ? WHERE id = ?');
                $update->execute([$token, $expires, $user['id']]);

                $resetLink = $baseUrl . '/reset_password.php?token=' . $token;
                
                // Email components
                $subject = 'Password Reset Request';
                $headers = "From: no-reply@example.com\r\n" .
                           "Reply-To: no-reply@example.com\r\n" .
                           "Content-Type: text/plain; charset=UTF-8";
                $body    = "Hello,\n\nTo reset your password, please click the link below:\n" . 
                           $resetLink . "\n\nThis link will expire in 1 hour.";

                @mail($email, $subject, $body, $headers);
            }

            // Always show the same message to prevent "User Enumeration"
            $msg      = 'If that email is in our system, you will receive a reset link shortly.';
            $msg_type = 'success';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password — CityStatus</title>
    <style>
        :root {
            --primary: #2563eb;
            --primary-hover: #1d4ed8;
            --bg: #f8fafc;
            --text-main: #1e293b;
            --text-muted: #64748b;
            --error-bg: #fef2f2;
            --error-text: #991b1b;
            --success-bg: #f0fdf4;
            --success-text: #166534;
        }

        body {
            font-family: 'Inter', system-ui, -apple-system, sans-serif;
            background-color: var(--bg);
            color: var(--text-main);
            margin: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100vh;
        }

        .auth-card {
            background: #ffffff;
            padding: 2.5rem;
            border-radius: 12px;
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 8px 10px -6px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 400px;
        }

        .auth-card h1 {
            margin: 0 0 0.5rem;
            font-size: 1.5rem;
            font-weight: 700;
            letter-spacing: -0.025em;
        }

        .lead {
            margin-bottom: 1.5rem;
            font-size: 0.95rem;
            color: var(--text-muted);
            line-height: 1.5;
        }

        .msg {
            padding: 0.875rem;
            border-radius: 6px;
            margin-bottom: 1.5rem;
            font-size: 0.9rem;
            border: 1px solid transparent;
        }

        .msg.success { background: var(--success-bg); color: var(--success-text); border-color: #dcfce7; }
        .msg.error { background: var(--error-bg); color: var(--error-text); border-color: #fee2e2; }

        label {
            display: block;
            font-size: 0.875rem;
            font-weight: 600;
            margin-bottom: 0.5rem;
        }

        .input {
            width: 100%;
            padding: 0.75rem;
            border: 1px solid #cbd5e1;
            border-radius: 6px;
            box-sizing: border-box;
            transition: border-color 0.2s, box-shadow 0.2s;
        }

        .input:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
        }

        .btn {
            width: 100%;
            padding: 0.75rem;
            background: var(--primary);
            color: white;
            border: none;
            border-radius: 6px;
            font-weight: 600;
            cursor: pointer;
            margin-top: 1.25rem;
            transition: background 0.2s;
        }

        .btn:hover { background: var(--primary-hover); }

        .footer-links {
            margin-top: 1.5rem;
            text-align: center;
            font-size: 0.875rem;
        }

        .link {
            color: var(--primary);
            text-decoration: none;
            font-weight: 500;
        }

        .link:hover { text-decoration: underline; }
    </style>
</head>
<body>

<div class="auth-card" role="main">
    <h1>Forgot password?</h1>
    <p class="lead">No worries! Enter your email below and we'll send you reset instructions.</p>

    <?php if ($msg): ?>
        <div class="msg <?php echo $msg_type; ?>">
            <?php echo htmlspecialchars($msg); ?>
        </div>
    <?php endif; ?>

    <form method="POST" action="">
        <div class="form-group">
            <label for="email">Email Address</label>
            <input 
                id="email" 
                class="input" 
                type="email" 
                name="email" 
                placeholder="name@company.com" 
                required 
                autofocus
            >
        </div>
        
        <button class="btn" type="submit">Send Reset Link</button>
    </form>

    <div class="footer-links">
        <a class="link" href="<?php echo $baseUrl; ?>/login">← Back to login</a>
    </div>
</div>

</body>
</html>