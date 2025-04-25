<?php

namespace App\Listeners;

use App\Events\IngredientStockReachedThreshold;
use App\Mail\LowStockAlert;
use Illuminate\Support\Facades\Mail;

class LowStockAlertListener
{
    /**
     * Handle the event.
     */
    public function handle(IngredientStockReachedThreshold $event): void
    {
        $ingredient = $event->ingredient;

        Mail::to('merchant@example.com')->send(new LowStockAlert($ingredient->name));

        $ingredient->alert_sent_at = now();
        $ingredient->save();
    }
}
