<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ProductTest extends TestCase
{

    /**
     * Test endpoint GET /products
     */
    public function test_can_list_products()
    {
        $response = $this->getJson('/api/products', [
            'Authorization' => env('API_TOKEN'),
        ]);

        $response->assertStatus(200)
                 ->assertJsonStructure([
                     'data' => [
                         '*' => ['code', 'product_name', 'status', 'imported_t']
                     ]
                 ]);
    }

    /**
     * Test endpoint GET /products/{code}
     */
    public function test_can_get_a_product_by_code()
    {
        $product = Product::first();

        $response = $this->getJson("/api/products/{$product->code}", [
            'Authorization' => env('API_TOKEN'),
        ]);

        $response->assertStatus(200);
    }

    /**
     * Test endpoint PUT /products/{code}
     */
    public function test_can_update_a_product()
    {
        $product = Product::first();

        $updatedData = ['product_name' => 'New Product Name Update'];

        $response = $this->putJson("/api/products/{$product->code}", $updatedData, [
            'Authorization' => env('API_TOKEN'),
        ]);

        $response->assertStatus(200)
                 ->assertJson([
                     'status' => true,
                     'message' => 'Product has been updated.'
                 ]);
    }
}
