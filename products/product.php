<?php
$page_title = 'Product Details | The Mobile Store';
require_once '../includes/header.php';
require_once '../includes/navbar.php';
require_once '../includes/auth.php';

// Check if a product ID is provided in the URL
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: ../404.php"); // Redirect if no ID
    exit();
}

$product_id = $_GET['id'];

// --- Fetch Product Details ---
$stmt = $conn->prepare("SELECT p.product_name, p.description, p.specifications, b.brand_name FROM products p JOIN brands b ON p.brand_id = b.brand_id WHERE p.product_id = ?");
$stmt->bind_param("i", $product_id);
$stmt->execute();
$product_result = $stmt->get_result();
if ($product_result->num_rows === 0) {
    header("Location: ../404.php");
    exit();
}
$product = $product_result->fetch_assoc();
$product['specifications'] = json_decode($product['specifications'], true);

// --- Fetch All Variants for this Product ---
$variants_stmt = $conn->prepare("SELECT variant_id, color, ram_gb, storage_gb, price FROM product_variants WHERE product_id = ? ORDER BY ram_gb, storage_gb");
$variants_stmt->bind_param("i", $product_id);
$variants_stmt->execute();
$variants_result = $variants_stmt->get_result();
$variants = [];
while ($row = $variants_result->fetch_assoc()) {
    $variants[] = $row;
}

// --- CORRECTED: Fetch All Images, Grouped by Color from the new table ---
$images_stmt = $conn->prepare("SELECT color, image_url FROM product_color_images WHERE product_id = ? ORDER BY is_thumbnail DESC, image_id ASC");
$images_stmt->bind_param("i", $product_id);
$images_stmt->execute();
$images_result = $images_stmt->get_result();
$images_by_color = [];
while ($row = $images_result->fetch_assoc()) {
    $images_by_color[$row['color']][] = $row['image_url'];
}

// --- Fetch Reviews ---
$reviews_stmt = $conn->prepare("SELECT r.rating, r.comment, r.created_at, u.full_name FROM reviews r JOIN users u ON r.user_id = u.id WHERE r.product_id = ? ORDER BY r.created_at DESC");
$reviews_stmt->bind_param("i", $product_id);
$reviews_stmt->execute();
$reviews = $reviews_stmt->get_result();
?>

