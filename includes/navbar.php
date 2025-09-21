
<body>
    <section class="header-container" id="header-section">
        <header class="header">
            <a href="<?php echo $base_path; ?>index.php" class="logo-container">
                <img class="logo-image" src="<?php echo $base_path; ?>assets/images/Logo.png" alt="The Mobile Store">
                <span class="logo-text">The Mobile Store</span>
            </a>
            <div class="nav-wrapper">
                <nav class="navbar">
                    <form action="<?php echo $base_path; ?>products/search.php" method="GET" class="search-container">
                        <input type="search" name="query" class="search-input" placeholder="Search..." required>
                        <button type="submit" class="nav-icon search-btn" style="border:none; background:transparent; cursor:pointer;" title="Search">
                            <span class="material-symbols-rounded">search</span>
                        </button>
                    </form>
                    <a href="<?php echo $base_path; ?>index.php" class="nav-icon" title="Home">
                        <span class="material-symbols-rounded">home</span>
                    </a>
                    <a href="<?php echo $base_path; ?>products/cart.php" class="nav-icon" title="View Cart">
                        <span class="material-symbols-rounded">shopping_cart</span>
                        <?php if ($cart_count > 0): ?>
                            <span class="cart-badge"><?php echo $cart_count; ?></span>
                        <?php endif; ?>
                    </a>
                    <a href="<?php echo $account_link; ?>" class="nav-icon" title="My Account">
                        <span class="material-symbols-rounded">account_circle</span>
                    </a>
                    <?php if (isset($_SESSION['is_admin']) && $_SESSION['is_admin']): ?>
                        <a href="<?php echo $base_path; ?>admin/dashboard.php" class="nav-icon" title="Admin Dashboard">
                            <span class="material-symbols-rounded">admin_panel_settings</span>
                        </a>
                    <?php endif; ?>
                </nav>

                <button class="mobile-menu-btn">
                    <span class="material-symbols-rounded">
                        menu
                    </span>
                </button>
            </div>

            <div class="mobile-menu">
                <form action="<?php echo $base_path; ?>products/search.php" method="GET" class="mobile-search-container">
                    <input type="search" name="query" class="mobile-search-input" placeholder="Search..." required>
                    <button type="submit" class="mobile-search-btn" title="Search">
                        <span class="material-symbols-rounded">search</span>
                    </button>
                </form>
                <a href="<?php echo $base_path; ?>index.php" class="mobile-nav-icon" title="Home">
                    <span class="material-symbols-rounded">home</span>
                    <span>Home</span>
                </a>
                <a href="<?php echo $base_path; ?>cart.php" class="mobile-nav-icon" title="View Cart">
                    <span class="material-symbols-rounded">shopping_cart</span>
                    <span>Cart</span>
                    <?php if ($cart_count > 0): ?>
                        <span class="cart-badge"><?php echo $cart_count; ?></span>
                    <?php endif; ?>
                </a>
                <a href="<?php echo $account_link; ?>" class="mobile-nav-icon" title="My Account">
                    <span class="material-symbols-rounded">account_circle</span>
                    <span><?php echo $account_text; ?></span>
                </a>
                <?php if (isset($_SESSION['is_admin']) && $_SESSION['is_admin']): ?>
                    <a href="<?php echo $base_path; ?>admin/dashboard.php" class="mobile-nav-icon" title="Admin Dashboard">
                        <span class="material-symbols-rounded">admin_panel_settings</span>
                        <span>Dashboard</span>
                    </a>
                <?php endif; ?>
                <?php if (isset($_SESSION['user_id'])): ?>
                    <a href="<?php echo $base_path; ?>includes/auth.php?action=logout" class="mobile-nav-icon" title="Logout">
                        <span class="material-symbols-rounded">logout</span>
                        <span>Logout</span>
                    </a>
                <?php endif; ?>
            </div>
        </header>
    </section>