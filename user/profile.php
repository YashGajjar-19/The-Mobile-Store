<?php
session_start();
require_once '../includes/config.php';

// Redirect if not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Get user data
$stmt = $conn->prepare("SELECT fullname, email FROM users WHERE id = ?");
$stmt->bind_param("i", $_SESSION['user_id']);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile | The Mobile Store</title>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Rounded:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" rel="stylesheet" />
    <link rel="stylesheet" href="../assets/css/main.css">
</head>

<body>
    <div class="profile-container">
        <div class="profile-card">
            <div class="profile-header">
                <h2>My Profile</h2>
            </div>

            <div class="profile-info">
                <div class="info-item">
                    <span class="label">Name:</span>
                    <span class="value"><?php echo htmlspecialchars($user['fullname']); ?></span>
                </div>
                <div class="info-item">
                    <span class="label">Email:</span>
                    <span class="value"><?php echo htmlspecialchars($user['email']); ?></span>
                </div>
            </div>

            <div class="profile-actions">
                <a href="edit.php" class="button">Edit Profile</a>
                <a href="password.php" class="button">Change Password</a>
                <a href="../user/logout.php" class="button logout">Logout</a>
            </div>
        </div>
    </div>
</body>

</html>