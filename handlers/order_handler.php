<?php
session_start();
require_once '../includes/config.php';

// Universal check: User must be logged in for any action in this file.
if (!isset($_SESSION['user_id'])) {
    header('Location: ../user/login.php?error=loginrequired');
    exit();
}

// Universal check: Must be a POST request.
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../index.php');
    exit();
}

$user_id = $_SESSION['user_id'];

// --- LOGIC ROUTER ---
// Decide which action to perform based on the submitted data.

// ACTION 1: Customer is placing a new order from checkout.php
if (isset($_POST['shipping_address']) && isset($_POST['payment_method'])) {
    $shipping_address = trim($_POST['shipping_address']);
    if (empty($shipping_address)) {
        header('Location: ../products/checkout.php?error=address_required');
        exit();
    }

    $conn->begin_transaction();
    try {
        // Fetch cart items to calculate total and ensure cart is not empty
        $cart_items_stmt = $conn->prepare("SELECT c.quantity, pv.variant_id, pv.price FROM cart c JOIN product_variants pv ON c.variant_id = pv.variant_id WHERE c.user_id = ?");
        $cart_items_stmt->bind_param("i", $user_id);
        $cart_items_stmt->execute();
        $cart_items_result = $cart_items_stmt->get_result();

        if ($cart_items_result->num_rows === 0) {
            throw new Exception("Your cart is empty. Cannot place order.");
        }

        $cart_items = [];
        $total_amount = 0;
        while ($item = $cart_items_result->fetch_assoc()) {
            $cart_items[] = $item;
            $total_amount += $item['price'] * $item['quantity'];
        }

        // Create the main order record
        $order_stmt = $conn->prepare("INSERT INTO orders (user_id, total_amount, status, shipping_address) VALUES (?, ?, 'Pending', ?)");
        $order_stmt->bind_param("ids", $user_id, $total_amount, $shipping_address);
        $order_stmt->execute();
        $order_id = $conn->insert_id;

        // Insert each cart item into the order_items table
        $order_item_stmt = $conn->prepare("INSERT INTO order_items (order_id, variant_id, quantity, price_per_item) VALUES (?, ?, ?, ?)");
        foreach ($cart_items as $item) {
            $order_item_stmt->bind_param("iiid", $order_id, $item['variant_id'], $item['quantity'], $item['price']);
            $order_item_stmt->execute();
        }

        // Clear the user's cart
        $clear_cart_stmt = $conn->prepare("DELETE FROM cart WHERE user_id = ?");
        $clear_cart_stmt->bind_param("i", $user_id);
        $clear_cart_stmt->execute();

        $conn->commit();
        header('Location: ../products/order.php?order_id=' . $order_id);
        exit();
    } catch (Exception $e) {
        $conn->rollback();
        header('Location: ../products/checkout.php?error=' . urlencode($e->getMessage()));
        exit();
    }

    // ACTION 2: Admin is updating an order status from orders.php
} elseif (isset($_POST['order_id']) && isset($_POST['action'])) {
    // Security Check: This action is ADMIN ONLY.
    if (!isset($_SESSION['is_admin']) || !$_SESSION['is_admin']) {
        header("Location: ../user/login.php?error=unauthorized");
        exit();
    }

    $order_id = $_POST['order_id'];
    $action = $_POST['action'];
    $allowed_actions = ['Approved', 'Declined', 'Shipped', 'Delivered'];

    if (in_array($action, $allowed_actions)) {
        $stmt = $conn->prepare("UPDATE orders SET status = ? WHERE order_id = ?");
        $stmt->bind_param("si", $action, $order_id);
        if ($stmt->execute()) {
            header("Location: ../admin/orders.php?success=status_updated");
        } else {
            header("Location: ../admin/orders.php?error=update_failed");
        }
        $stmt->close();
    } else {
        header("Location: ../admin/orders.php?error=invalid_action");
    }

    // If neither set of parameters matches, it's an invalid request.
} else {
    header('Location: ../index.php');
    exit();
}

$conn->close();
