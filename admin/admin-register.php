<?php
require_once '../includes/config.php';
require_once '../includes/auth.php';

// Check if admin already exists
$stmt = $conn->prepare("SELECT id FROM users WHERE is_admin = TRUE");
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows > 0) {
    header("Location: ../user/login.php");
    exit();
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fullname = trim($_POST['fullname']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    $confirm_password = trim($_POST['confirm_password']);

    // Validate inputs
    if (empty($fullname) || empty($email) || empty($password) || empty($confirm_password)) {
        $_SESSION['error'] = "All fields are required";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['error'] = "Invalid email format";
    } elseif ($password !== $confirm_password) {
        $_SESSION['error'] = "Passwords do not match";
    } else {
        // Validate password strength
        $passwordValidation = validatePassword($password);
        if ($passwordValidation !== true) {
            $_SESSION['error'] = $passwordValidation;
        } else {
            // Hash password
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);

            // Create admin account
            $stmt = $conn->prepare("INSERT INTO users (fullname, email, password, is_admin, admin_registered) VALUES (?, ?, ?, TRUE, TRUE)");
            $stmt->bind_param("sss", $fullname, $email, $hashed_password);

            if ($stmt->execute()) {
                $_SESSION['success'] = "Admin registration successful! Please login.";
                header("Location: ./user/login.php");
                exit();
            } else {
                $_SESSION['error'] = "Registration failed. Please try again.";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Registration | The Mobile Store</title>
    <link rel="stylesheet" href="../assets/css/main.css">
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
</head>

<body>
    <div class="form-container">
        <div class="form-card">
            <div class="form-header">
                <h2>Admin Registration</h2>
                <p>Create the primary admin account</p>
            </div>

            <?php if (isset($_SESSION['error'])): ?>
                <div class="alert alert-error">
                    <ion-icon name="warning-outline" class="alert-icon"></ion-icon>
                    <?= $_SESSION['error'] ?>
                    <button class="alert-close" onclick="this.parentElement.remove()">&times;</button>
                </div>
                <?php unset($_SESSION['error']); ?>
            <?php endif; ?>

            <form method="POST" class="auth-form">
                <div class="form-group">
                    <label for="fullname" class="form-label">Full Name</label>
                    <div class="form-input">
                        <ion-icon name="person-outline"></ion-icon>
                        <input type="text" id="fullname" name="fullname" required placeholder="Admin's full name">
                    </div>
                </div>

                <div class="form-group">
                    <label for="email" class="form-label">Email</label>
                    <div class="form-input">
                        <ion-icon name="mail-outline"></ion-icon>
                        <input type="email" id="email" name="email" required placeholder="Admin email">
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
                        <input type="password" id="confirm_password" name="confirm_password" required placeholder="Confirm password">
                    </div>
                </div>

                <button type="submit" class="button">Register Admin</button>
            </form>
        </div>
    </div>
</body>

</html>