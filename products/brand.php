<?php
require_once '../includes/config.php';

// Check if a brand ID is provided in the URL
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: ../index.php"); // Redirect if no ID
    exit();
}

$brand_id = $_GET['id'];

// --- Fetch Brand Details (including the logo URL) ---
$brand_stmt = $conn->prepare("SELECT brand_name, brand_logo_url FROM brands WHERE brand_id = ?");
$brand_stmt->bind_param("i", $brand_id);
$brand_stmt->execute();
$brand_result = $brand_stmt->get_result();
if ($brand_result->num_rows === 0) {
    header("Location: ../404.html"); // Redirect if brand not found
    exit();
}
$brand = $brand_result->fetch_assoc();
$brand_name = $brand['brand_name'];
$brand_logo_url = $brand['brand_logo_url'];
$brand_stmt->close();


// --- Fetch Products for this Brand ---
$products_stmt = $conn->prepare("
    SELECT 
        p.product_id,
        p.product_name,
        MIN(pv.price) as starting_price,
        (SELECT image_url FROM product_images pi JOIN product_variants pv_img ON pi.variant_id = pv_img.variant_id WHERE pv_img.product_id = p.product_id AND pi.is_thumbnail = 1 LIMIT 1) as image_url
    FROM products p
    JOIN product_variants pv ON p.product_id = pv.product_id
    WHERE p.brand_id = ?
    GROUP BY p.product_id, p.product_name
    ORDER BY p.created_at DESC
");
$products_stmt->bind_param("i", $brand_id);
$products_stmt->execute();
$products_result = $products_stmt->get_result();

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($brand_name); ?> Products | The Mobile Store</title>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Rounded:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" rel="stylesheet" />
    <link rel="stylesheet" href="../assets/css/main.css">
    <style>
        /* New styles for consistent logo sizing */
        .brand-logo-container {
            height: 50px;
            display: flex;
            justify-content: center;
            align-items: center;
            margin-bottom: 10px;
        }

        .brand-logo-container img {
            max-width: 250px;
            max-height: 150px;
            width: auto;
            height: auto;
            object-fit: contain;
        }
    </style>
</head>

<body>
    <section class="header-container" id="header-section">
        <header class="header">
            <a href="./index.php" class="logo-container">
                <img class="logo-image" src="../assets/images/Logo.png" alt="The Mobile Store">
                <span class="logo-text">The Mobile Store</span>
            </a>
            <div class="nav-wrapper">
                <nav class="navbar">
                    <div class="search-container">
                        <input type="search" class="search-input" placeholder="Search...">
                        <a class="nav-icon search-btn">
                            <span class="material-symbols-rounded">search</span>
                        </a>
                    </div>
                    <a href="../index.php" class="nav-icon">
                        <span class="material-symbols-rounded">home</span>
                    </a>
                    <a href="#" class="nav-icon">
                        <span class="material-symbols-rounded">shopping_cart</span>
                        <span class="cart-badge">3</span>
                    </a>
                    <a href="<?php echo $account_link; ?>" class="nav-icon">
                        <span class="material-symbols-rounded">account_circle</span>
                    </a>
                </nav>

                <button class="mobile-menu-btn">
                    <span class="material-symbols-rounded">
                        menu
                    </span>
                </button>
            </div>

            <div class="mobile-menu">
                <div class="mobile-search-container">
                    <input type="search" class="mobile-search-input" placeholder="Search...">
                    <button class="mobile-search-btn">
                        <span class="material-symbols-rounded">search</span>
                    </button>
                </div>
                <a href="../index.php" class="mobile-nav-icon">
                    <span class="material-symbols-rounded">home</span>
                    <span>Home</span>
                </a>
                <a href="#" class="mobile-nav-icon">
                    <span class="material-symbols-rounded">shopping_cart</span>
                    <span>Cart</span>
                    <span class="cart-badge">3</span>
                </a>
                <a href="<?php echo $account_link; ?>" class="mobile-nav-icon">
                    <span class="material-symbols-rounded">account_circle</span>
                    <span><?php echo $account_text; ?></span>
                </a>
                <?php if (isset($_SESSION['user_id'])): ?>
                    <a href="./includes/auth.php?action=logout" class="mobile-nav-icon">
                        <span class="material-symbols-rounded">logout</span>
                        <span>Logout</span>
                    </a>
                <?php endif; ?>
            </div>
        </header>
    </section>

    <main class="products-page-section" style="padding-top: 150px;">
        <div class="section-title">
            <div class="brand-logo-container">
                <img src="../assets/images/brands-logo/<?php echo htmlspecialchars($brand_logo_url); ?>" alt="<?php echo htmlspecialchars($brand_name); ?> Logo">
            </div>
            <div class="title-line" style="margin-top: 45px;"></div>
        </div>

        <div class="products-grid">
            <?php if ($products_result->num_rows > 0): ?>
                <?php while ($product = $products_result->fetch_assoc()): ?>
                    <div class="product-card">
                        <h3 class="product-title"><?php echo htmlspecialchars($product['product_name']); ?></h3>
                        <div class="product-image-container">
                            <img src="../assets/images/products/<?php echo htmlspecialchars($product['image_url']); ?>" alt="<?php echo htmlspecialchars($product['product_name']); ?>">
                        </div>
                        <div class="product-info-bottom">
                            <div class="product-price">From &#8377;<?php echo number_format($product['starting_price']); ?></div>
                            <button class="button buy-button">View</button>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <p style="text-align: center; grid-column: 1 / -1;">No products found for this brand yet.</p>
            <?php endif; ?>
        </div>
    </main>


    <footer class="footer">
        <div class="footer-content">
            <div class="footer-section">
                <div class="footer-logo-container">
                    <img class="logo-image" src="../assets/images/logo.png" alt="The Mobile Store">
                    <span class="logo-text">The Mobile Store</span>
                </div>
                <p>Your one-stop shop for the latest mobile devices. We bring the future to your
                    fingertips.
                </p>
                <div class="social-links">
                    <a href="#" class="social-link" aria-label="Facebook">
                        <i class="fab fa-facebook-f"></i>
                    </a>
                    <a href="#" class="social-link" aria-label="Instagram">
                        <i class="fab fa-instagram"></i>
                    </a>
                    <a href="#" class="social-link" aria-label="Whatsapp">
                        <i class="fab fa-whatsapp"></i>
                    </a>
                    <a href="#" class="social-link" aria-label="Gmail">
                        <i class="fa-regular fa-envelope"></i>
                    </a>
                </div>
            </div>

            <div class="footer-section">
                <h3>Quick Links</h3>
                <ul>
                    <li><a href="../index.php">Home</a></li>
                    <li><a href="products.html">Products</a></li>
                    <li><a href="./about.html">About Us</a></li>
                    <li><a href="./contact.html">Contact</a></li>
                    <li><a href="./privacy.html">Privacy Policy</a></li>
                </ul>
            </div>

            <div class="footer-section">
                <h3>Customer Service</h3>
                <ul>
                    <li><a href="./faq.html">FAQ</a></li>
                    <li><a href="./returns.html">Returns & Refunds</a></li>
                    <li><a href="./shipping.html">Shipping Information</a></li>
                </ul>
            </div>

            <div class="footer-section footer-contact">
                <a href="./contact.html">
                    <h3>Contact Us</h3>
                </a>
                <p>123 Mobile Street, Tech City, 12345</p>
                <p>Email: <a href="#">info@themobilestore.com</a></p>
                <p>Phone: <a href="#">+91 1234567890</a></p>
            </div>
        </div>

        <div class="footer-bottom">
            &copy; 2025 The Mobile Store. All rights reserved.
        </div>
    </footer>

</body>

</html>