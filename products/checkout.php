<?php
$page_title = 'Checkout';
require_once '../includes/header.php';
require_once '../includes/navbar.php';

// Redirect user if not logged in
if (!isset($_SESSION['user_id'])) {
    $redirect_url = 'checkout.php';
    if (!empty($_SERVER['QUERY_STRING'])) {
        $redirect_url .= '?' . $_SERVER['QUERY_STRING'];
    }
    header('Location: ../user/login.php?redirect=' . urlencode($redirect_url));
    exit();
}

$user_id = $_SESSION['user_id'];
$items_to_display = [];
$cart_total = 0;
$is_buy_now = false;

// Check if this is a 'Buy Now' request
if (isset($_GET['variant_id']) && isset($_GET['quantity'])) {
    $is_buy_now = true;
    $variant_id = $_GET['variant_id'];
    $quantity = (int)$_GET['quantity'];

    $item_stmt = $conn->prepare("
        SELECT 
            pv.variant_id, pv.price, p.product_name, pv.color,
            (SELECT image_url FROM product_color_images pci WHERE pci.product_id = p.product_id AND pci.color = pv.color AND pci.is_thumbnail = 1 LIMIT 1) as image_url
        FROM product_variants pv JOIN products p ON pv.product_id = p.product_id
        WHERE pv.variant_id = ?
    ");
    $item_stmt->bind_param("i", $variant_id);
    $item_stmt->execute();
    $item_result = $item_stmt->get_result();
    if ($item = $item_result->fetch_assoc()) {
        $item['quantity'] = $quantity;
        $item['cart_item_id'] = 'buy_now';
        $items_to_display[] = $item;
        $cart_total = $item['price'] * $item['quantity'];
    }
} else {
    // This is a regular cart checkout
    $cart_items_stmt = $conn->prepare("
        SELECT 
            c.cart_item_id, c.quantity, pv.variant_id, pv.price, p.product_name, pv.color,
            (SELECT image_url FROM product_color_images pci WHERE pci.product_id = p.product_id AND pci.color = pv.color AND pci.is_thumbnail = 1 LIMIT 1) as image_url
        FROM cart c
        JOIN product_variants pv ON c.variant_id = pv.variant_id
        JOIN products p ON pv.product_id = p.product_id
        WHERE c.user_id = ?
    ");
    $cart_items_stmt->bind_param("i", $user_id);
    $cart_items_stmt->execute();
    $cart_items_result = $cart_items_stmt->get_result();
    while ($item = $cart_items_result->fetch_assoc()) {
        $items_to_display[] = $item;
        $cart_total += $item['price'] * $item['quantity'];
    }
}

// Fetch user's saved address
$user_stmt = $conn->prepare("SELECT address FROM users WHERE id = ?");
$user_stmt->bind_param("i", $user_id);
$user_stmt->execute();
$user_data = $user_stmt->get_result()->fetch_assoc();
$saved_address = $user_data['address'] ?? '';
?>

<main class="form-container" style="min-height: 100vh; margin-top: 60px; margin-bottom: 20px;">
    <div class="alert-container" style="position: fixed; top: 100px; right: 20px; z-index: 1050; width: 300px;">
        <?php
        if (isset($_SESSION['error_message'])) {
            echo '<div class="alert alert-error">' . htmlspecialchars($_SESSION['error_message']) . '</div>';
            unset($_SESSION['error_message']);
        }
        ?>
    </div>

    <div class="form-wrapper" style="max-width: 1300px;">
        <div class="form-content-column" style="width: 60%;">
            <div class="form-card" style="max-width: 100%; padding: 10px;">
                <div class="section-title" style="text-align: left;">
                    <h2 style="margin-top:0;">Checkout</h2>
                    <div class="title-line" style="margin: 10px 0 20px 0;"></div>
                </div>

                <form id="checkout-form" action="../handlers/order_handler.php" method="POST" class="auth-form" style="margin:0;">
                    <?php if ($is_buy_now && !empty($items_to_display)): ?>
                        <input type="hidden" name="buy_now_variant_id" value="<?php echo htmlspecialchars($_GET['variant_id']); ?>">
                        <input type="hidden" name="buy_now_quantity" value="<?php echo htmlspecialchars($_GET['quantity']); ?>">
                    <?php endif; ?>

                    <div class="order-summary">
                        <div id="cart-items-list">
                            <?php if (!empty($items_to_display)): ?>
                                <?php foreach ($items_to_display as $item): ?>
                                    <div class="summary-item" data-item-id="<?php echo $item['cart_item_id']; ?>">
                                        <img src="../assets/images/products/<?php echo htmlspecialchars($item['image_url']); ?>" class="summary-item-img">
                                        <div class="summary-item-details">
                                            <h4><?php echo htmlspecialchars($item['product_name']); ?></h4>
                                            <p>&#8377;<?php echo number_format($item['price']); ?> each</p>
                                        </div>
                                        <div class="summary-item-quantity">
                                            <span>Qty: <?php echo $item['quantity']; ?></span>
                                        </div>
                                        <div class="summary-item-price">&#8377;<?php echo number_format($item['price'] * $item['quantity']); ?></div>

                                        <?php if (!$is_buy_now): ?>
                                            <button type="button" class="remove-item-btn" data-item-id="<?php echo $item['cart_item_id']; ?>" title="Remove item">
                                                <span class="material-symbols-rounded">delete</span>
                                            </button>
                                        <?php endif; ?>
                                    </div>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <p style="text-align: center; padding: 50px;">Your cart is empty.</p>
                            <?php endif; ?>
                        </div>
                        <div class="summary-total">
                            <span id="cart-total">Total: &#8377;<?php echo number_format($cart_total); ?></span>
                        </div>
                    </div>

                    <div class="payment-details">
                        <div class="form-group">
                            <label class="form-label">Shipping Address</label>
                            <div class="form-input">
                                <ion-icon name="create-outline" style="top: 25px;" role="img" class="md hydrated"></ion-icon>
                                <textarea name="shipping_address" rows="4" placeholder="Enter your full shipping address" required style="min-height: 120px; width: 100%; padding: 15px 15px 15px 45px; border: 1px solid var(--glass); border-radius: 10px; font-size: 0.95rem;"><?php echo htmlspecialchars($saved_address); ?></textarea>
                            </div>
                        </div>

                        <label class="form-label" style=" margin: 20px 0;">Payment Method</label>

                        <div id="payment-options">
                            <div class="payment-option">
                                <input type="radio" name="payment_method" value="COD" id="cod-payment" checked>
                                <label for="cod-payment" style="flex-grow: 1; cursor:pointer;">Cash on Delivery (COD)</label>
                            </div>
                        </div>
                        <button type="submit" id="place-order-btn" class="button" style="width: 100%; margin-top: 20px;" <?php if (empty($items_to_display)) echo 'disabled'; ?>>Place Order</button>
                    </div>
                </form>
            </div>
        </div>
        <div class="form-image-column" style="max-width: 50%;">
            <img src="../assets/images/svg/checkout.svg" alt="Checkout Image" class="login-image" style="max-width: 80%;">
        </div>
    </div>
</main>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const cartItemsList = document.getElementById('cart-items-list');
        const placeOrderBtn = document.getElementById('place-order-btn');
        const checkoutForm = document.getElementById('checkout-form');

        if (checkoutForm) {
            checkoutForm.addEventListener('submit', () => {
                if (placeOrderBtn) {
                    placeOrderBtn.disabled = true;
                    placeOrderBtn.textContent = 'Placing Order...';
                }
            });
        }

        cartItemsList.addEventListener('click', e => {
            const removeButton = e.target.closest('.remove-item-btn');
            if (removeButton) {
                const itemId = removeButton.dataset.itemId;
                const itemElement = document.querySelector(`.summary-item[data-item-id='${itemId}']`);
                const formData = new FormData();
                formData.append('cart_item_id', itemId);
                formData.append('action', 'remove');

                fetch('../handlers/cart_handler.php', {
                        method: 'POST',
                        body: formData
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.status === 'success') {
                            if (itemElement) itemElement.remove();

                            document.getElementById('cart-total').innerHTML = `Total: &#8377;${parseFloat(data.cart_total).toLocaleString()}`;

                            if (data.cart_count === 0) {
                                cartItemsList.innerHTML = '<p style="text-align: center; padding: 50px;">Your cart is empty.</p>';
                                if (placeOrderBtn) placeOrderBtn.disabled = true;
                            }
                        } else {
                            console.error('Failed to remove item:', data.message);
                        }
                    })
                    .catch(error => console.error('Error:', error));
            }
        });
    });
</script>

<?php require_once '../includes/footer.php'; ?>