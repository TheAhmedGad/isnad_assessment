<?php

namespace App\Http\Controllers\Api\V1\Orders;

use App\Http\Controllers\Controller;
use App\Http\Requests\Orders\CreateOrderRequest;
use App\Http\Resources\Orders\OrderResource;
use App\Services\Orders\OrderServices;

class CreateOrderController extends Controller
{
    public function __invoke(OrderServices $orderServices, CreateOrderRequest $request)
    {
        try {
            return OrderResource::make(
                $orderServices->placeOrder($request->validated('products'))
            )->additional([
                'message' => 'Order created successfully',
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'message' => app()->environment() !== 'production' ? $e->getMessage() : 'Something went wrong, Please contact support .'
            ], 500);
        }
    }
}
