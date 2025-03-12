<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
    public function my_orders()
    {
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
