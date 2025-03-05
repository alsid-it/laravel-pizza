<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pizza;
use App\Models\Drink;

class ShowcaseController extends Controller
{
    public function index()
    {
        // Получаем все пиццы и напитки из базы данных
        $pizzas = Pizza::all();
        $drinks = Drink::all();

        // Session::flush();

        // Возвращаем представление с данными о пиццах и напитках
        return view('showcase', compact('pizzas', 'drinks'));
    }
}
