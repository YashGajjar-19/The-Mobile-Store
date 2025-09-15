<?php
session_start();
require_once 'config.php';

// --- Logout Logic ---
if (isset($_GET['action']) && $_GET['action'] == 'logout') {
    // Also delete the remember me cookie and database token
    if (isset($_COOKIE['remember_me'])) {
        list($selector, $validator) = explode(':', $_COOKIE['remember_me']);
        $stmt = $conn->prepare("DELETE FROM user_tokens WHERE selector = ?");
        $stmt->bind_param("s", $selector);
        $stmt->execute();
        $stmt->close();
        setcookie('remember_me', '', time() - 3600, '/'); // Expire the cookie
    }

    $_SESSION = array();
    session_destroy();
    header("Location: ../index.php");
    exit();
}

// --- Registration Logic ---
// ... (Your existing registration code remains unchanged) ...
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['fullname'])) {
    $fullname = $_POST['fullname'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    // Password validation
    if (strlen($password) < 8) {
        header("Location: ../user/register.php?error=passwords-length");
        exit();
    }
    if (!preg_match('/[A-Z]/', $password)) {
        header("Location: ../user/register.php?error=passwords-uppercase");
        exit();
    }
    if (!preg_match('/[a-z]/', $password)) {
        header("Location: ../user/register.php?error=passwords-lowercase");
        exit();
    }
    if (!preg_match('/[0-9]/', $password)) {
        header("Location: ../user/register.php?error=passwords-number");
        exit();
    }
    if (!preg_match('/[^A-Za-z0-9]/', $password)) {
        header("Location: ../user/register.php?error=passwords-special");
        exit();
    }
    if ($password !== $confirm_password) {
        header("Location: ../user/register.php?error=passwordmismatch");
        exit();
    }


    // Check if email already exists
    $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        header("Location: ../user/register.php?error=emailtaken");
        exit();
    }

    // Hash the password
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Insert new user into the database
    $stmt = $conn->prepare("INSERT INTO users (full_name, email, password) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $fullname, $email, $hashed_password);

    if ($stmt->execute()) {
        header("Location: ../user/login.php?success=registered");
        exit();
    } else {
        header("Location: ../user/register.php?error=dberror");
        exit();
    }

    $stmt->close();
}


// --- Login Logic ---
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['login'])) {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT id, full_name, password, is_admin FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $stmt->bind_result($id, $fullname, $hashed_password, $is_admin);
        $stmt->fetch();

        if (password_verify($password, $hashed_password)) {
            $_SESSION['user_id'] = $id;
            $_SESSION['full_name'] = $fullname;
            $_SESSION['is_admin'] = $is_admin;

            // --- REMEMBER ME LOGIC ---
            if (isset($_POST['remember'])) {
                $selector = bin2hex(random_bytes(16));
                $validator = bin2hex(random_bytes(32));
                $expires = date('Y-m-d H:i:s', time() + (86400 * 30)); // 30 days
                $hashed_validator = hash('sha256', $validator);

                // Insert token into the database
                $token_stmt = $conn->prepare("INSERT INTO user_tokens (user_id, selector, hashed_validator, expires) VALUES (?, ?, ?, ?)");
                $token_stmt->bind_param("isss", $id, $selector, $hashed_validator, $expires);
                $token_stmt->execute();
                $token_stmt->close();

                // Set the cookie
                setcookie('remember_me', $selector . ':' . $validator, time() + (86400 * 30), '/');
            }
            // --- END REMEMBER ME LOGIC ---

            if ($is_admin) {
                header("Location: ../admin/dashboard.php");
            } else {
                header("Location: ../index.php");
            }
            exit();
        } else {
            header("Location: ../user/login.php?error=invalidpassword");
            exit();
        }
    } else {
        header("Location: ../user/login.php?error=usernotfound");
        exit();
    }

    $stmt->close();
}

$conn->close();
