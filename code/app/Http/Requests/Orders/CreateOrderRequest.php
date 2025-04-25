<?php

namespace App\Http\Requests\Orders;

use App\Rules\HasSufficientStock;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CreateOrderRequest extends FormRequest
{
    public function rules()
    {
        return [
            'products' => ['required', 'array'],
            'products.*.product_id' => ['required', 'integer', 'distinct:products.*.product_id', Rule::exists('products', 'id')],
            'products.*.quantity' => ['required', 'integer', 'min:1', new HasSufficientStock()],
        ];
    }
}
