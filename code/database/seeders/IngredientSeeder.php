<?php

namespace Database\Seeders;

use App\Enums\StockActionType;
use App\Models\Ingredient;
use App\Models\Product;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class IngredientSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        if(Ingredient::count())
            return ;

        $ingredients = [
            'Beef' => 20000,
            'Cheese' => 5000,
            'Onion' => 1000
        ];

        foreach ($ingredients as $ingredientName => $stock) {

            $ingredient = Ingredient::create([
                'name' => $ingredientName,
                'current_stock_in_grams' => $stock,
                'max_capacity_in_grams' => $stock,
            ]);
            $ingredient->stock()->create([
                'amount_in_grams' => $stock,
                'action_type' => StockActionType::INITIAL,
            ]);
        }


    }
}
