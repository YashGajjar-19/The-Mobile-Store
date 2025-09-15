<?php
// This script should be included at the top of pages you want to protect
// or on pages where you want to auto-login a user.

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
require_once 'config.php';

// If user is not logged in, check for a "Remember Me" cookie
if (!isset($_SESSION['user_id']) && isset($_COOKIE['remember_me'])) {
    list($selector, $validator) = explode(':', $_COOKIE['remember_me']);

    if (!empty($selector) && !empty($validator)) {
        // Look up the selector in the database
        $stmt = $conn->prepare("SELECT * FROM user_tokens WHERE selector = ? AND expires >= NOW()");
        $stmt->bind_param("s", $selector);
        $stmt->execute();
        $result = $stmt->get_result();
        $token_data = $result->fetch_assoc();
        $stmt->close();

        if ($token_data) {
            // Hash the validator from the cookie and compare it to the one in the database
            $hashed_validator_from_cookie = hash('sha256', $validator);

            if (hash_equals($token_data['hashed_validator'], $hashed_validator_from_cookie)) {
                // If the token is valid, log the user in
                $user_stmt = $conn->prepare("SELECT id, full_name, is_admin FROM users WHERE id = ?");
                $user_stmt->bind_param("i", $token_data['user_id']);
                $user_stmt->execute();
                $user_result = $user_stmt->get_result();
                $user = $user_result->fetch_assoc();
                $user_stmt->close();

                // Set session variables
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['full_name'] = $user['full_name'];
                $_SESSION['is_admin'] = $user['is_admin'];
            }
        }
    }
}
