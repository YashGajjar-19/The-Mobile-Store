<?php
$page_title = 'Shipping Information | The Mobile Store';
require_once '../includes/header.php';
require_once '../includes/navbar.php';
?>

<main class="products-page-section">
    <!-- Header -->
    <div class="section-title" style="padding-top: 95px;">
        <h2>Shipping Information</h2>
        <div class="title-line" style="margin-top: 10px; margin-bottom: 20px;"></div>
    </div>

    <!-- Form container -->
    <div class="contact-form-container">
        <!-- Form wrapper -->
        <div class="contact-form-wrapper">
            <!-- Image column -->
            <div class="contact-image-column" style="padding: 0;"> <img src="../assets/images/svg/shipping.svg" alt="Shipping Information Image" class="login-image" style="width: 70%;">
            </div>

            <!-- Content column -->
            <div class="contact-content-column">
                <!-- Form card -->
                <div class="form-card" style="padding: 20px; min-width: 600px;">

                    <div class="policy-section" style="margin-bottom: 20px;">
                        <h3 style="font-size: 1.3rem; margin-bottom: 10px;">Shipping Options</h3>
                        <p style="text-align: justify; line-height: 1.3;">
                            We offer standard and express shipping options for all orders. Standard shipping is free on all orders, while express shipping is available for a nominal fee.
                        </p>
                    </div>

                    <div class="policy-section" style="margin-bottom: 20px;">
                        <h3 style="font-size: 1.3rem; margin-bottom: 10px;">Delivery Times</h3>
                        <p style="text-align: justify; line-height: 1.3;">
                            Standard shipping typically takes 3-5 business days for delivery, while express shipping takes 1-2 business days. Please note that delivery times may vary based on your location.
                        </p>
                    </div>

                    <div class="policy-section" style="margin-bottom: 20px;">
                        <h3 style="font-size: 1.3rem; margin-bottom: 10px;">Order Tracking</h3>
                        <p style="text-align: justify; line-height: 1.3;">
                            Once your order has been shipped, you will receive an email with a tracking number. You can use this tracking number to track the status of your order on our website or the courier's website.
                        </p>
                    </div>

                </div>
                <!-- Form card ends here -->
            </div>
            <!-- Content column ends here -->
        </div>
        <!-- Form wrapper ends here -->
    </div>
    <!-- Form container ends here -->
</main>

<?php
require_once '../includes/footer.php';
?>