<?php
session_start();
require_once '../includes/config.php';

// Redirect if not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$user_name = $_SESSION['full_name'];

// Fetch user's profile details
$stmt_user = $conn->prepare("SELECT full_name, email, phone_number, address FROM users WHERE id = ?");
$stmt_user->bind_param("i", $user_id);
$stmt_user->execute();
$user_data = $stmt_user->get_result()->fetch_assoc();
$stmt_user->close();

// Fetch user's recent orders
$stmt_orders = $conn->prepare("SELECT order_id, order_date, total_amount, status FROM orders WHERE user_id = ? ORDER BY order_date DESC LIMIT 5");
$stmt_orders->bind_param("i", $user_id);
$stmt_orders->execute();
$orders = $stmt_orders->get_result();
$stmt_orders->close();

// Wishlist items
$stmt_wishlist = $conn->prepare("
    SELECT
        p.product_name,
        MIN(pv.price) as starting_price,
        (SELECT image_url FROM product_images pi JOIN product_variants pv_img ON pi.variant_id = pv_img.variant_id WHERE pv_img.product_id = p.product_id AND pi.is_thumbnail = 1 LIMIT 1) as image_url
    FROM wishlist w
    JOIN products p ON w.product_id = p.product_id
    LEFT JOIN product_variants pv ON p.product_id = pv.product_id
    WHERE w.user_id = ?
    GROUP BY p.product_id
");
$stmt_wishlist->bind_param("i", $user_id);
$stmt_wishlist->execute();
$wishlist_items = $stmt_wishlist->get_result();
$stmt_wishlist->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Account | The Mobile Store</title>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Rounded:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" rel="stylesheet" />
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>
    <link rel="stylesheet" href="../assets/css/main.css">
    <style>
        .profile-container {
            display: flex;
            gap: 30px;
            max-width: 1350px;
            margin: 120px auto 40px;
            padding: 0 20px;
        }

        .profile-sidebar {
            flex: 0 0 250px;
        }

        .profile-content {
            flex-grow: 1;
            display: flex;
            flex-direction: column;
            gap: 30px;
        }

        .profile-card {
            background: var(--body);
            border-radius: 18px;
            padding: 30px;
            box-shadow: var(--shadow);
        }
    </style>
</head>

<body>
    <section class="header-container" id="header-section">
        <header class="header">
            <a href="./index.php" class="logo-container">
                <img class="logo-image" src="../assets/images/Logo.png" alt="The Mobile Store">
                <span class="logo-text">The Mobile Store</span>
            </a>
            <div class="nav-wrapper">
                <nav class="navbar">
                    <div class="search-container">
                        <input type="search" class="search-input" placeholder="Search...">
                        <a class="nav-icon search-btn">
                            <span class="material-symbols-rounded">search</span>
                        </a>
                    </div>
                    <a href="../index.php" class="nav-icon">
                        <span class="material-symbols-rounded">home</span>
                    </a>
                    <a href="#" class="nav-icon">
                        <span class="material-symbols-rounded">shopping_cart</span>
                        <span class="cart-badge">3</span>
                    </a>
                </nav>

                <button class="mobile-menu-btn">
                    <span class="material-symbols-rounded">
                        menu
                    </span>
                </button>
            </div>

            <div class="mobile-menu">
                <div class="mobile-search-container">
                    <input type="search" class="mobile-search-input" placeholder="Search...">
                    <button class="mobile-search-btn">
                        <span class="material-symbols-rounded">search</span>
                    </button>
                </div>
                <a href="../index.php" class="mobile-nav-icon">
                    <span class="material-symbols-rounded">home</span>
                    <span>Home</span>
                </a>
                <a href="#" class="mobile-nav-icon">
                    <span class="material-symbols-rounded">shopping_cart</span>
                    <span>Cart</span>
                    <span class="cart-badge">3</span>
                </a>
                <a href="<?php echo $account_link; ?>" class="mobile-nav-icon">
                    <span class="material-symbols-rounded">account_circle</span>
                    <span><?php echo $account_text; ?></span>
                </a>
                <?php if (isset($_SESSION['user_id'])): ?>
                    <a href="./includes/auth.php?action=logout" class="mobile-nav-icon">
                        <span class="material-symbols-rounded">logout</span>
                        <span>Logout</span>
                    </a>
                <?php endif; ?>
            </div>
        </header>
    </section>

    <main class="profile-container">
        <aside class="profile-sidebar">
            <div class="sidebar" style="position: static; height: auto; transform: none;">
                <div class="sidebar-logo-container">
                    <img src="../assets/images/svg/admin.svg" alt="User Avatar" class="sidebar-logo-image">
                    <span class="sidebar-logo-text"><?php echo htmlspecialchars($user_name); ?></span>
                </div>
                <nav class="sidebar-nav">
                    <ul class="sidebar-nav-list">
                        <li class="sidebar-nav-item">
                            <a href="#profile-details" class="sidebar-nav-link">
                                <span class="material-symbols-rounded">person</span>
                                <span>Profile Details</span>
                            </a>
                        </li>
                        <li class="sidebar-nav-item">
                            <a href="#recent-orders" class="sidebar-nav-link">
                                <span class="material-symbols-rounded">receipt_long</span>
                                <span>Recent Orders</span>
                            </a>
                        </li>
                        <li class="sidebar-nav-item">
                            <a href="#wishlist" class="sidebar-nav-link">
                                <span class="material-symbols-rounded">favorite</span>
                                <span>My Wishlist</span>
                            </a>
                        </li>
                        <li class="sidebar-nav-item">
                            <a href="../includes/auth.php?action=logout" class="sidebar-nav-link">
                                <span class="material-symbols-rounded">logout</span>
                                <span>Logout</span>
                            </a>
                        </li>
                    </ul>
                </nav>
            </div>
        </aside>

        <section class="profile-content">
            <div id="profile-details" class="profile-card">
                <div class="section-title" style="text-align: left; margin-bottom: 30px;">
                    <h2 style="margin-top: 0;">Profile Information</h2>
                    <div class="title-line" style="margin: 10px 0 0 0;"></div>
                </div>
                <form action="../includes/profile_update.php" method="POST" class="auth-form" style="margin: 0;">
                    <div class="form-group">
                        <label for="full_name" class="form-label">Full Name</label>
                        <div class="form-input">
                            <ion-icon name="person-outline"></ion-icon>
                            <input type="text" id="full_name" name="full_name" value="<?php echo htmlspecialchars($user_data['full_name']); ?>" required>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="email" class="form-label">Email</label>
                        <div class="form-input">
                            <ion-icon name="mail-outline"></ion-icon>
                            <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($user_data['email']); ?>" required>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="phone_number" class="form-label">Phone Number</label>
                        <div class="form-input">
                            <ion-icon name="call-outline"></ion-icon>
                            <input type="tel" id="phone_number" name="phone_number" value="<?php echo htmlspecialchars($user_data['phone_number']); ?>">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="address" class="form-label">Address</label>
                        <div class="form-input">
                            <ion-icon name="location-outline" class="icon-textarea"></ion-icon>
                            <textarea id="address" name="address" rows="3" style="width: 100%; padding: 10px 15px 10px 45px; line-height: 1.2rem;"><?php echo htmlspecialchars($user_data['address']); ?></textarea>
                        </div>
                    </div>
                    <div class="form-submit">
                        <button type="submit" class="button" style="width: 100%;">Save Changes</button>
                    </div>
                </form>
            </div>

            <div id="recent-orders" class="profile-card">
                <div class="section-title" style="text-align: left; margin-bottom: 20px;">
                    <h2 style="margin-top: 0;">Recent Orders</h2>
                    <div class="title-line" style="margin: 10px 0 0 0;"></div>
                </div>
                <div class="orders-table-container">
                    <table>
                        <thead>
                            <tr>
                                <th>Order ID</th>
                                <th>Date</th>
                                <th>Status</th>
                                <th>Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($orders->num_rows > 0): ?>
                                <?php while ($order = $orders->fetch_assoc()): ?>
                                    <tr>
                                        <td>#<?php echo $order['order_id']; ?></td>
                                        <td><?php echo date('M d, Y', strtotime($order['order_date'])); ?></td>
                                        <td><span class="status <?php echo strtolower($order['status']); ?>"><?php echo htmlspecialchars($order['status']); ?></span></td>
                                        <td>&#8377;<?php echo number_format($order['total_amount'], 2); ?></td>
                                    </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="4">You have not placed any orders yet.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <div id="wishlist" class="profile-card">
                <div class="section-title" style="text-align: left; margin-bottom: 20px;">
                    <h2 style="margin-top: 0;">My Wishlist</h2>
                    <div class="title-line" style="margin: 10px 0 0 0;"></div>
                </div>
                <div class="orders-table-container">
                    <table>
                        <thead>
                            <tr>
                                <th>Product</th>
                                <th>Starting Price</th>
                            </tr>
                        </thead>
                        <tbody>

                            <?php if ($wishlist_items->num_rows > 0): ?>
                                <?php while ($item = $wishlist_items->fetch_assoc()): ?>
                                    <tr>
                                        <td>
                                            <div style="display: flex; align-items: center; gap: 15px;">
                                                <img src="../assets/images/products/<?php echo htmlspecialchars($item['image_url']); ?>" alt="<?php echo htmlspecialchars($item['product_name']); ?>" style="width: 50px; height: 50px; object-fit: contain; border-radius: 8px;">
                                                <strong><?php echo htmlspecialchars($item['product_name']); ?></strong>
                                            </div>
                                        </td>
                                        <td>&#8377;<?php echo number_format($item['starting_price'], 2); ?></td>
                                    </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="2">Your wishlist is empty.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
    </main>
</body>

</html>