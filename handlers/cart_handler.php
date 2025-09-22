<?php
session_start();
require_once '../includes/config.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'You must be logged in to perform this action.']);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method.']);
    exit();
}

$user_id = $_SESSION['user_id'];
$conn->begin_transaction();

try {
    // --- ACTION 1: ADD item to cart (from product.php) ---
    if (isset($_POST['variant_id'])) {
        $variant_id = (int)$_POST['variant_id'];
        $quantity = isset($_POST['quantity']) ? (int)$_POST['quantity'] : 1;

        if ($variant_id <= 0 || $quantity <= 0) {
            throw new Exception('Invalid product details provided.');
        }

        // Check if item already exists to update quantity instead of inserting new row
        $check_stmt = $conn->prepare("SELECT cart_item_id, quantity FROM cart WHERE user_id = ? AND variant_id = ?");
        $check_stmt->bind_param("ii", $user_id, $variant_id);
        $check_stmt->execute();
        $result = $check_stmt->get_result();

        if ($result->num_rows > 0) {
            $cart_item = $result->fetch_assoc();
            $new_quantity = $cart_item['quantity'] + $quantity;
            $update_stmt = $conn->prepare("UPDATE cart SET quantity = ? WHERE cart_item_id = ?");
            $update_stmt->bind_param("ii", $new_quantity, $cart_item['cart_item_id']);
            $update_stmt->execute();
        } else {
            $insert_stmt = $conn->prepare("INSERT INTO cart (user_id, variant_id, quantity) VALUES (?, ?, ?)");
            $insert_stmt->bind_param("iii", $user_id, $variant_id, $quantity);
            $insert_stmt->execute();
        }

        // Get total cart count for header badge
        $total_items_stmt = $conn->prepare("SELECT SUM(quantity) as total FROM cart WHERE user_id = ?");
        $total_items_stmt->bind_param("i", $user_id);
        $total_items_stmt->execute();
        $total_items = $total_items_stmt->get_result()->fetch_assoc()['total'];

        echo json_encode(['status' => 'success', 'message' => 'Item added to cart!', 'cart_count' => $total_items ?? 0]);

        // --- ACTION 2: UPDATE/REMOVE item from cart (from cart.php / checkout.php) ---
    } elseif (isset($_POST['cart_item_id']) && isset($_POST['action'])) {
        $cart_item_id = (int)$_POST['cart_item_id'];
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

        // Recalculate totals and cart count for AJAX update
        $total_stmt = $conn->prepare("SELECT SUM(c.quantity * pv.price) as total_price, SUM(c.quantity) as total_items FROM cart c JOIN product_variants pv ON c.variant_id = pv.variant_id WHERE c.user_id = ?");
        $total_stmt->bind_param("i", $user_id);
        $total_stmt->execute();
        $result = $total_stmt->get_result()->fetch_assoc();

        echo json_encode([
            'status' => 'success',
            'cart_total' => $result['total_price'] ?? 0,
            'cart_count' => $result['total_items'] ?? 0
        ]);
    } else {
        throw new Exception('Invalid request parameters.');
    }

    $conn->commit();
} catch (Exception $e) {
    $conn->rollback();
    echo json_encode(['status' => 'error', 'message' => 'An error occurred: ' . $e->getMessage()]);
}

$conn->close();
