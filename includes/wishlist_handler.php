<?php
session_start();
require_once 'config.php';

if (!isset($_SESSION['user_id'])) {
    header('Content-Type: application/json');
    echo json_encode(['status' => 'error', 'message' => 'You must be logged in to add items to your wishlist.']);
    exit();
}

if (!isset($_POST['product_id']) || !is_numeric($_POST['product_id'])) {
    header('Content-Type: application/json');
    echo json_encode(['status' => 'error', 'message' => 'Invalid product specified.']);
    exit();
}

$user_id = $_SESSION['user_id'];
$product_id = $_POST['product_id'];

$conn->begin_transaction();

try {
    $check_stmt = $conn->prepare("SELECT wishlist_item_id FROM wishlist WHERE user_id = ? AND product_id = ?");
    $check_stmt->bind_param("ii", $user_id, $product_id);
    $check_stmt->execute();
    $check_result = $check_stmt->get_result();

    header('Content-Type: application/json');

    if ($check_result->num_rows > 0) {
        $remove_stmt = $conn->prepare("DELETE FROM wishlist WHERE user_id = ? AND product_id = ?");
        $remove_stmt->bind_param("ii", $user_id, $product_id);
        $remove_stmt->execute();
        echo json_encode(['status' => 'removed', 'message' => 'Item removed from wishlist.']);
        $remove_stmt->close();
    } else {
        $add_stmt = $conn->prepare("INSERT INTO wishlist (user_id, product_id) VALUES (?, ?)");
        $add_stmt->bind_param("ii", $user_id, $product_id);
        $add_stmt->execute();
        echo json_encode(['status' => 'added', 'message' => 'Item added to wishlist!']);
        $add_stmt->close();
    }

    $check_stmt->close();
    $conn->commit();
} catch (mysqli_sql_exception $exception) {
    $conn->rollback();
    header('Content-Type: application/json');
    echo json_encode(['status' => 'error', 'message' => 'A database error occurred.']);
}

$conn->close();
?>