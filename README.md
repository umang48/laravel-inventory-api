# Laravel Inventory & Management API

An enterprise-grade REST API built to manage product inventory, demonstrating decoupled architecture, strict validation, and database integrity. 

## Tech Stack
* **Backend:** PHP 8, Laravel 11, Eloquent ORM
* **Database:** MySQL
* **Auth & Security:** Laravel Sanctum, Spatie Permissions
* **Frontend (Demo):** jQuery AJAX, Bootstrap 5

## API Endpoints Documentation

All protected routes require an `Authorization: Bearer {token}` header and an `Accept: application/json` header.

### Authentication
* **`POST /api/login`**
  * **Payload:** `email`, `password`
  * **Returns:** User object and Sanctum access token.
* **`GET /api/profile`** (Protected)
  * **Returns:** The currently authenticated user's details.
* **`POST /api/logout`** (Protected)
  * **Returns:** Revokes the current token and ends the session.

### Categories (Protected, Requires 'admin' role)
* **`GET /api/categories`**
  * **Returns:** A JSON list of all categories.
* **`POST /api/categories`**
  * **Payload:** `name` (string), `slug` (string, unique).
  * **Returns:** 201 Created with the new category data.
* **`DELETE /api/categories/{id}`**
  * **Returns:** 204 No Content. Automatically cascades to delete all associated products.

### Products (Protected, Requires 'admin' role)
* **`GET /api/products`**
  * **Query Parameters (Optional):** 
    * `category_id`: Filter products by category.
    * `search`: Real-time search across SKU and product name.
    * `page`: For paginated results (10 per page).
  * **Returns:** Paginated JSON list of products with metadata and navigation links.
* **`POST /api/products`**
  * **Payload:** `category_id` (int), `sku` (string, unique), `name` (string), `price` (float), `stock_level` (int).
  * **Returns:** 201 Created with the new product data.
* **`DELETE /api/products/{id}`**
  * **Returns:** 204 No Content.

### Inventory Management (Protected, Requires 'admin' role)
* **`POST /api/products/{id}/stock`**
  * **Payload:** 
    * `type`: 'add' or 'subtract'
    * `quantity`: integer (min 1)
    * `notes`: string (optional, e.g., "New shipment")
  * **Description:** Utilizes strict `DB::transaction` blocks. Updates the product's `stock_level` and simultaneously writes an immutable audit record to the `inventory_transactions` table. Will rollback entirely if either query fails.

## Setup Instructions
1. Clone the repository: `git clone https://github.com/yourusername/inventory-api.git`
2. Install dependencies: `composer install`
3. Copy `.env.example` to `.env` and set your database credentials.
4. Generate app key: `php artisan key:generate`
5. Run migrations and seed the Admin user: `php artisan migrate --seed`
6. Start the server: `php artisan serve`