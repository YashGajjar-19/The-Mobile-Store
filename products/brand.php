<?php
session_start();
require_once '../includes/config.php';

// Check if a brand ID is provided in the URL
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: ../404.php"); // Redirect if no ID
    exit();
}

// Check if a brand ID is provided in the URL
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: ../index.php"); // Redirect if no ID
    exit();
}
$brand_id = $_GET['id'];

// --- Fetch Brand Details ---
$brand_stmt = $conn->prepare("SELECT brand_name, brand_logo_url FROM brands WHERE brand_id = ?");
$brand_stmt->bind_param("i", $brand_id);
$brand_stmt->execute();
$brand_result = $brand_stmt->get_result();
if ($brand_result->num_rows === 0) {
    header("Location: ../404.html"); // Redirect if brand not found
    exit();
}
$brand = $brand_result->fetch_assoc();
$brand_stmt->close();

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
$where_clauses = ['p.brand_id = ?']; // Always filter by brand
$having_clauses = [];
$params = [$brand_id];
$types = 'i';

if (!empty($popularity)) {
    $where_clauses[] = "p.status = ?";
    $params[] = $popularity;
    $types .= 's';
}
$where_sql = " WHERE " . implode(' AND ', $where_clauses);

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
$total_pages = ceil($total_products / $products_per_page);

// --- Get Products for the Current Page ---
$products_sql = "
    SELECT p.product_id, p.product_name, p.status, MIN(pv.price) as starting_price,
    (SELECT image_url FROM product_color_images pci WHERE pci.product_id = p.product_id AND pci.is_thumbnail = 1 LIMIT 1) as image_url
" . $base_sql . $where_sql . $group_by_sql . $having_sql;

switch ($sort_option) {
    case 'price_asc':
        $products_sql .= " ORDER BY starting_price ASC";
        break;
    case 'price_desc':
        $products_sql .= " ORDER BY starting_price DESC";
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

// Determine account link for header
if (isset($_SESSION['user_id'])) {
    $account_link = '../user/profile.php';
    $account_text = 'Account';
} else {
    $account_link = '../user/register.php';
    $account_text = 'Login / Register';
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($brand['brand_name']); ?> Products | The Mobile Store</title>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Rounded:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" rel="stylesheet" />
    <link rel="stylesheet" href="../assets/css/main.css">
    <style>
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
            <a href="../index.php" class="logo-container">
                <img class="logo-image" src="../assets/images/Logo.png" alt="The Mobile Store">
                <span class="logo-text">The Mobile Store</span>
            </a>
            <div class="nav-wrapper">
                <nav class="navbar">
                    <form action="search.php" method="GET" class="search-container">
                        <input type="search" name="query" class="search-input" placeholder="Search..." required>
                        <button type="submit" class="nav-icon search-btn" style="border:none; background:transparent; cursor:pointer;">
                            <span class="material-symbols-rounded">search</span>
                        </button>
                    </form>
                    <a href="../index.php" class="nav-icon"><span class="material-symbols-rounded">home</span></a>
                    <a href="#" class="nav-icon"><span class="material-symbols-rounded">shopping_cart</span><span class="cart-badge">3</span></a>
                    <a href="<?php echo $account_link; ?>" class="nav-icon"><span class="material-symbols-rounded">account_circle</span></a>
                </nav>
                <button class="mobile-menu-btn"><span class="material-symbols-rounded">menu</span></button>
            </div>
            <div class="mobile-menu">
                <form action="search.php" method="GET" class="mobile-search-container">
                    <input type="search" name="query" class="mobile-search-input" placeholder="Search..." required>
                    <button type="submit" class="mobile-search-btn"><span class="material-symbols-rounded">search</span></button>
                </form>
                <a href="../index.php" class="mobile-nav-icon"><span class="material-symbols-rounded">home</span><span>Home</span></a>
                <a href="#" class="mobile-nav-icon"><span class="material-symbols-rounded">shopping_cart</span><span>Cart</span><span class="cart-badge">3</span></a>
                <a href="<?php echo $account_link; ?>" class="mobile-nav-icon"><span class="material-symbols-rounded">account_circle</span><span><?php echo $account_text; ?></span></a>
                <?php if (isset($_SESSION['user_id'])): ?>
                    <a href="../includes/auth.php?action=logout" class="mobile-nav-icon"><span class="material-symbols-rounded">logout</span><span>Logout</span></a>
                <?php endif; ?>
            </div>
        </header>
    </section>

    <main class="products-page-section" style="padding-top: 150px;">
        <div class="section-title">
            <div class="brand-logo-container">
                <img src="../assets/images/brands-logo/<?php echo htmlspecialchars($brand['brand_logo_url']); ?>" alt="<?php echo htmlspecialchars($brand['brand_name']); ?> Logo">
            </div>
            <div class="title-line" style="margin-top: 45px;"></div>
        </div>

        <div class="filter-options-bar">
            <form action="brand.php" method="GET" class="filter-form">
                <input type="hidden" name="id" value="<?php echo $brand_id; ?>">
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
                <a href="brand.php?id=<?php echo $brand_id; ?>" class="button button-secondary" style="width: 100px; margin: 0; padding: 12px 20px; text-decoration: none;">Reset</a>
            </form>
        </div>

        <div class="alert-container"></div>
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
                            <a href="product.php?id=<?php echo $product['product_id']; ?>">
                                <img src="../assets/images/products/<?php echo htmlspecialchars($product['image_url']); ?>" alt="<?php echo htmlspecialchars($product['product_name']); ?>">
                            </a>
                        </div>
                        <div class="product-info-bottom">
                            <div class="product-price">From &#8377;<?php echo number_format($product['starting_price']); ?></div>
                            <a href="product.php?id=<?php echo $product['product_id']; ?>" class="button buy-button">View</a>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <p style="text-align: center; grid-column: 1 / -1; padding: 40px;">No products found for this brand matching your criteria.</p>
            <?php endif; ?>
        </div>

        <div class="pagination">
            <?php
            if ($total_pages > 1):
                // Build query string while excluding 'page'
                $query_params = $_GET;
                unset($query_params['page']);
                $query_string = http_build_query($query_params);

                for ($i = 1; $i <= $total_pages; $i++):
            ?>
                    <a href="?<?php echo $query_string . '&page=' . $i; ?>" class="<?php if ($i == $current_page) echo 'active'; ?>"><?php echo $i; ?></a>
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
            </div>
            <div class="footer-section">
                <h3>Quick Links</h3>
                <ul>
                    <li><a href="../index.php">Home</a></li>
                    <li><a href="index.php">Products</a></li>
                </ul>
            </div>
            <div class="footer-section footer-contact">
                <a href="../contact.php">
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