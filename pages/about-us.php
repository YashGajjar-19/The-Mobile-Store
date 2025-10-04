<?php
$page_title = 'About Us | The Mobile Store';
require_once '../includes/header.php';
require_once '../includes/navbar.php';
?>

<main class="products-page-section">
    <div class="section-title" style="padding-top: 95px;">
        <h2>About Us</h2>
        <div class="title-line" style="margin-top: 10px; margin-bottom: 20px;"></div>
    </div>

    <div class="contact-form-container">
        <div class="contact-form-wrapper">
            <!-- Image column -->
            <div class="contact-image-column" style="padding: 0;">
                <img src="../assets/images/svg/about_us.svg" alt="About Us Image" class="login-image" style="width: 70%;">
            </div>

            <!-- Content column -->
            <div class=" contact-content-column">
                <!-- Content form -->
                <div class="form-card" style="padding: 20px; min-width: 600px;">
                    <div class="form-header">
                        <h2>Welcome to The Mobile Store</h2>
                    </div>

                    <p style="text-align: justify; margin-top: 15px; line-height: 1.6;">
                        Welcome to The Mobile Store, your one-stop shop for the latest and greatest mobile phones. We are passionate about technology and believe in providing our customers with the best products and services.
                    </p>

                    <p style="text-align: justify; margin-top: 15px; line-height: 1.6;">
                        Our mission is to offer a wide range of mobile devices from all the leading brands at competitive prices. We are committed to providing an exceptional shopping experience, from browsing our extensive catalog to receiving your new phone at your doorstep.
                    </p>

                    <p style="text-align: justify; margin-top: 15px; line-height: 1.6;">
                        At The Mobile Store, we value our customers and strive to build long-lasting relationships. Our team of experts is always here to help you with any queries you may have. Thank you for choosing us as your trusted mobile phone provider.
                    </p>
                </div>
                <!-- Form card ends here -->
            </div>
            <!-- Content column ends here -->
        </div>
        <!-- Contact form wrapper ends here -->
    </div>
    <!-- Contact form container ends here -->
</main>

<?php
require_once '../includes/footer.php';
?>