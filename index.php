<?php
session_start();
if (!isset($_SESSION["user_id"])) {
    // User is not logged in, redirect to login page
    header("Location: ./user/login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home | The Mobile Store</title>

    <!-- Icons -->
    <link
        href="https://fonts.googleapis.com/css2?family=Material+Symbols+Rounded:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200"
        rel="stylesheet" />

    <!-- Style Sheets -->
    <link rel="stylesheet" href="./assets/css/main.css">
</head>

<body>
    <header class="header">
        <a href="./index.html" class="logo-container">
            <img class="logo-image" src="./assets/images/growth.png" alt="The Mobile Store">
            <span class="logo-text">The Mobile Store</span>
        </a>

        <div class="nav-wrapper">

            <nav class="navbar">
                <a class="nav-icon">
                    <span class="material-symbols-rounded">search</span>
                </a>
                <a href="./index.php" class="nav-icon">
                    <span class="material-symbols-rounded">home</span>
                </a>
                <a href="./cart/index.php" class="nav-icon">
                    <span class="material-symbols-rounded">shopping_cart</span>
                    <span class="cart-badge">3</span>
                </a>
                <?php if (isset($_SESSION['user_id'])): ?>
                    <!-- Logged in state - show profile link -->
                    <a href="./user/profile.php" class="nav-icon">
                        <span class="material-symbols-rounded">person</span>
                    </a>
                <?php else: ?>
                    <!-- Logged out state - show login link -->
                    <a href="./user/login.php" class="nav-icon">
                        <span class="material-symbols-rounded">login</span>
                    </a>
                <?php endif; ?>
            </nav>

            <button class="mobile-menu-btn">
                <span class="material-symbols-rounded">
                    menu
                </span>
            </button>
        </div>

        <!-- Mobile Menu -->
        <div class="mobile-menu">
            <a href="./index.php" class="mobile-nav-icon">
                <span class="material-symbols-rounded">home</span>
                <span>Home</span>
            </a>
            <a href="./cart/index.php" class="mobile-nav-icon">
                <span class="material-symbols-rounded">shopping_cart</span>
                <span>Cart</span>
                <span class="cart-badge">3</span>
            </a>
            <?php if (isset($_SESSION['user_id'])): ?>
                <a href="./user/profile.php" class="mobile-nav-icon">
                    <span class="material-symbols-rounded">person</span>
                    <span>Profile</span>
                </a>
            <?php else: ?>
                <a href="./user/login.php" class="mobile-nav-icon">
                    <span class="material-symbols-rounded">login</span>
                    <span>Login</span>
                </a>
            <?php endif; ?>
        </div>

    </header>

    <!-- Featured Products -->
    <section class="feature-section">
        <!-- Title Part -->
        <div class="feature-title">
            <h2>
                Featured Products
            </h2>
            <div class="title-line"></div>
        </div>

        <!-- Feature Slider -->
        <div class="slider-wrapper">
            <div class="slider-container">
                <!-- Slide 1 -->
                <!-- Image Section -->
                <div class="slide">
                    <div class="slide-image">
                        <div class="slide-status new">New</div>
                        <img src="./assets/images/products/Samsung/Z Fold 7/Blue Shadow/1.webp" alt="Galaxy Z Fold7">
                    </div>
                    <!-- Detail Section -->
                    <div class="slide-details">

                        <div class="slide-name">Galaxy Z Fold7</div>
                        <div class="slide-price">From &#8377;1,74,999</div>
                        <button class="button-small">Buy Now</button>
                    </div>
                </div>

                <!-- Slide 2 -->
                <!-- Image Section -->
                <div class="slide">
                    <div class="slide-image">
                        <div class="slide-status new">New</div>
                        <img src="./assets/images/products/Samsung/Z Flip 7/Blue Shadow/1.webp" alt="Galaxy Z Flip7">
                    </div>
                    <!-- Detail Section -->
                    <div class="slide-details">
                        <div class="slide-name">Galaxy Z Flip7</div>
                        <div class="slide-price">From &#8377;1,14,999</div>
                        <button class="button-small">Buy Now</button>
                    </div>
                </div>

                <!-- Slide 3 -->
                <!-- Image Section -->
                <div class="slide">
                    <div class="slide-image">
                        <div class="slide-status trending">Trending</div>
                        <img src="./assets/images/products/Google/Pixel 9A/Porcelain/1.webp" alt="Pixel 9A">
                    </div>
                    <!-- Detail Section -->
                    <div class="slide-details">
                        <div class="slide-name">Pixel 9A</div>
                        <div class="slide-price">From &#8377;64,999</div>
                        <button class="button-small">Buy Now</button>
                    </div>
                </div>

                <!-- Slide 4 -->
                <!-- Image Section -->
                <div class="slide">
                    <div class="slide-image">
                        <div class="slide-status trending">Trending</div>
                        <img src="./assets/images/products/Samsung/S25 Ultra/Titanium WhiteSilver/1.webp" alt="Galaxy S25 Ultra">
                    </div>
                    <!-- Detail Section -->
                    <div class="slide-details">
                        <div class="slide-name">Galaxy S25 Ultra</div>
                        <div class="slide-price">From &#8377;1,17,999</div>
                        <button class="button-small">Buy Now</button>
                    </div>
                </div>

                <!-- Slide 5 -->
                <!-- Image Section -->
                <div class="slide">
                    <div class="slide-image">
                        <img src="./assets/images/products/Oneplus/OnePlus 13S/Green Silk/1.webp" alt="Oneplus 13S">
                    </div>
                    <!-- Detail Section -->
                    <div class="slide-details">
                        <div class="slide-name">Oneplus 13S</div>
                        <div class="slide-price">From &#8377;51,999</div>
                        <button class="button-small">Buy Now</button>
                    </div>
                </div>

                <!-- Navigation Dots -->
                <div class="dots-container">
                    <div class="dot active" data-slide="0"></div>
                    <div class="dot" data-slide="1"></div>
                    <div class="dot" data-slide="2"></div>
                    <div class="dot" data-slide="3"></div>
                    <div class="dot" data-slide="4"></div>
                </div>
            </div>
        </div>
    </section>

    <!-- Shop By Brand Section -->
    <section class="shop-by-brand">
        <!-- Title Part-->
        <div class="section-title">
            <h2>
                Shop By Brand
            </h2>
            <div class="title-line"></div>
        </div>

        <!-- Brand Logos Grid -->
        <div class="brands-grid">
            <!-- Brand 1 -->
            <div class="brand-card">
                <img src="./assets/images/brands-logo/Samsung.svg" alt="Samsung">
            </div>

            <!-- Brand 2 -->
            <div class="brand-card">
                <img src="./assets/images/brands-logo/Apple.svg" alt="Apple">
            </div>

            <!-- Brand 3 -->
            <div class="brand-card">
                <img src="./assets/images/brands-logo/Google.svg" alt="Google" class="">
            </div>

            <!-- Brand 4 -->
            <div class="brand-card">
                <img src="./assets/images/brands-logo/Oneplus.svg" alt="OnePlus">
            </div>

            <!-- Brand 5 -->
            <div class="brand-card">
                <img src="./assets/images/brands-logo/Xiomi.svg" alt="Xiaomi" style="width: 55%;">
            </div>

            <!-- Brand 6 -->
            <div class="brand-card">
                <img src="./assets/images/brands-logo/Oppo.svg" alt="Oppo">
            </div>

            <!-- Brand 7 -->
            <div class="brand-card">
                <img src="./assets/images/brands-logo/Vivo.svg" alt="Vivo">
            </div>

            <!-- Brand 8 -->
            <div class="brand-card">
                <img src="./assets/images/brands-logo/Iqoo.svg" alt="">
            </div>

            <!-- Brand 9 -->
            <div class="brand-card">
                <img src="./assets/images/brands-logo/Nothing.svg" alt="Nothing">
            </div>

            <!-- Brand 10 -->
            <div class="brand-card">
                <img src="./assets/images/brands-logo/Motorola.svg" alt="Motorola">
            </div>
        </div>
    </section>

    <!-- Products Section -->
    <section class="products-section">
        <!-- Title Part-->
        <div class="section-title">
            <h2>
                Latest Products
            </h2>
            <div class="title-line"></div>
        </div>

        <div class="products-grid">
            <!-- Product Card 1 -->
            <div class="product-card">
                <div class="product-image-container">
                    <div class="product-badge">New</div>
                    <button class="wishlist-btn">
                        <span class="material-symbols-rounded">favorite</span>
                    </button>
                    <img src="./assets/images/products/Apple/iPhone 16 Pro/Desert Titanium/1.webp" alt="Iphone 16 Pro">
                </div>

                <div class="product-details">
                    <h3 class="product-title">Iphone 16 Pro</h3>
                    <div class="product-price">From &#8377;1,74,999</div>
                    <div class="product-actions">
                        <button class="add-button">Add to Cart</button>
                    </div>
                </div>
            </div>

            <!-- Product Card 2 -->
            <div class="product-card">
                <div class="product-image-container">
                    <div class="product-badge">Trending</div>
                    <button class="wishlist-btn">
                        <span class="material-symbols-rounded">favorite</span>
                    </button>
                    <img src="./assets/images/products/Google/Pixel 9 Pro/Rose Quartz/1.webp" alt="Pixel 9 Pro">
                </div>

                <div class="product-details">
                    <h3 class="product-title">Pixel 9 Pro</h3>
                    <div class="product-price">From &#8377;64,999</div>
                    <div class="product-actions">
                        <button class="add-button">Add to Cart</button>
                    </div>
                </div>
            </div>

            <!-- Product Card 3 -->
            <div class="product-card">
                <div class="product-image-container">
                    <button class="wishlist-btn">
                        <span class="material-symbols-rounded">favorite</span>
                    </button>
                    <img src="./assets/images/products/Samsung/S25 Ultra/Titanium Black/1.webp" alt="Galaxy S25 Ultra" style="width: 250px; height:250px;">
                </div>

                <div class="product-details">
                    <h3 class="product-title">Galaxy S25 Ultra</h3>
                    <div class="product-price">From &#3377;1,17,999</div>
                    <div class="product-actions">
                        <button class="add-button">Add to Cart</button>
                    </div>
                </div>
            </div>

            <!-- Product Card 4 -->
            <div class="product-card">
                <div class="product-image-container">
                    <div class="product-badge">New</div>
                    <button class="wishlist-btn">
                        <span class="material-symbols-rounded">favorite</span>
                    </button>
                    <img src="./assets/images/products/Vivo/X Fold 3 Pro/Celestial Black/2.webp" alt="Vivo X Fold3">
                </div>

                <div class="product-details">
                    <h3 class="product-title">Vivo X Fold3</h3>
                    <div class="product-price">From &#8377;1,49,999</div>
                    <div class="product-actions">
                        <button class="add-button">Add to Cart</button>
                    </div>
                </div>
            </div>

            <!-- Product Card 5 -->
            <div class="product-card">
                <div class="product-image-container">
                    <div class="product-badge">Trending</div>
                    <button class="wishlist-btn">
                        <span class="material-symbols-rounded">favorite</span>
                    </button>
                    <img src="./assets/images/products/Oppo/Find N3 Flip/Sleek Black/1.webp" alt="Oppo Find N3 Flip">
                </div>

                <div class="product-details">
                    <h3 class="product-title">Find N3 Flip</h3>
                    <div class="product-price">From &#8377;64,999</div>
                    <div class="product-actions">
                        <button class="add-button">Add to Cart</button>
                    </div>
                </div>
            </div>

            <!-- Product Card 6 -->
            <div class="product-card">
                <div class="product-image-container">
                    <button class="wishlist-btn">
                        <span class="material-symbols-rounded">favorite</span>
                    </button>
                    <img src="./assets/images/products/Oneplus/OnePlus 13/Arctic Dawn/1.webp" alt="Oneplus 13">
                </div>

                <div class="product-details">
                    <h3 class="product-title">Oneplus 13</h3>
                    <div class="product-price">From &#8377;51,999</div>
                    <div class="product-actions">
                        <button class="add-button">Add to Cart</button>
                    </div>
                </div>
            </div>

            <!-- Product Card 7 -->
            <div class="product-card">
                <div class="product-image-container">
                    <div class="product-badge">New</div>
                    <button class="wishlist-btn">
                        <span class="material-symbols-rounded">favorite</span>
                    </button>
                    <img src="./assets/images/products/Nothing/Phone 3A Pro/Black/1.webp" alt="Nothing 3A Pro">
                </div>

                <div class="product-details">
                    <h3 class="product-title">Nothing 3A Pro</h3>
                    <div class="product-price">From &#8377;34,999</div>
                    <div class="product-actions">
                        <button class="add-button">Add to Cart</button>
                    </div>
                </div>
            </div>

            <!-- Product Card 8 -->
            <div class="product-card">
                <div class="product-image-container">
                    <div class="product-badge">Trending</div>
                    <button class="wishlist-btn">
                        <span class="material-symbols-rounded">favorite</span>
                    </button>
                    <img src="./assets/images/products/Xiomi/Xiomi 15/White/1.webp" alt="Pixel 9A" style="width: 210px; height: 210px;">
                </div>

                <div class="product-details">
                    <h3 class="product-title">Xiomi 15</h3>
                    <div class="product-price">From &#8377;64,999</div>
                    <div class="product-actions">
                        <button class="add-button">Add to Cart</button>
                    </div>
                </div>
            </div>

            <!-- Product Card 9 -->
            <div class="product-card">
                <div class="product-image-container">
                    <button class="wishlist-btn">
                        <span class="material-symbols-rounded">favorite</span>
                    </button>
                    <img src="./assets/images/products/Iqoo/Iqoo 13/Legend/1.webp" alt="Iqoo 13" style="width: 170px; height: 170px;">
                </div>

                <div class="product-details">
                    <h3 class="product-title">Iqoo 13</h3>
                    <div class="product-price">From &#8377;51,999</div>
                    <div class="product-actions">
                        <button class="add-button">Add to Cart</button>
                    </div>
                </div>
            </div>

            <!-- Product Card 10 -->
            <div class="product-card">
                <div class="product-image-container">
                    <div class="product-badge">New</div>
                    <button class="wishlist-btn">
                        <span class="material-symbols-rounded">favorite</span>
                    </button>
                    <img src="./assets/images/products/Motorola/Edge 50 Ultra/Peach Fuzz/1.webp" alt="Galaxy Z Fold7" style="width: 160px; height: 160px;">
                </div>

                <div class="product-details">
                    <h3 class="product-title">Galaxy Z Fold7</h3>
                    <div class="product-price">From &#8377;1,74,999</div>
                    <div class="product-actions">
                        <button class="add-button">Add to Cart</button>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <script src="./assets/js/main.js"></script>
</body>

</html>