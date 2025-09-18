<?php
session_start();
require_once 'config.php';

// Security check: Ensure user is logged in and request is a POST
if (!isset($_SESSION['user_id']) || $_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../index.php');
    exit();
}

$user_id = $_SESSION['user_id'];

// Check if required form fields are submitted
if (!isset($_POST['shipping_address']) || !isset($_POST['payment_method'])) {
    header('Location: ../checkout.php?error=missing_details');
    exit();
}

$shipping_address = trim($_POST['shipping_address']);
$payment_method = trim($_POST['payment_method']);

if (empty($shipping_address)) {
    header('Location: ../checkout.php?error=address_required');
    exit();
}


// --- Start Database Transaction ---
$conn->begin_transaction();

try {
    // 1. Fetch cart items to calculate total and ensure cart is not empty
    $cart_items_stmt = $conn->prepare("
        SELECT c.quantity, pv.variant_id, pv.price
        FROM cart c
        JOIN product_variants pv ON c.variant_id = pv.variant_id
        WHERE c.user_id = ?
    ");
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

    // 2. Create the main order record
    $order_stmt = $conn->prepare("
        INSERT INTO orders (user_id, total_amount, status, shipping_address) 
        VALUES (?, ?, 'Pending', ?)
    ");
    $order_stmt->bind_param("ids", $user_id, $total_amount, $shipping_address);
    $order_stmt->execute();
    $order_id = $conn->insert_id;

    // 3. Insert each cart item into the order_items table
    $order_item_stmt = $conn->prepare("
        INSERT INTO order_items (order_id, variant_id, quantity, price_per_item) 
        VALUES (?, ?, ?, ?)
    ");
    foreach ($cart_items as $item) {
        $order_item_stmt->bind_param("iiid", $order_id, $item['variant_id'], $item['quantity'], $item['price']);
        $order_item_stmt->execute();
    }

    // 4. Clear the user's cart
    $clear_cart_stmt = $conn->prepare("DELETE FROM cart WHERE user_id = ?");
    $clear_cart_stmt->bind_param("i", $user_id);
    $clear_cart_stmt->execute();

    // If everything was successful, commit the changes to the database
    $conn->commit();

    // Redirect to the confirmation page
    header('Location: ./order_confirmation.php?order_id=' . $order_id);
    exit();
} catch (Exception $e) {
    // If anything went wrong, roll back all database changes
    $conn->rollback();
    // Redirect back to checkout with an error message
    header('Location: ../checkout.php?error=' . urlencode($e->getMessage()));
    exit();
}
