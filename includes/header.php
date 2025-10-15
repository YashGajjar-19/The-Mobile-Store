<?php

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

require_once 'config.php';

$cart_count = 0;
if (isset($_SESSION['user_id'])) {
    $stmt = $conn->prepare("SELECT SUM(quantity) as total FROM cart WHERE user_id = ?");
    $stmt->bind_param("i", $_SESSION['user_id']);
    $stmt->execute();
    $result = $stmt->get_result()->fetch_assoc();
    $cart_count = $result['total'] ?? 0;
}


// Determine account link and text
$current_dir = basename(dirname($_SERVER['PHP_SELF']));

// Base path for assets and links
$base_path = in_array($current_dir, ['products', 'user', 'includes', 'pages', 'admin']) ? '../' : './';

if (isset($_SESSION['user_id'])) {
    // Correct the path for files inside subdirectories
    $account_link = $base_path . 'user/profile.php';
    $account_text = 'Account';
} else {
    $account_link = $base_path . 'user/login.php';
    $account_text = 'Login / Register';
}

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

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <title><?php echo isset($page_title) ? $page_title : 'The Mobile Store'; ?></title>

    <script>
        const BASE_URL = "<?php echo SITE_URL; ?>";
    </script>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" crossorigin="anonymous" referrerpolicy="no-referrer" />
    
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>
    
    <link rel="stylesheet" href="<?php echo $base_path; ?>assets/css/main.css">
</head>