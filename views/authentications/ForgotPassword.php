<?php session_start(); ?>
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

        .msg.success {
            background: var(--success-bg);
            color: var(--success-text);
            border-color: #dcfce7;
        }

        .msg.error {
            background: var(--error-bg);
            color: var(--error-text);
            border-color: #fee2e2;
        }

        label {
            display: block;
            font-size: 0.875rem;
            font-weight: 600;
            margin-top: 1rem;
            margin-bottom: 0.5rem;
        }

        .input,
        select {
            width: 100%;
            padding: 0.75rem;
            border: 1px solid #cbd5e1;
            border-radius: 6px;
            box-sizing: border-box;
            transition: border-color 0.2s, box-shadow 0.2s;
            background: white;
        }

        .input:focus,
        select:focus {
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
            margin-top: 1.5rem;
            transition: background 0.2s;
        }

        .btn:hover {
            background: var(--primary-hover);
        }

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

        .link:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="auth-card" role="main">
        <h1>Forgot password?</h1>
        <p class="lead">No worries! Enter your details below.</p>

        <?php if (isset($msg)): ?>
            <div class="msg <?php echo $msg_type; ?>">
                <?php echo htmlspecialchars($msg); ?>
            </div>
        <?php endif; ?>

        <form id="forgotPasswordForm">
            <div class="form-group">
                <label for="email">Email Address</label>
                <input id="email" class="input" type="email" name="email" placeholder="name@company.com" required autofocus>

                <label for="security_q">Security Question</label>
                <select id="security_q" class="input" required>
                    <option value="">--Select a Question--</option>
                    <option value="What was the name of your first pet?">First pet name?</option>
                    <option value="In what city were you born?">Birth city?</option>
                    <option value="What was your first car?">First car?</option>
                </select>

                <label for="security_a">Your Answer</label>
                <input type="text" id="security_a" class="input" required>
            </div>
            <button class="btn" type="submit">Verify Identity</button>
        </form>

        <form id="newPassForm" style="display:none;">
            <label for="newPass">Enter New Password</label>
            <input type="password" id="newPass" class="input" required>

            <label for="confirmNewPass">Confirm New Password</label>
            <input type="password" id="confirmNewPass" class="input" required>

            <button class="btn" type="submit">Update Password</button>
        </form>

        <div class="footer-links">
            <a class="link" href="login">← Back to login</a>
        </div>
    </div>

    <script>
        const forgotForm = document.getElementById('forgotPasswordForm');
        const newPassForm = document.getElementById('newPassForm');

        forgotForm.addEventListener('submit', async function(event) {
            event.preventDefault();

            const email = document.getElementById('email').value;
            const security_q = document.getElementById('security_q').value;
            const security_a = document.getElementById('security_a').value;

            try {
                const response = await fetch('/citystatus/api/user/forgotPassword', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ email, security_q, security_a })
                });

                const result = await response.json();

                if (result.success) {
                    forgotForm.style.display = 'none';
                    newPassForm.style.display = 'block';
                    
                    newPassForm.onsubmit = async function(e) {
                        e.preventDefault();
                        const newPassword = document.getElementById('newPass').value;
                        const confirmPassword = document.getElementById('confirmNewPass').value;

                        if (newPassword !== confirmPassword) {
                            alert('Passwords do not match!');
                            return;
                        }

                        const resetResponse = await fetch('/citystatus/api/user/resetPassword', {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/json' },
                            body: JSON.stringify({ email, newPassword })
                        });

                        const resetResult = await resetResponse.json();

                        if (resetResult.success) {
                            alert('Password updated successfully!');
                            window.location.href = 'login';
                        } else {
                            alert('Password reset failed: ' + resetResult.error);
                        }
                    };
                } else {
                    alert('Error: ' + (result.error || 'Verification failed'));
                }
            } catch (err) {
                alert('An error occurred. Please try again later.');
                console.error(JSON.stringify(err));
            }
        });
    </script>
</body>
</html>
