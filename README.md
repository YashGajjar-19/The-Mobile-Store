# The Mobile Store 📱

Welcome to **The Mobile Store**, a comprehensive e-commerce platform designed for selling mobile phones and accessories online. It features a modern, user-friendly interface for customers and a powerful admin dashboard for managing products, orders, and users.

> **Note:** This project is built using vanilla PHP and MySQL.

## 🚀 Features

### Customer-Facing
- **User Authentication**: Secure registration and login system.
- **Product Browsing**: Browse mobile phones by brand, category, and price range.
- **Search & Filter**: Advanced search functionality with filtering options.
- **Shopping Cart**: Add items to cart, update quantities, and remove items.
- **Wishlist**: Save favorite items for later.
- **Checkout Process**: Seamless checkout flow with order summary.
- **Responsive Design**: Optimized for mobile, tablet, and desktop devices.
- **Modern UI**: Clean and intuitive interface with smooth animations.

### Admin Panel
- **Dashboard**: Real-time overview of total users, orders, and sales.
- **Product Management**: Add, edit, delete, and view products with image uploads.
- **Order Management**: View and update order statuses (Pending, Approved, Shipped, Delivered).
- **User Management**: View user details and manage accounts.
- **Category Management**: Organize products into brands and categories.

## 🛠️ Technology Stack

- **Frontend**: HTML5, CSS3, JavaScript (Vanilla)
- **Backend**: PHP (Vanilla)
- **Database**: MySQL
- **Server**: Apache (via XAMPP/WAMP or similar)

## 📂 Project Structure

The project follows a modular structure to separate concerns and improve maintainability:

```
├── admin/          # Admin dashboard interface and logic
├── assets/         # Static assets (CSS, JS, Images, Fonts)
├── handlers/       # Form submission and AJAX handlers
├── includes/       # Reusable components (Header, Footer, DB Config)
├── pages/          # Individual page content
├── products/       # Product-specific logic and views
├── user/           # User dashboard and profile management
├── index.php       # Main entry point (Homepage)
├── mobile_store.sql # Database schema and initial data
└── structure.ini   # Project structure configuration
```

## ⚙️ Installation & Setup

Follow these steps to set up the project locally:

### 1. Prerequisites
- A local web server environment like [XAMPP](https://www.apachefriends.org/) or [WAMP](http://www.wampserver.com/).
- PHP 7.4 or higher.
- MySQL 5.7 or higher.

### 2. Clone the Repository
```bash
git clone https://github.com/your_username/The-Mobile-Store.git
```
Or simply download the ZIP file and extract it to your web server's root directory (e.g., `htdocs` in XAMPP or `www` in WAMP).

### 3. Database Setup
1. Open **phpMyAdmin** (usually at `http://localhost/phpmyadmin`).
2. Create a new database named `mobile_store`.
3. Import the `mobile_store.sql` file located in the project root directory into the newly created database.

### 4. Configuration
1. Open the file `includes/config.php`.
2. Update the database connection details if your local setup differs from the default:

```php
$servername = "localhost";
$username = "root";       // Default XAMPP username
$password = "";           // Default XAMPP password (empty)
$dbname = "mobile_store"; // Database name
```

3. Ensure the `SITE_URL` constant matches your local path:
```php
define('SITE_URL', 'http://localhost/The-Mobile-Store/');
```

### 5. Run the Project
1. Start the Apache and MySQL modules in your XAMPP/WAMP control panel.
2. Open your web browser.
3. Navigate to: `http://localhost/The-Mobile-Store/`