<main class="product-page-container" style="margin-top: 110px;">

    <!-- Back button -->
    <a href="javascript:history.back()" class="button back-button">
        <!-- Back icon -->
        <span class="material-symbols-rounded">
            arrow_back
        </span>
        <!-- Button text -->
        Back
    </a>

    <!-- Product image -->
    <section class="product-images">
        <div class="main-image-container"></div>
        <div class="thumbnail-strip"></div>

        <!-- Product description -->
        <div class="product-description">
            <h3 style="margin-bottom: 10px;">
                <span>Description:</span>
            </h3>

            <p>
                <?php echo nl2br(htmlspecialchars($product['description'])); ?>
            </p>
        </div>

        <!-- Review part -->
        <div class="review-list">
            <div class="section-title" style="text-align: left;">
                <h2 style="margin:0;">
                    Customer Reviews
                </h2>
                <div class="title-line" style="margin: 10px 0 0 0;"></div>
            </div>

            <hr>

            <?php if ($reviews->num_rows > 0): ?>
                <?php while ($review = $reviews->fetch_assoc()): ?>
                    <!-- Review card -->
                    <div class="review-card">
                        <!-- Header -->
                        <div class="review-header">
                            <!-- Reviewer profile picture -->
                            <div class="review-avatar">
                                <?php echo strtoupper(substr($review['full_name'], 0, 1)); ?>
                            </div>

                            <!-- Reviewer details -->
                            <div class="review-author-info">
                                <!-- Name -->
                                <div class="author-name">
                                    <?php echo htmlspecialchars($review['full_name']); ?>
                                </div>
                                <!-- Date when reviewed -->
                                <div class="review-date">
                                    <?php echo date("F j, Y", strtotime($review['created_at'])); ?>
                                </div>
                            </div>
                            <!-- Details ends -->
                        </div>
                        <!-- Header ends -->

                        <!-- Rating stars -->
                        <div class="star-rating">
                            <?php for ($i = 1; $i <= 5; $i++): ?>
                                <span class="material-symbols-rounded" data-filled="<?php echo ($i <= $review['rating']) ? 'true' : 'false'; ?>">
                                    star
                                </span>
                            <?php endfor; ?>
                        </div>

                        <!-- Reviewers comment -->
                        <p class="review-comment">
                            <?php echo nl2br(htmlspecialchars($review['comment'])); ?>
                        </p>
                    </div>
                <?php endwhile; ?>
                <!-- Review card ends -->

                <!-- If no reviews are there -->
            <?php else: ?>
                <p>
                    No reviews yet. Be the first to review this product!
                </p>
            <?php endif; ?>
        </div>
    </section>

    <!-- Product details -->
    <section class="product-details">
        <!-- Brand name -->
        <p class="brand-name">
            <?php echo htmlspecialchars($product['brand_name']); ?>
        </p>

        <!-- Product name -->
        <h1 class="product-main-name">
            <?php echo htmlspecialchars($product['product_name']); ?>
        </h1>

        <!-- Variants -->
        <p class="product-price-display" id="price-display">
            Select a variant
        </p>

        <!-- Variants selection form -->
        <form id="add-to-cart-form">
            <input type="hidden" name="variant_id" id="selected-variant-id" value="">

            <!-- Variant options -->
            <div class="variant-options">
                <!-- Colors -->
                <div class="variant-options-color">
                    <div class="option-group" id="color-options">
                        <label class="form-label" style="margin-bottom: 12px; font-size: 14px">Color:</label>
                        <div class="option-choices">
                            <?php foreach (array_keys($images_by_color) as $color): ?>
                                <button type="button" class="choice-btn color-btn" data-color="<?php echo htmlspecialchars($color); ?>">
                                    <?php echo htmlspecialchars($color); ?>
                                </button>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>

                <!-- RAM and Storage options -->
                <div class="variant-options-grid">
                    <!-- RAM options -->
                    <div class="option-group" id="ram-options" style="display: none;">
                        <label class="form-label">RAM:</label>
                        <div class="option-choices"></div>
                    </div>

                    <!-- Storage options -->
                    <div class="option-group" id="storage-options" style="display: none;">
                        <label class="form-label">Storage:</label>
                        <div class="option-choices"></div>
                    </div>

                    <!-- Qauntity setter -->
                    <div class="quantity-input-wrapper">
                        <!-- Minus -->
                        <button type="button" class="quantity-btn" data-action="decrement">
                            -
                        </button>

                        <!-- Qauntity number -->
                        <input id="quantity-input" class="quantity-input" type="number" value="1" min="1" max="5">

                        <!-- Plus -->
                        <button type="button" class="quantity-btn" data-action="increment">
                            +
                        </button>
                    </div>
                </div>
            </div>
            <!-- Variant options ends -->

            <!-- Buttons -->
            <div class="product-actions" style="align-items: flex-end;">
                <!-- Cart -->
                <button type="submit" class="button" id="add-to-cart-btn" style="flex-grow: 1;">
                    Add to Cart
                </button>
                <!-- Buy now -->
                <button type="button" class="button" id="buy-now-btn" style="flex-grow: 1;">
                    Buy Now
                </button>
            </div>
        </form>
        <!-- Variants form ends -->

        <!-- Alert box -->
        <div class="alert-container" style="position: static; margin-top: 20px;"></div>

        <!-- Specifications -->
        <?php if (!empty($product['specifications'])): ?>
            <div class="product-specifications" style="margin-top: 20px;">
                <!-- Header -->
                <h3 style="margin-bottom: 15px;">
                    Specifications:
                </h3>
                <!-- Specs table -->
                <table class="spec-table">
                    <?php foreach ($product['specifications'] as $name => $value): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($name); ?></td>
                            <td><?php echo htmlspecialchars($value); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </table>
            </div>
        <?php endif; ?>
        <!-- Specifications part ends -->

        <!-- Reviews part -->
        <section class="reviews-section" style="max-width: 1400px; margin: 10px auto;">
            <?php if (isset($_SESSION['user_id'])): ?>
                <!-- Review form -->
                <div class="review-form-container" style="margin-bottom: 20px;">
                    <!-- Header -->
                    <h3>
                        Write a Review
                    </h3>
                    <div class="title-line" style="margin: 5px 0 15px 0; width: 100px;"></div>

                    <!-- Review form -->
                    <form action="../handlers/review_handler.php" method="POST" class="auth-form">
                        <input type="hidden" name="product_id" value="<?php echo $product_id; ?>">

                        <!-- Ratings in stars -->
                        <div class="form-group">
                            <label class="form-label">Your Rating</label>

                            <!-- Ratings -->
                            <div class="rating-input">
                                <input type="radio" id="star5" name="rating" value="5" required>
                                <label for="star5" title="5 stars">&#9733;</label>

                                <input type="radio" id="star4" name="rating" value="4">
                                <label for="star4" title="4 stars">&#9733;</label>

                                <input type="radio" id="star3" name="rating" value="3">
                                <label for="star3" title="3 stars">&#9733;</label>

                                <input type="radio" id="star2" name="rating" value="2">
                                <label for="star2" title="2 stars">&#9733;</label>

                                <input type="radio" id="star1" name="rating" value="1">
                                <label for="star1" title="1 star">&#9733;</label>
                            </div>
                            <!-- Ratings ends -->
                        </div>
                        <!-- Ratings in stars ends -->

                        <!-- Comments -->
                        <div class="form-group">
                            <label for="comment" class="form-label">
                                Your Review
                            </label>
                            <!-- Write comments -->
                            <div class="form-input">
                                <textarea name="comment" id="comment" rows="3" placeholder="Share your thoughts on the product..." style="width: 838px; padding: 10px;"></textarea>
                            </div>
                        </div>

                        <!-- Submit button -->
                        <div class="form-submit">
                            <button type="submit" class="button">
                                Submit Review
                            </button>
                        </div>
                    </form>
                    <!-- Form ends -->
                </div>
                <!-- Continer ends -->

                <!-- If user is not logged in -->
            <?php else: ?>
                <p>
                    Please
                    <a href="../user/login.php?redirect=products/product.php?id=<?php echo $product_id; ?>">
                        log in
                    </a>
                    to write a review.
                </p>
            <?php endif; ?>
        </section>
        <!-- Review section ends -->
    </section>
    <!-- Details ends -->
