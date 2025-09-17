<?php
require_once '../includes/config.php';

// Check if product_id is set
if (!isset($_GET['product_id']) || !is_numeric($_GET['product_id'])) {
    header("Location: manage_products.php");
    exit();
}

$product_id = $_GET['product_id'];

// --- Fetch Product Details ---
$stmt = $conn->prepare("SELECT * FROM products WHERE product_id = ?");
$stmt->bind_param("i", $product_id);
$stmt->execute();
$product_result = $stmt->get_result();
if ($product_result->num_rows === 0) {
    // Product not found
    header("Location: manage_products.php?error=productnotfound");
    exit();
}
$product = $product_result->fetch_assoc();
$product['specifications'] = json_decode($product['specifications'], true);
$stmt->close();


// --- Fetch Product Variants ---
$variants_stmt = $conn->prepare("SELECT * FROM product_variants WHERE product_id = ? ORDER BY color, ram_gb, storage_gb");
$variants_stmt->bind_param("i", $product_id);
$variants_stmt->execute();
$variants_result = $variants_stmt->get_result();
$variants_by_color = [];
while ($row = $variants_result->fetch_assoc()) {
    $variants_by_color[$row['color']][] = $row;
}
$variants_stmt->close();


// --- Fetch Brands & Categories for Dropdowns ---
$brands_result = $conn->query("SELECT * FROM brands ORDER BY brand_name");
$categories_result = $conn->query("SELECT * FROM categories ORDER BY category_name");

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Product | Admin</title>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Rounded:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" rel="stylesheet" />
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>
    <link rel="stylesheet" href="../assets/css/main.css">
    <style>
        .multi-select-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(100px, 1fr));
            gap: 10px;
        }

        .multi-select-grid label {
            font-size: 0.85rem;
            display: flex;
            align-items: center;
            gap: 8px;
            background: var(--glass);
            padding: 10px;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .multi-select-grid label:hover {
            background: #fff;
        }

        .multi-select-grid input[type="checkbox"] {
            accent-color: var(--blue);
        }

        .spec-row {
            display: flex;
            gap: 10px;
            margin-bottom: 10px;
            align-items: center;
        }

        .spec-row .form-input {
            margin-bottom: 0;
        }

        .combinations-container .form-input input {
            padding: 12px 15px 12px 45px;
        }

        .combinations-container .form-label {
            white-space: nowrap;
        }

        .remove-btn {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 36px;
            height: 36px;
            padding: 0;
            margin: 0;
            border-radius: 50%;
            background-color: rgba(246, 47, 50, 0.1);
            color: var(--red);
            font-size: 24px;
            font-weight: bold;
            border: 1px solid transparent;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .remove-btn:hover {
            background-color: var(--red);
            color: white;
            box-shadow: var(--red-shadow);
            transform: scale(1.1);
        }
    </style>
</head>

<body>
    <div class="form-container">
        <div class="form-wrapper" style="max-width: 800px;">
            <div class="form-content-column" style="margin: auto;">
                <div class="form-card" style="max-width: 100%;">
                    <div class="dashboard-header-top" style="margin-bottom: 30px;">
                        <div class="dashboard-title-group">
                            <h2 style="font-size: 2rem;">Edit Product</h2>
                            <div class="title-line"></div>
                        </div>
                        <a href="manage_products.php" class="button button-secondary" style="margin: 0; width: auto; text-decoration: none;">Back</a>
                    </div>
                    <form method="POST" action="manage_products.php" class="auth-form" enctype="multipart/form-data">
                        <input type="hidden" name="edit_product" value="1">
                        <input type="hidden" name="product_id" value="<?php echo $product['product_id']; ?>">

                        <div class="form-group">
                            <label class="form-label">Product Name</label>
                            <div class="form-input">
                                <ion-icon name="phone-portrait-outline"></ion-icon>
                                <input type="text" name="product_name" value="<?php echo htmlspecialchars($product['product_name']); ?>" required>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Brand</label>
                            <div class="form-input">
                                <select name="brand_id" required>
                                    <?php mysqli_data_seek($brands_result, 0); ?>
                                    <?php while ($brand = $brands_result->fetch_assoc()): ?>
                                        <option value="<?php echo $brand['brand_id']; ?>" <?php if ($brand['brand_id'] == $product['brand_id']) echo 'selected'; ?>>
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
                                    <?php mysqli_data_seek($categories_result, 0); ?>
                                    <?php while ($category = $categories_result->fetch_assoc()): ?>
                                        <option value="<?php echo $category['category_id']; ?>" <?php if ($category['category_id'] == $product['category_id']) echo 'selected'; ?>>
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
                                    <option value="" <?php if (empty($product['status'])) echo 'selected'; ?>>None</option>
                                    <option value="New" <?php if ($product['status'] == 'New') echo 'selected'; ?>>New</option>
                                    <option value="Trending" <?php if ($product['status'] == 'Trending') echo 'selected'; ?>>Trending</option>
                                    <option value="Hot" <?php if ($product['status'] == 'Hot') echo 'selected'; ?>>Hot</option>
                                </select>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Description</label>
                            <div class="form-input">
                                <ion-icon name="document-text-outline" class="icon-textarea"></ion-icon>
                                <textarea name="description" rows="4" style="width:100%; padding-left:45px;"><?php echo htmlspecialchars($product['description']); ?></textarea>
                            </div>
                        </div>

                        <div class="section-title" style="text-align: left; margin: 30px 0 20px 0;">
                            <h4 style="margin:0;">Specifications</h4>
                            <div class="title-line" style="margin: 10px 0 0 0;"></div>
                        </div>
                        <div id="specifications-container">
                            <?php foreach ($product['specifications'] as $spec_name => $spec_value): ?>
                                <div class="spec-row">
                                    <div class="form-input" style="flex: 1;"><ion-icon name="construct-outline"></ion-icon><input type="text" name="spec_name[]" placeholder="Specification Name" value="<?php echo htmlspecialchars($spec_name); ?>"></div>
                                    <div class="form-input" style="flex: 2;"><ion-icon name="list-outline"></ion-icon><input type="text" name="spec_value[]" placeholder="Value" value="<?php echo htmlspecialchars($spec_value); ?>"></div>
                                    <button type="button" class="remove-btn remove-spec-btn">&times;</button>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        <button type="button" id="add-spec-btn" class="button" style="background: var(--gradient); color: var(--light); margin-top: 10px; width: auto; padding: 10px 15px; font-size: 0.9rem;">Add Specification</button>

                        <hr style="margin: 30px 0; border-color: var(--glass);">

                        <div class="section-title" style="text-align: left; margin-bottom: 20px;">
                            <h4 style="margin:0;">Variants</h4>
                            <div class="title-line" style="margin: 10px 0 0 0;"></div>
                        </div>
                        <div id="variants-container">
                            <?php
                            $variant_key = 0;
                            foreach ($variants_by_color as $color => $combinations):
                            ?>
                                <div class="variant-group" data-id="<?php echo $variant_key; ?>" style="border:1px solid var(--glass); border-radius:15px; padding:20px; margin-bottom:20px; position: relative;">
                                    <button type="button" class="remove-btn remove-variant-btn" style="position: absolute; top: 10px; right: 10px;  max-width: 23px; max-height: 23px;">&times;</button>
                                    <div class="form-group">
                                        <label class="form-label">Color Name</label>
                                        <div class="form-input">
                                            <ion-icon name="color-palette-outline"></ion-icon>
                                            <input type="text" name="variants[<?php echo $variant_key; ?>][color]" value="<?php echo htmlspecialchars($color); ?>" required>
                                        </div>
                                    </div>

                                    <div class="combinations-container" style="margin-top: 20px;">
                                        <h6>Price & Stock for Each Combination:</h6>
                                        <?php
                                        $combo_key = 0;
                                        foreach ($combinations as $combo):
                                        ?>
                                            <div class="form-group" style="display: flex; gap: 10px; align-items: center;">
                                                <label class="form-label" style="flex: 1; margin-bottom: 0;"><?php echo $combo['ram_gb'] ?>GB / <?php echo $combo['storage_gb'] < 1024 ? $combo['storage_gb'] . 'GB' : ($combo['storage_gb'] / 1024) . 'TB'; ?></label>
                                                <input type="hidden" name="variants[<?php echo $variant_key; ?>][combinations][<?php echo $combo_key; ?>][ram]" value="<?php echo $combo['ram_gb']; ?>">
                                                <input type="hidden" name="variants[<?php echo $variant_key; ?>][combinations][<?php echo $combo_key; ?>][storage]" value="<?php echo $combo['storage_gb']; ?>">
                                                <div class="form-input" style="flex: 0.5; margin-bottom: 0;"><ion-icon name="pricetag-outline"></ion-icon><input type="text" name="variants[<?php echo $variant_key; ?>][combinations][<?php echo $combo_key; ?>][price]" placeholder="Price" value="<?php echo $combo['price']; ?>" required></div>
                                                <div class="form-input" style="flex: 0.5; margin-bottom: 0;"><ion-icon name="cube-outline"></ion-icon><input type="number" name="variants[<?php echo $variant_key; ?>][combinations][<?php echo $combo_key; ?>][stock]" placeholder="Stock" value="<?php echo $combo['stock_quantity']; ?>" required></div>
                                            </div>
                                        <?php
                                            $combo_key++;
                                        endforeach;
                                        ?>
                                    </div>
                                </div>
                            <?php
                                $variant_key++;
                            endforeach;
                            ?>
                        </div>
                        <button type="button" id="add-variant-btn" class="button" style="background: var(--gradient); color: var(--light); margin-top: 10px;">Add New Color Group</button>


                        <div class="form-submit" style="margin-top: 20px;">
                            <button type="submit" class="button">Update Product</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // --- SPECIFICATIONS SCRIPT ---
            const specsContainer = document.getElementById('specifications-container');

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

            // --- VARIANTS SCRIPT ---
            const variantsContainer = document.getElementById('variants-container');
            let variantIndex = <?php echo $variant_key; ?>;
            const ramOptions = [6, 8, 12, 16],
                storageOptions = [128, 256, 512, 1024, 2048];
            document.getElementById('add-variant-btn').addEventListener('click', addVariantGroup);

            function addVariantGroup() {
                const variantId = variantIndex++;
                const variantHTML = `
                    <div class="variant-group" data-id="${variantId}" style="border:1px solid var(--glass); border-radius:15px; padding:20px; margin-bottom:20px; position: relative;">
                        <button type="button" class="remove-btn remove-variant-btn" style="position: absolute; top: 10px; right: 10px;  max-width: 23px; max-height: 23px;">&times;</button>
                        <div class="form-group"><label class="form-label">Color Name</label><div class="form-input"><ion-icon name="color-palette-outline"></ion-icon><input type="text" name="variants[${variantId}][color]" required></div></div>
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
        });
    </script>
</body>

</html>