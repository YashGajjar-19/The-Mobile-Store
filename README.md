The Mobile Store : 
- The Mobile Store is a comprehensive e-commerce platform designed for selling mobile phones and accessories online. It features a user-friendly interface for customers and a powerful admin dashboard for managing products, orders, and users.

About The Project :
- This project is a full-featured online store for mobile devices. It allows users to browse a wide range of products, add items to their cart and wishlist, and proceed through a seamless checkout process. The admin panel provides complete control over the store's inventory, customer data, and order fulfillment.

Key Features: 

Customer-Facing:
- User registration and login
- Browse products by brand and category
- Product search and filtering capabilities
- Shopping cart and wishlist functionality
- Detailed product pages with images and specifications
- Responsive design for mobile and desktop Browse

Admin Panel:
- Dashboard with summaries of total users and orders
- Product management: add, edit, and view products
- Order management with status updates (e.g., pending, approved, delivered)
- User management and administration

Technologies Used: 
- Frontend: HTML, CSS, JavaScript
- Backend: PHP
- Database: MySQL

Project Structure :

The project follows a modular structure to separate concerns and improve maintainability:
- ├── admin/ # Admin dashboard files
- ├── assets/ # CSS, JS, images, etc.
- ├── includes/ # PHP configuration and function files
- ├── products/ # Product listing and detail pages
- ├── user/ # User profile, orders, and auth pages
- ├── index.html # Homepage
- └── db.sql # Database schema

Database Schema :

The database is designed to handle all aspects of an e-commerce store, including users, products, orders, and payments. Key tables include:
- users: Stores customer and admin information.
- products: Contains all product details, including pricing and specifications.
- brands: Manages the different brands available in the store.
- orders & order_items: Tracks customer orders and the products within them.
- cart & wishlist: Manages items saved by users for purchase or later viewing.

Getting Started :
- To get a local copy up and running, follow these simple steps.

Prerequisites :
- A web server environment (e.g., XAMPP, WAMP) with PHP and MySQL.

Installation : 

Clone the repository:
- Bash
- git clone https://github.com/your_username/the-mobile-store.git

Import the database:
- Create a new database named mobile_store.
- Import the db.sql file to set up the necessary tables and initial data.

Configure the database connection:
- Open includes/config.php
- Update the database credentials ($servername, $username, $password, $dbname) to match your local environment.

Run the project:
- Place the project folder in your web server's root directory (e.g., htdocs in XAMPP).
- Open your web browser and navigate to http://localhost/the-mobile-store.
