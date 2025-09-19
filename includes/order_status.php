<?php
session_start();
require_once '../includes/config.php';

// Security Check: Ensure the user is an admin and the request is valid.
if (!isset($_SESSION['user_id']) || !$_SESSION['is_admin']) {
    header("Location: ../user/login.php?error=unauthorized");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['order_id']) && isset($_POST['action'])) {
    $order_id = $_POST['order_id'];
    $action = $_POST['action'];

    // Define a list of acceptable statuses to prevent arbitrary updates.
    $allowed_actions = ['Approved', 'Declined', 'Shipped', 'Delivered'];

    if (in_array($action, $allowed_actions)) {
        // Prepare and execute the update statement.
        $stmt = $conn->prepare("UPDATE orders SET status = ? WHERE order_id = ?");
        $stmt->bind_param("si", $action, $order_id);

        if ($stmt->execute()) {
            // On success, redirect back with a success message.
            header("Location: ../admin/orders.php?success=status_updated");
        } else {
            // On failure, redirect with an error message.
            header("Location: ../admin/orders.php?error=update_failed");
        }
        $stmt->close();
    } else {
        // If the action is not in the allowed list, it's an invalid action.
        header("Location: ../admin/orders.php?error=invalid_action");
    }
} else {
    // If the page is accessed directly or required data is missing, redirect.
    header("Location: ../admin/orders.php");
}

$conn->close();
exit();
