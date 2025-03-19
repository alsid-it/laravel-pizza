<?php
namespace App\Services;

use App\DTO\OrderDTO;
use App\Enums\OrderProductTypes;
use App\Models\Order;
use Illuminate\Database\Eloquent\Builder;

class OrderService
{
    public static string $validateOrderError = '';
    const ALLOWED_ORDER_FILTERS = ['user_id'];

    public static function createOrder(OrderDTO $orderDTO): Order
    {
        $orderData = $orderDTO->toArray();

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

    public static function validateOrderList (array $orderData): bool
    {
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

                foreach ($productsArray as $productData) {
                    $productAmount = $productData['quantity'];

                    if (!is_numeric($productAmount)) {
                        self::$validateOrderError = 'Используется не верное число продукта в заказе';
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
                self::$validateOrderError = 'Не верный тип продукта в заказе, доступный только пиццы и напитки';
                return false;
            }

            if ($amount <= 0) {
                self::$validateOrderError = 'Количество товара в заказе равно 0';
                return false;
            }

            if ($drinksAmount > Order::MAX_DRINKS_IN_ORDER) {
                self::$validateOrderError = 'Максимум ' . Order::MAX_DRINKS_IN_ORDER . ' напитков в заказе';
                return false;
            }

            if ($pizzasAmount > Order::MAX_PIZZAS_IN_ORDER) {
                self::$validateOrderError = 'Максимум ' . Order::MAX_PIZZAS_IN_ORDER . ' пицц в заказе';
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
