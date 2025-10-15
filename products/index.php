<?php
$page_title = 'Products | The Mobile Store';
require_once '../includes/config.php';
require_once '../includes/header.php';
require_once '../includes/navbar.php';

$user_id = $_SESSION['user_id'] ?? null;
$wishlist_product_ids = [];

// --- EFFICIENT WISHLIST CHECK ---
// Fetch all of the user's wishlist items at once before the loop
if ($user_id) {
    $wishlist_stmt = $conn->prepare("SELECT product_id FROM wishlist WHERE user_id = ?");
    $wishlist_stmt->bind_param("i", $user_id);
    $wishlist_stmt->execute();
    $wishlist_result = $wishlist_stmt->get_result();
    while ($row = $wishlist_result->fetch_assoc()) {
        $wishlist_product_ids[] = $row['product_id'];
    }
    $wishlist_stmt->close();
}

// --- Pagination Configuration ---
$products_per_page = 15;
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
    (SELECT image_url FROM product_color_images pci WHERE pci.product_id = p.product_id AND pci.is_thumbnail = 1 LIMIT 1) as image_url
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

<!-- Alert box -->
<div class="alert-container"></div>

<main class="products-page-section" style="padding-top: 80px;">
    <!-- Header -->
    <div class="section-title">
        <h2>All Products</h2>
        <div class="title-line"></div>
    </div>

    <!-- Filter bar -->
    <div class="filter-options-bar">
        <!-- Filter form -->
        <form action="index.php" method="GET" class="filter-form">
            <!-- Price range -->
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

            <!-- Popularity -->
            <div class="form-group">
                <label class="form-label">Popularity:</label>
                <select name="popularity">
                    <option value="">All</option>

                    <option value="New" <?php if ($popularity == 'New') echo 'selected'; ?>>
                        New
                    </option>

                    <option value="Hot" <?php if ($popularity == 'Hot') echo 'selected'; ?>>
                        Hot
                    </option>

                    <option value="Trending" <?php if ($popularity == 'Trending') echo 'selected'; ?>>
                        Trending
                    </option>
                </select>
            </div>

            <!-- Sort By -->
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
            <button type="submit" class="button" style="width: 100px; margin: 0; padding: 12px 20px;">
                Apply
            </button>
            <a href="index.php" class="button button-secondary" style="width: 100px; margin: 0; padding: 12px 20px; text-decoration: none;">
                Reset
            </a>
        </form>
    </div>

    <!-- Products -->
    <div class="products-grid">
        <?php if ($products_result->num_rows > 0): ?>
            <?php while ($product = $products_result->fetch_assoc()): ?>
                <!-- Product cards -->
                <div class="product-card">
                    <!-- Wishlist -->
                    <?php $is_wishlisted = in_array($product['product_id'], $wishlist_product_ids); ?>
                    <button class="wishlist-btn <?php echo $is_wishlisted ? 'active' : ''; ?>"
                        data-product-id="<?php echo $product['product_id']; ?>"
                        data-in-wishlist="<?php echo $is_wishlisted ? 'true' : 'false'; ?>">
                        <span class="material-symbols-rounded">
                            <?php echo $is_wishlisted ? 'favorite' : 'favorite'; ?>
                        </span>
                    </button>

                    <!-- Product status -->
                    <?php if ($product['status']): ?>
                        <div class="product-badge <?php echo strtolower(htmlspecialchars($product['status'])); ?>"><?php echo htmlspecialchars($product['status']); ?></div>
                    <?php endif; ?>

                    <!-- Product details -->
                    <!-- Product name -->
                    <h3 class="product-title"><?php echo htmlspecialchars($product['product_name']); ?></h3>

                    <!--Product iamge -->
                    <div class="product-image-container">
                        <img src="../assets/images/products/<?php echo htmlspecialchars($product['image_url']); ?>" alt="<?php echo htmlspecialchars($product['product_name']); ?>">
                    </div>

                    <!-- Product price and view button -->
                    <div class="product-info-bottom">
                        <!-- Price -->
                        <div class="product-price">
                            From &#8377;<?php echo number_format($product['starting_price']); ?>
                        </div>
                        <!-- Button -->
                        <a href="../products/product.php?id=<?php echo $product['product_id']; ?>" class="button buy-button">
                            View
                        </a>
                    </div>
                </div>
            <?php endwhile; ?>
            <!-- Card ends -->

            <!-- If no products found -->
        <?php else: ?>
            <p style="text-align: center; grid-column: 1 / -1; padding: 40px;">
                No products match your criteria.
            </p>
        <?php endif; ?>
    </div>
    <!-- Product grid ends -->

    <!-- Pagination -->
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

<?php require_once '../includes/footer.php'; ?>