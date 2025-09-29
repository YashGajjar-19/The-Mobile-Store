<?php
$page_title = 'Cart';
require_once '../includes/header.php';
require_once '../includes/navbar.php';

// Redirect user if not logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: ../user/login.php?redirect=cart.php');
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
        (SELECT image_url FROM product_color_images pci WHERE pci.product_id = p.product_id AND pci.color = pv.color AND pci.is_thumbnail = 1 LIMIT 1) as image_url
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

<main class="cart-container two-column-layout">
    <div class="form-image-column">
        <img src="../assets/images/svg/cart.svg" alt="Contact Us Image" class="login-image" style="width: 80%;">
    </div>

    <div class="form-content-column" style="padding: 30px;">
        <div class="section-title">
            <h2>Your Cart</h2>
            <div class="title-line"></div>
        </div>
        <hr>

        <div class="cart-items-list">
            <?php if ($cart_items_result->num_rows > 0): ?>
                <?php while ($item = $cart_items_result->fetch_assoc()): ?>
                    <?php
                    $subtotal = $item['price'] * $item['quantity'];
                    $cart_total += $subtotal;
                    ?>
                    <div class="cart-item" data-cart-item-id="<?php echo $item['cart_item_id']; ?>">
                        <img src="../assets/images/products/<?php echo htmlspecialchars($item['image_url']); ?>" alt="<?php echo htmlspecialchars($item['product_name']); ?>" class="cart-item-img">

                        <div class="cart-item-details">
                            <h3><?php echo htmlspecialchars($item['product_name']); ?></h3>
                            <p><?php echo htmlspecialchars($item['color']) . ', ' . htmlspecialchars($item['ram_gb']) . 'GB RAM, ' . htmlspecialchars($item['storage_gb']) . 'GB'; ?></p>
                            <div class="cart-item-price">Price: &#8377;<?php echo number_format($item['price']); ?></div>
                        </div>

                        <div class="cart-item-quantity">
                            <label class="form-label">Quantity:</label>
                            <div class="quantity-input-wrapper">
                                <button type="button" class="quantity-btn" data-action="decrement" data-item-id="<?php echo $item['cart_item_id']; ?>">-</button>
                                <input type="number" class="quantity-input" value="<?php echo $item['quantity']; ?>" min="1" max="5" data-item-id="<?php echo $item['cart_item_id']; ?>">
                                <button type="button" class="quantity-btn" data-action="increment" data-item-id="<?php echo $item['cart_item_id']; ?>">+</button>
                            </div>
                            <div class="cart-item-subtotal">Subtotal: &#8377;<span><?php echo number_format($subtotal); ?></span></div>
                        </div>
                        <button class="remove-item-btn" data-item-id="<?php echo $item['cart_item_id']; ?>" title="Remove item">
                            <span class="material-symbols-rounded">delete</span>
                        </button>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <p style="text-align: center; padding: 50px;">Your cart is empty.</p>
            <?php endif; ?>
        </div>

        <div class="section-title">
            <h2>Bill Details</h2>
            <div class="title-line"></div>
        </div>
        <hr>

        <div class="cart-summary">
            <div class="summary-row">
                <span class="form-label">Subtotal</span>
                <span id="summary-subtotal">&#8377;<?php echo number_format($cart_total); ?></span>
            </div>
            <div class="summary-row">
                <span class="form-label">Shipping</span>
                <span>FREE</span>
            </div>
            <hr>
            <div class="summary-row total">
                <span class="form-label">Total</span>
                <span id="summary-total">&#8377;<?php echo number_format($cart_total); ?></span>
            </div>
            <a href="checkout.php" class="button" id="checkout-btn" style="padding: 15px 60px; margin-left: 250px;" <?php if ($cart_items_result->num_rows === 0) echo 'disabled'; ?>>Proceed to Checkout</a>
        </div>
    </div>
</main>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const cartItemsList = document.querySelector('.cart-items-list');

        function updateCartOnServer(itemId, action, quantity = 1) {
            const formData = new FormData();
            formData.append('cart_item_id', itemId);
            formData.append('action', action);
            formData.append('quantity', quantity);

            return fetch('../handlers/cart_handler.php', {
                method: 'POST',
                body: formData
            }).then(response => response.json());
        }

        function updatePageTotals(newTotal, newCount) {
            document.getElementById('summary-subtotal').innerHTML = `&#8377;${parseFloat(newTotal).toLocaleString()}`;
            document.getElementById('summary-total').innerHTML = `&#8377;${parseFloat(newTotal).toLocaleString()}`;
            const cartBadge = document.querySelector('.cart-badge');
            if (cartBadge) {
                if (newCount > 0) {
                    cartBadge.textContent = newCount;
                    cartBadge.style.display = 'flex';
                } else {
                    cartBadge.style.display = 'none';
                }
            }
            if (newCount === 0) {
                cartItemsList.innerHTML = '<p style="text-align: center; padding: 50px;">Your cart is empty.</p>';
                document.getElementById('checkout-btn').setAttribute('disabled', true);
            }
        }

        cartItemsList.addEventListener('click', e => {
            const removeButton = e.target.closest('.remove-item-btn');
            if (removeButton) {
                const itemId = removeButton.dataset.itemId;
                const itemElement = document.querySelector(`.cart-item[data-cart-item-id='${itemId}']`);

                updateCartOnServer(itemId, 'remove').then(data => {
                    if (data.status === 'success' && itemElement) {
                        itemElement.remove();
                        updatePageTotals(data.cart_total, data.cart_count);
                    } else {
                        console.error('Failed to remove item:', data.message);
                    }
                });
            }
        });

        cartItemsList.addEventListener('change', e => {
            if (e.target.classList.contains('quantity-input')) {
                const itemId = e.target.dataset.itemId;
                const newQuantity = e.target.value;
                const itemElement = document.querySelector(`.cart-item[data-cart-item-id='${itemId}']`);

                updateCartOnServer(itemId, 'update', newQuantity).then(data => {
                    if (data.status === 'success' && itemElement) {
                        // Update subtotal for this specific item
                        const price = parseFloat(itemElement.querySelector('.cart-item-price').textContent.replace(/[^0-9.-]+/g, ""));
                        const subtotalElement = itemElement.querySelector('.cart-item-subtotal span');
                        subtotalElement.textContent = (price * newQuantity).toLocaleString();

                        // Update the main totals
                        updatePageTotals(data.cart_total, data.cart_count);
                    }
                });
            }
        });
    });
</script>

<?php require_once '../includes/footer.php'; ?>