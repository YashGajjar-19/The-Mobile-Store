<?php
$page_title = 'Frequently Asked Questions';
require_once '../includes/header.php';
require_once '../includes/navbar.php';
?>

<main class="products-page-section">
    <div class="section-title" style="padding-top: 95px;">
        <h2>Frequently Asked Questions</h2>
        <div class="title-line" style="margin-top: 10px; margin-bottom: 20px;"></div>
    </div>

    <div class="contact-form-container">
        <div class="contact-form-wrapper">
            <div class="contact-image-column" style="padding: 0;">
                <img src="../assets/images/svg/faq.svg" alt="FAQ Image" class="login-image" style="width: 70%;">
            </div>
            <div class="contact-content-column">
                <div class="form-card" style="padding: 20px; min-width: 600px;">
                    <div class="faq-item" style="margin-bottom: 20px;">
                        <h3 style="font-size: 1.3rem; margin-bottom: 10px;">What payment methods do you accept?</h3>
                        <p style="text-align: justify; line-height: 1.6;">
                            We accept all major credit cards, debit cards, and net banking. We also offer cash on delivery on all orders.
                        </p>
                    </div>

                    <div class="faq-item" style="margin-bottom: 20px;">
                        <h3 style="font-size: 1.3rem; margin-bottom: 10px;">How long will it take to receive my order?</h3>
                        <p style="text-align: justify; line-height: 1.6;">
                            Orders are typically processed and shipped within 1-2 business days. Delivery times vary based on your location but usually take 3-5 business days.
                        </p>
                    </div>

                    <div class="faq-item" style="margin-bottom: 20px;">
                        <h3 style="font-size: 1.3rem; margin-bottom: 10px;">Do you ship internationally?</h3>
                        <p style="text-align: justify; line-height: 1.6;">
                            Currently, we only ship within India. We are working on expanding our shipping options to more countries in the future.
                        </p>
                    </div>

                    <div class="faq-item" style="margin-bottom: 20px;">
                        <h3 style="font-size: 1.3rem; margin-bottom: 10px;">What is your return policy?</h3>
                        <p style="text-align: justify; line-height: 1.6;">
                            We offer a 7-day return policy for all our products. If you are not satisfied with your purchase, you can return it for a full refund or exchange.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<?php
require_once '../includes/footer.php';
?>