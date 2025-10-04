<?php
session_start();
require_once '../includes/config.php';
require_once '../includes/header.php';

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Security Check
if (!isset($_SESSION['user_id']) || !$_SESSION['is_admin']) {
    header("Location: ../user/login.php?error=unauthorized");
    exit();
}

// Fetch all orders - FIXED: Using the correct column name
$orders_stmt = $conn->prepare("
    SELECT o.order_id, u.full_name, o.total_amount, o.status, o.order_date 
    FROM orders o JOIN users u ON o.user_id = u.id
    ORDER BY o.order_date DESC
");
$orders_stmt->execute();
$orders_result = $orders_stmt->get_result();
?>

<body class="admin-page">
    <main class="main-content" style="margin-left: 50px; margin-top: 50px;">
        <div class="dashboard-header-top">
            <div class="dashboard-title-group">
                <h2>All Orders</h2>
                <div class="title-line"></div>
            </div>
            <a href="dashboard.php" class="button">Back to Dashboard</a>
        </div>

        <div class="orders-card">
            <div class="orders-table-container">
                <table>
                    <thead>
                        <tr>
                            <th>Order ID</th>
                            <th>Customer</th>
                            <th>Amount</th>
                            <th>Status</th>
                            <th>Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>

                    <tbody>
                        <?php while ($order = $orders_result->fetch_assoc()): ?>
                            <tr>
                                <td>#<?php echo $order['order_id']; ?></td>
                                <td><?php echo htmlspecialchars($order['full_name']); ?></td>
                                <td>&#8377;<?php echo number_format($order['total_amount']); ?></td>
                                <td><span class="status <?php echo strtolower(htmlspecialchars($order['status'])); ?>"><?php echo htmlspecialchars($order['status']); ?></span></td>
                                <td><?php echo date('M d, Y', strtotime($order['order_date'])); ?></td>
                                <td class="action-buttons">
                                    <?php if ($order['status'] == 'Pending'): ?>
                                        <form action="../handlers/order_handler.php" method="POST">
                                            <input type="hidden" name="order_id" value="<?php echo $order['order_id']; ?>">
                                            <button type="submit" name="action" value="Approved" class="button-small approve">Approve</button>
                                        </form>

                                        <form action="../handlers/order_handler.php" method="POST">
                                            <input type="hidden" name="order_id" value="<?php echo $order['order_id']; ?>">
                                            <button type="submit" name="action" value="Declined" class="button-small decline">Decline</button>
                                        </form>
                                    <?php elseif ($order['status'] == 'Approved'): ?>
                                        <form action="../handlers/order_handler.php" method="POST">
                                            <input type="hidden" name="order_id" value="<?php echo $order['order_id']; ?>">
                                            <button type="submit" name="action" value="Shipped" class="button-small shipped">Ship</button>
                                        </form>
                                    <?php elseif ($order['status'] == 'Shipped'): ?>
                                        <form action="../handlers/order_handler.php" method="POST">
                                            <input type="hidden" name="order_id" value="<?php echo $order['order_id']; ?>">
                                            <button type="submit" name="action" value="Delivered" class="button-small delivered">Deliver</button>
                                        </form>
                                    <?php else: ?>
                                        -
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>
</body>