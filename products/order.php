<?php
$page_title = 'Order Confirmed!';
// Use require_once instead of include for critical files
require_once '../includes/header.php';

if (!isset($_GET['order_id'])) {
    header('Location: index.php');
    exit();
}
?>

<style>
    .confirmation-container {
        max-width: 800px;
        margin: 100px auto 50px;
        padding: 60px;
        text-align: center;
        background: var(--body);
        border-radius: 15px;
        box-shadow: var(--dark-shadow);
    }

    .confirmation-container .material-symbols-rounded {
        font-size: 80px;
        color: var(--green);
    }

    .confirmation-container h1 {
        font-size: 2.5rem;
        margin: 20px 0 10px;
    }

    .confirmation-container p {
        font-size: 1.1rem;
        color: var(--dark);
        line-height: 1.6;
    }
</style>

<main class="confirmation-container">
    <img class="login-image" src="../assets/images/svg/order.svg" alt="Order Confirmed" width="50%">
    <h1>Thank You For Your Order!</h1>
    <p style="margin-bottom: 40px;">Your order #<?php echo htmlspecialchars($_GET['order_id']); ?> has been placed successfully.</p>
    <a href="../index.php" class="button" style="width: auto; margin-top: 20px; padding: 15px 30px;">Continue Shopping</a>
</main>

