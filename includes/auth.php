<?php
session_start();
require_once 'config.php';

// Check for remember me cookie at the start
checkRememberMe();

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'register':
                handleRegistration();
                break;
            case 'login':
                handleLogin();
                break;
            case 'logout':
                handleLogout();
                break;
        }
    }
}

// Function to check remember me cookie
function checkRememberMe()
{
    global $conn;

    if (empty($_SESSION['user_id']) && isset($_COOKIE['remember_token'])) {
        $token = $_COOKIE['remember_token'];

        // Get user by token
        $stmt = $conn->prepare("SELECT id, email, fullname, is_admin FROM users WHERE remember_token = ? AND token_expiry > NOW()");
        $stmt->bind_param("s", $token);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 1) {
            $user = $result->fetch_assoc();

            // Set session
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_email'] = $user['email'];
            $_SESSION['user_fullname'] = $user['fullname'];

            // Set admin flag if admin
            if ($user['is_admin']) {
                $_SESSION['is_admin'] = true;
            }

            // Refresh cookie
            setRememberCookie($user['id']);

            // Redirect admin to dashboard
            if ($user['is_admin']) {
                header("Location: ../admin/dashboard.php");
                exit();
            }
        } else {
            // Invalid token - clear cookie
            setcookie('remember_token', '', time() - 3600, "/");
        }
    }
}

// Function to set remember me cookie
function setRememberCookie($user_id)
{
    global $conn;

    $token = bin2hex(random_bytes(32));
    $expiry = time() + (30 * 24 * 60 * 60); // 30 days

    // Store in database
    $stmt = $conn->prepare("UPDATE users SET remember_token = ?, token_expiry = FROM_UNIXTIME(?) WHERE id = ?");
    $stmt->bind_param("sii", $token, $expiry, $user_id);
    $stmt->execute();

    // Set cookie
    setcookie('remember_token', $token, [
        'expires' => $expiry,
        'path' => '/',
        'secure' => false, // Should be true in production with HTTPS
        'httponly' => true,
        'samesite' => 'Strict'
    ]);
}

// Function to validate password
function validatePassword($password)
{
    // Check length (minimum 8 characters)
    if (strlen($password) < 8) {
        return "Password must be at least 8 characters long";
    }

    // Check for allowed characters
    if (!preg_match('/^[A-Za-z0-9!@#$%^&*()_+\-=\[\]{};\':"\\\\|,.<>\/?~]+$/', $password)) {
        return "Password can only contain letters, numbers and these special characters: !@#$%^&*()_+-=[]{};':\"|,.<>/?~";
    }

    return true;
}

// Function to handle user registration
function handleRegistration()
{
    global $conn;

    // Get form data
    $fullname = trim($_POST['fullname']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    $confirm_password = trim($_POST['confirm_password']);

    // Basic validation
    if (empty($fullname) || empty($email) || empty($password) || empty($confirm_password)) {
        $_SESSION['error'] = "All fields are required";
        header("Location: ../user/register.php");
        exit();
    }

    // Validate email
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['error'] = "Invalid email format";
        header("Location: ../user/register.php");
        exit();
    }

    // Validate password
    $passwordValidation = validatePassword($password);
    if ($passwordValidation !== true) {
        $_SESSION['error'] = $passwordValidation;
        header("Location: ../user/register.php");
        exit();
    }

    // Check password match
    if ($password !== $confirm_password) {
        $_SESSION['error'] = "Passwords do not match";
        header("Location: ../user/register.php");
        exit();
    }

    // Check if email exists
    $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $_SESSION['error'] = "Email already registered";
        header("Location: ../user/register.php");
        exit();
    }

    // Hash password
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Insert user
    $stmt = $conn->prepare("INSERT INTO users (fullname, email, password) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $fullname, $email, $hashed_password);

    if ($stmt->execute()) {
        $_SESSION['success'] = "Registration successful! Please login.";
        header("Location: ../user/login.php");
    } else {
        $_SESSION['error'] = "Registration failed. Please try again.";
        header("Location: ../user/register.php");
    }
    exit();
}

// Function to handle user login
function handleLogin()
{
    global $conn;

    // Get form data
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    // Basic validation
    if (empty($email) || empty($password)) {
        $_SESSION['error'] = "Email and password are required";
        header("Location: ../user/login.php");
        exit();
    }

    // Get user/admin and password from database
    $stmt = $conn->prepare("SELECT id, fullname, email, password, is_admin FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        $_SESSION['error'] = "Invalid email or password";
        header("Location: ../user/login.php");
        exit();
    }

    $user = $result->fetch_assoc();

    // Verify password
    if (password_verify($password, $user['password'])) {
        // Set session variables
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_email'] = $user['email'];
        $_SESSION['user_fullname'] = $user['fullname'];

        // Set admin flag if admin
        if ($user['is_admin']) {
            $_SESSION['is_admin'] = true;
        }

        // Remember me functionality
        if (isset($_POST['remember'])) {
            $token = bin2hex(random_bytes(32)); // Secure random token
            $expiry = time() + (30 * 24 * 60 * 60); // 30 days

            // Store token in database
            $stmt = $conn->prepare("UPDATE users SET remember_token = ?, token_expiry = ? WHERE id = ?");
            $stmt->bind_param("ssi", $token, date('Y-m-d H:i:s', $expiry), $user['id']);
            $stmt->execute();

            // Set secure cookie - THIS IS THE CRUCIAL PART
            setcookie('remember_token', $token, [
                'expires' => $expiry,
                'path' => '/',
                'secure' => false, // Should be true in production with HTTPS
                'httponly' => true,
                'samesite' => 'Lax'
            ]);
        }

        // Check if admin
        if ($user['is_admin']) {
            header("Location: ../admin/dashboard.php");
        } else {
            header("Location: ../index.php");
        }
        exit();
    } else {
        $_SESSION['error'] = "Invalid email or password";
        header("Location: ../user/login.php");
        exit();
    }
}

// Function to handle logout
function handleLogout()
{
    // Clear session
    $_SESSION = array();
    session_destroy();

    // Clear remember me cookie
    if (isset($_COOKIE['remember_token'])) {
        setcookie('remember_token', '', time() - 3600, "/");
    }

    header("Location: ../index.php");
    exit();
}
