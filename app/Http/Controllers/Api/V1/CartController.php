<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\Drink;
use App\Models\Pizza;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Response;

class CartController extends Controller
{

    // показ всех корзин с разбивкой по пользователям
    public function index()
    {
        $carts = Cart::all();

        // Инициализируем пустой массив для результата
        $result = [];

        // Группируем данные по user_id
        foreach ($carts as $cart) {
            $userId = $cart->user_id;
            $productType = $cart->product_type;
            $productId = $cart->product_id;
            $quantity = $cart->quantity;

            if (User::find($userId)) {
                $result[$userId][$productType][$productId] = $quantity;
            }
        }

        // Возвращаем результат в формате JSON
        return response()->json($result, 200);
    }

    // добавление товаров в корзину
    public function store(Request $request)
    {
        $userId = $request->input('user_id', Auth::id());

        if (!$userId) {
            return response()->json(['error' => 'Пользователь не определён'], 400);
        }

        if (!User::find($userId)) {
            return response()->json(['error' => 'Пользователь с указанным ID не найден'], 404);
        }

        // Разбираем JSON-строки на массивы
        $pizzas = json_decode($request->input('pizzas'), true);
        $drinks = json_decode($request->input('drinks'), true);

        // Проверяем, что данные успешно разобраны
        if (json_last_error() !== JSON_ERROR_NONE) {
            return response()->json(['error' => 'Неверный формат данных pizzas или drinks'], 400);
        }

        // Проверка наличия с таким id и по количеству
        $errorResponse = self::checkPizzasDrinksAddInOrder($userId, $pizzas, $drinks);
        if (!is_null($errorResponse)) {
            return response()->json(['error' => $errorResponse['error_text']], $errorResponse['code']);
        }

        foreach ($pizzas as $pizzaId => $quantity) {
            Cart::create([
                'user_id' => $userId,
                'product_id' => $pizzaId,
                'product_type' => 'pizza',
                'quantity' => $quantity,
            ]);
        }

        foreach ($drinks as $drinkId => $quantity) {
            Cart::create([
                'user_id' => $userId,
                'product_id' => $drinkId,
                'product_type' => 'drink',
                'quantity' => $quantity,
            ]);
        }

        return response()->json(['message' => 'Данные успешно добавлены в корзину'], 201);
    }

    // показать корзину пользователя
    public function show(Request $request, $user_id)
    {
        if (!User::find($user_id)) {
            return response()->json(['error' => 'Пользователя с id ' . $user_id . ' нет'], 400);
        }

        $result = [];

        $carts = Cart::where('user_id', $user_id)
            ->get();

        // Группируем данные по user_id
        foreach ($carts as $cart) {
            $productType = $cart->product_type;
            $productId = $cart->product_id;
            $quantity = $cart->quantity;

            $result[$productType][$productId] = $quantity;
        }

        return response()->json($result, 200);
    }

    public function update(Request $request, $user_id)
    {
        if (!User::find($user_id)) {
            return response()->json(['error' => 'Пользователь с ID ' . $user_id . ' не найден'], 404);
        }

        // Разбираем JSON-строки на массивы
        $pizzas = json_decode($request->input('pizzas'), true);
        $drinks = json_decode($request->input('drinks'), true);

        // Проверяем, что данные успешно разобраны
        if (json_last_error() !== JSON_ERROR_NONE) {
            return response()->json(['error' => 'Неверный формат данных pizzas или drinks'], 400);
        }

        // Проверка наличия с таким id и по количеству
        $errorResponse = self::checkPizzasDrinksAddInOrder($user_id, $pizzas, $drinks);
        if (!is_null($errorResponse)) {
            return response()->json(['error' => $errorResponse['error_text']], $errorResponse['code']);
        }

        Cart::where('user_id', $user_id)->delete();

        foreach ($pizzas as $pizzaId => $quantity) {
            Cart::create([
                'user_id' => $user_id,
                'product_id' => $pizzaId,
                'product_type' => 'pizza',
                'quantity' => $quantity,
            ]);
        }

        foreach ($drinks as $drinkId => $quantity) {
            Cart::create([
                'user_id' => $user_id,
                'product_id' => $drinkId,
                'product_type' => 'drink',
                'quantity' => $quantity,
            ]);
        }

        return response()->json(['message' => 'Данные корзины успешно обновлены'], 201);
    }

    public function destroy(Request $request, $user_id)
    {
        if (!User::find($user_id)) {
            return response()->json(['error' => 'Такого пользователя нет'], 400);
        }

        Cart::where('user_id', $user_id)->delete();

        return response()->noContent();
    }

    public static function checkPizzasDrinksAddInOrder(int $userId, array $pizzas, array $drinks): ?array
    {
        $maxPizzasQuantity = OrderController::MAX_PIZZAS_IN_ORDER;
        $maxDrinksQuantity = OrderController::MAX_DRINKS_IN_ORDER;

        $currentPizzasAmount = self::countUserCartPizzasQuantity($userId);
        $currentDrinksAmount = self::countUserCartDrinksQuantity($userId);

        $pizzasAmount = $currentPizzasAmount;
        $drinksAmount = $currentDrinksAmount;

        $errorResponse = [];

        foreach ($pizzas as $pizzaId => $quantity) {
            if (!Pizza::find($pizzaId)) {
                return $errorResponse = [
                    'error_text' => 'Пицца с ID ' . $pizzaId . ' не найдена',
                    'code' => 404
                ];
            }
            $pizzasAmount += $quantity;
        }
        foreach ($drinks as $drinkId => $quantity) {
            if (!Drink::find($drinkId)) {
                return $errorResponse = [
                    'error_text' => 'Напиток с ID ' . $drinkId . ' не найден',
                    'code' => 404
                ];
            }
            $drinksAmount += $quantity;
        }

        if ($pizzasAmount > $maxPizzasQuantity) {
            return $errorResponse = [
                'error_text' => 'Количество пицц в заказе больше ' . $maxPizzasQuantity,
                'code' => 422
            ];
        }

        if ($drinksAmount > $maxDrinksQuantity) {
            return $errorResponse = [
                'error_text' => 'Количество напитков в заказе больше ' . $maxDrinksQuantity,
                'code' => 422
            ];
        }

        return null;
    }

    public static function countUserCartPizzasQuantity(int $userId): int
    {
        return Cart::where('user_id', $userId)
            ->where('product_type', 'pizza')
            ->sum('quantity');
    }

    public static function countUserCartDrinksQuantity(int $userId): int
    {
        return Cart::where('user_id', $userId)
            ->where('product_type', 'drink')
            ->sum('quantity');
    }

}