</main>

<script>
    const variantsData = <?php echo json_encode($variants); ?>;
    const imagesData = <?php echo json_encode($images_by_color); ?>;

    document.addEventListener('DOMContentLoaded', () => {
        const mainImageContainer = document.querySelector('.main-image-container');
        const thumbnailStrip = document.querySelector('.thumbnail-strip');
        const priceDisplay = document.getElementById('price-display');
        const colorOptions = document.getElementById('color-options');
        const ramOptions = document.getElementById('ram-options');
        const storageOptions = document.getElementById('storage-options');
        const addToCartBtn = document.getElementById('add-to-cart-btn');
        const buyNowBtn = document.getElementById('buy-now-btn');
        const selectedVariantIdInput = document.getElementById('selected-variant-id');
        const quantityInput = document.getElementById('quantity-input');
        const addToCartForm = document.getElementById('add-to-cart-form');

        let selectedColor = null,
            selectedRam = null,
            selectedStorage = null;

        function updateUI() {
            if (selectedColor && selectedRam && selectedStorage) {
                const variant = variantsData.find(v => v.color === selectedColor && v.ram_gb == selectedRam && v.storage_gb == selectedStorage);
                if (variant) {
                    priceDisplay.textContent = `₹${parseInt(variant.price).toLocaleString()}`;
                    selectedVariantIdInput.value = variant.variant_id;
                    addToCartBtn.disabled = false;
                    buyNowBtn.disabled = false;
                    addToCartBtn.textContent = 'Add to Cart';
                } else {
                    priceDisplay.textContent = 'Unavailable';
                    selectedVariantIdInput.value = '';
                    addToCartBtn.disabled = true;
                    buyNowBtn.disabled = true;
                    addToCartBtn.textContent = 'Unavailable';
                }
            } else {
                addToCartBtn.disabled = true;
                buyNowBtn.disabled = true;
                addToCartBtn.textContent = 'Select Options';
                selectedVariantIdInput.value = '';
                const firstAvailable = variantsData.find(v => v.color === selectedColor);
                if (firstAvailable) {
                    priceDisplay.textContent = `From ₹${parseInt(firstAvailable.price).toLocaleString()}`;
                } else {
                    priceDisplay.textContent = 'Select a variant';
                }
            }
        }

        function updateImages() {
            mainImageContainer.innerHTML = '';
            thumbnailStrip.innerHTML = '';
            if (selectedColor && imagesData[selectedColor]) {
                imagesData[selectedColor].forEach((imgUrl, index) => {
                    const fullUrl = `../assets/images/products/${imgUrl}`;
                    if (index === 0) mainImageContainer.innerHTML = `<img src="${fullUrl}" alt="Main product image">`;

                    const thumb = document.createElement('div');
                    thumb.className = `thumbnail-item ${index === 0 ? 'active' : ''}`;
                    thumb.innerHTML = `<img src="${fullUrl}" alt="Product thumbnail">`;
                    thumb.addEventListener('click', () => {
                        mainImageContainer.querySelector('img').src = fullUrl;
                        document.querySelector('.thumbnail-item.active')?.classList.remove('active');
                        thumb.classList.add('active');
                    });
                    thumbnailStrip.appendChild(thumb);
                });
            }
        }

        function updateOptions(groupElement, options, type, currentSelection) {
            const choicesContainer = groupElement.querySelector('.option-choices');
            choicesContainer.innerHTML = '';
            if (options.size > 0) {
                groupElement.style.display = 'block';
                options.forEach(option => {
                    const btn = document.createElement('button');
                    btn.type = 'button';
                    btn.className = 'choice-btn';
                    btn.dataset[type] = option;
                    btn.textContent = type === 'storage' && option >= 1024 ? `${option/1024} TB` : `${option} GB`;
                    if (option == currentSelection) {
                        btn.classList.add('active');
                    }
                    choicesContainer.appendChild(btn);
                });
            } else {
                groupElement.style.display = 'none';
            }
        }

        colorOptions.addEventListener('click', e => {
            if (e.target.classList.contains('color-btn')) {
                selectedColor = e.target.dataset.color;
                selectedRam = null;
                selectedStorage = null;

                document.querySelectorAll('#color-options .choice-btn.active').forEach(b => b.classList.remove('active'));
                e.target.classList.add('active');

                const availableRams = new Set(variantsData.filter(v => v.color === selectedColor).map(v => v.ram_gb));
                updateOptions(ramOptions, availableRams, 'ram', selectedRam);

                const availableStorages = new Set(variantsData.filter(v => v.color === selectedColor).map(v => v.storage_gb));
                updateOptions(storageOptions, availableStorages, 'storage', selectedStorage);

                updateImages();
                updateUI();
            }
        });

        ramOptions.addEventListener('click', e => {
            if (e.target.classList.contains('choice-btn')) {
                selectedRam = e.target.dataset.ram;
                document.querySelectorAll('#ram-options .choice-btn.active').forEach(b => b.classList.remove('active'));
                e.target.classList.add('active');
                updateUI();
            }
        });

        storageOptions.addEventListener('click', e => {
            if (e.target.classList.contains('choice-btn')) {
                selectedStorage = e.target.dataset.storage;
                document.querySelectorAll('#storage-options .choice-btn.active').forEach(b => b.classList.remove('active'));
                e.target.classList.add('active');
                updateUI();
            }
        });

        function handleCartAction() {
            const variantId = selectedVariantIdInput.value;
            const quantity = quantityInput.value;

            if (!variantId) {
                showAlert('error', 'Please select all product options.');
                return;
            }
            if (quantity < 1 || quantity > 5) {
                showAlert('error', 'Quantity must be between 1 and 5.');
                return;
            }

            fetch('../handlers/cart_handler.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded'
                    },
                    body: `variant_id=${variantId}&quantity=${quantity}`
                })
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'success') {
                        showAlert('success', data.message);
                        const cartBadge = document.querySelector('.cart-badge');
                        if (cartBadge) {
                            cartBadge.style.display = 'flex';
                            cartBadge.textContent = data.cart_count;
                        }
                    } else {
                        if (data.message.includes('logged in')) {
                            window.location.href = '../user/login.php?redirect=products/product.php?id=<?php echo $product_id; ?>';
                        } else {
                            showAlert('error', data.message);
                        }
                    }
                })
                .catch(error => showAlert('error', 'An error occurred.'));
        }

        addToCartForm.addEventListener('submit', function(e) {
            e.preventDefault();
            handleCartAction();
        });

        buyNowBtn.addEventListener('click', function() {
            const variantId = selectedVariantIdInput.value;
            const quantity = quantityInput.value;

            if (!variantId) {
                showAlert('error', 'Please select all product options.');
                return;
            }
            if (quantity < 1 || quantity > 5) {
                showAlert('error', 'Quantity must be between 1 and 5.');
                return;
            }

            // Redirect to checkout with variant and quantity as URL parameters
            window.location.href = `checkout.php?variant_id=${variantId}&quantity=${quantity}`;
        });

        if (colorOptions.querySelector('.color-btn')) {
            colorOptions.querySelector('.color-btn').click();
        }
        updateUI();
    });
</script>

<?php require_once '../includes/footer.php'; ?>