<?php
session_start();
require_once __DIR__ . '/../includes/config.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    http_response_code(200);
    exit();
}

try {
    if (!isset($_SESSION['user_id'])) {
        throw new Exception('You must be logged in to manage your wishlist.', 401);
    }

    if (!isset($_POST['product_id']) || !is_numeric($_POST['product_id'])) {
        throw new Exception('Invalid product specified.', 400);
    }

    $user_id = (int)$_SESSION['user_id'];
    $product_id = (int)$_POST['product_id'];

    // Check if item exists
    $check_stmt = $conn->prepare("SELECT wishlist_item_id FROM wishlist WHERE user_id = ? AND product_id = ?");
    $check_stmt->bind_param("ii", $user_id, $product_id);
    $check_stmt->execute();
    $result = $check_stmt->get_result();

    if ($result->num_rows > 0) {
        // Remove
        $remove_stmt = $conn->prepare("DELETE FROM wishlist WHERE user_id = ? AND product_id = ?");
        $remove_stmt->bind_param("ii", $user_id, $product_id);
        $remove_stmt->execute();
        echo json_encode(['status' => 'removed', 'message' => 'Item removed from wishlist.']);
        $remove_stmt->close();
    } else {
        // Add
        $add_stmt = $conn->prepare("INSERT INTO wishlist (user_id, product_id) VALUES (?, ?)");
        $add_stmt->bind_param("ii", $user_id, $product_id);
        $add_stmt->execute();
        echo json_encode(['status' => 'added', 'message' => 'Item added to wishlist!']);
        $add_stmt->close();
    }

    $check_stmt->close();
} catch (Exception $e) {
    http_response_code($e->getCode() ?: 500);
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
} finally {
    if (isset($conn)) $conn->close();
}
