<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use Spatie\Permission\Models\Role;

class InventoryTransactionTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    public function test_example(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }

    use RefreshDatabase; // Resets the DB after each test

    public function test_admin_can_add_stock_to_product()
    {
        // 1. Setup Data & Roles
        $adminRole = Role::create(['name' => 'admin']);
        $admin = User::factory()->create();
        $admin->assignRole($adminRole);

        $category = Category::create(['name' => 'Tech', 'slug' => 'tech']);
        $product = Product::create([
            'category_id' => $category->id,
            'sku' => 'LAP-001',
            'name' => 'Laptop',
            'price' => 1000,
            'stock_level' => 10
        ]);

        // 2. Execute the API Request
        $response = $this->actingAs($admin, 'sanctum')->postJson("/api/products/{$product->id}/stock", [
            'type' => 'add',
            'quantity' => 5,
            'notes' => 'New shipment arrived'
        ]);

        // 3. Assertions (Verify the outcome)
        $response->assertStatus(200)
                 ->assertJson(['current_stock' => 15]);

        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'stock_level' => 15
        ]);

        $this->assertDatabaseHas('inventory_transactions', [
            'product_id' => $product->id,
            'type' => 'add',
            'quantity' => 5
        ]);
    }
}
