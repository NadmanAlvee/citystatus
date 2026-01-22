<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Create an Account</title>
    <style>
        :root { --bg: #f4f7fb; --card: #ffffff; --primary: #0078d4; --muted: #666; }
        * { box-sizing: border-box; }
        body { margin: 0; font-family: Inter, Segoe UI, Arial, sans-serif; background: var(--bg); display: flex; align-items: center; justify-content: center; min-height: 100vh; padding: 20px 0; }
        .card { width: 400px; background: var(--card); padding: 24px; border-radius: 10px; box-shadow: 0 8px 30px rgba(16, 24, 40, 0.08); }
        h2 { margin: 0 0 18px; font-weight: 600; color: #222; text-align: center; }
        .input-group { margin-bottom: 12px; }
        label { display: block; margin-bottom: 6px; font-size: 13px; color: #444; }
        input, select { width: 100%; padding: 10px 12px; border: 1px solid #979797; border-radius: 8px; font-size: 14px; background: #fff; font-family: inherit; }
        .radio-group { display: flex; gap: 15px; margin-top: 5px; padding: 5px 0; }
        .radio-item { display: flex; align-items: center; gap: 5px; font-size: 14px; color: #444; }
        .radio-item input { width: auto; }
        button { width: 100%; padding: 11px; background: var(--primary); color: #fff; border: 0; border-radius: 8px; font-size: 15px; cursor: pointer; margin-top: 10px; }
        button:active { transform: translateY(1px); }
        .meta { margin-top: 14px; text-align: center; font-size: 13px; color: var(--muted); }
        .meta a { color: var(--primary); text-decoration: none; }
        #message { display: none; padding: 10px; border-radius: 8px; margin-bottom: 15px; font-size: 13px; text-align: center; }
        .error-msg { background: #fff2f2; color: #8a0000; border: 1px solid #ffcccc; }
        .success-msg { background: #f0fff0; color: #006400; border: 1px solid #ccffcc; }
    </style>
</head>
<body>
    <div class="card">
        <h2>Create an Account</h2>
        <div id="message"></div>
        <form id="signupForm">
            <div class="input-group">
                <label for="name">Full Name</label>
                <input type="text" id="name" required>
            </div>
            <div class="input-group">
                <label for="email">Email Address</label>
                <input type="email" id="email" required>
            </div>
            <div class="input-group">
                <label for="phone">Phone Number</label>
                <input type="text" id="phone" required maxlength="11">
            </div>
            <div class="input-group">
                <label for="password">Password</label>
                <input type="password" id="password" required>
            </div>
            <div class="input-group">
                <label>Sex</label>
                <div class="radio-group">
                    <label class="radio-item"><input type="radio" name="sex" value="Male" checked> Male</label>
                    <label class="radio-item"><input type="radio" name="sex" value="Female"> Female</label>
                    <label class="radio-item"><input type="radio" name="sex" value="Other"> Other</label>
                </div>
            </div>
            <div class="input-group">
                <label for="security_q">Security Question</label>
                <select id="security_q" required>
                    <option value="">--Select a Question--</option>
                    <option value="What was the name of your first pet?">First pet name?</option>
                    <option value="In what city were you born?">Birth city?</option>
                    <option value="What was your first car?">First car?</option>
                </select>
            </div>
            <div class="input-group" id="sec_ans_group" style="display:none;">
                <label for="security_a">Your Answer</label>
                <input type="text" id="security_a">
            </div>
            <div class="input-group">
                <label for="dob">Date of Birth</label>
                <input type="date" id="dob" required>
            </div>
            <div class="input-group">
                <label for="district">District</label>
                <input type="text" id="district" required>
            </div>
            <button type="submit">Sign Up</button>
            <div class="meta">Already have an account? <a href="login">Sign In</a></div>
        </form>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const questionSelect = document.getElementById('security_q');
            const answerGroup = document.getElementById('sec_ans_group');
            const answerInput = document.getElementById('security_a');
            const form = document.getElementById('signupForm');
            const msgBox = document.getElementById('message');

            questionSelect.addEventListener('change', function() {
                if (this.value !== "") {
                    answerGroup.style.display = 'block';
                    answerInput.required = true;
                    answerInput.disabled = false;
                } else {
                    answerGroup.style.display = 'none';
                    answerInput.required = false;
                    answerInput.disabled = true;
                    answerInput.value = "";
                }
            });

            form.onsubmit = function(e) {
                e.preventDefault();
                
                const formData = {
                    name: document.getElementById('name').value,
                    email: document.getElementById('email').value,
                    phone: document.getElementById('phone').value,
                    password: document.getElementById('password').value,
                    sex: document.querySelector('input[name="sex"]:checked').value,
                    security_q: questionSelect.value,
                    security_a: answerInput.value,
                    dob: document.getElementById('dob').value,
                    district: document.getElementById('district').value
                };

                const xhr = new XMLHttpRequest();
                xhr.open('POST', '/citystatus/api/user/signup', true);
                xhr.setRequestHeader('Content-Type', 'application/json');
                xhr.send(JSON.stringify(formData));
                xhr.onload = function() {
                    let res;
                    try {
                        res = JSON.parse(xhr.responseText);
                    } catch(e) {
                        res = { error: "Server error occurred" };
                    }
                    
                    msgBox.style.display = 'block';
                    if (xhr.status === 200) {
                        msgBox.className = 'success-msg';
                        msgBox.innerText = "Account created! Redirecting...";
                        setTimeout(() => window.location.href = '/citystatus/index', 1500);
                    } else {
                        msgBox.className = 'error-msg';
                        if (res.errors) {
                            msgBox.innerText = Object.values(res.errors).join(', ');
                        } else {
                            msgBox.innerText = res.error || "Signup failed.";
                        }
                    }
                }; 
            };
        });
    </script>
</body>
</html>
