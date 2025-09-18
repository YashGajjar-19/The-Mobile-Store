<?php
require_once '../includes/config.php';

// --- Initial Fetch for Form Dropdowns ---
$brands_result = $conn->query("SELECT * FROM brands ORDER BY brand_name");
$categories_result = $conn->query("SELECT * FROM categories ORDER BY category_name");
$payment_methods_result = $conn->query("SELECT * FROM payment_methods ORDER BY method_name");


$message = '';
$message_type = ''; // To hold 'success' or 'error'
$active_tab = 'product'; // Default tab

// --- Handle Add Brand Form Submission ---
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_brand'])) {
    $active_tab = 'brand';
    $brand_name = $_POST['brand_name'];
    $brand_logo_url = $_POST['brand_logo_url'];

    // Check for duplicates first
    $check_stmt = $conn->prepare("SELECT brand_id FROM brands WHERE brand_name = ?");
    $check_stmt->bind_param("s", $brand_name);
    $check_stmt->execute();
    $check_result = $check_stmt->get_result();

    if ($check_result->num_rows > 0) {
        $message = "Error: A brand with this name already exists.";
        $message_type = 'error';
    } else {
        $stmt = $conn->prepare("INSERT INTO brands (brand_name, brand_logo_url) VALUES (?, ?)");
        $stmt->bind_param("ss", $brand_name, $brand_logo_url);
        if ($stmt->execute()) {
            $message = "Brand added successfully!";
            $message_type = 'success';
            // Refresh brand list after successful insertion
            $brands_result = $conn->query("SELECT * FROM brands ORDER BY brand_name");
        } else {
            $message = "Error: " . $stmt->error;
            $message_type = 'error';
        }
        $stmt->close();
    }
    $check_stmt->close();
}

// --- Handle Add Category Form Submission ---
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_category'])) {
    $active_tab = 'category';
    $category_name = $_POST['category_name'];
    $description = $_POST['description'];

    // Check for duplicates
    $check_stmt = $conn->prepare("SELECT category_id FROM categories WHERE category_name = ?");
    $check_stmt->bind_param("s", $category_name);
    $check_stmt->execute();
    $check_result = $check_stmt->get_result();

    if ($check_result->num_rows > 0) {
        $message = "Error: A category with this name already exists.";
        $message_type = 'error';
    } else {
        $stmt = $conn->prepare("INSERT INTO categories (category_name, description) VALUES (?, ?)");
        $stmt->bind_param("ss", $category_name, $description);
        if ($stmt->execute()) {
            $message = "Category added successfully!";
            $message_type = 'success';
            // Refresh category list
            $categories_result = $conn->query("SELECT * FROM categories ORDER BY category_name");
        } else {
            $message = "Error: " . $stmt->error;
            $message_type = 'error';
        }
        $stmt->close();
    }
    $check_stmt->close();
}

// --- Handle Payment Method Submission ---
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['add_payment_method'])) {
        $active_tab = 'payment';
        $method_name = $_POST['method_name'];
        $check_stmt = $conn->prepare("SELECT payment_method_id FROM payment_methods WHERE method_name = ?");
        $check_stmt->bind_param("s", $method_name);
        $check_stmt->execute();
        if ($check_stmt->get_result()->num_rows > 0) {
            $message = "Error: This payment method already exists.";
            $message_type = 'error';
        } else {
            $stmt = $conn->prepare("INSERT INTO payment_methods (method_name) VALUES (?)");
            $stmt->bind_param("s", $method_name);
            if ($stmt->execute()) {
                $message = "Payment method added successfully!";
                $message_type = 'success';
                $payment_methods_result = $conn->query("SELECT * FROM payment_methods ORDER BY method_name");
            } else {
                $message = "Error: " . $stmt->error;
                $message_type = 'error';
            }
        }
    }
    // Handle Delete Payment Method
    elseif (isset($_POST['delete_payment_method'])) {
        $active_tab = 'payment';
        $method_id = $_POST['payment_method_id'];
        $stmt = $conn->prepare("DELETE FROM payment_methods WHERE payment_method_id = ?");
        $stmt->bind_param("i", $method_id);
        if ($stmt->execute()) {
            $message = "Payment method deleted successfully!";
            $message_type = 'success';
            $payment_methods_result = $conn->query("SELECT * FROM payment_methods ORDER BY method_name");
        } else {
            $message = "Error: Could not delete method. It might be in use.";
            $message_type = 'error';
        }
    }
}

