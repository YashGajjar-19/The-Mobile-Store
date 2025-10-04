<?php
$page_title = 'Login | The Mobile Store';
require_once '../includes/auth.php';
require_once '../includes/header.php';

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

<body>
    <!-- Form container -->
    <div class="form-container">
        <!-- Form wrapper -->
        <div class="form-wrapper">
            <!-- Image column -->
            <div class="form-image-column">
                <img src="../assets/images/svg/login.svg" alt="Login Image" class="login-image">
            </div>

            <!-- Content column -->
            <div class="form-content-column">
                <!-- Form card -->
                <div class="form-card">
                    <!-- Header -->
                    <div class="form-header">
                        <h2 class="form-header">
                            Welcome Back
                        </h2>
                        <p class="form-header">
                            Login to access your account
                        </p>
                    </div>

                    <!-- Alert box -->
                    <div id="alert-container"></div>
                    <?php
                    if (isset($_GET['status']) && $_GET['status'] == 'pw_reset_success') {
                        echo '<div class="alert alert-success">Your password has been reset successfully. Please log in.</div>';
                    }
                    ?>

                    <!-- Main form -->
                    <form action="../includes/auth.php" method="POST" class="auth-form">
                        <input type="hidden" name="login" value="1">

                        <!-- Email -->
                        <div class="form-group">
                            <label for="email" class="form-label">Email</label>
                            <div class="form-input">
                                <ion-icon name="mail-outline"></ion-icon>
                                <input type="email" id="email" name="email" placeholder="Enter your email" required>
                            </div>
                        </div>

                        <!-- Password -->
                        <div class="form-group">
                            <label for="password" class="form-label">Password</label>
                            <div class="form-input">
                                <ion-icon name="lock-closed-outline"></ion-icon>
                                <input type="password" id="password" name="password" placeholder="Enter your password"
                                    required>
                            </div>
                        </div>

                        <!-- Remember me and forgot password -->
                        <div class="form-terms">
                            <label class="remember-me">
                                <input type="checkbox" name="remember"> Remember me
                            </label>

                            <a href="forgot-password.php">Forgot Password?</a>
                        </div>

                        <!-- Form submission -->
                        <div class="form-submit">
                            <button type="submit" class="button">Login</button>
                        </div>

                        <!-- Sign up link -->
                        <div class="form-footer">
                            Don't have an account? <a href="./register.php" class="form-footer">Sign up</a>
                        </div>
                    </form>
                    <!-- Main form ends -->
                </div>
                <!-- Form card ends -->
            </div>
            <!-- Form content column ends -->
        </div>
        <!-- Form wrapper ends -->
    </div>
    <!-- Form container ends -->

    <script src="../assets/js/auth.js"></script>
</body>