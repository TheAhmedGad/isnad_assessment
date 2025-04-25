<?php

namespace App\Http\Controllers\Api\V1\Orders;

use App\Http\Controllers\Controller;
use App\Http\Requests\Orders\CreateOrderRequest;
use App\Services\Orders\OrderServices;

class CreateOrderController extends Controller
{
    public function __invoke(OrderServices $orderServices, CreateOrderRequest $request)
    {
        dd($request->validated());
        try {
            $order = $orderServices->placeOrder($request->validated('products'));
            return response()->json([
                'message' => 'Order created successfully',
                'data' => $order
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'message' => app()->environment() !== 'production' ? $e->getMessage() : 'Something went wrong, Please contact support .'
            ], 500);
        }
    }
}
