<?php

namespace Tests\Feature;

use App\Enums\StockActionType;
use App\Models\Product;
use App\Services\Orders\OrderServices;
use Tests\TestCase;

class OrdersTest extends TestCase
{
    public function test_invalid_product_id_returns_error()
    {
        $response = $this->postJson('/api/v1/orders', [
            'products' => [
                ['product_id' => 999, 'quantity' => 1]
            ]
        ]);

        $response->assertUnprocessable();
        $response->assertJsonValidationErrors('products.0.product_id');
    }

    public function test_insufficient_ingredient_stock_returns_error()
    {
        $response = $this->postJson('/api/v1/orders', [
            'products' => [
                ['product_id' => 1, 'quantity' => 99999999]
            ]
        ]);

        $response->assertUnprocessable();
        $response->assertJsonValidationErrors('products.0.quantity');
    }

    public function test_transaction_rolled_back_correctly_whenever_any_exception_thrown()
    {
        $this->partialMock(OrderServices::class, function ($mock) {
            $mock->shouldReceive('placeOrder')->andThrow(new \Exception('Something went wrong'));
        });

        $product = Product::first();

        $response = $this->postJson('/api/v1/orders', [
            'products' => [
                ['product_id' => $product->id, 'quantity' => 1]
            ]
        ]);

        $response->assertServerError();

        //Make sure no orders created
        //Make sure no order_product created
        //Make sure no outgoing stock history created
        $this->assertDatabaseMissing('orders', []);
        $this->assertDatabaseMissing('order_product', [
            'order_id' => $response->json('data.id'),
            'product_id' => $product->id,
            'quantity' => 1,
        ]);

        $this->assertDatabaseMissing('ingredient_stocks', [
            'action_type' => StockActionType::OUTGOING
        ]);

    }

    public function test_order_is_stored_correctly_in_database()
    {
        $product = Product::first();

        $response = $this->postJson('/api/v1/orders', [
            'products' => [
                ['product_id' => $product->id, 'quantity' => 1]
            ]
        ])->assertCreated()
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'created_at',
                    'products' => [
                        '*' => [
                            'id',
                            'name',
                            'quantity'
                        ]
                    ],
                ]
            ]);

        $this->assertDatabaseHas('orders', []);
        $this->assertDatabaseHas('order_product', [
            'order_id' => $response->json('data.id'),
            'product_id' => $product->id,
            'quantity' => 1,
        ]);
    }
}
