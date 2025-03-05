<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use App\Models\Pizza;
use App\Models\Drink;
use Illuminate\Support\Facades\Storage;

class AdminController extends Controller
{
    public function index()
    {
        return view('admin.index');
    }

    public function addPizza()
    {
        return view('pizzas.create');
    }

    public function addDrink()
    {
        return view('drinks.create');
    }

    public function deleteProductList()
    {
        $pizzas = Pizza::all();
        $drinks = Drink::all();

        return view('admin.delete-product', compact('pizzas', 'drinks'));
    }

    public function changeProductList()
    {
        $pizzas = Pizza::all();
        $drinks = Drink::all();

        return view('admin.change-product', compact('pizzas', 'drinks'));
    }

    public function changeOrderStatuses()
    {
        $orders = Order::all();

        return view('admin.change-orders', compact('orders'));
    }

    public function changeOrderStatus(Request $request)
    {
        $statusesString = implode(',', Order::statuses());

        $request->validate([
            'order_id' => 'required|integer|exists:orders,id',
            'status' => 'required|string|in:' . $statusesString,
        ]);

        $order_id = $request->input('order_id');
        $status = $request->input('status');

        $order = Order::findOrFail($order_id);
        $order->status = $status;

        $order->save();

        return redirect()->back()->with('success', 'Статус заказа изменён');

    }

    public function deleteProduct(Request $request)
    {
        $request->validate([
            'type' => 'required|string|in:pizza,drink',
            'id' => 'required|integer',
        ]);

        $type = $request->input('type');
        $id = $request->input('id');

        if ($type === 'pizza') {
            $product = Pizza::findOrFail($id);
        } elseif ($type === 'drink') {
            $product = Drink::findOrFail($id);
        } else {
            return redirect()->back()->with('error', 'Неверный тип продукта');
        }

        $product->delete();

        return redirect()->back()->with('success', 'Продукт успешно удален');
    }

    public function changeProduct(Request $request)
    {
        // Валидация входных данных
        $request->validate([
            'type' => 'required|string|in:pizza,drink', // Тип продукта (пицца или напиток)
            'id' => 'required|integer', // ID продукта
            'name' => 'required|string|max:255', // Новое название продукта
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048', // Новое изображение (опционально)
        ]);

        // Получаем тип продукта и его ID
        $type = $request->input('type');
        $id = $request->input('id');

        // Находим продукт в зависимости от типа
        if ($type === 'pizza') {
            $product = Pizza::findOrFail($id); // Ищем пиццу
        } elseif ($type === 'drink') {
            $product = Drink::findOrFail($id); // Ищем напиток
        } else {
            return redirect()->back()->with('error', 'Неверный тип продукта');
        }

        // Обновляем название продукта
        $product->name = $request->input('name');

        // Если загружено новое изображение
        if ($request->hasFile('image')) {
            // Удаляем старое изображение, если оно существует
            if ($product->image && Storage::exists($product->image)) {
                Storage::delete($product->image);
            }

            // Сохраняем новое изображение
            $imagePath = $request->file('image')->store('images', 'public'); // Сохраняем в папку `public/images`
            $product->image = $imagePath; // Обновляем путь к изображению
        }

        // Сохраняем изменения в базе данных
        $product->save();

        // Перенаправляем обратно с сообщением об успехе
        return redirect()->back()->with('success', 'Продукт успешно изменен');
    }


}
