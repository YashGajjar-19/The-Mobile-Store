<?php
session_start();
require_once 'config.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'User not logged in.']);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user_id = $_SESSION['user_id'];
    $cart_item_id = $_POST['cart_item_id'];
    $action = $_POST['action'];

    if ($action == 'update') {
        $quantity = (int)$_POST['quantity'];
        if ($quantity > 0 && $quantity <= 5) {
            $stmt = $conn->prepare("UPDATE cart SET quantity = ? WHERE cart_item_id = ? AND user_id = ?");
            $stmt->bind_param("iii", $quantity, $cart_item_id, $user_id);
            $stmt->execute();
        }
    } elseif ($action == 'remove') {
        $stmt = $conn->prepare("DELETE FROM cart WHERE cart_item_id = ? AND user_id = ?");
        $stmt->bind_param("ii", $cart_item_id, $user_id);
        $stmt->execute();
    }

    // Recalculate totals and cart count
    $total_stmt = $conn->prepare("SELECT SUM(c.quantity * pv.price) as total_price, SUM(c.quantity) as total_items FROM cart c JOIN product_variants pv ON c.variant_id = pv.variant_id WHERE c.user_id = ?");
    $total_stmt->bind_param("i", $user_id);
    $total_stmt->execute();
    $result = $total_stmt->get_result()->fetch_assoc();

    echo json_encode([
        'status' => 'success',
        'cart_total' => $result['total_price'] ?? 0,
        'cart_count' => $result['total_items'] ?? 0
    ]);
}
