<?php
session_start();
require_once '../includes/config.php';
require_once '../includes/header.php';

// Security Check
if (!isset($_SESSION['user_id']) || !$_SESSION['is_admin']) {
    header("Location: ../user/login.php?error=unauthorized");
    exit();
}

$message = '';
$message_type = '';

// --- Handle Product Update (POST Request) ---
// --- Handle Product Update (POST Request) ---
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit_product'])) {
    $product_id = $_POST['product_id'];
    $product_name = $_POST['product_name'];

    $conn->begin_transaction();
    try {
        // --- 1. Get Brand Name for Folder Path ---
        $brand_id = $_POST['brand_id'];
        $brand_stmt = $conn->prepare("SELECT brand_name FROM brands WHERE brand_id = ?");
        $brand_stmt->bind_param("i", $brand_id);
        $brand_stmt->execute();
        $brand_name_result = $brand_stmt->get_result()->fetch_assoc();
        $brand_folder_name = $brand_name_result['brand_name'];
        $brand_stmt->close();

        // 2. Update main product details
        $stmt = $conn->prepare("UPDATE products SET product_name = ?, description = ?, brand_id = ?, category_id = ?, status = ? WHERE product_id = ?");
        $stmt->bind_param("ssiisi", $product_name, $_POST['description'], $brand_id, $_POST['category_id'], $_POST['status'], $product_id);
        $stmt->execute();
        $stmt->close();

        // 3. Update Price and Stock for each variant
        if (isset($_POST['variants'])) {
            $update_variant_stmt = $conn->prepare("UPDATE product_variants SET price = ?, stock_quantity = ? WHERE variant_id = ?");
            foreach ($_POST['variants'] as $variant_id => $details) {
                $update_variant_stmt->bind_param("dii", $details['price'], $details['stock'], $variant_id);
                $update_variant_stmt->execute();
            }
            $update_variant_stmt->close();
        }

        // 4. Handle image deletions
        if (!empty($_POST['delete_images'])) {
            $images_to_delete = $_POST['delete_images'];
            $placeholders = implode(',', array_fill(0, count($images_to_delete), '?'));
            $delete_stmt = $conn->prepare("DELETE FROM product_color_images WHERE image_id IN ($placeholders)");
            // Note: You may also want to physically delete the file from the server here
            $delete_stmt->bind_param(str_repeat('i', count($images_to_delete)), ...$images_to_delete);
            $delete_stmt->execute();
            $delete_stmt->close();
        }

        // 5. Handle new image uploads per color
        if (isset($_FILES['new_images']) && !empty($brand_folder_name)) {
            // Use brand name for the folder
            $upload_dir = "../assets/images/products/" . $brand_folder_name . "/";
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }

            foreach ($_FILES['new_images']['name'] as $color => $images) {
                foreach ($images as $i => $name) {
                    if ($_FILES['new_images']['error'][$color][$i] === UPLOAD_ERR_OK) {
                        $tmp_name = $_FILES['new_images']['tmp_name'][$color][$i];

                        $safe_filename = preg_replace("/[^a-zA-Z0-9.\-_()]/", "", basename($name));

                        $counter = 1;
                        $new_filename = $safe_filename;
                        while (file_exists($upload_dir . $new_filename)) {
                            $filename_parts = pathinfo($safe_filename);
                            $new_filename = $filename_parts['filename'] . '(' . $counter . ').' . $filename_parts['extension'];
                            $counter++;
                        }

                        if (move_uploaded_file($tmp_name, $upload_dir . $new_filename)) {
                            // Use brand name in the database URL
                            $image_url = $brand_folder_name . "/" . $new_filename;

                            $thumb_check_stmt = $conn->prepare("SELECT 1 FROM product_color_images WHERE product_id = ? AND color = ? AND is_thumbnail = 1");
                            $thumb_check_stmt->bind_param("is", $product_id, $color);
                            $thumb_check_stmt->execute();
                            $is_thumbnail = $thumb_check_stmt->get_result()->num_rows === 0 ? 1 : 0;
                            $thumb_check_stmt->close(); // Close the statement after use

                            $img_stmt = $conn->prepare("INSERT INTO product_color_images (product_id, color, image_url, is_thumbnail) VALUES (?, ?, ?, ?)");
                            $img_stmt->bind_param("issi", $product_id, $color, $image_url, $is_thumbnail);
                            $img_stmt->execute();
                            $img_stmt->close();
                        }
                    }
                }
            }
        }

        $conn->commit();
        $message = "Product updated successfully!";
        $message_type = 'success';
    } catch (Exception $e) {
        $conn->rollback();
        $message = "An error occurred: " . $e->getMessage();
        $message_type = 'error';
    }
}

