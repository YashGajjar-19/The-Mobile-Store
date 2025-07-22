<?php
session_start();
require_once '../includes/config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../user/login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard | The Mobile Store</title>
    <link rel="stylesheet" href="../assets/css/main.css">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Rounded:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" rel="stylesheet" />
</head>

<body class="admin-dashboard">
    <!-- Floating Admin Header -->
    <header class="admin-header">
        <div class="logo-container">
            <img class="logo-image" src="../assets/images/growth.png" alt="The Mobile Store">
            <span class="logo-text">Admin Dashboard</span>
        </div>

        <div class="nav-wrapper">
            <nav class="navbar">
                <button class="notification-btn">
                    <span class="material-symbols-rounded">notifications</span>
                    <span class="notification-badge">3</span>
                </button>
                <a href="../user/logout.php" class="nav-icon">
                    <span class="material-symbols-rounded">logout</span>
                </a>
            </nav>
        </div>
    </header>

    <div class="admin-container">
        <!-- Admin Sidebar -->
        <aside class="admin-sidebar">
            <nav class="sidebar-nav">
                <a href="dashboard.php" class="nav-item active">
                    <span class="material-symbols-rounded">dashboard</span>
                    <span>Dashboard</span>
                </a>
                <a href="products/" class="nav-item">
                    <span class="material-symbols-rounded">phone_iphone</span>
                    <span>Products</span>
                </a>
                <a href="orders/" class="nav-item">
                    <span class="material-symbols-rounded">receipt</span>
                    <span>Orders</span>
                </a>
                <a href="users/" class="nav-item">
                    <span class="material-symbols-rounded">group</span>
                    <span>Users</span>
                </a>
                <a href="settings/" class="nav-item">
                    <span class="material-symbols-rounded">settings</span>
                    <span>Settings</span>
                </a>
            </nav>
        </aside>

        <!-- Main Content -->
        <main class="admin-main">
            <!-- Stats Section -->
            <section class="stats-section">
                <div class="section-title">
                    <h2>Dashboard Overview</h2>
                    <div class="title-line"></div>
                </div>

                <div class="stats-grid">
                    <div class="stat-card">
                        <div class="stat-icon">
                            <span class="material-symbols-rounded">shopping_cart</span>
                        </div>
                        <div class="stat-content">
                            <h3>Today's Orders</h3>
                            <p>24</p>
                            <span class="stat-trend up">+12% from yesterday</span>
                        </div>
                    </div>

                    <div class="stat-card">
                        <div class="stat-icon">
                            <span class="material-symbols-rounded">payments</span>
                        </div>
                        <div class="stat-content">
                            <h3>Revenue</h3>
                            <p>₹1,84,999</p>
                            <span class="stat-trend up">+8% from yesterday</span>
                        </div>
                    </div>

                    <div class="stat-card">
                        <div class="stat-icon">
                            <span class="material-symbols-rounded">group</span>
                        </div>
                        <div class="stat-content">
                            <h3>New Users</h3>
                            <p>12</p>
                            <span class="stat-trend down">-3% from yesterday</span>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Recent Activity Section -->
            <section class="activity-section">
                <div class="section-title">
                    <h2>Recent Activity</h2>
                    <div class="title-line"></div>
                </div>

                <div class="activity-grid">
                    <div class="activity-card">
                        <div class="activity-header">
                            <span class="material-symbols-rounded">shopping_cart</span>
                            <h3>Recent Orders</h3>
                            <a href="orders/" class="view-all">View All</a>
                        </div>
                        <div class="activity-content">
                            <!-- Order items would be dynamically generated here -->
                        </div>
                    </div>

                    <div class="activity-card">
                        <div class="activity-header">
                            <span class="material-symbols-rounded">star</span>
                            <h3>Popular Products</h3>
                            <a href="products/" class="view-all">View All</a>
                        </div>
                        <div class="activity-content">
                            <!-- Product items would be dynamically generated here -->
                        </div>
                    </div>
                </div>
            </section>
        </main>
    </div>
</body>

</html>