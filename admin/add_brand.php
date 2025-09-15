<?php
require_once '../includes/config.php';

$message = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $brand_name = $_POST['brand_name'];
    $brand_logo_url = $_POST['brand_logo_url']; // This can be a path to the logo file

    $stmt = $conn->prepare("INSERT INTO brands (brand_name, brand_logo_url) VALUES (?, ?)");
    $stmt->bind_param("ss", $brand_name, $brand_logo_url);

    if ($stmt->execute()) {
        $message = "Brand added successfully!";
    } else {
        $message = "Error: " . $stmt->error;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Brand | Admin</title>
    <link rel="stylesheet" href="../assets/css/main.css">
</head>
<body>
    <div class="form-container">
        <div class="form-wrapper">
            <div class="form-content-column">
                <div class="form-card">
                    <div class="form-header">
                        <h2>Add a New Brand</h2>
                    </div>
                    <?php if ($message): ?>
                        <div class="alert alert-info"><?php echo $message; ?></div>
                    <?php endif; ?>
                    <form method="POST" class="auth-form">
                        <div class="form-group">
                            <label for="brand_name" class="form-label">Brand Name</label>
                            <div class="form-input">
                                <input type="text" id="brand_name" name="brand_name" required>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="brand_logo_url" class="form-label">Brand Logo URL</label>
                            <div class="form-input">
                                <input type="text" id="brand_logo_url" name="brand_logo_url">
                            </div>
                        </div>
                        <div class="form-submit">
                            <button type="submit" class="button">Add Brand</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</body>
</html>