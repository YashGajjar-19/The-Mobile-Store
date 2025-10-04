<?php
$page_title = 'Order Confirmed | The Mobile Store';
require_once '../includes/header.php';
require_once '../includes/navbar.php';

if (!isset($_GET['order_id'])) {
    header('Location: index.php');
    exit();
}
?>

<main class="confirmation-container">
    <img class="login-image" src="../assets/images/svg/order.svg" alt="Order Confirmed" width="50%">

    <h1>
        Thank You For Your Order!
    </h1>

    <p style="margin-bottom: 40px;">
        Your order #<?php echo htmlspecialchars($_GET['order_id']); ?> has been placed successfully.
    </p>

    <a href="invoice.php?order_id=<?php echo htmlspecialchars($_GET['order_id']); ?>" class="button" style="width: auto; margin-top: 20px; padding: 15px 30px;" target="_blank">
        See Invoice
    </a>

    <a href="../index.php" class="button" style="width: auto; margin-top: 20px; padding: 15px 30px;">
        Continue Shopping
    </a>
</main>

<?php require_once '../includes/footer.php'; ?>