<?php
session_start();
require_once '../includes/config.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: ../user/login.php?error=loginrequired");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $product_id = $_POST['product_id'];
    $user_id = $_SESSION['user_id'];
    $rating = $_POST['rating'];
    $comment = trim($_POST['comment']);

    // Simple validation
    if (empty($product_id) || empty($rating)) {
        header("Location: ../products/product.php?id=$product_id&error=missingfields");
        exit();
    }

    // Insert the review into the database
    $stmt = $conn->prepare("INSERT INTO reviews (product_id, user_id, rating, comment) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("iiis", $product_id, $user_id, $rating, $comment);

    if ($stmt->execute()) {
        header("Location: ../products/product.php?id=$product_id&success=reviewadded");
    } else {
        header("Location: ../products/product.php?id=$product_id&error=dberror");
    }

    $stmt->close();
    $conn->close();
} else {
    header("Location: ../index.php");
    exit();
}
