<?php
session_start();
require_once '../includes/config.php';
require_once '../includes/header.php';

// Security Check
if (!isset($_SESSION['user_id']) || !$_SESSION['is_admin']) {
    header("Location: ../user/login.php?error=unauthorized");
    exit();
}

// Fetch all non-admin users
$users_stmt = $conn->prepare("SELECT full_name, email, created_at FROM users WHERE is_admin = 0 ORDER BY created_at DESC");
$users_stmt->execute();
$users_result = $users_stmt->get_result();
?>

<body class="admin-page">
    <main class="main-content" style="margin-left: 50px; margin-top: 50px;">

        <div class="dashboard-header-top">
            <div class="dashboard-title-group">
                <h2>All Users</h2>
                <div class="title-line"></div>
            </div>

            <a href="dashboard.php" class="button">Back to Dashboard</a>

        </div>

        <div class="orders-card">
            <div class="orders-table-container">
                <table>
                    <thead>
                        <tr>
                            <th>Full Name</th>
                            <th>Email</th>
                            <th>Date Registered</th>
                        </tr>
                    </thead>

                    <tbody>
                        <?php while ($user = $users_result->fetch_assoc()): ?>
                            <tr>
                                <td>
                                    <?php echo htmlspecialchars($user['full_name']); ?>
                                </td>

                                <td>
                                    <?php echo htmlspecialchars($user['email']); ?>
                                </td>

                                <td>
                                    <?php echo date('M d, Y', strtotime($user['created_at'])); ?>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>
</body>

</html>