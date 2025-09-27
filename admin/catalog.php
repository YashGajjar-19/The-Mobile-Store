<?php
// --- SETUP --- //
// Includes the database configuration and the standard page header.
require_once '../includes/config.php';
require_once '../includes/header.php';

// --- EDIT PRODUCT MODE --- //
// Checks if the page was loaded with an 'edit' action and a product ID.
// This block handles fetching all the data needed to populate the edit form.
if (isset($_GET['action']) && $_GET['action'] === 'edit' && isset($_GET['product_id'])) {
    $product_id = $_GET['product_id'];

    // Security check: ensure the product ID is a number before querying the database.
    if (!is_numeric($product_id)) {
        header("Location: catalog.php");
        exit();
    }

    // --- Fetch Core Product Details ---
    // Prepares and executes a query to get all information for the specified product.
    $stmt = $conn->prepare("SELECT * FROM products WHERE product_id = ?");
    $stmt->bind_param("i", $product_id);
    $stmt->execute();
    $product_result = $stmt->get_result();
    // If no product is found with that ID, redirect back to the main catalog.
    if ($product_result->num_rows === 0) {
        header("Location: catalog.php?error=productnotfound");
        exit();
    }
    $product = $product_result->fetch_assoc();
    // The specifications are stored as a JSON string in the database, so we decode it into a PHP array.
    $product['specifications'] = json_decode($product['specifications'], true);
    $stmt->close();

    // --- Fetch Product Variants ---
    // Gets all the different versions (e.g., colors, storage sizes) of this product.
    $variants_stmt = $conn->prepare("SELECT * FROM product_variants WHERE product_id = ? ORDER BY color, ram_gb, storage_gb");
    $variants_stmt->bind_param("i", $product_id);
    $variants_stmt->execute();
    $variants_result = $variants_stmt->get_result();
    // Organizes the variants into a structured array, grouped by color, for easier display.
    $variants_by_color = [];
    while ($row = $variants_result->fetch_assoc()) {
        $variants_by_color[$row['color']][] = $row;
    }
    $variants_stmt->close();

    // --- Fetch Brands & Categories ---
    // Grabs all available brands and categories to populate the dropdown menus in the edit form.
    $brands_result = $conn->query("SELECT * FROM brands ORDER BY brand_name");
    $categories_result = $conn->query("SELECT * FROM categories ORDER BY category_name");

    // --- Render Edit Product Page ---
    // The script stops here to prevent the default catalog page from loading.
    // The rest of the page would be the HTML form for editing the product.
?>
<?php
    exit(); // Stop the script execution to only show the edit form.
}

// --- DEFAULT CATALOG & MANAGEMENT PAGE --- //
// This section runs if the page is not in 'edit' mode. It displays the forms for adding new data.

// --- Initial Data Fetch ---
// Gets all brands and categories for the "Add New" forms.
$brands_result = $conn->query("SELECT * FROM brands ORDER BY brand_name");
$categories_result = $conn->query("SELECT * FROM categories ORDER BY category_name");

// Initialize variables to hold success or error messages for the user.
$message = '';
$message_type = ''; // Will be 'success' or 'error' for styling.
$active_tab = 'product'; // Sets which tab is shown by default.

// --- FORM SUBMISSION HANDLING --- //

