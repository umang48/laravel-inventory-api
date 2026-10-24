# Laravel Inventory & Management API

An enterprise-grade REST API built to manage product inventory, demonstrating decoupled architecture, strict validation, and database integrity. 

## Tech Stack
* **Backend:** PHP 8, Laravel 11, Eloquent ORM
* **Database:** MySQL
* **Auth & Security:** Laravel Sanctum, Spatie Permissions
* **Frontend (Demo):** jQuery AJAX, Bootstrap 5

## Key Features Highlighted for Interviewers
* **Database Transactions (`DB::transaction`):** Ensures absolute data integrity between the `products` table and the `inventory_transactions` audit ledger during stock updates.
* **Role-Based Access Control:** Secure routes utilizing Spatie middleware to restrict stock manipulation to Admin roles.
* **Global Exception Handling:** Custom interceptors in `bootstrap/app.php` to guarantee standard JSON responses (404, 422, 401) instead of HTML stack traces.
* **Query Scopes & Pagination:** Dynamic filtering (by category/search term) optimized for large datasets.

## Setup Instructions
1. Clone the repository: `git clone https://github.com/yourusername/inventory-api.git`
2. Install dependencies: `composer install`
3. Copy `.env.example` to `.env` and set your database credentials.
4. Generate app key: `php artisan key:generate`
5. Run migrations and seed the Admin user: `php artisan migrate --seed`
6. Start the server: `php artisan serve`