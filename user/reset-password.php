<?php
session_start();
$page_title = 'Reset Password | The Mobile Store';
require_once '../includes/header.php';
require_once '../includes/config.php';

// Check for the temporary token in the session
$token = $_SESSION['reset_token'] ?? '';
$is_token_valid = false;
$error_message = '';

if ($token) {
    $parts = explode('.', $token);
    if (count($parts) === 3) {
        list($header_encoded, $payload_encoded, $signature_encoded) = $parts;
        $payload = json_decode(base64_decode(str_replace(['-', '_'], ['+', '/'], $payload_encoded)), true);

        $data_to_sign = $header_encoded . '.' . $payload_encoded;
        $expected_signature = hash_hmac('sha256', $data_to_sign, SECRET_KEY, true);
        $expected_signature_encoded = str_replace(['+', '=', '/'], ['-', '', '_'], base64_encode($expected_signature));

        if (hash_equals($expected_signature_encoded, $signature_encoded)) {
            if (isset($payload['exp']) && $payload['exp'] >= time()) {
                $is_token_valid = true;
            } else {
                $error_message = 'Your session has expired. Please verify your identity again.';
            }
        } else {
            $error_message = 'Invalid session. Please try again.';
        }
    } else {
        $error_message = 'Invalid session data.';
    }
} else {
    $error_message = 'No permission to access this page. Please verify first.';
}

// The line `unset($_SESSION['reset_token']);` has been removed from here.
?>

<body>
    <div class="form-container">
        <div class="form-wrapper">
            <div class="form-image-column">
                <img src="../assets/images/svg/register.svg" alt="Reset Password Image" class="login-image">
            </div>
            <div class="form-content-column">
                <div class="form-card">
                    <div class="form-header">
                        <h2 class="form-header">Reset Your Password</h2>
                    </div>

                    <?php
                    // Display success message if account is found
                    if (isset($_GET['status']) && $_GET['status'] == 'found' && $is_token_valid) {
                        echo '<div class="alert alert-success">Account verified successfully. You can now reset your password.</div>';
                    }
                    ?>

                    <?php if ($is_token_valid) : ?>
                        <p class="form-header" style="margin-top: -1rem; margin-bottom: 1rem;">Enter and confirm your new password below.</p>
                        <?php
                        if (isset($_GET['status']) && $_GET['status'] == 'mismatch') {
                            echo '<div class="alert alert-danger">Passwords do not match. Please try again.</div>';
                        }
                        ?>
                        <form id="reset-password-form" action="../handlers/password_reset_handler.php" method="POST" class="auth-form">
                            <input type="hidden" name="action" value="reset_password">
                            <input type="hidden" name="user_id" value="<?php echo htmlspecialchars($payload['user_id']); ?>">

                            <div class="form-group">
                                <label for="new_password" class="form-label">New Password</label>
                                <div class="form-input">
                                    <ion-icon name="lock-closed-outline"></ion-icon>
                                    <input type="password" id="new_password" name="new_password" placeholder="Enter new password" required>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="confirm_password" class="form-label">Confirm New Password</label>
                                <div class="form-input">
                                    <ion-icon name="lock-closed-outline"></ion-icon>
                                    <input type="password" id="confirm_password" name="confirm_password" placeholder="Confirm new password" required>
                                </div>
                            </div>

                            <div class="form-submit">
                                <button type="submit" class="button">Update Password</button>
                            </div>
                        </form>
                    <?php else : ?>
                        <div class="alert alert-danger"><?php echo $error_message; ?></div>
                        <div class="form-submit">
                            <a href="forgot-password.php" class="button" style="text-align: center;">Start Over</a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</body>