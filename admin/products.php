<?php
session_start();
require_once '../includes/config.php';
require_once '../includes/header.php';

// Security Check
if (!isset($_SESSION['user_id']) || !$_SESSION['is_admin']) {
    header("Location: ../user/login.php?error=unauthorized");
    exit();
}

// Fetch all products
$products_stmt = $conn->prepare("
    SELECT p.product_name, b.brand_name, SUM(pv.stock_quantity) as total_stock, MIN(pv.price) as starting_price
    FROM products p
    JOIN brands b ON p.brand_id = b.brand_id
    LEFT JOIN product_variants pv ON p.product_id = pv.product_id
    GROUP BY p.product_id
    ORDER BY p.product_name ASC
");
$products_stmt->execute();
$products_result = $products_stmt->get_result();
?>

<body class="admin-page">
    <main class="main-content" style="margin-left: 50px; margin-top: 50px;">
        <div class="dashboard-header-top">
            <div class="dashboard-title-group">
                <h2>All Products</h2>
                <div class="title-line"></div>
            </div>
            <a href="dashboard.php" class="button">Back to Dashboard</a>
        </div>
        <div class="orders-card">
            <div class="orders-table-container">
                <table>
                    <thead>
                        <tr>
                            <th>Product Name</th>
                            <th>Brand</th>
                            <th>Total Stock</th>
                            <th>Starting Price</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($product = $products_result->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($product['product_name']); ?></td>
                                <td><?php echo htmlspecialchars($product['brand_name']); ?></td>
                                <td><?php echo $product['total_stock'] ?? 0; ?></td>
                                <td>&#8377;<?php echo number_format($product['starting_price'] ?? 0); ?></td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>
</body>

</html>