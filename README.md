# Madura Mart - Digital Platform & Management System

<p align="center">
  <img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo">
</p>

Madura Mart is a comprehensive digital platform built with Laravel to manage retail operations, inventory, and transactions. It features a complete management system for distributors, clients, couriers, products, and sales tracking.

## 🚀 Key Features

- **Advanced User Management**:
    - Multi-role support: `Admin`, `Owner`, `Courier`, and `Customer`.
    - Profile status and photo management.
    - Full CRUD for users with secure password hashing.
- **Inventory & Product Management**:
    - Product categorization and tracking.
    - **Recycle Bin**: Soft-delete feature to prevent data loss.
    - Image management for products.
- **Logistics & Transactions**:
    - **Purchases**: Track stock from distributors.
    - **Orders & Sales**: Manage incoming customer orders and final sales.
    - **Deliveries**: Monitor courier assignments and delivery statuses.
- **Reporting System**:
    - Real-time reports for distributors, products, purchases, orders, and sales.
    - Data visualization and summaries.

## 🛠 Tech Stack

- **Backend**: Laravel 11.x (PHP 8.2+)
- **Database**: MySQL / MariaDB
- **Frontend**: Blade Templating, Bootstrap, Custom Vanilla CSS
- **Tools**: Composer, Vite, NPM

## ⚙️ Installation Guide

Follow these steps to set up the project locally:

1. **Clone the Repository**
   ```bash
   git clone https://github.com/rey109/madura_mart.git
   cd madura_mart
   ```

2. **Install PHP Dependencies**
   ```bash
   composer install
   ```

3. **Install Frontend Dependencies**
   ```bash
   npm install
   ```

4. **Environment Configuration**
   Copy the `.env.example` file to `.env` and configure your database settings.
   ```bash
   cp .env.example .env
   # Open .env and set DB_DATABASE, DB_USERNAME, DB_PASSWORD
   ```

5. **Generate Application Key**
   ```bash
   php artisan key:generate
   ```

6. **Run Database Migrations & Seeding**
   ```bash
   php artisan migrate --seed
   ```

7. **Create Storage Link**
   ```bash
   php artisan storage:link
   ```

8. **Compile Assets**
   ```bash
   npm run dev
   ```

9. **Start the Server**
   ```bash
   php artisan serve
   ```

## 📂 Project Structure Highlights

- `app/Http/Controllers`: Contains the business logic for all modules.
- `app/Models`: Database structure and relationships (User, Product, Order, etc.).
- `resources/views`: UI templates organized by module (User, Product, Dashboard).
- `public/images/users`: Storage for user profile photos.
- `database/migrations`: Database schema history.

## 🤝 Contributing

This project is currently in the prototype stage. Feel free to fork the repository and submit pull requests.

## 📄 License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
