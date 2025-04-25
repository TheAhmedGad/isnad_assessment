<?php

namespace App\Rules;

use App\Models\Product;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class HasSufficientStock implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        // Extract index from "products.{index}.quantity"
        preg_match('/products\.(\d+)\.quantity/', $attribute, $matches);
        $index = $matches[1] ?? null;

        if (!is_numeric($index)) {
            $fail("Invalid product index.");
            return;
        }

        // You can use request() here or pass data into the rule via constructor if needed
        $product = Product::with('ingredients')->find(
            request("products.{$index}.product_id")
        );

        if (!$product) {
            $fail("Product not found.");
            return;
        }

        foreach ($product->ingredients as $ingredient) {
            $required = $ingredient->pivot->amount_in_grams * $value;
            if ($ingredient->current_stock_in_grams < $required) {
                $fail("Not enough stock for product: {$ingredient->name}");
                return;
            }
        }
    }
}
