<?php
session_start();
require_once 'config.php';

header('Content-Type: application/json');

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'You must be logged in to add items to your cart.']);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user_id = $_SESSION['user_id'];
    $variant_id = $_POST['variant_id'];
    $quantity = isset($_POST['quantity']) ? (int)$_POST['quantity'] : 1;

    if (empty($variant_id) || $quantity <= 0) {
        echo json_encode(['status' => 'error', 'message' => 'Invalid product details.']);
        exit();
    }

    // Check if this variant is already in the user's cart
    $check_stmt = $conn->prepare("SELECT cart_item_id, quantity FROM cart WHERE user_id = ? AND variant_id = ?");
    $check_stmt->bind_param("ii", $user_id, $variant_id);
    $check_stmt->execute();
    $result = $check_stmt->get_result();

    if ($result->num_rows > 0) {
        // Item exists, update quantity
        $cart_item = $result->fetch_assoc();
        $new_quantity = $cart_item['quantity'] + $quantity;
        $update_stmt = $conn->prepare("UPDATE cart SET quantity = ? WHERE cart_item_id = ?");
        $update_stmt->bind_param("ii", $new_quantity, $cart_item['cart_item_id']);
        $update_stmt->execute();
    } else {
        // Item does not exist, insert new
        $insert_stmt = $conn->prepare("INSERT INTO cart (user_id, variant_id, quantity) VALUES (?, ?, ?)");
        $insert_stmt->bind_param("iii", $user_id, $variant_id, $quantity);
        $insert_stmt->execute();
    }

    // Get total items in cart for the badge update
    $total_items_stmt = $conn->prepare("SELECT SUM(quantity) as total FROM cart WHERE user_id = ?");
    $total_items_stmt->bind_param("i", $user_id);
    $total_items_stmt->execute();
    $total_items = $total_items_stmt->get_result()->fetch_assoc()['total'];

    echo json_encode(['status' => 'success', 'message' => 'Item added to cart!', 'cart_count' => $total_items]);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method.']);
}
