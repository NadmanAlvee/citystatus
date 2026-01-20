<?php
// Minimal forgot-password implementation. Update DB credentials & mail settings.

session_start();

$dsn = 'mysql:host=localhost;dbname=your_db;charset=utf8mb4';
$dbUser = 'db_user';
$dbPass = 'db_pass';
$baseUrl = 'http://localhost:80/citystatus';

$pdo = null;
$db_error = false;

try {
    $pdo = new PDO($dsn, $dbUser, $dbPass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);
} catch (Exception $e) {
    // Do not exit on failure — enable preview mode without DB.
    $pdo = null;
    $db_error = true;
    // In production, log $e->getMessage()
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = filter_var($_POST['email'] ?? '', FILTER_VALIDATE_EMAIL);
    if (!$email) {
        $msg = 'Please enter a valid email.';
        $msg_type = 'error';
    } else {
        if ($pdo === null) {
            // Preview mode: do not attempt DB or mail, just show success message so UI can be tested.
            $msg = 'Preview mode: a password reset link would be sent if the system were connected to a database.';
            $msg_type = 'success';
        } else {
            // Find user (do not reveal existence)
            $stmt = $pdo->prepare('SELECT id FROM users WHERE email = :email LIMIT 1');
            $stmt->execute([':email' => $email]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($user) {
                $token = bin2hex(random_bytes(24));
                $expires = date('Y-m-d H:i:s', time() + 3600); // 1 hour expiry

                $up = $pdo->prepare('UPDATE users SET reset_token = :token, reset_expires = :expires WHERE id = :id');
                $up->execute([':token' => $token, ':expires' => $expires, ':id' => $user['id']]);

                $resetLink = $baseUrl . '/reset_password.php?token=' . urlencode($token);

                $subject = 'Password reset';
                $message = "If you requested a password reset, click the link:\n\n{$resetLink}\n\nThis link expires in 1 hour.";
                $headers = "From: no-reply@example.com\r\n";

                // Use mail() or PHPMailer. Replace with real SMTP in production.
                @mail($email, $subject, $message, $headers);
            }

            $msg = 'If that email exists in our system, a password reset link has been sent.';
            $msg_type = 'success';
        }
    }
}
?>
<!doctype html>
<html>
<head>
	<meta charset="utf-8">
	<title>Forgot Password — CityStatus</title>
	<link rel="stylesheet" href="<?php echo $baseUrl; ?>/assets/css/style.css"> <!-- adjust path if needed -->
	<meta name="viewport" content="width=device-width,initial-scale=1">
	<style>
		/* minimal fallback styling so preview looks like login/signup */
		body{font-family:Arial,Helvetica,sans-serif;background:#f5f7fb;margin:0;padding:0;display:flex;align-items:center;justify-content:center;height:100vh}
		.auth-card{background:#fff;padding:28px;border-radius:8px;box-shadow:0 6px 18px rgba(20,30,60,0.08);width:360px;max-width:90%}
		.auth-card h1{margin:0 0 8px;font-size:20px}
		.auth-card p.lead{margin:0 0 16px;color:#6b7280}
		.input{width:100%;padding:10px 12px;border:1px solid #979797;border-radius:6px;margin-top:8px}
		.btn{display:inline-block;width:100%;padding:10px 12px;background:#2563eb;color:#fff;border-radius:6px;border:none;cursor:pointer;margin-top:12px}
		.msg{padding:10px;border-radius:6px;margin-bottom:12px}
		.msg.success{background:#ecfdf5;color:#064e3b}
		.msg.error{background:#fff1f2;color:#7f1d1d}
		.small{font-size:13px;color:#6b7280;margin-top:12px;text-align:center}
		.link{color:#2563eb;text-decoration:none}
	</style>
</head>
<body>
	<div class="auth-card" role="main" aria-labelledby="forgot-title">
		<h1 id="forgot-title">Forgot your password?</h1>
		<p class="lead">Enter the email associated with your account and we'll send a link to reset your password.</p>

		<?php if (!empty($msg)): ?>
			<div class="msg <?php echo ($msg_type ?? '') === 'success' ? 'success' : 'error'; ?>">
				<?php echo htmlspecialchars($msg); ?>
			</div>
		<?php endif; ?>

		<form method="post" action="">
			<label for="email">Email</label>
			<input id="email" class="input" type="email" name="email" required>
			<button class="btn" type="submit">Send reset link</button>
		</form>

		<div class="small">
			<a class="link" href="<?php echo $baseUrl; ?>/login">Back to login</a>
		</div>
	</div>
</body>
</html>
