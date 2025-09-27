<?php
session_start();
require_once '../includes/config.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: ../user/login.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_SESSION['user_id'];

    // Validation for shipping address
    if (!isset($_POST['shipping_address']) || trim($_POST['shipping_address']) === '') {
        $_SESSION['error_message'] = "Please provide a shipping address.";
        header('Location: ../products/checkout.php');
        exit();
    }

    $shipping_address = trim($_POST['shipping_address']);

    $conn->begin_transaction();

    try {
        $total_amount = 0;
        $items_to_order = [];

        // Logic for 'Buy Now'
        if (isset($_POST['buy_now_variant_id']) && isset($_POST['buy_now_quantity'])) {
            $variant_id = $_POST['buy_now_variant_id'];
            $quantity = (int)$_POST['buy_now_quantity'];
            $stmt = $conn->prepare("SELECT price FROM product_variants WHERE variant_id = ?");
            $stmt->bind_param("i", $variant_id);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($row = $result->fetch_assoc()) {
                $price = $row['price'];
                $total_amount = $price * $quantity;
                $items_to_order[] = ['variant_id' => $variant_id, 'quantity' => $quantity];
            } else {
                throw new Exception("Invalid product variant.");
            }
        } else {
            // Logic for regular cart checkout
            $cart_stmt = $conn->prepare("SELECT c.variant_id, c.quantity, pv.price FROM cart c JOIN product_variants pv ON c.variant_id = pv.variant_id WHERE c.user_id = ?");
            $cart_stmt->bind_param("i", $user_id);
            $cart_stmt->execute();
            $cart_result = $cart_stmt->get_result();

            if ($cart_result->num_rows === 0) {
                throw new Exception("Your cart is empty.");
            }

            while ($item = $cart_result->fetch_assoc()) {
                $total_amount += $item['price'] * $item['quantity'];
                $items_to_order[] = ['variant_id' => $item['variant_id'], 'quantity' => $item['quantity']];
            }
        }

        // 1. Insert into 'orders' table
        $order_stmt = $conn->prepare("INSERT INTO orders (user_id, total_amount, shipping_address) VALUES (?, ?, ?)");
        $order_stmt->bind_param("ids", $user_id, $total_amount, $shipping_address);
        $order_stmt->execute();
        $order_id = $conn->insert_id;

        // 2. Insert into 'order_items' table
        $order_item_stmt = $conn->prepare("INSERT INTO order_items (order_id, variant_id, quantity) VALUES (?, ?, ?)");
        foreach ($items_to_order as $item) {
            $order_item_stmt->bind_param("iii", $order_id, $item['variant_id'], $item['quantity']);
            $order_item_stmt->execute();
        }

        // 3. Clear the user's cart (if not a 'Buy Now' order)
        if (!isset($_POST['buy_now_variant_id'])) {
            $clear_cart_stmt = $conn->prepare("DELETE FROM cart WHERE user_id = ?");
            $clear_cart_stmt->bind_param("i", $user_id);
            $clear_cart_stmt->execute();
        }

        // 4. Save the shipping address for future use
        $update_addr_stmt = $conn->prepare("UPDATE users SET address = ? WHERE id = ?");
        $update_addr_stmt->bind_param("si", $shipping_address, $user_id);
        $update_addr_stmt->execute();

        $conn->commit();

        $_SESSION['success_message'] = "Your order has been placed successfully!";

        // **CORRECTED REDIRECTION**
        // This will now redirect to the specific order page, e.g., products/order.php?id=123
        header('Location: ../products/order.php?id=' . $order_id);
        exit();
    } catch (Exception $e) {
        $conn->rollback();
        $_SESSION['error_message'] = "Failed to place order: " . $e->getMessage();
        header('Location: ../products/checkout.php');
        exit();
    }
} else {
    header('Location: ../products/cart.php');
    exit();
}
