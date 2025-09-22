<?php
session_start();
require_once '../includes/config.php';

// Security Check
if (!isset($_SESSION['user_id']) || !$_SESSION['is_admin']) {
    header("Location: ../user/login.php?error=unauthorized");
    exit();
}

// Fetch all orders
$orders_stmt = $conn->prepare("
    SELECT o.order_id, u.full_name, o.total_amount, o.status, o.order_date 
    FROM orders o JOIN users u ON o.user_id = u.id
    ORDER BY o.order_date DESC
");
$orders_stmt->execute();
$orders_result = $orders_stmt->get_result();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>All Orders | Admin</title>
    <link rel="stylesheet" href="../assets/css/main.css">
    <style>
        .action-buttons form {
            display: inline-block;
            margin-right: 5px;
        }

        .button-small {
            padding: 6px 12px;
            font-size: 0.8rem;
            border-radius: 20px;
            border: none;
            cursor: pointer;
            color: white;
            text-decoration: none;
            display: inline-block;
            min-width: 80px;
            text-align: center;
        }

        .approve {
            background-color: var(--green);
            box-shadow:
                0px 0px 20px rgba(74, 255, 71, 0.25),
                0px 5px 5px -1px rgba(58, 233, 67, 0.25),
                inset 4px 4px 8px rgba(175, 255, 178, 0.5),
                inset -4px -4px 8px rgba(19, 216, 45, 0.25);
            backdrop-filter: blur(5px);
            -webkit-backdrop-filter: blur(5px);
        }

        .decline {
            background-color: var(--red);
            box-shadow:
                0px 0px 20px rgba(255, 71, 71, 0.25),
                0px 5px 5px -1px rgba(233, 58, 58, 0.25),
                inset 4px 4px 8px rgba(255, 175, 175, 0.5),
                inset -4px -4px 8px rgba(216, 19, 19, 0.35);
            backdrop-filter: blur(5px);
            -webkit-backdrop-filter: blur(5px);
        }

        .shipped {
            background-color: var(--yellow);
            color: var(--dark);
            box-shadow:
                0px 0px 20px rgba(255, 255, 71, 0.25),
                0px 5px 5px -1px rgba(233, 233, 58, 0.25),
                inset 4px 4px 8px rgba(254, 255, 175, 0.5),
                inset -4px -4px 8px rgba(213, 216, 19, 0.35);
            backdrop-filter: blur(5px);
            -webkit-backdrop-filter: blur(5px);
        }

        .delivered {
            background-color: var(--blue);
            box-shadow:
                0px 0px 20px rgba(71, 184, 255, 0.25),
                0px 5px 5px -1px rgba(58, 125, 233, 0.25),
                inset 4px 4px 8px rgba(175, 230, 255, 0.5),
                inset -4px -4px 8px rgba(19, 95, 216, 0.35);
            backdrop-filter: blur(5px);
            -webkit-backdrop-filter: blur(5px);
        }
    </style>
</head>

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
                                        <form action="../includes/order_status.php" method="POST">
                                            <input type="hidden" name="order_id" value="<?php echo $order['order_id']; ?>">
                                            <button type="submit" name="action" value="Approved" class="button-small approve">Approve</button>
                                        </form>
                                        <form action="../includes/order_status.php" method="POST">
                                            <input type="hidden" name="order_id" value="<?php echo $order['order_id']; ?>">
                                            <button type="submit" name="action" value="Declined" class="button-small decline">Decline</button>
                                        </form>
                                    <?php elseif ($order['status'] == 'Approved'): ?>
                                        <form action="../includes/order_status.php" method="POST">
                                            <input type="hidden" name="order_id" value="<?php echo $order['order_id']; ?>">
                                            <button type="submit" name="action" value="Shipped" class="button-small shipped">Ship</button>
                                        </form>
                                    <?php elseif ($order['status'] == 'Shipped'): ?>
                                        <form action="../includes/order_status.php" method="POST">
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

</html>