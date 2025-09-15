<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard | The Mobile Store</title>
    <link
        href="https://fonts.googleapis.com/css2?family=Material+Symbols+Rounded:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200"
        rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css"
        crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="../assets/css/main.css">
</head>

<body>
    <aside class="sidebar">
        <div class="sidebar-logo-container">
            <img class="sidebar-logo-image" src="../assets/images/svg/admin.svg" alt="Admin Profile">
            <span class="sidebar-logo-text">John Doe</span>
        </div>
        <nav class="sidebar-nav">
            <ul class="sidebar-nav-list">
                <li class="sidebar-nav-item">
                    <a href="#" class="sidebar-nav-link active">
                        <span class="material-symbols-rounded">dashboard</span>
                        <span>Dashboard</span>
                    </a>
                </li>
                <li class="sidebar-nav-item">
                    <a href="#" class="sidebar-nav-link">
                        <span class="material-symbols-rounded">shopping_bag</span>
                        <span>Products</span>
                    </a>
                </li>
                <li class="sidebar-nav-item">
                    <a href="#" class="sidebar-nav-link">
                        <span class="material-symbols-rounded">receipt_long</span>
                        <span>Orders</span>
                    </a>
                </li>
                <li class="sidebar-nav-item">
                    <a href="#" class="sidebar-nav-link">
                        <span class="material-symbols-rounded">group</span>
                        <span>Users</span>
                    </a>
                </li>
            </ul>
        </nav>
    </aside>

    <section class="header-container" id="header-section">
        <header class="header admin-header">
            <a href="../index.html" class="logo-container">
                <img class="logo-image" src="../assets/images/Logo.png" alt="The Mobile Store">
                <span class="logo-text">The Mobile Store</span>
            </a>
            <div class="nav-wrapper">
                <nav class="navbar">
                    <a href="#" class="nav-icon">
                        <span class="material-symbols-rounded">search</span>
                    </a>
                    <a href="../index.html" class="nav-icon" title="View Storefront">
                        <span class="material-symbols-rounded">storefront</span>
                    </a>
                    <a href="#" class="nav-icon">
                        <span class="material-symbols-rounded">notifications</span>
                    </a>
                    <a href="#" class="nav-icon">
                        <span class="material-symbols-rounded">account_circle</span>
                    </a>
                </nav>
                <button class="mobile-menu-btn" id="sidebar-toggle-mobile">
                    <span class="material-symbols-rounded">menu</span>
                </button>
            </div>
        </header>
    </section>

    <main class="main-content">
        <div class="dashboard-header-top">
            <div class="dashboard-title-group">
                <h2>Dashboard</h2>
                <div class="title-line"></div>
            </div>
            <a href="add_products.php" class="button">
                <span class="material-symbols-rounded">add_circle</span>
                Add Product
            </a>
        </div>

        <div class="dashboard-cards">
            <div class="summary-card">
                <div class="card-icon" style="background-color: rgba(0, 123, 255, 0.1); color: var(--blue);">
                    <span class="material-symbols-rounded">group</span>
                </div>
                <div class="card-info">
                    <p>Total Users</p>
                    <h3 id="total-users">0</h3>
                </div>
            </div>
            <div class="summary-card">
                <div class="card-icon" style="background-color: rgba(255, 193, 7, 0.1); color: var(--yellow);">
                    <span class="material-symbols-rounded">receipt_long</span>
                </div>
                <div class="card-info">
                    <p>Total Orders</p>
                    <h3 id="total-orders">0</h3>
                </div>
            </div>
        </div>

        <div class="orders-card">
            <div class="orders-header">
                <h2>Current Orders</h2>
            </div>
            <div class="orders-table-container">
                <table>
                    <thead>
                        <tr>
                            <th>Order ID</th>
                            <th>Customer</th>
                            <th>Amount</th>
                            <th>Status</th>
                            <th>Date</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody id="orders-tbody">
                        <tr>
                            <td>#12345</td>
                            <td>John Doe</td>
                            <td>&#8377;1,14,999</td>
                            <td><span class="status pending">Pending</span></td>
                            <td>Aug 06, 2025</td>
                            <td><button class="action-btn">View</button></td>
                        </tr>
                        <tr>
                            <td>#12346</td>
                            <td>Jane Smith</td>
                            <td>&#8377;78,999</td>
                            <td><span class="status approved">Approved</span></td>
                            <td>Aug 05, 2025</td>
                            <td><button class="action-btn">View</button></td>
                        </tr>
                        <tr>
                            <td>#12347</td>
                            <td>Peter Jones</td>
                            <td>&#8377;51,999</td>
                            <td><span class="status delivered">Delivered</span></td>
                            <td>Aug 04, 2025</td>
                            <td><button class="action-btn">View</button></td>
                        </tr>
                        <tr>
                            <td>#12348</td>
                            <td>Mary Jane</td>
                            <td>&#8377;64,999</td>
                            <td><span class="status rejected">Rejected</span></td>
                            <td>Aug 03, 2025</td>
                            <td><button class="action-btn">View</button></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </main>
    <script src="../assets/js/main.js"></script>
</body>

</html>