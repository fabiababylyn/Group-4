<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Financial System</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="login.css">
</head>
<body class="login-body">

    <div class="login-box">
        <form action="auth.php" method="POST">
            <h2>Financial System</h2>

            <?php if(isset($_GET['msg']) && $_GET['msg'] == 'registered'): ?>
                <div class="alert alert-success">Account created! You can now login.</div>
            <?php endif; ?>

            <?php if(isset($_GET['error']) && $_GET['error'] == 'invalid'): ?>
                <div class="alert alert-danger">Invalid Username or Password.</div>
            <?php endif; ?>

            <div class="input-group">
                <label>Username</label>
                <input type="text" name="username" placeholder="Username" required 
                       class="<?php echo isset($_GET['error']) ? 'border-error' : ''; ?>">
            </div>

            <div class="input-group">
                <label>Password</label>
                <input type="password" name="password" id="password" placeholder="Password" required
                       class="<?php echo isset($_GET['error']) ? 'border-error' : ''; ?>">
                <span class="toggle-password" onclick="togglePass()">Show</span>
            </div>
            
            <button type="submit" name="login">Login</button>

            <p style="margin-top: 15px; text-align: center; font-size: 0.9em;">
                Don't have an account? <a href="register.php" style="color: #1abc9c; text-decoration: none; font-weight: bold;">Create New Account</a>
            </p>
        </form>
    </div>

    <script>
        function togglePass() {
            var x = document.getElementById("password");
            var btn = document.querySelector(".toggle-password");
            if (x.type === "password") {
                x.type = "text";
                btn.innerHTML = "Hide";
            } else {
                x.type = "password";
                btn.innerHTML = "Show";
            }
        }
    </script>
</body>
</html>