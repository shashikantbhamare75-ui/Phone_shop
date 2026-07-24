# Phone Shop — PHP/MySQL E-Commerce Project

A simple phone e-commerce site with customer storefront + admin panel,
built with PHP (PDO), MySQL, HTML, CSS, and vanilla JavaScript.

## Features

**Customer side**
- Browse products, filter by category, search by brand/model
- Product detail page with stock-aware "Add to Cart"
- Cart with live quantity update / remove (AJAX)
- Checkout with delivery address + payment method, atomic order placement
- Register / Login / Logout with hashed passwords and PHP sessions
- Profile page: edit info, view order history with status

**Admin side**
- Dashboard with product/user/order/revenue stats
- Add / edit / delete products with image upload
- Manage categories
- Manage orders (update status, view line items)
- Manage users (promote/demote admin, delete accounts)

## Requirements

- PHP 8.0+
- MySQL / MariaDB
- A local server stack (XAMPP, WAMP, MAMP, or `php -S` + MySQL)

## Setup

1. **Create the database**
   Import `database/phone_shop.sql` into MySQL (via phpMyAdmin, or):
```bash
   mysql -u root -p < database/phone_shop.sql
```

2. **Set the default admin password**
   The SQL file inserts a placeholder admin row. Generate a real password
   hash before logging in:
```php
   <?php echo password_hash("your-chosen-password", PASSWORD_DEFAULT);
```
   Run that snippet once (e.g. in a throwaway PHP file), copy the output,
   and update the `password` column for the `admin@phoneshop.com` row in
   the `users` table with it.

3. **Configure the database connection**
   Edit `database/db.php` if your MySQL username/password/host differ
   from the defaults (`root` / no password / `localhost`).

4. **Create required folders**
   These must exist and be writable by PHP: