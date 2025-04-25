<?php

namespace App\Listeners;

use App\Events\IngredientStockReachedThreshold;
use App\Events\OrderPlaced;
use App\Services\Ingredients\StockServices;

class OrderPlacedListener
{
    /**
     * Create the event listener.
     */
    public function __construct(
        private StockServices $ingredientStockServices,
    ){}

    /**
     *  Handle the event.
     *
     * @param OrderPlaced $event
     *
     * @return void
     */
    public function handle(OrderPlaced $event): void
    {
        $event->order->load('products.ingredients');

        foreach ($event->order->products as $product) {
            foreach ($product->ingredients as $ingredient) {
                $usedAmount = $ingredient->pivot->amount_in_grams * $product->pivot->quantity; // grams
                $this->ingredientStockServices->decreaseStockWithHistory($ingredient, $usedAmount);

                if ($this->ingredientStockServices->hasReachedAlertThreshold($ingredient))
                    event(new IngredientStockReachedThreshold($ingredient));
            }
        }
    }

}