// --- Handle Add Product Form Submission ---
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_product'])) {
    $active_tab = 'product';
    $conn->begin_transaction();
    try {
        // --- 1. Insert the Main Product with Specifications ---
        $product_name = $_POST['product_name'];
        $description = $_POST['description'];
        $brand_id = $_POST['brand_id'];
        $category_id = $_POST['category_id'];
        $status = $_POST['status'];

        // --- Handle Specifications ---
        $specifications = [];
        if (isset($_POST['spec_name']) && isset($_POST['spec_value'])) {
            for ($i = 0; $i < count($_POST['spec_name']); $i++) {
                if (!empty($_POST['spec_name'][$i]) && !empty($_POST['spec_value'][$i])) {
                    $specifications[$_POST['spec_name'][$i]] = $_POST['spec_value'][$i];
                }
            }
        }
        $specifications_json = json_encode($specifications);

        $stmt = $conn->prepare("INSERT INTO products (product_name, description, specifications, brand_id, category_id, status) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("sssiss", $product_name, $description, $specifications_json, $brand_id, $category_id, $status);
        $stmt->execute();
        $product_id = $stmt->insert_id;
        $stmt->close();

        // --- 2. Process Each Color Variant Group ---
        if (isset($_POST['variants'])) {
            foreach ($_POST['variants'] as $key => $variant_group) {
                $color = $variant_group['color'];

                // --- 3. Handle Image Uploads for this Color ---
                $image_urls = [];
                if (isset($_FILES['variants']['name'][$key]['images'])) {
                    $image_files = $_FILES['variants'];
                    $upload_dir = "../assets/images/products/" . preg_replace('/[^a-zA-Z0-9]/', '-', $product_name) . "/";
                    if (!is_dir($upload_dir)) {
                        mkdir($upload_dir, 0777, true);
                    }

                    for ($i = 0; $i < count($image_files['name'][$key]['images']); $i++) {
                        if ($image_files['error'][$key]['images'][$i] === UPLOAD_ERR_OK) {
                            $tmp_name = $image_files['tmp_name'][$key]['images'][$i];
                            $file_extension = pathinfo($image_files['name'][$key]['images'][$i], PATHINFO_EXTENSION);
                            $new_filename = uniqid($color . '_', true) . '.' . $file_extension;

                            if (move_uploaded_file($tmp_name, $upload_dir . $new_filename)) {
                                $image_urls[] = preg_replace('/[^a-zA-Z0-9]/', '-', $product_name) . "/" . $new_filename;
                            }
                        }
                    }
                }

                // --- 4. Insert Each Specific Combination (RAM/Storage/Price) ---
                if (isset($variant_group['combinations'])) {
                    foreach ($variant_group['combinations'] as $combo) {
                        $variant_stmt = $conn->prepare("INSERT INTO product_variants (product_id, color, storage_gb, ram_gb, price, stock_quantity) VALUES (?, ?, ?, ?, ?, ?)");
                        $variant_stmt->bind_param("isiiid", $product_id, $color, $combo['storage'], $combo['ram'], $combo['price'], $combo['stock']);
                        $variant_stmt->execute();
                        $variant_id = $variant_stmt->insert_id;
                        $variant_stmt->close();

                        // --- 5. Link Uploaded Images to this Variant ---
                        foreach ($image_urls as $index => $url) {
                            $is_thumbnail = ($index == 0); // Mark the first image as the thumbnail
                            $img_stmt = $conn->prepare("INSERT INTO product_images (variant_id, image_url, is_thumbnail) VALUES (?, ?, ?)");
                            $img_stmt->bind_param("isi", $variant_id, $url, $is_thumbnail);
                            $img_stmt->execute();
                            $img_stmt->close();
                        }
                    }
                }
            }
        }

        $conn->commit();
        $message = "Product and all variations were added successfully!";
        $message_type = 'success';
    } catch (mysqli_sql_exception $exception) {
        $conn->rollback();
        $message = "An error occurred: " . $exception->getMessage();
        $message_type = 'error';
    }
}