// --- Handle "Add Product" Form ---
// This is a complex operation, so it's wrapped in a database transaction.
// A transaction ensures that if any part fails, all changes are rolled back, preventing partial data.
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_product'])) {
    $active_tab = 'product';
    $conn->begin_transaction(); // Start transaction.
    try {
        // Step 1: Get the brand name to use for creating a folder for product images.
        $brand_id = $_POST['brand_id'];
        $brand_stmt = $conn->prepare("SELECT brand_name FROM brands WHERE brand_id = ?");
        $brand_stmt->bind_param("i", $brand_id);
        $brand_stmt->execute();
        $brand_name_result = $brand_stmt->get_result()->fetch_assoc();
        $brand_folder_name = $brand_name_result['brand_name'];
        $brand_stmt->close();

        // Step 2: Prepare and insert the main product data.
        $product_name = $_POST['product_name'];
        $description = $_POST['description'];
        $category_id = $_POST['category_id'];
        $status = $_POST['status'];

        // Collate all specifications from the form into a single array.
        $specifications = [];
        if (isset($_POST['spec_name']) && isset($_POST['spec_value'])) {
            for ($i = 0; $i < count($_POST['spec_name']); $i++) {
                if (!empty($_POST['spec_name'][$i]) && !empty($_POST['spec_value'][$i])) {
                    $specifications[$_POST['spec_name'][$i]] = $_POST['spec_value'][$i];
                }
            }
        }
        // Convert the specifications array into a JSON string for database storage.
        $specifications_json = json_encode($specifications);

        // Insert the main product record.
        $stmt = $conn->prepare("INSERT INTO products (product_name, description, specifications, brand_id, category_id, status) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("sssiss", $product_name, $description, $specifications_json, $brand_id, $category_id, $status);
        $stmt->execute();
        $product_id = $stmt->insert_id; // Get the ID of the newly created product.
        $stmt->close();

        // Step 3: Loop through each variant group (e.g., each color) submitted from the form.
        if (isset($_POST['variants'])) {
            foreach ($_POST['variants'] as $key => $variant_group) {
                $color = $variant_group['color'];
                $is_first_image_for_color = true; // Used to mark the first uploaded image as the thumbnail.

                // Step 4: Handle the file uploads for this color.
                if (isset($_FILES['variants']['name'][$key]['images'])) {
                    $image_files = $_FILES['variants'];
                    // Create a directory for the brand if it doesn't exist.
                    $upload_dir = "../assets/images/products/" . $brand_folder_name . "/";
                    if (!is_dir($upload_dir)) {
                        mkdir($upload_dir, 0777, true);
                    }

                    // Loop through each uploaded image for this variant.
                    for ($i = 0; $i < count($image_files['name'][$key]['images']); $i++) {
                        if ($image_files['error'][$key]['images'][$i] === UPLOAD_ERR_OK) {
                            $tmp_name = $image_files['tmp_name'][$key]['images'][$i];
                            // Create a unique filename to prevent overwriting existing files.
                            $file_extension = pathinfo($image_files['name'][$key]['images'][$i], PATHINFO_EXTENSION);
                            $new_filename = uniqid($color . '_', true) . '.' . $file_extension;

                            // Move the uploaded file to its final destination.
                            if (move_uploaded_file($tmp_name, $upload_dir . $new_filename)) {
                                // Save the image path to the database.
                                $image_url = $brand_folder_name . "/" . $new_filename;
                                $is_thumbnail = $is_first_image_for_color ? 1 : 0; // 1 for true, 0 for false.

                                $img_stmt = $conn->prepare("INSERT INTO product_color_images (product_id, color, image_url, is_thumbnail) VALUES (?, ?, ?, ?)");
                                $img_stmt->bind_param("issi", $product_id, $color, $image_url, $is_thumbnail);
                                $img_stmt->execute();
                                $img_stmt->close();
                                $is_first_image_for_color = false; // Ensure only the first image is the thumbnail.
                            }
                        }
                    }
                }

                // Step 5: Insert the specific combinations (e.g., 8GB RAM/256GB Storage) for this color.
                if (isset($variant_group['combinations'])) {
                    foreach ($variant_group['combinations'] as $combo) {
                        $variant_stmt = $conn->prepare("INSERT INTO product_variants (product_id, color, storage_gb, ram_gb, price, stock_quantity) VALUES (?, ?, ?, ?, ?, ?)");
                        $variant_stmt->bind_param("isiiid", $product_id, $color, $combo['storage'], $combo['ram'], $combo['price'], $combo['stock']);
                        $variant_stmt->execute();
                        $variant_stmt->close();
                    }
                }
            }
        }

        $conn->commit(); // If everything was successful, commit the changes to the database.
        $message = "Product and all variations were added successfully!";
        $message_type = 'success';
    } catch (mysqli_sql_exception $exception) {
        $conn->rollback(); // If any error occurred, undo all database changes from this block.
        $message = "An error occurred: " . $exception->getMessage();
        $message_type = 'error';
    }
}

