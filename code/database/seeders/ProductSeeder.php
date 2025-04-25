<?php

namespace Database\Seeders;

use App\Models\Ingredient;
use App\Models\Product;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        if(Product::count())
            return ;

        $product = Product::create(['name' => 'Burger']);

        $ingredients = Ingredient::whereIn('name', ['Beef', 'Cheese', 'Onion'])->get()->keyBy('name');

        $product->ingredients()->sync([
            $ingredients['Beef']->id => ['amount_in_grams' => 150],
            $ingredients['Cheese']->id => ['amount_in_grams' => 30],
            $ingredients['Onion']->id => ['amount_in_grams' => 20],
        ]);
    }
}
