<?php
namespace App\Services;

use App\Enums\OrderProductTypes;
use App\Models\Drink;
use App\Models\Order;
use App\Models\Pizza;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Validator;

class OrderService
{
    const ALLOWED_ORDER_FILTERS = ['user_id'];

    public static function createOrder(array $orderData): Order
    {
        return Order::create($orderData);
    }

    public static function getFilteredOrdersQuery(array $filters = []): Builder
    {
        $query = Order::query();

        foreach ($filters as $filterName => $filterValue) {
            if (in_array($filterName, self::ALLOWED_ORDER_FILTERS)) {
                $query->where($filterName, $filterValue);
            }
        }

        return $query;
    }

    public static function validateOrderList (string $orderString, &$validateOrderError): bool
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

        $orderProductTypes = array_map(
            fn(OrderProductTypes $type) => $type->value,
            OrderProductTypes::cases()
        );

        // учесть тут максимальное количество продуктов в корзине
        // передавать ошибку проверки в json ответ
        foreach ($orderData as $productType => $productsArray) {
            if (in_array($productType, $orderProductTypes)) {
                $amount = 0;
                $drinksAmount = 0;
                $pizzasAmount = 0;

                foreach ($productsArray as $productAmount) {
                    if (!is_numeric($productAmount)) {
                        $validateOrderError = 'Используется не верное число продукта в заказе';
                        return false;
                    }

                    $amount += $productAmount;

                    if (OrderProductTypes::DRINKS->value == $productType) {
                        $drinksAmount += $productAmount;
                    }
                    if (OrderProductTypes::PIZZAS->value == $productType) {
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

            if ($drinksAmount > Order::MAX_DRINKS_IN_ORDER) {
                $validateOrderError = 'Максимум ' . Order::MAX_DRINKS_IN_ORDER . ' напитков в заказе';
                return false;
            }

            if ($pizzasAmount > Order::MAX_PIZZAS_IN_ORDER) {
                $validateOrderError = 'Максимум ' . Order::MAX_PIZZAS_IN_ORDER . ' пицц в заказе';
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

}