// --- Handle "Edit Product" Form Submission ---
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit_product'])) {
    $active_tab = 'edit_product';
    $product_id = $_POST['product_id'];

    $conn->begin_transaction(); // Use a transaction for the update as well.
    try {
        // Update the main product details first.
        $product_name = $_POST['product_name'];
        $description = $_POST['description'];
        $brand_id = $_POST['brand_id'];
        $category_id = $_POST['category_id'];
        $status = $_POST['status'];

        // Re-assemble the specifications JSON object.
        $specifications = [];
        if (isset($_POST['spec_name']) && isset($_POST['spec_value'])) {
            for ($i = 0; $i < count($_POST['spec_name']); $i++) {
                if (!empty($_POST['spec_name'][$i]) && !empty($_POST['spec_value'][$i])) {
                    $specifications[$_POST['spec_name'][$i]] = $_POST['spec_value'][$i];
                }
            }
        }
        $specifications_json = json_encode($specifications);

        // Execute the UPDATE query.
        $stmt = $conn->prepare("UPDATE products SET product_name = ?, description = ?, specifications = ?, brand_id = ?, category_id = ?, status = ? WHERE product_id = ?");
        $stmt->bind_param("sssissi", $product_name, $description, $specifications_json, $brand_id, $category_id, $status, $product_id);
        $stmt->execute();
        $stmt->close();

        // For variants, the simplest approach is to delete all existing ones and re-insert the submitted ones.
        // This avoids complex logic for tracking which ones were added, removed, or changed.
        if (isset($_POST['variants'])) {
            // First, delete all old variants for this product.
            $delete_stmt = $conn->prepare("DELETE FROM product_variants WHERE product_id = ?");
            $delete_stmt->bind_param("i", $product_id);
            $delete_stmt->execute();
            $delete_stmt->close();

            // Now, re-insert the variants from the form, just like in the "Add Product" logic.
            foreach ($_POST['variants'] as $key => $variant_group) {
                $color = $variant_group['color'];

                if (isset($variant_group['combinations'])) {
                    foreach ($variant_group['combinations'] as $combo) {
                        $variant_stmt = $conn->prepare("INSERT INTO product_variants (product_id, color, storage_gb, ram_gb, price, stock_quantity) VALUES (?, ?, ?, ?, ?, ?)");
                        $variant_stmt->bind_param("isiiid", $product_id, $color, $combo['storage'], $combo['ram'], $combo['price'], $combo['stock']);
                        $variant_stmt->execute();
                        $variant_stmt->close();
                    }
                }
            }
        }

        $conn->commit(); // Finalize the transaction.
        $message = "Product updated successfully!";
        $message_type = 'success';
    } catch (mysqli_sql_exception $exception) {
        $conn->rollback(); // Rollback on error.
        $message = "An error occurred during the update: " . $exception->getMessage();
        $message_type = 'error';
    }
}

// --- Data for the Main Catalog View ---
// Fetches a list of all products to be displayed on the page for easy selection for editing.
$all_products_result = $conn->query("SELECT p.product_id, p.product_name, b.brand_name FROM products p JOIN brands b ON p.brand_id = b.brand_id ORDER BY p.product_name");
?>

