<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <!-- Icons -->
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>

    <!-- CSS -->
    <link rel="stylesheet" href="../assets/css/main.css">
</head>

<body>
    <div class="form-container">
        <div class="form-card">
            <div class="form-header">
                <h2>Create Account</h2>
                <p>Join The Mobile Store community</p>
            </div>

            <?php if (isset($_SESSION['error'])): ?>
                <div class="alert alert-error">
                    <ion-icon name="warning-outline" class="alert-icon"></ion-icon>
                    <?= $_SESSION['error'] ?>
                    <button class="alert-close" onclick="this.parentElement.remove()">&times;</button>
                </div>
                <?php unset($_SESSION['error']); ?>
            <?php endif; ?>

            <?php if (isset($_SESSION['success'])): ?>
                <div class="alert alert-success">
                    <ion-icon name="checkmark-outline" class="alert-icon"></ion-icon>
                    <?= $_SESSION['success'] ?>
                    <button class="alert-close" onclick="this.parentElement.remove()">&times;</button>
                </div>
                <?php unset($_SESSION['success']); ?>
            <?php endif; ?>

            <form method="POST" class="auth-form">
                <div class="form-group">
                    <label for="fullname" class="form-label">Full Name</label>
                    <div class="form-input">
                        <ion-icon name="person-outline"></ion-icon>
                        <input type="text" id="fullname" name="fullname" required placeholder="Your full name" value="<?= isset($_POST['fullname']) ? htmlspecialchars($_POST['fullname']) : '' ?>">
                    </div>
                </div>

                <div class="form-group">
                    <label for="email" class="form-label">Email</label>
                    <div class="form-input">
                        <ion-icon name="mail-outline"></ion-icon>
                        <input type="email" id="email" name="email" required placeholder="Your email address" value="<?= isset($_POST['email']) ? htmlspecialchars($_POST['email']) : '' ?>">
                    </div>
                </div>

                <div class="form-group">
                    <label for="password" class="form-label">Password</label>
                    <div class="form-input">
                        <ion-icon name="lock-closed-outline"></ion-icon>
                        <input type="password" id="password" name="password" required placeholder="Create a password">
                    </div>
                </div>

                <div class="form-group">
                    <label for="confirm_password" class="form-label">Confirm Password</label>
                    <div class="form-input">
                        <ion-icon name="lock-closed-outline"></ion-icon>
                        <input type="password" id="confirm_password" name="confirm_password" required placeholder="Confirm your password">
                    </div>
                </div>

                <button type="submit" class="button">Register</button>
            </form>

            <div class="form-footer">
                <p>Already have an account? <a href="login.php">Sign in</a></p>
            </div>
        </div>
    </div>
</body>

</html>