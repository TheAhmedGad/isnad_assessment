<?php

namespace App\Services\Orders;

use App\Events\OrderPlaced;
use App\Models\Order;
use Illuminate\Support\Facades\DB;

class OrderServices
{
    /**
     * Place Order Service
     *
     * @param array $data
     *
     * @return Order
     * @throws \Throwable
     */
    public function placeOrder(array $data): Order
    {
        return DB::transaction(function () use ($data) {
            $order = Order::create();

            //Transform request data to match sync syntax
            $data = array_column($data, 'quantity', 'product_id');
            $data = array_map(fn($q) => ['quantity' => $q], $data);

            $order->products()->sync($data);
            event(new OrderPlaced($order));

            return $order;
        });
    }


}