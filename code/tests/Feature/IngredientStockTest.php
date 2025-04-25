<?php

namespace Tests\Feature;

use App\Enums\StockActionType;
use App\Mail\LowStockAlert;
use App\Models\IngredientStock;
use App\Models\Product;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class IngredientStockTest extends TestCase
{
    public function test_ingredient_stock_is_deducted_correctly()
    {
        $product = Product::with('ingredients')->first();

        $quantity = 1;
        $this->postJson('/api/v1/orders', [
            'products' => [
                ['product_id' => $product->id, 'quantity' => $quantity]
            ]
        ]);

        foreach ($product->ingredients as $ingredient) {
            $expected = $ingredient->current_stock_in_grams - ($ingredient->pivot->amount_in_grams * $quantity);
            $actual = $ingredient->fresh()->current_stock_in_grams;
            $this->assertEquals($expected, $actual);
        }
    }

    public function test_stock_history_stored_correctly()
    {
        $product = Product::with('ingredients')->first();

        $quantity = 1;
        $this->postJson('/api/v1/orders', [
            'products' => [
                ['product_id' => $product->id, 'quantity' => $quantity]
            ]
        ]);
        foreach ($product->ingredients as $ingredient)
            $this->assertDatabaseHas('ingredient_stocks', [
                'ingredient_id' => $ingredient->id,
                'action_type' => StockActionType::OUTGOING,
                'amount_in_grams' => ($ingredient->pivot->amount_in_grams * $quantity) * -1, //-1 for deduction factory
            ]);
    }

    public function test_ingredient_stock_is_synced_with_history()
    {
        $product = Product::with('ingredients')->first();

        $quantity = 1;
        $this->postJson('/api/v1/orders', [
            'products' => [
                ['product_id' => $product->id, 'quantity' => $quantity]
            ]
        ]);

        foreach ($product->ingredients as $ingredient) {
            $expected = $ingredient->current_stock_in_grams - ($ingredient->pivot->amount_in_grams * $quantity);
            $actual = IngredientStock::where('ingredient_id', $ingredient->id)->sum('amount_in_grams');
            $this->assertEquals($expected, $actual);
        }
    }


    public function test_email_alert_sent_once_when_below_50_percent()
    {
        Mail::fake();
        $product = Product::first();
        $ingredient = $product->ingredients()->first();
        $ingredient->update(['current_stock_in_grams' => ($ingredient->current_stock_in_grams / 2) + 1]);  // just above 50%

        $this->postJson('/api/v1/orders', [
            'products' => [
                ['product_id' => $product->id, 'quantity' => 4]
            ]
        ]);

        Mail::assertSent(LowStockAlert::class, function ($mail) use ($ingredient) {
            return $mail->ingredient === $ingredient->name;
        });

        $this->assertNotNull($ingredient->fresh()->alert_sent_at);
    }

    public function test_email_alert_not_sent_twice()
    {
        Mail::fake();

        $product = Product::first();
        $ingredient = $product->ingredients()->first();
        $ingredient->update([
            'current_stock_in_grams' => ($ingredient->current_stock_in_grams / 2) - 1,
            'alert_sent_at' => now()
        ]);

        $this->postJson('/api/v1/orders', [
            'products' => [
                ['product_id' => $product->id, 'quantity' => 1]
            ]
        ]);

        Mail::assertNothingSent();
    }

}
