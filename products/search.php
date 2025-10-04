<?php
session_start();
$page_title = 'Search | The Mobile Store';
require_once '../includes/config.php';
require_once '../includes/header.php';
require_once '../includes/navbar.php';

// Get the search query from the URL
$search_query = isset($_GET['query']) ? trim($_GET['query']) : '';

// --- Fetch User's Wishlist (if logged in) ---
$wishlist_product_ids = [];
if (isset($_SESSION['user_id'])) {
    $wishlist_stmt = $conn->prepare("SELECT product_id FROM wishlist WHERE user_id = ?");
    $wishlist_stmt->bind_param("i", $_SESSION['user_id']);
    $wishlist_stmt->execute();
    $wishlist_result = $wishlist_stmt->get_result();
    while ($row = $wishlist_result->fetch_assoc()) {
        $wishlist_product_ids[] = $row['product_id'];
    }
    $wishlist_stmt->close();
}

// Fetch products based on the search query
$products_result = null;
if (!empty($search_query)) {
    $like_query = "%" . $search_query . "%";

    $products_stmt = $conn->prepare("
    SELECT
        p.product_id,
        p.product_name,
        p.status,
        MIN(pv.price) as starting_price,
        (SELECT image_url FROM product_color_images pci WHERE pci.product_id = p.product_id AND pci.is_thumbnail = 1 LIMIT 1) as image_url
    FROM products p
    JOIN product_variants pv ON p.product_id = pv.product_id
    JOIN brands b ON p.brand_id = b.brand_id
    WHERE p.product_name LIKE ? OR b.brand_name LIKE ?
    GROUP BY p.product_id
    ORDER BY p.created_at DESC
");
    $products_stmt->bind_param("ss", $like_query, $like_query);
    $products_stmt->execute();
    $products_result = $products_stmt->get_result();
}

// Determine account link for header
if (isset($_SESSION['user_id'])) {
    $account_link = '../user/profile.php';
    $account_text = 'Account';
} else {
    $account_link = '../user/register.php';
    $account_text = 'Login / Register';
}
?>

<main class="products-page-section" style="padding-top: 150px;">
    <!-- Header -->
    <div class="section-title">
        <h2>Search Results</h2>
        <p>Showing results for: <strong>"<?php echo htmlspecialchars($search_query); ?>"</strong></p>
        <div class="title-line"></div>
    </div>

    <!-- Alert box -->
    <div class="alert-container"></div>

    <!-- Products searched -->
    <div class="products-grid">
        <?php if ($products_result && $products_result->num_rows > 0): ?>
            <?php while ($product = $products_result->fetch_assoc()): ?>
                <!-- Product cards -->
                <div class="product-card">
                    <!-- Wishlist -->
                    <?php $is_wishlisted = in_array($product['product_id'], $wishlist_product_ids); ?>
                    <button class="wishlist-btn <?php if ($is_wishlisted) echo 'active'; ?>" data-product-id="<?php echo $product['product_id']; ?>">
                        <span class="material-symbols-rounded">favorite</span>
                    </button>

                    <!-- Status -->
                    <?php if ($product['status']): ?>
                        <div class="product-badge <?php echo strtolower(htmlspecialchars($product['status'])); ?>">
                            <?php echo htmlspecialchars($product['status']); ?>
                        </div>
                    <?php endif; ?>

                    <!-- Product name -->
                    <h3 class="product-title">
                        <?php echo htmlspecialchars($product['product_name']); ?>
                    </h3>

                    <!-- Product image -->
                    <div class="product-image-container">
                        <a href="product.php?id=<?php echo $product['product_id']; ?>">
                            <img src="../assets/images/products/<?php echo htmlspecialchars($product['image_url']); ?>" alt="<?php echo htmlspecialchars($product['product_name']); ?>">
                        </a>
                    </div>

                    <!-- Price -->
                    <div class="product-info-bottom">
                        <div class="product-price">
                            From &#8377;<?php echo number_format($product['starting_price']); ?>
                        </div>
                        <!-- Buy now button -->
                        <a href="product.php?id=<?php echo $product['product_id']; ?>" class="button buy-button">
                            View
                        </a>
                    </div>
                </div>
            <?php endwhile; ?>
            <!-- Product card ends -->

            <!-- If no product founded -->
        <?php else: ?>
            <p style="text-align: center; grid-column: 1 / -1; padding: 40px;">
                No products found matching your search. Try a different keyword.
            </p>
        <?php endif; ?>
    </div>
    <!-- Product grid ends -->
</main>

<script src="../assets/js/main.js"></script>