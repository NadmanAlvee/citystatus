<html>
<head>
    <title>Signup Page</title>
</head>
<body>

    <h2>Create an Account</h2>

    <form action="api/users/signup.php" method="POST">
        
        <label for="name">Full Name:</label><br>
        <input type="text" id="name" name="name" required><br><br>

        <label for="email">Email Address:</label><br>
        <input type="email" id="email" name="email" required><br><br>

        <label for="phone">Phone Number:</label><br>
        <input type="number" id="phone" name="phone" required><br><br>

        <label for="password">Password:</label><br>
        <input type="password" id="password" name="password" required><br><br>

        <label>Sex:</label><br>
        <input type="radio" id="male" name="sex" value="male">
        <label for="male">Male</label>
        <input type="radio" id="female" name="sex" value="female">
        <label for="female">Female</label>
        <input type="radio" id="other" name="sex" value="other">
        <label for="other">Other</label><br><br>

        <label for="password_ques">Security Question:</label><br>
        <select id="password_ques" name="password_ques" required>
            <option value="">--Select a Question--</option>
            <option value="pet">What was the name of your first pet?</option>
            <option value="city">In what city were you born?</option>
            <option value="school">What was the name of your elementary school?</option>
            <option value="car">What was your first car?</option>
            <option value="mother">What is your mother's maiden name?</option>
        </select><br><br>

        <label for="dob">Date of Birth:</label><br>
        <input type="date" id="dob" name="dob" required><br><br>

        <label for="district">District:</label><br>
        <input type="text" id="district" name="district" required><br><br>

        <button type="submit" name="signup_submit">Sign Up</button>

    </form>

</body>
</html>