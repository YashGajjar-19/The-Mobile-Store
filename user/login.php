<?php
session_start();

// If user is already logged in, redirect appropriately
if (isset($_SESSION['user_id'])) {
    if (isset($_SESSION['is_admin']) && $_SESSION['is_admin'] === true) {
        header("Location: ../admin/dashboard.php");
    } else {
        header("Location: ../index.php");
    }
    exit();
}

// Display error/success messages
$error = $_SESSION['error'] ?? null;
$success = $_SESSION['success'] ?? null;

// Clear the messages so they don't persist
unset($_SESSION['error']);
unset($_SESSION['success']);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | The Mobile Store</title>

    <!-- Icons -->
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>

    <!-- Style Sheet -->
    <link rel="stylesheet" href="../assets/css/main.css">
</head>

<body>

    <div class="form-container">
        <div class="form-card">
            <div class="form-header">
                <h2 class="form-header">Welcome Back</h2>
                <p class="form-header">Login to access your account</p>
            </div>

            <form action="../includes/auth.php" method="POST" class="auth-form">
                <?php if ($error): ?>
                    <div class="form-alert alert-error">
                        <ion-icon name="warning-outline" class="alert-icon"></ion-icon>
                        <?php echo htmlspecialchars($error); ?>
                        <button class="alert-close" onclick="this.parentElement.remove()">&times;</button>
                    </div>
                <?php endif; ?>

                <?php if ($success): ?>
                    <div class="form-alert alert-success">
                        <ion-icon name="checkmark-circle-outline" class="alert-icon"></ion-icon>
                        <?php echo htmlspecialchars($success); ?>
                        <button class="alert-close" onclick="this.parentElement.remove()">&times;</button>
                    </div>
                <?php endif; ?>

                <input type="hidden" name="action" value="login">

                <div class="form-group">
                    <label for="email" class="form-label">Email</label>
                    <div class="form-input">
                        <ion-icon name="mail-outline"></ion-icon>
                        <input type="email" id="email" name="email" class="form-input" placeholder="Enter your email" required>
                    </div>
                </div>

                <div class="form-group">
                    <label for="password" class="form-label">Password</label>
                    <div class="form-input">
                        <ion-icon name="lock-closed-outline"></ion-icon>
                        <input type="password" id="password" name="password" class="form-input" placeholder="Enter your password" required>
                    </div>
                </div>

                <div class="form-options">
                    <label class="remember-me">
                        <input type="checkbox" name="remember"> Remember me
                    </label>
                    <a href="" class="forgot-password">Forgot password?</a>
                </div>

                <button type="submit" class="button">Login</button>

                <div class="form-footer">
                    Don't have an account? <a href="./register.php" class="form-footer">Sign up</a>
                </div>
            </form>
        </div>
    </div>
</body>

</html>