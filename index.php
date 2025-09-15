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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css"
        crossorigin="anonymous" referrerpolicy="no-referrer" />

    <!-- Style Sheets -->
    <link rel="stylesheet" href="./assets/css/main.css">
</head>

<body>
    <!-- Header Section -->
    <section class="header-container" id="header-section">
        <header class="header">
            <a href="./index.html" class="logo-container">
                <img class="logo-image" src="./assets/images/Logo.png" alt="The Mobile Store">
                <span class="logo-text">The Mobile Store</span>
            </a>
            <!-- Navigation Bar -->
            <div class="nav-wrapper">
                <nav class="navbar">
                    <div class="search-container">
                        <input type="search" class="search-input" placeholder="Search...">
                        <a class="nav-icon search-btn">
                            <span class="material-symbols-rounded">search</span>
                        </a>
                    </div>
                    <a href="#" class="nav-icon">
                        <span class="material-symbols-rounded">home</span>
                    </a>
                    <a href="#" class="nav-icon">
                        <span class="material-symbols-rounded">shopping_cart</span>
                        <span class="cart-badge">3</span>
                    </a>
                    <a href="#" class="nav-icon">
                        <span class="material-symbols-rounded">account_circle</span>
                    </a>
                </nav>

                <!-- Mobile Menu Button -->
                <button class="mobile-menu-btn">
                    <span class="material-symbols-rounded">
                        menu
                    </span>
                </button>
            </div>

            <!-- Mobile Menu -->
            <div class="mobile-menu">
                <div class="mobile-search-container">
                    <input type="search" class="mobile-search-input" placeholder="Search...">
                    <button class="mobile-search-btn">
                        <span class="material-symbols-rounded">search</span>
                    </button>
                </div>
                <a href="#" class="mobile-nav-icon">
                    <span class="material-symbols-rounded">home</span>
                    <span>Home</span>
                </a>
                <a href="#" class="mobile-nav-icon">
                    <span class="material-symbols-rounded">shopping_cart</span>
                    <span>Cart</span>
                    <span class="cart-badge">3</span>
                </a>
                <a href="#" class="mobile-nav-icon">
                    <span class="material-symbols-rounded">account_circle</span>
                    <span>Account</span>
                </a>
                <a href="includes/auth.php?action=logout" class="nav-icon">
                    <span class="material-symbols-rounded">logout</span>
                </a>
            </div>
        </header>
    </section>

    <!-- Hero Section -->
    <section class="hero-section">
        <div class="hero-container">
            <div class="hero-image-column">
                <img src="./assets/images/svg/hero.svg" alt="Hero Image" class="hero-image">
            </div>

            <div class="hero-text-column">
                <h1 class="hero-headline">Welcome to the world of premium smart phones</h1>
                <p class="hero-subheadline">Discover cutting-edge technology and elegant design, crafted for a seamless
                    mobile experience.</p>
                <button class="button" onclick="window.location.href='#products-section'">Explore More</button>
            </div>
        </div>
    </section>

    <!-- Featured Products -->
    <section class="feature-section">
        <!-- Title Part -->
        <div class="feature-title">
            <h2> Featured Products </h2>
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
                        <button class="button" style="width: 25%; border-radius: 50px;">Buy Now</button>
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
                        <button class="button" style="width: 25%; border-radius: 50px;">Buy Now</button>
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
                        <button class="button" style="width: 25%; border-radius: 50px;">Buy Now</button>
                    </div>
                </div>

                <!-- Slide 4 -->
                <!-- Image Section -->
                <div class="slide">
                    <div class="slide-image">
                        <div class="slide-status trending">Trending</div>
                        <img src="./assets/images/products/Samsung/S25 Ultra/Titanium WhiteSilver/1.webp"
                            alt="Galaxy S25 Ultra">
                    </div>
                    <!-- Detail Section -->
                    <div class="slide-details">
                        <div class="slide-name">Galaxy S25 Ultra</div>
                        <div class="slide-price">From &#8377;1,17,999</div>
                        <button class="button" style="width: 25%; border-radius: 50px;">Buy Now</button>
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
                        <button class="button" style="width: 25%; border-radius: 50px;">Buy Now</button>
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
        <!-- Title Part -->
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
                <img src="./assets/images/brands-logo/Samsung.webp" alt="Samsung">
            </div>

            <!-- Brand 2 -->
            <div class="brand-card">
                <img src="./assets/images/brands-logo/Apple.webp" alt="Apple">
            </div>

            <!-- Brand 3 -->
            <div class="brand-card">
                <img src="./assets/images/brands-logo/Google.webp" alt="Google">
            </div>

            <!-- Brand 4 -->
            <div class="brand-card">
                <img src="./assets/images/brands-logo/Oneplus.webp" alt="OnePlus">
            </div>

            <!-- Brand 5 -->
            <div class="brand-card">
                <img src="./assets/images/brands-logo/Xiomi.webp" alt="Xiaomi">
            </div>

            <!-- Brand 6 -->
            <div class="brand-card">
                <img src="./assets/images/brands-logo/Oppo.webp" alt="Oppo">
            </div>

            <!-- Brand 7 -->
            <div class="brand-card">
                <img src="./assets/images/brands-logo/Vivo.webp" alt="Vivo">
            </div>

            <!-- Brand 8 -->
            <div class="brand-card">
                <img src="./assets/images/brands-logo/Iqoo.webp" alt="">
            </div>

            <!-- Brand 9 -->
            <div class="brand-card">
                <img src="./assets/images/brands-logo/Nothing.webp" alt="Nothing">
            </div>

            <!-- Brand 10 -->
            <div class="brand-card" id="products-section">
                <img src="./assets/images/brands-logo/Motorola.webp" alt="Motorola">
            </div>
        </div>
    </section>

    <!-- Products Section -->
    <section class="products-section">
        <!-- Title Part-->
        <div class="section-title" id="section-title">
            <h2>Browse Products</h2>
            <div class="title-line"></div>
        </div>

        <div class="products-grid">
            <!-- Product Card 1 -->
            <div class="product-card">
                <div class="product-badge trending">Hot</div>
                <h3 class="product-title">Iphone 16 Pro</h3>
                <p class="product-tagline">The ultimate iPhone experience.</p>
                <button class="wishlist-btn">
                    <span class="material-symbols-rounded">favorite</span>
                </button>

                <div class="product-image-container">
                    <img src="./assets/images/products/Apple/iPhone 16 Pro/Black Titanium/1.webp" alt="Iphone 16 Pro">
                </div>

                <div class="product-info-bottom">
                    <div class="product-price">From &#8377;1,74,999</div>
                    <button class="button buy-button">Buy</button>
                </div>
            </div>

            <!-- Product Card 2 -->
            <div class="product-card">
                <div class="product-badge new">New</div>
                <h3 class="product-title">iPhone 16E</h3>
                <p class="product-tagline">The ultimate iPhone experience.</p>
                <button class="wishlist-btn">
                    <span class="material-symbols-rounded">favorite</span>
                </button>

                <div class="product-image-container">
                    <img src="./assets/images/products/Apple/iPhone 16E/Black/1.webp" alt="Iphone 16 Pro">
                </div>

                <div class="product-info-bottom">
                    <div class="product-price">From &#8377;78,999</div>
                    <button class="button buy-button">Buy</button>
                </div>
            </div>

            <!-- Product Card 3 -->
            <div class="product-card">
                <div class="product-badge trending">Hot</div>
                <button class="wishlist-btn">
                    <span class="material-symbols-rounded">favorite</span>
                </button>
                <h3 class="product-title">Pixel 9</h3>
                <p class="product-tagline">The ultimate Pixel experience.</p>

                <div class="product-image-container">
                    <img src="./assets/images/products/Google/Pixel 9/Obsidian/1.webp" alt="Pixel 9">
                </div>

                <div class="product-info-bottom">
                    <div class="product-price">From &#8377;74,999</div>
                    <button class="button buy-button">Buy</button>
                </div>
            </div>
        </div>
        <div class="browse-more-container">
            <a href="#" class="button browse-more-btn">Browse More</a>
        </div>
    </section>

    <!-- Footer Section -->
    <footer class="footer">
        <div class="footer-content">
            <div class="footer-section">
                <div class="footer-logo-container">
                    <img class="logo-image" src="./assets/images/logo.png" alt="The Mobile Store">
                    <span class="logo-text">The Mobile Store</span>
                </div>
                <p>Your one-stop shop for the latest mobile devices. We bring the future to your
                    fingertips.
                </p>
                <div class="social-links">
                    <a href="#" class="social-link" aria-label="Facebook">
                        <i class="fab fa-facebook-f"></i>
                    </a>
                    <a href="#" class="social-link" aria-label="Instagram">
                        <i class="fab fa-instagram"></i>
                    </a>
                    <a href="#" class="social-link" aria-label="Whatsapp">
                        <i class="fab fa-whatsapp"></i>
                    </a>
                    <a href="#" class="social-link" aria-label="Gmail">
                        <i class="fa-regular fa-envelope"></i>
                    </a>
                </div>
            </div>

            <div class="footer-section">
                <h3>Quick Links</h3>
                <ul>
                    <li><a href="./index.html">Home</a></li>
                    <li><a href="./products.html">Products</a></li>
                    <li><a href="./about.html">About Us</a></li>
                    <li><a href="./contact.html">Contact</a></li>
                    <li><a href="./privacy.html">Privacy Policy</a></li>
                </ul>
            </div>

            <div class="footer-section">
                <h3>Customer Service</h3>
                <ul>
                    <li><a href="./faq.html">FAQ</a></li>
                    <li><a href="./returns.html">Returns & Refunds</a></li>
                    <li><a href="./shipping.html">Shipping Information</a></li>
                </ul>
            </div>

            <div class="footer-section footer-contact">
                <a href="./contact.html">
                    <h3>Contact Us</h3>
                </a>
                <p>123 Mobile Street, Tech City, 12345</p>
                <p>Email: <a href="#">info@themobilestore.com</a></p>
                <p>Phone: <a href="#">+91 1234567890</a></p>
            </div>
        </div>

        <div class="footer-bottom">
            &copy; 2025 The Mobile Store. All rights reserved.
        </div>
    </footer>

    <script src="./assets/js/main.js"></script>
</body>

</html>