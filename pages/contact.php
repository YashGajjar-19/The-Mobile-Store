<?php
$page_title = 'Contact Us | The Mobile Store';
require_once '../includes/header.php';
require_once '../includes/navbar.php';
?>

<main class="products-page-section">

    <!-- Form header -->
    <div class="section-title" style="padding-top: 95px;">
        <h2>Contact Us</h2>
        <div class="title-line" style="margin-top: 10px; margin-bottom: 20px;"></div>
    </div>

    <!-- Details -->
    <div class="contact-details-section">
        <div class="contact-info-container">

            <div class="contact-info-item">
                <span class="material-symbols-rounded icon">location_on</span>
                <h3>Our Address</h3>
                <p>123 Mobile Street, Tech City, 12345, India</p>
            </div>

            <div class="contact-info-item">
                <span class="material-symbols-rounded icon">phone</span>
                <h3>Call Us</h3>
                <p><a href="tel:+911234567890">+91 1234567890</a></p>
            </div>

            <div class="contact-info-item">
                <span class="material-symbols-rounded icon">mail</span>
                <h3>Email Us</h3>
                <p><a href="mailto:info@themobilestore.com">info@themobilestore.com</a></p>
            </div>
        </div>
    </div>

    <!-- Contact form container -->
    <div class="contact-form-container">
        <!-- Contact form wrapper -->
        <div class="contact-form-wrapper">
            <!-- Contact content -->
            <div class="contact-content-column">
                <!-- Contact form -->
                <div class="form-card" style="min-width: 600px;">
                    <!-- Contact header -->
                    <div class="form-header">
                        <h2>Send Us a Message</h2>
                    </div>

                    <!-- Alert container for submission status -->
                    <div class="alert-container" style="position: static; max-width: 1000px; margin: 10px 0px;">
                        <?php
                        if (isset($_GET['status'])) {
                            if ($_GET['status'] == 'success') {
                                echo '<div class="alert alert-success">Your message has been sent successfully! We will get back to you shortly.</div>';
                            } else if ($_GET['status'] == 'error') {
                                $msg = $_GET['msg'] ?? 'unknown';
                                $error_message = 'An unknown error occurred.';
                                if ($msg == 'emptyfields') {
                                    $error_message = 'Please fill in all the required fields.';
                                } else if ($msg == 'invalidemail') {
                                    $error_message = 'Please provide a valid email address.';
                                } else if ($msg == 'dberror') {
                                    $error_message = 'Could not send your message due to a server error. Please try again later.';
                                }
                                echo '<div class="alert alert-error">' . $error_message . '</div>';
                            }
                        }
                        ?>
                    </div>

                    <!-- Contact form starts here -->
                    <form action="../handlers/contact_handler.php" method="POST" class="auth-form">

                        <div class="form-group">
                            <label for="name" class="form-label">Your Name</label>
                            <div class="form-input">
                                <ion-icon name="person-outline"></ion-icon>
                                <input type="text" id="name" name="name" placeholder="Enter your name" required>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="email" class="form-label">Your Email</label>
                            <div class="form-input">
                                <ion-icon name="mail-outline"></ion-icon>
                                <input type="email" id="email" name="email" placeholder="Enter your email" required>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="subject" class="form-label">Subject</label>
                            <div class="form-input">
                                <ion-icon name="chatbox-ellipses-outline"></ion-icon>
                                <input type="text" id="subject" name="subject" placeholder="Subject of your message"
                                    required>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="message" class="form-label">Message</label>
                            <div class="form-input">
                                <ion-icon name="create-outline" style="top: 25px;"></ion-icon>
                                <textarea id="message" name="message" placeholder="Your message here..." required
                                    style="min-height: 120px; width: 100%; padding: 15px 15px 15px 45px; border: 1px solid var(--glass); border-radius: 10px; font-size: 0.95rem;"></textarea>
                            </div>
                        </div>

                        <div class="form-submit">
                            <button type="submit" class="button">Send Message</button>
                        </div>
                    </form>
                </div>
                <!-- Form card ends here -->
            </div>
            <!-- Content column ends here -->

            <div class="contact-image-column">
                <img src="../assets/images/svg/contact.svg" alt="Contact Us Image" class="login-image">
            </div>

        </div>
        <!-- Form wrapper ends here -->
    </div>
    <!-- Form contact container ends here -->
</main>
<?php
require_once '../includes/footer.php';
?>