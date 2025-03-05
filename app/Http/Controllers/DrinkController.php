<?php

namespace App\Http\Controllers;

use App\Models\Drink;
use Illuminate\Http\Request;

class DrinkController extends Controller
{
    public function create()
    {
        return view('drinks.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'image' => 'required|image|mimes:jpg,png,jpeg|max:1024', // Проверяем, что изображение jpg, png или jpeg и не больше 1 мб
        ]);

        // Сохраняем изображение
        $imagePath = $request->file('image')->store('images', 'public');

        Drink::create([
            'name' => $request->name,
            'image' => $imagePath,
        ]);

        return redirect()->route('admin.add-drink')->with('success', 'Напиток добавлен успешно!');
    }
}
