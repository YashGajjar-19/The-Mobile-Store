<?php
require_once '../includes/config.php';

// --- Fetch User's Wishlist ---
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

// --- Pagination Configuration ---
$products_per_page = 12;
$current_page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($current_page - 1) * $products_per_page;

// --- Initialize Filter and Sort Variables ---
$sort_option = isset($_GET['sort']) ? $_GET['sort'] : 'default';
$price_range = isset($_GET['price_range']) ? $_GET['price_range'] : '';
$popularity = isset($_GET['popularity']) ? $_GET['popularity'] : '';

// --- Build the SQL Query for fetching products ---
$base_sql = "
    FROM products p
    JOIN product_variants pv ON p.product_id = pv.product_id
";
$where_clauses = [];
$having_clauses = [];
$params = [];
$types = '';

if (!empty($popularity)) {
    $where_clauses[] = "p.status = ?";
    $params[] = $popularity;
    $types .= 's';
}
$where_sql = count($where_clauses) > 0 ? " WHERE " . implode(' AND ', $where_clauses) : "";

$group_by_sql = " GROUP BY p.product_id";

if (!empty($price_range)) {
    list($min_price, $max_price) = explode('-', $price_range);
    $having_clauses[] = "MIN(pv.price) BETWEEN ? AND ?";
    $params[] = (int)$min_price;
    $params[] = (int)$max_price;
    $types .= 'ii';
}
$having_sql = count($having_clauses) > 0 ? " HAVING " . implode(' AND ', $having_clauses) : "";

// --- Get Total Product Count for Pagination ---
$count_sql = "SELECT COUNT(DISTINCT p.product_id) as total " . $base_sql . $where_sql . $having_sql;
$count_stmt = $conn->prepare($count_sql);
if (!empty($types)) {
    $count_stmt->bind_param($types, ...$params);
}
$count_stmt->execute();
$total_products = $count_stmt->get_result()->fetch_assoc()['total'];
$total_pages = ceil($total_products / $products_per_page); // Correct variable name is here

// --- Get Products for the Current Page ---
$products_sql = "
    SELECT p.product_id, p.product_name, p.status, MIN(pv.price) as starting_price,
    (SELECT image_url FROM product_images pi JOIN product_variants pv_img ON pi.variant_id = pv_img.variant_id WHERE pv_img.product_id = p.product_id AND pi.is_thumbnail = 1 LIMIT 1) as image_url
" . $base_sql . $where_sql . $group_by_sql . $having_sql;

switch ($sort_option) {
    case 'price_asc':
        $products_sql .= " ORDER BY starting_price ASC";
        break;
    case 'price_desc':
        $products_sql .= " ORDER BY starting_price DESC";
        break;
    case 'popularity':
        $products_sql .= " ORDER BY FIELD(p.status, 'Hot', 'Trending', 'New') DESC, p.created_at DESC";
        break;
    default:
        $products_sql .= " ORDER BY p.created_at DESC";
        break;
}

$products_sql .= " LIMIT ? OFFSET ?";
$params[] = $products_per_page;
$params[] = $offset;
$types .= 'ii';

$products_stmt = $conn->prepare($products_sql);
$products_stmt->bind_param($types, ...$params);
$products_stmt->execute();
$products_result = $products_stmt->get_result();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>All Products | The Mobile Store</title>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Rounded:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" rel="stylesheet" />
    <link rel="stylesheet" href="../assets/css/main.css">
</head>

<body>
    <section class="header-container" id="header-section">
        <header class="header">
            <a href="../index.php" class="logo-container">
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
                    <a href="../includes/auth.php?action=logout" class="mobile-nav-icon">
                        <span class="material-symbols-rounded">logout</span>
                        <span>Logout</span>
                    </a>
                <?php endif; ?>
            </div>
        </header>
    </section>

    <div class="alert-container"></div>
    <main class="products-page-section" style="padding-top: 80px;">
        <div class="section-title">
            <h2>All Products</h2>
            <div class="title-line"></div>
        </div>

        <div class="filter-options-bar">
            <form action="index.php" method="GET" class="filter-form">
                <div class="form-group">
                    <label class="form-label">Price Range:</label>
                    <select name="price_range">
                        <option value="">All Prices</option>
                        <option value="0-50000" <?php if ($price_range == '0-50000') echo 'selected'; ?>>Under ₹50,000</option>
                        <option value="50000-70000" <?php if ($price_range == '50000-70000') echo 'selected'; ?>>₹50,000 - ₹70,000</option>
                        <option value="70000-100000" <?php if ($price_range == '70000-100000') echo 'selected'; ?>>₹70,000 - ₹1,00,000</option>
                        <option value="100000-999999" <?php if ($price_range == '100000-999999') echo 'selected'; ?>>Over ₹1,00,000</option>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Popularity:</label>
                    <select name="popularity">
                        <option value="">All</option>
                        <option value="New" <?php if ($popularity == 'New') echo 'selected'; ?>>New</option>
                        <option value="Hot" <?php if ($popularity == 'Hot') echo 'selected'; ?>>Hot</option>
                        <option value="Trending" <?php if ($popularity == 'Trending') echo 'selected'; ?>>Trending</option>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Sort By:</label>
                    <select name="sort">
                        <option value="default" <?php if ($sort_option == 'default') echo 'selected'; ?>>Featured</option>
                        <option value="price_asc" <?php if ($sort_option == 'price_asc') echo 'selected'; ?>>Price: Low to High</option>
                        <option value="price_desc" <?php if ($sort_option == 'price_desc') echo 'selected'; ?>>Price: High to Low</option>
                    </select>
                </div>
                <button type="submit" class="button" style="width: 100px; margin: 0; padding: 12px 20px;">Apply</button>
                <a href="index.php" class="button button-secondary" style="width: 100px; margin: 0; padding: 12px 20px; text-decoration: none;">Reset</a>
            </form>
        </div>

        <div class="products-grid">
            <?php if ($products_result->num_rows > 0): ?>
                <?php while ($product = $products_result->fetch_assoc()): ?>
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
                            <img src="../assets/images/products/<?php echo htmlspecialchars($product['image_url']); ?>" alt="<?php echo htmlspecialchars($product['product_name']); ?>">
                        </div>

                        <div class="product-info-bottom">
                            <div class="product-price">From &#8377;<?php echo number_format($product['starting_price']); ?></div>
                            <a href="../products/product.php?id=<?php echo $product['product_id']; ?>" class="button buy-button">View</a>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <p style="text-align: center; grid-column: 1 / -1; padding: 40px;">No products match your criteria.</p>
            <?php endif; ?>
        </div>

        <div class="pagination">
            <?php
            if ($total_pages > 1):
                $query_params = http_build_query(array_merge($_GET, ['page' => '']));
                for ($i = 1; $i <= $total_pages; $i++):
            ?>
                    <a href="?<?php echo $query_params . $i; ?>" class="<?php if ($i == $current_page) echo 'active'; ?>"><?php echo $i; ?></a>
            <?php
                endfor;
            endif;
            ?>
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
                    <li><a href="./index.php">Home</a></li>
                    <li><a href="./products.html">Products</a></li>
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

    <script src="../assets/js/main.js"></script>
</body>

</html>