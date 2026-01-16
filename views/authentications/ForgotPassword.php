<?php
// Minimal forgot-password implementation. Update DB credentials & mail settings.

session_start();

$dsn = 'mysql:host=localhost;dbname=your_db;charset=utf8mb4';
$dbUser = 'db_user';
$dbPass = 'db_pass';
$baseUrl = 'http://localhost/citystatus'; // change to your site base URL

try {
    $pdo = new PDO($dsn, $dbUser, $dbPass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);
} catch (Exception $e) {
    // In production, log error instead of echo
    exit('Database connection failed.');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = filter_var($_POST['email'] ?? '', FILTER_VALIDATE_EMAIL);
    if (!$email) {
        $msg = 'Please enter a valid email.';
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
    }
}
?>
<!doctype html>
<html>
<head><meta charset="utf-8"><title>Forgot Password</title></head>
<body>
<?php if (!empty($msg)): ?>
<p><?php echo htmlspecialchars($msg); ?></p>
<?php endif; ?>

<form method="post" action="">
    <label>Email:<br><input type="email" name="email" required></label><br><br>
    <button type="submit">Send reset link</button>
</form>
</body>
</html>
