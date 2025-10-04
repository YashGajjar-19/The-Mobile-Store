<?php
session_start();
require_once '../includes/config.php';

function generate_token($user_id)
{
    $secret_key = SECRET_KEY;
    $header = json_encode(['typ' => 'JWT', 'alg' => 'HS256']);

    // Token expires in 10 minutes (600 seconds)
    $payload = json_encode(['user_id' => $user_id, 'exp' => time() + 600]);

    $base64_header = str_replace(['+', '=', '/'], ['-', '', '_'], base64_encode($header));
    $base64_payload = str_replace(['+', '=', '/'], ['-', '', '_'], base64_encode($payload));

    $signature = hash_hmac('sha256', $base64_header . '.' . $base64_payload, $secret_key, true);
    $base64_signature = str_replace(['+', '=', '/'], ['-', '', '_'], base64_encode($signature));

    return $base64_header . '.' . $base64_payload . '.' . $base64_signature;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {

    if ($_POST['action'] === 'verify_user') {
        $email = $_POST['email'];
        $phone_number = $_POST['phone_number'];

        $stmt = $conn->prepare("SELECT id FROM users WHERE email = ? AND phone_number = ?");
        $stmt->bind_param("ss", $email, $phone_number);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 1) {
            $user = $result->fetch_assoc();
            // User verified, create a temporary token and store it in the session
            $_SESSION['reset_token'] = generate_token($user['id']);
            header("Location: ../user/reset-password.php");
            exit();
        } else {
            // Verification failed
            header("Location: ../user/forgot-password.php?error=notfound");
            exit();
        }
    } elseif ($_POST['action'] === 'reset_password') {
        $user_id = $_POST['user_id'];
        $new_password = $_POST['new_password'];
        $confirm_password = $_POST['confirm_password'];

        if ($new_password !== $confirm_password) {
            // Redirect back with an error. We need to regenerate the token to allow a retry.
            $_SESSION['reset_token'] = generate_token($user_id);
            header("Location: ../user/reset-password.php?status=mismatch");
            exit();
        }

        // Hash the new password and update the database
        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
        $stmt = $conn->prepare("UPDATE users SET password = ? WHERE id = ?");
        $stmt->bind_param("si", $hashed_password, $user_id);

        if ($stmt->execute()) {
            header("Location: ../user/login.php?status=pw_reset_success");
        } else {
            header("Location: ../user/reset-password.php?status=error");
        }
        exit();
    }
}