<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Create an Account</title>
    <style>
        :root{--bg:#f4f7fb;--card:#ffffff;--primary:#0078d4;--muted:#666}
        *{box-sizing:border-box}
        body{margin:0;font-family:Inter,Segoe UI,Arial,sans-serif;background:var(--bg);display:flex;align-items:center;justify-content:center;min-height:100vh;padding: 20px 0;}
        .card{width:400px;background:var(--card);padding:24px;border-radius:10px;box-shadow:0 8px 30px rgba(16,24,40,.08)}
        h2{margin:0 0 18px;font-weight:600;color:#222;text-align:center}
        .input-group{margin-bottom:12px}
        label{display:block;margin-bottom:6px;font-size:13px;color:#444}
        input[type="email"],
        input[type="password"],
        input[type="text"],
        input[type="number"],
        input[type="date"],
        select {
            width:100%;
            padding:10px 12px;
            border:1px solid #e6e9ee;
            border-radius:8px;
            font-size:14px;
            background:#fff;
            font-family: inherit;
        }
        /* Styling for Radio Buttons */
        .radio-group {
            display: flex;
            gap: 15px;
            margin-top: 5px;
            padding: 5px 0;
        }
        .radio-item {
            display: flex;
            align-items: center;
            gap: 5px;
            font-size: 14px;
            color: #444;
        }
        .radio-item input { margin: 0; }
        
        button{width:100%;padding:11px;background:var(--primary);color:#fff;border:0;border-radius:8px;font-size:15px;cursor:pointer;margin-top: 10px;}
        button:active{transform:translateY(1px)}
        .meta{margin-top:14px;text-align:center;font-size:13px;color:var(--muted)}
        .meta a{color:var(--primary);text-decoration:none}
        .error{background:#fff2f2;color:#8a0000;padding:8px;border-radius:8px;margin-bottom:12px;font-size:13px}
    </style>
</head>
<body>
    <div class="card">
        <h2>Create an Account</h2>

        <form action="api/users/signup.php" method="POST">
            <div class="input-group">
                <label for="name">Full Name</label>
                <input type="text" id="name" name="name" required>
            </div>

            <div class="input-group">
                <label for="email">Email Address</label>
                <input type="email" id="email" name="email" required>
            </div>

            <div class="input-group">
                <label for="phone">Phone Number</label>
                <input type="number" id="phone" name="phone" required>
            </div>

            <div class="input-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required>
            </div>

            <div class="input-group">
                <label>Sex</label>
                <div class="radio-group">
                    <div class="radio-item">
                        <input type="radio" id="male" name="sex" value="male">
                        <span>Male</span>
                    </div>
                    <div class="radio-item">
                        <input type="radio" id="female" name="sex" value="female">
                        <span>Female</span>
                    </div>
                    <div class="radio-item">
                        <input type="radio" id="other" name="sex" value="other">
                        <span>Other</span>
                    </div>
                </div>
            </div>

            <div class="input-group">
                <label for="password_ques">Security Question</label>
                <select id="password_ques" name="password_ques" required>
                    <option value="">--Select a Question--</option>
                    <option value="pet">What was the name of your first pet?</option>
                    <option value="city">In what city were you born?</option>
                    <option value="school">What was the name of your elementary school?</option>
                    <option value="car">What was your first car?</option>
                    <option value="mother">What is your mother's maiden name?</option>
                </select>
            </div>

            <div class="input-group">
                <label for="dob">Date of Birth</label>
                <input type="date" id="dob" name="dob" required>
            </div>

            <div class="input-group">
                <label for="district">District</label>
                <input type="text" id="district" name="district" required>
            </div>

            <button type="submit" name="signup_submit">Sign Up</button>

            <div class="meta">Already have an account? <a href="login">Sign In</a></div>
        </form>
    </div>
</body>
</html>