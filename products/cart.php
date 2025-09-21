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
                    <div class="cart-item">
                        <img src="./assets/images/products/<?php echo htmlspecialchars($item['image_url']); ?>" alt="<?php echo htmlspecialchars($item['product_name']); ?>" class="cart-item-img">

                        <div class="cart-item-details">
                            <h3><?php echo htmlspecialchars($item['product_name']); ?></h3>

                            <p><?php echo htmlspecialchars($item['color']) . ', ' . htmlspecialchars($item['ram_gb']) . 'GB RAM, ' . htmlspecialchars($item['storage_gb']) . 'GB'; ?></p>

                            <div class="cart-item-price">Price: &#8377;<?php echo number_format($item['price']); ?></div>

                             <button class="remove-item-btn" data-item-id="<?php echo $item['cart_item_id']; ?>" title="Remove item">
                            <span class="material-symbols-rounded">delete</span>
                        </button>
                        </div>

                        <div class="cart-item-quantity">
                            <label class="form-label" for="quantity-<?php echo $item['cart_item_id']; ?>">
                                Quantity:
                            </label>

                            <input type="number" id="quantity-<?php echo $item['cart_item_id']; ?>" value="<?php echo $item['quantity']; ?>" min="1" max="5" class="quantity-setter">

                            <div class="cart-item-subtotal">Subtotal: &#8377;<?php echo number_format($subtotal); ?>
                            </div>
                        </div>
                    </div>

                <?php endwhile; ?>
            <?php else: ?>

                <p style="text-align: center; padding: 50px;">
                    Your cart is empty.
                </p>

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
                <span>&#8377;<?php echo number_format($cart_total); ?></span>
            </div>
            <div class="summary-row">
                <span class="form-label">Shipping</span>
                <span>FREE</span>
            </div>
            <hr>
            <div class="summary-row total">
                <span class="form-label">Total</span>
                <span>&#8377;<?php echo number_format($cart_total); ?></span>
            </div>
            <a href="checkout.php" class="button" style="max-width: 100%; padding: 15px auto;">Proceed to Checkout</a>
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

    cartItemsList.addEventListener('click', e => {
        const removeButton = e.target.closest('.remove-item-btn');
        if (removeButton) {
            const itemId = removeButton.dataset.itemId;
            
            updateCartOnServer(itemId, 'remove').then(data => {
                if (data.status === 'success') {
                    // Remove the item element from the page
                    const itemElement = document.querySelector(`.cart-item[data-cart-item-id='${itemId}']`);
                    if (itemElement) {
                        itemElement.remove();
                    }
                    
                    // Update totals
                    document.getElementById('summary-subtotal').innerHTML = `&#8377;${parseFloat(data.cart_total).toLocaleString()}`;
                    document.getElementById('summary-total').innerHTML = `&#8377;${parseFloat(data.cart_total).toLocaleString()}`;
                    
                    // Update cart badge in header
                    const cartBadge = document.querySelector('.cart-badge');
                    if (cartBadge) {
                        if (data.cart_count > 0) {
                            cartBadge.textContent = data.cart_count;
                        } else {
                            cartBadge.style.display = 'none';
                             cartItemsList.innerHTML = '<p style="text-align: center; padding: 50px;">Your cart is empty.</p>';
                        }
                    }
                } else {
                    console.error('Failed to remove item:', data.message);
                }
            });
        }
    });
});
</script>

<?php require_once '../includes/footer.php'; ?>