// --- Handle Edit Product Form Submission ---
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit_product'])) {
    $active_tab = 'edit_product';
    $product_id = $_POST['product_id'];

    // Update product details
    $stmt = $conn->prepare("UPDATE products SET product_name = ?, description = ?, brand_id = ?, category_id = ?, status = ? WHERE product_id = ?");
    $stmt->bind_param("ssiisi", $_POST['product_name'], $_POST['description'], $_POST['brand_id'], $_POST['category_id'], $_POST['status'], $product_id);
    $stmt->execute();
    $stmt->close();

    $message = "Product updated successfully!";
    $message_type = 'success';
}


// --- Fetch All Products for Editing ---
$all_products_result = $conn->query("SELECT p.product_id, p.product_name, b.brand_name FROM products p JOIN brands b ON p.brand_id = b.brand_id ORDER BY p.product_name");
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Products | Admin</title>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Rounded:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" rel="stylesheet" />
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>
    <link rel="stylesheet" href="../assets/css/main.css">
</head>

<body>
    <div class="form-container">
        <div class="form-wrapper" style="max-width: 1300px;">
            <div class="form-image-column" style="max-width: 24%;">
                <img src="../assets/images/svg/add_products.svg" alt="Add Product" style="max-width: 80%;">
            </div>
            <div class="form-content-column" style="margin: auto;">
                <div class="form-card" style="max-width: 100%;">

                    <div class="dashboard-header-top" style="margin-bottom: 30px;">
                        <div class="dashboard-title-group">
                            <h2 style="font-size: 2rem;">Manage Content</h2>
                            <div class="title-line"></div>
                        </div>
                        <a href="dashboard.php" class="button button-secondary" style="margin: 0; width: auto; text-decoration: none;">Back to Dashboard</a>
                    </div>

                    <div class="tabs">
                        <button class="tab-link active" onclick="openTab(event, 'products')">Products</button>
                        <button class="tab-link" onclick="openTab(event, 'brands')">Brands</button>
                        <button class="tab-link" onclick="openTab(event, 'categories')">Categories</button>
                        <button class="tab-link" onclick="openTab(event, 'edit_product')">Edit Products</button>
                        <button class="tab-link" onclick="openTab(event, 'payment')">Payment Methods</button>
                    </div>

                    <div id="products" class="tab-content active">
                        <div class=" form-header">
                            <h2>Add New Product</h2>
                        </div>
                        <?php if ($message && $active_tab === 'product'): ?><div class="alert alert-<?php echo $message_type; ?>"><?php echo $message; ?></div><?php endif; ?>
                        <form method="POST" class="auth-form" enctype="multipart/form-data">
                            <input type="hidden" name="add_product" value="1">
                            <div class="section-title" style="text-align: left; margin-bottom: 20px;">
                                <h4 style="margin:0;">1. Main Product Details</h4>
                                <div class="title-line" style="margin: 10px 0 0 0;"></div>
                            </div>
                            <div class="form-group"><label class="form-label">Product Name</label>
                                <div class="form-input"><ion-icon name="phone-portrait-outline"></ion-icon><input type="text" name="product_name" required></div>
                            </div>
                            <div class="form-group"><label class="form-label">Brand</label>
                                <div class="form-input"><select name="brand_id" required>
                                        <option value="">Select a Brand</option><?php mysqli_data_seek($brands_result, 0);
                                                                                while ($brand = $brands_result->fetch_assoc()): ?><option value="<?php echo $brand['brand_id']; ?>"><?php echo htmlspecialchars($brand['brand_name']); ?></option><?php endwhile; ?>
                                    </select></div>
                            </div>
                            <div class="form-group"><label class="form-label">Category</label>
                                <div class="form-input"><select name="category_id" required>
                                        <option value="">Select a Category</option><?php mysqli_data_seek($categories_result, 0);
                                                                                    while ($category = $categories_result->fetch_assoc()): ?><option value="<?php echo $category['category_id']; ?>"><?php echo htmlspecialchars($category['category_name']); ?></option><?php endwhile; ?>
                                    </select></div>
                            </div>
                            <div class="form-group"><label class="form-label">Product Status</label>
                                <div class="form-input"><select name="status">
                                        <option value="">None</option>
                                        <option value="New">New</option>
                                        <option value="Trending">Trending</option>
                                        <option value="Hot">Hot</option>
                                    </select></div>
                            </div>
                            <div class="form-group"><label class="form-label">Description</label>
                                <div class="form-input"><ion-icon name="document-text-outline" class="icon-textarea"></ion-icon><textarea name="description" rows="4" style="width:100%; padding-left:45px;"></textarea></div>
                            </div>
                            <div class="section-title" style="text-align: left; margin: 30px 0 20px 0;">
                                <h4 style="margin:0;">2. Product Specifications</h4>
                                <div class="title-line" style="margin: 10px 0 0 0;"></div>
                            </div>
                            <div id="specifications-container"></div>
                            <button type="button" id="add-spec-btn" class="button" style="background: var(--gradient); color: var(--light); margin-top: 10px; width: auto; padding: 10px 15px; font-size: 0.9rem;">Add Custom Specification</button>
                            <hr style="margin: 30px 0; border-color: var(--glass);">
                            <div class="section-title" style="text-align: left; margin-bottom: 20px;">
                                <h4 style="margin:0;">3. Color Variants & Combinations</h4>
                                <div class="title-line" style="margin: 10px 0 0 0;"></div>
                            </div>
                            <div id="variants-container"></div>
                            <button type="button" id="add-variant-btn" class="button" style="background: var(--gradient); color: var(--light); margin-top: 10px;">Add Color Group</button>
                            <div class="form-submit" style="margin-top: 10px;"><button type="submit" class="button">Save Product</button></div>
                        </form>
                    </div>

                    <div id="brands" class="tab-content">
                        <div class="form-header">
                            <h2>Add a New Brand</h2>
                        </div>
                        <?php if ($message && $active_tab === 'brand'): ?>
                            <div class="alert alert-<?php echo $message_type; ?>"><?php echo $message; ?></div>
                        <?php endif; ?>
                        <form method="POST" class="auth-form">
                            <input type="hidden" name="add_brand" value="1">
                            <div class="form-group">
                                <label for="brand_name" class="form-label">Brand Name</label>
                                <div class="form-input">
                                    <ion-icon name="phone-portrait-outline"></ion-icon>
                                    <input type="text" id="brand_name" name="brand_name" required>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="brand_logo_url" class="form-label">Brand Logo URL</label>
                                <div class="form-input">
                                    <ion-icon name="globe-outline"></ion-icon>
                                    <input type="text" id="brand_logo_url" name="brand_logo_url" placeholder="e.g., Apple.webp">
                                </div>
                            </div>
                            <div class="form-submit">
                                <button type="submit" class="button" style="margin-bottom: 30px;">Add Brand</button>
                            </div>
                        </form>
                    </div>

                    <div id="categories" class="tab-content">
                        <div class="form-header">
                            <h2>Add a New Category</h2>
                        </div>
                        <?php if ($message && $active_tab === 'category'): ?>
                            <div class="alert alert-<?php echo $message_type; ?>"><?php echo $message; ?></div>
                        <?php endif; ?>
                        <form method="POST" class="auth-form">
                            <input type="hidden" name="add_category" value="1">
                            <div class="form-group">
                                <label for="category_name" class="form-label">Category Name</label>
                                <div class="form-input">
                                    <input type="text" id="category_name" name="category_name" required>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="description" class="form-label">Description</label>
                                <div class="form-input">
                                    <textarea id="description" name="description" rows="4"></textarea>
                                </div>
                            </div>
                            <div class="form-submit">
                                <button type="submit" class="button" style="margin-bottom: 30px;">Add Category</button>
                            </div>
                        </form>
                    </div>

                    <div id="edit_product" class="tab-content">
                        <div class="form-header">
                            <h2>Edit Product</h2>
                        </div>
                        <?php if ($message && $active_tab === 'edit_product'): ?>
                            <div class="alert alert-<?php echo $message_type; ?>"><?php echo $message; ?></div>
                        <?php endif; ?>

                        <div class="orders-table-container">
                            <table>
                                <thead>
                                    <tr>
                                        <th>Product Name</th>
                                        <th>Brand</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php while ($product = $all_products_result->fetch_assoc()): ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($product['product_name']); ?></td>
                                            <td><?php echo htmlspecialchars($product['brand_name']); ?></td>
                                            <td>
                                                <a href="edit_product.php?product_id=<?php echo $product['product_id']; ?>" class="button buy-button">Edit</a>
                                            </td>
                                        </tr>
                                    <?php endwhile; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div id="payment" class="tab-content">
                        <div class="form-header">
                            <h2>Manage Payment Methods</h2>
                        </div>
                        <?php if ($message && $active_tab === 'payment'): ?><div class="alert alert-<?php echo $message_type; ?>"><?php echo $message; ?></div><?php endif; ?>
                        <form method="POST" class="auth-form">
                            <input type="hidden" name="add_payment_method" value="1">
                            <div class="form-group"><label for="method_name" class="form-label">New Method Name</label>
                                <div class="form-input"><ion-icon name="card-outline"></ion-icon><input type="text" id="method_name" name="method_name" placeholder="e.g., Cash on Delivery" required></div>
                            </div>
                            <div class="form-submit"><button type="submit" class="button">Add Method</button></div>
                        </form>
                        <hr style="margin: 30px 0;">
                        <h4>Existing Methods</h4>
                        <div class="orders-table-container">
                            <table>
                                <thead>
                                    <tr>
                                        <th>Method Name</th>
                                        <th style="text-align: right;">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php mysqli_data_seek($payment_methods_result, 0); ?>
                                    <?php while ($method = $payment_methods_result->fetch_assoc()): ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($method['method_name']); ?></td>
                                            <td style="text-align: right;">
                                                <form method="POST" onsubmit="return confirm('Are you sure you want to delete this payment method?');" style="display: inline;">
                                                    <input type="hidden" name="delete_payment_method" value="1">
                                                    <input type="hidden" name="payment_method_id" value="<?php echo $method['payment_method_id']; ?>">
                                                    <button type="submit" class="remove-btn" title="Delete Method">&times;</button>
                                                </form>
                                            </td>
                                        </tr>
                                    <?php endwhile; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>

    <script>
        function openTab(evt, tabName) {
            var i, tabcontent, tablinks;
            tabcontent = document.getElementsByClassName("tab-content");
            for (i = 0; i < tabcontent.length; i++) {
                tabcontent[i].style.display = "none";
            }
            tablinks = document.getElementsByClassName("tab-link");
            for (i = 0; i < tablinks.length; i++) {
                tablinks[i].className = tablinks[i].className.replace(" active", "");
            }
            document.getElementById(tabName).style.display = "block";
            if (evt) evt.currentTarget.className += " active";
        }

        document.addEventListener('DOMContentLoaded', function() {
            const activeTabName = "<?php echo $active_tab; ?>";
            const tabButtonToActivate = document.querySelector(`.tab-link[onclick*="'${activeTabName}'"]`);
            if (tabButtonToActivate) {
                tabButtonToActivate.click();
            } else {
                document.querySelector('.tab-link').click();
            }

            // --- SPECIFICATIONS SCRIPT ---
            const specsContainer = document.getElementById('specifications-container');
            const predefinedSpecs = ["Processor", "Main Camera", "Selfie Camera", "Video Camera", "Display", "Battery", "Operating System", "Connectivity"];

            function addSpecField(name = "", value = "") {
                const specHTML = `
                    <div class="spec-row">
                        <div class="form-input" style="flex: 1;"><ion-icon name="construct-outline"></ion-icon><input type="text" name="spec_name[]" placeholder="Specification Name" value="${name}"></div>
                        <div class="form-input" style="flex: 2;"><ion-icon name="list-outline"></ion-icon><input type="text" name="spec_value[]" placeholder="Value" value="${value}"></div>
                        <button type="button" class="remove-btn remove-spec-btn">&times;</button>
                    </div>`;
                specsContainer.insertAdjacentHTML('beforeend', specHTML);
            }

            document.getElementById('add-spec-btn').addEventListener('click', () => addSpecField());
            specsContainer.addEventListener('click', e => {
                if (e.target.classList.contains('remove-spec-btn')) {
                    e.target.closest('.spec-row').remove();
                }
            });

            predefinedSpecs.forEach(specName => addSpecField(specName));

            // --- VARIANTS SCRIPT ---
            const variantsContainer = document.getElementById('variants-container');
            let variantIndex = 0;
            const ramOptions = [6, 8, 12, 16],
                storageOptions = [128, 256, 512, 1024, 2048];
            document.getElementById('add-variant-btn').addEventListener('click', addVariantGroup);

            function addVariantGroup() {
                const variantId = variantIndex++;
                const variantHTML = `
                    <div class="variant-group" data-id="${variantId}" style="border:1px solid var(--glass); border-radius:15px; padding:20px; margin-bottom:20px; position: relative;">
                        <button type="button" class="remove-btn remove-variant-btn" style="position: absolute; top: 10px; right: 10px;  max-width: 23px; max-height: 23px;">&times;</button>
                        <div class="form-group"><label class="form-label">Color Name</label><div class="form-input"><ion-icon name="color-palette-outline"></ion-icon><input type="text" name="variants[${variantId}][color]" required></div></div>
                        <div class="form-group"><label class="form-label">Images for this Color</label><div class="form-input"><ion-icon name="images-outline"></ion-icon><input type="file" name="variants[${variantId}][images][]" multiple accept="image/*" style="padding-left: 45px;"></div></div>
                        <div class="form-group"><label class="form-label">Available RAM Options</label><div class="multi-select-grid">${createCheckboxes(variantId, 'ram', ramOptions)}</div></div>
                        <div class="form-group"><label class="form-label" style="margin-top: 12px;">Available Storage Options</label><div class="multi-select-grid">${createCheckboxes(variantId, 'storage', storageOptions)}</div></div>
                        <button type="button" class="button generate-combinations-btn" style="width: auto; padding: 10px 15px; font-size: 0.9rem; margin-top: 25px;">Generate Price & Stock Fields</button>
                        <div class="combinations-container" style="margin-top: 20px;"></div>
                    </div>`;
                variantsContainer.insertAdjacentHTML('beforeend', variantHTML);
            }

            function createCheckboxes(id, name, options) {
                return options.map(val => `<label><input type="checkbox" data-name="${name}" value="${val}"> ${name === 'storage' && val >= 1024 ? val/1024 + ' TB' : val + ' GB'}</label>`).join('');
            }

            variantsContainer.addEventListener('click', function(e) {
                if (e.target.classList.contains('generate-combinations-btn')) {
                    generateCombinationFields(e.target.closest('.variant-group'));
                }
                if (e.target.classList.contains('remove-variant-btn')) {
                    e.target.closest('.variant-group').remove();
                }
            });

            function generateCombinationFields(group) {
                const container = group.querySelector('.combinations-container');
                const variantId = group.dataset.id;
                const selectedRam = Array.from(group.querySelectorAll('input[data-name="ram"]:checked')).map(cb => cb.value);
                const selectedStorage = Array.from(group.querySelectorAll('input[data-name="storage"]:checked')).map(cb => cb.value);

                if (selectedRam.length === 0 || selectedStorage.length === 0) {
                    container.innerHTML = '<p style="color: var(--red);">Please select at least one RAM and one Storage option before generating fields.</p>';
                    return;
                }

                container.innerHTML = '<h6>Price & Stock for Each Combination:</h6>';
                let comboIndex = 0;
                selectedRam.forEach(ram => {
                    selectedStorage.forEach(storage => {
                        const storageLabel = storage < 1024 ? storage + 'GB' : storage / 1024 + 'TB';
                        const comboHTML = `
                            <div class="form-group" style="display: flex; gap: 10px; align-items: center;">
                                <label class="form-label" style="flex: 1; margin-bottom: 0;">${ram}GB / ${storageLabel}</label>
                                <input type="hidden" name="variants[${variantId}][combinations][${comboIndex}][ram]" value="${ram}">
                                <input type="hidden" name="variants[${variantId}][combinations][${comboIndex}][storage]" value="${storage}">
                                <div class="form-input" style="flex: 0.5; margin-bottom: 0;"><ion-icon name="pricetag-outline"></ion-icon><input type="text" name="variants[${variantId}][combinations][${comboIndex}][price]" placeholder="Price" required></div>
                                <div class="form-input" style="flex: 0.5; margin-bottom: 0;"><ion-icon name="cube-outline"></ion-icon><input type="number" name="variants[${variantId}][combinations][${comboIndex}][stock]" placeholder="Stock" required></div>
                            </div>`;
                        container.insertAdjacentHTML('beforeend', comboHTML);
                        comboIndex++;
                    });
                });
            }
            addVariantGroup();
        });
    </script>
</body>

</html>