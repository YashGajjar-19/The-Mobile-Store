<?php
require_once '../includes/auth.php';

// Determine the link for the account icon
if (isset($_SESSION['user_id'])) {
    // User is logged in, link to profile page
    $account_link = './user/profile/index.php';
    $account_text = 'Account';
} else {
    // User is not logged in, link to register page
    $account_link = 'register.php';
    $account_text = 'Login / Register';
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | The Mobile Store</title>

    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>

    <link rel="stylesheet" href="../assets/css/main.css">
</head>

<body>
    <div class="form-container">
        <div class="form-wrapper">
            <div class="form-image-column">
                <img src="../assets/images/svg/login.svg" alt="Login Image" class="login-image">
            </div>
            <div class="form-content-column">
                <div class="form-card">
                    <div class="form-header">
                        <h2 class="form-header">Welcome Back</h2>
                        <p class="form-header">Login to access your account</p>
                    </div>
                    <div id="alert-container"></div>
                    <form action="../includes/auth.php" method="POST" class="auth-form">
                        <input type="hidden" name="login" value="1">

                        <div class="form-group">
                            <label for="email" class="form-label">Email</label>
                            <div class="form-input">
                                <ion-icon name="mail-outline"></ion-icon>
                                <input type="email" id="email" name="email" placeholder="Enter your email" required>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="password" class="form-label">Password</label>
                            <div class="form-input">
                                <ion-icon name="lock-closed-outline"></ion-icon>
                                <input type="password" id="password" name="password" placeholder="Enter your password"
                                    required>
                            </div>
                        </div>

                        <div class="form-terms">
                            <label class="remember-me">
                                <input type="checkbox" name="remember"> Remember me
                            </label>
                            <a href="forgot_password.html" class="forgot-password">Forgot password?</a>
                        </div>

                        <div class="form-submit">
                            <button type="submit" class="button">Login</button>
                        </div>

                        <div class="form-footer">
                            Don't have an account? <a href="./register.php" class="form-footer">Sign up</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="../assets/js/auth.js"></script>
</body>

</html>