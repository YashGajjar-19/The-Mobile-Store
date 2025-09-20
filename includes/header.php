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
$base_path = in_array($current_dir, ['products', 'user', 'includes']) ? '../' : './';

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

    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Rounded:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>
    <link rel="stylesheet" href="<?php echo $base_path; ?>assets/css/main.css">
</head>

<body>
    <section class="header-container" id="header-section">
        <header class="header">
            <a href="<?php echo $base_path; ?>index.php" class="logo-container">
                <img class="logo-image" src="<?php echo $base_path; ?>assets/images/Logo.png" alt="The Mobile Store">
                <span class="logo-text">The Mobile Store</span>
            </a>
            <div class="nav-wrapper">
                <nav class="navbar">
                    <form action="<?php echo $base_path; ?>products/search.php" method="GET" class="search-container">
                        <input type="search" name="query" class="search-input" placeholder="Search..." required>
                        <button type="submit" class="nav-icon search-btn" style="border:none; background:transparent; cursor:pointer;" title="Search">
                            <span class="material-symbols-rounded">search</span>
                        </button>
                    </form>
                    <a href="<?php echo $base_path; ?>index.php" class="nav-icon" title="Home">
                        <span class="material-symbols-rounded">home</span>
                    </a>
                    <a href="<?php echo $base_path; ?>cart.php" class="nav-icon" title="View Cart">
                        <span class="material-symbols-rounded">shopping_cart</span>
                        <?php if ($cart_count > 0): ?>
                            <span class="cart-badge"><?php echo $cart_count; ?></span>
                        <?php endif; ?>
                    </a>
                    <a href="<?php echo $account_link; ?>" class="nav-icon" title="My Account">
                        <span class="material-symbols-rounded">account_circle</span>
                    </a>
                    <?php if (isset($_SESSION['is_admin']) && $_SESSION['is_admin']): ?>
                        <a href="<?php echo $base_path; ?>admin/dashboard.php" class="nav-icon" title="Admin Dashboard">
                            <span class="material-symbols-rounded">admin_panel_settings</span>
                        </a>
                    <?php endif; ?>
                </nav>

                <button class="mobile-menu-btn">
                    <span class="material-symbols-rounded">
                        menu
                    </span>
                </button>
            </div>

            <div class="mobile-menu">
                <form action="<?php echo $base_path; ?>products/search.php" method="GET" class="mobile-search-container">
                    <input type="search" name="query" class="mobile-search-input" placeholder="Search..." required>
                    <button type="submit" class="mobile-search-btn" title="Search">
                        <span class="material-symbols-rounded">search</span>
                    </button>
                </form>
                <a href="<?php echo $base_path; ?>index.php" class="mobile-nav-icon" title="Home">
                    <span class="material-symbols-rounded">home</span>
                    <span>Home</span>
                </a>
                <a href="<?php echo $base_path; ?>cart.php" class="mobile-nav-icon" title="View Cart">
                    <span class="material-symbols-rounded">shopping_cart</span>
                    <span>Cart</span>
                    <?php if ($cart_count > 0): ?>
                        <span class="cart-badge"><?php echo $cart_count; ?></span>
                    <?php endif; ?>
                </a>
                <a href="<?php echo $account_link; ?>" class="mobile-nav-icon" title="My Account">
                    <span class="material-symbols-rounded">account_circle</span>
                    <span><?php echo $account_text; ?></span>
                </a>
                <?php if (isset($_SESSION['is_admin']) && $_SESSION['is_admin']): ?>
                    <a href="<?php echo $base_path; ?>admin/dashboard.php" class="mobile-nav-icon" title="Admin Dashboard">
                        <span class="material-symbols-rounded">admin_panel_settings</span>
                        <span>Dashboard</span>
                    </a>
                <?php endif; ?>
                <?php if (isset($_SESSION['user_id'])): ?>
                    <a href="<?php echo $base_path; ?>includes/auth.php?action=logout" class="mobile-nav-icon" title="Logout">
                        <span class="material-symbols-rounded">logout</span>
                        <span>Logout</span>
                    </a>
                <?php endif; ?>
            </div>
        </header>
    </section>