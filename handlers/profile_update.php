<?php
session_start();
require_once '../includes/config.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: ../user/login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user_id = $_SESSION['user_id'];
    $full_name = $_POST['full_name'];
    $email = $_POST['email'];
    $phone_number = !empty($_POST['phone_number']) ? $_POST['phone_number'] : NULL;
    $address = !empty($_POST['address']) ? $_POST['address'] : NULL;

    // Prepare the update statement
    $stmt = $conn->prepare("UPDATE users SET full_name = ?, email = ?, phone_number = ?, address = ? WHERE id = ?");
    $stmt->bind_param("ssisi", $full_name, $email, $phone_number, $address, $user_id);

    if ($stmt->execute()) {
        // Update session variable with new name
        $_SESSION['full_name'] = $full_name;
        header("Location: ../user/profile.php?success=updated");
    } else {
        header("Location: ../user/profle.php?error=updatefailed");
    }

    $stmt->close();
    $conn->close();
}
