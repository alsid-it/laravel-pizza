<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreOrderRequest;
use App\Http\Requests\UpdateOrderRequest;
use App\Http\Resources\Api\V1\OrderResource;
use App\Models\Order;
use App\Models\Pizza;
use App\Models\Drink;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;


class OrderController extends Controller
{
    const AVAILABLE_PRODUCT_TYPES = ['pizzas', 'drinks'];
    const DRINK_PRODUCT_TYPES = 'drinks';
    const PIZZA_PRODUCT_TYPES = 'pizzas';
    const MAX_PIZZAS_IN_ORDER = 10;
    const MAX_DRINKS_IN_ORDER = 20;
    public string $validateOrderError = '';

    public static function getPizzaMaxInOrder(): int
    {
        return self::MAX_PIZZAS_IN_ORDER;
    }

    public static function getDrinkMaxInOrder(): int
    {
        return self::MAX_DRINKS_IN_ORDER;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Order::query();

        // получить заказы конкретного пользователя
        if ($request->has('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        return OrderResource::collection($query->paginate());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreOrderRequest $request)
    {
        $orderData = $request->all();

        $orderList = $orderData['order_list'];

        if (!self::validateOrderList($orderList, $this->validateOrderError)) {
            $errorArr = [
                'message' => 'The order_list is invalid.',
                'errors' => [
                    'order_list' => [
                        'The order_list is invalid.'
                    ]
                ],
            ];

            $errorArr['validate_order_message'] = $this->validateOrderError;

            return response()->json($errorArr, 422);
        }

        $createdOrder = Order::create($request->all());

        return response()->json([
            'message' => 'Order created successfully.', // Сообщение об успехе
            'data' => new OrderResource($createdOrder), // Данные созданного заказа
            'links' => [
                'self' => url("/api/v1/orders/{$createdOrder->id}"),
                'list' => url('/api/v1/orders'),
            ],
        ], 201);
    }

    private static function validateOrderList(string $orderString, &$validateOrderError): bool
    {
        $orderData = json_decode($orderString, true);

        $validator = Validator::make($orderData, [
            'pizzas' => 'required|array',
            'drinks' => 'required|array',
        ]);

        if ($validator->fails()) {
            $validateOrderError = 'Нет данных о пиццах/напитках';
            return false;
        }

        // Проверяем id pizzas и drinks
        $hasValidPizzas = self::validateItems($orderData['pizzas'], Pizza::class);
        $hasValidDrinks = self::validateItems($orderData['drinks'], Drink::class);

        if (!$hasValidPizzas || !$hasValidDrinks) {
            $validateOrderError = 'Неправильный id пиццы/напитка';
            return false;
        }

        // учесть тут максимальное количество продуктов в корзине
        // передавать ошибку проверки в json ответ
        foreach ($orderData as $productType => $productsArray) {
            if (in_array($productType, self::AVAILABLE_PRODUCT_TYPES)) {
                $amount = 0;
                $drinksAmount = 0;
                $pizzasAmount = 0;

                foreach ($productsArray as $productAmount) {
                    if (!is_numeric($productAmount)) {
                        $validateOrderError = 'Используется не верное число продукта в заказе';
                        return false;
                    }

                    $amount += $productAmount;

                    if (self::DRINK_PRODUCT_TYPES == $productType) {
                        $drinksAmount += $productAmount;
                    }
                    if (self::PIZZA_PRODUCT_TYPES == $productType) {
                        $pizzasAmount += $productAmount;
                    }
                }
            } else {
                $validateOrderError = 'Не верный тип продукта в заказе, доступный только пиццы и напитки';
                return false;
            }

            if ($amount <= 0) {
                $validateOrderError = 'Количество товара в заказе равно 0';
                return false;
            }

            if ($drinksAmount > self::MAX_DRINKS_IN_ORDER) {
                $validateOrderError = 'Максимум ' . self::MAX_DRINKS_IN_ORDER . ' напитков в заказе';
                return false;
            }

            if ($pizzasAmount > self::MAX_PIZZAS_IN_ORDER) {
                $validateOrderError = 'Максимум ' . self::MAX_PIZZAS_IN_ORDER . ' пицц в заказе';
                return false;
            }
        }

        return true;
    }

    private static function validateItems($items, $modelClass)
    {
        foreach ($items as $id => $quantity) {
            // Проверяем, что товар с таким id существует
            if (!$modelClass::find($id)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Display the specified resource.
     */
    public function show(Order $order)
    {
        return new OrderResource($order);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateOrderRequest $request, Order $order)
    {
        $order->update($request->all());

        return response()->json([
            'message' => 'Order updated successfully.',
            'data' => new OrderResource($order),
            'links' => [
                'self' => url("/api/v1/orders/{$order->id}"),
                'list' => url('/api/v1/orders'),
            ],
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Order $order)
    {
        $order->delete();

        return response()->noContent();
    }
}