<body>
    <div class="form-container">
        <div class="form-wrapper" style="max-width: 1300px;">

            <div class="form-image-column" style="max-width: 30%;">
                <img src="../assets/images/svg/edit_product.svg" alt="Add Product" style="max-width: 80%;">
            </div>

            <div class="form-content-column" style="margin: auto;">
                <div class="form-card" style="max-width: 100%;">
                    <div class="dashboard-header-top" style="margin-bottom: 30px;">
                        <div class="dashboard-title-group">
                            <h2 style="font-size: 2rem;">Manage Products</h2>
                            <div class="title-line"></div>
                        </div>
                        <a href="dashboard.php" class="button button-secondary" style="margin: 0; width: auto; text-decoration: none;">Back to Dashboard</a>
                    </div>

                    <div class="tabs">
                        <button class="tab-link active" onclick="openTab(event, 'products')">Add Product</button>
                        <button class="tab-link" onclick="openTab(event, 'edit_product')">Edit Products</button>
                    </div>

                    <div id="products" class="tab-content active">
                        <div class=" form-header">
                            <h2>Add New Product</h2>
                        </div>
                        <?php if ($message && $active_tab === 'product'): ?>
                            <div class="alert alert-<?php echo $message_type; ?>">
                                <?php echo $message; ?>
                            </div>
                        <?php endif; ?>

                        <form method="POST" class="auth-form" enctype="multipart/form-data">
                            <input type="hidden" name="add_product" value="1">

                            <div class="section-title" style="text-align: left; margin-bottom: 20px;">
                                <h4 style="margin:0;">
                                    1. Main Product Details
                                </h4>
                                <div class="title-line" style="margin: 10px 0 0 0;"></div>
                            </div>

                            <div class="form-group">
                                <label class="form-label">Product Name</label>
                                <div class="form-input">
                                    <ion-icon name="phone-portrait-outline"></ion-icon>
                                    <input type="text" name="product_name" required>
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="form-group">
                                    <label class="form-label">Brand</label>
                                    <div class="form-input">
                                        <select name="brand_id" required>
                                            <option value="">Select a Brand</option>
                                            <?php mysqli_data_seek($brands_result, 0);
                                            while ($brand = $brands_result->fetch_assoc()): ?>
                                                <option value="<?php echo $brand['brand_id']; ?>">
                                                    <?php echo htmlspecialchars($brand['brand_name']); ?>
                                                </option>
                                            <?php endwhile; ?>
                                        </select>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label class="form-label">Category</label>
                                    <div class="form-input">
                                        <select name="category_id" required>
                                            <option value="">Select a Category</option>
                                            <?php mysqli_data_seek($categories_result, 0);
                                            while ($category = $categories_result->fetch_assoc()): ?>
                                                <option value="<?php echo $category['category_id']; ?>">
                                                    <?php echo htmlspecialchars($category['category_name']); ?>
                                                </option>
                                            <?php endwhile; ?>
                                        </select>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label class="form-label">Product Status</label>
                                    <div class="form-input">
                                        <select name="status">
                                            <option value="">None</option>
                                            <option value="New">New</option>
                                            <option value="Trending">Trending</option>
                                            <option value="Hot">Hot</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="form-label">Description</label>
                                <div class="form-input">
                                    <ion-icon name="document-text-outline" class="icon-textarea"></ion-icon>
                                    <textarea name="description" rows="4" style="width:100%; padding-left:45px;"></textarea>
                                </div>
                            </div>

                            <div class="section-title" style="text-align: left; margin: 30px 0 20px 0;">
                                <h4 style="margin:0;">2. Product Specifications</h4>
                                <div class="title-line" style="margin: 10px 0 0 0;"></div>
                            </div>

                            <div id="specifications-container"></div>
                            <button type="button" id="add-spec-btn" class="button" style="background: var(--gradient); color: var(--light); margin-top: 10px; width: auto; padding: 10px 15px; font-size: 0.9rem;">
                                Add Custom Specification
                            </button>

                            <hr style="margin: 30px 0; border-color: var(--glass);">

                            <div class="section-title" style="text-align: left; margin-bottom: 20px;">
                                <h4 style="margin:0;">3. Color Variants & Combinations</h4>
                                <div class="title-line" style="margin: 10px 0 0 0;"></div>
                            </div>

                            <div id="variants-container"></div>

                            <div class="form-row">
                                <button type="button" id="add-variant-btn" class="button" style="background: var(--gradient); color: var(--light); width: 50%;">
                                    Add Color Group
                                </button>

                                <div class="form-submit" style="width: 50%; margin: 0;">
                                    <button type="submit" class="button">Save Product</button>
                                </div>
                            </div>
                        </form>
                    </div>

                    <div id="edit_product" class="tab-content">
                        <div class="form-header">
                            <h2>Edit Product</h2>
                        </div>
                        <?php if ($message && $active_tab === 'edit_product'): ?>
                            <div class="alert alert-<?php echo $message_type; ?>">
                                <?php echo $message; ?>
                            </div>
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
                                                <a href="edit-product.php?id=<?php echo $product['product_id']; ?>" class="button buy-button">
                                                    Edit
                                                </a>
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
        // TABS SCRIPT

        /**
         * Manages the tabbed interface.
         * Hides all tab content and then shows only the one that was clicked.
         * @param {Event} evt - The click event.
         * @param {string} tabName - The ID of the tab content to display.
         */
        function openTab(evt, tabName) {
            // First, hide all the tab content sections.
            var i, tabcontent, tablinks;
            tabcontent = document.getElementsByClassName("tab-content");
            for (i = 0; i < tabcontent.length; i++) {
                tabcontent[i].style.display = "none";
            }

            // Then, remove the "active" look from all tab buttons.
            tablinks = document.getElementsByClassName("tab-link");
            for (i = 0; i < tablinks.length; i++) {
                tablinks[i].className = tablinks[i].className.replace(" active", "");
            }

            // Finally, show the content of the clicked tab and give the button the "active" look.
            document.getElementById(tabName).style.display = "block";
            if (evt) evt.currentTarget.className += " active";
        }

        // Wait for the page to fully load before running any scripts.
        document.addEventListener('DOMContentLoaded', function() {
            // Automatically open the correct tab when the page loads, based on a value from PHP.
            const activeTabName = "<?php echo $active_tab; ?>";
            const tabButtonToActivate = document.querySelector(`.tab-link[onclick*="'${activeTabName}'"]`);
            if (tabButtonToActivate) {
                tabButtonToActivate.click(); // Click the tab from the PHP variable.
            } else {
                document.querySelector('.tab-link').click(); // Or, just click the first tab by default.
            }


            // --- SPECIFICATIONS SCRIPT --- //

            const specsContainer = document.getElementById('specifications-container');
            // A list of common specs to add to the form by default.
            const predefinedSpecs = ["Processor", "Main Camera", "Selfie Camera", "Video Camera", "Display", "Battery", "Operating System", "Connectivity"];

            /**
             * Creates and adds a new row of input fields for a specification.
             * @param {string} name - The default name for the spec (e.g., "Processor").
             * @param {string} value - The default value for the spec.
             */
            function addSpecField(name = "", value = "") {
                const specHTML = `
                <div class="spec-row">
                    <div class="form-input" style="flex: 1;">
                        <ion-icon name="construct-outline"></ion-icon>
                        <input type="text" name="spec_name[]" placeholder="Specification Name" value="${name}">
                    </div>
                    
                    <div class="form-input" style="flex: 2;">
                        <ion-icon name="list-outline"></ion-icon>
                        <input type="text" name="spec_value[]" placeholder="Value" value="${value}">
                    </div>
                    
                    <button type="button" class="remove-btn remove-spec-btn">&times;</button>
                </div>`;
                specsContainer.insertAdjacentHTML('beforeend', specHTML);
            }

            // When the "Add Specification" button is clicked, add a new empty spec row.
            document.getElementById('add-spec-btn').addEventListener('click', () => addSpecField());

            // Listen for clicks inside the specs container to handle the "remove" button.
            specsContainer.addEventListener('click', e => {
                if (e.target.classList.contains('remove-spec-btn')) {
                    // If a remove button was clicked, find its parent row and delete it.
                    e.target.closest('.spec-row').remove();
                }
            });

            // Add the predefined specification fields to the page when it first loads.
            predefinedSpecs.forEach(specName => addSpecField(specName));


            // --- VARIANTS SCRIPT --- //
            // This section handles the complex logic for adding product variants (color, RAM, storage, etc.).

            const variantsContainer = document.getElementById('variants-container');
            let variantIndex = 0; // A counter to give each new variant a unique ID.
            // Predefined options for RAM and Storage selection.
            const ramOptions = [6, 8, 12, 16],
                storageOptions = [128, 256, 512, 1024, 2048]; // 1024 = 1TB, 2048 = 2TB

            // When the "Add Variant" button is clicked, run the function to add a new group.
            document.getElementById('add-variant-btn').addEventListener('click', addVariantGroup);

            /**
             * Creates a whole new section for a product variant (like a new color).
             * This includes fields for color name, images, and RAM/storage options.
             */
            function addVariantGroup() {
                const variantId = variantIndex++;
                const variantHTML = `
                <div class="variant-group" data-id="${variantId}" style="border:1px solid var(--glass); border-radius:15px; padding:20px; margin-bottom:20px; position: relative;">
                    <button type="button" class="remove-btn remove-variant-btn" style="position: absolute; top: 10px; right: 10px;  max-width: 23px; max-height: 23px;">&times;</button>
                    
                    <div class="form-group">
                    <label class="form-label">Color Name</label>
                        <div class="form-input">
                            <ion-icon name="color-palette-outline"></ion-icon>
                            <input type="text" name="variants[${variantId}][color]" required>
                        </div>
                    </div>
                    
                    <div class="form-group">
                    <label class="form-label">Images for this Color</label>
                        <div class="form-input">
                            <ion-icon name="images-outline"></ion-icon><input type="file" name="variants[${variantId}][images][]" multiple accept="image/*" style="padding-left: 45px;">
                        </div>
                    </div>
                    
                    <div class="form-group">
                    <label class="form-label">Available RAM Options</label>
                        <div class="multi-select-grid">${createCheckboxes(variantId, 'ram', ramOptions)}</div>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label" style="margin-top: 12px;">Available Storage Options</label>
                    <div class="multi-select-grid">${createCheckboxes(variantId, 'storage', storageOptions)}</div>
                    
                    </div>
                    <button type="button" class="button generate-combinations-btn" style="width: auto; padding: 10px 15px; font-size: 0.9rem; margin-top: 25px;">Generate Price & Stock Fields</button>
                    
                    <div class="combinations-container" style="margin-top: 20px;"></div>
                </div>`;
                variantsContainer.insertAdjacentHTML('beforeend', variantHTML);
            }

            /**
             * A helper function to create a grid of checkboxes.
             * @param {number} id - The unique ID of the variant group.
             * @param {string} name - The type of option, either "ram" or "storage".
             * @param {Array} options - The array of values (e.g., [6, 8, 12]).
             * @returns {string} The HTML for the checkboxes.
             */
            function createCheckboxes(id, name, options) {
                // Converts numbers to user-friendly labels (e.g., 1024 becomes "1 TB").
                return options.map(val => `<label><input type="checkbox" data-name="${name}" value="${val}"> ${name === 'storage' && val >= 1024 ? val/1024 + ' TB' : val + ' GB'}</label>`).join('');
            }

            // Listen for clicks inside the main variants container.
            variantsContainer.addEventListener('click', function(e) {
                // If the "Generate" button was clicked...
                if (e.target.classList.contains('generate-combinations-btn')) {
                    generateCombinationFields(e.target.closest('.variant-group'));
                }
                // If the "Remove Variant" button was clicked...
                if (e.target.classList.contains('remove-variant-btn')) {
                    e.target.closest('.variant-group').remove();
                }
            });

            /**
             * Generates input fields for price and stock for every possible
             * combination of the selected RAM and Storage options.
             * @param {HTMLElement} group - The variant group container.
             */
            function generateCombinationFields(group) {
                const container = group.querySelector('.combinations-container');
                const variantId = group.dataset.id;
                const selectedRam = Array.from(group.querySelectorAll('input[data-name="ram"]:checked')).map(cb => cb.value);
                const selectedStorage = Array.from(group.querySelectorAll('input[data-name="storage"]:checked')).map(cb => cb.value);

                // Show an error if the user hasn't selected at least one of each option.
                if (selectedRam.length === 0 || selectedStorage.length === 0) {
                    container.innerHTML = '<p style="color: var(--red);">Please select at least one RAM and one Storage option before generating fields.</p>';
                    return;
                }

                // Clear any previous fields and create new ones.
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
                            <div class="form-input" style="flex: 0.5; margin-bottom: 0;">
                                <ion-icon name="pricetag-outline"></ion-icon>
                                <input type="text" name="variants[${variantId}][combinations][${comboIndex}][price]" placeholder="Price" required>
                            </div>
                            <div class="form-input" style="flex: 0.5; margin-bottom: 0;">
                                <ion-icon name="cube-outline"></ion-icon>
                                <input type="number" name="variants[${variantId}][combinations][${comboIndex}][stock]" placeholder="Stock" required>
                            </div>
                        </div>`;
                        container.insertAdjacentHTML('beforeend', comboHTML);
                        comboIndex++;
                    });
                });
            }

            // Automatically add one empty variant group when the page loads.
            addVariantGroup();
        });
    </script>
</body>

</html>