<?php
$page_title = 'Checkout';
require_once 'includes/header.php';

// Redirect user if not logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: user/login.php?redirect=checkout.php');
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch user's saved address to pre-fill the form
$user_stmt = $conn->prepare("SELECT address FROM users WHERE id = ?");
$user_stmt->bind_param("i", $user_id);
$user_stmt->execute();
$user_data = $user_stmt->get_result()->fetch_assoc();
$saved_address = $user_data['address'] ?? '';


// Fetch cart items for the logged-in user
$cart_items_stmt = $conn->prepare("
    SELECT 
        c.cart_item_id,
        c.quantity,
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

<main class="form-container" style="min-height: 100vh; margin-top: 60px; margin-bottom: 20px;">
    <div class="form-wrapper" style="max-width: 1300px;">
        <div class="form-content-column" style="width: 60%;">
            <div class="form-card" style="max-width: 100%; padding: 10px;">
                <div class="section-title" style="text-align: left;">
                    <h2 style="margin-top:0;">Checkout</h2>
                    <div class="title-line" style="margin: 10px 0 20px 0;"></div>
                </div>

                <form action="includes/order_handler.php" method="POST" class="auth-form" style="margin:0;">
                    <div class="order-summary">
                        <div id="cart-items-list">
                            <?php if ($cart_items_result->num_rows > 0): ?>
                                <?php while ($item = $cart_items_result->fetch_assoc()): ?>
                                    <?php $cart_total += $item['price'] * $item['quantity']; ?>
                                    <div class="summary-item" data-item-id="<?php echo $item['cart_item_id']; ?>">
                                        <img src="./assets/images/products/<?php echo htmlspecialchars($item['image_url']); ?>" class="summary-item-img">
                                        <div class="summary-item-details">
                                            <h4><?php echo htmlspecialchars($item['product_name']); ?></h4>
                                            <p>&#8377;<?php echo number_format($item['price']); ?> each</p>
                                        </div>
                                        <div class="cart-item-quantity">
                                            <input class="quantity-setter" type="number" value="<?php echo $item['quantity']; ?>" min="1" max="5" data-item-id="<?php echo $item['cart_item_id']; ?>">
                                        </div>
                                        <div class="summary-item-price">&#8377;<?php echo number_format($item['price'] * $item['quantity']); ?></div>
                                        <button type="button" class="remove-item-btn" data-item-id="<?php echo $item['cart_item_id']; ?>"><span class="material-symbols-rounded">delete</span></button>
                                    </div>
                                <?php endwhile; ?>
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
                            <div class="payment-option selected" data-payment="card">
                                <input type="radio" name="payment_method" value="Card" id="card-payment" checked>
                                <label for="card-payment" style="flex-grow: 1; cursor:pointer;">Credit/Debit Card</label>
                            </div>
                            <div class="payment-option-content" id="card-content">
                                <div class="form-group"><label class="form-label">Card Number</label>
                                    <div class="form-input"><input type="text" placeholder="XXXX XXXX XXXX XXXX"></div>
                                </div>
                                <div class="form-group" style="display: flex; gap: 15px;">
                                    <div style="flex: 1;"><label class="form-label">Expiry</label>
                                        <div class="form-input"><input type="text" placeholder="MM/YY"></div>
                                    </div>
                                    <div style="flex: 1;"><label class="form-label">CVV</label>
                                        <div class="form-input"><input type="text" placeholder="XXX"></div>
                                    </div>
                                </div>
                            </div>

                            <div class="payment-option" data-payment="upi">
                                <input type="radio" name="payment_method" value="UPI" id="upi-payment">
                                <label for="upi-payment" style="flex-grow: 1; cursor:pointer;">UPI</label>
                            </div>
                            <div class="payment-option-content" id="upi-content">
                                <p>Enter your UPI ID to proceed with the payment.</p>
                                <div class="form-input"><input type="text" placeholder="yourname@bank"></div>
                            </div>

                            <div class="payment-option" data-payment="cod">
                                <input type="radio" name="payment_method" value="COD" id="cod-payment">
                                <label for="cod-payment" style="flex-grow: 1; cursor:pointer;">Cash on Delivery (COD)</label>
                            </div>
                            <div class="payment-option-content" id="cod-content">
                                <p>You can pay in cash at the time of delivery.</p>
                            </div>
                            <div class="payment-option" data-payment="emi">
                                <input type="radio" name="payment_method" value="EMI" id="emi-payment">
                                <label for="emi-payment" style="flex-grow: 1; cursor:pointer;">EMI</label>
                            </div>
                            <div class="payment-option-content" id="emi-content">
                                <p>EMI options are available from various banks.</p>
                            </div>
                        </div>

                        <button type="submit" class="button" style="width: 100%; margin-top: 20px;" <?php if ($cart_items_result->num_rows === 0) echo 'disabled'; ?>>Place Order</button>
                    </div>
                </form>
            </div>
        </div>

        <div class="form-image-column" style="max-width: 50%;">
            <img src="./assets/images/svg/checkout.svg" alt="Checkout Image" class="login-image" style="max-width: 80%;">
        </div>
    </div>
</main>
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const cartItemsList = document.getElementById('cart-items-list');

        function updateCart(itemId, action, quantity = 1) {
            const formData = new FormData();
            formData.append('cart_item_id', itemId);
            formData.append('action', action);
            formData.append('quantity', quantity);

            fetch('includes/cart_update.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'success') {
                        document.getElementById('cart-total').innerHTML = `Total: ₹${parseFloat(data.cart_total).toLocaleString()}`;

                        const cartBadge = document.querySelector('.cart-badge');
                        if (cartBadge) {
                            if (data.cart_count > 0) {
                                cartBadge.textContent = data.cart_count;
                                cartBadge.style.display = 'flex';
                            } else {
                                cartBadge.style.display = 'none';
                            }
                        }

                        if (action === 'remove') {
                            document.querySelector(`.summary-item[data-item-id='${itemId}']`).remove();
                            if (document.querySelectorAll('.summary-item').length === 0) {
                                cartItemsList.innerHTML = '<p style="text-align: center; padding: 50px;">Your cart is empty.</p>';
                                document.querySelector('button[type="submit"]').disabled = true;
                            }
                        }
                    } else {
                        console.error(data.message);
                    }
                });
        }

        cartItemsList.addEventListener('change', e => {
            if (e.target.classList.contains('item-quantity')) {
                const itemId = e.target.dataset.itemId;
                const quantity = e.target.value;
                updateCart(itemId, 'update', quantity);
                location.reload();
            }
        });

        cartItemsList.addEventListener('click', e => {
            const removeButton = e.target.closest('.remove-item-btn');
            if (removeButton) {
                const itemId = removeButton.dataset.itemId;
                updateCart(itemId, 'remove');
            }
        });

        // Payment Options Logic
        const paymentOptions = document.querySelectorAll('.payment-option');
        paymentOptions.forEach(option => {
            option.addEventListener('click', () => {
                paymentOptions.forEach(opt => opt.classList.remove('selected'));
                option.classList.add('selected');
                option.querySelector('input[type="radio"]').checked = true;
            });
        });
    });
</script>
<?php require_once 'includes/footer.php'; ?>