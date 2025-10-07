<?php
session_start();
$page_title = 'Forgot Password | The Mobile Store';
require_once '../includes/header.php';
?>

<body>
    <div class="form-container">
        <div class="form-wrapper">

            <div class="form-image-column">
                <img src="../assets/images/svg/forgot.svg" alt="Forgot Password Image" class="login-image" style="width: 80%;">
            </div>

            <div class="form-content-column">
                <div class="form-card">
                    <!-- Header -->
                    <div class="form-header">
                        <h2 class="form-header">
                            Forgot Password
                        </h2>

                        <p class="form-header">
                            Enter your email and phone to verify your account
                        </p>
                    </div>

                    <?php
                    if (isset($_GET['error'])) {
                        if ($_GET['error'] == 'notfound') {
                            echo '<div class="alert alert-error">
                                No account found with that email and phone number.
                            </div>';
                        } else {
                            echo '<div class="alert alert-error">
                            An unknown error occurred. Please try again.
                            </div>';
                        }
                    }
                    ?>

                    <form id="forgot-password-form" action="../handlers/password_reset_handler.php" method="POST" class="auth-form">
                        <input type="hidden" name="action" value="verify_user">

                        <div class="form-group">
                            <label for="email" class="form-label">Email Address</label>
                            <div class="form-input">
                                <ion-icon name="mail-outline"></ion-icon>
                                <input type="email" id="email" name="email" placeholder="Enter your email" required>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="phone_number" class="form-label">Registered Phone Number</label>
                            <div class="form-input">
                                <ion-icon name="call-outline"></ion-icon>
                                <input type="tel" id="phone_number" name="phone_number" placeholder="Enter your phone number" required>
                            </div>
                        </div>

                        <div class="form-submit">
                            <button type="submit" class="button">Verify Account</button>
                        </div>
                    </form>

                    <div class="form-footer">
                        <p>Remembered your password? <a href="login.php">Log In</a></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>