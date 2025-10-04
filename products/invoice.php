<?php
$page_title = 'Invoice | The Mobile Store';
require_once '../includes/header.php';
require_once '../includes/navbar.php';
require_once '../includes/config.php';

// Check for a valid order_id in the URL
if (!isset($_GET['order_id']) || empty($_GET['order_id'])) {
    echo "<h1>Error: No Order ID was provided.</h1>";
    require_once '../includes/footer.php';
    exit();
}

$order_id = $_GET['order_id'];

// Fetch the main order details
$order_query = "SELECT * FROM `orders` WHERE `order_id` = ?";
$stmt = $conn->prepare($order_query);

if ($stmt === false) {
    die("Database query failed: " . htmlspecialchars($conn->error));
}

$stmt->bind_param("i", $order_id);
$stmt->execute();
$order_result = $stmt->get_result();
$order = $order_result->fetch_assoc();

// If no order is found, display an error
if (!$order) {
    echo "<h1>Error: Order with ID #" . htmlspecialchars($order_id) . " was not found.</h1>";
    require_once '../includes/footer.php';
    exit();
}

// Fetch order items
$order_items_query = "
    SELECT oi.quantity, oi.price_per_item, p.product_name 
    FROM `order_items` oi 
    LEFT JOIN `product_variants` pv ON oi.variant_id = pv.variant_id
    LEFT JOIN `products` p ON pv.product_id = p.product_id 
    WHERE oi.order_id = ?";

$stmt_items = $conn->prepare($order_items_query);

if ($stmt_items === false) {
    die("Database query for items failed: " . htmlspecialchars($conn->error));
}

$stmt_items->bind_param("i", $order_id);
$stmt_items->execute();
$order_items_result = $stmt_items->get_result();
?>

<div class="invoice-container">
    <div class="invoice-header">
        <img src="../assets/images/Logo.png" alt="The Mobile Store" class="invoice-logo">
        <h1>Invoice</h1>
    </div>

    <div class="invoice-details">
        <div class="invoice-content">
            <h2>Billed To:</h2>
            
            <p>
                <?php echo nl2br(htmlspecialchars(trim($order['shipping_address']))); ?>
            </p>
        </div>
        
        <div style="text-align: right;">
            <h2>Invoice Details:</h2>

            <p>
                <strong>Invoice #:</strong>
                INV-
                <?php echo str_pad(htmlspecialchars($order['order_id']), 4, '0', STR_PAD_LEFT); ?>
            </p>

            <p>
                <strong>Order Date:</strong>
                <?php echo htmlspecialchars(date("F j, Y", strtotime($order['order_date']))); ?>
            </p>

            <p>
                <strong>Payment Method:</strong>
                <?php echo htmlspecialchars($order['payment_method']); ?>
            </p>
        </div>
    </div>

    <table class="invoice-table">
        <thead>
            <tr>
                <th>Product</th>
                <th>Quantity</th>
                <th>Unit Price</th>
                <th style="text-align: right;">Total</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($order_items_result->num_rows > 0): ?>
                <?php while ($item = $order_items_result->fetch_assoc()): ?>
                    <tr>
                        <td>
                            <?php
                            $product_name = $item['product_name'] ?? '[Product Removed]';
                            echo htmlspecialchars($product_name);
                            ?>
                        </td>
                        <td><?php echo htmlspecialchars($item['quantity']); ?></td>
                        <td>&#8377;<?php echo htmlspecialchars(number_format($item['price_per_item'], 2)); ?></td>
                        <td style="text-align: right;">&#8377;<?php echo htmlspecialchars(number_format($item['quantity'] * $item['price_per_item'], 2)); ?></td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="4" style="text-align: center;">No items found for this order.</td>
                </tr>
            <?php endif; ?>
        </tbody>
        <tfoot>
            <tr>
                <td colspan="2"></td>
                <td class="total-label">Grand Total</td>
                <td class="total-amount">&#8377;<?php echo htmlspecialchars(number_format($order['total_amount'], 2)); ?></td>
            </tr>
        </tfoot>
    </table>

    <div class="invoice-footer">
        <p>Thank you for shopping with The Mobile Store!</p>
        <p>This is a computer-generated invoice and does not require a signature.</p>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>