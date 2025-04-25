<?php

namespace App\Services\Ingredients;

use App\Enums\StockActionType;
use App\Models\Ingredient;
use App\Models\IngredientStock;

class StockServices
{
    /**
     * Increase Ingredient Stock and Store history
     *
     * @param Ingredient $ingredient
     * @param $amount
     *
     * @return void
     */
    public function increaseStockWithHistory(Ingredient $ingredient, $amount): void
    {
        // Assuming we have web gui to increase stock or restock amount
        // Also reset the alert timestamp `$ingredient-<alert_sent_at` to NULL
        // when the (new amount + the current amount) is more than 50% of the threshold
    }

    /**
     * Decrease Ingredient Stock and Store history
     *
     * @param Ingredient $ingredient
     * @param $amount
     *
     * @return void
     */
    public function decreaseStockWithHistory(Ingredient $ingredient, $amount): void
    {
        $newAmount = $ingredient->current_stock_in_grams - $amount;
        $this->updateStock($ingredient, $newAmount);

        $this->createStockHistoryRecord($ingredient, -$amount, StockActionType::OUTGOING);
    }

    /**
     * Update current stock
     *
     * @param Ingredient $ingredient
     * @param $amount
     *
     * @return bool
     */
    protected function updateStock(Ingredient $ingredient, $amount): bool
    {
        $ingredient->current_stock_in_grams = $amount;
        return $ingredient->save();
    }

    /**
     * Create Stock History Record
     *
     * @param Ingredient $ingredient
     * @param $amount
     * @param $action
     *
     * @return IngredientStock
     */
    protected function createStockHistoryRecord(Ingredient $ingredient, $amount, $action): IngredientStock
    {
        return $ingredient->stock()->create([
            'amount_in_grams' => $amount,
            'action_type' => $action,
        ]);
    }

    /**
     * Check for stock Threshold
     *
     * @param $ingredient
     *
     * @return bool
     */
    public function hasReachedAlertThreshold($ingredient): bool
    {
        //This could be moved to config file or even better to db column for the sake of flexibility
        $threshold = $ingredient->max_capacity_in_grams * 0.5;

        return $ingredient->current_stock_in_grams <= $threshold && !$ingredient->alert_sent_at;
    }
}