// --- Fetch Product Data for Display ---
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: catalog.php");
    exit();
}
$product_id = $_GET['id'];

// Fetch basic product info
$product_stmt = $conn->prepare("SELECT * FROM products WHERE product_id = ?");
$product_stmt->bind_param("i", $product_id);
$product_stmt->execute();
$product_result = $product_stmt->get_result();
if ($product_result->num_rows === 0) {
    header("Location: catalog.php?error=notfound");
    exit();
}
$product = $product_result->fetch_assoc();

// Fetch variants and images, then group them by color
$variants_stmt = $conn->prepare("SELECT * FROM product_variants WHERE product_id = ? ORDER BY color, ram_gb, storage_gb");
$variants_stmt->bind_param("i", $product_id);
$variants_stmt->execute();
$variants_result = $variants_stmt->get_result();

$images_stmt = $conn->prepare("SELECT * FROM product_color_images WHERE product_id = ? ORDER BY is_thumbnail DESC, image_id ASC");
$images_stmt->bind_param("i", $product_id);
$images_stmt->execute();
$images_result = $images_stmt->get_result();

// Group data by color
$data_by_color = [];
while ($image = $images_result->fetch_assoc()) {
    $data_by_color[$image['color']]['images'][] = $image;
}
while ($variant = $variants_result->fetch_assoc()) {
    if (!isset($data_by_color[$variant['color']]['images'])) {
        $data_by_color[$variant['color']]['images'] = []; // Ensure images array exists
    }
    $data_by_color[$variant['color']]['variants'][] = $variant;
}

$brands_result = $conn->query("SELECT * FROM brands ORDER BY brand_name");
$categories_result = $conn->query("SELECT * FROM categories ORDER BY category_name");
?>

