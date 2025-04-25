<?php

use App\Http\Controllers\Api\V1\Orders\CreateOrderController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    Route::post('/orders', CreateOrderController::class);
});
