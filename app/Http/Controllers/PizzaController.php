<?php

namespace App\Http\Controllers;

use App\Models\Pizza;
use Illuminate\Http\Request;

class PizzaController extends Controller
{
    public function create()
    {
        return view('pizzas.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'image' => 'required|image|mimes:jpg,png,jpeg|max:1024',
        ]);

        // Сохраняем изображение
        $imagePath = $request->file('image')->store('images', 'public');

        // Создаем новую пиццу
        Pizza::create([
            'name' => $request->name,
            'image' => $imagePath,
        ]);

        return redirect()->route('admin.add-pizza')->with('success', 'Пицца добавлена успешно!');
    }
}
