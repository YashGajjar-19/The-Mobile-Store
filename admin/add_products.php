<?php
require_once '../includes/config.php';

// --- Fetch Brands & Categories for Dropdowns ---
$brands_result = $conn->query("SELECT * FROM brands ORDER BY brand_name");
$categories_result = $conn->query("SELECT * FROM categories ORDER BY category_name");

$message = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $conn->begin_transaction();
    try {
        // --- 1. Insert the Main Product ---
        $stmt = $conn->prepare("INSERT INTO products (product_name, description, brand_id, category_id) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssii", $_POST['product_name'], $_POST['description'], $_POST['brand_id'], $_POST['category_id']);
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
                    $upload_dir = "../assets/images/products/" . preg_replace('/[^a-zA-Z0-9]/', '-', $_POST['product_name']) . "/";
                    if (!is_dir($upload_dir)) {
                        mkdir($upload_dir, 0777, true);
                    }

                    for ($i = 0; $i < count($image_files['name'][$key]['images']); $i++) {
                        if ($image_files['error'][$key]['images'][$i] === UPLOAD_ERR_OK) {
                            $tmp_name = $image_files['tmp_name'][$key]['images'][$i];
                            $file_extension = pathinfo($image_files['name'][$key]['images'][$i], PATHINFO_EXTENSION);
                            $new_filename = uniqid($color . '_', true) . '.' . $file_extension;

                            if (move_uploaded_file($tmp_name, $upload_dir . $new_filename)) {
                                $image_urls[] = preg_replace('/[^a-zA-Z0-9]/', '-', $_POST['product_name']) . "/" . $new_filename;
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
                            $is_thumbnail = ($index == 0); // First image is thumbnail
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
        $message = "Product and all variants were created successfully!";
    } catch (mysqli_sql_exception $exception) {
        $conn->rollback();
        $message = "Error: " . $exception->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Product | Admin</title>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Rounded:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" rel="stylesheet" />
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

        .combinations-container .form-input input {
            padding: 12px 15px;
        }
    </style>
</head>

<body>
    <div class="form-container">
        <div class="form-wrapper" style="max-width: 800px;">
            <div class="form-content-column" style="margin: auto;">
                <div class="form-card" style="max-width: 100%;">
                    <div class="form-header">
                        <h2>Add New Product</h2>
                    </div>
                    <?php if ($message): ?><div class="alert alert-info"><?php echo $message; ?></div><?php endif; ?>

                    <form method="POST" class="auth-form" enctype="multipart/form-data">
                        <div class="section-title" style="text-align: left; margin-bottom: 20px;">
                            <h4 style="margin:0;">1. Main Product Details</h4>
                            <div class="title-line" style="margin: 10px 0 0 0;"></div>
                        </div>
                        <div class="form-group"><label class="form-label">Product Name</label>
                            <div class="form-input"><input type="text" name="product_name" required></div>
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

                        <hr style="margin: 30px 0; border-color: var(--glass);">

                        <div class="section-title" style="text-align: left; margin-bottom: 20px;">
                            <h4 style="margin:0;">2. Color Variants & Combinations</h4>
                            <div class="title-line" style="margin: 10px 0 0 0;"></div>
                        </div>
                        <div id="variants-container"></div>
                        <button type="button" id="add-variant-btn" class="button" style="background: var(--gradient); color: var(--light); margin-top: 10px;">Add Color Group</button>

                        <div class="form-submit" style="margin-top: 10px;"><button type="submit" class="button">Save Product</button></div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const variantsContainer = document.getElementById('variants-container');
            let variantIndex = 0;
            const ramOptions = [6, 8, 12, 16],
                storageOptions = [128, 256, 512, 1024, 2048];

            document.getElementById('add-variant-btn').addEventListener('click', addVariantGroup);

            function addVariantGroup() {
                const variantId = variantIndex++;
                const variantHTML = `
                    <div class="variant-group" data-id="${variantId}" style="border:1px solid var(--glass); border-radius:15px; padding:20px; margin-bottom:20px; position: relative;">
                        <button type="button" class="remove-variant-btn" style="position: absolute; top: 5px; right: 10px; background: none; border: none; cursor: pointer; font-size: 24px; color: var(--red);">&times;</button>
                        <div class="form-group"><label class="form-label">Color Name</label><div class="form-input"><input type="text" name="variants[${variantId}][color]" required></div></div>
                        <div class="form-group"><label class="form-label">Images for this Color</label><div class="form-input"><input type="file" name="variants[${variantId}][images][]" multiple accept="image/*"></div></div>
                        <div class="form-group"><label class="form-label">Available RAM Options</label><div class="multi-select-grid">${createCheckboxes(variantId, 'ram', ramOptions)}</div></div>
                        <div class="form-group"><label class="form-label">Available Storage Options</label><div class="multi-select-grid">${createCheckboxes(variantId, 'storage', storageOptions)}</div></div>
                        <button type="button" class="button generate-combinations-btn" style="width: auto; padding: 10px 15px; font-size: 0.9rem; margin-top: 15px;">Generate Price & Stock Fields</button>
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
                                <div class="form-input" style="flex: 0.5; margin-bottom: 0;"><input type="text" name="variants[${variantId}][combinations][${comboIndex}][price]" placeholder="Price" required></div>
                                <div class="form-input" style="flex: 0.5; margin-bottom: 0;"><input type="number" name="variants[${variantId}][combinations][${comboIndex}][stock]" placeholder="Stock" required></div>
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