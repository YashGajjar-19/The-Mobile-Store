<?php
session_start();
require_once '../includes/config.php';

// Security Check
if (!isset($_SESSION['user_id']) || !$_SESSION['is_admin']) {
    header("Location: ../user/login.php?error=unauthorized");
    exit();
}

// --- Fetch Dashboard Data (Limiting to 5) ---
$total_users = $conn->query("SELECT COUNT(id) as total FROM users WHERE is_admin = 0")->fetch_assoc()['total'];
$total_orders = $conn->query("SELECT COUNT(order_id) as total FROM orders")->fetch_assoc()['total'];
$message_count = $conn->query("SELECT COUNT(message_id) as total FROM contact_messages")->fetch_assoc()['total'];


$recent_users = $conn->query("SELECT full_name, email, created_at FROM users WHERE is_admin = 0 ORDER BY created_at DESC LIMIT 5");
$recent_orders = $conn->query("SELECT o.order_id, u.full_name, o.total_amount, o.status, o.order_date FROM orders o JOIN users u ON o.user_id = u.id ORDER BY o.order_date DESC LIMIT 5");
$recent_products = $conn->query("SELECT p.product_name, b.brand_name FROM products p JOIN brands b ON p.brand_id = b.brand_id ORDER BY p.created_at DESC LIMIT 5");

// Determine account link
if (isset($_SESSION['user_id'])) {
    $account_link = '../user/profile.php';
} else {
    $account_link = '../user/login.php';
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard | The Mobile Store</title>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Rounded:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" rel="stylesheet" />
    <link rel="stylesheet" href="../assets/css/main.css">
</head>

<body>
    <section class="header-container" id="header-section">
        <header class="header admin-header">
            <a href="../index.php" class="logo-container">
                <img class="logo-image" src="../assets/images/Logo.png" alt="The Mobile Store">
                <span class="logo-text">The Mobile Store</span>
            </a>
            <div class="nav-wrapper">
                <nav class="navbar">
                    <a href="../index.php" class="nav-icon" title="View Storefront">
                        <span class="material-symbols-rounded">storefront</span>
                    </a>
                    <a href="messages.php" class="nav-icon" title="View Messages">
                        <span class="material-symbols-rounded">notifications_active
                        </span>
                        <?php if ($message_count > 0): ?>
                            <span class="cart-badge"><?php echo $message_count; ?></span>
                        <?php endif; ?>
                    </a>
                    <a href="<?php echo $account_link; ?>" class="nav-icon" title="My Account">
                        <span class="material-symbols-rounded">account_circle</span>
                    </a>
                </nav>
                <button class="mobile-menu-btn" id="sidebar-toggle-mobile">
                    <span class="material-symbols-rounded">menu</span>
                </button>
            </div>
        </header>
    </section>

    <aside class="sidebar">
        <div class="sidebar-logo-container">
            <img class="sidebar-logo-image" src="../assets/images/svg/admin.svg" alt="Admin Profile">
            <span class="sidebar-logo-text"><?php echo htmlspecialchars($_SESSION['full_name']); ?></span>
        </div>
        <nav class="sidebar-nav">
            <ul class="sidebar-nav-list">
                <li class="sidebar-nav-item"><a href="#" class="sidebar-nav-link active"><span class="material-symbols-rounded">dashboard</span><span>Dashboard</span></a></li>
                <li class="sidebar-nav-item"><a href="manage_products.php" class="sidebar-nav-link"><span class="material-symbols-rounded">shopping_bag</span><span>Products</span></a></li>
                <li class="sidebar-nav-item"><a href="./orders.php" class="sidebar-nav-link"><span class="material-symbols-rounded">receipt_long</span><span>Orders</span></a></li>
                <li class="sidebar-nav-item"><a href="./users.php" class="sidebar-nav-link"><span class="material-symbols-rounded">group</span><span>Users</span></a></li>
                <li class="sidebar-nav-item" style="margin-top: auto;"><a href="../includes/auth.php?action=logout" class="sidebar-nav-link"><span class="material-symbols-rounded">logout</span><span>Logout</span></a></li>
            </ul>
        </nav>
    </aside>

    <main class="main-content">
        <div class="dashboard-header-top">
            <div class="dashboard-title-group">
                <h2>Dashboard</h2>
                <div class="title-line"></div>
            </div>
            <a href="manage_products.php" class="button"><span class="material-symbols-rounded">build</span>Manage</a>
        </div>

        <div class="dashboard-cards">
            <div class="summary-card">
                <div class="card-icon"><span class="material-symbols-rounded" style="color: lightseagreen; border: 1px Solid lightseagreen;">group</span></div>
                <div class="card-info">
                    <p>Total Users</p>
                    <h3><?php echo $total_users; ?></h3>
                </div>
            </div>
            <div class="summary-card">
                <div class="card-icon"><span class="material-symbols-rounded" style="color: yellow; border: 1px Solid yellow;">receipt_long</span></div>
                <div class=" card-info">
                    <p>Total Orders</p>
                    <h3><?php echo $total_orders; ?></h3>
                </div>
            </div>
        </div>

        <div class="orders-card">
            <div class="orders-header">
                <h2>Recent Products</h2><a href="products.php" class="button buy-button">View All</a>
            </div>
            <div class="orders-table-container">
                <table>
                    <thead>
                        <tr>
                            <th>Product Name</th>
                            <th>Brand</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($product = $recent_products->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($product['product_name']); ?></td>
                                <td><?php echo htmlspecialchars($product['brand_name']); ?></td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="orders-card" style="margin-top:30px;">
            <div class="orders-header">
                <h2>Recent Orders</h2>
                <a href="orders.php" class="button buy-button">View All</a>
            </div>
            <div class="orders-table-container">
                <table>
                    <thead>
                        <tr>
                            <th>Order ID</th>
                            <th>Customer</th>
                            <th>Amount</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($order = $recent_orders->fetch_assoc()): ?>
                            <tr>
                                <td>#<?php echo $order['order_id']; ?></td>
                                <td><?php echo htmlspecialchars($order['full_name']); ?></td>
                                <td>&#8377;<?php echo number_format($order['total_amount']); ?></td>
                                <td><span class="status <?php echo strtolower(htmlspecialchars($order['status'])); ?>"><?php echo htmlspecialchars($order['status']); ?></span></td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="orders-card" style="margin-top:30px;">
            <div class="orders-header">
                <h2>Recent Users</h2><a href="users.php" class="button buy-button">View All</a>
            </div>
            <div class="orders-table-container">
                <table>
                    <thead>
                        <tr>
                            <th>Full Name</th>
                            <th>Email</th>
                            <th>Date Registered</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($user = $recent_users->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($user['full_name']); ?></td>
                                <td><?php echo htmlspecialchars($user['email']); ?></td>
                                <td><?php echo date('M d, Y', strtotime($user['created_at'])); ?></td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>
</body>

</html>