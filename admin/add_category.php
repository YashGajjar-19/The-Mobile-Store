<?php
require_once '../includes/config.php';

$message = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $category_name = $_POST['category_name'];
    $description = $_POST['description'];

    $stmt = $conn->prepare("INSERT INTO categories (category_name, description) VALUES (?, ?)");
    $stmt->bind_param("ss", $category_name, $description);

    if ($stmt->execute()) {
        $message = "Category added successfully!";
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
    <title>Add Category | Admin</title>
    <link rel="stylesheet" href="../assets/css/main.css">
</head>
<body>
    <div class="form-container">
        <div class="form-wrapper">
            <div class="form-content-column">
                <div class="form-card">
                    <div class="form-header">
                        <h2>Add a New Category</h2>
                    </div>
                    <?php if ($message): ?>
                        <div class="alert alert-info"><?php echo $message; ?></div>
                    <?php endif; ?>
                    <form method="POST" class="auth-form">
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
                            <button type="submit" class="button">Add Category</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</body>
</html>