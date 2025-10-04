<?php
session_start();
$page_title = 'Brands | The Mobile Store';
require_once '../includes/config.php';
require_once '../includes/header.php';
require_once '../includes/navbar.php';

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

// Fetch Brand Details 
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

// Fetch User's Wishlist
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

// Pagination Configuration
$products_per_page = 12;
$current_page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($current_page - 1) * $products_per_page;

// Initialize Filter and Sort Variables
$sort_option = isset($_GET['sort']) ? $_GET['sort'] : 'default';
$price_range = isset($_GET['price_range']) ? $_GET['price_range'] : '';
$popularity = isset($_GET['popularity']) ? $_GET['popularity'] : '';

// Build the SQL Query for fetching products
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

// Get Total Product Count for Pagination
$count_sql = "SELECT COUNT(DISTINCT p.product_id) as total " . $base_sql . $where_sql . $having_sql;
$count_stmt = $conn->prepare($count_sql);
if (!empty($types)) {
    $count_stmt->bind_param($types, ...$params);
}
$count_stmt->execute();
$total_products = $count_stmt->get_result()->fetch_assoc()['total'];
$total_pages = ceil($total_products / $products_per_page);

// Get Products for the Current Page
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

<main class="products-page-section" style="padding-top: 150px;">
    <!-- Header -->
    <div class="section-title">
        <div class="brand-logo-container">
            <img src="../assets/images/brands-logo/<?php echo htmlspecialchars($brand['brand_logo_url']); ?>" alt="<?php echo htmlspecialchars($brand['brand_name']); ?> Logo">
        </div>
        <div class="title-line" style="margin-top: 45px;"></div>
    </div>

    <!-- Filter bar -->
    <div class="filter-options-bar">
        <!-- Filter form -->
        <form action="brand.php" method="GET" class="filter-form">
            <input type="hidden" name="id" value="<?php echo $brand_id; ?>">

            <!-- Price -->
            <div class="form-group">
                <label class="form-label">Price Range:</label>
                <select name="price_range">
                    <option value="">All Prices</option>

                    <option value="0-50000" <?php if ($price_range == '0-50000') echo 'selected'; ?>>
                        Under ₹50,000
                    </option>

                    <option value="50000-70000" <?php if ($price_range == '50000-70000') echo 'selected'; ?>>
                        ₹50,000 - ₹70,000
                    </option>

                    <option value="70000-100000" <?php if ($price_range == '70000-100000') echo 'selected'; ?>>
                        ₹70,000 - ₹1,00,000
                    </option>

                    <option value="100000-999999" <?php if ($price_range == '100000-999999') echo 'selected'; ?>>
                        Over ₹1,00,000
                    </option>
                </select>
            </div>

            <!-- Status -->
            <div class="form-group">
                <label class="form-label">Popularity:</label>
                <select name="popularity">
                    <option value="">All</option>

                    <option value="New" <?php if ($popularity == 'New') echo 'selected'; ?>>New</option>

                    <option value="Hot" <?php if ($popularity == 'Hot') echo 'selected'; ?>>Hot</option>

                    <option value="Trending" <?php if ($popularity == 'Trending') echo 'selected'; ?>>Trending</option>
                </select>
            </div>

            <!-- Price - High-Low, Low-High -->
            <div class="form-group">
                <label class="form-label">Sort By:</label>
                <select name="sort">
                    <option value="default" <?php if ($sort_option == 'default') echo 'selected'; ?>>
                        Featured
                    </option>

                    <option value="price_asc" <?php if ($sort_option == 'price_asc') echo 'selected'; ?>>
                        Price: Low to High
                    </option>

                    <option value="price_desc" <?php if ($sort_option == 'price_desc') echo 'selected'; ?>>
                        Price: High to Low
                    </option>
                </select>
            </div>

            <!-- Buttons -->
            <button type="submit" class="button" style="width: 100px; margin: 0; padding: 12px 20px;">Apply</button>

            <a href="brand.php?id=<?php echo $brand_id; ?>" class="button button-secondary" style="width: 100px; margin: 0; padding: 12px 20px; text-decoration: none;">
                Reset
            </a>
        </form>
        <!-- Form ends -->
    </div>

    <!-- Alert box -->
    <div class="alert-container"></div>

    <!-- Products -->
    <div class="products-grid">
        <?php if ($products_result->num_rows > 0): ?>
            <?php while ($product = $products_result->fetch_assoc()): ?>

                <!-- Product cards -->
                <div class="product-card">

                    <!-- Wishlist items -->
                    <?php $is_wishlisted = in_array($product['product_id'], $wishlist_product_ids); ?>
                    <button class="wishlist-btn <?php if ($is_wishlisted) echo 'active'; ?>" data-product-id="<?php echo $product['product_id']; ?>">
                        <span class="material-symbols-rounded">favorite</span>
                    </button>

                    <!-- Product status -->
                    <?php if ($product['status']): ?>
                        <div class="product-badge <?php echo strtolower(htmlspecialchars($product['status'])); ?>"><?php echo htmlspecialchars($product['status']); ?></div>
                    <?php endif; ?>

                    <!-- Product name -->
                    <h3 class="product-title"><?php echo htmlspecialchars($product['product_name']); ?></h3>

                    <!-- Product image -->
                    <div class="product-image-container">
                        <a href="product.php?id=<?php echo $product['product_id']; ?>">
                            <img src="../assets/images/products/<?php echo htmlspecialchars($product['image_url']); ?>" alt="<?php echo htmlspecialchars($product['product_name']); ?>">
                        </a>
                    </div>

                    <!-- Product price and view product button -->
                    <div class="product-info-bottom">
                        <div class="product-price">
                            From &#8377;<?php echo number_format($product['starting_price']); ?>
                        </div>
                        <a href="product.php?id=<?php echo $product['product_id']; ?>" class="button buy-button">View</a>
                    </div>

                </div>
            <?php endwhile; ?>
            <!-- Product card ends -->

            <!-- Error if no product found -->
        <?php else: ?>
            <p style="text-align: center; grid-column: 1 / -1; padding: 40px;">
                No products found for this brand matching your criteria.
            </p>
        <?php endif; ?>
    </div>
    <!-- Product grid ends -->

    <!-- Pagination -->
    <div class="pagination">
        <?php
        if ($total_pages > 1):
            // Build query string while excluding 'page'
            $query_params = $_GET;
            unset($query_params['page']);
            $query_string = http_build_query($query_params);

            for ($i = 1; $i <= $total_pages; $i++):
        ?>
                <a href="?<?php echo $query_string . '&page=' . $i; ?>" class="<?php if ($i == $current_page) echo 'active'; ?>">
                    <?php echo $i; ?>
                </a>
        <?php
            endfor;
        endif;
        ?>
    </div>
    <!-- Pagination ends -->
</main>

<?php require_once '../includes/footer.php'; ?>