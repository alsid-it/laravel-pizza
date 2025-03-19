<?php

namespace App\Http\Requests;

use App\Models\Order;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreOrderRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'order_list' => 'required|array',
            'order_list.pizzas' => 'required|array',
            'order_list.pizzas.*.id' => 'required|exists:pizzas,id',
            'order_list.pizzas.*.quantity' => 'required|integer|min:1',
            'order_list.drinks' => 'required|array',
            'order_list.drinks.*.id' => 'required|exists:drinks,id',
            'order_list.drinks.*.quantity' => 'required|integer|min:1',
            'phone' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'address' => 'required|string|max:255',
            'status' => [
                'required',
                'string',
                Rule::in(Order::statuses()),
            ],
            'user_id' => 'required|integer|exists:users,id',
            'delivery_datetime' => 'required|date_format:Y-m-d H:i:s',
        ];
    }
}
