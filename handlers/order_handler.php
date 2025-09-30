<?php
session_start();
require_once '../includes/config.php'; // Adjust path if needed

// Redirect user if not logged in
if (!isset($_SESSION['user_id'])) {
    $_SESSION['error_message'] = "You must be logged in to place an order.";
    header('Location: ../user/login.php');
    exit();
}

// Check if the form was submitted
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../products/checkout.php');
    exit();
}

$user_id = $_SESSION['user_id'];
$shipping_address = $_POST['shipping_address'] ?? '';
$payment_method = $_POST['payment_method'] ?? '';

// Basic Validation
if (empty($shipping_address) || empty($payment_method)) {
    $_SESSION['error_message'] = "Please fill in all required fields.";
    header('Location: ../products/checkout.php');
    exit();
}

$conn->begin_transaction();

try {
    $order_items = [];
    $total_amount = 0;
    $is_buy_now = isset($_POST['buy_now_variant_id']) && isset($_POST['buy_now_quantity']);

    if ($is_buy_now) {
        // Handle "Buy Now" Order
        $variant_id = (int)$_POST['buy_now_variant_id'];
        $quantity = (int)$_POST['buy_now_quantity'];

        $stmt = $conn->prepare("SELECT price FROM product_variants WHERE variant_id = ?");
        $stmt->bind_param("i", $variant_id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($variant = $result->fetch_assoc()) {
            $total_amount = $variant['price'] * $quantity;
            $order_items[] = [
                'variant_id' => $variant_id,
                'quantity' => $quantity,
                'price' => $variant['price']
            ];
        } else {
            throw new Exception("Invalid product variant specified.");
        }
        $stmt->close();
    } else {
        // Handle Cart Checkout
        $cart_stmt = $conn->prepare("
            SELECT c.variant_id, c.quantity, pv.price 
            FROM cart c
            JOIN product_variants pv ON c.variant_id = pv.variant_id
            WHERE c.user_id = ?
        ");
        $cart_stmt->bind_param("i", $user_id);
        $cart_stmt->execute();
        $cart_items_result = $cart_stmt->get_result();

        if ($cart_items_result->num_rows === 0) {
            throw new Exception("Your cart is empty.");
        }

        while ($item = $cart_items_result->fetch_assoc()) {
            $total_amount += $item['price'] * $item['quantity'];
            $order_items[] = [
                'variant_id' => $item['variant_id'],
                'quantity' => $item['quantity'],
                'price' => $item['price']
            ];
        }
        $cart_stmt->close();
    }

    // --- Create the Order (Using YOUR column names) ---
    $order_stmt = $conn->prepare("
        INSERT INTO orders (user_id, total_amount, shipping_address, payment_method, order_status) 
        VALUES (?, ?, ?, ?, 'Pending')
    ");
    $order_stmt->bind_param("idss", $user_id, $total_amount, $shipping_address, $payment_method);
    if (!$order_stmt->execute()) {
        throw new Exception("Failed to create order: " . $order_stmt->error);
    }
    $order_id = $order_stmt->insert_id;
    $order_stmt->close();

    // --- Insert Order Items (Using YOUR column names) ---
    $order_item_stmt = $conn->prepare("
        INSERT INTO order_items (order_id, variant_id, quantity, price_per_item) 
        VALUES (?, ?, ?, ?)
    ");
    foreach ($order_items as $item) {
        $order_item_stmt->bind_param("iiid", $order_id, $item['variant_id'], $item['quantity'], $item['price']);
        if (!$order_item_stmt->execute()) {
            throw new Exception("Failed to save order items: " . $order_item_stmt->error);
        }
    }
    $order_item_stmt->close();

    // Clear the Cart if it wasn't a "Buy Now" order
    if (!$is_buy_now) {
        $clear_cart_stmt = $conn->prepare("DELETE FROM cart WHERE user_id = ?");
        $clear_cart_stmt->bind_param("i", $user_id);
        $clear_cart_stmt->execute();
        $clear_cart_stmt->close();
    }

    // Commit Transaction
    $conn->commit();

    // Redirect to Success Page
    header("Location: ../products/order.php?order_id=" . $order_id);
    exit();
} catch (Exception $e) {
    // Rollback on Error
    $conn->rollback();
    $_SESSION['error_message'] = "An error occurred: " . $e->getMessage();
    header('Location: ../products/checkout.php');
    exit();
}
