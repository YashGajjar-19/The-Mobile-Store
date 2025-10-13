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
    <img class="login-image" src="../assets/images/svg/order.svg" alt="Order Confirmed">

    <h1>
        Thank You For Your Order!
    </h1>

    <p>
        Your order #<?php echo htmlspecialchars($_GET['order_id']); ?> has been placed successfully.
    </p>

    <div class="confirmation-buttons">
        <a href="../index.php" class="button">
            Continue Shopping
        </a>
        <a href="invoice.php?order_id=<?php echo htmlspecialchars($_GET['order_id']); ?>" class="button">
            See Invoice
        </a>
    </div>
</main>

<?php require_once '../includes/footer.php'; ?>