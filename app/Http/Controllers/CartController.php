<?php

namespace App\Http\Controllers;

use App\Models\Drink;
use App\Models\Order;
use App\Models\Pizza;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class CartController extends Controller
{
    const MAX_PIZZAS_AMOUNT = 10;
    const MAX_DRINKS_AMOUNT = 20;

    public function index() {
        $cart = Session::get('cart', []);
        // dump(['$cart' => $cart]);

        return view('cart', compact('cart'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'type' => 'required|string|in:pizza,drink',
            'pizza_id' => 'sometimes|integer|exists:pizzas,id',
            'drink_id' => 'sometimes|integer|exists:drinks,id',
            'quantity' => 'required|integer',
        ]);

        $cart = Session::get('cart', []);
        $quantity = intval($request->input('quantity'));

        if ($request->input('type') === 'pizza') {
            $pizza_id = $request->input('pizza_id');

            if (isset($cart['pizzas'][$pizza_id])) {
                $cart['pizzas'][$pizza_id]['quantity'] = $quantity;
            }

            // Проверяем количество пицц в корзине
            $pizzaCount = isset($cart['pizzas']) ? array_sum(array_column($cart['pizzas'], 'quantity')) : 0;

            if ($pizzaCount > self::MAX_PIZZAS_AMOUNT) {
                return redirect()->back()->with('error', 'В корзине не может быть больше ' . self::MAX_PIZZAS_AMOUNT . ' пицц.');
            }
        } elseif ($request->input('type') === 'drink') {
            $drink_id = $request->input('drink_id');

            if (isset($cart['drinks'][$drink_id])) {
                $cart['drinks'][$drink_id]['quantity'] = $quantity;
            }

            // Проверяем количество напитков в корзине
            $drinkCount = isset($cart['drinks']) ? array_sum(array_column($cart['drinks'], 'quantity')) : 0;

            if ($drinkCount > self::MAX_DRINKS_AMOUNT) {
                return redirect()->back()->with('error', 'В корзине не может быть больше ' . self::MAX_DRINKS_AMOUNT . ' напитков.');
            }
        }

        Session::put('cart', $cart);

        return redirect()->back()->with('success', 'Товар обновлен в корзине!');
    }

    public function delete(Request $request)
    {
        $request->validate([
            'type' => 'required|string|in:pizza,drink',
            'pizza_id' => 'sometimes|integer|exists:pizzas,id',
            'drink_id' => 'sometimes|integer|exists:drinks,id',
        ]);

        $cart = Session::get('cart', []);

        if ($request->input('type') === 'pizza') {
            $pizza_id = $request->input('pizza_id');

            if (isset($cart['pizzas'][$pizza_id])) {
                unset($cart['pizzas'][$pizza_id]);
            }
        } elseif ($request->input('type') === 'drink') {
            $drink_id = $request->input('drink_id');

            if (isset($cart['drinks'][$drink_id])) {
                unset($cart['drinks'][$drink_id]);
            }
        }

        Session::put('cart', $cart);

        return redirect()->back()->with('success', 'Товар удален из корзины!');
    }

    public function order(Request $request)
    {
        // Валидация входящих данных
        $request->validate([
            'phone' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'address' => 'required|string|max:255',
            'delivery_time' => 'required|date_format:H:i', // Для проверки формата времени
        ]);

        $cart = Session::get('cart', []);

        if (!is_array($cart)) {
            $cart = [];
        }

        // Создайте запись заказа
        $order = new Order();
        $order->order_date = now(); // Устанавливаем текущую дату заказа
        $order->order_list = json_encode($cart); // Здесь вы должны указать информацию о заказе, когда она будет доступна
        $order->phone = $request->input('phone');
        $order->email = $request->input('email');
        $order->address = $request->input('address');
        $order->delivery_time = $request->input('delivery_time'); // Сохраняем время доставки
        $order->user_id = Auth::id(); // Получаем ID текущего пользователя
        $order->save(); // Сохраняем заказ в базе данных

        // Можно добавить сообщение об успешном заказе или перенаправление
        return redirect()->route('showcase')->with('success', 'Заказ успешно оформлен!');
    }

    public function add(Request $request)
    {
        // Валидация
        $request->validate([
            'type' => 'required|string|in:pizza,drink',
            'pizza_id' => 'sometimes|integer|exists:pizzas,id',
            'drink_id' => 'sometimes|integer|exists:drinks,id',
        ]);

        $cart = Session::get('cart', []);

        if ($request->input('type') === 'pizza') {
            // Проверяем количество пицц в корзине
            $pizzaCount = isset($cart['pizzas']) ? array_sum(array_column($cart['pizzas'], 'quantity')) : 0;

            if ($pizzaCount >= self::MAX_PIZZAS_AMOUNT) {
                return redirect()->back()->with('error', 'В корзине не может быть больше ' . self::MAX_PIZZAS_AMOUNT . ' пицц.');
            }

            // Добавляем пиццу в корзину
            $pizzaId = $request->input('pizza_id');

            if (isset($cart['pizzas'][$pizzaId])) {
                $cart['pizzas'][$pizzaId]['quantity']++;
            } else {
                // Получаем информацию о пицце
                $pizza = Pizza::find($pizzaId);
                $cart['pizzas'][$pizzaId] = [
                    'quantity' => 1,
                    'name' => $pizza->name,
                    'pizza_id' => $pizza->id,
                    'type' => 'pizza',
                    'image' => asset('storage/' . $pizza->image),
                ];
            }
        } elseif ($request->input('type') === 'drink') {
            // Проверяем количество напитков в корзине
            $drinkCount = isset($cart['drinks']) ? array_sum(array_column($cart['drinks'], 'quantity')) : 0;

            if ($drinkCount >= self::MAX_DRINKS_AMOUNT) {
                return redirect()->back()->with('error', 'В корзине не может быть больше ' . self::MAX_DRINKS_AMOUNT . ' напитков.');
            }

            // Добавляем напиток в корзину
            $drinkId = $request->input('drink_id');

            if (isset($cart['drinks'][$drinkId])) {
                $cart['drinks'][$drinkId]['quantity']++;
            } else {
                // Получаем информацию о напитке
                $drink = Drink::find($drinkId);
                $cart['drinks'][$drinkId] = [
                    'quantity' => 1,
                    'name' => $drink->name,
                    'type' => 'drink',
                    'image' => asset('storage/' . $drink->image),
                ];
            }
        }

        Session::put('cart', $cart);

        return redirect()->back()->with('success', 'Товар добавлен в корзину!');
    }
}
