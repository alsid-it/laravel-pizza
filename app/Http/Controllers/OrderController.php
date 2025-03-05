<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
    public function my_orders()
    {
        // Проверяем, авторизован ли пользователь
        if (!Auth::check()) {
            // Если не авторизован, перенаправляем на страницу входа или на другую страницу
            return redirect()->route('showcase')->with('message', 'Пожалуйста, войдите в свою учетную запись для просмотра заказов.');
        }

        // Получаем идентификатор текущего авторизованного пользователя
        $userId = Auth::id();

        // Получаем все заказы текущего пользователя
        $orders = Order::where('user_id', $userId)->get();
        $ordersArray = $orders->toArray();

        foreach ($ordersArray as &$order) {
            $order['order_list'] = json_decode($order['order_list'], true);
        }

        // Возвращаем представление с заказами
        return view('orders.my_orders', compact('ordersArray'));
    }
}