<body class="admin-page">

    <div class="form-container">
        <div class="form-wrapper">
            <div class="form-content-column">

                <!-- Header -->
                <div class="dashboard-header-top" style="margin: 20px;">
                    <div class="dashboard-title-group">
                        <h2>Edit Product</h2>
                        <div class="title-line"></div>
                    </div>
                    <a href="catalog.php" class="button">Back to Catalog</a>
                </div>

                <!-- Display Messages -->
                <?php if ($message): ?>
                <div class="alert alert-<?php echo $message_type; ?>" style="max-width: 1100px;">
                    <?php echo $message; ?>
                </div>
                <?php endif; ?>

                <!-- Edit Product Form -->
                <form action="edit-product.php?id=<?php echo $product_id; ?>" method="POST"
                    enctype="multipart/form-data" class="auth-form" style="margin: 0;">
                    <input type="hidden" name="edit_product" value="1">
                    <input type="hidden" name="product_id" value="<?php echo $product['product_id']; ?>">

                    <div class="edit-product-container">
                        <div class="form-card" style="min-width: 100%;">
                            <h3 style="margin-bottom: 15px" ;>Product Information</h3>

                            <hr>

                            <div class=" form-group" style="margin-top: 15px;">
                                <label class=" form-label">
                                    Product Name
                                </label>

                                <div class="form-input">
                                    <ion-icon name="phone-portrait-outline"></ion-icon>
                                    <input type="text" name="product_name"
                                        value="<?php echo htmlspecialchars($product['product_name']); ?>" required>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="form-label">
                                    Description
                                </label>

                                <div class="form-input">
                                    <ion-icon name="document-text-outline" class="icon-textarea"></ion-icon>
                                    <textarea name="description"
                                        rows="8"><?php echo htmlspecialchars($product['description']); ?></textarea>
                                </div>
                            </div>

                            <div class="form-row">

                                <div class="form-group">
                                    <label class="form-label">
                                        Brand
                                    </label>

                                    <div class="form-input">
                                        <select name="brand_id" required>
                                            <?php mysqli_data_seek($brands_result, 0);
                                            while ($brand = $brands_result->fetch_assoc()) {
                                                echo "<option value='{$brand['brand_id']}'" . ($brand['brand_id'] == $product['brand_id'] ? ' selected' : '') . ">{$brand['brand_name']}</option>";
                                            } ?>
                                        </select>
                                    </div>
                                </div>

                                <div class="form-group">

                                    <label class="form-label">
                                        Category
                                    </label>

                                    <div class="form-input">
                                        <select name="category_id" required>
                                            <?php mysqli_data_seek($categories_result, 0);
                                            while ($category = $categories_result->fetch_assoc()) {
                                                echo "<option value='{$category['category_id']}'" . ($category['category_id'] == $product['category_id'] ? ' selected' : '') . ">{$category['category_name']}</option>";
                                            } ?>
                                        </select>
                                    </div>
                                </div>

                                <div class="form-group">

                                    <label class="form-label">
                                        Status
                                    </label>

                                    <div class="form-input">
                                        <select name="status">
                                            <option value="" <?php if (empty($product['status'])) echo 'selected' ; ?>>
                                                None
                                            </option>

                                            <option value="New" <?php if ($product['status']=='New' ) echo 'selected' ;
                                                ?>>
                                                New
                                            </option>

                                            <option value="Hot" <?php if ($product['status']=='Hot' ) echo 'selected' ;
                                                ?>>
                                                Hot
                                            </option>

                                            <option value="Trending" <?php if ($product['status']=='Trending' )
                                                echo 'selected' ; ?>>
                                                Trending
                                            </option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="variants-images-column">
                            <h3>Variants & Images</h3>

                            <hr style="margin: 15px 0 30px 0;">

                            <?php foreach ($data_by_color as $color => $data): ?>
                            <div class="variant-group"
                                style="border:1px solid var(--glass); padding: 20px; margin-bottom: 20px; border-radius: 12px;">

                                <h4 style="margin-bottom: 15px;">
                                    Color:
                                    <?php echo htmlspecialchars($color); ?>
                                </h4>

                                <label class="form-label">Image Gallery</label>

                                <div class="image-gallery">
                                    <?php if (!empty($data['images'])): ?>
                                    <?php foreach ($data['images'] as $image): ?>
                                    <div class="image-thumbnail">
                                        <img src="../assets/images/products/<?php echo htmlspecialchars($image['image_url']); ?>"
                                            alt="Product Image">
                                        <label class="delete-image-label" title="Mark for deletion">
                                            <input type="checkbox" name="delete_images[]"
                                                value="<?php echo $image['image_id']; ?>">
                                            <span class="material-symbols-rounded">delete</span>
                                        </label>
                                    </div>
                                    <?php endforeach; ?>

                                    <?php else: ?>
                                    <p>No images for this color.</p>
                                    <?php endif; ?>
                                </div>

                                <div class="form-group">
                                    <label class="form-label">
                                        Upload New Images for this Color
                                    </label>

                                    <div class="form-input">
                                        <ion-icon name="images-outline"></ion-icon>
                                        <input type="file" name="new_images[<?php echo htmlspecialchars($color); ?>][]"
                                            multiple accept="image/*" style="padding-left: 45px;">
                                    </div>
                                </div>

                                <hr style="margin: 20px 0;">

                                <?php if (!empty($data['variants'])): ?>
                                <?php foreach ($data['variants'] as $variant): ?>
                                <div class="form-row" style="margin-left: 0px; align-items: center;">
                                    <div style="flex: 1; font-weight: 500;">
                                        <?php echo htmlspecialchars($variant['ram_gb']); ?>
                                        GB RAM /
                                        <?php echo htmlspecialchars($variant['storage_gb']); ?>
                                        GB
                                    </div>

                                    <div class="form-group" style="flex: 1;">
                                        <label class="form-label" style="display:none;">Price</label>

                                        <div class="form-input">
                                            <ion-icon name="pricetag-outline"></ion-icon>
                                            <input type="text"
                                                name="variants[<?php echo $variant['variant_id']; ?>][price]"
                                                value="<?php echo htmlspecialchars($variant['price']); ?>" required>
                                        </div>
                                    </div>
                                    <div class="form-group" style="flex: 1;"><label class="form-label"
                                            style="display:none;">Stock</label>
                                        <div class="form-input">
                                            <ion-icon name="cube-outline"></ion-icon>
                                            <input type="number"
                                                name="variants[<?php echo $variant['variant_id']; ?>][stock]"
                                                value="<?php echo htmlspecialchars($variant['stock_quantity']); ?>"
                                                required>
                                        </div>
                                    </div>
                                </div>

                                <?php endforeach; ?>
                                <?php endif; ?>
                            </div>

                            <?php endforeach; ?>
                        </div>

                        <div class="form-submit" style="margin-top: 30px;">
                            <button type="submit" class="button">
                                Save All Changes
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>

</html>