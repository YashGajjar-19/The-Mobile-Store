<?php
require_once 'includes/auth.php';
require_once 'includes/config.php';
require_once 'includes/header.php';
require_once 'includes/navbar.php';


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

// --- Fetch Data for Homepage ---
// 1. Fetch 5 random "Hot", "Trending", or "New" products for the slider
$featured_products_stmt = $conn->prepare("
    SELECT
        p.product_id,
        p.product_name,
        p.status,
        MIN(pv.price) as starting_price,
        (SELECT image_url FROM product_color_images pci WHERE pci.product_id = p.product_id AND pci.is_thumbnail = 1 LIMIT 1) as image_url
    FROM products p
    JOIN product_variants pv ON p.product_id = pv.product_id
    WHERE p.status IN ('New', 'Hot', 'Trending')
    GROUP BY p.product_id
    ORDER BY RAND()
    LIMIT 5
");

$featured_products_stmt->execute();
$featured_products = $featured_products_stmt->get_result();

// 2. Fetch 10 random products for the main grid
$random_products_stmt = $conn->prepare("
    SELECT
        p.product_id,
        p.product_name,
        p.status,
        MIN(pv.price) as starting_price,
        (SELECT image_url FROM product_color_images pci WHERE pci.product_id = p.product_id AND pci.is_thumbnail = 1 LIMIT 1) as image_url
    FROM products p
    JOIN product_variants pv ON p.product_id = pv.product_id
    GROUP BY p.product_id
    ORDER BY RAND()
    LIMIT 10
");

$random_products_stmt->execute();
$random_products = $random_products_stmt->get_result();

// 3. Fetch brands for the "Shop by Brand" section
$brands_result = $conn->query("SELECT * FROM brands");

// Determine account link
if (isset($_SESSION['user_id'])) {
    $account_link = './user/profile.php';
    $account_text = 'Account';
} else {
    $account_link = './user/register.php';
    $account_text = 'Login / Register';
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home | The Mobile Store</title>

    <link
        href="https://fonts.googleapis.com/css2?family=Material+Symbols+Rounded:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200"
        rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css"
        crossorigin="anonymous" referrerpolicy="no-referrer" />

    <link rel="stylesheet" href="./assets/css/main.css">
</head>

<body>
    <section class="hero-section">
        <div class="hero-container">
            <div class="hero-image-column">
                <img src="./assets/images/svg/hero.svg" alt="Hero Image" class="hero-image">
            </div>

            <div class="hero-text-column">
                <h1 class="hero-headline">Welcome to the world of premium smart phones</h1>
                <p class="hero-subheadline">Discover cutting-edge technology and elegant design, crafted for a seamless
                    mobile experience.</p>
                <button class="button" onclick="window.location.href='#brand-section'">Explore More</button>
            </div>
        </div>
    </section>

    <section class="feature-section">
        <div class="feature-title">
            <h2>Featured Products</h2>
            <div class="title-line"></div>
        </div>
        <div class="slider-wrapper">
            <div class="slider-container">
                <?php while ($product = $featured_products->fetch_assoc()): ?>
                    <div class="slide">
                        <div class="slide-image">
                            <div class="slide-status <?php echo strtolower(htmlspecialchars($product['status'])); ?>"><?php echo htmlspecialchars($product['status']); ?></div>
                            <img src="./assets/images/products/<?php echo htmlspecialchars($product['image_url']); ?>" alt="<?php echo htmlspecialchars($product['product_name']); ?>">
                        </div>
                        <div class="slide-details">
                            <div class="slide-name"><?php echo htmlspecialchars($product['product_name']); ?></div>
                            <div class="slide-price">From &#8377;<?php echo number_format($product['starting_price']); ?></div>
                            <a href="./products/product.php?id=<?php echo $product['product_id']; ?>" class="button" style="width: 25%; border-radius: 50px; text-decoration:none;">Buy Now</a>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>
            <div class="dots-container">
                <?php mysqli_data_seek($featured_products, 0); ?>
                <?php for ($i = 0; $i < $featured_products->num_rows; $i++): ?>
                    <div class="dot" data-slide="<?php echo $i; ?>"></div>
                <?php endfor; ?>
            </div>
        </div>
    </section>

    <section class="shop-by-brand" id="brand-section">
        <div class="section-title">
            <h2>Shop By Brand</h2>
            <div class="title-line"></div>
        </div>
        <div class="brands-grid">
            <?php if ($brands_result->num_rows > 0): ?>
                <?php while ($brand = $brands_result->fetch_assoc()): ?>
                    <a href="./products/brand.php?id=<?php echo $brand['brand_id']; ?>" class="brand-card">
                        <img src="./assets/images/brands-logo/<?php echo htmlspecialchars($brand['brand_logo_url']); ?>" alt="<?php echo htmlspecialchars($brand['brand_name']); ?>">
                    </a>
                <?php endwhile; ?>
            <?php else: ?>
                <p>No brands found.</p>
            <?php endif; ?>
        </div>
    </section>

    <div class="alert-container"></div>
    <section class="products-section">
        <div class="section-title">
            <h2>Browse Products</h2>
            <div class="title-line"></div>
        </div>
        <div class="products-grid">
            <?php mysqli_data_seek($random_products, 0); ?>
            <?php while ($product = $random_products->fetch_assoc()): ?>
                <div class="product-card">
                    <?php $is_wishlisted = in_array($product['product_id'], $wishlist_product_ids); ?>
                    <button class="wishlist-btn <?php if ($is_wishlisted) echo 'active'; ?>" data-product-id="<?php echo $product['product_id']; ?>">
                        <span class="material-symbols-rounded">favorite</span>
                    </button>

                    <?php if ($product['status']): ?>
                        <div class="product-badge <?php echo strtolower(htmlspecialchars($product['status'])); ?>"><?php echo htmlspecialchars($product['status']); ?></div>
                    <?php endif; ?>
                    <h3 class="product-title"><?php echo htmlspecialchars($product['product_name']); ?></h3>
                    <div class="product-image-container">
                        <a href="./products/product.php?id=<?php echo $product['product_id']; ?>">
                            <img src="./assets/images/products/<?php echo htmlspecialchars($product['image_url']); ?>" alt="<?php echo htmlspecialchars($product['product_name']); ?>">
                        </a>
                    </div>
                    <div class="product-info-bottom">
                        <div class="product-price">From &#8377;<?php echo number_format($product['starting_price']); ?></div>
                        <a href="./products/product.php?id=<?php echo $product['product_id']; ?>" class="button buy-button">View</a>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>
        <div class="browse-more-container">
            <a href="./products/index.php" class="button browse-more-btn">Browse All Products</a>
        </div>
    </section>

    <?php require_once 'includes/footer.php'; ?>

    <script src="./assets/js/main.js"></script>
</body>

</html>