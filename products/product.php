<?php
session_start();
require_once '../includes/config.php';

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: index.php");
    exit();
}
$product_id = $_GET['id'];

// --- Fetch Product Details ---
$stmt = $conn->prepare("SELECT p.product_name, p.description, p.specifications, b.brand_name FROM products p JOIN brands b ON p.brand_id = b.brand_id WHERE p.product_id = ?");
$stmt->bind_param("i", $product_id);
$stmt->execute();
$product_result = $stmt->get_result();
if ($product_result->num_rows === 0) {
    header("Location: ../404.html");
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

// --- Fetch All Images, Grouped by Color ---
$images_stmt = $conn->prepare("SELECT DISTINCT pv.color, pi.image_url FROM product_images pi JOIN product_variants pv ON pi.variant_id = pv.variant_id WHERE pv.product_id = ? ORDER BY pi.image_id");
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
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($product['product_name']); ?> | The Mobile Store</title>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Rounded:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" rel="stylesheet" />
    <link rel="stylesheet" href="../assets/css/main.css">
    <link rel="stylesheet" href="../assets/css/pages/_product-page.css">
</head>

<body>
    <main class="product-page-container">
        <a href="javascript:history.back()" class="button back-button"><span class="material-symbols-rounded">arrow_back</span> Back</a>
        <section class="product-images">
            <div class="main-image-container"></div>
            <div class="thumbnail-strip"></div>

            <div class="product-description">
                <h3 style="margin-bottom: 15px;">Description:</h3>
                <p><?php echo nl2br(htmlspecialchars($product['description'])); ?></p>
            </div>

            <div class="review-list">
                <div class="section-title" style="text-align: left;">
                    <h2 style="margin:0;">Customer Reviews</h2>
                    <div class="title-line" style="margin: 10px 0 0 0;"></div>
                </div>
                <hr>
                <?php if ($reviews->num_rows > 0): ?>
                    <?php while ($review = $reviews->fetch_assoc()): ?>
                        <div class="review-card">
                            <div class="review-header">
                                <div class="review-avatar"><?php echo strtoupper(substr($review['full_name'], 0, 1)); ?></div>
                                <div class="review-author-info">
                                    <div class="author-name"><?php echo htmlspecialchars($review['full_name']); ?></div>
                                    <div class="review-date"><?php echo date("F j, Y", strtotime($review['created_at'])); ?></div>
                                </div>
                            </div>
                            <div class="star-rating">
                                <?php for ($i = 1; $i <= 5; $i++): ?>
                                    <span class="material-symbols-rounded"
                                        data-filled="<?php echo ($i <= $review['rating']) ? 'true' : 'false'; ?>">
                                        star
                                    </span>
                                <?php endfor; ?>
                            </div>
                            <p class="review-comment"><?php echo nl2br(htmlspecialchars($review['comment'])); ?></p>
                        </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <p>No reviews yet. Be the first to review this product!</p>
                <?php endif; ?>
            </div>
        </section>

        <section class="product-details">
            <p class="brand-name"><?php echo htmlspecialchars($product['brand_name']); ?></p>
            <h1 class="product-main-name"><?php echo htmlspecialchars($product['product_name']); ?></h1>
            <p class="product-price-display" id="price-display">Select a variant</p>

            <div class="variant-options">
                <div class="variant-options-color">
                    <div class="option-group" id="color-options">
                        <label class="option-label">Color:</label>
                        <div class="option-choices">
                            <?php foreach (array_keys($images_by_color) as $color): ?>
                                <button class="choice-btn color-btn" data-color="<?php echo htmlspecialchars($color); ?>"><?php echo htmlspecialchars($color); ?></button>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>

                <div class="variant-options-grid">
                    <div class="option-group" id="ram-options" style="display: none;"><label class="option-label">RAM:</label>
                        <div class="option-choices"></div>
                    </div>
                    <div class="option-group" id="storage-options" style="display: none;"><label class="option-label">Storage:</label>
                        <div class="option-choices"></div>
                    </div>
                </div>
            </div>

            <div class="product-actions">
                <button class="button" style="flex-grow: 1;">Add to Cart</button>
                <button class="button" style="flex-grow: 1;">Buy Now</button>
            </div>

            <?php if (!empty($product['specifications'])): ?>
                <div class="product-specifications">
                    <h3 style="margin-bottom: 15px;">Specifications:</h3>
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

            <section class="reviews-section" style="max-width: 1400px; margin: 10px auto;">
                <?php if (isset($_SESSION['user_id'])): ?>
                    <div class="review-form-container" style="margin-bottom: 20px;">
                        <h3>Write a Review</h3>
                        <div class="title-line" style="margin: 5px 0 15px 0; width: 100px;"></div>
                        <form action="../includes/submit_review.php" method="POST" class="auth-form">
                            <input type="hidden" name="product_id" value="<?php echo $product_id; ?>">

                            <div class="form-group">
                                <label class="form-label">Your Rating</label>

                                <div class="rating-input">
                                    <input type="radio" id="star5" name="rating" value="5" required><label for="star5" title="5 stars">&#9733;</label>
                                    <input type="radio" id="star4" name="rating" value="4"><label for="star4" title="4 stars">&#9733;</label>
                                    <input type="radio" id="star3" name="rating" value="3"><label for="star3" title="3 stars">&#9733;</label>
                                    <input type="radio" id="star2" name="rating" value="2"><label for="star2" title="2 stars">&#9733;</label>
                                    <input type="radio" id="star1" name="rating" value="1"><label for="star1" title="1 star">&#9733;</label>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="comment" class="form-label">Your Review</label>
                                <div class="form-input">
                                    <textarea name="comment" id="comment" rows="3" placeholder="Share your thoughts on the product..." style="width: 838px; padding: 10px;"></textarea>
                                </div>
                            </div>
                            <div class="form-submit">
                                <button type="submit" class="button">Submit Review</button>
                            </div>
                        </form>
                    </div>
                <?php else: ?>
                    <p>Please <a href="../user/login.php?redirect=products/product.php?id=<?php echo $product_id; ?>">log in</a> to write a review.</p>
                <?php endif; ?>
            </section>
        </section>
    </main>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const variantsData = <?php echo json_encode($variants); ?>;
            const imagesData = <?php echo json_encode($images_by_color); ?>;

            const mainImageContainer = document.querySelector('.main-image-container');
            const thumbnailStrip = document.querySelector('.thumbnail-strip');
            const priceDisplay = document.getElementById('price-display');
            const colorOptions = document.getElementById('color-options');
            const ramOptions = document.getElementById('ram-options');
            const storageOptions = document.getElementById('storage-options');

            let selectedColor = null,
                selectedRam = null,
                selectedStorage = null;

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

            function updateVariantOptions(groupElement, options, type, autoSelect = false) {
                const choicesContainer = groupElement.querySelector('.option-choices');
                choicesContainer.innerHTML = '';
                if (options.size > 0) {
                    groupElement.style.display = 'block';
                    options.forEach(option => {
                        const btn = document.createElement('button');
                        btn.className = 'choice-btn';
                        btn.dataset[type] = option;
                        btn.textContent = type === 'storage' && option >= 1024 ? `${option/1024} TB` : `${option} GB`;
                        choicesContainer.appendChild(btn);
                    });
                    if (autoSelect && options.size === 1) {
                        choicesContainer.querySelector('.choice-btn').click();
                    }
                } else {
                    groupElement.style.display = 'none';
                }
            }

            function updatePrice() {
                if (selectedColor && selectedRam && selectedStorage) {
                    const variant = variantsData.find(v => v.color === selectedColor && v.ram_gb == selectedRam && v.storage_gb == selectedStorage);
                    priceDisplay.textContent = variant ? `₹${parseInt(variant.price).toLocaleString()}` : 'Unavailable';
                } else {
                    const firstVariant = variantsData.find(v => v.color === selectedColor);
                    if (firstVariant) {
                        priceDisplay.textContent = `From ₹${parseInt(firstVariant.price).toLocaleString()}`;
                    } else {
                        priceDisplay.textContent = 'Select a variant';
                    }
                }
            }

            colorOptions.addEventListener('click', e => {
                if (e.target.classList.contains('color-btn')) {
                    selectedColor = e.target.dataset.color;
                    selectedRam = null;
                    selectedStorage = null;

                    document.querySelectorAll('.color-btn.active').forEach(b => b.classList.remove('active'));
                    e.target.classList.add('active');

                    const availableRams = new Set(variantsData.filter(v => v.color === selectedColor).map(v => v.ram_gb));
                    updateVariantOptions(ramOptions, availableRams, 'ram', true);

                    if (availableRams.size !== 1) {
                        storageOptions.style.display = 'none';
                    }
                    updatePrice();
                    updateImages();
                }
            });

            ramOptions.addEventListener('click', e => {
                if (e.target.classList.contains('choice-btn')) {
                    selectedRam = e.target.dataset.ram;
                    selectedStorage = null;
                    document.querySelectorAll('#ram-options .choice-btn.active').forEach(b => b.classList.remove('active'));
                    e.target.classList.add('active');

                    const availableStorages = new Set(variantsData.filter(v => v.color === selectedColor && v.ram_gb == selectedRam).map(v => v.storage_gb));
                    updateVariantOptions(storageOptions, availableStorages, 'storage');
                    updatePrice();
                }
            });

            storageOptions.addEventListener('click', e => {
                if (e.target.classList.contains('choice-btn')) {
                    selectedStorage = e.target.dataset.storage;
                    document.querySelectorAll('#storage-options .choice-btn.active').forEach(b => b.classList.remove('active'));
                    e.target.classList.add('active');
                    updatePrice();
                }
            });

            if (colorOptions.querySelector('.color-btn')) {
                colorOptions.querySelector('.color-btn').click();
            }
        });
    </script>
</body>

</html>