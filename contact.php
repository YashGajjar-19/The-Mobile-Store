<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Us | The Mobile Store</title>
    <link
        href="https://fonts.googleapis.com/css2?family=Material+Symbols+Rounded:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200"
        rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css"
        crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="./assets/css/main.css">
    <style>
        /* Specific styles for the contact page info boxes and map */
        .contact-details-section {
            padding: 20px;
            max-width: 1000px;
            margin: auto;
            display: flex;
            flex-direction: column;
            gap: 40px;
        }

        .contact-info-container {
            display: flex;
            gap: 20px;
            justify-content: space-evenly;
            text-align: center;
        }

        .contact-info-item {
            flex: 1 1 calc(33% - 40px);
            min-width: 280px;
            background-color: var(--body);
            border-radius: 15px;
            padding: 30px;
            box-shadow: var(--shadow);
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 15px;
            transition: transform 0.3s ease;
        }

        .contact-info-item:hover {
            transform: translateY(-5px);
        }

        .contact-info-item .icon {
            font-size: 3rem;
            color: var(--blue);
        }

        .contact-info-item h3 {
            font-size: 1.5rem;
            color: var(--darkest);
            margin-bottom: 5px;
        }

        .contact-info-item p,
        .contact-info-item a {
            font-size: 1rem;
            color: var(--dark);
            text-decoration: none;
        }
    </style>
</head>

<body>
    <main class="products-page-section">
        <div class="section-title" style="padding-top: 50px;">
            <h2>Contact Us</h2>
            <div class="title-line" style="margin-top: 10px; margin-bottom: 20px;"></div>
        </div>

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

        <div class="contact-form-container">
            <div class="contact-form-wrapper">
                <div class="contact-content-column">
                    <div class="form-card">
                        <div class="form-header">
                            <h2>Send Us a Message</h2>
                        </div>
                        <form action="#" method="POST" class="auth-form">
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
                </div>
                <div class="contact-image-column">
                    <img src="./assets/images/svg/contact.svg" alt="Contact Us Image" class="login-image">
                </div>
            </div>
        </div>
    </main>
    <script src="./assets/js/main.js"></script>
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>
</body>

</html>