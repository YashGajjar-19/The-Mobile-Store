<?php
$page_title = 'Your Shopping Cart';
require_once 'includes/header.php';

// Redirect user if not logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: user/login.php?redirect=cart.php');
    exit();
}

// Fetch cart items for the logged-in user
$user_id = $_SESSION['user_id'];
$cart_items_stmt = $conn->prepare("
    SELECT 
        c.cart_item_id,
        c.quantity,
        pv.variant_id,
        pv.color,
        pv.ram_gb,
        pv.storage_gb,
        pv.price,
        p.product_name,
        (SELECT image_url FROM product_images pi WHERE pi.variant_id = pv.variant_id AND pi.is_thumbnail = 1 LIMIT 1) as image_url
    FROM cart c
    JOIN product_variants pv ON c.variant_id = pv.variant_id
    JOIN products p ON pv.product_id = p.product_id
    WHERE c.user_id = ?
");
$cart_items_stmt->bind_param("i", $user_id);
$cart_items_stmt->execute();
$cart_items_result = $cart_items_stmt->get_result();
$cart_total = 0;
?>

<style>
    .cart-container {
        max-width: 1200px;
        margin: 150px auto 50px;
        padding: 20px;
    }

    .cart-item {
        display: flex;
        align-items: center;
        padding: 20px;
        border-bottom: 1px solid var(--glass);
        gap: 20px;
    }

    .cart-item-img {
        width: 100px;
        height: 100px;
        object-fit: contain;
        background: var(--body);
        border-radius: 10px;
        padding: 5px;
    }

    .cart-item-details {
        flex-grow: 1;
    }

    .cart-item-details h3 {
        margin: 0 0 5px 0;
        font-size: 1.2rem;
    }

    .cart-item-details p {
        margin: 0;
        color: var(--dark);
        font-size: 0.9rem;
    }

    .cart-item-price,
    .cart-item-subtotal {
        font-weight: 600;
        width: 120px;
        text-align: right;
    }

    .cart-summary {
        margin-top: 30px;
        padding: 20px;
        background: var(--body);
        border-radius: 15px;
        text-align: right;
    }

    .cart-summary h2 {
        font-size: 1.8rem;
        margin: 0 0 10px 0;
    }
</style>

<main class="cart-container">
    <div class="section-title">
        <h2>Your Cart</h2>
        <div class="title-line"></div>
    </div>

    <div class="cart-items-list">
        <?php if ($cart_items_result->num_rows > 0): ?>
            <?php while ($item = $cart_items_result->fetch_assoc()): ?>
                <?php
                $subtotal = $item['price'] * $item['quantity'];
                $cart_total += $subtotal;
                ?>
                <div class="cart-item">
                    <img src="./assets/images/products/<?php echo htmlspecialchars($item['image_url']); ?>" alt="<?php echo htmlspecialchars($item['product_name']); ?>" class="cart-item-img">
                    <div class="cart-item-details">
                        <h3><?php echo htmlspecialchars($item['product_name']); ?></h3>
                        <p><?php echo htmlspecialchars($item['color']) . ', ' . htmlspecialchars($item['ram_gb']) . 'GB RAM, ' . htmlspecialchars($item['storage_gb']) . 'GB'; ?></p>
                    </div>
                    <div class="cart-item-quantity">
                        <p>Qty: <?php echo $item['quantity']; ?></p>
                    </div>
                    <div class="cart-item-price">&#8377;<?php echo number_format($item['price']); ?></div>
                    <div class="cart-item-subtotal">&#8377;<?php echo number_format($subtotal); ?></div>
                </div>
            <?php endwhile; ?>
    </div>

    <div class="cart-summary">
        <h2>Total: &#8377;<?php echo number_format($cart_total); ?></h2>
        <a href="#" class="button" style="width: auto; padding: 15px 30px;">Proceed to Checkout</a>
    </div>

<?php else: ?>
    <p style="text-align: center; padding: 50px;">Your cart is empty.</p>
<?php endif; ?>
</main>

<?
